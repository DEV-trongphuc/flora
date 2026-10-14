<?php
/**
 * Flora Dental Clinic - RAG Engine & Helper Utilities
 * Cung cấp:
 * 1. Tự động khởi tạo & Migration CSDL (3 bảng: docs, chunks, vector cache)
 * 2. Bóc tách tài liệu đa định dạng: Word (.docx), PDF, Text (.txt), Crawl Web URL
 * 3. Chia nhỏ văn bản thông minh (Intelligent Chunking với Sentence Boundary Lookback)
 * 4. Tạo Vector Embeddings qua Google Gemini API kèm cơ chế Cache băm MD5
 * 5. Tính toán khoảng cách Vector Cosine Similarity
 * 6. Truy hồi ngữ cảnh lai (Hybrid RAG: Vector Similarity + MySQL FullText / LIKE)
 *
 * Tham chiếu thiết kế từ: D:\GITHUB_SPACE\MYERP (rag_helpers.php & ChatController.php)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. KHỞI TẠO BẢNG CƠ SỞ DỮ LIỆU & TỰ ĐỘNG MIGRATION
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_create_ai_training_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';
    $table_cache = $wpdb->prefix . 'flora_ai_vector_cache';

    // 1. Bảng tài liệu và thư mục
    $sql_docs = "CREATE TABLE IF NOT EXISTS $table_docs (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        source_type varchar(50) NOT NULL DEFAULT 'manual',
        content longtext DEFAULT NULL,
        tags varchar(255) DEFAULT '',
        parent_id bigint(20) unsigned NOT NULL DEFAULT 0,
        file_path varchar(500) DEFAULT '',
        file_size bigint(20) unsigned DEFAULT 0,
        status varchar(50) NOT NULL DEFAULT 'pending',
        is_active tinyint(1) NOT NULL DEFAULT 1,
        version int(11) NOT NULL DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY parent_id (parent_id),
        KEY status (status),
        KEY is_active (is_active)
    ) $charset_collate;";

    // 2. Bảng phân mảnh vector hóa (chunks)
    $sql_chunks = "CREATE TABLE IF NOT EXISTS $table_chunks (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        doc_id bigint(20) unsigned NOT NULL,
        chunk_index int(11) NOT NULL,
        content text NOT NULL,
        vector longtext DEFAULT NULL,
        vector_norm double DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY doc_id (doc_id),
        FULLTEXT KEY ft_content (content)
    ) $charset_collate;";

    // 3. Bảng bộ nhớ đệm Vector Cache
    $sql_cache = "CREATE TABLE IF NOT EXISTS $table_cache (
        hash varchar(64) NOT NULL,
        vector longtext NOT NULL,
        vector_norm double DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (hash)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql_docs);
    dbDelta($sql_chunks);
    dbDelta($sql_cache);
}

// Tự động kiểm tra và tạo bảng khi file được nạp
add_action('init', function() {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_docs'") !== $table_docs) {
        flora_create_ai_training_tables();
    }
});

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 2. CẤU HÌNH RAG MẶC ĐỊNH
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_get_rag_settings() {
    $defaults = array(
        'is_enabled'           => 1,
        'bot_name'             => 'Bác Sĩ Tư Vấn Flora',
        'clinic_address'       => '326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh',
        'clinic_hotline'       => '028 7305 8999',
        'welcome_msg'          => "Xin chào bạn! Tôi là Bác Sĩ AI của Nha Khoa Flora 🩺.\n\nBạn đang quan tâm đến cấy ghép Implant Thụy Sĩ, niềng răng trong suốt, bọc răng sứ hay các gói ưu đãi hot như Gói Care Plus 999k (chăm sóc cả gia đình), Gói Flora White Up 1.999k (tẩy trắng Plasma)? Hãy nhắn cho tôi để nhận báo giá chi tiết và hỗ trợ nhanh nhất nhé!",
        'persona_prompt'       => "Bạn là Bác Sĩ Tư Vấn Trực Tuyến của Nha Khoa Flora (Flora Dental Clinic) tại TP.HCM.\nVăn phong nhắn tin ân cần, giải thích cặn kẽ, trung thực 100% theo dữ liệu bảng giá và phác đồ chuyên khoa được cung cấp.\nKhi giải đáp xong tình trạng răng miệng, hãy lịch sự mời khách để lại Số Điện Thoại (hoặc Zalo) để Bác sĩ/Chuyên viên liên hệ tư vấn chi tiết hơn và sắp xếp lịch hẹn phù hợp.\nTUYỆT ĐỐI KHÔNG tự ý bịa đặt bất kỳ chương trình khuyến mãi, ưu đãi, quà tặng miễn phí nếu không có trong dữ liệu phòng khám.",
        'similarity_threshold' => 0.40,
        'top_k'                => 6,
        'chunk_size'           => 700,
        'chunk_overlap'        => 150,
        'temperature'          => 0.25,
        'max_output_tokens'    => 1024,
        'model'                => 'gemini-3.5-flash-lite',
        'api_key'              => get_option('flora_gemini_api_key', '')
    );

    $saved = get_option('flora_rag_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }

    $merged = wp_parse_args($saved, $defaults);
    if (empty($merged['api_key'])) {
        $merged['api_key'] = get_option('flora_gemini_api_key', '') ?: (getenv('FLORA_GEMINI_API_KEY') ?: '');
    }

    return $merged;
}

function flora_update_rag_settings($new_settings) {
    $current = flora_get_rag_settings();
    $updated = wp_parse_args($new_settings, $current);
    update_option('flora_rag_settings', $updated);

    if (!empty($updated['api_key'])) {
        update_option('flora_gemini_api_key', sanitize_text_field($updated['api_key']));
    }
    if (!empty($updated['model'])) {
        update_option('flora_gemini_model', sanitize_text_field($updated['model']));
    }
    return $updated;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 3. BÓC TÁCH VĂN BẢN ĐA ĐỊNH DẠNG (DOCX, PDF, TEXT, CRAWL WEB)
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Trích xuất văn bản từ file Word (.docx) bằng PHP ZipArchive
 */
