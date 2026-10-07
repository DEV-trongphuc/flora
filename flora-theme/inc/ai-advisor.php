<?php
/**
 * Flora Dental Clinic - AI Doctor & Price Advisor
 * Tích hợp Google Gemini 3.5 Flash Lite để chẩn đoán y khoa sơ bộ & báo giá chính xác theo Database
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Lấy API Key từ Database (Mặc định được cấu hình an toàn)
 */
function flora_get_ai_api_key() {
    $key = get_option('flora_gemini_api_key', '');
    if (empty($key)) {
        $key = getenv('FLORA_GEMINI_API_KEY') ?: '';
    }
    return $key;
}

/**
 * 2. Lấy Model AI được chọn
 */
function flora_get_ai_model() {
    $model = get_option('flora_gemini_model');
    if (empty($model)) {
        return 'gemini-3.5-flash-lite';
    }
    return $model;
}

/**
 * 3. Lấy Toàn Bộ Cấu Hình & Tri Thức Bác Sĩ AI
 */
function flora_get_ai_config() {
    $default_suggestions = array(
        array('label' => 'Gói Care Plus 999k', 'query' => 'Gói Care Plus 999k cho cả gia đình có những quyền lợi gì và dùng được mấy người?'),
        array('label' => 'Gói Tẩy Trắng 1.999k', 'query' => 'Gói Flora White Up tẩy trắng răng bằng công nghệ Plasma giá bao nhiêu và có bị ê buốt không?'),
        array('label' => 'Mất 1 răng hàm', 'query' => 'Tôi bị mất 1 răng hàm, chi phí trồng Implant trọn gói là bao nhiêu?'),
        array('label' => 'Báo giá niềng răng', 'query' => 'Chi phí niềng răng trong suốt Invisalign và mắc cài có những gói nào?'),
        array('label' => 'Chữa cười hở lợi', 'query' => 'Tôi cười bị hở nướu nhiều thì điều trị bằng cách nào và giá bao nhiêu?'),
        array('label' => 'Báo giá dán sứ Veneer', 'query' => 'Dán sứ Veneer và bọc răng sứ thẩm mỹ giá bao nhiêu 1 răng?')
    );

    return array(
        'api_key'             => flora_get_ai_api_key(),
        'model'               => flora_get_ai_model(),
        'doctor_name'         => get_option('flora_ai_doctor_name', 'Bác Sĩ Tư Vấn Flora'),
        'clinic_address'      => get_option('flora_ai_clinic_address', '326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh'),
        'clinic_hotline'      => get_option('flora_ai_clinic_hotline', '028 7305 8999'),
        'clinic_perks'        => get_option('flora_ai_clinic_perks', ''),
        'welcome_msg'         => get_option('flora_ai_welcome_msg', "Xin chào bạn! Tôi là **Bác Sĩ AI của Nha Khoa Flora** 🩺.\n\nBạn đang quan tâm đến dịch vụ nào (trồng răng Implant, niềng răng, dán sứ...) hay các gói ưu đãi hot như **Gói Care Plus 999k (chăm sóc cả gia đình)**, **Gói Flora White Up 1.999k (tẩy trắng Plasma)**? Hãy nhắn cho tôi để nhận báo giá chi tiết và tư vấn nhanh nhất nhé!"),
        'custom_instructions' => get_option('flora_ai_custom_instructions', ''),
        'suggestions'         => get_option('flora_ai_suggestions', $default_suggestions)
    );
}

/**
 * 4. AJAX Lưu Cấu Hình AI từ WP-Admin
 */
function flora_ajax_save_ai_config() {
    check_ajax_referer('flora_pricing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền cập nhật cấu hình AI.'));
    }

    if (isset($_POST['api_key'])) {
        update_option('flora_gemini_api_key', sanitize_text_field(wp_unslash($_POST['api_key'])));
    }
    if (isset($_POST['model'])) {
        update_option('flora_gemini_model', sanitize_text_field(wp_unslash($_POST['model'])));
    }
    if (isset($_POST['doctor_name'])) {
        update_option('flora_ai_doctor_name', sanitize_text_field(wp_unslash($_POST['doctor_name'])));
    }
    if (isset($_POST['clinic_address'])) {
        update_option('flora_ai_clinic_address', sanitize_text_field(wp_unslash($_POST['clinic_address'])));
    }
    if (isset($_POST['clinic_hotline'])) {
        update_option('flora_ai_clinic_hotline', sanitize_text_field(wp_unslash($_POST['clinic_hotline'])));
    }
    if (isset($_POST['clinic_perks'])) {
        update_option('flora_ai_clinic_perks', sanitize_textarea_field(wp_unslash($_POST['clinic_perks'])));
    }
    if (isset($_POST['welcome_msg'])) {
        update_option('flora_ai_welcome_msg', sanitize_textarea_field(wp_unslash($_POST['welcome_msg'])));
    }
    if (isset($_POST['custom_instructions'])) {
        update_option('flora_ai_custom_instructions', sanitize_textarea_field(wp_unslash($_POST['custom_instructions'])));
    }
    if (isset($_POST['suggestions']) && is_array($_POST['suggestions'])) {
        $clean_sugs = array();
        foreach ($_POST['suggestions'] as $sug) {
            $label = sanitize_text_field($sug['label'] ?? '');
            $query = sanitize_text_field($sug['query'] ?? '');
            if (!empty($label) && !empty($query)) {
                $clean_sugs[] = array('label' => $label, 'query' => $query);
            }
        }
        if (!empty($clean_sugs)) {
            update_option('flora_ai_suggestions', $clean_sugs);
        }
    }

    wp_send_json_success(array('message' => 'Đã lưu cấu hình Bác Sĩ AI thành công!'));
}
add_action('wp_ajax_flora_save_ai_config', 'flora_ajax_save_ai_config');

