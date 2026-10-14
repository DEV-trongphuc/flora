<?php
/**
 * Flora Dental Clinic - AI RAG Training & Knowledge Hub Manager
 * Quản lý Kho Tri Thức Huấn Luyện AI, Bóc tách File Word/PDF/Text,
 * Cắt lát Chunking, Vector Embeddings và RAG Search Playground trong WP-Admin.
 *
 * Tham chiếu thiết kế từ: D:\GITHUB_SPACE\MYERP (ai_training_handler.php & AITrainingPanel.tsx)
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/rag-helpers.php';

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. ĐĂNG KÝ MENU ADMIN "HUẤN LUYỆN AI (RAG)"
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_ai_training_admin_menu() {
    // 1. Menu chính độc lập trên Left Sidebar
    add_menu_page(
        'Huấn Luyện Bác Sĩ AI (RAG)',
        'Huấn Luyện AI',
        'manage_options',
        'flora-ai-training',
        'flora_render_ai_training_admin_page',
        'dashicons-superhero-alt',
        25.1
    );

    // 2. Submenu tiện ích nằm ngay dưới "Bảng Giá Flora"
    add_submenu_page(
        'flora-pricing',
        'Huấn Luyện Bác Sĩ AI (RAG)',
        '⚡ Huấn Luyện AI (RAG)',
        'manage_options',
        'flora-ai-training',
        'flora_render_ai_training_admin_page'
    );
}
add_action('admin_menu', 'flora_ai_training_admin_menu');

/**
 * Tắt toàn bộ thông báo rác / bản quyền từ các plugin khác trên trang Huấn Luyện AI
 */
function flora_ai_training_suppress_notices() {
    $screen = get_current_screen();
    if ($screen && (strpos($screen->id, 'flora-ai-training') !== false)) {
        remove_all_actions('admin_notices');
        remove_all_actions('all_admin_notices');
        remove_all_actions('user_admin_notices');
        remove_all_actions('network_admin_notices');
    }
}
add_action('in_admin_header', 'flora_ai_training_suppress_notices', 1);

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 2. CÁC AJAX ENDPOINTS CHO ADMIN QUẢN LÝ HUẤN LUYỆN
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * 2.1. Lấy danh sách tài liệu và cây thư mục (Tự động nạp mẫu hệ thống nếu trống)
 */
function flora_ajax_rag_list_docs() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền truy cập.'));
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    // Tự động nạp kho tri thức mẫu nếu bảng tài liệu đang trống
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_docs");
    if (empty($count) || intval($count) === 0) {
        flora_rag_seed_default_knowledge();
    }

    $docs = $wpdb->get_results("SELECT d.*, COUNT(c.id) as chunk_count 
        FROM $table_docs d 
        LEFT JOIN $table_chunks c ON d.id = c.doc_id 
        GROUP BY d.id 
        ORDER BY d.parent_id ASC, d.id DESC", ARRAY_A);

    wp_send_json_success(array('docs' => $docs ?: array()));
}
add_action('wp_ajax_flora_rag_list_docs', 'flora_ajax_rag_list_docs');

/**
 * 2.2. Tạo thư mục nhóm tri thức
 */
function flora_ajax_rag_create_folder() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    if (empty($name)) {
        wp_send_json_error(array('message' => 'Tên thư mục không được để trống.'));
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';

    $inserted = $wpdb->insert(
        $table_docs,
        array(
            'name'        => $name,
            'source_type' => 'folder',
            'parent_id'   => 0,
            'status'      => 'trained',
            'is_active'   => 1,
            'created_at'  => current_time('mysql')
        ),
        array('%s', '%s', '%d', '%s', '%d', '%s')
    );

    if ($inserted) {
        wp_send_json_success(array('message' => 'Đã tạo thư mục thành công!'));
    } else {
        wp_send_json_error(array('message' => 'Không thể tạo thư mục CSDL.'));
    }
}
add_action('wp_ajax_flora_rag_create_folder', 'flora_ajax_rag_create_folder');

/**
 * 2.3. Thêm hoặc Sửa tài liệu thủ công / web
 */
function flora_ajax_rag_save_doc() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $doc_id = isset($_POST['doc_id']) ? intval($_POST['doc_id']) : 0;
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $content = isset($_POST['content']) ? wp_unslash($_POST['content']) : '';
    $tags = isset($_POST['tags']) ? sanitize_text_field(wp_unslash($_POST['tags'])) : '';
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $source_type = isset($_POST['source_type']) ? sanitize_text_field(wp_unslash($_POST['source_type'])) : 'manual';

    if (empty($name)) {
        wp_send_json_error(array('message' => 'Tiêu đề tài liệu không được để trống.'));
    }

    // Nếu là nguồn web link crawl
    if ($source_type === 'web' && strpos($content, 'http') === 0) {
        $crawled = flora_fetch_web_content(trim($content));
        if (!empty($crawled)) {
            $content = $crawled;
        }
    }

    if (empty($content)) {
        wp_send_json_error(array('message' => 'Nội dung tài liệu không được để trống.'));
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';

    if ($doc_id > 0) {
        // Cập nhật tài liệu cũ (đổi trạng thái về pending để yêu cầu huấn luyện lại)
        $wpdb->update(
            $table_docs,
            array(
                'name'        => $name,
                'content'     => $content,
                'tags'        => $tags,
                'parent_id'   => $parent_id,
                'status'      => 'pending',
                'updated_at'  => current_time('mysql')
            ),
            array('id' => $doc_id),
            array('%s', '%s', '%s', '%d', '%s', '%s'),
            array('%d')
        );
        wp_send_json_success(array('message' => 'Đã cập nhật tài liệu thành công. Vui lòng bấm Huấn luyện lại.'));
    } else {
        // Thêm tài liệu mới
        $wpdb->insert(
            $table_docs,
            array(
                'name'        => $name,
                'content'     => $content,
                'tags'        => $tags,
                'source_type' => $source_type,
                'parent_id'   => $parent_id,
                'status'      => 'pending',
                'is_active'   => 1,
                'created_at'  => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
        );
        wp_send_json_success(array('message' => 'Đã thêm tài liệu mới thành công! Hãy bấm Huấn luyện để tạo Vector.'));
    }
}
add_action('wp_ajax_flora_rag_save_doc', 'flora_ajax_rag_save_doc');

/**
 * 2.4. Tải lên file huấn luyện (.docx, .pdf, .txt)
 */
function flora_ajax_rag_upload_file() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền tải file lên.'));
    }

    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(array('message' => 'Không tìm thấy file hoặc xảy ra lỗi tải file.'));
    }

    $file = $_FILES['file'];
    $parent_id = isset($_POST['parent_id']) ? intval($_POST['parent_id']) : 0;
    $tags = isset($_POST['tags']) ? sanitize_text_field(wp_unslash($_POST['tags'])) : '';
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    $allowed = array('docx', 'pdf', 'txt', 'md', 'json');
    if (!in_array($ext, $allowed)) {
        wp_send_json_error(array('message' => 'Định dạng file không hỗ trợ. Vui lòng chọn .docx, .pdf, .txt, .md, .json'));
    }

    // Thư mục lưu trữ an toàn trong wp-content/uploads/flora-training/
    $upload_dir = wp_upload_dir();
    $target_dir = $upload_dir['basedir'] . '/flora-training/';
    if (!file_exists($target_dir)) {
        wp_mkdir_p($target_dir);
    }

    $safe_name = time() . '_' . sanitize_file_name($file['name']);
    $target_path = $target_dir . $safe_name;

    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        wp_send_json_error(array('message' => 'Không thể lưu file trên máy chủ.'));
    }

    $file_url = $upload_dir['baseurl'] . '/flora-training/' . $safe_name;
    $raw_text = '';

    // Bóc tách nội dung theo định dạng file
    if ($ext === 'docx') {
        $raw_text = flora_extract_docx_text($target_path);
    } elseif ($ext === 'pdf') {
        $settings = flora_get_rag_settings();
        $api_key = $settings['api_key'];
        if (empty($api_key)) {
            $raw_text = "Tài liệu PDF: " . $file['name'] . "\n(Cần cấu hình Gemini API Key để bóc tách văn bản qua Vision OCR)";
        } else {
            $raw_text = flora_extract_pdf_text_via_gemini($target_path, $api_key);
        }
    } else {
        $raw_text = file_get_contents($target_path);
    }

    if (empty($raw_text)) {
        $raw_text = "Tài liệu đính kèm: " . $file['name'];
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';

    $inserted = $wpdb->insert(
        $table_docs,
        array(
            'name'        => sanitize_text_field($file['name']),
            'content'     => $raw_text,
            'tags'        => $tags,
            'source_type' => 'file',
            'parent_id'   => $parent_id,
            'file_path'   => $file_url,
            'file_size'   => $file['size'],
            'status'      => 'pending',
            'is_active'   => 1,
            'created_at'  => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s')
    );

    if ($inserted) {
        wp_send_json_success(array('message' => 'Đã tải lên và bóc tách nội dung file thành công! Sẵn sàng huấn luyện.'));
    } else {
        wp_send_json_error(array('message' => 'Lỗi lưu thông tin file vào CSDL.'));
    }
}
add_action('wp_ajax_flora_rag_upload_file', 'flora_ajax_rag_upload_file');

/**
 * 2.5. Xóa tài liệu hoặc thư mục
 */
function flora_ajax_rag_delete_doc() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $doc_id = isset($_POST['doc_id']) ? intval($_POST['doc_id']) : 0;
    if ($doc_id <= 0) {
        wp_send_json_error(array('message' => 'ID tài liệu không hợp lệ.'));
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    // Xóa các chunks liên quan
    $wpdb->delete($table_chunks, array('doc_id' => $doc_id), array('%d'));

    // Chuyển các con của thư mục (nếu có) về thư mục gốc
    $wpdb->update($table_docs, array('parent_id' => 0), array('parent_id' => $doc_id), array('%d'), array('%d'));

    // Xóa tài liệu
    $wpdb->delete($table_docs, array('id' => $doc_id), array('%d'));

    wp_send_json_success(array('message' => 'Đã xóa tài liệu và các phân mảnh vector liên quan thành công!'));
}
add_action('wp_ajax_flora_rag_delete_doc', 'flora_ajax_rag_delete_doc');

/**
 * 2.6. Bật / Tắt kích hoạt tài liệu
 */
function flora_ajax_rag_toggle_doc() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $doc_id = isset($_POST['doc_id']) ? intval($_POST['doc_id']) : 0;
    $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 0;

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';

    $wpdb->update($table_docs, array('is_active' => $is_active), array('id' => $doc_id), array('%d'), array('%d'));
    wp_send_json_success(array('message' => 'Đã cập nhật trạng thái tài liệu.'));
}
add_action('wp_ajax_flora_rag_toggle_doc', 'flora_ajax_rag_toggle_doc');

