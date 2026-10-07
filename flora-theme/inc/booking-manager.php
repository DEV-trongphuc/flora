<?php
/**
 * Flora Booking Manager
 * Quản lý cơ sở dữ liệu và danh sách Đăng Ký Đặt Hẹn trong WP-Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Khởi tạo bảng cơ sở dữ liệu wp_flora_bookings & Tự động Migration
 */
function flora_get_booking_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_bookings';
}

function flora_create_booking_table() {
    global $wpdb;
    $table_name = flora_get_booking_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        email varchar(255) DEFAULT '',
        location varchar(255) DEFAULT '',
        gender varchar(20) DEFAULT '',
        service varchar(255) DEFAULT '',
        preferred_time varchar(255) DEFAULT '',
        source_page varchar(500) DEFAULT '',
        status varchar(50) NOT NULL DEFAULT 'new',
        notes text DEFAULT NULL,
        ip_address varchar(100) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY status (status),
        KEY created_at (created_at)
    ) $charset_collate;";

    $wpdb->query($sql);

    // Tự động kiểm tra và thêm các cột nếu bảng cũ thiếu (Migration an toàn)
    $existing_cols = $wpdb->get_col("DESC $table_name", 0);
    if (!empty($existing_cols)) {
        if (!in_array('email', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN email varchar(255) DEFAULT '' AFTER phone");
        }
        if (!in_array('location', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN location varchar(255) DEFAULT '' AFTER email");
        }
        if (!in_array('gender', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN gender varchar(20) DEFAULT '' AFTER location");
        }
        if (!in_array('service', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN service varchar(255) DEFAULT '' AFTER gender");
        }
        if (!in_array('preferred_time', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN preferred_time varchar(255) DEFAULT '' AFTER service");
        }
        if (!in_array('source_page', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN source_page varchar(500) DEFAULT '' AFTER preferred_time");
        }
        if (!in_array('notes', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN notes text DEFAULT NULL AFTER status");
        }
        if (!in_array('ip_address', $existing_cols)) {
            $wpdb->query("ALTER TABLE $table_name ADD COLUMN ip_address varchar(100) DEFAULT '' AFTER notes");
        }
    }

    // Di chuyển dữ liệu cũ từ options nếu có
    $old_leads = get_option('flora_booking_leads', array());
    if (!empty($old_leads) && is_array($old_leads)) {
        $existing_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($existing_count == 0) {
            foreach ($old_leads as $lead) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'name'           => isset($lead['name']) ? $lead['name'] : 'Khách hàng',
                        'phone'          => isset($lead['phone']) ? $lead['phone'] : '',
                        'email'          => isset($lead['email']) ? $lead['email'] : '',
                        'location'       => isset($lead['location']) ? $lead['location'] : '',
                        'gender'         => isset($lead['gender']) ? $lead['gender'] : '',
                        'service'        => isset($lead['service']) ? $lead['service'] : '',
                        'preferred_time' => isset($lead['pref_time']) ? $lead['pref_time'] : '',
                        'source_page'    => 'Dữ liệu trước đó',
                        'status'         => 'new',
                        'notes'          => isset($lead['notes']) ? $lead['notes'] : '',
                        'created_at'     => isset($lead['date']) ? $lead['date'] : current_time('mysql')
                    )
                );
            }
        }
    }
}
add_action('after_switch_theme', 'flora_create_booking_table');
add_action('admin_init', function() {
    global $wpdb;
    $table_name = flora_get_booking_table_name();
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        flora_create_booking_table();
    }
});

/**
 * 2. Tiếp nhận Form đặt hẹn (AJAX Submission & JSON Support)
 */
function flora_handle_booking_submission() {
    // Đọc dữ liệu từ FormData ($_POST) hoặc raw JSON input (fetch body)
    $raw_input = file_get_contents('php://input');
    $json_data = json_decode($raw_input, true);

    $name     = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : (isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : (isset($json_data['fullname']) ? sanitize_text_field($json_data['fullname']) : (isset($json_data['name']) ? sanitize_text_field($json_data['name']) : '')));
    $phone    = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : (isset($_POST['tel']) ? sanitize_text_field($_POST['tel']) : (isset($json_data['phone']) ? sanitize_text_field($json_data['phone']) : (isset($json_data['tel']) ? sanitize_text_field($json_data['tel']) : '')));
    $email    = isset($_POST['email']) ? sanitize_email($_POST['email']) : (isset($json_data['email']) ? sanitize_email($json_data['email']) : '');
    $service  = isset($_POST['service']) ? sanitize_text_field($_POST['service']) : (isset($_POST['toothStatus']) ? sanitize_text_field($_POST['toothStatus']) : (isset($json_data['service']) ? sanitize_text_field($json_data['service']) : (isset($json_data['clinic']) ? sanitize_text_field($json_data['clinic']) : (isset($json_data['interest']) ? sanitize_text_field($json_data['interest']) : ''))));
    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : (isset($_POST['address']) ? sanitize_text_field($_POST['address']) : (isset($_POST['city']) ? sanitize_text_field($_POST['city']) : (isset($json_data['location']) ? sanitize_text_field($json_data['location']) : (isset($json_data['city']) ? sanitize_text_field($json_data['city']) : (isset($json_data['address']) ? sanitize_text_field($json_data['address']) : '')))));
    $gender   = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : (isset($json_data['gender']) ? sanitize_text_field($json_data['gender']) : '');
    $pref_time= isset($_POST['preferredTime']) ? sanitize_text_field($_POST['preferredTime']) : (isset($_POST['pref_time']) ? sanitize_text_field($_POST['pref_time']) : (isset($_POST['timeSlot']) ? sanitize_text_field($_POST['timeSlot']) : (isset($json_data['preferredTime']) ? sanitize_text_field($json_data['preferredTime']) : (isset($json_data['timeSlot']) ? sanitize_text_field($json_data['timeSlot']) : ''))));
    $source   = isset($_POST['source']) ? esc_url_raw($_POST['source']) : (isset($_POST['eventSourceUrl']) ? esc_url_raw($_POST['eventSourceUrl']) : (isset($json_data['eventSourceUrl']) ? esc_url_raw($json_data['eventSourceUrl']) : (wp_get_referer() ?: '')));
    $notes    = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : (isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : (isset($json_data['note']) ? sanitize_textarea_field($json_data['note']) : (isset($json_data['notes']) ? sanitize_textarea_field($json_data['notes']) : '')));

    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập họ tên và số điện thoại liên hệ.'));
    }

    global $wpdb;
    $table_name = flora_get_booking_table_name();

    // Đảm bảo bảng tồn tại trước khi insert
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        flora_create_booking_table();
    }

    // Ghi trực tiếp vào Database wp_flora_bookings
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'name'           => $name,
            'phone'          => $phone,
            'email'          => $email,
            'location'       => $location ?: 'Hồ Chí Minh',
            'gender'         => $gender,
            'service'        => $service ?: 'Tư vấn thăm khám',
            'preferred_time' => $pref_time ?: 'Bất kỳ lúc nào',
            'source_page'    => $source,
            'status'         => 'new',
            'notes'          => $notes,
            'ip_address'     => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
            'created_at'     => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    // Lưu thêm bản sao lưu dự phòng trong Options để đảm bảo an toàn tuyệt đối
    $backup_leads = get_option('flora_booking_leads_backup', array());
    if (!is_array($backup_leads)) $backup_leads = array();
    $backup_leads[] = array(
        'name'           => $name,
        'phone'          => $phone,
        'email'          => $email,
        'location'       => $location,
        'gender'         => $gender,
        'service'        => $service,
        'preferred_time' => $pref_time,
        'source_page'    => $source,
        'notes'          => $notes,
        'date'           => current_time('mysql')
    );
    if (count($backup_leads) > 500) {
        $backup_leads = array_slice($backup_leads, -500);
    }
    update_option('flora_booking_leads_backup', $backup_leads, false);

    if ($inserted === false) {
        wp_send_json_error(array('message' => 'Có lỗi khi lưu lịch hẹn. Vui lòng liên hệ hotline 028 7305 8999 để được hỗ trợ ngay!'));
    }

    // Gửi email thông báo cho Admin nếu máy chủ có cấu hình mail
    $admin_email = get_option('admin_email');
    $subject = "[Flora Đặt Hẹn Mới] $name - $phone (" . ($service ?: 'Tư vấn tổng quát') . ")";
    $body = "HỆ THỐNG NHA KHOA FLORA NHẬN ĐƯỢC YÊU CẦU ĐẶT HẸN:\n\n"
          . "----------------------------------------\n"
          . "Họ và tên: $name\n"
          . "Số điện thoại: $phone\n"
          . "Email: $email\n"
          . "Dịch vụ quan tâm: $service\n"
          . "Khu vực: $location\n"
          . "Giới tính: $gender\n"
          . "Thời gian mong muốn: $pref_time\n"
          . "Ghi chú: $notes\n"
          . "Trang đăng ký: $source\n"
          . "Thời gian gửi: " . current_time('d/m/Y H:i:s') . "\n"
          . "----------------------------------------\n"
          . "Xem chi tiết trong trang quản trị: " . admin_url('admin.php?page=flora-bookings');
    @wp_mail($admin_email, $subject, $body);

    // Gửi thông báo Zalo theo Style Minimalist sạch sẽ
    if (function_exists('flora_send_zalo_lead_notification')) {
        flora_send_zalo_lead_notification(array(
            'name'           => $name,
            'phone'          => $phone,
            'email'          => $email,
            'service'        => $service ?: 'Tư vấn thăm khám',
            'preferred_time' => $pref_time ?: 'Bất kỳ lúc nào',
            'source_page'    => !empty($source) ? $source : 'Website Flora (Đặt Hẹn)',
            'notes'          => $notes ?: 'Khách để lại thông tin đặt hẹn'
        ));
    }

    wp_send_json_success(array(
        'message' => 'Đăng ký đặt hẹn thành công! Đội ngũ y khoa Nha khoa Flora sẽ liên hệ hỗ trợ bạn sớm nhất.',
        'lead_id' => $wpdb->insert_id
    ));
}
add_action('wp_ajax_flora_booking', 'flora_handle_booking_submission');
add_action('wp_ajax_nopriv_flora_booking', 'flora_handle_booking_submission');