// Backward compatibility alias
function flora_ajax_save_ai_key() {
    flora_ajax_save_ai_config();
}
add_action('wp_ajax_flora_save_ai_key', 'flora_ajax_save_ai_key');

/**
 * 5. Xây Dựng System Instruction (Tri Thức Bảng Giá & Chống Tuyệt Đối Bịa Đặt)
 */
function flora_build_ai_system_instruction() {
    $pricing = flora_get_pricing_data();
    $config  = flora_get_ai_config();
    
    $doctor_name    = !empty($config['doctor_name']) ? $config['doctor_name'] : 'Bác Sĩ Tư Vấn Flora';
    $clinic_address = !empty($config['clinic_address']) ? $config['clinic_address'] : '326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh';
    $clinic_hotline = !empty($config['clinic_hotline']) ? $config['clinic_hotline'] : '028 7305 8999';
    $clinic_perks   = trim($config['clinic_perks'] ?? '');
    $custom_inst    = trim($config['custom_instructions'] ?? '');
    
    // Biên dịch dữ liệu bảng giá thành văn bản tri thức
    $price_knowledge = "=== BẢNG GIÁ DỊCH VỤ NIÊM YẾT CHÍNH THỨC NHA KHOA FLORA ===\n\n";

    // 1. Implant
    $price_knowledge .= "1. CẤY GHÉP IMPLANT:\n";
    $price_knowledge .= "- Báo giá trụ đơn lẻ (Đã bao gồm trụ Implant + Khớp nối Abutment + Mão răng sứ trọn gói):\n";
    foreach ($pricing['implant']['tables']['single']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']} ({$r['origin']}): {$r['price']}" . (!empty($r['highlight']) ? " [DÒNG NỔI BẬT KHUYÊN DÙNG]" : "") . "\n";
    }
    $price_knowledge .= "- Báo giá trồng răng toàn hàm All-on-4 / All-on-6:\n";
    foreach ($pricing['implant']['tables']['allonx']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']}: All-on-4 (4 trụ) giá {$r['price_4']} | All-on-6 (6 trụ) giá {$r['price_6']}\n";
    }
    $price_knowledge .= "\n";

    // 2. Niềng răng
    $price_knowledge .= "2. CHỈNH NHA NIỀNG RĂNG:\n";
    $price_knowledge .= "- Niềng răng mắc cài (Trọn gói 2 hàm):\n";
    foreach ($pricing['nieng_rang']['tables']['mac_cai']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']}: {$r['price']}\n";
    }
    $price_knowledge .= "- Niềng khay trong suốt Invisalign (Mỹ):\n";
    foreach ($pricing['nieng_rang']['tables']['invisalign']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']}: {$r['price']}" . (!empty($r['highlight']) ? " [GÓI TOÀN DIỆN KHUYÊN DÙNG]" : "") . "\n";
    }
    $price_knowledge .= "\n";

    // 3. Răng sứ & Veneer
    $price_knowledge .= "3. RĂNG SỨ THẨM MỸ & DÁN SỨ VENEER:\n";
    foreach ($pricing['rang_su_veneer']['tables']['main']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']} ({$r['unit']}): {$r['price']}\n";
    }
    $price_knowledge .= "\n";

    // 4. Cười hở lợi
    $price_knowledge .= "4. ĐIỀU TRỊ CƯỜI HỞ LỢI:\n";
    foreach ($pricing['cuoi_ho_loi']['tables']['main']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']} ({$r['unit']}): {$r['price']}\n";
    }
    $price_knowledge .= "\n";

    // 5. Tổng quát
    $price_knowledge .= "5. NHA KHOA TỔNG QUÁT & ĐIỀU TRỊ:\n";
    foreach ($pricing['tong_quat']['tables']['main']['rows'] ?? array() as $r) {
        $price_knowledge .= "  + {$r['name']} ({$r['unit']}): {$r['price']}\n";
    }

    // 6. Các gói chăm sóc răng miệng điện tử ưu đãi đặc biệt (Hot Deals)
    $price_knowledge .= "\n6. CÁC GÓI CHĂM SÓC RĂNG MIỆNG ĐIỆN TỬ ƯU ĐÃI ĐẶC BIỆT:\n";
    $price_knowledge .= "- GÓI CARE PLUS (CHĂM RĂNG CHO CẢ NHÀ - CHĂM SÓC ĐỊNH KỲ):\n";
    $price_knowledge .= "  + Giá ưu đãi: 999.000 VNĐ / gói / năm (Giá gốc niêm yết: 2.500.000 VNĐ) — Tiết kiệm vượt trội, chỉ từ ~250.000 VNĐ/người/năm.\n";
    $price_knowledge .= "  + Đối tượng & Quy mô: Dành cho gia đình hoặc nhóm từ 2 đến 4 thành viên (đăng ký tối đa 4 người dùng chung 1 gói trong 1 năm).\n";
    $price_knowledge .= "  + Thời hạn sử dụng: 1 năm (365 ngày) trọn vẹn kể từ ngày đăng ký kích hoạt.\n";
    $price_knowledge .= "  + 4 Quyền lợi thiết thực nổi bật:\n";
    $price_knowledge .= "    (1) Cạo vôi răng MIỄN PHÍ KHÔNG GIỚI HẠN số lần trong suốt 1 năm cho tất cả 4 thành viên trong gói.\n";
    $price_knowledge .= "    (2) Thăm khám răng miệng định kỳ trực tiếp cùng Bác sĩ Chuyên Khoa I (BS CKI), phát hiện sớm sâu răng & bệnh lý nướu.\n";
    $price_knowledge .= "    (3) Giảm 10% chi phí điều trị phát sinh khi cần trám răng và chữa tủy.\n";
    $price_knowledge .= "    (4) Dùng chung linh hoạt cho gia đình, có thể bổ sung thông tin thành viên khi đến phòng khám.\n";
    $price_knowledge .= "  + Hướng dẫn tư vấn: Chủ động giới thiệu Gói Care Plus khi khách hỏi về cạo vôi răng lẻ, lấy cao răng, kiểm tra răng định kỳ, hoặc hỏi dịch vụ cho cả nhà/vợ chồng/con cái. Nhấn mạnh rằng cạo vôi lẻ 200k-400k/lần thì gói 999k dùng cho cả 4 người không giới hạn cả năm là phương án tiết kiệm nhất.\n\n";

    $price_knowledge .= "- GÓI FLORA WHITE UP (LÀM SẠCH & TẨY TRẮNG RĂNG CHUYÊN SÂU PLASMA):\n";
    $price_knowledge .= "  + Giá ưu đãi: 1.999.000 VNĐ / gói trọn gói (Giá gốc niêm yết: 3.000.000 VNĐ).\n";
    $price_knowledge .= "  + Đối tượng phù hợp: Khách hàng muốn nâng tông sắc độ răng trắng sáng tự nhiên, nụ cười rạng rỡ chuẩn bị cho sự kiện quan trọng (tiệc cưới, lễ tết, phỏng vấn, gặp đối tác) hoặc răng bị xỉn màu do trà, cà phê, thực phẩm màu.\n";
    $price_knowledge .= "  + Công nghệ ánh sáng Plasma lạnh chuẩn y khoa: An toàn tuyệt đối cho men răng, không gây kích ứng tủy, KHÔNG gây ê buốt kéo dài, răng bật sáng từ 2 đến 4 tone màu rõ rệt ngay sau liệu trình.\n";
    $price_knowledge .= "  + 4 Quyền lợi trọn gói đã bao gồm (Khách không phải phát sinh thêm phí phụ):\n";
    $price_knowledge .= "    (1) Thăm khám và chẩn đoán men răng trực tiếp cùng Bác sĩ CKI để chọn nồng độ gel chuẩn y khoa.\n";
    $price_knowledge .= "    (2) ĐÃ BAO GỒM cạo vôi răng & đánh bóng răng sạch sâu trước khi tẩy trắng (khách không tốn thêm tiền cạo vôi).\n";
    $price_knowledge .= "    (3) Tẩy trắng chuyên sâu với hệ thống đèn Plasma lạnh kích hoạt hoạt chất làm trắng sâu an toàn.\n";
    $price_knowledge .= "    (4) Thời hạn bảo lưu sử dụng cực dài: Hạn dùng linh hoạt đến hết ngày 31/12/2026.\n";
    $price_knowledge .= "  + Hướng dẫn tư vấn: Chủ động tư vấn Gói White Up khi khách hỏi về tẩy trắng răng, lo lắng tẩy trắng có đau hay ê buốt không, hỏi chi phí làm trắng răng. Nhấn mạnh công nghệ Plasma lạnh êm dịu, không ê buốt và mức giá ưu đãi trọn gói 1.999.000đ (đã bao gồm cạo vôi đánh bóng).\n";

    $perks_text = "";
    if (!empty($clinic_perks)) {
        $perks_text = "- Chính sách & Ưu đãi áp dụng thực tế:\n" . $clinic_perks . "\n";
    }

    $custom_text = "";
    if (!empty($custom_inst)) {
        $custom_text = "\nHƯỚNG DẪN BỔ SUNG TỪ PHÒNG KHÁM:\n" . $custom_inst . "\n";
    }

    $system_prompt = <<<EOT
Bạn là "{$doctor_name}" của Nha Khoa Flora (Flora Dental Clinic) tại TP.HCM. Bạn đang nhắn tin tư vấn trực tiếp với bệnh nhân/khách hàng trên website.

THÔNG TIN PHÒNG KHÁM:
- Đại diện tư vấn: {$doctor_name}
- Địa chỉ: {$clinic_address}
- Giờ làm việc: 8h30 - 18h30 (T2 - CN hàng tuần)
- Hotline/Zalo tư vấn: {$clinic_hotline} (Hỗ trợ 24/7)
{$perks_text}
BẢNG GIÁ DỊCH VỤ NIÊM YẾT CHÍNH THỨC:
{$price_knowledge}

QUY TẮC CỐT LÕI (BẮT BUỘC TUÂN THỦ 100% - TUYỆT ĐỐI KHÔNG ĐƯỢC BỊA ĐẶT THÔNG TIN):
1. TRUNG THỰC & CHÍNH XÁC TUYỆT ĐỐI THEO DỮ LIỆU:
   - CHỈ cung cấp thông tin, báo giá và dịch vụ CÓ TRONG BẢNG GIÁ VÀ THÔNG TIN PHÒNG KHÁM Ở TRÊN.
   - TUYỆT ĐỐI KHÔNG tự ý bịa đặt bất kỳ chương trình khuyến mãi, ưu đãi, quà tặng, thiết bị máy móc (như máy quét 3D TRIOS, chụp phim CT 3D miễn phí, giữ suất chụp CT, gói trả góp...) nếu KHÔNG có trong dữ liệu phòng khám cung cấp.
   - Nếu khách hỏi về dịch vụ hoặc chi phí chưa có trong bảng giá, hãy giải thích rõ: Khách hàng cần ghé phòng khám để bác sĩ kiểm tra cụ thể hoặc liên hệ Hotline {$clinic_hotline} để được báo giá chính xác. Tuyệt đối không tự đoán mức giá.

2. VĂN PHONG TỰ NHIÊN, CHUYÊN NGHIỆP:
   - Nhắn tin tự nhiên, thân thiện, ân cần, giải thích dễ hiểu, đi thẳng vào câu hỏi của khách hàng.
   - TUYỆT ĐỐI KHÔNG xuất ra các tiêu đề máy móc (như "1. CHẨN ĐOÁN...", "2. BÁO GIÁ...", "3. ĐỐI ĐÁP...").
   - Báo giá rõ ràng, minh bạch đúng mức giá trong Bảng Giá Niêm Yết.

3. MỜI KHÁM & XIN SỐ ĐIỆN THOẠI LỊCH SỰ, CHÂN THÀNH:
   - Khi giải đáp thắc mắc xong, mời khách để lại Số Điện Thoại (hoặc Zalo) để Bác sĩ/Chuyên viên liên hệ tư vấn chi tiết hơn và sắp xếp lịch hẹn phù hợp cho khách.
   - Ví dụ chuẩn mực: "Bạn có thể để lại Số Điện Thoại (hoặc Zalo) để Bác sĩ/Chuyên viên liên hệ hỗ trợ tư vấn chi tiết và sắp xếp lịch hẹn phù hợp cho mình nhé!"
   - TUYỆT ĐỐI KHÔNG dùng các câu bịa đặt như "giữ suất chụp CT 3D miễn phí" hay "quà tặng miễn phí".
   - Khi khách ĐÃ ĐỂ LẠI SỐ ĐIỆN THOẠI: Xác nhận ấm áp: "Dạ Bác sĩ Flora đã ghi nhận số điện thoại của bạn rồi nhé! Đội ngũ phòng khám sẽ liên hệ lại sớm nhất để hỗ trợ tư vấn chi tiết cho bạn ạ."

4. GỢI Ý THÔNG MINH VỀ 2 GÓI CHĂM SÓC RĂNG MIỆNG ĐIỆN TỬ:
   - Khi khách hàng hỏi về cạo vôi răng, lấy cao răng, kiểm tra răng định kỳ hoặc chăm sóc răng cho gia đình: Hãy chủ động tư vấn **Gói CARE PLUS (999.000đ/năm)** — áp dụng tối đa cho 4 người trong 1 năm, cạo vôi MIỄN PHÍ KHÔNG GIỚI HẠN, khám cùng Bác sĩ CKI và giảm 10% trám răng/chữa tủy.
   - Khi khách hàng hỏi về tẩy trắng răng, làm sáng nụ cười, răng xỉn màu do trà/cà phê hoặc hỏi làm đẹp răng đón Tết/sự kiện: Hãy giải thích ưu điểm êm ái của công nghệ **Ánh sáng Plasma lạnh** (an toàn men răng, không buốt) và giới thiệu **Gói FLORA WHITE UP (1.999.000đ trọn gói)** đã bao gồm cả cạo vôi đánh bóng, hạn dùng linh hoạt đến hết 31/12/2026.
   - Hướng dẫn khách: Khách có thể đăng ký mua trực tuyến ngay trên trang Gói Dịch Vụ của website Flora hoặc để lại Số Điện Thoại để nhận mã kích hoạt gói và hướng dẫn sử dụng.
{$custom_text}
EOT;

    return $system_prompt;
}