/**
 * 2.7. Xem trước các phân mảnh (Preview Chunks)
 */
function flora_ajax_rag_preview_chunks() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $doc_id = isset($_POST['doc_id']) ? intval($_POST['doc_id']) : 0;
    global $wpdb;
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    $chunks = $wpdb->get_results($wpdb->prepare("SELECT id, chunk_index, content, vector_norm FROM $table_chunks WHERE doc_id = %d ORDER BY chunk_index ASC", $doc_id), ARRAY_A);

    wp_send_json_success(array('chunks' => $chunks ?: array()));
}
add_action('wp_ajax_flora_rag_preview_chunks', 'flora_ajax_rag_preview_chunks');

/**
 * 2.8. Huấn luyện (Train) tài liệu: Cắt Chunks + Embeddings + Lưu CSDL
 */
function flora_ajax_rag_train_docs() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $settings = flora_get_rag_settings();
    $api_key = $settings['api_key'];

    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'Vui lòng cấu hình Gemini API Key trước khi huấn luyện.'));
    }

    $doc_ids = isset($_POST['doc_ids']) ? array_map('intval', (array)$_POST['doc_ids']) : array();
    if (empty($doc_ids)) {
        wp_send_json_error(array('message' => 'Không tìm thấy tài liệu nào được chọn để huấn luyện.'));
    }

    $chunk_size = (int)($settings['chunk_size'] ?? 700);
    $chunk_overlap = (int)($settings['chunk_overlap'] ?? 150);

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    $trained_count = 0;
    $total_chunks_created = 0;
    $error_msg = '';

    foreach ($doc_ids as $id) {
        if ($id <= 0) continue;

        $doc = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_docs WHERE id = %d", $id), ARRAY_A);
        if (!$doc || $doc['source_type'] === 'folder') continue;

        $raw_text = trim($doc['content'] ?? '');
        if (empty($raw_text)) {
            $wpdb->update($table_docs, array('status' => 'error'), array('id' => $id), array('%s'), array('%d'));
            continue;
        }

        // 1. Phân mảnh văn bản thông minh
        $chunks = flora_chunk_text($raw_text, $chunk_size, $chunk_overlap);
        if (empty($chunks)) {
            $wpdb->update($table_docs, array('status' => 'error'), array('id' => $id), array('%s'), array('%d'));
            continue;
        }

        // 2. Xóa các chunks cũ của doc này
        $wpdb->delete($table_chunks, array('doc_id' => $id), array('%d'));

        // 3. Vector Embedding từng mẩu (tận dụng cache có sẵn)
        $has_error = false;
        $uncached_chunks = array();
        $uncached_indices = array();
        $processed_chunks = array();

        foreach ($chunks as $c_idx => $chunk) {
            $c_text = trim($chunk);
            if (empty($c_text)) continue;

            $hash = md5('models/gemini-embedding-001|v1beta|' . mb_strtolower($c_text, 'UTF-8'));
            $table_cache = $wpdb->prefix . 'flora_ai_vector_cache';
            $cached = $wpdb->get_row($wpdb->prepare("SELECT vector, vector_norm FROM $table_cache WHERE hash = %s LIMIT 1", $hash), ARRAY_A);

            if ($cached && !empty($cached['vector'])) {
                $vec = json_decode($cached['vector'], true);
                if (is_array($vec)) {
                    $processed_chunks[$c_idx] = array(
                        'content' => $c_text,
                        'vector'  => $cached['vector'],
                        'norm'    => (float)$cached['vector_norm']
                    );
                    continue;
                }
            }

            $uncached_chunks[] = $c_text;
            $uncached_indices[] = $c_idx;
        }

        // Gọi Gemini Batch Embedding cho các mẩu chưa có trong cache
        if (!empty($uncached_chunks)) {
            $batches = array_chunk($uncached_chunks, 50);
            $offset = 0;

            foreach ($batches as $b_idx => $b_items) {
                if ($b_idx > 0) {
                    usleep(300000); // 300ms pacing delay tránh rate limit
                }

                $vectors = flora_generate_batch_embeddings($b_items, $api_key);
                if (empty($vectors) || count($vectors) !== count($b_items)) {
                    $has_error = true;
                    $error_msg = 'Lỗi gọi API Google Gemini Embeddings.';
                    break;
                }

                foreach ($b_items as $i => $item_text) {
                    $vec = $vectors[$i] ?? null;
                    if (!is_array($vec)) {
                        $has_error = true;
                        break;
                    }

                    $norm = flora_compute_vector_norm($vec);
                    $vec_json = wp_json_encode($vec);
                    $orig_idx = $uncached_indices[$offset + $i];

                    $processed_chunks[$orig_idx] = array(
                        'content' => $item_text,
                        'vector'  => $vec_json,
                        'norm'    => $norm
                    );

                    // Lưu vào cache
                    $hash = md5('models/gemini-embedding-001|v1beta|' . mb_strtolower($item_text, 'UTF-8'));
                    $table_cache = $wpdb->prefix . 'flora_ai_vector_cache';
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
                }

                if ($has_error) break;
                $offset += count($b_items);
            }
        }

        if (!$has_error && !empty($processed_chunks)) {
            // Lưu tất cả chunks vào CSDL
            ksort($processed_chunks);
            foreach ($processed_chunks as $c_idx => $p) {
                $wpdb->insert(
                    $table_chunks,
                    array(
                        'doc_id'      => $id,
                        'chunk_index' => $c_idx,
                        'content'     => $p['content'],
                        'vector'      => $p['vector'],
                        'vector_norm' => $p['norm'],
                        'created_at'  => current_time('mysql')
                    ),
                    array('%d', '%d', '%s', '%s', '%f', '%s')
                );
                $total_chunks_created++;
            }

            $wpdb->update($table_docs, array('status' => 'trained'), array('id' => $id), array('%s'), array('%d'));
            $trained_count++;
        } else {
            $wpdb->update($table_docs, array('status' => 'error'), array('id' => $id), array('%s'), array('%d'));
        }
    }

    if ($trained_count > 0) {
        wp_send_json_success(array(
            'message' => "Đã hoàn tất huấn luyện thành công $trained_count tài liệu (tạo $total_chunks_created phân mảnh vector RAG)!"
        ));
    } else {
        wp_send_json_error(array('message' => !empty($error_msg) ? $error_msg : 'Huấn luyện tài liệu không thành công.'));
    }
}
add_action('wp_ajax_flora_rag_train_docs', 'flora_ajax_rag_train_docs');

/**
 * 2.9. Nạp Kho Tri Thức Mẫu Từ Thư Mục Hệ Thống và Tự Động Phân Mảnh (Chunks)
 */
function flora_rag_seed_default_knowledge() {
    $kb_dir = get_template_directory() . '/knowledge_base';
    if (!is_dir($kb_dir)) {
        $kb_dir = dirname(dirname(dirname(__DIR__))) . '/knowledge_base';
    }
    if (!is_dir($kb_dir)) {
        $kb_dir = dirname(get_template_directory()) . '/../knowledge_base';
    }

    if (!is_dir($kb_dir)) {
        return array('success' => false, 'message' => 'Không tìm thấy thư mục knowledge_base trong theme.');
    }

    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $table_chunks = $wpdb->prefix . 'flora_ai_training_chunks';

    $folder_map = array(
        '01_bang_gia_dich_vu'           => 'Bảng Giá & Chi Phí Điều Trị',
        '02_goi_dich_vu_va_voucher'     => 'Gói Dịch Vụ Khám & Voucher Ưu Đãi',
        '03_chinh_sach_va_bao_hanh'     => 'Chính Sách Bảo Hành & Trả Góp',
        '04_chuyen_mon_va_bac_si'       => 'Đội Ngũ Bác Sĩ & Công Nghệ Thụy Sĩ',
        '05_kich_ban_tu_van_va_cham_soc' => 'Kịch Bản Tư Vấn & Quy Tắc Y Khoa'
    );

    $imported_folders = 0;
    $imported_docs = 0;
    $created_chunks = 0;

    $settings = flora_get_rag_settings();
    $chunk_size = !empty($settings['chunk_size']) ? intval($settings['chunk_size']) : 700;
    $chunk_overlap = !empty($settings['chunk_overlap']) ? intval($settings['chunk_overlap']) : 150;

    foreach ($folder_map as $dir_name => $folder_label) {
        $sub_dir = $kb_dir . '/' . $dir_name;
        if (!is_dir($sub_dir)) continue;

        // Tạo hoặc tìm thư mục cha
        $existing_folder = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_docs WHERE name = %s AND source_type = 'folder' LIMIT 1", $folder_label));
        $folder_id = 0;

        if ($existing_folder) {
            $folder_id = intval($existing_folder);
        } else {
            $wpdb->insert(
                $table_docs,
                array(
                    'name'        => $folder_label,
                    'source_type' => 'folder',
                    'parent_id'   => 0,
                    'status'      => 'trained',
                    'is_active'   => 1,
                    'created_at'  => current_time('mysql')
                ),
                array('%s', '%s', '%d', '%s', '%d', '%s')
            );
            $folder_id = $wpdb->insert_id;
            $imported_folders++;
        }

        // Quét các file trong thư mục
        $files = glob($sub_dir . '/*.{md,txt}', GLOB_BRACE);
        if (!empty($files)) {
            foreach ($files as $file_path) {
                $file_name = basename($file_path);
                $content = file_get_contents($file_path);
                if (empty($content)) continue;

                // Lấy tiêu đề từ dòng đầu tiên nếu có định dạng Markdown # ...
                $title = pathinfo($file_name, PATHINFO_FILENAME);
                if (preg_match('/^#\s+(.+)$/m', $content, $m)) {
                    $title = trim($m[1]);
                }

                // Kiểm tra xem đã nạp chưa
                $doc_id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_docs WHERE name = %s AND parent_id = %d LIMIT 1", $title, $folder_id));
                if (!$doc_id) {
                    $wpdb->insert(
                        $table_docs,
                        array(
                            'name'        => $title,
                            'content'     => $content,
                            'tags'        => $dir_name,
                            'source_type' => 'manual',
                            'parent_id'   => $folder_id,
                            'status'      => 'pending',
                            'is_active'   => 1,
                            'created_at'  => current_time('mysql')
                        ),
                        array('%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s')
                    );
                    $doc_id = $wpdb->insert_id;
                    $imported_docs++;
                } else {
                    $doc_id = intval($doc_id);
                }

                // Tự động phân mảnh Chunks cho tài liệu nếu chưa có Chunks
                $chunk_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_chunks WHERE doc_id = %d", $doc_id));
                if (empty($chunk_count) || intval($chunk_count) === 0) {
                    $chunks = flora_chunk_text($content, $chunk_size, $chunk_overlap);
                    foreach ($chunks as $c_idx => $c_text) {
                        $wpdb->insert(
                            $table_chunks,
                            array(
                                'doc_id'      => $doc_id,
                                'chunk_index' => $c_idx,
                                'content'     => $c_text,
                                'created_at'  => current_time('mysql')
                            ),
                            array('%d', '%d', '%s', '%s')
                        );
                        $created_chunks++;
                    }
                }
            }
        }
    }

    return array(
        'success'          => true,
        'imported_folders' => $imported_folders,
        'imported_docs'    => $imported_docs,
        'created_chunks'   => $created_chunks,
        'message'          => "Đã nạp thành công $imported_folders thư mục, $imported_docs tài liệu tri thức và tạo $created_chunks phân mảnh (chunks) chuẩn bị huấn luyện!"
    );
}

