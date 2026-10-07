<?php
/**
 * Flora Dental Clinic - Order & VietQR ACB Manual Confirmation Manager
 * Hệ thống Quản lý Đơn hàng Gói dịch vụ & Xác thực Chuyển khoản Thủ công Ngân hàng ACB
 *
 * Tài khoản nhận tiền duy nhất:
 * - Số TK: 77779268
 * - Tên chủ TK: CONG TY CO PHAN FLORA DENTAL CARE
 * - Ngân hàng: Ngân hàng TMCP Á Châu (ACB)
 * - Chi nhánh: ACB - CN HOA HUNG
 *
 * Tính năng chính:
 * 1. Cơ sở dữ liệu wp_flora_orders chuẩn hóa với Idempotency Key & Safe Migration
 * 2. Xác thực số điện thoại di động Việt Nam chuẩn 10 chữ số
 * 3. Tạo đơn khi khách bấm "Tôi đã chuyển khoản" (Trạng thái: pending_confirmation)
 * 4. Gửi email xác nhận tiếp nhận đơn hàng cho khách (Email 1)
 * 5. Bắn thông báo Realtime qua Webhook đến Zalo Bot / Nhóm CSKH (kèm ảnh minh chứng nếu có)
 * 6. Cho phép khách hàng tải lên minh chứng chuyển khoản (Ảnh bill chụp màn hình - Tùy chọn)
 * 7. Tư vấn viên duyệt (Paid -> Gửi Email 2) hoặc từ chối (Rejected -> Gửi Email 3 kèm hotline khiếu nại)
 * 8. Tự động quét và hủy đơn quá 24 giờ chưa được xác nhận, gửi email từ chối + hotline giải quyết
 * 9. API tra cứu trạng thái đơn hàng theo Mã đơn hoặc Số điện thoại
 * 10. Giao diện quản trị WP-Admin chuyên nghiệp, xem ảnh bill phóng to, duyệt/từ chối 1-click
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. ĐỊNH NGHĨA BẢNG CƠ SỞ DỮ LIỆU & TỰ ĐỘNG MIGRATION
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_get_orders_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_orders';
}

function flora_create_orders_table() {
    global $wpdb;
    $table_name = flora_get_orders_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        order_code varchar(64) NOT NULL,
        idempotency_key varchar(128) NOT NULL,
        customer_name varchar(255) NOT NULL,
        customer_phone varchar(50) NOT NULL,
        customer_email varchar(255) DEFAULT '',
        need_consult tinyint(1) DEFAULT 1,
        package_id varchar(100) NOT NULL,
        package_name varchar(255) NOT NULL,
        original_price bigint(20) NOT NULL DEFAULT 0,
        discount_amount bigint(20) NOT NULL DEFAULT 0,
        final_amount bigint(20) NOT NULL DEFAULT 0,
        voucher_code varchar(100) DEFAULT '',
        affiliate_code varchar(50) DEFAULT '',
        affiliate_id bigint(20) unsigned DEFAULT 0,
        commission_amount bigint(20) NOT NULL DEFAULT 0,
        commission_status varchar(30) NOT NULL DEFAULT 'pending',
        transfer_syntax varchar(255) NOT NULL,
        payment_method varchar(50) NOT NULL DEFAULT 'acb_vietqr',
        payment_status varchar(50) NOT NULL DEFAULT 'pending_confirmation',
        proof_image_url text DEFAULT NULL,
        user_confirmed_transfer tinyint(1) DEFAULT 1,
        user_confirmed_at datetime DEFAULT NULL,
        paid_at datetime DEFAULT NULL,
        rejected_at datetime DEFAULT NULL,
        reject_reason varchar(255) DEFAULT '',
        notes text DEFAULT NULL,
        ip_address varchar(100) DEFAULT '',
        user_agent varchar(500) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_order_code (order_code),
        UNIQUE KEY uq_idempotency (idempotency_key),
        KEY idx_phone (customer_phone),
        KEY idx_status (payment_status),
        KEY idx_created (created_at),
        KEY idx_affiliate (affiliate_id),
        KEY idx_voucher (voucher_code),
        KEY idx_aff_status (affiliate_id, payment_status)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Kiểm tra & bổ sung cột nếu bảng đã tồn tại từ trước (Safe Migration)
    $existing_cols = $wpdb->get_col("DESC $table_name", 0);
    if (!empty($existing_cols)) {
        if (!in_array('proof_image_url', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN proof_image_url text DEFAULT NULL AFTER payment_status");
        }
        if (!in_array('rejected_at', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN rejected_at datetime DEFAULT NULL AFTER paid_at");
        }
        if (!in_array('reject_reason', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN reject_reason varchar(255) DEFAULT '' AFTER rejected_at");
        }
        if (!in_array('user_confirmed_transfer', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN user_confirmed_transfer tinyint(1) DEFAULT 1 AFTER proof_image_url");
        }
        if (!in_array('user_confirmed_at', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN user_confirmed_at datetime DEFAULT NULL AFTER user_confirmed_transfer");
        }
        if (!in_array('affiliate_code', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN affiliate_code varchar(50) DEFAULT '' AFTER voucher_code");
        }
        if (!in_array('affiliate_id', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN affiliate_id bigint(20) unsigned DEFAULT 0 AFTER affiliate_code");
        }
        if (!in_array('commission_amount', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN commission_amount bigint(20) NOT NULL DEFAULT 0 AFTER final_amount");
        }
        if (!in_array('commission_status', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN commission_status varchar(30) NOT NULL DEFAULT 'pending' AFTER commission_amount");
        }
    }
}
add_action('after_switch_theme', 'flora_create_orders_table');

add_action('admin_init', function() {
    global $wpdb;
    $table_name = flora_get_orders_table_name();
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        flora_create_orders_table();
    } else {
        $col = $wpdb->get_results("SHOW COLUMNS FROM `$table_name` LIKE 'proof_image_url'");
        if (empty($col)) {
            flora_create_orders_table();
        }
    }
});

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 2. CẤU HÌNH TÀI KHOẢN NGÂN HÀNG ACB & ZALO BOT WEBHOOK
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_get_payment_config() {
    return array(
        'bank_code'          => 'ACB',
        'bank_name'          => 'Ngân hàng TMCP Á Châu (ACB)',
        'bank_account'       => '77779268',
        'bank_owner'         => 'CONG TY CO PHAN FLORA DENTAL CARE',
        'bank_branch'        => 'ACB - CN HOA HUNG',
        'zalo_webhook_url'   => get_option('flora_zalo_webhook_url', ''),
        'zalo_bot_token'     => get_option('flora_zalo_bot_token', ''),
        'zalo_group_chat_id' => get_option('flora_zalo_group_chat_id', ''),
        'zalo_secret_token'  => get_option('flora_zalo_secret_token', 'flora2026'),
        'hotline'            => '028 7305 8999',
        'zalo_support'       => '0902 535 068',
        'support_email'      => 'support@floraclinic.vn',
        'complaint_url'      => home_url('/lien-he/')
    );
}

// Giữ lại hàm cũ để tương thích các module khác nếu có gọi
function flora_get_momo_config() {
    $cfg = flora_get_payment_config();
    return array(
        'partner_code' => '',
        'access_key'   => '',
        'secret_key'   => '',
        'environment'  => 'production',
        'momo_phone'   => '',
        'momo_name'    => '',
        'bank_code'    => $cfg['bank_code'],
        'bank_account' => $cfg['bank_account'],
        'bank_owner'   => $cfg['bank_owner'],
        'bank_branch'  => $cfg['bank_branch']
    );
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 3. HÀM XÁC THỰC SỐ ĐIỆN THOẠI DI ĐỘNG VIỆT NAM (CHUẨN 10 SỐ)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_validate_vn_phone($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);

    if (strpos($clean, '84') === 0 && strlen($clean) === 11) {
        $clean = '0' . substr($clean, 2);
    }

    $pattern = '/^(03[2-9]|05[2689]|07[06-9]|08[1-9]|09[0-9])[0-9]{7}$/';
    if (preg_match($pattern, $clean)) {
        return $clean;
    }
    return false;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 4. BẮN THÔNG BÁO REALTIME ZALO BOT WEBHOOK / NHÓM TELEGRAM / LARK
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_send_zalo_notification($order, $event_type = 'order_awaiting', $extra = array()) {
    $config = flora_get_payment_config();
    $bot_token     = trim($config['zalo_bot_token']);
    $group_chat_id = trim($config['zalo_group_chat_id']);
    $webhook_url   = trim($config['zalo_webhook_url']);

    if (empty($bot_token) && empty($webhook_url)) {
        return false;
    }

    $amount_fmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
    $admin_link = admin_url('admin.php?page=flora-orders&s=' . urlencode($order['order_code']));
    $proof_img  = !empty($order['proof_image_url']) ? $order['proof_image_url'] : (!empty($extra['proof_image_url']) ? $extra['proof_image_url'] : '');

    $title = '';
    $message = '';

    if ($event_type === 'order_awaiting') {
        $title = "🔔 [ CÓ ĐƠN HÀNG MỚI CHỜ DUYỆT ] 🔔";
        $message = "🔔 [ CÓ ĐƠN HÀNG MỚI CHỜ DUYỆT ] 🔔\n"
                 . "━━━━━━\n"
                 . "Dịch vụ: Nha Khoa Flora - Gói Khám\n"
                 . "Mã đơn: #" . $order['order_code'] . "\n\n"
                 . "👤 Thông Tin Khách Hàng:\n"
                 . "  ▸ Họ tên: " . $order['customer_name'] . "\n"
                 . "  ▸ Số ĐT: " . $order['customer_phone'] . "\n"
                 . "  ▸ Email: " . (!empty($order['customer_email']) ? $order['customer_email'] : "Chưa cung cấp") . "\n"
                 . "  └─ Gói đăng ký: " . $order['package_name'] . "\n"
                 . "  └─ Số tiền: " . $amount_fmt . "\n"
                 . (!empty($order['affiliate_code']) ? ("  └─ Mã giới thiệu: " . $order['affiliate_code'] . "\n") : "")
                 . (!empty($proof_img) ? ("  └─ Ảnh bill: " . $proof_img . "\n") : "")
                 . "  └─ Thao tác: Gõ /duyet " . $order['order_code'] . " hoặc /tuchoi " . $order['order_code'] . "\n\n"
                 . "━━━━━━\n"
                 . "  └─ Nguồn: Website Nha Khoa Flora (Gói Dịch Vụ)\n"
                 . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
    } elseif ($event_type === 'proof_uploaded') {
        $title = "🔔 [ KHÁCH ĐÃ TẢI LÊN ẢNH BILL ] 🔔";
        $message = "🔔 [ KHÁCH ĐÃ TẢI LÊN ẢNH BILL ] 🔔\n"
                 . "━━━━━━\n"
                 . "Dịch vụ: Nha Khoa Flora - Xác Thực Chuyển Khoản\n"
                 . "Mã đơn: #" . $order['order_code'] . "\n\n"
                 . "👤 Thông Tin Khách Hàng:\n"
                 . "  ▸ Họ tên: " . $order['customer_name'] . "\n"
                 . "  ▸ Số ĐT: " . $order['customer_phone'] . "\n"
                 . "  └─ Gói đăng ký: " . $order['package_name'] . "\n"
                 . "  └─ Số tiền: " . $amount_fmt . "\n"
                 . "  └─ Link ảnh bill: " . $proof_img . "\n"
                 . "  └─ Thao tác: Gõ /duyet " . $order['order_code'] . " hoặc /tuchoi " . $order['order_code'] . "\n\n"
                 . "━━━━━━\n"
                 . "  └─ Nguồn: Website Nha Khoa Flora\n"
                 . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
    } elseif ($event_type === 'order_paid') {
        $operator_note = !empty($extra['operator']) ? $extra['operator'] : 'Ban Quản Trị Flora';
        $title = "🔔 [ ĐƠN HÀNG ĐÃ ĐƯỢC DUYỆT ] 🔔";
        $message = "🔔 [ ĐƠN HÀNG ĐÃ ĐƯỢC DUYỆT ] 🔔\n"
                 . "━━━━━━\n"
                 . "Dịch vụ: Nha Khoa Flora - Kích Hoạt Gói Khám\n"
                 . "Mã đơn: #" . $order['order_code'] . "\n\n"
                 . "👤 Thông Tin Khách Hàng:\n"
                 . "  ▸ Họ tên: " . $order['customer_name'] . "\n"
                 . "  ▸ Số ĐT: " . $order['customer_phone'] . "\n"
                 . "  └─ Gói kích hoạt: " . $order['package_name'] . "\n"
                 . "  └─ Thực thu ACB: " . $amount_fmt . "\n"
                 . "  └─ Trạng thái: Đã thanh toán thành công (PAID)\n"
                 . "  └─ Người duyệt: " . $operator_note . "\n\n"
                 . "━━━━━━\n"
                 . "  └─ Nguồn: Hệ Thống Flora\n"
                 . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
    } elseif ($event_type === 'order_rejected') {
        $reason = !empty($extra['reason']) ? $extra['reason'] : 'Chưa nhận được chuyển khoản';
        $operator_note = !empty($extra['operator']) ? $extra['operator'] : 'Ban Quản Trị Flora';
        $title = "🔔 [ ĐƠN HÀNG BỊ TỪ CHỐI ] 🔔";
        $message = "🔔 [ ĐƠN HÀNG BỊ TỪ CHỐI ] 🔔\n"
                 . "━━━━━━\n"
                 . "Dịch vụ: Nha Khoa Flora\n"
                 . "Mã đơn: #" . $order['order_code'] . "\n\n"
                 . "👤 Thông Tin Khách Hàng:\n"
                 . "  ▸ Họ tên: " . $order['customer_name'] . "\n"
                 . "  ▸ Số ĐT: " . $order['customer_phone'] . "\n"
                 . "  └─ Gói đăng ký: " . $order['package_name'] . "\n"
                 . "  └─ Trạng thái: Đã từ chối đơn\n"
                 . "  └─ Lý do: " . $reason . "\n"
                 . "  └─ Người từ chối: " . $operator_note . "\n\n"
                 . "━━━━━━\n"
                 . "  └─ Nguồn: Hệ Thống Flora\n"
                 . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
    }

    // 1. Gửi trực tiếp qua Zalo Bot Platform (nếu đã cấu hình Bot Token & Group Chat ID)
    if (!empty($bot_token) && !empty($group_chat_id)) {
        $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
        wp_remote_post($zalo_api_url, array(
            'method'      => 'POST',
            'timeout'     => 10,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => false,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'chat_id' => $group_chat_id,
                'text'    => $message
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    // 2. Gửi tiếp qua webhook trung gian tùy chỉnh nếu có cấu hình
    if (!empty($webhook_url)) {
        $payload = array(
            'event'                  => $event_type,
            'title'                  => $title,
            'message'                => $message,
            'text'                   => $message,
            'content'                => $message,
            'order_code'             => $order['order_code'],
            'customer_name'          => $order['customer_name'],
            'customer_phone'         => $order['customer_phone'],
            'package_name'           => $order['package_name'],
            'final_amount'           => (int)$order['final_amount'],
            'final_amount_formatted' => $amount_fmt,
            'transfer_syntax'        => $order['transfer_syntax'],
            'proof_image_url'        => $proof_img,
            'image'                  => $proof_img,
            'image_url'              => $proof_img,
            'admin_url'              => $admin_link,
            'created_at'             => current_time('mysql')
        );

        wp_remote_post($webhook_url, array(
            'method'      => 'POST',
            'timeout'     => 10,
            'redirection' => 5,
            'httpversion' => '1.1',
            'blocking'    => false, // Không chặn luồng frontend
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode($payload, JSON_UNESCAPED_UNICODE)
        ));
    }

    return true;
}

/**
 * Gửi thông báo Zalo khi có Data Lead mới (từ form đặt hẹn, tư vấn, Bác sĩ AI)
 * Thiết kế tối giản, sạch sẽ, ít icon theo đúng tiêu chuẩn
 */