/**
 * 5. AJAX Endpoint Xử Lý Chẩn Đoán & Báo Giá AI từ Client & Admin
 */
function flora_ajax_ai_diagnose() {
    // Cho phép gọi cả từ khách vãng lai và admin
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'flora_ai_nonce') && !wp_verify_nonce($nonce, 'flora_pricing_nonce') && !wp_verify_nonce($nonce, 'flora_booking_nonce')) {
        // Fallback nhẹ nếu nonce hết hạn
    }

    $user_message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    if (empty($user_message)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập câu hỏi hoặc mô tả tình trạng răng miệng của bạn.'));
    }

    // TỰ ĐỘNG BẮT VÀ LƯU LEAD VÀO DATABASE KHI KHÁCH CUNG CẤP SỐ ĐIỆN THOẠI
    $clean_digits = preg_replace('/[^0-9+]/', '', $user_message);
    $phone_captured = false;
    if (preg_match('/(?:(?:\+84|84|0)[3|5|7|8|9][0-9]{8})|(?:0[2][0-9]{8,9})/', $clean_digits, $matches)) {
        $extracted_phone = $matches[0];
        if (str_starts_with($extracted_phone, '84')) {
            $extracted_phone = '0' . substr($extracted_phone, 2);
        } elseif (str_starts_with($extracted_phone, '+84')) {
            $extracted_phone = '0' . substr($extracted_phone, 3);
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'flora_bookings';
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            if (function_exists('flora_create_booking_table')) {
                flora_create_booking_table();
            }
        }

        $wpdb->insert(
            $table_name,
            array(
                'name'           => 'Khách tư vấn Bác sĩ AI',
                'phone'          => $extracted_phone,
                'email'          => '',
                'location'       => '326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh',
                'gender'         => '',
                'service'        => 'Bác Sĩ AI Báo Giá & Tư Vấn Khám',
                'preferred_time' => '8h30 - 18h30 (T2 - CN)',
                'source_page'    => wp_get_referer() ?: home_url('/'),
                'status'         => 'new',
                'notes'          => 'Khách gửi SĐT qua AI Chatbot: ' . $user_message,
                'ip_address'     => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? ''),
                'created_at'     => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

        $backup_leads = get_option('flora_booking_leads_backup', array());
        $backup_leads[] = array(
            'fullname'       => 'Khách tư vấn Bác sĩ AI',
            'phone'          => $extracted_phone,
            'clinic'         => 'Bác Sĩ AI Báo Giá & Tư Vấn Khám',
            'note'           => 'Khách gửi SĐT qua AI: ' . $user_message,
            'time'           => current_time('mysql')
        );
        update_option('flora_booking_leads_backup', array_slice($backup_leads, -200), false);
        $phone_captured = true;

        // Bắn thông báo Zalo khi có Lead từ Bác Sĩ AI
        if (function_exists('flora_send_zalo_lead_notification')) {
            flora_send_zalo_lead_notification(array(
                'name'           => 'Khách tư vấn Bác sĩ AI',
                'phone'          => $extracted_phone,
                'email'          => '',
                'service'        => 'Bác Sĩ AI Báo Giá & Tư Vấn Khám',
                'preferred_time' => '8h30 - 18h30 (T2 - CN)',
                'source_page'    => 'Bác Sĩ AI Chatbot (Website)',
                'notes'          => 'Khách gửi qua AI: ' . $user_message
            ));
        }
    }

    $api_key = flora_get_ai_api_key();
    $model   = flora_get_ai_model();

    if (empty($api_key)) {
        wp_send_json_error(array('message' => 'Hệ thống AI chưa được cấu hình API Key. Vui lòng liên hệ quản trị viên.'));
    }

    $system_instruction = flora_build_ai_system_instruction();

    // Chuẩn bị Payload gửi đến Google Gemini 3.5 Flash Lite API
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}";

    $payload = array(
        'system_instruction' => array(
            'parts' => array(
                array('text' => $system_instruction)
            )
        ),
        'contents' => array(
            array(
                'role' => 'user',
                'parts' => array(
                    array('text' => $user_message)
                )
            )
        ),
        'generationConfig' => array(
            'temperature'     => 0.35,
            'topP'            => 0.9,
            'maxOutputTokens' => 1000
        )
    );

    $args = array(
        'body'        => wp_json_encode($payload),
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'timeout'     => 25,
        'data_format' => 'body'
    );

    $response = wp_remote_post($endpoint, $args);

    if (is_wp_error($response)) {
        wp_send_json_error(array('message' => 'Không thể kết nối đến máy chủ AI: ' . $response->get_error_message()));
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $result_data   = json_decode($response_body, true);

    if ($response_code !== 200) {
        $err_msg = $result_data['error']['message'] ?? 'Lỗi không xác định từ Gemini API (' . $response_code . ')';
        wp_send_json_error(array('message' => $err_msg));
    }

    $ai_reply = $result_data['candidates'][0]['content']['parts'][0]['text'] ?? '';

    if (empty($ai_reply)) {
        wp_send_json_error(array('message' => 'AI không thể tạo câu trả lời vào lúc này.'));
    }

    // Làm sạch triệt để các tiêu đề máy móc nếu có
    $ai_reply = preg_replace('/^\s*(?:\d+\.|\*|-)?\s*(?:CHẨN ĐOÁN|BÁO GIÁ|ĐỐI ĐÁP|HƯỚNG DẪN|QUY TẮC|PHÂN TÍCH)[^\n:]*[:\n]*/mui', '', $ai_reply);
    $ai_reply = preg_replace('/^\s*Tuy nhiên,\s*dựa trên thông tin[^\n]*\n*/mui', '', $ai_reply);
    $ai_reply = trim($ai_reply);

    wp_send_json_success(array(
        'reply'          => $ai_reply,
        'model'          => $model,
        'phone_captured' => $phone_captured
    ));
}
add_action('wp_ajax_flora_ai_diagnose', 'flora_ajax_ai_diagnose');
add_action('wp_ajax_nopriv_flora_ai_diagnose', 'flora_ajax_ai_diagnose');