function flora_ajax_rag_seed_knowledge() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $res = flora_rag_seed_default_knowledge();
    if ($res['success']) {
        wp_send_json_success(array('message' => $res['message']));
    } else {
        wp_send_json_error(array('message' => $res['message']));
    }
}
add_action('wp_ajax_flora_rag_seed_knowledge', 'flora_ajax_rag_seed_knowledge');

/**
 * 2.10. Cập nhật cấu hình RAG Sliders & Persona
 */
function flora_ajax_rag_save_settings() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền cập nhật cấu hình.'));
    }

    $settings = array(
        'is_enabled'           => isset($_POST['is_enabled']) ? intval($_POST['is_enabled']) : 1,
        'bot_name'             => sanitize_text_field(wp_unslash($_POST['bot_name'] ?? 'Bác Sĩ Tư Vấn Flora')),
        'clinic_address'       => sanitize_text_field(wp_unslash($_POST['clinic_address'] ?? '')),
        'clinic_hotline'       => sanitize_text_field(wp_unslash($_POST['clinic_hotline'] ?? '')),
        'welcome_msg'          => sanitize_textarea_field(wp_unslash($_POST['welcome_msg'] ?? '')),
        'persona_prompt'       => sanitize_textarea_field(wp_unslash($_POST['persona_prompt'] ?? '')),
        'similarity_threshold' => floatval($_POST['similarity_threshold'] ?? 0.40),
        'top_k'                => intval($_POST['top_k'] ?? 6),
        'chunk_size'           => intval($_POST['chunk_size'] ?? 700),
        'chunk_overlap'        => intval($_POST['chunk_overlap'] ?? 150),
        'temperature'          => floatval($_POST['temperature'] ?? 0.25),
        'max_output_tokens'    => intval($_POST['max_output_tokens'] ?? 1024),
        'model'                => sanitize_text_field(wp_unslash($_POST['model'] ?? 'gemini-3.5-flash-lite')),
        'api_key'              => sanitize_text_field(wp_unslash($_POST['api_key'] ?? ''))
    );

    flora_update_rag_settings($settings);
    wp_send_json_success(array('message' => 'Đã lưu cấu hình RAG & Persona thành công!'));
}
add_action('wp_ajax_flora_rag_save_settings', 'flora_ajax_rag_save_settings');

/**
 * 2.11. Thử nghiệm trực tiếp RAG Query (Live Playground Test)
 */
function flora_ajax_rag_test_query() {
    check_ajax_referer('flora_rag_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
    }

    $query = isset($_POST['query']) ? sanitize_textarea_field(wp_unslash($_POST['query'])) : '';
    if (empty($query)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập câu hỏi thử nghiệm.'));
    }

    $settings = flora_get_rag_settings();
    $api_key = $settings['api_key'];

    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'Hệ thống chưa được cấu hình Gemini API Key.'));
    }

    // 1. Truy hồi Context qua RAG
    $retrieved_chunks = flora_rag_retrieve_context($query, $api_key, $settings['top_k'], $settings['similarity_threshold']);

    // 2. Xây dựng ngữ cảnh tri thức
    $rag_context_text = "";
    if (!empty($retrieved_chunks)) {
        $rag_context_text = "=== KHO TRI THỨC ĐƯỢC TRÍCH XUẤT TỪ CƠ SỞ DỮ LIỆU NHA KHOA FLORA ===\n";
        foreach ($retrieved_chunks as $idx => $chunk) {
            $source_num = $idx + 1;
            $rag_context_text .= "\n[Nguồn {$source_num} - {$chunk['doc_name']} (Điểm tương đồng: " . round($chunk['score'] * 100, 1) . "%)]:\n" . $chunk['content'] . "\n";
        }
    } else {
        $rag_context_text = "(Không tìm thấy mẩu tri thức nào vượt ngưỡng tương đồng RAG trong CSDL. Hãy trả lời dựa trên thông tin phòng khám hoặc hướng dẫn khách liên hệ bác sĩ).";
    }

    // 3. Xây dựng System Prompt linh hoạt
    $system_instruction = <<<EOT
{$settings['persona_prompt']}

THÔNG TIN PHÒNG KHÁM:
- Đại diện tư vấn: {$settings['bot_name']}
- Địa chỉ: {$settings['clinic_address']}
- Hotline / Zalo: {$settings['clinic_hotline']} (Hỗ trợ 24/7)

{$rag_context_text}