/**
 * 3. Đăng ký Menu Admin "Đặt Lịch Hẹn"
 */
function flora_register_booking_admin_menu() {
    global $wpdb;
    $table_name = flora_get_booking_table_name();

    // Đếm số lịch hẹn mới (chưa liên hệ)
    $new_count = 0;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $new_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    }

    $menu_title = 'Đặt Lịch Hẹn';
    if ($new_count > 0) {
        $menu_title .= ' <span class="update-plugins count-' . $new_count . '"><span class="plugin-count">' . $new_count . '</span></span>';
    }

    add_menu_page(
        'Quản Lý Đăng Ký Đặt Hẹn - Nha Khoa Flora',
        $menu_title,
        'manage_options',
        'flora-bookings',
        'flora_render_bookings_admin_page',
        'dashicons-calendar-alt',
        5.1
    );
}
add_action('admin_menu', 'flora_register_booking_admin_menu');

/**
 * 4. Xuất file CSV (Tương thích chuẩn UTF-8 BOM cho Microsoft Excel)
 */
function flora_export_bookings_csv() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'flora-bookings' || !isset($_GET['action']) || $_GET['action'] !== 'export_csv') {
        return;
    }

    if (!current_user_can('manage_options') || !check_admin_referer('flora_export_csv_nonce')) {
        wp_die('Bạn không có quyền thực hiện thao tác này.');
    }

    global $wpdb;
    $table_name = flora_get_booking_table_name();
    $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);

    $filename = 'danh-sach-dat-hen-flora-' . date('Y-m-d-His') . '.csv';

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');
    // Xuất UTF-8 BOM để Excel hiển thị tiếng Việt không bị lỗi font
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Tiêu đề cột
    fputcsv($output, array('ID', 'Họ Và Tên', 'Số Điện Thoại', 'Email', 'Khu Vực', 'Giới Tính', 'Dịch Vụ', 'Giờ Hẹn Mong Muốn', 'Ghi Chú', 'Trạng Thái', 'Trang Đăng Ký', 'Ngày Gửi'));

    $status_map = array(
        'new'        => 'Mới (Chưa liên hệ)',
        'contacted'  => 'Đã liên hệ',
        'completed'  => 'Đã đến khám',
        'cancelled'  => 'Đã hủy'
    );

    foreach ($rows as $row) {
        $status_label = isset($status_map[$row['status']]) ? $status_map[$row['status']] : $row['status'];
        fputcsv($output, array(
            $row['id'],
            $row['name'],
            $row['phone'],
            $row['email'],
            $row['location'],
            $row['gender'],
            $row['service'],
            $row['preferred_time'],
            isset($row['notes']) ? $row['notes'] : '',
            $status_label,
            $row['source_page'],
            $row['created_at']
        ));
    }

    fclose($output);
    exit;
}
add_action('admin_init', 'flora_export_bookings_csv');