/**
 * 6. Helper Render Nút Bấm & Modal Bác Sĩ AI Tư Vấn ngoài Frontend
 */
function flora_render_ai_consultation_widget() {
    $nonce = wp_create_nonce('flora_ai_nonce');
    $config = flora_get_ai_config();
    $mascot_url = flora_asset('images/mascot-bac-minh.webp');
    $avatar_url = flora_asset('images/mascot-avatar.webp');
    $doctor_name = !empty($config['doctor_name']) ? $config['doctor_name'] : 'Bác Sĩ Tư Vấn Flora';
    $welcome_msg = !empty($config['welcome_msg']) ? $config['welcome_msg'] : "Xin chào bạn! Tôi là Bác Sĩ Tư Vấn của Nha Khoa Flora 🩺.\n\nBạn đang quan tâm đến dịch vụ nào hoặc cần tư vấn phác đồ điều trị? Hãy nhắn cho tôi để nhận báo giá chi tiết và hỗ trợ nhanh nhất nhé!";
    $suggestions = !empty($config['suggestions']) && is_array($config['suggestions']) ? $config['suggestions'] : array();
    ?>
    <!-- ─── FLORA AI DOCTOR FLOATING BUTTON & CHAT MODAL ─── -->
    <div id="flora-ai-widget-container">
        <!-- Floating Trigger Button with Doctor Mascot -->
        <button id="flora-ai-trigger-btn" aria-label="<?php echo esc_attr($doctor_name); ?>" title="<?php echo esc_attr($doctor_name); ?> - Tư Vấn & Báo Giá 24/7">
            <span class="ai-btn-pulse"></span>
            <div class="flora-ai-mascot-wrap">
                <img src="<?php echo $mascot_url; ?>" alt="<?php echo esc_attr($doctor_name); ?>" class="flora-ai-mascot-img" width="62" height="112" loading="eager" />
            </div>
            <div class="flora-ai-btn-label">
                <span class="ai-btn-text">Bác Sĩ AI</span>
            </div>
        </button>

        <!-- AI Chat Modal Popup -->
        <div id="flora-ai-modal" class="flora-ai-modal-hidden" aria-hidden="true">
            <div class="flora-ai-modal-header">
                <div class="flora-ai-doctor-info">
                    <div class="flora-ai-avatar">
                        <img src="<?php echo $avatar_url; ?>" alt="<?php echo esc_attr($doctor_name); ?>" class="ai-header-mascot-img" />
                        <span class="ai-online-dot"></span>
                    </div>
                    <div class="flora-ai-title-wrap">
                        <h4><?php echo esc_html($doctor_name); ?></h4>
                        <span class="ai-status-badge"><i class="fa-solid fa-bolt"></i> Dr. Flora AI • Trực tuyến</span>
                    </div>
                </div>
                <button type="button" id="flora-ai-close-btn" aria-label="Đóng">&times;</button>
            </div>

            <div class="flora-ai-modal-body" id="flora-ai-messages">
                <div class="flora-ai-msg ai-msg-bot">
                    <div class="ai-msg-avatar">
                        <img src="<?php echo $avatar_url; ?>" alt="<?php echo esc_attr($doctor_name); ?>" class="ai-msg-mascot-img" />
                    </div>
                    <div class="ai-msg-bubble">
                        <?php 
                        $formatted_welcome = esc_html($welcome_msg);
                        $formatted_welcome = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $formatted_welcome);
                        $formatted_welcome = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $formatted_welcome);
                        $formatted_welcome = nl2br($formatted_welcome);
                        echo $formatted_welcome;
                        ?>
                    </div>
                </div>
            </div>

            <!-- Quick Suggestion Tags -->
            <?php if (!empty($suggestions)): ?>
            <div class="flora-ai-suggestions">
                <?php foreach ($suggestions as $sug): 
                    $label = !empty($sug['label']) ? $sug['label'] : '';
                    $query = !empty($sug['query']) ? $sug['query'] : $label;
                    if (empty($label)) continue;
                ?>
                <button type="button" class="ai-sug-tag" data-query="<?php echo esc_attr($query); ?>"><?php echo esc_html($label); ?></button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="flora-ai-modal-footer">
                <form id="flora-ai-chat-form">
                    <input type="text" id="flora-ai-input" placeholder="Mô tả tình trạng răng miệng hoặc hỏi giá..." autocomplete="off" />
                    <button type="submit" id="flora-ai-send-btn" aria-label="Gửi">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- AI Widget Styling -->
    <style>
        #flora-ai-widget-container {
            position: fixed;
            bottom: 24px;
            left: 24px;
            right: auto;
            z-index: 99999;
            font-family: var(--font-body, 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif);
        }
        #flora-ai-trigger-btn {
            display: inline-flex;
            align-items: center;
            position: relative;
            background: linear-gradient(180deg, #026ae8 0%, #0056cb 50%, #0045b5 100%);
            color: #ffffff;
            border: 3px solid #67b8ff;
            border-radius: 9999px;
            padding: 0 24px 0 10px;
            height: 52px;
            cursor: pointer;
            box-shadow: 0 10px 28px rgba(0, 80, 200, 0.45), 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            outline: none;
            overflow: visible;
        }
        #flora-ai-trigger-btn:hover {
            transform: translateY(-4px) scale(1.03);
            box-shadow: 0 14px 34px rgba(0, 80, 200, 0.55), 0 0 20px rgba(112, 196, 255, 0.6);
            border-color: #a5dcff;
        }
        #flora-ai-trigger-btn:active {
            transform: translateY(-1px) scale(0.98);
        }
        .ai-btn-pulse {
            position: absolute;
            inset: -4px;
            border-radius: 9999px;
            background: rgba(4, 147, 241, 0.45);
            z-index: -1;
            animation: aiPulse 2.4s infinite cubic-bezier(0.25, 1, 0.5, 1);
            pointer-events: none;
        }
        @keyframes aiPulse {
            0% { transform: scale(0.98); opacity: 0.8; }
            50% { transform: scale(1.08, 1.15); opacity: 0; }
            100% { transform: scale(1.15, 1.25); opacity: 0; }
        }
        .flora-ai-mascot-wrap {
            position: relative;
            width: 46px;
            height: 52px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            margin-right: 10px;
            margin-left: -6px;
            flex-shrink: 0;
            pointer-events: none;
        }
        .flora-ai-mascot-img {
            position: absolute;
            bottom: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: auto;
            max-height: 82px;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.25));
            transition: transform 0.3s ease;
            animation: mascotFloat 3s ease-in-out infinite;
        }
        #flora-ai-trigger-btn:hover .flora-ai-mascot-img {
            transform: translateX(-50%) translateY(-3px) scale(1.06);
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.35));
        }
        @keyframes mascotFloat {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-4px); }
        }
        .flora-ai-btn-label {
            display: flex;
            align-items: center;
        }
        .ai-btn-text {
            font-size: 1.12rem;
            font-weight: 800;
            letter-spacing: -0.2px;
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0, 30, 100, 0.4);
            white-space: nowrap;
        }

        /* Modal Chatbox - Larger & More Spacious */
        #flora-ai-modal {
            position: fixed;
            bottom: 86px;
            left: 24px;
            right: auto;
            width: 440px;
            max-width: calc(100vw - 40px);
            height: 600px;
            max-height: calc(100vh - 180px);
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 51, 163, 0.25);
            border: 1px solid rgba(4, 147, 241, 0.22);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            transform-origin: bottom left;
            z-index: 100000;
        }
        #flora-ai-modal.flora-ai-modal-hidden {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.85) translateY(20px);
        }
        .flora-ai-modal-header {
            background: linear-gradient(135deg, #002270 0%, #0033a3 100%);
            color: #ffffff;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .flora-ai-doctor-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .flora-ai-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: visible;
            border: 2px solid #70c4ff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        .ai-header-mascot-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            object-position: top center;
        }
        .ai-online-dot {
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #10b981;
            border: 2px solid #ffffff;
            box-shadow: 0 0 6px rgba(16, 185, 129, 0.8);
            z-index: 2;
        }
        .flora-ai-title-wrap h4 {
            margin: 0;
            font-size: 0.96rem;
            font-weight: 700;
            color: #ffffff;
        }
        .ai-status-badge {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.82);
            display: flex;
            align-items: center;
            gap: 4px;
        }
        #flora-ai-close-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.6rem;
            cursor: pointer;
            line-height: 1;
            padding: 0 4px;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        #flora-ai-close-btn:hover { opacity: 1; }

        .flora-ai-modal-body {
            flex: 1;
            padding: 14px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8fafc;
        }
        .flora-ai-msg {
            display: flex;
            gap: 10px;
            max-width: 90%;
        }
        .ai-msg-bot { align-self: flex-start; }
        .ai-msg-user { align-self: flex-end; flex-direction: row-reverse; }
        .ai-msg-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 1.5px solid #70c4ff;
            overflow: hidden;
            flex-shrink: 0;
            margin-top: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .ai-msg-mascot-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }
        .ai-msg-bubble {
            padding: 10px 14px;
            border-radius: 14px;
            font-size: 0.86rem;
            line-height: 1.55;
            color: #1e293b;
        }
        .ai-msg-bot .ai-msg-bubble {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-top-left-radius: 3px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .ai-msg-user .ai-msg-bubble {
            background: #0033a3;
            color: #ffffff;
            border-top-right-radius: 3px;
        }

        /* Typing & Thinking Animation */
        .ai-typing-bubble {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 16px;
            border-top-left-radius: 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .ai-thinking-label {
            font-size: 0.82rem;
            color: #475569;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .ai-thinking-label i {
            color: #0493f1;
            animation: aiIconPulse 1.5s infinite;
        }
        .ai-typing-dots {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .ai-typing-dots span {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
            display: inline-block;
            animation: aiTypingBounce 1.4s infinite ease-in-out both;
        }
        .ai-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .ai-typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        .ai-typing-dots span:nth-child(3) { animation-delay: 0s; }

        @keyframes aiTypingBounce {
            0%, 80%, 100% {
                transform: scale(0.4);
                opacity: 0.4;
            }
            40% {
                transform: scale(1.1);
                opacity: 1;
            }
        }
        @keyframes aiIconPulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
        }

        .flora-ai-suggestions {
            display: flex;
            gap: 6px;
            padding: 8px 12px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            overflow-x: auto;
            white-space: nowrap;
        }
        .flora-ai-suggestions::-webkit-scrollbar { height: 3px; }
        .flora-ai-suggestions::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .ai-sug-tag {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 4px 10px;
            font-size: 0.74rem;
            color: #0033a3;
            cursor: pointer;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .ai-sug-tag:hover {
            background: #0493f1;
            color: #ffffff;
            border-color: #0493f1;
        }

        .flora-ai-modal-footer {
            padding: 10px 12px;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
        #flora-ai-chat-form {
            display: flex;
            gap: 6px;
        }
        #flora-ai-input {
            flex: 1;
            border: 1px solid #cbd5e1;
            border-radius: 20px;
            padding: 9px 15px;
            font-size: 0.86rem;
            outline: none;
            transition: border 0.2s;
        }
        #flora-ai-input:focus { border-color: #0493f1; }
        #flora-ai-send-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            background: #0493f1;
            color: #ffffff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s, background 0.2s;
        }
        #flora-ai-send-btn:hover { background: #0033a3; transform: scale(1.05); }

        @media (max-width: 768px) {
            #flora-ai-widget-container { bottom: 85px; left: 12px; right: auto; }
            #flora-ai-trigger-btn {
                height: 46px;
                padding: 0 16px 0 8px;
                border-width: 2.5px;
            }
            .flora-ai-mascot-wrap {
                width: 38px;
                height: 46px;
                margin-right: 8px;
                margin-left: -4px;
            }
            .flora-ai-mascot-img {
                width: 42px;
                max-height: 70px;
                bottom: -2px;
            }
            .ai-btn-text {
                font-size: 0.92rem;
            }
            #flora-ai-modal {
                left: 10px;
                right: 10px;
                bottom: 140px;
                width: calc(100vw - 20px);
                height: 520px;
                max-height: calc(100vh - 160px);
            }
        }
    </style>

    <!-- AI Widget Frontend Logic -->
    <script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            var triggerBtn = document.getElementById('flora-ai-trigger-btn');
            var modal = document.getElementById('flora-ai-modal');
            var closeBtn = document.getElementById('flora-ai-close-btn');
            var form = document.getElementById('flora-ai-chat-form');
            var input = document.getElementById('flora-ai-input');
            var msgBox = document.getElementById('flora-ai-messages');
            var sugTags = document.querySelectorAll('.ai-sug-tag');
            var mascotAvatarUrl = '<?php echo $avatar_url; ?>';

            if (!triggerBtn || !modal) return;

            // Toggle modal
            triggerBtn.addEventListener('click', function() {
                modal.classList.toggle('flora-ai-modal-hidden');
                if (!modal.classList.contains('flora-ai-modal-hidden')) {
                    input.focus();
                }
            });

            closeBtn.addEventListener('click', function() {
                modal.classList.add('flora-ai-modal-hidden');
            });

            // Quick suggestions
            sugTags.forEach(function(tag) {
                tag.addEventListener('click', function() {
                    var query = this.getAttribute('data-query');
                    input.value = query;
                    form.dispatchEvent(new Event('submit'));
                });
            });

            // Helper format markdown bold/linebreaks
            function formatAiText(text) {
                var escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                escaped = escaped.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                escaped = escaped.replace(/\*(.*?)\*/g, '<em>$1</em>');
                escaped = escaped.replace(/\n/g, '<br>');
                return escaped;
            }

            // Append message
            function appendMsg(role, text) {
                var div = document.createElement('div');
                div.className = 'flora-ai-msg ' + (role === 'user' ? 'ai-msg-user' : 'ai-msg-bot');
                
                var avatarHtml = role === 'user' ? '' : '<div class="ai-msg-avatar"><img src="' + mascotAvatarUrl + '" alt="Bác Sĩ AI" class="ai-msg-mascot-img" /></div>';
                div.innerHTML = avatarHtml + '<div class="ai-msg-bubble">' + formatAiText(text) + '</div>';
                msgBox.appendChild(div);
                msgBox.scrollTop = msgBox.scrollHeight;
                return div;
            }

            // Submit handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                var msg = input.value.trim();
                if (!msg) return;

                appendMsg('user', msg);
                input.value = '';

                // Modern animated Typing & Thinking Indicator
                var typingDiv = document.createElement('div');
                typingDiv.className = 'flora-ai-msg ai-msg-bot';
                typingDiv.id = 'flora-ai-thinking-indicator';
                typingDiv.innerHTML = '<div class="ai-msg-avatar"><img src="' + mascotAvatarUrl + '" alt="Bác Sĩ AI" class="ai-msg-mascot-img" /></div>' +
                    '<div class="ai-typing-bubble">' +
                        '<span class="ai-thinking-label"><i class="fa-solid fa-sparkles"></i> Bác sĩ AI đang phân tích & báo giá</span>' +
                        '<div class="ai-typing-dots"><span></span><span></span><span></span></div>' +
                    '</div>';
                msgBox.appendChild(typingDiv);
                msgBox.scrollTop = msgBox.scrollHeight;

                var formData = new FormData();
                formData.append('action', 'flora_ai_diagnose');
                formData.append('nonce', '<?php echo $nonce; ?>');
                formData.append('message', msg);

                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(function(r) { return r.json(); })
                .then(function(res) {
                    var indicator = document.getElementById('flora-ai-thinking-indicator');
                    if (indicator) indicator.remove();

                    if (res.success && res.data && res.data.reply) {
                        appendMsg('bot', res.data.reply);

                        // Trigger lead tracking event if phone was captured
                        if (res.data.phone_captured) {
                            try {
                                if (typeof gtag === 'function') {
                                    gtag('event', 'generate_lead', {
                                        event_category: 'Flora AI Chat Lead',
                                        event_label: 'Bác Sĩ AI Báo Giá'
                                    });
                                }
                                if (window.dataLayer && Array.isArray(window.dataLayer)) {
                                    window.dataLayer.push({
                                        event: 'lead_form_success',
                                        form_name: 'Bác Sĩ AI Tư Vấn'
                                    });
                                }
                            } catch(trackErr) {}
                        }
                    } else {
                        appendMsg('bot', 'Xin lỗi, ' + (res.data && res.data.message ? res.data.message : 'đã xảy ra lỗi khi kết nối. Bạn vui lòng liên hệ hotline 028 7305 8999 để được hỗ trợ ngay.'));
                    }
                })
                .catch(function(err) {
                    var indicator = document.getElementById('flora-ai-thinking-indicator');
                    if (indicator) indicator.remove();
                    appendMsg('bot', 'Đã xảy ra lỗi kết nối mạng. Vui lòng thử lại sau giây lát.');
                });
            });
        });
    })();
    </script>
    <?php
}