QUY TẮC CỐT LÕI:
1. Trả lời chính xác, trung thực dựa trên nguồn tri thức nha khoa trích xuất ở trên. Tuyệt đối không tự bịa đặt mức giá hoặc ưu đãi không có.
2. Trả lời súc tích, chuyên nghiệp, ân cần như Bác sĩ khám bệnh.
3. Khi giải đáp xong, mời khách để lại Số Điện Thoại (hoặc Zalo) để Bác sĩ/Chuyên viên liên hệ tư vấn chi tiết và sắp xếp lịch hẹn phù hợp.
EOT;

    // 4. Gọi Gemini API
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$settings['model']}:generateContent?key={$api_key}";
    $payload = array(
        'system_instruction' => array(
            'parts' => array(array('text' => $system_instruction))
        ),
        'contents' => array(
            array('role' => 'user', 'parts' => array(array('text' => $query)))
        ),
        'generationConfig' => array(
            'temperature'     => (float)$settings['temperature'],
            'maxOutputTokens' => (int)$settings['max_output_tokens']
        )
    );

    $response = wp_remote_post($endpoint, array(
        'body'        => wp_json_encode($payload),
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'timeout'     => 30,
        'data_format' => 'body'
    ));

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => 'Lỗi kết nối máy chủ AI: ' . $response->get_error_message()));
    }

    $res_body = wp_remote_retrieve_body($response);
    $res_data = json_decode($res_body, true);
    $ai_reply = $res_data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($ai_reply)) {
        $err = $res_data['error']['message'] ?? 'Không nhận được phản hồi từ mô hình AI.';
        wp_send_json_error(array('message' => $err));
    }

    wp_send_json_success(array(
        'reply'            => trim($ai_reply),
        'retrieved_chunks' => $retrieved_chunks,
        'query'            => $query
    ));
}
add_action('wp_ajax_flora_rag_test_query', 'flora_ajax_rag_test_query');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 3. GIAO DIỆN QUẢN TRỊ WP-ADMIN (LUXURY SAAS EXPERIENCE)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_render_ai_training_admin_page() {
    $nonce = wp_create_nonce('flora_rag_nonce');
    $settings = flora_get_rag_settings();

    // Tự động nạp mẫu hệ thống nếu CSDL chưa có dữ liệu
    global $wpdb;
    $table_docs = $wpdb->prefix . 'flora_ai_training_docs';
    $existing_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_docs");
    if (empty($existing_count) || intval($existing_count) === 0) {
        flora_rag_seed_default_knowledge();
    }
    ?>
    <div class="wrap flora-rag-admin-wrap">
        <!-- Header Banner -->
        <div class="flora-rag-header">
            <div class="flora-header-left">
                <h1><span class="dashicons dashicons-superhero-alt" style="font-size: 28px; vertical-align: middle; margin-right: 8px; color: #0493f1;"></span> Trung Tâm Huấn Luyện Bác Sĩ AI (RAG Knowledge Hub)</h1>
                <p class="description">Quản lý kho tri thức nha khoa, bóc tách tài liệu Word (.docx), PDF, Text, cắt lát phân mảnh (chunking) và vector hóa embeddings cho Bác Sĩ AI.</p>
            </div>
            <div class="flora-header-actions">
                <button type="button" class="button button-secondary" id="btn-seed-knowledge" title="Nạp lại bộ bảng giá, gói khám, bảo hành và hồ sơ bác sĩ từ thư mục hệ thống">
                    <span class="dashicons dashicons-download" style="vertical-align: middle;"></span> Nạp Tri Thức Mẫu
                </button>
                <button type="button" class="button button-primary button-hero" id="btn-train-all-docs" style="margin-left: 10px;">
                    <span class="dashicons dashicons-update" style="vertical-align: middle;"></span> ⚡ Huấn Luyện Tất Cả
                </button>
            </div>
        </div>

        <!-- Global Notice Box -->
        <div id="flora-rag-notice" class="notice" style="display: none; margin: 15px 0;">
            <p></p>
        </div>

        <!-- Main Tabs Navigation -->
        <div class="flora-rag-container">
            <ul class="flora-nav-tabs">
                <li class="active" data-tab="tab-knowledge-docs"><i class="dashicons dashicons-portfolio"></i> 1. Kho Tri Thức & Tài Liệu</li>
                <li data-tab="tab-rag-tuners"><i class="dashicons dashicons-admin-settings"></i> 2. Cài Đặt RAG & Persona</li>
                <li data-tab="tab-rag-playground" class="tab-highlight"><i class="dashicons dashicons-format-chat"></i> 3. Phòng Thử Nghiệm Live (Playground)</li>
            </ul>

            <div class="flora-tabs-content">
                <!-- ─────────────────────────────────────────────
                     TAB 1: KHO TRI THỨC & TÀI LIỆU
                     ───────────────────────────────────────────── -->
                <div class="flora-tab-pane active" id="tab-knowledge-docs">
                    <!-- Stats Overview -->
                    <div class="flora-stats-grid">
                        <div class="flora-stat-card">
                            <div class="stat-icon" style="background: rgba(4, 147, 241, 0.12); color: #0493f1;"><i class="dashicons dashicons-media-document"></i></div>
                            <div class="stat-content">
                                <span class="stat-num" id="stat-total-docs">0</span>
                                <span class="stat-label">Tổng Tài Liệu</span>
                            </div>
                        </div>
                        <div class="flora-stat-card">
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;"><i class="dashicons dashicons-database"></i></div>
                            <div class="stat-content">
                                <span class="stat-num" id="stat-trained-chunks">0</span>
                                <span class="stat-label">Mảnh Vector (Chunks)</span>
                            </div>
                        </div>
                        <div class="flora-stat-card">
                            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;"><i class="dashicons dashicons-category"></i></div>
                            <div class="stat-content">
                                <span class="stat-num" id="stat-total-folders">0</span>
                                <span class="stat-label">Thư Mục Nhóm</span>
                            </div>
                        </div>
                        <div class="flora-stat-card">
                            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;"><i class="dashicons dashicons-yes-alt"></i></div>
                            <div class="stat-content">
                                <span class="stat-num" id="stat-active-docs">0</span>
                                <span class="stat-label">Đang Bật Hoạt Động</span>
                            </div>
                        </div>
                    </div>

                    <!-- Toolbar Actions -->
                    <div class="flora-doc-toolbar">
                        <div class="toolbar-left">
                            <button type="button" class="button button-primary" id="btn-open-add-modal">
                                <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm Tài Liệu
                            </button>
                            <button type="button" class="button" id="btn-open-upload-modal">
                                <span class="dashicons dashicons-upload" style="vertical-align: middle;"></span> Tải File (.docx, .pdf, .txt)
                            </button>
                            <button type="button" class="button" id="btn-open-folder-modal">
                                <span class="dashicons dashicons-category" style="vertical-align: middle;"></span> Tạo Thư Mục
                            </button>
                        </div>
                        <div class="toolbar-right">
                            <input type="text" id="flora-doc-search" placeholder="Tìm tài liệu tri thức..." style="width: 240px;" />
                        </div>
                    </div>

                    <!-- Progress Bar for Batch Training -->
                    <div id="flora-training-progress-wrap" style="display: none; margin: 15px 0; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px;">
                        <div style="display: flex; justify-content: space-between; font-weight: 600; margin-bottom: 8px; font-size: 13px;">
                            <span id="training-progress-text"><i class="dashicons dashicons-update flora-spin"></i> Đang huấn luyện vector hóa...</span>
                            <span id="training-progress-pct">0%</span>
                        </div>
                        <div style="background: #e2e8f0; border-radius: 999px; height: 10px; overflow: hidden;">
                            <div id="training-progress-bar" style="background: linear-gradient(90deg, #0493f1 0%, #10b981 100%); width: 0%; height: 100%; transition: width 0.3s;"></div>
                        </div>
                    </div>

                    <!-- Documents & Folders Tree Container -->
                    <div class="flora-tree-container" id="flora-docs-tree">
                        <div style="text-align: center; padding: 40px; color: #94a3b8;">
                            <span class="dashicons dashicons-update flora-spin" style="font-size: 32px; width: 32px; height: 32px;"></span>
                            <p style="margin-top: 10px;">Đang tải danh sách tri thức...</p>
                        </div>
                    </div>
                </div>

                <!-- ─────────────────────────────────────────────
                     TAB 2: CÀI ĐẶT RAG SLIDERS & PERSONA
                     ───────────────────────────────────────────── -->
                <div class="flora-tab-pane" id="tab-rag-tuners">
                    <form id="flora-rag-settings-form">
                        <!-- Box 1: Hồ Sơ Trợ Lý -->
                        <div class="flora-card-box" style="margin-bottom: 20px;">
                            <h3><i class="dashicons dashicons-businessman" style="color: #0493f1;"></i> 1. Hồ Sơ Bác Sĩ Tư Vấn AI</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="rag-bot-name">Tên Bác Sĩ / Trợ Lý AI</label></th>
                                    <td>
                                        <input type="text" id="rag-bot-name" name="bot_name" class="regular-text" value="<?php echo esc_attr($settings['bot_name']); ?>" style="width: 380px;" />
                                        <p class="description">Tên đại diện xuất hiện trong tin nhắn chào mừng và tiêu đề chatbox.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rag-clinic-hotline">Hotline / Zalo Tư Vấn</label></th>
                                    <td>
                                        <input type="text" id="rag-clinic-hotline" name="clinic_hotline" class="regular-text" value="<?php echo esc_attr($settings['clinic_hotline']); ?>" style="width: 380px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rag-clinic-address">Địa Chỉ Phòng Khám</label></th>
                                    <td>
                                        <input type="text" id="rag-clinic-address" name="clinic_address" class="large-text" value="<?php echo esc_attr($settings['clinic_address']); ?>" style="width: 100%; max-width: 650px;" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rag-welcome-msg">Lời Chào Mở Đầu Khung Chat</label></th>
                                    <td>
                                        <textarea id="rag-welcome-msg" name="welcome_msg" class="large-text" rows="3" style="width: 100%; max-width: 650px;"><?php echo esc_textarea($settings['welcome_msg']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rag-persona-prompt">Chỉ Thị Phong Cách (Persona Prompt)</label></th>
                                    <td>
                                        <textarea id="rag-persona-prompt" name="persona_prompt" class="large-text" rows="4" style="width: 100%; max-width: 650px;"><?php echo esc_textarea($settings['persona_prompt']); ?></textarea>
                                        <p class="description">Định hình văn phong Bác sĩ y khoa, quy tắc xin số điện thoại chân thành và cấm đoán bịa đặt. <em>Toàn bộ bảng giá dịch vụ sẽ được RAG tự động truy hồi theo câu hỏi của khách hàng, không cần viết lại vào đây.</em></p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Box 2: Tinh Chỉnh Tham Số RAG Sliders -->
                        <div class="flora-card-box" style="margin-bottom: 20px;">
                            <h3><i class="dashicons dashicons-admin-settings" style="color: #0493f1;"></i> 2. Tinh Chỉnh Tham Số RAG (Retrieval Tuners)</h3>
                            
                            <div class="flora-sliders-grid">
                                <!-- Slider 1: Similarity Threshold -->
                                <div class="flora-slider-group">
                                    <div class="slider-header">
                                        <label for="rag-similarity-threshold">Ngưỡng Tương Đồng Cosine (Similarity Threshold)</label>
                                        <span class="slider-val-badge" id="val-similarity-threshold"><?php echo $settings['similarity_threshold']; ?></span>
                                    </div>
                                    <input type="range" id="rag-similarity-threshold" name="similarity_threshold" min="0.25" max="0.75" step="0.05" value="<?php echo $settings['similarity_threshold']; ?>" class="rag-slider" />
                                    <div class="slider-ticks"><span>0.25 (Rộng rãi)</span><span>0.45 (Chuẩn khuyên dùng)</span><span>0.75 (Cực kỳ nghiêm ngặt)</span></div>
                                    <p class="slider-desc">Chỉ những mẩu tri thức có điểm tương đồng vector lớn hơn hoặc bằng ngưỡng này mới được trích xuất đưa vào câu trả lời của AI.</p>
                                </div>

                                <!-- Slider 2: Top-K Chunks -->
                                <div class="flora-slider-group">
                                    <div class="slider-header">
                                        <label for="rag-top-k">Số Mảnh Tri Thức Tối Đa (Top-K Chunks)</label>
                                        <span class="slider-val-badge" id="val-top-k"><?php echo $settings['top_k']; ?> mảnh</span>
                                    </div>
                                    <input type="range" id="rag-top-k" name="top_k" min="2" max="12" step="1" value="<?php echo $settings['top_k']; ?>" class="rag-slider" />
                                    <div class="slider-ticks"><span>2 mảnh</span><span>6 mảnh (Khuyên dùng)</span><span>12 mảnh</span></div>
                                    <p class="slider-desc">Số lượng phân mảnh văn bản liên quan nhất được nhúng vào context cho mỗi câu hỏi.</p>
                                </div>

                                <!-- Slider 3: Chunk Size -->
                                <div class="flora-slider-group">
                                    <div class="slider-header">
                                        <label for="rag-chunk-size">Kích Thước Mảnh Cắt (Chunk Size)</label>
                                        <span class="slider-val-badge" id="val-chunk-size"><?php echo $settings['chunk_size']; ?> ký tự</span>
                                    </div>
                                    <input type="range" id="rag-chunk-size" name="chunk_size" min="400" max="1500" step="50" value="<?php echo $settings['chunk_size']; ?>" class="rag-slider" />
                                    <div class="slider-ticks"><span>400 ký tự</span><span>700 ký tự</span><span>1500 ký tự</span></div>
                                    <p class="slider-desc">Độ dài ký tự tối đa của một phân mảnh khi cắt lát tài liệu.</p>
                                </div>

                                <!-- Slider 4: Chunk Overlap -->
                                <div class="flora-slider-group">
                                    <div class="slider-header">
                                        <label for="rag-chunk-overlap">Độ Gối Đầu (Chunk Overlap)</label>
                                        <span class="slider-val-badge" id="val-chunk-overlap"><?php echo $settings['chunk_overlap']; ?> ký tự</span>
                                    </div>
                                    <input type="range" id="rag-chunk-overlap" name="chunk_overlap" min="50" max="300" step="25" value="<?php echo $settings['chunk_overlap']; ?>" class="rag-slider" />
                                    <div class="slider-ticks"><span>50 ký tự</span><span>150 ký tự</span><span>300 ký tự</span></div>
                                    <p class="slider-desc">Độ dài phần văn bản gối đầu lặp lại giữa 2 mảnh liên tiếp để tránh mất ngữ cảnh giữa các câu.</p>
                                </div>

                                <!-- Slider 5: Temperature -->
                                <div class="flora-slider-group">
                                    <div class="slider-header">
                                        <label for="rag-temperature">Nhiệt Độ Sáng Tạo (Temperature)</label>
                                        <span class="slider-val-badge" id="val-temperature"><?php echo $settings['temperature']; ?></span>
                                    </div>
                                    <input type="range" id="rag-temperature" name="temperature" min="0.1" max="0.7" step="0.05" value="<?php echo $settings['temperature']; ?>" class="rag-slider" />
                                    <div class="slider-ticks"><span>0.1 (Chính xác tuyệt đối)</span><span>0.25 (Chuẩn Y khoa)</span><span>0.7 (Tự do sáng tạo)</span></div>
                                    <p class="slider-desc">Nên để từ 0.2 đến 0.3 trong lĩnh vực nha khoa y tế để đảm bảo tính chuẩn xác và trung thực tuyệt đối theo bảng giá.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Box 3: API & Model -->
                        <div class="flora-card-box" style="margin-bottom: 20px;">
                            <h3><i class="dashicons dashicons-cloud" style="color: #0493f1;"></i> 3. Cấu Hình Gemini Model & API Key</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row"><label for="rag-api-key">Gemini API Key</label></th>
                                    <td>
                                        <input type="password" id="rag-api-key" name="api_key" class="regular-text" value="<?php echo esc_attr($settings['api_key']); ?>" style="width: 380px; font-family: monospace;" />
                                        <button type="button" class="button" id="btn-toggle-rag-key"><span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span></button>
                                        <p class="description">Khóa API kết nối Google Gemini Embedding & Chat Completion. Được mã hóa lưu trữ an toàn trong máy chủ.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="rag-model">Mô Hình AI Sử Dụng</label></th>
                                    <td>
                                        <select id="rag-model" name="model" style="min-width: 320px; font-weight: 600;">
                                            <option value="gemini-3.5-flash-lite" <?php selected($settings['model'], 'gemini-3.5-flash-lite'); ?>>gemini-3.5-flash-lite (Khuyên dùng - Siêu tốc & Báo giá chuẩn)</option>
                                            <option value="gemini-2.5-flash" <?php selected($settings['model'], 'gemini-2.5-flash'); ?>>gemini-2.5-flash</option>
                                            <option value="gemini-2.5-pro" <?php selected($settings['model'], 'gemini-2.5-pro'); ?>>gemini-2.5-pro (Mô hình sâu chuyên sâu)</option>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                                <button type="submit" class="button button-primary button-large" id="btn-save-rag-settings">
                                    <span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Cấu Hình RAG & Persona
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- ─────────────────────────────────────────────
                     TAB 3: PHÒNG THỬ NGHIỆM LIVE (PLAYGROUND)
                     ───────────────────────────────────────────── -->
                <div class="flora-tab-pane" id="tab-rag-playground">
                    <div class="flora-playground-grid">
                        <!-- Left Chat Column -->
                        <div class="playground-chat-col">
                            <div class="playground-chat-header">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="dashicons dashicons-format-chat" style="color: #0493f1; font-size: 20px;"></span>
                                    <strong>Khung Chat Thử Nghiệm Bác Sĩ AI</strong>
                                </div>
                                <span class="badge-online">Trực tuyến</span>
                            </div>

                            <div class="playground-chat-body" id="playground-chat-messages">
                                <div class="chat-bubble bot-bubble">
                                    <div class="bubble-avatar"><i class="dashicons dashicons-superhero-alt"></i></div>
                                    <div class="bubble-content">
                                        Chào bạn! Tôi là Bác Sĩ AI của Nha Khoa Flora. Bạn hãy nhập câu hỏi thử nghiệm về giá, quy trình hay các gói dịch vụ để kiểm tra xem hệ thống RAG hoạt động ra sao nhé!
                                    </div>
                                </div>
                            </div>

                            <!-- Quick test suggestion tags -->
                            <div class="playground-quick-tags">
                                <span style="font-size: 11px; font-weight: 700; color: #64748b; margin-right: 4px;">Câu hỏi mẫu:</span>
                                <button type="button" class="quick-tag" data-q="Báo giá cấy ghép 1 răng Implant Thụy Sĩ trọn gói là bao nhiêu và bảo hành mấy năm?">Mất 1 răng Implant Thụy Sĩ</button>
                                <button type="button" class="quick-tag" data-q="Gói Care Plus 999k cho cả gia đình có những quyền lợi gì và được cạo vôi mấy lần?">Gói Care Plus 999k</button>
                                <button type="button" class="quick-tag" data-q="Tẩy trắng răng công nghệ Plasma có đau buốt không và Gói White Up giá bao nhiêu?">Gói White Up 1.999k</button>
                                <button type="button" class="quick-tag" data-q="Tôi cười bị hở nướu nhiều thì điều trị bằng cách nào và giá bao nhiêu?">Chữa cười hở lợi</button>
                                <button type="button" class="quick-tag" data-q="Bọc 4 răng sứ Cercon HT giá bao nhiêu và có hỗ trợ trả góp không?">Bọc răng sứ Cercon HT</button>
                            </div>

                            <div class="playground-chat-footer">
                                <form id="playground-chat-form">
                                    <input type="text" id="playground-input" placeholder="Hỏi Bác Sĩ AI về bảng giá, gói khám, bảo hành..." autocomplete="off" />
                                    <button type="submit" class="button button-primary" id="playground-send-btn">
                                        <span class="dashicons dashicons-arrow-right-alt2" style="vertical-align: middle;"></span> Gửi
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Right Retrieved Chunks Column -->
                        <div class="playground-chunks-col">
                            <div class="playground-chunks-header">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons dashicons-search" style="color: #10b981; font-size: 18px;"></span>
                                    <strong>Mảnh Tri Thức RAG Được Trích Xuất (Retrieved Chunks)</strong>
                                </div>
                                <span class="badge-chunks-count" id="retrieved-count-badge">0 mẩu</span>
                            </div>

                            <div class="playground-chunks-body" id="playground-retrieved-chunks">
                                <div style="text-align: center; padding: 50px 20px; color: #94a3b8;">
                                    <span class="dashicons dashicons-portfolio" style="font-size: 36px; width: 36px; height: 36px; opacity: 0.5;"></span>
                                    <p style="margin-top: 10px; font-size: 13px;">Khi bạn gửi câu hỏi, các phân mảnh tài liệu liên quan nhất được RAG tìm thấy từ CSDL sẽ hiển thị tại đây kèm điểm số tương đồng (Similarity Score %).</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─────────────────────────────────────────────
             MODALS: THÊM / SỬA / UPLOAD TÀI LIỆU
             ───────────────────────────────────────────── -->

        <!-- Modal 1: Thêm/Sửa Tài Liệu Thủ Công -->
        <div id="modal-doc-edit" class="flora-modal-backdrop" style="display: none;">
            <div class="flora-modal-box">
                <div class="flora-modal-header">
                    <h3 id="modal-doc-title">Thêm Tài Liệu Tri Thức Mới</h3>
                    <button type="button" class="flora-modal-close">&times;</button>
                </div>
                <div class="flora-modal-body">
                    <form id="form-doc-edit">
                        <input type="hidden" id="edit-doc-id" value="0" />
                        <input type="hidden" id="edit-doc-source-type" value="manual" />

                        <div class="form-group">
                            <label for="edit-doc-name">Tiêu Đề Tài Liệu <span style="color: #ef4444;">*</span></label>
                            <input type="text" id="edit-doc-name" class="large-text" placeholder="Ví dụ: Bảng Giá Cấy Ghép Implant Thụy Sĩ 2026" required />
                        </div>

                        <div class="form-group-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="edit-doc-parent">Thư Mục Nhóm</label>
                                <select id="edit-doc-parent" style="width: 100%;">
                                    <option value="0">-- Thư mục gốc --</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="edit-doc-tags">Tags Phân Loại</label>
                                <input type="text" id="edit-doc-tags" placeholder="Ví dụ: implant, bang_gia, thuy_si" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit-doc-content">Nội Dung Văn Bản (Hỗ trợ Markdown) <span style="color: #ef4444;">*</span></label>
                            <textarea id="edit-doc-content" rows="12" class="large-text" placeholder="Nhập chi tiết nội dung, báo giá, chính sách bảo hành..." required style="font-family: monospace; font-size: 13px;"></textarea>
                        </div>
                    </form>
                </div>
                <div class="flora-modal-footer">
                    <button type="button" class="button button-secondary flora-modal-close">Hủy Bỏ</button>
                    <button type="button" class="button button-primary" id="btn-save-doc-submit">Lưu Tài Liệu</button>
                </div>
            </div>
        </div>

        <!-- Modal 2: Tải Lên File (.docx, .pdf, .txt) -->
        <div id="modal-file-upload" class="flora-modal-backdrop" style="display: none;">
            <div class="flora-modal-box">
                <div class="flora-modal-header">
                    <h3>Tải Lên File Tài Liệu Huấn Luyện (.docx, .pdf, .txt)</h3>
                    <button type="button" class="flora-modal-close">&times;</button>
                </div>
                <div class="flora-modal-body">
                    <form id="form-file-upload" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Chọn File Từ Máy Tính <span style="color: #ef4444;">*</span></label>
                            <input type="file" id="upload-file-input" accept=".docx,.pdf,.txt,.md,.json" style="padding: 10px; border: 2px dashed #cbd5e1; border-radius: 8px; width: 100%; box-sizing: border-box; background: #f8fafc;" required />
                            <p class="description" style="margin-top: 6px;">Hệ thống tự động đọc và trích xuất nội dung văn bản từ file Word (.docx), file PDF và file Text (.txt).</p>
                        </div>

                        <div class="form-group-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="upload-file-parent">Thư Mục Nhóm</label>
                                <select id="upload-file-parent" style="width: 100%;">
                                    <option value="0">-- Thư mục gốc --</option>
                                </select>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="upload-file-tags">Tags Phân Loại</label>
                                <input type="text" id="upload-file-tags" placeholder="Ví dụ: docx_bang_gia, bao_hanh" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="flora-modal-footer">
                    <button type="button" class="button button-secondary flora-modal-close">Hủy Bỏ</button>
                    <button type="button" class="button button-primary" id="btn-upload-file-submit">Tải Lên & Bóc Tách Text</button>
                </div>
            </div>

        <!-- Modal 3: Tạo Thư Mục Nhóm -->
        <div id="modal-create-folder" class="flora-modal-backdrop" style="display: none;">
            <div class="flora-modal-box" style="max-width: 480px;">
                <div class="flora-modal-header">
                    <h3>Tạo Thư Mục Tri Thức Mới</h3>
                    <button type="button" class="flora-modal-close">&times;</button>
                </div>
                <div class="flora-modal-body">
                    <div class="form-group">
                        <label for="folder-name-input">Tên Thư Mục <span style="color: #ef4444;">*</span></label>
                        <input type="text" id="folder-name-input" class="large-text" placeholder="Ví dụ: Quy Trình & Phác Đồ Điều Trị" required />
                    </div>
                </div>
                <div class="flora-modal-footer">
                    <button type="button" class="button button-secondary flora-modal-close">Hủy Bỏ</button>
                    <button type="button" class="button button-primary" id="btn-create-folder-submit">Tạo Thư Mục</button>
                </div>
            </div>
        </div>

        <!-- Modal 5: Xem Trước Phân Mảnh (Preview Chunks) -->
        <div id="modal-preview-chunks" class="flora-modal-backdrop" style="display: none;">
            <div class="flora-modal-box" style="max-width: 780px;">
                <div class="flora-modal-header">
                    <h3 id="preview-chunks-title">Xem Các Phân Mảnh Vector (Chunks)</h3>
                    <button type="button" class="flora-modal-close">&times;</button>
                </div>
                <div class="flora-modal-body" id="preview-chunks-container" style="max-height: 480px; overflow-y: auto;">
                    <!-- Render dynamic chunks -->
                </div>
                <div class="flora-modal-footer">
                    <button type="button" class="button button-primary flora-modal-close">Đóng Lại</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────
         ADMIN STYLES (LUXURY SAAS MATCHING FLORA CUSTOMIZER)
         ───────────────────────────────────────────── -->
    <style>
        /* Ẩn triệt để toàn bộ thông báo của các plugin khác làm bẩn giao diện Huấn Luyện AI */
        .toplevel_page_flora-ai-training #wpbody-content > .notice:not(#flora-rag-notice),
        .toplevel_page_flora-ai-training #wpbody-content > .updated:not(#flora-rag-notice),
        .toplevel_page_flora-ai-training #wpbody-content > .update-nag,
        .toplevel_page_flora-ai-training #wpbody-content > .error:not(#flora-rag-notice),
        .toplevel_page_flora-ai-training .is-dismissible:not(#flora-rag-notice),
        .toplevel_page_flora-ai-training div[class*="ilj"],
        .toplevel_page_flora-ai-training div[class*="notice"]:not(#flora-rag-notice),
        div.ilj-notice,
        .flora-rag-admin-wrap ~ div.notice,
        .flora-rag-admin-wrap .notice:not(#flora-rag-notice) {
            display: none !important;
        }

        .flora-rag-admin-wrap { max-width: 1250px; margin-top: 20px; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .flora-rag-header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 22px 28px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 22px; position: relative; }
        .flora-rag-header h1 { margin: 0 0 6px 0; font-size: 21px; font-weight: 800; color: #0033a3; letter-spacing: -0.3px; }
        .flora-rag-header p { margin: 0; color: #64748b; font-size: 13.5px; }

        .flora-nav-tabs { display: flex; gap: 8px; margin: 0; padding: 0; list-style: none; border-bottom: 2px solid #e2e8f0; }
        .flora-nav-tabs li { padding: 13px 22px; background: #f1f5f9; border-radius: 10px 10px 0 0; cursor: pointer; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 8px; border: 1px solid #e2e8f0; border-bottom: none; margin-bottom: -2px; transition: all 0.2s ease; font-size: 13.5px; }
        .flora-nav-tabs li:hover { background: #e2e8f0; color: #0033a3; }
        .flora-nav-tabs li.active { background: #ffffff; color: #0493f1; border-top: 3.5px solid #0493f1; border-bottom: 2px solid #ffffff; font-weight: 800; }
        .flora-nav-tabs li.tab-highlight { color: #8b5cf6; }
        .flora-nav-tabs li.tab-highlight.active { border-top-color: #8b5cf6; color: #8b5cf6; }

        .flora-tabs-content { background: #ffffff; padding: 28px; border-radius: 0 0 14px 14px; border: 1px solid #e2e8f0; border-top: none; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .flora-tab-pane { display: none; }
        .flora-tab-pane.active { display: block; }

        /* Stats Cards */
        .flora-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 22px; }
        .flora-stat-card { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; display: flex; align-items: center; gap: 14px; transition: transform 0.2s, box-shadow 0.2s; }
        .flora-stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,0.04); }
        .stat-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .stat-num { font-size: 22px; font-weight: 800; color: #0f172a; line-height: 1.1; }
        .stat-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        /* Toolbar */
        .flora-doc-toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; gap: 12px; flex-wrap: wrap; }
        .toolbar-left { display: flex; gap: 8px; flex-wrap: wrap; }

        /* Tree List Container */
        .flora-tree-container { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
        .folder-group { border-bottom: 1px solid #f1f5f9; }
        .folder-header { background: #f8fafc; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; font-weight: 700; color: #0033a3; cursor: pointer; user-select: none; }
        .folder-header:hover { background: #f1f5f9; }
        .folder-title { display: flex; align-items: center; gap: 8px; font-size: 14px; }
        .folder-items-list { padding: 4px 0; }
        .doc-item-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 18px 10px 42px; border-bottom: 1px solid #f8fafc; transition: background 0.15s; }
        .doc-item-row:hover { background: #f8fafc; }
        .doc-item-left { display: flex; align-items: center; gap: 12px; flex: 1; }
        .doc-name { font-weight: 600; font-size: 13.5px; color: #1e293b; }
        .doc-tags { font-size: 11px; color: #64748b; background: #e2e8f0; padding: 2px 7px; border-radius: 4px; }
        .doc-item-meta { display: flex; align-items: center; gap: 14px; }
        .badge-status { font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; text-transform: uppercase; }
        .badge-trained { background: #dcfce7; color: #15803d; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-error { background: #fee2e2; color: #b91c1c; }
        .badge-chunks { font-size: 11.5px; color: #475569; font-weight: 600; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; border: 1px solid #e2e8f0; }

        /* Card Box & Sliders Grid */
        .flora-card-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; }
        .flora-card-box h3 { margin-top: 0; font-size: 16px; color: #0033a3; font-weight: 700; margin-bottom: 16px; }
        .flora-sliders-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .flora-slider-group { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; }
        .slider-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .slider-header label { font-weight: 700; font-size: 13px; color: #1e293b; }
        .slider-val-badge { background: #e0f2fe; color: #0284c7; padding: 2px 8px; border-radius: 6px; font-weight: 800; font-size: 12px; }
        .rag-slider { width: 100%; accent-color: #0493f1; cursor: pointer; }
        .slider-ticks { display: flex; justify-content: space-between; font-size: 11px; color: #94a3b8; font-weight: 600; margin-top: 4px; }
        .slider-desc { font-size: 11.5px; color: #64748b; margin: 8px 0 0 0; line-height: 1.45; }

        /* Playground Layout */
        .flora-playground-grid { display: grid; grid-template-columns: 1.15fr 1fr; gap: 20px; }
        .playground-chat-col, .playground-chunks-col { background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; display: flex; flex-direction: column; height: 600px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .playground-chat-header, .playground-chunks-header { background: #f8fafc; padding: 12px 18px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .badge-online { background: #dcfce7; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 4px; }
        .badge-chunks-count { background: #f1f5f9; color: #0493f1; font-weight: 800; font-size: 12px; padding: 2px 8px; border-radius: 6px; border: 1px solid #cbd5e1; }
        .playground-chat-body { flex: 1; padding: 16px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 12px; }
        .chat-bubble { display: flex; gap: 10px; max-width: 88%; }
        .bot-bubble { align-self: flex-start; }
        .user-bubble { align-self: flex-end; flex-direction: row-reverse; }
        .bubble-avatar { width: 32px; height: 32px; border-radius: 50%; background: #0493f1; color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .bubble-content { background: #ffffff; border: 1px solid #e2e8f0; padding: 10px 14px; border-radius: 12px; font-size: 13px; line-height: 1.55; color: #1e293b; white-space: pre-wrap; }
        .user-bubble .bubble-content { background: #0033a3; color: #ffffff; border-color: #0033a3; }
        .playground-quick-tags { padding: 8px 12px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; }
        .quick-tag { background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 12px; padding: 4px 9px; font-size: 11.5px; color: #0033a3; cursor: pointer; flex-shrink: 0; transition: all 0.2s; }
        .quick-tag:hover { background: #0493f1; color: #ffffff; border-color: #0493f1; }
        .playground-chat-footer { padding: 10px 12px; background: #ffffff; border-top: 1px solid #e2e8f0; }
        #playground-chat-form { display: flex; gap: 8px; }
        #playground-input { flex: 1; border: 1px solid #cbd5e1; border-radius: 8px; padding: 9px 14px; font-size: 13.5px; }
        .playground-chunks-body { flex: 1; padding: 14px; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 10px; }
        .chunk-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; font-size: 12.5px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .chunk-card-header { display: flex; justify-content: space-between; font-weight: 700; color: #0033a3; margin-bottom: 6px; font-size: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 4px; }
        .chunk-score-pill { background: #dcfce7; color: #166534; padding: 1px 6px; border-radius: 4px; font-size: 11px; }

        /* Modals */
        .flora-modal-backdrop { position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(3px); z-index: 99999; display: flex; align-items: center; justify-content: center; }
        .flora-modal-box { background: #ffffff; border-radius: 14px; width: 90%; max-width: 680px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); overflow: hidden; display: flex; flex-direction: column; animation: modalFadeIn 0.25s ease-out; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.96) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .flora-modal-header { padding: 16px 22px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
        .flora-modal-header h3 { margin: 0; font-size: 16px; color: #0033a3; font-weight: 700; }
        .flora-modal-close { background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8; line-height: 1; }
        .flora-modal-close:hover { color: #0f172a; }
        .flora-modal-body { padding: 20px 22px; overflow-y: auto; max-height: calc(85vh - 120px); }
        .flora-modal-footer { padding: 14px 22px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-weight: 700; font-size: 13px; color: #1e293b; margin-bottom: 6px; }
        .form-group-row { display: flex; gap: 12px; }

        .flora-spin { animation: spin 1s infinite linear; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

    <!-- ─────────────────────────────────────────────
         ADMIN JAVASCRIPT LOGIC
         ───────────────────────────────────────────── -->
    <script>
    jQuery(document).ready(function($) {
        // Loại bỏ triệt để các thông báo rác / hết hạn bản quyền của các plugin khác
        $('.notice:not(#flora-rag-notice), .update-nag, .updated:not(#flora-rag-notice), .ilj-notice').remove();

        var nonce = '<?php echo $nonce; ?>';
        var allDocs = [];

        // 1. Tab Navigation
        $('.flora-nav-tabs li').on('click', function() {
            var tab = $(this).data('tab');
            $('.flora-nav-tabs li').removeClass('active');
            $(this).addClass('active');
            $('.flora-tab-pane').removeClass('active');
            $('#' + tab).addClass('active');
        });

        // 2. Sliders Live Value Update
        $('#rag-similarity-threshold').on('input', function() { $('#val-similarity-threshold').text($(this).val()); });
        $('#rag-top-k').on('input', function() { $('#val-top-k').text($(this).val() + ' mảnh'); });
        $('#rag-chunk-size').on('input', function() { $('#val-chunk-size').text($(this).val() + ' ký tự'); });
        $('#rag-chunk-overlap').on('input', function() { $('#val-chunk-overlap').text($(this).val() + ' ký tự'); });
        $('#rag-temperature').on('input', function() { $('#val-temperature').text($(this).val()); });

        // Toggle Key Visibility
        $('#btn-toggle-rag-key').on('click', function() {
            var $inp = $('#rag-api-key');
            $inp.attr('type', $inp.attr('type') === 'password' ? 'text' : 'password');
        });

        // Helper Notification
        function showNotice(msg, type) {
            var $box = $('#flora-rag-notice');
            $box.removeClass('notice-success notice-error notice-info')
                .addClass('notice-' + (type || 'info'))
                .show().find('p').html(msg);
            $('html, body').animate({ scrollTop: 0 }, 200);
        }

        // 3. Load Documents List
        function loadDocsList() {
            $.post(ajaxurl, { action: 'flora_rag_list_docs', nonce: nonce }, function(res) {
                if (res.success) {
                    allDocs = res.data.docs || [];
                    renderDocsTree(allDocs);
                    updateStats(allDocs);
                    updateFolderSelects(allDocs);
                } else {
                    $('#flora-docs-tree').html('<div style="padding: 30px; text-align: center; color: #ef4444;">' + (res.data ? res.data.message : 'Lỗi tải danh sách') + '</div>');
                }
            });
        }
        loadDocsList();

        // Update Stats Counters
        function updateStats(docs) {
            var totalDocs = 0;
            var totalChunks = 0;
            var totalFolders = 0;
            var activeDocs = 0;

            docs.forEach(function(d) {
                if (d.source_type === 'folder') {
                    totalFolders++;
                } else {
                    totalDocs++;
                    totalChunks += parseInt(d.chunk_count || 0, 10);
                    if (parseInt(d.is_active, 10) === 1) activeDocs++;
                }
            });

            $('#stat-total-docs').text(totalDocs);
            $('#stat-trained-chunks').text(totalChunks);
            $('#stat-total-folders').text(totalFolders);
            $('#stat-active-docs').text(activeDocs);
        }

        // Populate Folder Dropdowns
        function updateFolderSelects(docs) {
            var opts = '<option value="0">-- Thư mục gốc --</option>';
            docs.forEach(function(d) {
                if (d.source_type === 'folder') {
                    opts += '<option value="' + d.id + '">' + d.name + '</option>';
                }
            });
            $('#edit-doc-parent, #upload-file-parent').html(opts);
        }

        // Render Tree Hierarchy
        function renderDocsTree(docs) {
            var $tree = $('#flora-docs-tree');
            if (!docs.length) {
                $tree.html('<div style="text-align: center; padding: 50px 20px; color: #94a3b8;">' +
                    '<span class="dashicons dashicons-portfolio" style="font-size: 36px; width: 36px; height: 36px; opacity: 0.5;"></span>' +
                    '<p style="margin-top: 10px;">Chưa có tài liệu tri thức nào trong CSDL.</p>' +
                    '<button type="button" class="button button-primary" id="btn-seed-first">Nạp Sẵn Tri Thức Mẫu Từ Hệ Thống</button>' +
                '</div>');
                $('#btn-seed-first').on('click', function() { $('#btn-seed-knowledge').click(); });
                return;
            }

            var folders = docs.filter(function(d) { return d.source_type === 'folder'; });
            var rootDocs = docs.filter(function(d) { return d.source_type !== 'folder' && (!d.parent_id || d.parent_id == 0); });
            var html = '';

            // Render Folders
            folders.forEach(function(f) {
                var children = docs.filter(function(d) { return d.parent_id == f.id && d.source_type !== 'folder'; });
                html += '<div class="folder-group" data-folder-id="' + f.id + '">';
                html += '  <div class="folder-header">';
                html += '    <div class="folder-title"><span class="dashicons dashicons-category" style="color: #0493f1;"></span> <span>' + f.name + '</span> <span style="font-size: 11.5px; color: #64748b; font-weight: 500;">(' + children.length + ' tài liệu)</span></div>';
                html += '    <div style="display: flex; gap: 8px;">';
                html += '      <button type="button" class="button button-small btn-train-folder" data-id="' + f.id + '" title="Huấn luyện tất cả tài liệu trong thư mục này"><span class="dashicons dashicons-update" style="vertical-align: middle;"></span> Train</button>';
                html += '      <button type="button" class="button button-small btn-delete-doc" data-id="' + f.id + '" title="Xóa thư mục"><span class="dashicons dashicons-trash" style="vertical-align: middle; color: #ef4444;"></span></button>';
                html += '    </div>';
                html += '  </div>';

                html += '  <div class="folder-items-list">';
                if (children.length) {
                    children.forEach(function(c) { html += renderDocRow(c); });
                } else {
                    html += '    <div style="padding: 10px 42px; font-size: 12px; color: #94a3b8; font-style: italic;">Chưa có tài liệu nào trong thư mục này.</div>';
                }
                html += '  </div>';
                html += '</div>';
            });

            // Render Root Docs
            if (rootDocs.length) {
                html += '<div class="folder-group">';
                html += '  <div class="folder-header"><div class="folder-title"><span class="dashicons dashicons-media-document"></span> <span>Tài Liệu Độc Lập</span></div></div>';
                html += '  <div class="folder-items-list">';
                rootDocs.forEach(function(c) { html += renderDocRow(c); });
                html += '  </div>';
                html += '</div>';
            }

            $tree.html(html);
        }

        function renderDocRow(d) {
            var statusBadge = '';
            if (d.status === 'trained') {
                statusBadge = '<span class="badge-status badge-trained"><i class="dashicons dashicons-yes-alt" style="font-size: 13px; vertical-align: text-top;"></i> Trained</span>';
            } else if (d.status === 'error') {
                statusBadge = '<span class="badge-status badge-error"><i class="dashicons dashicons-warning" style="font-size: 13px; vertical-align: text-top;"></i> Error</span>';
            } else {
                statusBadge = '<span class="badge-status badge-pending"><i class="dashicons dashicons-clock" style="font-size: 13px; vertical-align: text-top;"></i> Pending</span>';
            }

            var icon = 'dashicons-media-document';
            if (d.source_type === 'file') icon = 'dashicons-upload';
            else if (d.source_type === 'web') icon = 'dashicons-admin-site-alt3';

            var isChecked = parseInt(d.is_active, 10) === 1 ? 'checked' : '';

            var out = '<div class="doc-item-row" data-doc-id="' + d.id + '">';
            out += '  <div class="doc-item-left">';
            out += '    <span class="dashicons ' + icon + '" style="color: #64748b;"></span>';
            out += '    <span class="doc-name">' + d.name + '</span>';
            if (d.tags) out += '    <span class="doc-tags">' + d.tags + '</span>';
            out += '  </div>';

            out += '  <div class="doc-item-meta">';
            out += '    <span class="badge-chunks">' + (d.chunk_count || 0) + ' chunks</span>';
            out += '    ' + statusBadge;
            out += '    <label title="Bật/Tắt sử dụng mẩu tri thức này trong RAG" style="display: flex; align-items: center; margin: 0; cursor: pointer;">';
            out += '      <input type="checkbox" class="doc-active-toggle" data-id="' + d.id + '" ' + isChecked + ' /> <span style="font-size: 11px; margin-left: 4px; font-weight: 600;">Active</span>';
            out += '    </label>';
            out += '    <button type="button" class="button button-small btn-preview-chunks" data-id="' + d.id + '" data-name="' + d.name + '" title="Xem các phân mảnh"><span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span></button>';
            out += '    <button type="button" class="button button-small btn-train-single" data-id="' + d.id + '" title="Huấn luyện tài liệu này"><span class="dashicons dashicons-update" style="vertical-align: middle;"></span></button>';
            out += '    <button type="button" class="button button-small btn-edit-doc" data-id="' + d.id + '" title="Chỉnh sửa"><span class="dashicons dashicons-edit" style="vertical-align: middle;"></span></button>';
            out += '    <button type="button" class="button button-small btn-delete-doc" data-id="' + d.id + '" title="Xóa"><span class="dashicons dashicons-trash" style="vertical-align: middle; color: #ef4444;"></span></button>';
            out += '  </div>';
            out += '</div>';
            return out;
        }

        // 4. Modal Triggers
        $('#btn-open-add-modal').on('click', function() {
            $('#modal-doc-title').text('Thêm Tài Liệu Tri Thức Mới');
            $('#edit-doc-id').val('0');
            $('#edit-doc-source-type').val('manual');
            $('#edit-doc-name').val('');
            $('#edit-doc-content').val('');
            $('#edit-doc-tags').val('');
            $('#modal-doc-edit').fadeIn(150);
        });

        $('#btn-open-upload-modal').on('click', function() {
            $('#form-file-upload')[0].reset();
            $('#modal-file-upload').fadeIn(150);
        });

        $('#btn-open-folder-modal').on('click', function() {
            $('#folder-name-input').val('');
            $('#modal-create-folder').fadeIn(150);
        });

        $('.flora-modal-close').on('click', function() {
            $('.flora-modal-backdrop').fadeOut(150);
        });

        // 5. Submit Document (Add/Edit)
        $('#btn-save-doc-submit').on('click', function() {
            var id = $('#edit-doc-id').val();
            var name = $('#edit-doc-name').val().trim();
            var content = $('#edit-doc-content').val().trim();
            var parentId = $('#edit-doc-parent').val();
            var tags = $('#edit-doc-tags').val().trim();
            var sourceType = $('#edit-doc-source-type').val();

            if (!name || !content) {
                alert('Vui lòng nhập đầy đủ tiêu đề và nội dung tài liệu.');
                return;
            }

            var $btn = $(this).prop('disabled', true).text('Đang lưu...');
            $.post(ajaxurl, {
                action: 'flora_rag_save_doc',
                nonce: nonce,
                doc_id: id,
                name: name,
                content: content,
                parent_id: parentId,
                tags: tags,
                source_type: sourceType
            }, function(res) {
                $btn.prop('disabled', false).text('Lưu Tài Liệu');
                if (res.success) {
                    $('#modal-doc-edit').fadeOut(150);
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Có lỗi xảy ra');
                }
            });
        });

        // 6. Submit File Upload (.docx, .pdf, .txt)
        $('#btn-upload-file-submit').on('click', function() {
            var fileInput = document.getElementById('upload-file-input');
            if (!fileInput.files.length) {
                alert('Vui lòng chọn 1 file từ máy tính.');
                return;
            }

            var formData = new FormData();
            formData.append('action', 'flora_rag_upload_file');
            formData.append('nonce', nonce);
            formData.append('file', fileInput.files[0]);
            formData.append('parent_id', $('#upload-file-parent').val());
            formData.append('tags', $('#upload-file-tags').val().trim());

            var $btn = $(this).prop('disabled', true).text('Đang bóc tách & tải lên...');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $btn.prop('disabled', false).text('Tải Lên & Bóc Tách Text');
                    if (res.success) {
                        $('#modal-file-upload').fadeOut(150);
                        showNotice(res.data.message, 'success');
                        loadDocsList();
                    } else {
                        alert(res.data ? res.data.message : 'Lỗi tải file');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Tải Lên & Bóc Tách Text');
                    alert('Lỗi kết nối máy chủ khi upload file.');
                }
            });
        });

        // 7. Create Folder
        $('#btn-create-folder-submit').on('click', function() {
            var name = $('#folder-name-input').val().trim();
            if (!name) {
                alert('Vui lòng nhập tên thư mục.');
                return;
            }

            var $btn = $(this).prop('disabled', true).text('Đang tạo...');
            $.post(ajaxurl, {
                action: 'flora_rag_create_folder',
                nonce: nonce,
                name: name
            }, function(res) {
                $btn.prop('disabled', false).text('Tạo Thư Mục');
                if (res.success) {
                    $('#modal-create-folder').fadeOut(150);
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Lỗi tạo thư mục');
                }
            });
        });

        // 9. Edit Doc Click
        $(document).on('click', '.btn-edit-doc', function() {
            var id = $(this).data('id');
            var doc = allDocs.find(function(d) { return d.id == id; });
            if (!doc) return;

            $('#modal-doc-title').text('Chỉnh Sửa Tài Liệu: ' + doc.name);
            $('#edit-doc-id').val(doc.id);
            $('#edit-doc-source-type').val(doc.source_type);
            $('#edit-doc-name').val(doc.name);
            $('#edit-doc-content').val(doc.content);
            $('#edit-doc-tags').val(doc.tags);
            $('#edit-doc-parent').val(doc.parent_id);
            $('#modal-doc-edit').fadeIn(150);
        });

        // 10. Delete Doc Click
        $(document).on('click', '.btn-delete-doc', function() {
            if (!confirm('Bạn có chắc chắn muốn xóa tài liệu này và toàn bộ các phân mảnh vector liên quan không?')) {
                return;
            }
            var id = $(this).data('id');
            $.post(ajaxurl, { action: 'flora_rag_delete_doc', nonce: nonce, doc_id: id }, function(res) {
                if (res.success) {
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Có lỗi xảy ra');
                }
            });
        });

        // 11. Toggle Active
        $(document).on('change', '.doc-active-toggle', function() {
            var id = $(this).data('id');
            var isActive = $(this).is(':checked') ? 1 : 0;
            $.post(ajaxurl, { action: 'flora_rag_toggle_doc', nonce: nonce, doc_id: id, is_active: isActive }, function(res) {
                if (!res.success) alert('Không thể cập nhật trạng thái');
            });
        });

        // 12. Preview Chunks Click
        $(document).on('click', '.btn-preview-chunks', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#preview-chunks-title').text('Các Phân Mảnh Vector: ' + name);
            var $cont = $('#preview-chunks-container').html('<div style="text-align: center; padding: 30px;"><span class="dashicons dashicons-update flora-spin"></span> Đang tải chunks...</div>');
            $('#modal-preview-chunks').fadeIn(150);

            $.post(ajaxurl, { action: 'flora_rag_preview_chunks', nonce: nonce, doc_id: id }, function(res) {
                if (res.success && res.data.chunks && res.data.chunks.length) {
                    var html = '';
                    res.data.chunks.forEach(function(c, i) {
                        html += '<div class="chunk-card">';
                        html += '  <div class="chunk-card-header">';
                        html += '    <span>Mảnh #' + (i + 1) + ' (Độ dài: ' + c.content.length + ' ký tự)</span>';
                        html += '    <span class="chunk-score-pill">Vector Norm: ' + Math.round(c.vector_norm * 100) / 100 + '</span>';
                        html += '  </div>';
                        html += '  <div style="line-height: 1.5; color: #334155; white-space: pre-wrap;">' + c.content + '</div>';
                        html += '</div>';
                    });
                    $cont.html(html);
                } else {
                    $cont.html('<div style="text-align: center; padding: 40px; color: #94a3b8;">Tài liệu này chưa có phân mảnh vector nào. Hãy bấm nút Huấn Luyện để tạo mẩu cắt.</div>');
                }
            });
        });

        // 13. Train Single Doc
        $(document).on('click', '.btn-train-single', function() {
            var id = $(this).data('id');
            var $btn = $(this).prop('disabled', true);
            $btn.find('.dashicons').addClass('flora-spin');

            $.post(ajaxurl, { action: 'flora_rag_train_docs', nonce: nonce, doc_ids: [id] }, function(res) {
                $btn.prop('disabled', false).find('.dashicons').removeClass('flora-spin');
                if (res.success) {
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Có lỗi khi huấn luyện');
                }
            });
        });

        // 14. Train Folder
        $(document).on('click', '.btn-train-folder', function() {
            var folderId = $(this).data('id');
            var children = allDocs.filter(function(d) { return d.parent_id == folderId && d.source_type !== 'folder'; });
            if (!children.length) {
                alert('Thư mục này không có tài liệu nào để huấn luyện.');
                return;
            }
            var ids = children.map(function(c) { return c.id; });
            var $btn = $(this).prop('disabled', true).text('Đang train...');

            $.post(ajaxurl, { action: 'flora_rag_train_docs', nonce: nonce, doc_ids: ids }, function(res) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-update"></span> Train');
                if (res.success) {
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Lỗi huấn luyện thư mục');
                }
            });
        });

        // 15. Train All Docs
        $('#btn-train-all-docs').on('click', function() {
            var trainable = allDocs.filter(function(d) { return d.source_type !== 'folder'; });
            if (!trainable.length) {
                alert('Không có tài liệu nào để huấn luyện. Hãy bấm Nạp Tri Thức Mẫu trước.');
                return;
            }

            var ids = trainable.map(function(d) { return d.id; });
            var $btn = $(this).prop('disabled', true);
            $btn.find('.dashicons').addClass('flora-spin');
            $('#flora-training-progress-wrap').slideDown(200);
            $('#training-progress-bar').css('width', '30%');
            $('#training-progress-pct').text('30%');

            $.post(ajaxurl, { action: 'flora_rag_train_docs', nonce: nonce, doc_ids: ids }, function(res) {
                $btn.prop('disabled', false).find('.dashicons').removeClass('flora-spin');
                $('#training-progress-bar').css('width', '100%');
                $('#training-progress-pct').text('100%');
                setTimeout(function() { $('#flora-training-progress-wrap').slideUp(200); }, 2000);

                if (res.success) {
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Có lỗi khi huấn luyện');
                }
            });
        });

        // 16. Seed Default Knowledge Base
        $('#btn-seed-knowledge').on('click', function() {
            if (!confirm('Hệ thống sẽ quét và nạp toàn bộ các file tri thức chuẩn nha khoa (bảng giá Implant, niềng răng, răng sứ, cười hở lợi, gói Care Plus 999k, gói White Up 1.999k, bảo hành trọn đời, kịch bản chốt hẹn) vào CSDL. Tiếp tục?')) {
                return;
            }
            var $btn = $(this).prop('disabled', true).text('Đang đồng bộ...');
            $.post(ajaxurl, { action: 'flora_rag_seed_knowledge', nonce: nonce }, function(res) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> Nạp Tri Thức Mẫu');
                if (res.success) {
                    showNotice(res.data.message, 'success');
                    loadDocsList();
                } else {
                    alert(res.data ? res.data.message : 'Lỗi đồng bộ');
                }
            });
        });

        // 17. Save RAG Settings
        $('#flora-rag-settings-form').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#btn-save-rag-settings').prop('disabled', true).text('Đang lưu cấu hình...');
            var formData = $(this).serialize() + '&action=flora_rag_save_settings&nonce=' + nonce;

            $.post(ajaxurl, formData, function(res) {
                $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved"></span> Lưu Cấu Hình RAG & Persona');
                if (res.success) {
                    showNotice(res.data.message, 'success');
                } else {
                    alert(res.data ? res.data.message : 'Lỗi lưu cấu hình');
                }
            });
        });

        // 18. Live Playground Chat & Retrieved Chunks
        $('#playground-chat-form').on('submit', function(e) {
            e.preventDefault();
            var q = $('#playground-input').val().trim();
            if (!q) return;

            appendPlaygroundMsg('user', q);
            $('#playground-input').val('');

            var $sendBtn = $('#playground-send-btn').prop('disabled', true);
            var $chunksCont = $('#playground-retrieved-chunks').html('<div style="text-align: center; padding: 40px;"><span class="dashicons dashicons-search flora-spin"></span> Đang truy hồi Vector RAG...</div>');
            $('#retrieved-count-badge').text('Đang tìm...');

            $.post(ajaxurl, { action: 'flora_rag_test_query', nonce: nonce, query: q }, function(res) {
                $sendBtn.prop('disabled', false);
                if (res.success && res.data) {
                    appendPlaygroundMsg('bot', res.data.reply);

                    // Render Retrieved Chunks
                    var chunks = res.data.retrieved_chunks || [];
                    $('#retrieved-count-badge').text(chunks.length + ' mẩu');

                    if (chunks.length) {
                        var html = '';
                        chunks.forEach(function(c, i) {
                            html += '<div class="chunk-card">';
                            html += '  <div class="chunk-card-header">';
                            html += '    <span>Mẩu #' + (i + 1) + ' — ' + c.doc_name + '</span>';
                            html += '    <span class="chunk-score-pill">Độ tương đồng: ' + Math.round(c.score * 1000) / 10 + '%</span>';
                            html += '  </div>';
                            html += '  <div style="color: #334155; line-height: 1.5; white-space: pre-wrap;">' + c.content + '</div>';
                            html += '</div>';
                        });
                        $chunksCont.html(html);
                    } else {
                        $chunksCont.html('<div style="text-align: center; padding: 40px; color: #94a3b8;">Không có mẩu tri thức nào vượt ngưỡng tương đồng RAG trong CSDL cho câu hỏi này.</div>');
                    }
                } else {
                    appendPlaygroundMsg('bot', 'Lỗi: ' + (res.data ? res.data.message : 'Không nhận được câu trả lời'));
                    $chunksCont.html('<div style="text-align: center; padding: 30px; color: #ef4444;">Lỗi truy vấn RAG.</div>');
                }
            });
        });

        // Quick Tag Click
        $('.quick-tag').on('click', function() {
            var q = $(this).data('q');
            $('#playground-input').val(q);
            $('#playground-chat-form').submit();
        });

        function appendPlaygroundMsg(role, text) {
            var $box = $('#playground-chat-messages');
            var avatar = role === 'user' ? '<div class="bubble-avatar" style="background: #0033a3;"><i class="dashicons dashicons-admin-users"></i></div>' : '<div class="bubble-avatar"><i class="dashicons dashicons-superhero-alt"></i></div>';
            var div = '<div class="chat-bubble ' + (role === 'user' ? 'user-bubble' : 'bot-bubble') + '">' +
                avatar +
                '<div class="bubble-content">' + $('<div>').text(text).html() + '</div>' +
            '</div>';
            $box.append(div);
            $box.scrollTop($box[0].scrollHeight);
        }

        // Search Filter in Documents
        $('#flora-doc-search').on('input', function() {
            var term = $(this).val().toLowerCase().trim();
            if (!term) {
                $('.doc-item-row, .folder-group').show();
                return;
            }
            $('.doc-item-row').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.indexOf(term) !== -1) {
                    $(this).show();
                    $(this).closest('.folder-group').show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
    </script>
    <?php
}