function flora_send_zalo_lead_notification($lead) {
    $config = flora_get_payment_config();
    $bot_token     = trim($config['zalo_bot_token'] ?? '');
    $group_chat_id = trim($config['zalo_group_chat_id'] ?? '');
    $webhook_url   = trim($config['zalo_webhook_url'] ?? '');

    if (empty($bot_token) && empty($webhook_url)) {
        return false;
    }

    $name    = !empty($lead['name']) ? $lead['name'] : 'Khách hàng';
    $phone   = !empty($lead['phone']) ? $lead['phone'] : 'Chưa cung cấp';
    $email   = !empty($lead['email']) ? $lead['email'] : 'Chưa cung cấp';
    $service = !empty($lead['service']) ? $lead['service'] : 'Tư vấn nha khoa';
    $time    = !empty($lead['preferred_time']) ? $lead['preferred_time'] : 'Giờ hành chính';
    $source  = !empty($lead['source_page']) ? $lead['source_page'] : 'Website Flora';
    $notes   = !empty($lead['notes']) ? $lead['notes'] : (!empty($lead['note']) ? $lead['note'] : 'Khách cần tư vấn');

    $message = "🔔 [ CÓ DATA LEAD MỚI ] 🔔\n"
             . "━━━━━━\n"
             . "Dự án: Nha Khoa Flora - {$service}\n\n"
             . "👤 Thông Tin Khách Hàng:\n"
             . "  ▸ Họ tên: {$name}\n"
             . "  ▸ Số ĐT: {$phone}\n"
             . "  ▸ Email: {$email}\n"
             . "  └─ Nhu cầu: {$service}\n"
             . "  └─ Thời gian hẹn: {$time}\n"
             . "  └─ Chi tiết: {$notes}\n\n"
             . "━━━━━━\n"
             . "  └─ Nguồn: {$source}\n"
             . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');

    // 1. Gửi qua Zalo Bot Platform
    if (!empty($bot_token) && !empty($group_chat_id)) {
        $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
        wp_remote_post($zalo_api_url, array(
            'method'      => 'POST',
            'timeout'     => 10,
            'blocking'    => false,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'chat_id' => $group_chat_id,
                'text'    => $message
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    // 2. Gửi qua Webhook trung gian nếu có
    if (!empty($webhook_url)) {
        wp_remote_post($webhook_url, array(
            'method'      => 'POST',
            'timeout'     => 10,
            'blocking'    => false,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'event'          => 'lead_new',
                'title'          => '🔔 [ CÓ DATA LEAD MỚI ] 🔔',
                'text'           => $message,
                'customer_name'  => $name,
                'customer_phone' => $phone,
                'customer_email' => $email,
                'service'        => $service,
                'notes'          => $notes,
                'created_at'     => current_time('mysql')
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    return true;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 5. HỆ THỐNG EMAIL TỰ ĐỘNG CHUẨN HTML THƯƠNG HIỆU FLORA
 * ─────────────────────────────────────────────────────────────────────────────
 */

// Helper chung gửi Email HTML
function flora_send_branded_html_mail($to, $subject, $content_html) {
    if (empty($to) || !is_email($to)) {
        return false;
    }

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Nha Khoa Flora <support@floraclinic.vn>'
    );

    $full_html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . esc_html($subject) . '</title>
    </head>
    <body style="font-family: \'Segoe UI\', Tahoma, Arial, sans-serif; margin: 0; padding: 0; background-color: #f1f5f9; color: #0f172a; -webkit-font-smoothing: antialiased;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9; padding: 30px 10px;">
            <tr>
                <td align="center">
                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0, 51, 163, 0.08); border: 1px solid #e2e8f0; max-width: 100%;">
                        <!-- HEADER -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); padding: 28px 20px 24px; text-align: center;">
                                <img src="https://nhakhoaflora.com/wp-content/themes/flora-theme/assets/flora-logo-white.png" alt="NHA KHOA FLORA" width="220" style="display: block; margin: 0 auto 10px; border: 0; outline: none; text-decoration: none; width: 220px; max-width: 100%; height: auto;" />
                                <p style="color: #e0f2fe; margin: 0; font-size: 12px; text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600;">HỆ THỐNG NHA KHOA ÊM ÁI TIÊU CHUẨN THỤY SĨ</p>
                            </td>
                        </tr>
                        <!-- BODY -->
                        <tr>
                            <td style="padding: 32px 28px; background-color: #ffffff;">
                                ' . $content_html . '
                            </td>
                        </tr>
                        <!-- FOOTER -->
                        <tr>
                            <td style="background-color: #0f172a; color: #94a3b8; padding: 24px; text-align: center; font-size: 12px; line-height: 1.6;">
                                <strong style="color: #ffffff; font-size: 13px; display: block; margin-bottom: 6px;">CÔNG TY CỔ PHẦN FLORA DENTAL CARE</strong>
                                📍 326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh<br>
                                📞 Hotline CSKH 24/7: <a href="tel:02873058999" style="color: #38bdf8; text-decoration: none; font-weight: 700;">028 7305 8999</a> | Zalo: 0902 535 068<br>
                                🌐 Website: <a href="https://nhakhoaflora.com" target="_blank" style="color: #38bdf8; text-decoration: none;">nhakhoaflora.com</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    return @wp_mail($to, $subject, $full_html, $headers);
}

// EMAIL 1: Gửi ngay khi khách bấm "Tôi đã chuyển khoản" (Đang chờ xác nhận)
function flora_send_customer_email_awaiting($order) {
    if (empty($order['customer_email'])) return false;

    $amount_fmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
    $subject = "⏳ [Nha Khoa Flora] Đã tiếp nhận yêu cầu xác nhận gói dịch vụ #" . $order['order_code'];

    $content = '
    <div style="text-align: center; margin-bottom: 20px;">
        <span style="display: inline-block; background: #e0f2fe; color: #0284c7; padding: 6px 16px; border-radius: 9999px; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">
            ⏳ ĐANG CHỜ XÁC NHẬN TỪ FLORA
        </span>
        <h3 style="font-size: 1.45rem; color: #0f172a; margin: 10px 0 6px;">YÊU CẦU ĐÃ ĐƯỢC TIẾP NHẬN!</h3>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Cảm ơn Quý khách <strong>' . esc_html($order['customer_name']) . '</strong> đã tin chọn Nha Khoa Flora.</p>
    </div>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px;">
            <span style="color: #64748b;">Mã đơn hàng:</span>
            <strong style="color: #0033a3; font-family: monospace; font-size: 15px;">' . esc_html($order['order_code']) . '</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px;">
            <span style="color: #64748b;">Gói dịch vụ:</span>
            <strong style="color: #0f172a;">' . esc_html($order['package_name']) . '</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px;">
            <span style="color: #64748b;">Số tiền thanh toán:</span>
            <strong style="color: #0033a3; font-size: 16px;">' . $amount_fmt . '</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 14px;">
            <span style="color: #64748b;">Cú pháp chuyển khoản:</span>
            <strong style="color: #b45309; font-family: monospace;">' . esc_html($order['transfer_syntax']) . '</strong>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px;">
            <span style="color: #64748b;">Tài khoản nhận:</span>
            <strong style="color: #0f172a;">77779268 - ACB (CN HOA HUNG)</strong>
        </div>
    </div>

    <!-- CAM KẾT UY TÍN MINH BẠCH -->
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px; margin-bottom: 22px;">
        <h4 style="margin: 0 0 6px 0; color: #166534; font-size: 14px; font-weight: 700;">
            🛡️ CAM KẾT MINH BẠCH & BẢO ĐẢM QUYỀN LỢI 100%:
        </h4>
        <p style="margin: 0; font-size: 13px; color: #14532d; line-height: 1.55;">
            Tư vấn viên trực tiếp của Flora đang kiểm tra giao dịch đối soát qua tài khoản ngân hàng công ty ACB. Gói khám sẽ được kích hoạt trong vòng <strong>5 - 15 phút</strong>. Quý khách hoàn toàn yên tâm quyền lợi giao dịch luôn được đảm bảo tuyệt đối.
        </p>
    </div>

    <p style="font-size: 13px; color: #64748b; line-height: 1.6; text-align: center; margin: 0;">
        Quý khách có thể kiểm tra trạng thái đơn hàng trên website hoặc liên hệ hotline <strong style="color: #0033a3;">028 7305 8999</strong> để được hỗ trợ tức thì.
    </p>';

    return flora_send_branded_html_mail($order['customer_email'], $subject, $content);
}

// EMAIL 2: Gửi khi Tư vấn viên duyệt "ĐÃ THANH TOÁN" (Kích hoạt thành công)
function flora_send_customer_email_confirmed($order) {
    if (empty($order['customer_email'])) return false;

    $amount_fmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
    $subject = "🎉 [Nha Khoa Flora] Xác nhận kích hoạt thành công gói dịch vụ #" . $order['order_code'];

    $content = '
    <div style="text-align: center; margin-bottom: 22px;">
        <div style="width: 56px; height: 56px; line-height: 56px; border-radius: 50%; background: #dcfce7; color: #16a34a; font-size: 26px; margin: 0 auto 12px; text-align: center;">✓</div>
        <span style="display: inline-block; background: #dcfce7; color: #15803d; padding: 5px 16px; border-radius: 9999px; font-weight: 800; font-size: 12px; text-transform: uppercase;">
            XÁC NHẬN GIAO DỊCH THÀNH CÔNG
        </span>
        <h3 style="font-size: 1.5rem; color: #0f172a; margin: 8px 0 6px;">CHÚC MỪNG QUÝ KHÁCH!</h3>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Gói chăm sóc răng miệng điện tử của Quý khách đã chính thức được kích hoạt trên hệ thống Flora.</p>
    </div>

    <!-- HỒ SƠ GÓI KHÁM ĐIỆN TỬ -->
    <div style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1.5px solid #0493f1; border-radius: 14px; padding: 22px; margin-bottom: 22px;">
        <div style="font-size: 11px; font-weight: 800; color: #0033a3; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">THẺ GÓI DỊCH VỤ ĐIỆN TỬ FLORA</div>
        <div style="font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 12px;">' . esc_html($order['package_name']) . '</div>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Mã định danh hồ sơ:</td>
                <td style="padding: 6px 0; font-weight: 800; color: #0033a3; text-align: right; font-family: monospace;">' . esc_html($order['order_code']) . '</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Chủ sở hữu gói:</td>
                <td style="padding: 6px 0; font-weight: 700; color: #0f172a; text-align: right;">' . esc_html($order['customer_name']) . '</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Số điện thoại:</td>
                <td style="padding: 6px 0; font-weight: 700; color: #0f172a; text-align: right;">' . esc_html($order['customer_phone']) . '</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Số tiền đã thanh toán:</td>
                <td style="padding: 6px 0; font-weight: 800; color: #16a34a; text-align: right; font-size: 16px;">' . $amount_fmt . '</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #64748b;">Thời điểm xác nhận:</td>
                <td style="padding: 6px 0; color: #475569; text-align: right;">' . current_time('H:i d/m/Y') . '</td>
            </tr>
        </table>
    </div>

    <!-- HƯỚNG DẪN ĐẶT HẸN -->
    <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 16px; margin-bottom: 22px;">
        <h4 style="margin: 0 0 6px 0; color: #0369a1; font-size: 14px; font-weight: 700;">
            📅 BƯỚC TIẾP THEO ĐỂ SỬ DỤNG DỊCH VỤ:
        </h4>
        <p style="margin: 0; font-size: 13px; color: #0c4a6e; line-height: 1.55;">
            Chuyên viên chăm sóc khách hàng của Nha Khoa Flora sẽ gọi điện đến số <strong>' . esc_html($order['customer_phone']) . '</strong> trong ít phút để hoàn tất lưu trữ hồ sơ và sắp xếp lịch hẹn theo thời gian thuận tiện nhất cho Quý khách.
        </p>
    </div>

    <div style="text-align: center;">
        <a href="tel:02873058999" style="display: inline-block; background: #0033a3; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; font-size: 14px;">
            📞 Gọi Đặt Lịch Ưu Tiên: 028 7305 8999
        </a>
    </div>';

    return flora_send_branded_html_mail($order['customer_email'], $subject, $content);
}

// EMAIL 3: Gửi khi Tư vấn viên từ chối HOẶC quá 24h chưa nhận được chuyển khoản (KÈM KÊNH KHIẾU NẠI)
function flora_send_customer_email_rejected($order, $reason = '') {
    if (empty($order['customer_email'])) return false;

    if (empty($reason)) {
        $reason = 'Chưa nhận được giao dịch chuyển khoản vào tài khoản ngân hàng sau thời gian đối soát.';
    }

    $amount_fmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
    $subject = "⚠️ [Nha Khoa Flora] Thông báo trạng thái gói dịch vụ #" . $order['order_code'];

    $content = '
    <div style="text-align: center; margin-bottom: 20px;">
        <span style="display: inline-block; background: #fee2e2; color: #dc2626; padding: 5px 16px; border-radius: 9999px; font-weight: 800; font-size: 12px; text-transform: uppercase;">
            THÔNG BÁO TRẠNG THÁI ĐƠN HÀNG
        </span>
        <h3 style="font-size: 1.4rem; color: #0f172a; margin: 10px 0 6px;">ĐƠN HÀNG CHƯA ĐƯỢC XÁC NHẬN</h3>
        <p style="color: #64748b; font-size: 14px; margin: 0;">Kính gửi Quý khách <strong>' . esc_html($order['customer_name']) . '</strong>,</p>
    </div>

    <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 16px;">
        Hệ thống Nha Khoa Flora xin thông báo: Đơn hàng <strong>#' . esc_html($order['order_code']) . '</strong> đăng ký <strong>' . esc_html($order['package_name']) . '</strong> (' . $amount_fmt . ') hiện tại chưa thể kích hoạt thành công.
    </p>

    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 14px 18px; margin-bottom: 22px;">
        <strong style="color: #991b1b; font-size: 13px; display: block; margin-bottom: 4px;">LÝ DO TỪ CHỐI / HỦY:</strong>
        <span style="color: #7f1d1d; font-size: 13px;">' . esc_html($reason) . '</span>
    </div>

    <!-- CAM KẾT BẢO VỆ QUYỀN LỢI & KÊNH KHIẾU NẠI MINH BẠCH -->
    <div style="background: #eff6ff; border: 1.5px solid #3b82f6; border-radius: 12px; padding: 18px 20px; margin-bottom: 22px;">
        <h4 style="margin: 0 0 8px 0; color: #1e40af; font-size: 14px; font-weight: 700;">
            🛡️ BẢO VỆ QUYỀN LỢI KHÁCH HÀNG & KÊNH KHIẾU NẠI 24/7:
        </h4>
        <p style="margin: 0 0 10px 0; font-size: 13px; color: #1e3a8a; line-height: 1.55;">
            Nếu Quý khách <strong>ĐÃ BỊ TRỪ TIỀN</strong> tại ứng dụng ngân hàng hoặc đã chuyển khoản thành công, Quý khách <strong>HOÀN TOÀN YÊN TÂM</strong>: Nha khoa Flora cam kết <strong>HOÀN TIỀN 100%</strong> hoặc kích hoạt gói dịch vụ thủ công ngay lập tức!
        </p>
        <p style="margin: 0; font-size: 13px; color: #1e3a8a; line-height: 1.55;">
            Vui lòng liên hệ với Bộ phận Kiểm soát Tài chính Flora qua các kênh ưu tiên sau:
        </p>
        <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 13px; color: #1e3a8a; line-height: 1.6;">
            <li><strong>Hotline khiếu nại (24/7):</strong> <a href="tel:02873058999" style="color: #0033a3; font-weight: 700;">028 7305 8999</a> (Bấm phím 1)</li>
            <li><strong>Zalo hỗ trợ trực tiếp:</strong> <a href="https://zalo.me/0902535068" target="_blank" style="color: #0033a3; font-weight: 700;">0902 535 068</a> (Gửi ảnh chụp biên lai ngân hàng)</li>
            <li><strong>Email tiếp nhận khiếu nại:</strong> <a href="mailto:support@floraclinic.vn" style="color: #0033a3; font-weight: 700;">support@floraclinic.vn</a></li>
        </ul>
    </div>

    <p style="font-size: 13px; color: #64748b; line-height: 1.6; text-align: center; margin: 0;">
        Nha khoa Flora chân thành xin lỗi vì sự bất tiện này và sẵn sàng hỗ trợ Quý khách giải quyết nhanh nhất!
    </p>';

    return flora_send_branded_html_mail($order['customer_email'], $subject, $content);
}

// Thông báo Email cho Quản trị viên
function flora_notify_admin_order_created($data) {
    $admin_email = get_option('admin_email');
    $subject = "[Đơn Chờ Duyệt] #" . $data['order_code'] . " - " . $data['customer_name'] . " (" . number_format($data['final_amount'], 0, ',', '.') . "đ)";
    $body = "HỆ THỐNG NHA KHOA FLORA TIẾP NHẬN YÊU CẦU XÁC NHẬN CHUYỂN KHOẢN:\n\n"
          . "Mã đơn hàng: " . $data['order_code'] . "\n"
          . "Khách hàng: " . $data['customer_name'] . "\n"
          . "Số điện thoại: " . $data['customer_phone'] . "\n"
          . "Gói dịch vụ: " . $data['package_name'] . "\n"
          . "Số tiền thanh toán: " . number_format($data['final_amount'], 0, ',', '.') . " VNĐ\n"
          . "Cú pháp chuyển khoản: " . $data['transfer_syntax'] . "\n"
          . "Tài khoản nhận: ACB - 77779268 (CN HOA HUNG)\n"
          . "Thời gian tạo: " . current_time('d/m/Y H:i:s') . "\n\n"
          . "Duyệt hoặc từ chối đơn tại:\n"
          . admin_url('admin.php?page=flora-orders&s=' . urlencode($data['order_code']));
    @wp_mail($admin_email, $subject, $body);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 6. TẠO ĐƠN HÀNG KHI KHÁCH BẤM "TÔI ĐÃ CHUYỂN KHOẢN" (AJAX ENDPOINT)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_ajax_create_order() {
    $raw_input = file_get_contents('php://input');
    $json_data = json_decode($raw_input, true) ?: array();

    $name            = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : (isset($json_data['name']) ? sanitize_text_field($json_data['name']) : '');
    $raw_phone       = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : (isset($json_data['phone']) ? sanitize_text_field($json_data['phone']) : '');
    $email           = isset($_POST['email']) ? sanitize_email($_POST['email']) : (isset($json_data['email']) ? sanitize_email($json_data['email']) : '');
    $package_id      = isset($_POST['package_id']) ? sanitize_text_field($_POST['package_id']) : (isset($json_data['package_id']) ? sanitize_text_field($json_data['package_id']) : 'careplus');
    $voucher_code    = isset($_POST['voucher_code']) ? sanitize_text_field($_POST['voucher_code']) : (isset($json_data['voucher_code']) ? sanitize_text_field($json_data['voucher_code']) : '');
    $need_consult    = isset($_POST['need_consult']) ? (int)$_POST['need_consult'] : (isset($json_data['need_consult']) ? (int)$json_data['need_consult'] : 1);
    $idempotency_key = isset($_POST['idempotency_key']) ? sanitize_text_field($_POST['idempotency_key']) : (isset($json_data['idempotency_key']) ? sanitize_text_field($json_data['idempotency_key']) : '');
    $proof_image_url = isset($_POST['proof_image_url']) ? esc_url_raw($_POST['proof_image_url']) : '';

    if (empty($name) || mb_strlen($name) < 2) {
        wp_send_json_error(array('field' => 'name', 'message' => 'Vui lòng nhập đầy đủ Họ và tên người sở hữu gói dịch vụ.'));
    }

    $clean_phone = flora_validate_vn_phone($raw_phone);
    if (!$clean_phone) {
        wp_send_json_error(array('field' => 'phone', 'message' => 'Số điện thoại không hợp lệ! Vui lòng nhập đúng 10 số (bắt đầu bằng 03, 05, 07, 08, 09).'));
    }

    if (empty($idempotency_key)) {
        $idempotency_key = 'FLORA-IK-' . md5($clean_phone . '_' . $package_id . '_' . date('YmdH'));
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();

    // Kiểm tra Idempotency chống trùng lặp
    $existing_order = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table_name WHERE idempotency_key = %s LIMIT 1",
        $idempotency_key
    ), ARRAY_A);

    if ($existing_order) {
        // Cập nhật trạng thái sang pending_confirmation nếu trước đó chưa bấm
        if ($existing_order['payment_status'] === 'pending') {
            $wpdb->update(
                $table_name,
                array(
                    'payment_status'          => 'pending_confirmation',
                    'user_confirmed_transfer' => 1,
                    'user_confirmed_at'       => current_time('mysql'),
                    'proof_image_url'         => !empty($proof_image_url) ? $proof_image_url : $existing_order['proof_image_url']
                ),
                array('id' => $existing_order['id'])
            );
            $existing_order['payment_status'] = 'pending_confirmation';
        }

        wp_send_json_success(array(
            'is_duplicate'     => true,
            'order_id'         => $existing_order['id'],
            'order_code'       => $existing_order['order_code'],
            'transfer_syntax'  => $existing_order['transfer_syntax'],
            'customer_phone'   => $existing_order['customer_phone'],
            'customer_name'    => $existing_order['customer_name'],
            'package_name'     => $existing_order['package_name'],
            'final_amount'     => (int)$existing_order['final_amount'],
            'final_amount_fmt' => number_format($existing_order['final_amount'], 0, ',', '.') . ' VNĐ',
            'payment_status'   => $existing_order['payment_status']
        ));
    }

    // Định nghĩa gói khám
    $packages_def = array(
        'careplus' => array(
            'name'           => 'GÓI CARE PLUS (Gia đình đến 4 người)',
            'original_price' => 2500000,
            'base_price'     => 999000
        ),
        'whiteup' => array(
            'name'           => 'GÓI FLORA WHITE UP (Tẩy trắng chuyên sâu)',
            'original_price' => 3000000,
            'base_price'     => 1999000
        )
    );

    if (!isset($packages_def[$package_id])) {
        $package_id = 'careplus';
    }

    $pkg_info = $packages_def[$package_id];
    $original_price = $pkg_info['original_price'];

    // Tiêu chuẩn mức giảm Flora khi có mã / ref
    $std_discount = ($package_id === 'whiteup') ? 1001000 : 1501000;

    // Kiểm tra mã voucher hoặc ref_code
    $v_code = strtoupper(trim($voucher_code));
    $ref_code = isset($_POST['ref_code']) ? strtoupper(trim(sanitize_text_field($_POST['ref_code']))) : '';
    if (empty($ref_code) && isset($_COOKIE['flora_kol_ref'])) {
        $ref_code = strtoupper(trim(sanitize_text_field($_COOKIE['flora_kol_ref'])));
    }

    $active_code = !empty($v_code) ? $v_code : $ref_code;
    $total_discount = 0;
    $voucher_id = 0;
    $voucher_kol = null;

    if (!empty($active_code) && function_exists('flora_voucher_validate')) {
        $val_res = flora_voucher_validate($active_code, $original_price, $clean_phone, $package_id);
        if ($val_res['valid']) {
            if (!empty($val_res['voucher'])) {
                $voucher_id = (int)$val_res['voucher']['id'];
                $total_discount = (int)$val_res['discount_amount'];
            } else {
                // Mã Ref đối tác KOL
                $total_discount = $std_discount;
            }
            if (!empty($val_res['affiliate'])) {
                $voucher_kol = $val_res['affiliate'];
            }
        }
    } elseif (!empty($active_code)) {
        if ($active_code === 'FLORA' || $active_code === 'FLORA50' || $active_code === 'TRIAN50') {
            $total_discount = $std_discount;
        } elseif ($active_code === 'FLORA100') {
            $total_discount = $std_discount;
        }
    }

    // Nếu không có mã hoặc ref hợp lệ -> Thanh toán theo GIÁ GỐC
    $final_amount = max(0, $original_price - $total_discount);

    // Sinh mã đơn hàng
    $unique_suffix = rand(10000, 99999);
    $order_code = 'FLORA' . $unique_suffix;

    $tries = 0;
    while ($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE order_code = %s", $order_code)) > 0 && $tries < 5) {
        $unique_suffix = rand(10000, 99999);
        $order_code = 'FLORA' . $unique_suffix;
        $tries++;
    }

    // KOL / Affiliate tracking
    $kol = $voucher_kol;
    if (!$kol && !empty($ref_code) && function_exists('flora_affiliate_get_by_ref')) {
        $kol = flora_affiliate_get_by_ref($ref_code);
    }

    $affiliate_id = $kol ? (int)$kol['id'] : 0;
    $affiliate_code = $kol ? $kol['ref_code'] : '';
    $commission_amount = $kol ? flora_affiliate_calculate_commission($kol, $final_amount) : 0;

    // Cú pháp chuyển khoản chứa SĐT xác thực
    if (!empty($affiliate_code)) {
        $transfer_syntax = 'FLORA ' . $affiliate_code . ' ' . $clean_phone . ' ' . $unique_suffix;
    } else {
        $transfer_syntax = 'FLORA ' . $clean_phone . ' ' . $unique_suffix;
    }

    $ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(substr($_SERVER['HTTP_USER_AGENT'], 0, 500)) : '';

    // LƯU ĐƠN HÀNG VỚI TRẠNG THÁI PENDING_CONFIRMATION (ĐANG CHỜ FLORA XÁC NHẬN)
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'order_code'              => $order_code,
            'idempotency_key'         => $idempotency_key,
            'customer_name'           => $name,
            'customer_phone'          => $clean_phone,
            'customer_email'          => $email,
            'need_consult'            => $need_consult,
            'package_id'              => $package_id,
            'package_name'            => $pkg_info['name'],
            'original_price'          => $original_price,
            'discount_amount'         => $total_discount,
            'final_amount'            => $final_amount,
            'voucher_code'            => $v_code,
            'affiliate_code'          => $affiliate_code,
            'affiliate_id'            => $affiliate_id,
            'commission_amount'       => $commission_amount,
            'commission_status'       => 'pending',
            'transfer_syntax'         => $transfer_syntax,
            'payment_method'          => 'acb_vietqr',
            'payment_status'          => 'pending_confirmation',
            'proof_image_url'         => $proof_image_url,
            'user_confirmed_transfer' => 1,
            'user_confirmed_at'       => current_time('mysql'),
            'ip_address'              => $ip_address,
            'user_agent'              => $user_agent,
            'created_at'              => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s')
    );

    if ($inserted === false) {
        wp_send_json_error(array('message' => 'Lỗi kết nối lưu đơn hàng. Vui lòng liên hệ Hotline 028 7305 8999 để được hỗ trợ tức thì!'));
    }

    $order_id = $wpdb->insert_id;
    $order_data = array(
        'id'               => $order_id,
        'order_code'       => $order_code,
        'customer_name'    => $name,
        'customer_phone'   => $clean_phone,
        'customer_email'   => $email,
        'package_name'     => $pkg_info['name'],
        'final_amount'     => $final_amount,
        'transfer_syntax'  => $transfer_syntax,
        'affiliate_code'   => $affiliate_code,
        'proof_image_url'  => $proof_image_url
    );

    // Ghi nhận voucher
    if ($voucher_id > 0 && function_exists('flora_voucher_record_usage')) {
        flora_voucher_record_usage($voucher_id, $v_code, $order_id, $order_code, $clean_phone, $name, $base_price, $voucher_discount, $final_amount, $affiliate_id);
    }

    // Tăng tổng đơn KOL
    if ($affiliate_id > 0) {
        $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
        $wpdb->query($wpdb->prepare("UPDATE $aff_table SET total_orders = total_orders + 1 WHERE id = %d", $affiliate_id));
    }

    // Gửi ngay phản hồi HTTP 200 JSON cho client để giao diện chuyển tức thì (< 100ms)
    $response_payload = array(
        'success' => true,
        'data'    => array(
            'order_id'         => $order_id,
            'order_code'       => $order_code,
            'transfer_syntax'  => $transfer_syntax,
            'customer_name'    => $name,
            'customer_phone'   => $clean_phone,
            'package_name'     => $pkg_info['name'],
            'final_amount'     => $final_amount,
            'final_amount_fmt' => number_format($final_amount, 0, ',', '.') . ' VNĐ',
            'bank_account'     => '77779268',
            'bank_owner'       => 'CONG TY CO PHAN FLORA DENTAL CARE',
            'bank_code'        => 'ACB',
            'bank_branch'      => 'ACB - CN HOA HUNG',
            'payment_status'   => 'pending_confirmation'
        )
    );

    $json_output = wp_json_encode($response_payload);

    // Xóa sạch buffer trước đó để LiteSpeed đóng kết nối ngay lập tức
    while (ob_get_level() > 0) {
        @ob_end_clean();
    }

    header('Content-Type: application/json; charset=UTF-8');
    header('Content-Length: ' . strlen($json_output));
    header('Connection: close');
    echo $json_output;

    if (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
    } elseif (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        ignore_user_abort(true);
        flush();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CÁC TÁC VỤ EMAIL & THÔNG BÁO CHẠY NGẦM SAU KHI CLIENT ĐÃ NHẬN KẾT QUẢ
    // ─────────────────────────────────────────────────────────────────────────
    // 1. Gửi Email 1 cho Khách hàng (Đang chờ xác nhận)
    if (!empty($email)) {
        flora_send_customer_email_awaiting($order_data);
    }

    // 2. Bắn Realtime Webhook đến Zalo Bot / Nhóm CSKH
    flora_send_zalo_notification($order_data, 'order_awaiting');

    // 3. Gửi Email cho Quản trị viên
    flora_notify_admin_order_created($order_data);

    exit;
}
add_action('wp_ajax_flora_create_order', 'flora_ajax_create_order');
add_action('wp_ajax_nopriv_flora_create_order', 'flora_ajax_create_order');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 7. TẢI LÊN ẢNH MINH CHỨNG CHUYỂN KHOẢN (AJAX ENDPOINT)
 * ─────────────────────────────────────────────────────────────────────────────
 */
/**
 * Nén hình ảnh bill thành định dạng WebP chất lượng cao, tối ưu dung lượng trước khi lưu
 */
function flora_compress_and_save_webp($source_filepath, $quality = 80, $max_dim = 1600) {
    if (!file_exists($source_filepath) || !function_exists('imagewebp')) {
        return $source_filepath;
    }

    $info = @getimagesize($source_filepath);
    if (!$info) return $source_filepath;

    $mime = $info['mime'];
    $src = null;
    switch ($mime) {
        case 'image/jpeg':
            $src = @imagecreatefromjpeg($source_filepath);
            break;
        case 'image/png':
            $src = @imagecreatefrompng($source_filepath);
            break;
        case 'image/webp':
            $src = @imagecreatefromwebp($source_filepath);
            break;
        case 'image/gif':
            $src = @imagecreatefromgif($source_filepath);
            break;
        default:
            return $source_filepath;
    }

    if (!$src) return $source_filepath;

    $w = imagesx($src);
    $h = imagesy($src);

    $new_w = $w;
    $new_h = $h;
    if ($w > $max_dim || $h > $max_dim) {
        if ($w > $h) {
            $new_h = (int)round(($h * $max_dim) / $w);
            $new_w = $max_dim;
        } else {
            $new_w = (int)round(($w * $max_dim) / $h);
            $new_h = $max_dim;
        }
    }

    $dst = imagecreatetruecolor($new_w, $new_h);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $new_w, $new_h, $w, $h);

    $webp_filepath = preg_replace('/\.[^.]+$/', '.webp', $source_filepath);
    if ($webp_filepath === $source_filepath) {
        $webp_filepath = $source_filepath . '.webp';
    }

    $saved = @imagewebp($dst, $webp_filepath, $quality);
    imagedestroy($src);
    imagedestroy($dst);

    if ($saved && file_exists($webp_filepath)) {
        if ($webp_filepath !== $source_filepath && file_exists($source_filepath)) {
            @unlink($source_filepath);
        }
        return $webp_filepath;
    }

    return $source_filepath;
}

function flora_ajax_upload_payment_proof() {
    $order_code = isset($_POST['order_code']) ? sanitize_text_field($_POST['order_code']) : '';
    if (empty($order_code)) {
        wp_send_json_error(array('message' => 'Thiếu mã đơn hàng.'));
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();
    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE order_code = %s LIMIT 1", $order_code), ARRAY_A);

    if (!$order) {
        wp_send_json_error(array('message' => 'Không tìm thấy đơn hàng.'));
    }

    $image_url = '';

    // Hỗ trợ File Upload thông thường
    if (!empty($_FILES['proof_image']) && !empty($_FILES['proof_image']['tmp_name'])) {
        $file = $_FILES['proof_image'];

        // Kiểm tra dung lượng (Max 15MB)
        if ($file['size'] > 15 * 1024 * 1024) {
            wp_send_json_error(array('message' => 'Ảnh quá lớn! Vui lòng chọn ảnh dưới 15MB.'));
        }

        require_once(ABSPATH . 'wp-admin/includes/file.php');
        $upload_overrides = array('test_form' => false);
        $uploaded = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded['error'])) {
            wp_send_json_error(array('message' => 'Không thể tải ảnh: ' . $uploaded['error']));
        }

        // Tự động nén sang WebP 80% chất lượng cao & giảm dung lượng siêu tốc
        $optimized_filepath = flora_compress_and_save_webp($uploaded['file'], 80, 1600);
        $upload_dir = wp_upload_dir();
        $image_url = $upload_dir['url'] . '/' . basename($optimized_filepath);
    }
    // Hỗ trợ Base64 Image
    elseif (!empty($_POST['proof_base64'])) {
        $b64 = $_POST['proof_base64'];
        if (preg_match('/^data:image\/(\w+);base64,/', $b64, $type)) {
            $data = substr($b64, strpos($b64, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, array('jpg', 'jpeg', 'gif', 'png', 'webp'))) {
                wp_send_json_error(array('message' => 'Định dạng ảnh không hợp lệ.'));
            }
            $data = base64_decode($data);
            if ($data === false) {
                wp_send_json_error(array('message' => 'Lỗi giải mã ảnh base64.'));
            }
            $filename = 'proof_' . $order_code . '_' . time() . '.' . $type;
            $upload_dir = wp_upload_dir();
            $filepath = $upload_dir['path'] . '/' . $filename;
            file_put_contents($filepath, $data);

            // Nén sang WebP
            $optimized_filepath = flora_compress_and_save_webp($filepath, 80, 1600);
            $image_url = $upload_dir['url'] . '/' . basename($optimized_filepath);
        }
    }

    if (empty($image_url)) {
        wp_send_json_error(array('message' => 'Vui lòng chọn hình ảnh biên lai chụp màn hình.'));
    }

    // Cập nhật Database
    $wpdb->update(
        $table_name,
        array(
            'proof_image_url' => $image_url,
            'updated_at'      => current_time('mysql')
        ),
        array('order_code' => $order_code)
    );

    $order['proof_image_url'] = $image_url;

    // Bắn Webhook cập nhật Zalo Bot
    flora_send_zalo_notification($order, 'proof_uploaded', array('proof_image_url' => $image_url));

    wp_send_json_success(array(
        'message'   => 'Đã tải lên ảnh minh chứng thành công! Tư vấn viên Flora sẽ đối soát ngay.',
        'image_url' => $image_url
    ));
}
add_action('wp_ajax_flora_upload_payment_proof', 'flora_ajax_upload_payment_proof');
add_action('wp_ajax_nopriv_flora_upload_payment_proof', 'flora_ajax_upload_payment_proof');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 8. TRA CỨU ĐƠN HÀNG THEO MÃ HOẶC SỐ ĐIỆN THOẠI (AJAX ENDPOINT)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_ajax_lookup_order() {
    $query = isset($_GET['query']) ? sanitize_text_field($_GET['query']) : (isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '');
    $query = trim($query);

    if (empty($query)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập Mã đơn hàng hoặc Số điện thoại.'));
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();

    $clean_digits = preg_replace('/[^0-9]/', '', $query);
    if (strlen($clean_digits) >= 9) {
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE customer_phone = %s OR customer_phone LIKE %s ORDER BY created_at DESC LIMIT 1",
            $clean_digits,
            '%' . $wpdb->esc_like($clean_digits)
        ), ARRAY_A);
    } else {
        $order = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE order_code = %s OR order_code LIKE %s ORDER BY created_at DESC LIMIT 1",
            $query,
            '%' . $wpdb->esc_like($query)
        ), ARRAY_A);
    }

    if (!$order) {
        wp_send_json_error(array('message' => 'Không tìm thấy thông tin đơn hàng với: ' . esc_html($query)));
    }

    $status_label = 'Đang chờ xác nhận từ Flora';
    $status_badge_class = 'badge-pending';
    if ($order['payment_status'] === 'paid') {
        $status_label = 'Đã thanh toán thành công';
        $status_badge_class = 'badge-paid';
    } elseif ($order['payment_status'] === 'rejected') {
        $status_label = 'Đã từ chối / Hết hạn 24 giờ';
        $status_badge_class = 'badge-failed';
    } elseif ($order['payment_status'] === 'cancelled') {
        $status_label = 'Đã hủy';
        $status_badge_class = 'badge-cancelled';
    }

    wp_send_json_success(array(
        'order_code'          => $order['order_code'],
        'customer_name'       => $order['customer_name'],
        'customer_phone_mask' => substr($order['customer_phone'], 0, 4) . '***' . substr($order['customer_phone'], -3),
        'customer_phone'      => $order['customer_phone'],
        'package_name'        => $order['package_name'],
        'final_amount'        => (int)$order['final_amount'],
        'final_amount_fmt'    => number_format($order['final_amount'], 0, ',', '.') . ' VNĐ',
        'transfer_syntax'     => $order['transfer_syntax'],
        'payment_status'      => $order['payment_status'],
        'status_label'        => $status_label,
        'status_badge_class'  => $status_badge_class,
        'proof_image_url'     => $order['proof_image_url'],
        'reject_reason'       => $order['reject_reason'],
        'created_at'          => date('H:i d/m/Y', strtotime($order['created_at'])),
        'paid_at'             => !empty($order['paid_at']) ? date('H:i d/m/Y', strtotime($order['paid_at'])) : ''
    ));
}
add_action('wp_ajax_flora_lookup_order', 'flora_ajax_lookup_order');
add_action('wp_ajax_nopriv_flora_lookup_order', 'flora_ajax_lookup_order');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 9. POLLING TRẠNG THÁI ĐƠN HÀNG THEO THỜI GIAN THỰC
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_ajax_check_order_status() {
    $order_code = isset($_GET['order_code']) ? sanitize_text_field($_GET['order_code']) : (isset($_POST['order_code']) ? sanitize_text_field($_POST['order_code']) : '');

    if (empty($order_code)) {
        wp_send_json_error(array('message' => 'Thiếu mã đơn hàng.'));
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();

    $order = $wpdb->get_row($wpdb->prepare(
        "SELECT id, order_code, payment_status, paid_at, rejected_at, reject_reason, proof_image_url FROM $table_name WHERE order_code = %s LIMIT 1",
        $order_code
    ), ARRAY_A);

    if (!$order) {
        wp_send_json_error(array('message' => 'Không tìm thấy đơn hàng.'));
    }

    wp_send_json_success(array(
        'order_code'      => $order['order_code'],
        'payment_status'  => $order['payment_status'],
        'is_paid'         => ($order['payment_status'] === 'paid'),
        'is_rejected'     => ($order['payment_status'] === 'rejected'),
        'reject_reason'   => $order['reject_reason'],
        'proof_image_url' => $order['proof_image_url'],
        'paid_at'         => $order['paid_at']
    ));
}
add_action('wp_ajax_flora_check_order_status', 'flora_ajax_check_order_status');
add_action('wp_ajax_nopriv_flora_check_order_status', 'flora_ajax_check_order_status');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 10. TỰ ĐỘNG QUÉT VÀ HỦY ĐƠN QUÁ 24 GIỜ CHƯA XÁC NHẬN (CRON JOB)
 * ─────────────────────────────────────────────────────────────────────────────
 */
add_action('flora_cron_hourly_check_expired_orders', 'flora_check_expired_orders_cron');
if (!wp_next_scheduled('flora_cron_hourly_check_expired_orders')) {
    wp_schedule_event(time(), 'hourly', 'flora_cron_hourly_check_expired_orders');
}

function flora_check_expired_orders_cron() {
    global $wpdb;
    $table_name = flora_get_orders_table_name();

    // Tìm các đơn còn ở pending hoặc pending_confirmation tạo cách đây quá 24h
    $expired_orders = $wpdb->get_results(
        "SELECT * FROM $table_name 
         WHERE payment_status IN ('pending', 'pending_confirmation') 
         AND created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)",
        ARRAY_A
    );

    if (!empty($expired_orders)) {
        foreach ($expired_orders as $order) {
            $reason = 'Tự động hủy: Đã quá 24 giờ kể từ khi tạo yêu cầu nhưng chưa nhận được xác nhận thanh toán.';
            $wpdb->update(
                $table_name,
                array(
                    'payment_status' => 'rejected',
                    'rejected_at'    => current_time('mysql'),
                    'reject_reason'  => $reason
                ),
                array('id' => $order['id'])
            );

            // Gửi email từ chối / hướng dẫn khiếu nại
            flora_send_customer_email_rejected($order, $reason);

            // Bắn Zalo webhook cảnh báo
            flora_send_zalo_notification($order, 'order_rejected', array('reason' => $reason));
        }
    }
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 10.1. HÀM XỬ LÝ DUYỆT ĐƠN HÀNG TOÀN DIỆN (Core Approval Engine)
 * Sử dụng chung cho: WP-Admin, Zalo Bot Webhook, Cron, hoặc API
 * ─────────────────────────────────────────────────────────────────────────────
 */
/**
 * Trích xuất người xử lý và thời gian từ lịch sử notes của đơn hàng
 */
function flora_extract_operator_from_order_notes($notes, $action = 'approve') {
    if (empty($notes)) return 'Ban Quản Trị / TVV';
    if ($action === 'approve') {
        if (preg_match('/(?:Duyệt|Khôi phục & Duyệt lại) bởi\s+([^|]+?)\s+lúc/iu', $notes, $m)) {
            return trim($m[1]);
        }
    } else {
        if (preg_match('/Từ chối bởi\s+([^|]+?)\s+lúc/iu', $notes, $m)) {
            return trim($m[1]);
        }
    }
    return 'Ban Quản Trị / TVV';
}

function flora_approve_order($order_id_or_code, $operator_name = 'Admin', $force_reapprove = false) {
    global $wpdb;
    $table_name = flora_get_orders_table_name();

    $order = null;
    if (is_numeric($order_id_or_code)) {
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d LIMIT 1", (int)$order_id_or_code), ARRAY_A);
    } else {
        $clean_code = strtoupper(trim(trim($order_id_or_code), ' "#\':;.,<>[]()'));
        // 1. Tìm chính xác
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE UPPER(order_code) = %s LIMIT 1", $clean_code), ARRAY_A);
        // 2. Thử thêm tiền tố FLORA-
        if (!$order && stripos($clean_code, 'FLORA') === false) {
            $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE UPPER(order_code) = %s LIMIT 1", 'FLORA-' . $clean_code), ARRAY_A);
        }
        // 3. Tìm không phân biệt dấu gạch ngang
        if (!$order) {
            $no_dash = str_replace('-', '', $clean_code);
            $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE REPLACE(UPPER(order_code), '-', '') = %s LIMIT 1", $no_dash), ARRAY_A);
        }
    }

    if (!$order) {
        return array('success' => false, 'message' => 'Không tìm thấy đơn hàng với mã: ' . $order_id_or_code);
    }

    // 1. Kiểm tra nếu đơn ĐÃ ĐƯỢC DUYỆT THANH TOÁN rồi
    if ($order['payment_status'] === 'paid') {
        $prev_operator = flora_extract_operator_from_order_notes($order['notes'] ?? '', 'approve');
        $paid_time_fmt = !empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : 'trước đó';
        return array(
            'success'       => false,
            'already_paid'  => true,
            'operator'      => $prev_operator,
            'paid_time'     => $paid_time_fmt,
            'message'       => 'Đơn hàng #' . $order['order_code'] . ' ĐÃ ĐƯỢC DUYỆT THANH TOÁN TRƯỚC ĐÓ lúc ' . $paid_time_fmt . ' bởi ' . $prev_operator . '. Không cần duyệt lại!',
            'order'         => $order
        );
    }

    // 2. Kiểm tra nếu đơn ĐÃ BỊ TỪ CHỐI trước đó mà chưa có cờ ép buộc duyệt lại
    $is_recovering = false;
    if ($order['payment_status'] === 'rejected') {
        if (!$force_reapprove) {
            $prev_operator = flora_extract_operator_from_order_notes($order['notes'] ?? '', 'reject');
            $reject_time_fmt = !empty($order['rejected_at']) ? date('d/m/Y H:i:s', strtotime($order['rejected_at'])) : 'trước đó';
            $reject_reason = !empty($order['reject_reason']) ? $order['reject_reason'] : 'Không ghi rõ lý do';
            return array(
                'success'          => false,
                'already_rejected' => true,
                'operator'         => $prev_operator,
                'rejected_time'    => $reject_time_fmt,
                'reject_reason'    => $reject_reason,
                'message'          => 'Đơn hàng #' . $order['order_code'] . ' ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ lúc ' . $reject_time_fmt . ' bởi ' . $prev_operator . ' (Lý do: ' . $reject_reason . '). Để khôi phục & duyệt lại, vui lòng dùng lệnh /duyetlai ' . $order['order_code'] . ' hoặc thao tác tại WP-Admin.',
                'order'            => $order
            );
        } else {
            $is_recovering = true;
        }
    }

    $now = current_time('mysql');
    if ($is_recovering) {
        $log_note = ' | Khôi phục & Duyệt lại bởi ' . esc_html($operator_name) . ' lúc ' . current_time('d/m/Y H:i:s') . ' (Trước đó bị từ chối lúc ' . ($order['rejected_at'] ?? 'N/A') . ')';
    } else {
        $log_note = ' | Duyệt bởi ' . esc_html($operator_name) . ' lúc ' . current_time('d/m/Y H:i:s');
    }
    $new_notes = trim(($order['notes'] ?? '') . $log_note);

    $updated = $wpdb->update(
        $table_name,
        array(
            'payment_status' => 'paid',
            'paid_at'        => $now,
            'notes'          => $new_notes
        ),
        array('id' => $order['id'])
    );

    if ($updated === false) {
        return array('success' => false, 'message' => 'Lỗi cập nhật trạng thái đơn trong cơ sở dữ liệu.');
    }

    $order['payment_status'] = 'paid';
    $order['paid_at']        = $now;
    $order['notes']          = $new_notes;

    // 1. Kích hoạt hoa hồng KOL Affiliate
    if (function_exists('flora_affiliate_mark_order_paid')) {
        flora_affiliate_mark_order_paid($order['id']);
    }

    // 2. Cập nhật thống kê Voucher
    if (function_exists('flora_voucher_update_stats_on_order_paid')) {
        flora_voucher_update_stats_on_order_paid($order['order_code'], $order['final_amount']);
    }

    // 3. Gửi Email 2 cho Khách hàng
    $email_sent = false;
    if (function_exists('flora_send_customer_email_confirmed')) {
        $email_sent = flora_send_customer_email_confirmed($order);
    }

    // 4. Bắn Webhook Zalo Bot (nếu thao tác từ WP-Admin để báo vào nhóm Zalo)
    if (strpos($operator_name, 'Zalo') === false) {
        flora_send_zalo_notification($order, 'order_paid');
    }

    return array(
        'success'    => true,
        'reapproved' => $is_recovering,
        'message'    => ($is_recovering ? 'Đã KHÔI PHỤC & DUYỆT THÀNH CÔNG đơn hàng #' : 'Đã DUYỆT THÀNH CÔNG đơn hàng #') . $order['order_code'],
        'email_sent' => $email_sent,
        'order'      => $order
    );
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 10.2. HÀM TỪ CHỐI ĐƠN HÀNG TOÀN DIỆN (Core Rejection Engine)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_reject_order($order_id_or_code, $reason = '', $operator_name = 'Admin') {
    global $wpdb;
    $table_name = flora_get_orders_table_name();

    $order = null;
    if (is_numeric($order_id_or_code)) {
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d LIMIT 1", (int)$order_id_or_code), ARRAY_A);
    } else {
        $clean_code = strtoupper(trim(trim($order_id_or_code), ' "#\':;.,<>[]()'));
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE UPPER(order_code) = %s LIMIT 1", $clean_code), ARRAY_A);
        if (!$order && stripos($clean_code, 'FLORA') === false) {
            $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE UPPER(order_code) = %s LIMIT 1", 'FLORA-' . $clean_code), ARRAY_A);
        }
        if (!$order) {
            $no_dash = str_replace('-', '', $clean_code);
            $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE REPLACE(UPPER(order_code), '-', '') = %s LIMIT 1", $no_dash), ARRAY_A);
        }
    }

    if (!$order) {
        return array('success' => false, 'message' => 'Không tìm thấy đơn hàng với mã: ' . $order_id_or_code);
    }

    // 1. Kiểm tra nếu đơn ĐÃ ĐƯỢC DUYỆT THANH TOÁN (Không cho phép từ chối qua bot để bảo vệ kế toán)
    if ($order['payment_status'] === 'paid') {
        $prev_operator = flora_extract_operator_from_order_notes($order['notes'] ?? '', 'approve');
        $paid_time_fmt = !empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : 'trước đó';
        return array(
            'success'   => false,
            'is_paid'   => true,
            'operator'  => $prev_operator,
            'paid_time' => $paid_time_fmt,
            'message'   => 'Đơn hàng #' . $order['order_code'] . ' ĐÃ THANH TOÁN THÀNH CÔNG trước đó lúc ' . $paid_time_fmt . ' bởi ' . $prev_operator . '. Để hoàn tiền hoặc hủy đơn, vui lòng xử lý tại WP-Admin!',
            'order'     => $order
        );
    }

    // 2. Kiểm tra nếu đơn ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ RỒI
    if ($order['payment_status'] === 'rejected') {
        $prev_operator = flora_extract_operator_from_order_notes($order['notes'] ?? '', 'reject');
        $reject_time_fmt = !empty($order['rejected_at']) ? date('d/m/Y H:i:s', strtotime($order['rejected_at'])) : 'trước đó';
        $reject_reason = !empty($order['reject_reason']) ? $order['reject_reason'] : 'Không ghi rõ lý do';
        return array(
            'success'          => false,
            'already_rejected' => true,
            'operator'         => $prev_operator,
            'rejected_time'    => $reject_time_fmt,
            'reject_reason'    => $reject_reason,
            'message'          => 'Đơn hàng #' . $order['order_code'] . ' ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ lúc ' . $reject_time_fmt . ' bởi ' . $prev_operator . ' (Lý do: ' . $reject_reason . '). Không cần từ chối lại!',
            'order'            => $order
        );
    }

    if (empty($reason)) {
        $reason = 'Chưa nhận được giao dịch chuyển khoản vào tài khoản ngân hàng ACB sau thời gian đối soát.';
    }

    $now = current_time('mysql');
    $log_note = ' | Từ chối bởi ' . esc_html($operator_name) . ' lúc ' . current_time('d/m/Y H:i:s') . ' (Lý do: ' . esc_html($reason) . ')';
    $new_notes = trim(($order['notes'] ?? '') . $log_note);

    $updated = $wpdb->update(
        $table_name,
        array(
            'payment_status' => 'rejected',
            'rejected_at'    => $now,
            'reject_reason'  => $reason,
            'notes'          => $new_notes
        ),
        array('id' => $order['id'])
    );

    if ($updated === false) {
        return array('success' => false, 'message' => 'Lỗi cập nhật trạng thái đơn trong cơ sở dữ liệu.');
    }

    $order['payment_status'] = 'rejected';
    $order['rejected_at']    = $now;
    $order['reject_reason']  = $reason;
    $order['notes']          = $new_notes;

    // 1. Gửi Email 3 cho Khách hàng kèm kênh khiếu nại
    $email_sent = false;
    if (function_exists('flora_send_customer_email_rejected')) {
        $email_sent = flora_send_customer_email_rejected($order, $reason);
    }

    // 2. Bắn Webhook Zalo Bot (nếu thao tác từ WP-Admin)
    if (strpos($operator_name, 'Zalo') === false) {
        flora_send_zalo_notification($order, 'order_rejected', array('reason' => $reason));
    }

    return array(
        'success'    => true,
        'message'    => 'Đã từ chối đơn hàng #' . $order['order_code'],
        'email_sent' => $email_sent,
        'order'      => $order
    );
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 11. GIAO DIỆN WP-ADMIN: QUẢN LÝ ĐƠN HÀNG & CÀI ĐẶT
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_register_orders_admin_menu() {
    global $wpdb;
    $table_name = flora_get_orders_table_name();
    $pending_count = 0;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $pending_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status IN ('pending', 'pending_confirmation')");
    }

    $menu_title = 'Đơn Hàng Gói Khám';
    if ($pending_count > 0) {
        $menu_title .= ' <span class="update-plugins count-' . $pending_count . '"><span class="plugin-count">' . $pending_count . '</span></span>';
    }

    add_menu_page(
        'Quản Lý Đơn Hàng Gói Dịch Vụ - Flora Clinic',
        $menu_title,
        'manage_options',
        'flora-orders',
        'flora_render_orders_admin_page',
        'dashicons-cart',
        5.3
    );

    add_submenu_page(
        'flora-orders',
        'Tất Cả Đơn Hàng',
        'Tất Cả Đơn Hàng',
        'manage_options',
        'flora-orders',
        'flora_render_orders_admin_page'
    );

    add_submenu_page(
        'flora-orders',
        'Cài Đặt Ngân Hàng & Zalo Bot',
        'Cài Đặt Webhook & Ngân Hàng',
        'manage_options',
        'flora-payment-settings',
        'flora_render_payment_settings_page'
    );
}
add_action('admin_menu', 'flora_register_orders_admin_menu');

/**
 * Xuất danh sách đơn hàng sang file CSV UTF-8 BOM
 */
function flora_export_orders_csv() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'flora-orders' || !isset($_GET['action']) || $_GET['action'] !== 'export_csv') {
        return;
    }

    check_admin_referer('flora_export_orders_nonce');
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();
    $orders = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Flora_Don_Hang_' . date('Y-m-d_H-i') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Mã Đơn Hàng', 'Họ Tên Khách Hàng', 'Số Điện Thoại', 'Email', 'Gói Dịch Vụ', 'Số Tiền (VNĐ)', 'Voucher', 'KOL/Ref', 'Cú Pháp CK', 'Trạng Thái', 'Ảnh Bill', 'Lý Do Hủy', 'Ngày Tạo', 'Ngày Thanh Toán'));

    if (!empty($orders)) {
        foreach ($orders as $o) {
            $status_str = 'Chờ xác nhận';
            if ($o['payment_status'] === 'paid') $status_str = 'Đã thanh toán';
            elseif ($o['payment_status'] === 'rejected') $status_str = 'Đã từ chối/Hết hạn';
            elseif ($o['payment_status'] === 'cancelled') $status_str = 'Đã hủy';

            fputcsv($output, array(
                $o['id'],
                $o['order_code'],
                $o['customer_name'],
                $o['customer_phone'],
                $o['customer_email'],
                $o['package_name'],
                $o['final_amount'],
                $o['voucher_code'],
                $o['affiliate_code'],
                $o['transfer_syntax'],
                $status_str,
                $o['proof_image_url'],
                $o['reject_reason'],
                $o['created_at'],
                $o['paid_at']
            ));
        }
    }
    fclose($output);
    exit;
}
add_action('admin_init', 'flora_export_orders_csv');