/**
 * 5. Giao diện hiển thị danh sách Đặt Hẹn trong WP-Admin
 */
function flora_render_bookings_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = flora_get_booking_table_name();

    // Xử lý đổi trạng thái đơn lẻ
    if (isset($_GET['action']) && $_GET['action'] === 'change_status' && isset($_GET['id']) && isset($_GET['new_status'])) {
        check_admin_referer('flora_status_action');
        $id = (int)$_GET['id'];
        $new_status = sanitize_text_field($_GET['new_status']);
        $wpdb->update($table_name, array('status' => $new_status), array('id' => $id));
        echo '<div class="notice notice-success is-dismissible"><p>Đã cập nhật trạng thái lịch hẹn #' . $id . ' thành công!</p></div>';
    }

    // Xử lý xóa đơn lẻ
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        check_admin_referer('flora_delete_action');
        $id = (int)$_GET['id'];
        $wpdb->delete($table_name, array('id' => $id));
        echo '<div class="notice notice-success is-dismissible"><p>Đã xóa lịch hẹn #' . $id . ' thành công!</p></div>';
    }

    // Xử lý thao tác hàng loạt
    if (isset($_POST['bulk_action']) && !empty($_POST['booking_ids']) && is_array($_POST['booking_ids'])) {
        check_admin_referer('flora_bulk_bookings_action');
        $ids = array_map('intval', $_POST['booking_ids']);
        $ids_str = implode(',', $ids);

        if ($_POST['bulk_action'] === 'delete') {
            $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids_str)");
            echo '<div class="notice notice-success is-dismissible"><p>Đã xóa ' . count($ids) . ' lịch hẹn được chọn.</p></div>';
        } elseif ($_POST['bulk_action'] === 'mark_contacted') {
            $wpdb->query("UPDATE $table_name SET status = 'contacted' WHERE id IN ($ids_str)");
            echo '<div class="notice notice-success is-dismissible"><p>Đã đánh dấu ' . count($ids) . ' lịch hẹn là Đã liên hệ.</p></div>';
        } elseif ($_POST['bulk_action'] === 'mark_completed') {
            $wpdb->query("UPDATE $table_name SET status = 'completed' WHERE id IN ($ids_str)");
            echo '<div class="notice notice-success is-dismissible"><p>Đã đánh dấu ' . count($ids) . ' lịch hẹn là Đã đến khám.</p></div>';
        }
    }

    // Lấy thống kê
    $total_all       = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_new       = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'new'");
    $total_contacted = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'contacted'");
    $total_completed = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'completed'");
    $total_today     = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = CURDATE()");

    // Bộ lọc và tìm kiếm
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
    $search_query  = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where = array('1=1');
    if (!empty($status_filter)) {
        $where[] = $wpdb->prepare("status = %s", $status_filter);
    }
    if (!empty($search_query)) {
        $search_like = '%' . $wpdb->esc_like($search_query) . '%';
        $where[] = $wpdb->prepare("(name LIKE %s OR phone LIKE %s OR email LIKE %s OR service LIKE %s OR location LIKE %s OR notes LIKE %s)", $search_like, $search_like, $search_like, $search_like, $search_like, $search_like);
    }

    $where_sql = implode(' AND ', $where);

    // Phân trang
    $page = isset($_GET['paged']) ? max(1, (int)$_GET['paged']) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    $total_filtered = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE $where_sql");
    $total_pages = ceil($total_filtered / $per_page);

    $items = $wpdb->get_results("SELECT * FROM $table_name WHERE $where_sql ORDER BY created_at DESC LIMIT $offset, $per_page");

    $export_url = wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=export_csv'), 'flora_export_csv_nonce');
    ?>

    <style>
        .flora-booking-wrap {
            margin: 20px 20px 0 2px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        .flora-booking-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            padding: 18px 24px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 1px solid #e2e8f0;
        }
        .flora-booking-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0033a3;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .flora-stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 15px;
            margin-bottom: 24px;
        }
        @media screen and (max-width: 1024px) {
            .flora-stats-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media screen and (max-width: 640px) {
            .flora-stats-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .flora-stat-card {
            background: #ffffff;
            padding: 18px 20px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .flora-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }
        .flora-stat-num {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 3px;
        }
        .flora-stat-label {
            font-size: 0.82rem;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-new { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-contacted { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
        .badge-completed { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }
        .badge-cancelled { background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; }
        .status-pill {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 700;
            text-decoration: none;
        }
        .flora-table-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            overflow: hidden;
        }
        .flora-filter-bar {
            padding: 16px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .flora-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }
        .flora-table th {
            background: #f1f5f9;
            color: #0f172a;
            font-weight: 700;
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        .flora-table td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .flora-table tr:hover td {
            background: #f8fafc;
        }
        .flora-phone-btn {
            color: #0033a3;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .flora-phone-btn:hover {
            color: #0493f1;
            text-decoration: underline;
        }
        .flora-action-btn {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.78rem;
            border-radius: 6px;
            text-decoration: none;
            margin-right: 4px;
            border: 1px solid transparent;
        }
        .btn-call { background: #e0f2fe; color: #0284c7; }
        .btn-del { background: #fee2e2; color: #dc2626; }
        .btn-status { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; cursor: pointer; }
    </style>

    <div class="flora-booking-wrap">
        <div class="flora-booking-header">
            <div>
                <h1><span class="dashicons dashicons-calendar-alt" style="font-size: 1.8rem; height: 1.8rem; width: 1.8rem; color: #0493f1;"></span> Danh Sách Đăng Ký Đặt Hẹn Khám</h1>
                <p style="margin: 4px 0 0; color: #64748b; font-size: 0.88rem;">Hệ thống Nha Khoa Flora — Tiếp nhận thông tin khám & tư vấn 1:1 theo thời gian thực</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo esc_url($export_url); ?>" class="button button-primary" style="background: #16a34a; border-color: #15803d; font-weight: 700;">
                    <span class="dashicons dashicons-download" style="margin-top: 3px;"></span> Xuất File Excel (CSV)
                </a>
            </div>
        </div>

        <!-- Thống kê nhanh (BẮT BUỘC 4 CARD 1 HÀNG) -->
        <div class="flora-stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 24px;">
            <div class="flora-stat-card">
                <div class="flora-stat-icon" style="background: #f1f5f9; color: #334155;">
                    <span class="dashicons dashicons-groups"></span>
                </div>
                <div>
                    <div class="flora-stat-num" style="color: #0f172a;"><?php echo number_format($total_all); ?></div>
                    <div class="flora-stat-label">Tổng Đặt Hẹn</div>
                </div>
            </div>
            <div class="flora-stat-card">
                <div class="flora-stat-icon" style="background: #f1f5f9; color: #334155;">
                    <span class="dashicons dashicons-bell"></span>
                </div>
                <div>
                    <div class="flora-stat-num" style="color: #0f172a;"><?php echo number_format($total_new); ?></div>
                    <div class="flora-stat-label">Chưa Liên Hệ (Mới)</div>
                </div>
            </div>
            <div class="flora-stat-card">
                <div class="flora-stat-icon" style="background: #f1f5f9; color: #334155;">
                    <span class="dashicons dashicons-phone"></span>
                </div>
                <div>
                    <div class="flora-stat-num" style="color: #0f172a;"><?php echo number_format($total_contacted); ?></div>
                    <div class="flora-stat-label">Đã Liên Hệ</div>
                </div>
            </div>
            <div class="flora-stat-card">
                <div class="flora-stat-icon" style="background: #f1f5f9; color: #334155;">
                    <span class="dashicons dashicons-clock"></span>
                </div>
                <div>
                    <div class="flora-stat-num" style="color: #0f172a;"><?php echo number_format($total_today); ?></div>
                    <div class="flora-stat-label">Đăng Ký Hôm Nay</div>
                </div>
            </div>
        </div>

        <!-- Bảng danh sách & Bộ lọc -->
        <div class="flora-table-card">
            <div class="flora-filter-bar">
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <a href="<?php echo admin_url('admin.php?page=flora-bookings'); ?>" class="button <?php echo empty($status_filter) ? 'button-primary' : ''; ?>">Tất Cả (<?php echo $total_all; ?>)</a>
                    <a href="<?php echo admin_url('admin.php?page=flora-bookings&status_filter=new'); ?>" class="button <?php echo $status_filter === 'new' ? 'button-primary' : ''; ?>" style="color: #dc2626;">Mới (<?php echo $total_new; ?>)</a>
                    <a href="<?php echo admin_url('admin.php?page=flora-bookings&status_filter=contacted'); ?>" class="button <?php echo $status_filter === 'contacted' ? 'button-primary' : ''; ?>" style="color: #d97706;">Đã Liên Hệ (<?php echo $total_contacted; ?>)</a>
                    <a href="<?php echo admin_url('admin.php?page=flora-bookings&status_filter=completed'); ?>" class="button <?php echo $status_filter === 'completed' ? 'button-primary' : ''; ?>" style="color: #16a34a;">Đã Đến Khám (<?php echo $total_completed; ?>)</a>
                </div>

                <form method="get" action="<?php echo admin_url('admin.php'); ?>" style="display: flex; gap: 6px;">
                    <input type="hidden" name="page" value="flora-bookings" />
                    <?php if (!empty($status_filter)) : ?>
                        <input type="hidden" name="status_filter" value="<?php echo esc_attr($status_filter); ?>" />
                    <?php endif; ?>
                    <input type="search" name="s" value="<?php echo esc_attr($search_query); ?>" placeholder="Tìm tên, số điện thoại, dịch vụ, ghi chú..." style="width: 280px; border-radius: 6px;" />
                    <button type="submit" class="button"><span class="dashicons dashicons-search" style="margin-top: 3px;"></span> Tìm</button>
                    <?php if (!empty($search_query)) : ?>
                        <a href="<?php echo admin_url('admin.php?page=flora-bookings'); ?>" class="button">Hủy lọc</a>
                    <?php endif; ?>
                </form>
            </div>

            <form method="post">
                <?php wp_nonce_field('flora_bulk_bookings_action'); ?>
                <div class="flora-table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%;">
                    <table class="flora-table" style="min-width: 1260px; width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="width: 36px; padding: 12px 10px;"><input type="checkbox" id="selectAll" onclick="jQuery('input[name=\'booking_ids[]\']').prop('checked', this.checked);" /></th>
                                <th style="width: 50px; white-space: nowrap;">ID</th>
                                <th style="min-width: 180px; white-space: nowrap;">Khách Hàng</th>
                                <th style="min-width: 140px; white-space: nowrap;">Số Điện Thoại</th>
                                <th style="min-width: 170px; white-space: nowrap;">Dịch Vụ Quan Tâm</th>
                                <th style="min-width: 200px; max-width: 320px;">Ghi Chú / Phác Đồ</th>
                                <th style="min-width: 100px; white-space: nowrap;">Khu Vực</th>
                                <th style="min-width: 160px; white-space: nowrap;">Giờ Hẹn</th>
                                <th style="min-width: 110px; white-space: nowrap;">Trạng Thái</th>
                                <th style="min-width: 130px; white-space: nowrap;">Thời Gian Gửi</th>
                                <th style="min-width: 140px; text-align: right; white-space: nowrap;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($items)) : ?>
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 40px; color: #64748b;">
                                        <span class="dashicons dashicons-calendar" style="font-size: 2rem; width: 2rem; height: 2rem; color: #cbd5e1; display: block; margin: 0 auto 10px;"></span>
                                        Không tìm thấy yêu cầu đặt lịch hẹn nào phù hợp.
                                    </td>
                                </tr>
                            <?php else : ?>
                                <?php foreach ($items as $item) : 
                                    $status_class = 'badge-' . $item->status;
                                    $status_text = 'Mới';
                                    if ($item->status === 'contacted') $status_text = 'Đã liên hệ';
                                    elseif ($item->status === 'completed') $status_text = 'Đã đến khám';
                                    elseif ($item->status === 'cancelled') $status_text = 'Đã hủy';
                                ?>
                                    <tr>
                                        <td style="padding: 12px 10px;"><input type="checkbox" name="booking_ids[]" value="<?php echo $item->id; ?>" /></td>
                                        <td style="white-space: nowrap;"><strong>#<?php echo $item->id; ?></strong></td>
                                        <td>
                                            <strong style="color: #0f172a; font-size: 0.95rem;"><?php echo esc_html($item->name); ?></strong>
                                            <?php if (!empty($item->gender)) : ?>
                                                <span style="font-size: 0.78rem; color: #64748b; margin-left: 4px;">(<?php echo esc_html($item->gender); ?>)</span>
                                            <?php endif; ?>
                                            <?php if (!empty($item->email)) : ?>
                                                <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px; word-break: break-all;"><?php echo esc_html($item->email); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <a href="tel:<?php echo esc_attr($item->phone); ?>" class="flora-phone-btn" style="white-space: nowrap;">
                                                <span class="dashicons dashicons-phone" style="font-size: 1rem; width: 1rem; height: 1rem;"></span>
                                                <?php echo esc_html($item->phone); ?>
                                            </a>
                                            <div style="margin-top: 4px;">
                                                <a href="https://zalo.me/<?php echo preg_replace('/[^0-9]/', '', $item->phone); ?>" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem; color: #0068ff; text-decoration: none; font-weight: 700; white-space: nowrap;">
                                                    Chat Zalo &rarr;
                                                </a>
                                            </div>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <span style="background: #f1f5f9; color: #334155; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.82rem; border: 1px solid #e2e8f0; white-space: nowrap; display: inline-block;">
                                                <?php echo !empty($item->service) ? esc_html($item->service) : 'Tư vấn tổng quát'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($item->notes)) : ?>
                                                <div style="font-size: 0.82rem; color: #334155; line-height: 1.45; background: #f8fafc; padding: 6px 10px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                                    <?php echo esc_html($item->notes); ?>
                                                </div>
                                            <?php else : ?>
                                                <span style="color: #94a3b8; font-size: 0.8rem;">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="white-space: nowrap;"><?php echo !empty($item->location) ? esc_html($item->location) : '-'; ?></td>
                                        <td style="white-space: nowrap;">
                                            <strong style="color: #0f172a; font-size: 0.85rem; white-space: nowrap;"><?php echo !empty($item->preferred_time) ? esc_html($item->preferred_time) : '-'; ?></strong>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <span class="status-pill <?php echo $status_class; ?>" style="white-space: nowrap;">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td style="white-space: nowrap;">
                                            <span style="font-size: 0.82rem; color: #64748b; white-space: nowrap;">
                                                <?php echo date('d/m/Y H:i', strtotime($item->created_at)); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right; white-space: nowrap;">
                                            <!-- Đổi trạng thái dropdown nhanh -->
                                            <select onchange="if(this.value) window.location.href=this.value;" style="font-size: 0.78rem; border-radius: 6px; padding: 2px 4px; margin-right: 4px; white-space: nowrap;">
                                                <option value="">Trạng thái &hellip;</option>
                                                <option value="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=change_status&id=' . $item->id . '&new_status=new'), 'flora_status_action'); ?>" <?php selected($item->status, 'new'); ?>>Mới</option>
                                                <option value="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=change_status&id=' . $item->id . '&new_status=contacted'), 'flora_status_action'); ?>" <?php selected($item->status, 'contacted'); ?>>Đã liên hệ</option>
                                                <option value="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=change_status&id=' . $item->id . '&new_status=completed'), 'flora_status_action'); ?>" <?php selected($item->status, 'completed'); ?>>Đã đến khám</option>
                                                <option value="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=change_status&id=' . $item->id . '&new_status=cancelled'), 'flora_status_action'); ?>" <?php selected($item->status, 'cancelled'); ?>>Hủy</option>
                                            </select>

                                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=flora-bookings&action=delete&id=' . $item->id), 'flora_delete_action'); ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn của <?php echo esc_js($item->name); ?>?');" class="flora-action-btn btn-del" title="Xóa lịch hẹn">
                                                <span class="dashicons dashicons-trash" style="font-size: 0.95rem; width: 0.95rem; height: 0.95rem; line-height: 1;"></span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Thao tác hàng loạt & Phân trang -->
                <div style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <select name="bulk_action" style="font-size: 0.85rem; border-radius: 6px;">
                            <option value="">Thao tác hàng loạt &hellip;</option>
                            <option value="mark_contacted">Đánh dấu Đã liên hệ</option>
                            <option value="mark_completed">Đánh dấu Đã đến khám</option>
                            <option value="delete">Xóa đã chọn</option>
                        </select>
                        <button type="submit" class="button" onclick="return confirm('Xác nhận thực hiện thao tác hàng loạt?');">Áp dụng</button>
                    </div>

                    <?php if ($total_pages > 1) : ?>
                        <div class="tablenav-pages">
                            <span class="displaying-num"><?php echo $total_filtered; ?> mục</span>
                            <?php
                            echo paginate_links(array(
                                'base'      => add_query_arg('paged', '%#%'),
                                'format'    => '',
                                'prev_text' => '&laquo;',
                                'next_text' => '&raquo;',
                                'total'     => $total_pages,
                                'current'   => $page
                            ));
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    <?php
}