function flora_extract_docx_text($file_path) {
    if (!file_exists($file_path)) {
        return '';
    }

    if (!class_exists('ZipArchive')) {
        // Fallback đọc chuỗi thô nếu máy chủ chưa bật php-zip
        $content = @file_get_contents($file_path);
        if (empty($content)) return '';
        $text = strip_tags(str_replace('<', ' <', $content));
        return preg_replace('/\s+/', ' ', $text);
    }

    $zip = new ZipArchive();
    if ($zip->open($file_path) !== true) {
        return '';
    }

    $xml = $zip->getFromName('word/document.xml');
    $zip->close();

    if (!$xml) {
        return '';
    }

    // Thay thế ngắt dòng đoạn văn XML bằng ngắt dòng chuẩn
    $xml = preg_replace('/<\/w:p>/', "\n", $xml);
    $xml = preg_replace('/<w:br[^>]*\/>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tr>/', "\n", $xml);
    $xml = preg_replace('/<\/w:tc>/', " | ", $xml);

    $text = strip_tags($xml);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    // Chuẩn hóa khoảng trắng dư thừa
    $text = preg_replace('/[ \t]+/', ' ', $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);
    return trim($text);
}

/**
 * Trích xuất văn bản từ file PDF qua Gemini Multimodal
 */
function flora_extract_pdf_text_via_gemini($file_path, $api_key) {
    if (!file_exists($file_path)) {
        return '';
    }

    $data = file_get_contents($file_path);
    if (empty($data)) {
        return '';
    }

    $base64 = base64_encode($data);
    $payload = array(
        'contents' => array(
            array(
                'parts' => array(
                    array(
                        'inlineData' => array(
                            'mimeType' => 'application/pdf',
                            'data'     => $base64
                        )
                    ),
                    array(
                        'text' => 'Hãy trích xuất và trả về toàn bộ nội dung văn bản tiếng Việt có trong tài liệu y khoa nha khoa PDF này dưới dạng văn bản thô đầy đủ nhất có thể. Không thêm bình luận, tiêu đề phụ hay bất kỳ giải thích nào khác.'
                    )
                )
            )
        ),
        'generationConfig' => array(
            'temperature' => 0.1
        )
    );

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $api_key;
    $args = array(
        'body'        => wp_json_encode($payload),
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'timeout'     => 60,
        'data_format' => 'body'
    );

    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        return '';
    }

    $body = wp_remote_retrieve_body($response);
    $res = json_decode($body, true);
    return $res['candidates'][0]['content']['parts'][0]['text'] ?? '';
}

/**
 * Thu thập và làm sạch nội dung bài viết từ đường dẫn Web URL
 */