/**
 * TRANG QUẢN TRỊ ĐƠN HÀNG CHÍNH (WP-ADMIN)
 */
function flora_render_orders_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = flora_get_orders_table_name();

    // Chạy quét đơn quá 24h tự động
    flora_check_expired_orders_cron();

    // Xử lý đổi trạng thái (Duyệt / Từ Chối / Hủy)
    if (isset($_GET['action']) && $_GET['action'] === 'change_status' && isset($_GET['id']) && isset($_GET['new_status'])) {
        check_admin_referer('flora_order_action');
        $id = (int)$_GET['id'];
        $new_status = sanitize_text_field($_GET['new_status']);
        $reason = isset($_GET['reason']) ? sanitize_text_field($_GET['reason']) : '';
        $current_user = wp_get_current_user();
        $admin_name = $current_user->display_name ?: 'Admin WP';

        if ($new_status === 'paid') {
            $res = flora_approve_order($id, $admin_name);
            if ($res['success']) {
                echo '<div class="notice notice-success is-dismissible"><p><strong>Đã DUYỆT thành công đơn hàng #' . esc_html($res['order']['order_code']) . '!</strong> Đã gửi email xác nhận cho khách hàng và cập nhật hệ thống.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p><strong>' . esc_html($res['message']) . '</strong></p></div>';
            }
        } elseif ($new_status === 'rejected') {
            $res = flora_reject_order($id, $reason, $admin_name);
            if ($res['success']) {
                echo '<div class="notice notice-warning is-dismissible"><p><strong>Đã TỪ CHỐI đơn hàng #' . esc_html($res['order']['order_code']) . '!</strong> Đã gửi email thông báo kèm hotline khiếu nại đến khách hàng.</p></div>';
            } else {
                echo '<div class="notice notice-error is-dismissible"><p><strong>' . esc_html($res['message']) . '</strong></p></div>';
            }
        } else {
            $wpdb->update($table_name, array('payment_status' => $new_status), array('id' => $id));
            echo '<div class="notice notice-info is-dismissible"><p>Đã cập nhật trạng thái đơn hàng thành công!</p></div>';
        }
    }

    // Xử lý xóa đơn
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('flora_order_delete');
        $id = (int)$_GET['id'];
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success is-dismissible"><p>Đã xóa đơn hàng #' . $id . ' thành công!</p></div>';
    }

    // Thống kê nhanh
    $total_orders  = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_paid    = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status = 'paid'");
    $total_pending = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status IN ('pending', 'pending_confirmation')");
    $total_rejected= (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status = 'rejected'");
    $revenue_paid  = (int) $wpdb->get_var("SELECT COALESCE(SUM(final_amount), 0) FROM $table_name WHERE payment_status = 'paid'");

    // Bộ lọc và tìm kiếm
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
    $search_query  = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where = array('1=1');
    if (!empty($status_filter)) {
        if ($status_filter === 'pending_confirmation' || $status_filter === 'pending') {
            $where[] = "payment_status IN ('pending', 'pending_confirmation')";
        } else {
            $where[] = $wpdb->prepare("payment_status = %s", $status_filter);
        }
    }
    if (!empty($search_query)) {
        $like = '%' . $wpdb->esc_like($search_query) . '%';
        $where[] = $wpdb->prepare("(order_code LIKE %s OR customer_name LIKE %s OR customer_phone LIKE %s OR transfer_syntax LIKE %s)", $like, $like, $like, $like);
    }
    $where_sql = implode(' AND ', $where);

    // Phân trang
    $page = isset($_GET['paged']) ? max(1, (int)$_GET['paged']) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    $total_filtered = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where_sql");
    $total_pages = ceil($total_filtered / $per_page);

    $orders = $wpdb->get_results("SELECT * FROM $table_name WHERE $where_sql ORDER BY created_at DESC LIMIT $offset, $per_page", ARRAY_A);
    $export_url = wp_nonce_url(admin_url('admin.php?page=flora-orders&action=export_csv'), 'flora_export_orders_nonce');
    ?>

    <style>
        .flora-order-wrap { margin: 16px 20px 0 2px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .flora-order-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 18px; padding: 16px 20px; background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; }
        .flora-order-header h1 { font-size: 1.25rem; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px; letter-spacing: -0.2px; }
        
        /* KPI GRID - Clean, Compact, Refined */
        .flora-kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr) !important; gap: 14px; margin-bottom: 18px; }
        @media screen and (max-width: 1024px) { .flora-kpi-grid { grid-template-columns: repeat(2, 1fr) !important; } }
        @media screen and (max-width: 640px) { .flora-kpi-grid { grid-template-columns: 1fr !important; } }
        
        .flora-kpi-card { background: #fff; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 14px; }
        .flora-kpi-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; flex-shrink: 0; }
        .flora-kpi-num { font-size: 1.35rem; font-weight: 700; line-height: 1.1; margin-bottom: 2px; color: #0f172a; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .flora-kpi-label { font-size: 0.76rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }

        /* FILTER BAR - Modern Segmented Tabs */
        .flora-filter-bar { background: #fff; padding: 10px 16px; border-radius: 10px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .flora-tab-group { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .flora-tab-btn { padding: 6px 12px; border-radius: 6px; font-size: 0.82rem; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; background: #fff; color: #475569; transition: all 0.15s ease; display: inline-flex; align-items: center; gap: 5px; }
        .flora-tab-btn:hover { background: #f8fafc; color: #0f172a; border-color: #cbd5e1; }
        .flora-tab-btn.active { background: #0033a3; color: #fff; border-color: #0033a3; }
        .flora-tab-count { font-size: 0.75rem; opacity: 0.85; font-weight: 500; }

        /* TABLE - Compact, Clean, Professional */
        .tbl-orders-wrap { background: #fff; border-radius: 10px; border: 1px solid #e2e8f0; overflow-x: auto; margin-top: 14px; }
        .tbl-orders { width: 100%; border-collapse: collapse; text-align: left; font-size: 0.84rem; }
        .tbl-orders th { background: #f8fafc; color: #475569; font-weight: 600; padding: 10px 14px; border-bottom: 1px solid #e2e8f0; white-space: nowrap; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.4px; }
        .tbl-orders td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #1e293b; }
        .tbl-orders tr:hover td { background: #fcfdfe; }

        .order-code-badge { font-weight: 700; color: #0f172a; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.86rem; }
        .order-date-text { font-size: 0.73rem; color: #64748b; margin-top: 2px; }
        .time-pill-clean { font-size: 0.7rem; color: #64748b; background: #f1f5f9; padding: 1px 6px; border-radius: 4px; display: inline-block; margin-top: 3px; font-weight: 500; }
        .time-pill-clean.warning { color: #b45309; background: #fef3c7; }
        .time-pill-clean.expired { color: #b91c1c; background: #fee2e2; }

        /* Customer column */
        .customer-name { font-weight: 600; color: #0f172a; font-size: 0.86rem; }
        .customer-phone { color: #334155; font-weight: 500; text-decoration: none; font-size: 0.82rem; }
        .customer-phone:hover { color: #0033a3; text-decoration: underline; }
        .customer-email { font-size: 0.74rem; color: #64748b; }
        .badge-consult { font-size: 0.68rem; color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 1px 6px; border-radius: 4px; font-weight: 600; display: inline-block; margin-top: 2px; }

        /* Package column */
        .package-title { font-weight: 600; color: #0f172a; line-height: 1.35; font-size: 0.84rem; }
        .tag-minimal { font-size: 0.7rem; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 1px 6px; border-radius: 4px; font-weight: 500; display: inline-block; margin-top: 3px; }
        .tag-minimal strong { color: #0f172a; font-weight: 600; }

        /* Price */
        .price-badge { font-weight: 700; color: #0f172a; font-size: 0.88rem; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; }
        .price-old { font-size: 0.72rem; color: #94a3b8; text-decoration: line-through; }

        /* Syntax Pill - Clean Monospace */
        .syntax-pill { background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; padding: 2px 7px; border-radius: 5px; font-weight: 600; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 0.8rem; display: inline-block; letter-spacing: 0.2px; }
        .syntax-sub { font-size: 0.7rem; color: #94a3b8; margin-top: 2px; }

        /* Proof Thumbnail */
        .proof-thumb { width: 38px; height: 38px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1; cursor: pointer; transition: transform 0.15s; vertical-align: middle; }
        .proof-thumb:hover { transform: scale(1.08); border-color: #0033a3; }
        .proof-none { font-size: 0.76rem; color: #94a3b8; }

        /* Status Pill - Dot + Label, Minimalist */
        .status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 9999px; font-size: 0.74rem; font-weight: 600; white-space: nowrap; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
        
        .status-pill.status-paid { background: #f0fdf4; border: 1px solid #dcfce7; color: #166534; }
        .status-pill.status-paid .status-dot { background: #16a34a; }
        
        .status-pill.status-pending { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; }
        .status-pill.status-pending .status-dot { background: #d97706; }
        
        .status-pill.status-rejected { background: #fef2f2; border: 1px solid #fee2e2; color: #991b1b; }
        .status-pill.status-rejected .status-dot { background: #dc2626; }
        
        .status-pill.status-other { background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; }
        .status-pill.status-other .status-dot { background: #94a3b8; }

        .status-sub { font-size: 0.7rem; color: #64748b; margin-top: 2px; }
        .status-sub-reason { font-size: 0.7rem; color: #94a3b8; margin-top: 2px; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Actions - Compact, Clean, Sleek */
        .action-btn-group { display: flex; gap: 5px; align-items: center; }
        .btn-act { padding: 4px 9px; border-radius: 5px; font-size: 0.76rem; font-weight: 600; text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; border: 1px solid transparent; transition: all 0.15s ease; height: 26px; box-sizing: border-box; }
        
        .btn-act-paid { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
        .btn-act-paid:hover { background: #16a34a; color: #ffffff; border-color: #16a34a; }
        
        .btn-act-reject { background: #ffffff; color: #64748b; border-color: #e2e8f0; }
        .btn-act-reject:hover { background: #fef2f2; color: #b91c1c; border-color: #fca5a5; }
        
        .btn-act-del { background: transparent; color: #94a3b8; border: none; width: 26px; height: 26px; padding: 0; border-radius: 5px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; cursor: pointer; }
        .btn-act-del:hover { background: #fee2e2; color: #dc2626; }
    </style>

    <div class="flora-order-wrap">
        <div class="flora-order-header">
            <div>
                <h1><i class="dashicons dashicons-cart" style="font-size: 1.4rem; width: auto; height: auto; color: #0033a3;"></i> Quản Lý Đơn Hàng Gói Dịch Vụ</h1>
                <p style="margin: 3px 0 0; color: #64748b; font-size: 0.84rem;">
                    Xác thực chuyển khoản VietQR Ngân hàng ACB (77779268), đối soát cú pháp SĐT và kích hoạt gói khám tự động.
                </p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-payment-settings')); ?>" class="button button-secondary" style="height: 36px; line-height: 34px; padding: 0 14px; font-weight: 600; font-size: 13px;">
                    <i class="dashicons dashicons-admin-generic" style="margin-top: 7px;"></i> Cài Đặt ACB & Zalo Bot
                </a>
                <a href="<?php echo esc_url($export_url); ?>" class="button button-primary" style="height: 36px; line-height: 34px; padding: 0 14px; font-weight: 600; font-size: 13px; background: #0033a3; border-color: #0033a3;">
                    <i class="dashicons dashicons-download" style="margin-top: 7px;"></i> Xuất Excel (CSV)
                </a>
            </div>
        </div>

        <!-- 4 KPI CARDS -->
        <div class="flora-kpi-grid">
            <div class="flora-kpi-card">
                <div class="flora-kpi-icon" style="background: #f1f5f9; color: #334155;"><i class="dashicons dashicons-list-view"></i></div>
                <div>
                    <div class="flora-kpi-num"><?php echo number_format($total_orders); ?></div>
                    <div class="flora-kpi-label">Tổng Đơn Hàng</div>
                </div>
            </div>
            <div class="flora-kpi-card">
                <div class="flora-kpi-icon" style="background: #f0fdf4; color: #16a34a;"><i class="dashicons dashicons-yes-alt"></i></div>
                <div>
                    <div class="flora-kpi-num" style="color: #166534;"><?php echo number_format($revenue_paid, 0, ',', '.'); ?>đ</div>
                    <div class="flora-kpi-label">Đã Thu (<?php echo $total_paid; ?> đơn)</div>
                </div>
            </div>
            <div class="flora-kpi-card">
                <div class="flora-kpi-icon" style="background: #fffbeb; color: #b45309;"><i class="dashicons dashicons-clock"></i></div>
                <div>
                    <div class="flora-kpi-num" style="color: #92400e;"><?php echo number_format($total_pending); ?></div>
                    <div class="flora-kpi-label">Chờ Xác Nhận</div>
                </div>
            </div>
            <div class="flora-kpi-card">
                <div class="flora-kpi-icon" style="background: #fef2f2; color: #dc2626;"><i class="dashicons dashicons-dismiss"></i></div>
                <div>
                    <div class="flora-kpi-num" style="color: #991b1b;"><?php echo number_format($total_rejected); ?></div>
                    <div class="flora-kpi-label">Từ Chối / Quá 24h</div>
                </div>
            </div>
        </div>

        <!-- FILTER & SEARCH -->
        <div class="flora-filter-bar">
            <div class="flora-tab-group">
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-orders')); ?>" class="flora-tab-btn <?php echo empty($status_filter) ? 'active' : ''; ?>">
                    Tất cả <span class="flora-tab-count">(<?php echo $total_orders; ?>)</span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-orders&status_filter=pending_confirmation')); ?>" class="flora-tab-btn <?php echo ($status_filter === 'pending_confirmation' || $status_filter === 'pending') ? 'active' : ''; ?>">
                    Chờ duyệt <span class="flora-tab-count">(<?php echo $total_pending; ?>)</span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-orders&status_filter=paid')); ?>" class="flora-tab-btn <?php echo $status_filter === 'paid' ? 'active' : ''; ?>">
                    Đã thanh toán <span class="flora-tab-count">(<?php echo $total_paid; ?>)</span>
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-orders&status_filter=rejected')); ?>" class="flora-tab-btn <?php echo $status_filter === 'rejected' ? 'active' : ''; ?>">
                    Từ chối / Hết hạn <span class="flora-tab-count">(<?php echo $total_rejected; ?>)</span>
                </a>
            </div>

            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="display: flex; gap: 8px;">
                <input type="hidden" name="page" value="flora-orders" />
                <?php if (!empty($status_filter)): ?><input type="hidden" name="status_filter" value="<?php echo esc_attr($status_filter); ?>" /><?php endif; ?>
                <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Tìm mã đơn, Tên, SĐT..." style="height: 32px; border-radius: 6px; border: 1px solid #cbd5e1; width: 220px; font-size: 13px;" />
                <button type="submit" class="button button-secondary" style="height: 32px; font-size: 13px;">Tìm kiếm</button>
            </form>
        </div>

        <!-- TABLE OF ORDERS -->
        <div class="tbl-orders-wrap">
            <table class="tbl-orders" style="min-width: 1040px; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="white-space: nowrap;">Mã Đơn / Hạn 24h</th>
                        <th>Khách Hàng (SĐT)</th>
                        <th>Gói Dịch Vụ</th>
                        <th style="white-space: nowrap;">Số Tiền</th>
                        <th style="white-space: nowrap;">Cú Pháp CK (ACB 77779268)</th>
                        <th style="text-align: center; white-space: nowrap;">Minh Chứng Bill</th>
                        <th style="text-align: center; white-space: nowrap;">Trạng Thái</th>
                        <th style="text-align: right; white-space: nowrap;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 36px; color: #94a3b8;">
                                <i class="dashicons dashicons-cart" style="font-size: 2.5rem; width: auto; height: auto; opacity: 0.35;"></i>
                                <p style="margin: 8px 0 0; font-size: 0.95rem;">Chưa có đơn hàng nào trong mục này.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                            <?php
                            $created_ts = strtotime($o['created_at']);
                            $expire_ts  = $created_ts + (24 * 3600);
                            $remaining_secs = $expire_ts - current_time('timestamp');
                            $is_pending = in_array($o['payment_status'], array('pending', 'pending_confirmation'));
                            ?>
                            <tr>
                                <td style="white-space: nowrap;">
                                    <div class="order-code-badge"><?php echo esc_html($o['order_code']); ?></div>
                                    <div class="order-date-text">
                                        <?php echo date('d/m/Y H:i', $created_ts); ?>
                                    </div>
                                    <?php if ($is_pending): ?>
                                        <?php if ($remaining_secs > 0): ?>
                                            <div class="time-pill-clean warning">
                                                Còn <?php echo ceil($remaining_secs / 3600); ?> giờ
                                            </div>
                                        <?php else: ?>
                                            <div class="time-pill-clean expired">
                                                Quá 24 giờ
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="customer-name"><?php echo esc_html($o['customer_name']); ?></div>
                                    <div style="margin-top: 1px;">
                                        <a href="tel:<?php echo esc_attr($o['customer_phone']); ?>" class="customer-phone">
                                            <?php echo esc_html($o['customer_phone']); ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($o['customer_email'])): ?>
                                        <div class="customer-email"><?php echo esc_html($o['customer_email']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($o['need_consult'])): ?>
                                        <span class="badge-consult">Cần tư vấn</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="package-title"><?php echo esc_html($o['package_name']); ?></div>
                                    <?php if (!empty($o['voucher_code'])): ?>
                                        <span class="tag-minimal">Voucher: <strong><?php echo esc_html($o['voucher_code']); ?></strong></span>
                                    <?php endif; ?>
                                    <?php if (!empty($o['affiliate_code'])): ?>
                                        <span class="tag-minimal">KOL: <strong><?php echo esc_html($o['affiliate_code']); ?></strong></span>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div class="price-badge"><?php echo number_format($o['final_amount'], 0, ',', '.'); ?>đ</div>
                                    <?php if ($o['discount_amount'] > 0): ?>
                                        <div class="price-old"><?php echo number_format($o['original_price'], 0, ',', '.'); ?>đ</div>
                                    <?php endif; ?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <div class="syntax-pill"><?php echo esc_html($o['transfer_syntax']); ?></div>
                                    <div class="syntax-sub">ACB: <strong>77779268</strong></div>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <?php if (!empty($o['proof_image_url'])): ?>
                                        <a href="<?php echo esc_url($o['proof_image_url']); ?>" target="_blank" title="Bấm xem ảnh bill gốc">
                                            <img src="<?php echo esc_url($o['proof_image_url']); ?>" class="proof-thumb" alt="Bill" />
                                        </a>
                                    <?php else: ?>
                                        <span class="proof-none">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center; white-space: nowrap;">
                                    <?php if ($o['payment_status'] === 'paid'): ?>
                                        <span class="status-pill status-paid"><span class="status-dot"></span>Đã duyệt</span>
                                        <?php if (!empty($o['paid_at'])): ?>
                                            <div class="status-sub"><?php echo date('H:i d/m', strtotime($o['paid_at'])); ?></div>
                                        <?php endif; ?>
                                    <?php elseif ($is_pending): ?>
                                        <span class="status-pill status-pending"><span class="status-dot"></span>Chờ duyệt</span>
                                    <?php elseif ($o['payment_status'] === 'rejected'): ?>
                                        <span class="status-pill status-rejected"><span class="status-dot"></span>Từ chối</span>
                                        <?php if (!empty($o['reject_reason'])): ?>
                                            <div class="status-sub-reason" title="<?php echo esc_attr($o['reject_reason']); ?>">
                                                <?php echo esc_html($o['reject_reason']); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-pill status-other"><span class="status-dot"></span><?php echo esc_html($o['payment_status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <div class="action-btn-group" style="justify-content: flex-end;">
                                        <?php if ($o['payment_status'] !== 'paid'): ?>
                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-orders&action=change_status&id=' . $o['id'] . '&new_status=paid'), 'flora_order_action'); ?>" class="btn-act btn-act-paid" onclick="return confirm('XÁC NHẬN: Khách hàng đã chuyển khoản thành công đơn #' + '<?php echo esc_js($o['order_code']); ?>' + ' qua ACB 77779268? Hệ thống sẽ gửi email kích hoạt và báo Zalo.');" title="Duyệt đơn & Gửi email">
                                                Duyệt
                                            </a>
                                            <button type="button" class="btn-act btn-act-reject" onclick="promptRejectOrder(<?php echo $o['id']; ?>, '<?php echo esc_js($o['order_code']); ?>')" title="Từ chối đơn & Báo khách">
                                                Từ chối
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-orders&action=delete&id=' . $o['id']), 'flora_order_delete'); ?>" class="btn-act-del" onclick="return confirm('CẢNH BÁO: Xóa vĩnh viễn đơn hàng này?');" title="Xóa">
                                            <i class="dashicons dashicons-trash" style="font-size:15px;width:15px;height:15px;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PHÂN TRANG -->
        <?php if ($total_pages > 1): ?>
            <div style="margin-top: 16px; display: flex; justify-content: flex-end; gap: 6px;">
                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $p)); ?>" class="button <?php echo $p === $page ? 'button-primary' : ''; ?>" style="<?php echo $p === $page ? 'background:#0033a3;border-color:#0033a3;' : ''; ?>"><?php echo $p; ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function promptRejectOrder(orderId, orderCode) {
        var reason = prompt('Nhập lý do từ chối đơn hàng #' + orderCode + ' (Lý do này sẽ được gửi trong email thông báo cho khách hàng kèm hotline khiếu nại):', 'Chưa nhận được chuyển khoản sau khi đối soát sao kê ACB.');
        if (reason !== null && reason.trim() !== '') {
            var url = '<?php echo admin_url('admin.php?page=flora-orders&action=change_status'); ?>'
                    + '&id=' + orderId
                    + '&new_status=rejected'
                    + '&reason=' + encodeURIComponent(reason.trim())
                    + '&_wpnonce=<?php echo wp_create_nonce('flora_order_action'); ?>';
            window.location.href = url;
        }
    }
    </script>
    <?php
}

/**
 * TRANG CÀI ĐẶT NGÂN HÀNG ACB & ZALO WEBHOOK (WP-ADMIN)
 */
function flora_render_payment_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['flora_payment_save_nonce']) && wp_verify_nonce($_POST['flora_payment_save_nonce'], 'flora_payment_save')) {
        update_option('flora_zalo_bot_token', sanitize_text_field(trim($_POST['flora_zalo_bot_token'] ?? '')));
        update_option('flora_zalo_group_chat_id', sanitize_text_field(trim($_POST['flora_zalo_group_chat_id'] ?? '')));
        update_option('flora_zalo_secret_token', sanitize_text_field(trim($_POST['flora_zalo_secret_token'] ?? 'flora2026')));
        update_option('flora_zalo_webhook_url', esc_url_raw(trim($_POST['zalo_webhook_url'] ?? '')));
        echo '<div class="notice notice-success is-dismissible"><p><strong>Đã lưu thành công cấu hình Ngân Hàng ACB & Zalo Bot Webhook!</strong></p></div>';
    }

    $config = flora_get_payment_config();
    $webhook_endpoint_url = get_stylesheet_directory_uri() . '/api/zalo-webhook.php';
    ?>
    <div class="flora-order-wrap" style="max-width: 960px;">
        <div class="flora-order-header">
            <div>
                <h1><i class="dashicons dashicons-admin-generic" style="font-size: 1.7rem; width: auto; height: auto;"></i> CẤU HÌNH NGÂN HÀNG ACB & HỆ THỐNG ZALO BOT</h1>
                <p style="margin: 4px 0 0; color: #64748b; font-size: 0.88rem;">Quản lý tài khoản nhận tiền VietQR ACB và tích hợp Zalo Bot tương tác 2 chiều cho Nhóm Tư Vấn Viên.</p>
            </div>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-orders')); ?>" class="button button-secondary" style="height: 38px; line-height: 36px;">
                &larr; Quay lại Danh Sách Đơn Hàng
            </a>
        </div>

        <form method="post" action="">
            <?php wp_nonce_field('flora_payment_save', 'flora_payment_save_nonce'); ?>

            <!-- Card 1: Tài khoản Ngân Hàng ACB -->
            <div style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #0033a3;">
                        <i class="dashicons dashicons-money-alt"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a;">1. Tài Khoản Nhận Tiền Duy Nhất (ACB VietQR)</h3>
                        <p style="margin: 2px 0 0; color: #64748b; font-size: 0.82rem;">Được hiển thị cố định trên Modal chuyển khoản cho khách hàng.</p>
                    </div>
                </div>

                <table class="form-table" style="margin: 0;">
                    <tr>
                        <th scope="row" style="width: 220px;">Ngân Hàng</th>
                        <td><input type="text" readonly value="<?php echo esc_attr($config['bank_name']); ?>" class="regular-text" style="background:#f8fafc;" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Số Tài Khoản (STK)</th>
                        <td><input type="text" readonly value="<?php echo esc_attr($config['bank_account']); ?>" class="regular-text" style="font-weight: 800; color: #0033a3; font-size: 1.1rem; background:#f8fafc;" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Chủ Tài Khoản</th>
                        <td><input type="text" readonly value="<?php echo esc_attr($config['bank_owner']); ?>" class="regular-text" style="background:#f8fafc;" /></td>
                    </tr>
                    <tr>
                        <th scope="row">Chi Nhánh</th>
                        <td><input type="text" readonly value="<?php echo esc_attr($config['bank_branch']); ?>" class="regular-text" style="background:#f8fafc;" /></td>
                    </tr>
                </table>
            </div>

            <!-- Card 2: Zalo Bot Platform (2 Chiều) -->
            <div style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #e0f2fe; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #0284c7;">
                        <i class="dashicons dashicons-format-chat"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a;">2. Hệ Thống Zalo Bot & Webhook Tương Tác 2 Chiều (Zalo Bot Platform)</h3>
                        <p style="margin: 2px 0 0; color: #64748b; font-size: 0.82rem;">Tự động bắn thông báo đơn mới vào nhóm Zalo, cho phép TVV duyệt đơn trực tiếp bằng lệnh <code>/duyet [mã]</code>.</p>
                    </div>
                </div>

                <table class="form-table" style="margin: 0;">
                    <tr>
                        <th scope="row" style="width: 220px;"><label>Link Webhook của Website Flora</label></th>
                        <td>
                            <div style="display: flex; gap: 8px; max-width: 680px;">
                                <input type="text" id="flora_webhook_display" readonly value="<?php echo esc_url($webhook_endpoint_url); ?>" style="flex: 1; height: 38px; border-radius: 6px; background: #f8fafc; font-family: monospace; font-size: 0.88rem;" />
                                <button type="button" class="button" onclick="copyWebhookUrl()" style="height: 38px; white-space: nowrap;">
                                    📋 Sao Chép
                                </button>
                                <a href="<?php echo esc_url($webhook_endpoint_url); ?>" target="_blank" class="button button-secondary" style="height: 38px; line-height: 36px; white-space: nowrap;">
                                    🔍 Kiểm Tra Link
                                </a>
                            </div>
                            <p class="description" style="margin-top: 6px;">
                                Đây là địa chỉ tiếp nhận Webhook chuẩn của Nha Khoa Flora, hỗ trợ lệnh Zalo Bot, duyệt đơn, và kiểm tra sức khỏe hệ thống (GET healthcheck).
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="flora_zalo_bot_token">Zalo Bot Token</label></th>
                        <td>
                            <input type="text" name="flora_zalo_bot_token" id="flora_zalo_bot_token" value="<?php echo esc_attr($config['zalo_bot_token']); ?>" placeholder="VD: 2619217939593455388:llxFUkOWLJCvqLxpvBRnJoVtzuIZoVEcJmVJPHUqjMHOxHpRWamrFseaQFsuEDaq" style="width: 100%; max-width: 680px; height: 38px; border-radius: 6px; font-family: monospace; font-size: 0.88rem;" />
                            <p class="description" style="margin-top: 6px;">
                                Token lấy từ <strong>Zalo Bot Platform</strong> (bot-api.zaloplatforms.com).
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="flora_zalo_secret_token">Secret Token Bảo Mật</label></th>
                        <td>
                            <input type="text" name="flora_zalo_secret_token" id="flora_zalo_secret_token" value="<?php echo esc_attr($config['zalo_secret_token']); ?>" placeholder="flora2026" style="width: 260px; height: 38px; border-radius: 6px; font-family: monospace;" />
                            <span class="description" style="margin-left: 10px;">Token bí mật để Zalo Bot chứng thực Webhook (mặc định: <code>flora2026</code>).</span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="flora_zalo_group_chat_id">Zalo Group Chat ID</label></th>
                        <td>
                            <input type="text" name="flora_zalo_group_chat_id" id="flora_zalo_group_chat_id" value="<?php echo esc_attr($config['zalo_group_chat_id']); ?>" placeholder="VD: zgr-46e8aa01ae6e47301e7f" style="width: 320px; height: 38px; border-radius: 6px; font-family: monospace;" />
                            <p class="description" style="margin-top: 6px; color: #0284c7; font-weight: 500;">
                                💡 <strong>Tự động nhận diện:</strong> Sau khi kích hoạt Webhook, bạn chỉ cần mời Bot vào Nhóm Zalo Tư Vấn Viên và gõ tin nhắn <code>/chatid</code>. Nhóm sẽ tự động kết nối và lưu vào đây!
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Thao Tác Kích Hoạt & Thử Nghiệm</th>
                        <td>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                <button type="button" class="button button-primary" onclick="setZaloBotWebhook()" style="background: #0284c7; border-color: #0284c7; height: 38px;">
                                    ⚡ 1-Click Kích Hoạt Webhook Với Zalo
                                </button>
                                <button type="button" class="button button-secondary" onclick="testSendZaloGroupMessage()" style="height: 38px;">
                                    💬 Bắn Thử Tin Nhắn Vào Nhóm Zalo
                                </button>
                            </div>
                            <div id="zaloBotActionStatus" style="margin-top: 10px; font-size: 0.9rem; font-weight: 600;"></div>
                        </td>
                    </tr>
                </table>

                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 14px 18px; margin-top: 20px;">
                    <div style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">📖 Danh Sách Lệnh Tương Tác Trong Nhóm Zalo (TVV Sử Dụng Trực Tiếp):</div>
                    <ul style="margin: 0; padding-left: 20px; font-size: 0.86rem; color: #475569; line-height: 1.8;">
                        <li><code>/chatid</code> : Nhận diện và đăng ký nhóm Zalo này với website Flora.</li>
                        <li><code>/pending</code> : Xem danh sách các đơn hàng chuyển khoản đang chờ duyệt kèm link ảnh biên lai.</li>
                        <li><code>/duyet &lt;Mã Đơn&gt;</code> : Duyệt đơn ngay trên Zalo &rarr; Kích hoạt gói khám và gửi Email xác nhận kèm mã QR cho khách.</li>
                        <li><code>/tuchoi &lt;Mã Đơn&gt; [Lý do]</code> : Từ chối đơn và gửi email hướng dẫn khiếu nại/hotline cho khách.</li>
                        <li><code>/report</code> : Báo cáo nhanh số lượng đơn và tổng doanh thu thực thu trong ngày.</li>
                        <li><code>/test</code> hoặc <code>/hello</code> : Kiểm tra trạng thái hoạt động của Zalo Bot.</li>
                    </ul>
                </div>
            </div>

            <!-- Card 3: Webhook Dự Phòng / Phụ (Tùy chọn) -->
            <div style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.03); margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div style="width: 38px; height: 38px; border-radius: 8px; background: #fef3c7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: #d97706;">
                        <i class="dashicons dashicons-networking"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a;">3. Webhook Dự Phòng / Nền Tảng Khác (Tùy Chọn)</h3>
                        <p style="margin: 2px 0 0; color: #64748b; font-size: 0.82rem;">Đồng thời bắn JSON sang n8n, Make, Telegram, Lark, hoặc Google Sheets nếu có nhu cầu tích hợp thêm.</p>
                    </div>
                </div>

                <table class="form-table" style="margin: 0;">
                    <tr>
                        <th scope="row" style="width: 220px;"><label for="zalo_webhook_url">Custom Webhook URL</label></th>
                        <td>
                            <input type="url" name="zalo_webhook_url" id="zalo_webhook_url" value="<?php echo esc_attr($config['zalo_webhook_url']); ?>" placeholder="https://hook.eu1.make.com/... hoặc n8n webhook" style="width: 100%; max-width: 680px; height: 38px; border-radius: 6px;" />
                            <p class="description" style="margin-top: 6px;">
                                Để trống nếu bạn chỉ sử dụng Bot Zalo Platform ở Mục 2.
                            </p>
                            <div style="margin-top: 10px;">
                                <button type="button" class="button button-secondary" onclick="testCustomWebhook()">
                                    🔔 Bắn Thử 1 Tin Nhắn Test Custom Webhook
                                </button>
                                <span id="testWebhookResult" style="margin-left: 10px; font-weight: 600;"></span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div>
                <button type="submit" class="button button-primary" style="height: 44px; padding: 0 32px; font-size: 0.98rem; font-weight: 700; background: #0033a3; border-color: #0033a3; border-radius: 8px;">
                    💾 LƯU CẤU HÌNH HỆ THỐNG
                </button>
            </div>
        </form>
    </div>

    <script>
    function copyWebhookUrl() {
        var copyText = document.getElementById("flora_webhook_display");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            alert("✓ Đã sao chép link Webhook: " + copyText.value);
        });
    }

    function setZaloBotWebhook() {
        var botToken = document.getElementById('flora_zalo_bot_token').value.trim();
        var secretToken = document.getElementById('flora_zalo_secret_token').value.trim();
        var statusEl = document.getElementById('zaloBotActionStatus');

        if (!botToken) {
            alert('Vui lòng nhập Zalo Bot Token trước khi kích hoạt Webhook.');
            document.getElementById('flora_zalo_bot_token').focus();
            return;
        }

        statusEl.innerText = '⏳ Đang gửi yêu cầu setWebhook tới Zalo Bot Platform...';
        statusEl.style.color = '#0284c7';

        var fd = new FormData();
        fd.append('action', 'flora_set_zalo_webhook');
        fd.append('bot_token', botToken);
        fd.append('secret_token', secretToken);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: fd
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                statusEl.innerText = '✅ ' + data.data.message;
                statusEl.style.color = '#16a34a';
            } else {
                statusEl.innerText = '❌ Lỗi: ' + (data.data ? data.data.message : 'Không thể kết nối Zalo Bot Platform');
                statusEl.style.color = '#dc2626';
            }
        })
        .catch(function(err) {
            statusEl.innerText = '❌ Lỗi kết nối máy chủ WordPress.';
            statusEl.style.color = '#dc2626';
        });
    }

    function testSendZaloGroupMessage() {
        var botToken = document.getElementById('flora_zalo_bot_token').value.trim();
        var chatId = document.getElementById('flora_zalo_group_chat_id').value.trim();
        var statusEl = document.getElementById('zaloBotActionStatus');

        if (!botToken) {
            alert('Vui lòng nhập Zalo Bot Token.');
            return;
        }
        if (!chatId) {
            alert('Chưa có Group Chat ID! Bạn hãy thêm Bot vào nhóm Zalo và gõ /chatid, hoặc nhập ID zgr-... vào ô trên.');
            return;
        }

        statusEl.innerText = '⏳ Đang bắn tin nhắn thử nghiệm vào nhóm Zalo...';
        statusEl.style.color = '#0284c7';

        var fd = new FormData();
        fd.append('action', 'flora_test_zalo_bot_message');
        fd.append('bot_token', botToken);
        fd.append('chat_id', chatId);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: fd
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                statusEl.innerText = '✅ ' + data.data.message;
                statusEl.style.color = '#16a34a';
            } else {
                statusEl.innerText = '❌ Lỗi: ' + (data.data ? data.data.message : 'Gửi tin nhắn thất bại');
                statusEl.style.color = '#dc2626';
            }
        })
        .catch(function() {
            statusEl.innerText = '❌ Lỗi kết nối mạng.';
            statusEl.style.color = '#dc2626';
        });
    }

    function testCustomWebhook() {
        var urlInput = document.getElementById('zalo_webhook_url');
        var resSpan = document.getElementById('testWebhookResult');
        if (!urlInput.value.trim()) {
            alert('Vui lòng nhập Custom Webhook URL trước khi thử.');
            urlInput.focus();
            return;
        }

        resSpan.innerText = 'Đang gửi...';
        resSpan.style.color = '#0284c7';

        var fd = new FormData();
        fd.append('action', 'flora_test_zalo_webhook');
        fd.append('test_url', urlInput.value.trim());

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: fd
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                resSpan.innerText = '✓ ' + data.data.message;
                resSpan.style.color = '#16a34a';
            } else {
                resSpan.innerText = '❌ Lỗi: ' + (data.data ? data.data.message : 'Không thể kết nối.');
                resSpan.style.color = '#dc2626';
            }
        })
        .catch(function() {
            resSpan.innerText = '❌ Lỗi kết nối máy chủ.';
            resSpan.style.color = '#dc2626';
        });
    }
    </script>
    <?php
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 12. AJAX ACTIONS: KÍCH HOẠT VÀ TEST ZALO BOT PLATFORM
 * ─────────────────────────────────────────────────────────────────────────────
 */

// 1. AJAX: Kích hoạt setWebhook với Zalo Bot Platform
add_action('wp_ajax_flora_set_zalo_webhook', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    $bot_token    = isset($_POST['bot_token']) ? sanitize_text_field(trim($_POST['bot_token'])) : '';
    $secret_token = isset($_POST['secret_token']) ? sanitize_text_field(trim($_POST['secret_token'])) : 'flora2026';
    $webhook_url  = get_stylesheet_directory_uri() . '/api/zalo-webhook.php';

    if (empty($bot_token)) {
        wp_send_json_error(array('message' => 'Bot Token không được để trống.'));
    }

    // Lưu token ngay
    update_option('flora_zalo_bot_token', $bot_token);
    update_option('flora_zalo_secret_token', $secret_token);

    $api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/setWebhook";
    $body = wp_json_encode(array(
        'url'          => $webhook_url,
        'secret_token' => $secret_token
    ));

    $res = wp_remote_post($api_url, array(
        'method'      => 'POST',
        'timeout'     => 15,
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => $body,
        'sslverify'   => false
    ));

    if (is_wp_error($res)) {
        wp_send_json_error(array('message' => $res->get_error_message()));
    }

    $code = wp_remote_retrieve_response_code($res);
    $response_body = wp_remote_retrieve_body($res);
    $json = json_decode($response_body, true);

    if ($code >= 200 && $code < 300) {
        $desc = !empty($json['description']) ? $json['description'] : 'Webhook đã được kích hoạt thành công trên Zalo Bot Platform!';
        wp_send_json_success(array('message' => $desc, 'raw' => $json));
    } else {
        $msg = !empty($json['description']) ? $json['description'] : ('Zalo Bot Platform trả về HTTP code ' . $code . ': ' . $response_body);
        wp_send_json_error(array('message' => $msg));
    }
});

// 2. AJAX: Bắn tin nhắn test trực tiếp vào nhóm Zalo qua Zalo Bot Platform
add_action('wp_ajax_flora_test_zalo_bot_message', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    $bot_token = isset($_POST['bot_token']) ? sanitize_text_field(trim($_POST['bot_token'])) : get_option('flora_zalo_bot_token', '');
    $chat_id   = isset($_POST['chat_id']) ? sanitize_text_field(trim($_POST['chat_id'])) : get_option('flora_zalo_group_chat_id', '');

    if (empty($bot_token) || empty($chat_id)) {
        wp_send_json_error(array('message' => 'Vui lòng cung cấp đầy đủ Bot Token và Group Chat ID.'));
    }

    $test_msg = "👋 [XIN CHÀO TỪ TRỢ LÝ BOT NHA KHOA FLORA!]\n"
              . "━━━━━━━\n"
              . "🤖 Kết nối Zalo Bot Platform với Website Flora thành công rực rỡ!\n"
              . "🏷️ Group ID: " . $chat_id . "\n"
              . "⏰ Thời gian: " . current_time('d/m/Y H:i:s') . "\n"
              . "━━━━━━━\n"
              . "✨ Từ bây giờ, mọi đơn hàng khách chuyển khoản ACB sẽ được thông báo ngay tại đây.\n"
              . "💡 Gõ /pending để xem đơn chờ duyệt, hoặc /help để xem hướng dẫn lệnh.";

    $api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
    $body = wp_json_encode(array(
        'chat_id' => $chat_id,
        'text'    => $test_msg
    ), JSON_UNESCAPED_UNICODE);

    $res = wp_remote_post($api_url, array(
        'method'      => 'POST',
        'timeout'     => 15,
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => $body,
        'sslverify'   => false
    ));

    if (is_wp_error($res)) {
        wp_send_json_error(array('message' => $res->get_error_message()));
    }

    $code = wp_remote_retrieve_response_code($res);
    $response_body = wp_remote_retrieve_body($res);
    $json = json_decode($response_body, true);

    if ($code >= 200 && $code < 300) {
        wp_send_json_success(array('message' => 'Tin nhắn thử nghiệm đã được gửi vào nhóm Zalo thành công!'));
    } else {
        $msg = !empty($json['description']) ? $json['description'] : ('Lỗi gửi tin (HTTP ' . $code . '): ' . $response_body);
        wp_send_json_error(array('message' => $msg));
    }
});

// 3. AJAX: Test Custom Webhook URL
add_action('wp_ajax_flora_test_zalo_webhook', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    $test_url = isset($_POST['test_url']) ? esc_url_raw($_POST['test_url']) : '';
    if (empty($test_url)) {
        wp_send_json_error(array('message' => 'URL webhook trống.'));
    }

    $dummy_order = array(
        'order_code'      => 'FLORA-TEST-' . rand(1000, 9999),
        'customer_name'   => 'Khách Mẫu Thử Nghiệm',
        'customer_phone'  => '0912345678',
        'package_name'    => 'Gói Trồng Răng Toàn Diện (Thử nghiệm)',
        'final_amount'    => 999000,
        'transfer_syntax' => 'FLORA 0912345678 TEST',
        'proof_image_url' => 'https://nhakhoaflora.com/wp-content/uploads/2022/05/cropped-LOGO-FLORA1-3-192x192.png'
    );

    $msg = "🧪 [TIN NHẮN TEST WEBHOOK TỪ NHA KHOA FLORA]\n"
         . "━━━━━━━\n"
         . "Cấu hình Custom Webhook hoạt động hoàn toàn chính xác!\n"
         . "Mã đơn mẫu: " . $dummy_order['order_code'] . "\n"
         . "Khách hàng mẫu: " . $dummy_order['customer_name'] . "\n"
         . "Số tiền mẫu: 999.000 VNĐ\n"
         . "Thời gian test: " . current_time('d/m/Y H:i:s');

    $payload = array(
        'event'                  => 'webhook_test',
        'title'                  => '🧪 [TEST WEBHOOK NHA KHOA FLORA]',
        'message'                => $msg,
        'text'                   => $msg,
        'content'                => $msg,
        'order_code'             => $dummy_order['order_code'],
        'customer_name'          => $dummy_order['customer_name'],
        'customer_phone'         => $dummy_order['customer_phone'],
        'final_amount'           => 999000,
        'final_amount_formatted' => '999.000 VNĐ',
        'image'                  => $dummy_order['proof_image_url'],
        'image_url'              => $dummy_order['proof_image_url']
    );

    $res = wp_remote_post($test_url, array(
        'method'      => 'POST',
        'timeout'     => 10,
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => wp_json_encode($payload, JSON_UNESCAPED_UNICODE)
    ));

    if (is_wp_error($res)) {
        wp_send_json_error(array('message' => $res->get_error_message()));
    }

    $code = wp_remote_retrieve_response_code($res);
    if ($code >= 200 && $code < 300) {
        wp_send_json_success(array('message' => 'Đã gửi thành công (HTTP ' . $code . ')'));
    } else {
        wp_send_json_error(array('message' => 'Máy chủ webhook phản hồi HTTP code: ' . $code));
    }
});