function flora_fetch_web_content($url) {
    $args = array(
        'timeout'    => 15,
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) FloraDentalBot/2.0'
    );
    $response = wp_remote_get($url, $args);
    if (is_wp_error($response)) {
        return '';
    }

    $html = wp_remote_retrieve_body($response);
    if (empty($html)) {
        return '';
    }

    // Loại bỏ thẻ script, style, iframe
    $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
    $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
    $html = preg_replace('/<nav\b[^>]*>(.*?)<\/nav>/is', '', $html);
    $html = preg_replace('/<footer\b[^>]*>(.*?)<\/footer>/is', '', $html);

    $text = strip_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 4. CHIA NHỎ VĂN BẢN THÔNG MINH (INTELLIGENT CHUNKING VỚI SENTENCE BOUNDARY)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_chunk_text($text, $chunk_size = 700, $chunk_overlap = 150) {
    $chunks = array();
    $text = trim($text);
    $length = mb_strlen($text, 'UTF-8');

    if ($length <= $chunk_size) {
        return array($text);
    }

    $start = 0;
    while ($start < $length) {
        $end_index = min($start + $chunk_size, $length);
        $chunk = mb_substr($text, $start, $end_index - $start, 'UTF-8');

        if ($end_index === $length) {
            $chunks[] = trim($chunk);
            break;
        }

        // Tìm điểm ngắt câu phù hợp trong vùng gối đầu (lookback zone)
        $lookback_zone = mb_substr($chunk, -$chunk_overlap, null, 'UTF-8');
        $last_period   = mb_strrpos($lookback_zone, '.');
        $last_excl     = mb_strrpos($lookback_zone, '!');
        $last_quest    = mb_strrpos($lookback_zone, '?');
        $last_newline  = mb_strrpos($lookback_zone, "\n");

        $boundary_pos = false;
        foreach (array($last_period, $last_excl, $last_quest, $last_newline) as $pos) {
            if ($pos !== false) {
                if ($boundary_pos === false || $pos > $boundary_pos) {
                    $boundary_pos = $pos;
                }
            }
        }

        if ($boundary_pos !== false) {
            $cut_offset = ($end_index - $chunk_overlap) + $boundary_pos + 1;
            $chunk = mb_substr($text, $start, $cut_offset - $start, 'UTF-8');
            $chunks[] = trim($chunk);
            $start = $cut_offset;
        } else {
            // Fallback ngắt theo khoảng trắng
            $last_space = mb_strrpos($lookback_zone, ' ');
            if ($last_space !== false) {
                $cut_offset = ($end_index - $chunk_overlap) + $last_space + 1;
                $chunk = mb_substr($text, $start, $cut_offset - $start, 'UTF-8');
                $chunks[] = trim($chunk);
                $start = $cut_offset;
            } else {
                $chunks[] = trim($chunk);
                $start += ($chunk_size - $chunk_overlap);
            }
        }
    }

    return array_values(array_filter($chunks, function($c) {
        return mb_strlen(trim($c), 'UTF-8') >= 15;
    }));
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 5. VECTOR EMBEDDING & BỘ NHỚ ĐỆM (CACHE) GOOGLE GEMINI
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Tính Norm của Vector
 */
function flora_compute_vector_norm($vector) {
    if (!is_array($vector) || empty($vector)) return 0.0;
    $sum = 0.0;
    foreach ($vector as $v) {
        $sum += $v * $v;
    }
    return sqrt($sum);
}

/**
 * Sinh Vector Embedding cho 1 đoạn text (kèm Cache Database)
 */
function flora_generate_embedding($text, $api_key) {
    if (empty($text)) return null;

    global $wpdb;
    $hash = md5('models/gemini-embedding-001|v1beta|' . mb_strtolower(trim($text), 'UTF-8'));
    $table_cache = $wpdb->prefix . 'flora_ai_vector_cache';

    // 1. Kiểm tra cache CSDL trước
    $cached = $wpdb->get_row($wpdb->prepare("SELECT vector, vector_norm FROM $table_cache WHERE hash = %s LIMIT 1", $hash), ARRAY_A);
    if ($cached && !empty($cached['vector'])) {
        $vec = json_decode($cached['vector'], true);
        if (is_array($vec)) {
            return array('vector' => $vec, 'norm' => (float)$cached['vector_norm']);
        }
    }

    $payload = array(
        'model'   => 'models/gemini-embedding-001',
        'content' => array(
            'parts' => array(
                array('text' => $text)
            )
        )
    );

    // Gọi Gemini API v1beta
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:embedContent?key=" . $api_key;
    $args = array(
        'body'        => wp_json_encode($payload),
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'timeout'     => 15,
        'data_format' => 'body'
    );

    $response = wp_remote_post($url, $args);
    $vector = null;

    if (!is_wp_error($response)) {
        $res = json_decode(wp_remote_retrieve_body($response), true);
        $vector = $res['embedding']['values'] ?? null;
    }

    // Fallback sang text-embedding-004 nếu gemini-embedding-001 không có
    if (empty($vector)) {
        $payload['model'] = 'models/text-embedding-004';
        $url_fallback = "https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key=" . $api_key;
        $fb_resp = wp_remote_post($url_fallback, array(
            'body'        => wp_json_encode($payload),
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'timeout'     => 15,
            'data_format' => 'body'
        ));
        if (!is_wp_error($fb_resp)) {
            $fb_res = json_decode(wp_remote_retrieve_body($fb_resp), true);
            $vector = $fb_res['embedding']['values'] ?? null;
        }
    }

    if (!is_array($vector) || empty($vector)) {
        return null;
    }

    $norm = flora_compute_vector_norm($vector);
    $vec_json = wp_json_encode($vector);

    // Lưu cache
    $wpdb->replace(
        $table_cache,
        array(
            'hash'        => $hash,
            'vector'      => $vec_json,
            'vector_norm' => $norm,
            'created_at'  => current_time('mysql')
        ),
        array('%s', '%s', '%f', '%s')
    );

    return array('vector' => $vector, 'norm' => $norm);
}

/**
 * Sinh Vector Embeddings theo Batch (tối đa 100 mẩu/lần)
 */
function flora_generate_batch_embeddings($texts, $api_key) {
    if (empty($texts)) return array();

    $payload = array('requests' => array());
    foreach ($texts as $text) {
        $payload['requests'][] = array(
            'model'   => 'models/gemini-embedding-001',
            'content' => array(
                'parts' => array(array('text' => $text))
            )
        );
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-embedding-001:batchEmbedContents?key=" . $api_key;
    $args = array(
        'body'        => wp_json_encode($payload),
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'timeout'     => 60,
        'data_format' => 'body'
    );

    $response = wp_remote_post($url, $args);
    $embeddings = array();

    if (!is_wp_error($response)) {
        $res = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($res['embeddings'])) {
            foreach ($res['embeddings'] as $emb) {
                $embeddings[] = $emb['values'] ?? null;
            }
            return $embeddings;
        }
    }

    // Fallback sang text-embedding-004 nếu cần
    $payload['requests'] = array();
    foreach ($texts as $text) {
        $payload['requests'][] = array(
            'model'   => 'models/text-embedding-004',
            'content' => array(
                'parts' => array(array('text' => $text))
            )
        );
    }
    $url = "https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:batchEmbedContents?key=" . $api_key;
    $fb_resp = wp_remote_post($url, $args);
    if (!is_wp_error($fb_resp)) {
        $res = json_decode(wp_remote_retrieve_body($fb_resp), true);
        if (isset($res['embeddings'])) {
            foreach ($res['embeddings'] as $emb) {
                $embeddings[] = $emb['values'] ?? null;
            }
        }
    }

    return $embeddings;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 6. TÍNH TOÁN COSINE SIMILARITY & HYBRID RAG RETRIEVAL
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Tính Cosine Similarity giữa 2 Vector (Dot Product / (NormA * NormB))
 */
function flora_cosine_similarity($vec1, $vec2, $norm1 = null, $norm2 = null) {
    if (!is_array($vec1) || !is_array($vec2) || count($vec1) !== count($vec2)) {
        return 0.0;
    }

    $dot_product = 0.0;
    $count = count($vec1);
    for ($i = 0; $i < $count; $i++) {
        $dot_product += $vec1[$i] * $vec2[$i];
    }

    if ($norm1 === null) {
        $norm1 = flora_compute_vector_norm($vec1);
    }
    if ($norm2 === null) {
        $norm2 = flora_compute_vector_norm($vec2);
    }

    if ($norm1 == 0.0 || $norm2 == 0.0) {
        return 0.0;
    }

    return $dot_product / ($norm1 * $norm2);
}

/**
 * Truy hồi ngữ cảnh lai (Hybrid RAG Search)
 * Kết hợp Semantic Vector Search + Keyword Fulltext Matching
 */
function flora_rag_retrieve_context($query, $api_key = null, $top_k = null, $threshold = null) {
    global $wpdb;
    $settings = flora_get_rag_settings();

    if ($api_key === null) {
        $api_key = $settings['api_key'];
    }
    if ($top_k === null) {
        $top_k = (int)($settings['top_k'] ?? 6);
    }
    if ($threshold === null) {
        $threshold = (float)($settings['similarity_threshold'] ?? 0.40);
    }

    $clean_query = trim($query);
    if (mb_strlen($clean_query, 'UTF-8') < 2) {
        return array();
    }

    $table_docs   = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    // Kiểm tra xem đã có tài liệu nào được huấn luyện thành công chưa
    $trained_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_docs WHERE is_active = 1 AND status = 'trained'");
    if ($trained_count === 0) {
        return array();
    }

    $scored_chunks = array();

    // 1. Semantic Vector Search
    $query_emb = flora_generate_embedding($clean_query, $api_key);
    if ($query_emb && is_array($query_emb['vector'])) {
        $q_vector = $query_emb['vector'];
        $q_norm   = $query_emb['norm'];

        // Lấy danh sách chunks từ các tài liệu đang active
        $sql = "SELECT c.id, c.doc_id, c.chunk_index, c.content, c.vector, c.vector_norm, d.name AS doc_name, d.tags
                FROM $table_chunks c
                JOIN $table_docs d ON c.doc_id = d.id
                WHERE d.is_active = 1 AND d.status = 'trained'";
        $rows = $wpdb->get_results($sql, ARRAY_A);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (empty($row['vector'])) continue;
                $c_vector = json_decode($row['vector'], true);
                if (!is_array($c_vector)) continue;

                $c_norm = (float)($row['vector_norm'] ?: flora_compute_vector_norm($c_vector));
                $sim = flora_cosine_similarity($q_vector, $c_vector, $q_norm, $c_norm);

                if ($sim >= $threshold) {
                    $scored_chunks[$row['id']] = array(
                        'id'          => $row['id'],
                        'doc_id'      => $row['doc_id'],
                        'chunk_index' => $row['chunk_index'],
                        'doc_name'    => $row['doc_name'],
                        'tags'        => $row['tags'],
                        'content'     => $row['content'],
                        'score'       => round($sim, 4),
                        'method'      => 'vector'
                    );
                }
            }
        }
    }

    // Sắp xếp giảm dần theo điểm tương đồng
    if (!empty($scored_chunks)) {
        uasort($scored_chunks, function($a, $b) {
            return ($b['score'] <=> $a['score']);
        });
    }

    // 2. Bổ sung từ khóa Keyword/FullText Search nếu số kết quả chưa đủ Top-K
    $current_count = count($scored_chunks);
    if ($current_count < $top_k) {
        $needed = $top_k - $current_count;
        $exclude_ids = !empty($scored_chunks) ? implode(',', array_map('intval', array_keys($scored_chunks))) : '0';

        // Tách các từ khóa có độ dài >= 2
        $clean_words = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clean_query);
        $words = array_values(array_filter(explode(' ', mb_strtolower($clean_words, 'UTF-8')), function($w) {
            return mb_strlen($w, 'UTF-8') >= 2;
        }));

        if (!empty($words)) {
            $like_term = '%' . $wpdb->esc_like($words[0]) . '%';
            $sql_like = "SELECT c.id, c.doc_id, c.chunk_index, c.content, d.name AS doc_name, d.tags
                         FROM $table_chunks c
                         JOIN $table_docs d ON c.doc_id = d.id
                         WHERE d.is_active = 1 AND d.status = 'trained'
                           AND c.id NOT IN ($exclude_ids)
                           AND (c.content LIKE %s OR d.name LIKE %s OR d.tags LIKE %s)
                         ORDER BY c.id DESC
                         LIMIT %d";

            $like_rows = $wpdb->get_results($wpdb->prepare($sql_like, $like_term, $like_term, $like_term, $needed), ARRAY_A);
            if (!empty($like_rows)) {
                foreach ($like_rows as $lr) {
                    $scored_chunks[$lr['id']] = array(
                        'id'          => $lr['id'],
                        'doc_id'      => $lr['doc_id'],
                        'chunk_index' => $lr['chunk_index'],
                        'doc_name'    => $lr['doc_name'],
                        'tags'        => $lr['tags'],
                        'content'     => $lr['content'],
                        'score'       => 0.40, // Điểm cơ sở cho keyword matching
                        'method'      => 'keyword'
                    );
                }
            }
        }
    }

    // Cắt về đúng giới hạn top_k
    return array_slice(array_values($scored_chunks), 0, $top_k);
}
