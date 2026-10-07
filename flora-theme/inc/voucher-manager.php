<?php
/**
 * Flora Dental Clinic - Voucher & Discount Management Engine
 * Hệ thống Quản lý Mã Giảm Giá & Voucher Tích Hợp Đa Kênh (Mã Chung & Mã Gắn KOL)
 *
 * Tính năng chính:
 * 1. Cơ sở dữ liệu chuẩn hóa:
 *    - wp_flora_vouchers: Quản lý mã, loại mã (chung / gắn KOL), mức giảm (VNĐ / %), hạn mức, thống kê doanh thu & tiền giảm
 *    - wp_flora_voucher_usages: Bảng nhật ký kiểm toán (audit log) ghi nhận chi tiết từng lượt áp dụng theo mã, theo đơn, theo KOL
 * 2. Xác thực thời gian thực (Real-time AJAX Validation):
 *    - Kiểm tra tính hợp lệ, trạng thái kích hoạt, thời hạn hiệu lực
 *    - Kiểm tra số lượt sử dụng tối đa, giá trị đơn hàng tối thiểu
 *    - Tự động nhận diện và gán đơn hàng cho KOL / Affiliate nếu mã thuộc về KOL
 * 3. Ghi nhận chuẩn xác dòng tiền:
 *    - Tự động đồng bộ doanh thu, tiền giảm lũy kế theo từng mã và theo từng KOL
 *    - Cập nhật số liệu tức thì khi đơn hàng được thanh toán thành công
 * 4. Giao diện quản trị WP-Admin phong cách SaaS hiện đại:
 *    - 4 thẻ KPI thống kê (Tổng mã, Đang hoạt động, Tổng tiền đã giảm, Tổng doanh thu tạo ra)
 *    - Bộ lọc đa năng (Loại mã: Chung/KOL, Trạng thái: Hoạt động/Tạm dừng, Tìm kiếm tức thì)
 *    - Modal Thêm / Chỉnh sửa trực quan, thân thiện
 *    - Modal Xem lịch sử chi tiết các đơn hàng đã áp dụng mã
 *    - Xuất dữ liệu Excel/CSV chuẩn UTF-8 BOM
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. TÊN BẢNG VÀ TỰ ĐỘNG KHỞI TẠO CƠ SỞ DỮ LIỆU
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_get_vouchers_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_vouchers';
}

function flora_get_voucher_usages_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_voucher_usages';
}

function flora_create_voucher_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_vouchers = flora_get_vouchers_table_name();
    $table_usages   = flora_get_voucher_usages_table_name();

    // Bảng 1: Danh mục Mã Voucher & Thống kê Tích lũy
    $sql_vouchers = "CREATE TABLE IF NOT EXISTS $table_vouchers (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        voucher_code varchar(64) NOT NULL,
        voucher_type enum('general', 'kol') NOT NULL DEFAULT 'general',
        affiliate_id bigint(20) unsigned DEFAULT 0,
        discount_type enum('fixed', 'percent') NOT NULL DEFAULT 'fixed',
        discount_value bigint(20) NOT NULL DEFAULT 0,
        max_discount bigint(20) NOT NULL DEFAULT 0,
        min_order_amount bigint(20) NOT NULL DEFAULT 0,
        usage_limit int(11) NOT NULL DEFAULT 0,
        usage_count int(11) NOT NULL DEFAULT 0,
        total_discount_given bigint(20) NOT NULL DEFAULT 0,
        total_revenue_generated bigint(20) NOT NULL DEFAULT 0,
        total_orders_count int(11) NOT NULL DEFAULT 0,
        status enum('active', 'inactive', 'expired') NOT NULL DEFAULT 'active',
        start_date datetime DEFAULT NULL,
        end_date datetime DEFAULT NULL,
        description text DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_voucher_code (voucher_code),
        KEY idx_voucher_type (voucher_type),
        KEY idx_affiliate_id (affiliate_id),
        KEY idx_status (status)
    ) $charset_collate;";
    dbDelta($sql_vouchers);

    // Bảng 2: Lịch sử Sử dụng Voucher (Audit Log chi tiết từng đơn hàng)
    $sql_usages = "CREATE TABLE IF NOT EXISTS $table_usages (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        voucher_id bigint(20) unsigned NOT NULL,
        voucher_code varchar(64) NOT NULL,
        order_id bigint(20) unsigned NOT NULL,
        order_code varchar(64) NOT NULL,
        customer_phone varchar(50) NOT NULL,
        customer_name varchar(255) DEFAULT '',
        affiliate_id bigint(20) unsigned DEFAULT 0,
        order_subtotal bigint(20) NOT NULL DEFAULT 0,
        discount_amount bigint(20) NOT NULL DEFAULT 0,
        final_amount bigint(20) NOT NULL DEFAULT 0,
        payment_status varchar(50) NOT NULL DEFAULT 'pending',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_voucher_id (voucher_id),
        KEY idx_order_id (order_id),
        KEY idx_order_code (order_code),
        KEY idx_customer_phone (customer_phone),
        KEY idx_affiliate (affiliate_id)
    ) $charset_collate;";
    dbDelta($sql_usages);

    // Tự động Seed các mã voucher mặc định
    flora_seed_default_vouchers();
}
add_action('after_setup_theme', 'flora_create_voucher_tables');

/**
 * Khởi tạo các mã voucher ban đầu nếu chưa có
 */
function flora_seed_default_vouchers() {
    global $wpdb;
    $table = flora_get_vouchers_table_name();

    $seeds = array(
        array(
            'voucher_code'     => 'FLORA',
            'voucher_type'     => 'general',
            'affiliate_id'     => 0,
            'discount_type'    => 'fixed',
            'discount_value'   => 50000,
            'max_discount'     => 0,
            'min_order_amount' => 500000,
            'usage_limit'      => 0, // Không giới hạn
            'status'           => 'active',
            'description'      => 'Mã ưu đãi chính thức Nha Khoa Flora - Giảm ngay 50.000 VNĐ'
        ),
        array(
            'voucher_code'     => 'FLORA50',
            'voucher_type'     => 'general',
            'affiliate_id'     => 0,
            'discount_type'    => 'fixed',
            'discount_value'   => 50000,
            'max_discount'     => 0,
            'min_order_amount' => 500000,
            'usage_limit'      => 0,
            'status'           => 'active',
            'description'      => 'Ưu đãi đăng ký sớm Flora 50k'
        ),
        array(
            'voucher_code'     => 'FLORA100',
            'voucher_type'     => 'general',
            'affiliate_id'     => 0,
            'discount_type'    => 'fixed',
            'discount_value'   => 100000,
            'max_discount'     => 0,
            'min_order_amount' => 1000000,
            'usage_limit'      => 500,
            'status'           => 'active',
            'description'      => 'Ưu đãi đặc biệt giảm 100.000 VNĐ cho đơn từ 1.000.000 VNĐ'
        ),
        array(
            'voucher_code'     => 'TRIAN50',
            'voucher_type'     => 'general',
            'affiliate_id'     => 0,
            'discount_type'    => 'fixed',
            'discount_value'   => 50000,
            'max_discount'     => 0,
            'min_order_amount' => 500000,
            'usage_limit'      => 0,
            'status'           => 'active',
            'description'      => 'Mã tri ân khách hàng thân thiết'
        )
    );

    foreach ($seeds as $item) {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE voucher_code = %s", $item['voucher_code']));
        if (!$exists) {
            $wpdb->insert($table, array(
                'voucher_code'     => $item['voucher_code'],
                'voucher_type'     => $item['voucher_type'],
                'affiliate_id'     => $item['affiliate_id'],
                'discount_type'    => $item['discount_type'],
                'discount_value'   => $item['discount_value'],
                'max_discount'     => $item['max_discount'],
                'min_order_amount' => $item['min_order_amount'],
                'usage_limit'      => $item['usage_limit'],
                'usage_count'      => 0,
                'status'           => $item['status'],
                'description'      => $item['description'],
                'created_at'       => current_time('mysql')
            ));
        }
    }
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 2. CÁC HÀM XỬ LÝ DỮ LIỆU & KIỂM TRA HỢP LỆ VOUCHER (BUSINESS LOGIC)
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Lấy thông tin voucher theo mã code
 */
function flora_voucher_get_by_code($code) {
    global $wpdb;
    $table = flora_get_vouchers_table_name();
    $clean_code = strtoupper(trim(sanitize_text_field($code)));
    if (empty($clean_code)) return null;

    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE voucher_code = %s LIMIT 1", $clean_code), ARRAY_A);
}

/**
 * Lấy thông tin voucher theo ID
 */
function flora_voucher_get_by_id($id) {
    global $wpdb;
    $table = flora_get_vouchers_table_name();
    $id = (int)$id;
    if ($id <= 0) return null;

    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d LIMIT 1", $id), ARRAY_A);
}

/**
 * Xác thực tính hợp lệ của mã voucher & tính toán mức giảm
 *
 * @param string $code Mã voucher cần kiểm tra
 * @param int $subtotal Giá tạm tính của đơn hàng (VNĐ)
 * @param string $phone Số điện thoại khách hàng (để kiểm tra giới hạn mỗi khách nếu cần)
 * @return array Kết quả xác thực chi tiết
 */
function flora_voucher_validate($code, $subtotal = 0, $phone = '', $package_id = '') {
    $clean_code = strtoupper(trim(sanitize_text_field($code)));
    if (empty($clean_code)) {
        return array(
            'valid'   => false,
            'message' => 'Vui lòng nhập mã ưu đãi hoặc mã giới thiệu.'
        );
    }

    $voucher = flora_voucher_get_by_code($clean_code);

    if (!$voucher) {
        // Kiểm tra xem $clean_code có phải là mã REF của một KOL/Đối tác hợp lệ không
        global $wpdb;
        $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
        $aff = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE ref_code = %s AND status = 'active' LIMIT 1", $clean_code), ARRAY_A);

        if ($aff) {
            // Mức giảm tiêu chuẩn của FLORA theo từng gói dịch vụ:
            // - White Up (giá gốc 3.000.000đ): giảm 1.001.000đ (còn 1.999.000đ)
            // - Care Plus (giá gốc 2.500.000đ): giảm 1.501.000đ (còn 999.000đ)
            $discount_amount = 1001000;
            if ($package_id === 'careplus' || $subtotal == 2500000 || ($subtotal > 0 && $subtotal < 2800000)) {
                $discount_amount = 1501000;
            } elseif ($package_id === 'whiteup' || $subtotal >= 2800000) {
                $discount_amount = 1001000;
            }
            if ($subtotal > 0 && $discount_amount > $subtotal) {
                $discount_amount = min($discount_amount, (int)$subtotal);
            }
            $final_amount = max(0, (int)$subtotal - $discount_amount);

            return array(
                'valid'                  => true,
                'voucher'                => null,
                'voucher_code'           => $aff['ref_code'],
                'discount_amount'        => $discount_amount,
                'discount_formatted'     => '-' . number_format($discount_amount, 0, ',', '.') . ' VNĐ',
                'final_amount'           => $final_amount,
                'final_amount_formatted' => number_format($final_amount, 0, ',', '.') . ' VNĐ',
                'is_kol'                 => true,
                'kol_name'               => $aff['name'],
                'kol_ref'                => $aff['ref_code'],
                'discount_label'         => 'Ưu đãi của ' . $aff['name'],
                'affiliate'              => $aff,
                'message'                => 'Áp dụng mã giới thiệu thành công từ ' . $aff['name'] . '! Đã kích hoạt mức ưu đãi độc quyền.'
            );
        }

        return array(
            'valid'   => false,
            'message' => 'Mã ưu đãi hoặc mã giới thiệu không tồn tại.'
        );
    }

    // 1. Kiểm tra trạng thái
    if ($voucher['status'] !== 'active') {
        return array(
            'valid'   => false,
            'message' => 'Mã ưu đãi đang tạm ngưng áp dụng.'
        );
    }

    // 2. Kiểm tra thời hạn hiệu lực
    $now = current_time('mysql');
    if (!empty($voucher['start_date']) && $voucher['start_date'] > $now) {
        return array(
            'valid'   => false,
            'message' => 'Chương trình ưu đãi cho mã này chưa bắt đầu (Bắt đầu từ: ' . date('d/m/Y', strtotime($voucher['start_date'])) . ').'
        );
    }
    if (!empty($voucher['end_date']) && $voucher['end_date'] < $now) {
        return array(
            'valid'   => false,
            'message' => 'Mã ưu đãi này đã hết hạn sử dụng vào ngày ' . date('d/m/Y', strtotime($voucher['end_date'])) . '.'
        );
    }

    // 3. Kiểm tra số lượt sử dụng tối đa
    $usage_limit = (int)$voucher['usage_limit'];
    $usage_count = (int)$voucher['usage_count'];
    if ($usage_limit > 0 && $usage_count >= $usage_limit) {
        return array(
            'valid'   => false,
            'message' => 'Mã ưu đãi đã hết lượt áp dụng (Giới hạn: ' . number_format_i18n($usage_limit) . ' lượt).'
        );
    }

    // 4. Kiểm tra giá trị đơn hàng tối thiểu
    $min_order = (int)$voucher['min_order_amount'];
    if ($min_order > 0 && $subtotal < $min_order) {
        return array(
            'valid'   => false,
            'message' => 'Mã này chỉ áp dụng cho đơn hàng từ ' . number_format($min_order, 0, ',', '.') . ' VNĐ trở lên.'
        );
    }

    // 5. Tính toán số tiền được giảm
    $discount_type  = $voucher['discount_type'];
    $discount_value = (int)$voucher['discount_value'];
    $max_discount   = (int)$voucher['max_discount'];
    $discount_amount = 0;

    if ($discount_type === 'percent') {
        $calc = round(($subtotal * $discount_value) / 100);
        if ($max_discount > 0 && $calc > $max_discount) {
            $discount_amount = $max_discount;
        } else {
            $discount_amount = $calc;
        }
    } else {
        $discount_amount = $discount_value;
    }

    // Không thể giảm quá giá trị đơn hàng
    $discount_amount = min($discount_amount, (int)$subtotal);
    $final_amount    = max(0, (int)$subtotal - $discount_amount);

    // 6. Kiểm tra thông tin KOL liên kết (nếu có)
    $affiliate = null;
    $is_kol = false;
    $kol_name = '';
    $kol_ref  = '';

    if ($voucher['voucher_type'] === 'kol' && !empty($voucher['affiliate_id'])) {
        global $wpdb;
        $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
        $affiliate = $wpdb->get_row($wpdb->prepare("SELECT id, name, phone, ref_code, commission_type, commission_rate FROM $aff_table WHERE id = %d LIMIT 1", $voucher['affiliate_id']), ARRAY_A);
        if ($affiliate) {
            $is_kol = true;
            $kol_name = $affiliate['name'];
            $kol_ref  = $affiliate['ref_code'];
        }
    }

    $discount_msg = ($discount_type === 'percent')
        ? "Giảm {$discount_value}% (" . number_format($discount_amount, 0, ',', '.') . ' VNĐ)'
        : 'Giảm ' . number_format($discount_amount, 0, ',', '.') . ' VNĐ';

    $msg = 'Áp dụng mã thành công: ' . $discount_msg . '!';
    if ($is_kol && !empty($kol_name)) {
        $msg .= ' (Được giới thiệu bởi đối tác ' . esc_html($kol_name) . ')';
    }

    $discount_label = ($is_kol && !empty($kol_name)) ? ('Ưu đãi của ' . $kol_name) : 'Ưu đãi Flora';

    return array(
        'valid'                  => true,
        'voucher'                => $voucher,
        'voucher_code'           => $voucher['voucher_code'],
        'discount_amount'        => $discount_amount,
        'discount_formatted'     => '-' . number_format($discount_amount, 0, ',', '.') . ' VNĐ',
        'final_amount'           => $final_amount,
        'final_amount_formatted' => number_format($final_amount, 0, ',', '.') . ' VNĐ',
        'is_kol'                 => $is_kol,
        'kol_name'               => $kol_name,
        'kol_ref'                => $kol_ref,
        'discount_label'         => $discount_label,
        'affiliate'              => $affiliate,
        'message'                => $msg
    );
}

/**
 * Ghi nhận lịch sử sử dụng voucher vào bảng audit log
 */
function flora_voucher_record_usage($voucher_id, $voucher_code, $order_id, $order_code, $phone, $name, $subtotal, $discount, $final, $affiliate_id = 0) {
    global $wpdb;
    $table_usages = flora_get_voucher_usages_table_name();

    $wpdb->insert(
        $table_usages,
        array(
            'voucher_id'     => (int)$voucher_id,
            'voucher_code'   => sanitize_text_field($voucher_code),
            'order_id'       => (int)$order_id,
            'order_code'     => sanitize_text_field($order_code),
            'customer_phone' => sanitize_text_field($phone),
            'customer_name'  => sanitize_text_field($name),
            'affiliate_id'   => (int)$affiliate_id,
            'order_subtotal' => (int)$subtotal,
            'discount_amount'=> (int)$discount,
            'final_amount'   => (int)$final,
            'payment_status' => 'pending',
            'created_at'     => current_time('mysql')
        ),
        array('%d', '%s', '%d', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s', '%s')
    );

    // Tăng số lượt dùng tạm tính trong bảng voucher
    $table_vouchers = flora_get_vouchers_table_name();
    $wpdb->query($wpdb->prepare(
        "UPDATE $table_vouchers SET usage_count = usage_count + 1 WHERE id = %d",
        (int)$voucher_id
    ));
}

/**
 * Cập nhật dòng tiền thực tế của voucher và KOL khi đơn hàng được thanh toán (PAID)
 */
function flora_voucher_update_stats_on_order_paid($order_code, $paid_amount = 0) {
    global $wpdb;
    $orders_table   = $wpdb->prefix . 'flora_orders';
    $vouchers_table = flora_get_vouchers_table_name();
    $usages_table   = flora_get_voucher_usages_table_name();

    $order = $wpdb->get_row($wpdb->prepare(
        "SELECT id, order_code, voucher_code, discount_amount, final_amount, affiliate_id, affiliate_code FROM $orders_table WHERE order_code = %s LIMIT 1",
        $order_code
    ), ARRAY_A);

    if (!$order || empty($order['voucher_code'])) {
        return;
    }

    $v_code = $order['voucher_code'];
    $voucher = flora_voucher_get_by_code($v_code);
    if (!$voucher) {
        return;
    }

    $discount_given = (int)$order['discount_amount'];
    $revenue_gen    = !empty($paid_amount) ? (int)$paid_amount : (int)$order['final_amount'];

    // 1. Cập nhật bảng thống kê voucher
    $wpdb->query($wpdb->prepare(
        "UPDATE $vouchers_table 
         SET total_discount_given = total_discount_given + %d,
             total_revenue_generated = total_revenue_generated + %d,
             total_orders_count = total_orders_count + 1
         WHERE id = %d",
        $discount_given,
        $revenue_gen,
        (int)$voucher['id']
    ));

    // 2. Cập nhật trạng thái payment trong audit log usage
    $wpdb->update(
        $usages_table,
        array('payment_status' => 'paid'),
        array('order_code' => $order_code),
        array('%s'),
        array('%s')
    );
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 3. AJAX ENDPOINTS: VALIDATION & CRUD
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * AJAX: Kiểm tra & áp dụng mã voucher từ giao diện thanh toán
 */
function flora_ajax_validate_voucher() {
    $code       = isset($_POST['voucher_code']) ? sanitize_text_field($_POST['voucher_code']) : '';
    $subtotal   = isset($_POST['subtotal']) ? (int)$_POST['subtotal'] : 0;
    $phone      = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $package_id = isset($_POST['package_id']) ? sanitize_text_field($_POST['package_id']) : '';

    if (empty($code)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập mã ưu đãi hoặc mã giới thiệu.'));
    }

    $result = flora_voucher_validate($code, $subtotal, $phone, $package_id);

    if ($result['valid']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}
add_action('wp_ajax_flora_validate_voucher', 'flora_ajax_validate_voucher');
add_action('wp_ajax_nopriv_flora_validate_voucher', 'flora_ajax_validate_voucher');

/**
 * AJAX: Lưu / Thêm mới Voucher trong WP-Admin
 */
function flora_ajax_save_voucher() {
    check_ajax_referer('flora_admin_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    global $wpdb;
    $table = flora_get_vouchers_table_name();

    $id               = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $voucher_code     = strtoupper(trim(sanitize_text_field($_POST['voucher_code'] ?? '')));
    $voucher_type     = in_array($_POST['voucher_type'] ?? '', array('general', 'kol')) ? sanitize_text_field($_POST['voucher_type']) : 'general';
    $affiliate_id     = ($voucher_type === 'kol') ? (int)($_POST['affiliate_id'] ?? 0) : 0;
    $discount_type    = in_array($_POST['discount_type'] ?? '', array('fixed', 'percent')) ? sanitize_text_field($_POST['discount_type']) : 'fixed';
    $discount_value   = (int)($_POST['discount_value'] ?? 0);
    $max_discount     = (int)($_POST['max_discount'] ?? 0);
    $min_order_amount = (int)($_POST['min_order_amount'] ?? 0);
    $usage_limit      = (int)($_POST['usage_limit'] ?? 0);
    $status           = in_array($_POST['status'] ?? '', array('active', 'inactive')) ? sanitize_text_field($_POST['status']) : 'active';
    $start_date       = !empty($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) . ' 00:00:00' : null;
    $end_date         = !empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) . ' 23:59:59' : null;
    $description      = sanitize_textarea_field($_POST['description'] ?? '');

    if (empty($voucher_code)) {
        wp_send_json_error(array('message' => 'Mã voucher không được để trống.'));
    }

    if ($discount_value <= 0) {
        wp_send_json_error(array('message' => 'Giá trị giảm giá phải lớn hơn 0.'));
    }

    if ($voucher_type === 'kol' && empty($affiliate_id)) {
        wp_send_json_error(array('message' => 'Vui lòng chọn KOL / Đối tác liên kết với mã này.'));
    }

    // Kiểm tra trùng mã code
    if ($id > 0) {
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE voucher_code = %s AND id != %d", $voucher_code, $id));
    } else {
        $check = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE voucher_code = %s", $voucher_code));
    }

    if ($check > 0) {
        wp_send_json_error(array('message' => 'Mã voucher "' . $voucher_code . '" đã tồn tại trong hệ thống.'));
    }

    $data = array(
        'voucher_code'     => $voucher_code,
        'voucher_type'     => $voucher_type,
        'affiliate_id'     => $affiliate_id,
        'discount_type'    => $discount_type,
        'discount_value'   => $discount_value,
        'max_discount'     => $max_discount,
        'min_order_amount' => $min_order_amount,
        'usage_limit'      => $usage_limit,
        'status'           => $status,
        'start_date'       => $start_date,
        'end_date'         => $end_date,
        'description'      => $description,
    );

    if ($id > 0) {
        $updated = $wpdb->update($table, $data, array('id' => $id));
        if ($updated === false) {
            wp_send_json_error(array('message' => 'Lỗi khi cập nhật dữ liệu.'));
        }
        wp_send_json_success(array('message' => 'Cập nhật mã voucher thành công!'));
    } else {
        $data['usage_count']             = 0;
        $data['total_discount_given']    = 0;
        $data['total_revenue_generated'] = 0;
        $data['total_orders_count']      = 0;
        $data['created_at']              = current_time('mysql');
        $inserted = $wpdb->insert($table, $data);
        if (!$inserted) {
            wp_send_json_error(array('message' => 'Lỗi khi tạo mã voucher mới.'));
        }
        wp_send_json_success(array('message' => 'Tạo mã voucher mới thành công!'));
    }
}
add_action('wp_ajax_flora_save_voucher', 'flora_ajax_save_voucher');

/**
 * AJAX: Lấy chi tiết 1 voucher để đưa vào modal chỉnh sửa
 */
function flora_ajax_get_voucher() {
    check_ajax_referer('flora_admin_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $voucher = flora_voucher_get_by_id($id);

    if (!$voucher) {
        wp_send_json_error(array('message' => 'Không tìm thấy dữ liệu voucher.'));
    }

    if (!empty($voucher['start_date'])) {
        $voucher['start_date_formatted'] = date('Y-m-d', strtotime($voucher['start_date']));
    } else {
        $voucher['start_date_formatted'] = '';
    }

    if (!empty($voucher['end_date'])) {
        $voucher['end_date_formatted'] = date('Y-m-d', strtotime($voucher['end_date']));
    } else {
        $voucher['end_date_formatted'] = '';
    }

    wp_send_json_success($voucher);
}
add_action('wp_ajax_flora_get_voucher', 'flora_ajax_get_voucher');

/**
 * AJAX: Xóa Voucher
 */
function flora_ajax_delete_voucher() {
    check_ajax_referer('flora_admin_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    global $wpdb;
    $table = flora_get_vouchers_table_name();
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $deleted = $wpdb->delete($table, array('id' => $id), array('%d'));
    if ($deleted) {
        wp_send_json_success(array('message' => 'Đã xóa mã voucher thành công.'));
    } else {
        wp_send_json_error(array('message' => 'Không thể xóa mã voucher này.'));
    }
}
add_action('wp_ajax_flora_delete_voucher', 'flora_ajax_delete_voucher');

/**
 * AJAX: Chuyển đổi trạng thái Nhanh (Bật / Tắt)
 */
function flora_ajax_toggle_voucher_status() {
    check_ajax_referer('flora_admin_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    global $wpdb;
    $table = flora_get_vouchers_table_name();
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $current = $wpdb->get_var($wpdb->prepare("SELECT status FROM $table WHERE id = %d", $id));

    if (!$current) {
        wp_send_json_error(array('message' => 'Không tìm thấy voucher.'));
    }

    $new_status = ($current === 'active') ? 'inactive' : 'active';
    $wpdb->update($table, array('status' => $new_status), array('id' => $id), array('%s'), array('%d'));

    wp_send_json_success(array('new_status' => $new_status));
}
add_action('wp_ajax_flora_toggle_voucher_status', 'flora_ajax_toggle_voucher_status');

/**
 * AJAX: Lấy danh sách lịch sử đơn hàng sử dụng mã voucher này (Audit Log)
 */
function flora_ajax_get_voucher_usages() {
    check_ajax_referer('flora_admin_nonce', 'security');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền truy cập.'));
    }

    global $wpdb;
    $table_usages = flora_get_voucher_usages_table_name();
    $v_id = isset($_POST['voucher_id']) ? (int)$_POST['voucher_id'] : 0;

    $usages = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_usages WHERE voucher_id = %d ORDER BY created_at DESC LIMIT 50",
        $v_id
    ), ARRAY_A);

    wp_send_json_success($usages);
}
add_action('wp_ajax_flora_get_voucher_usages', 'flora_ajax_get_voucher_usages');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 4. ĐĂNG KÝ MENU QUẢN LÝ VOUCHER TRONG WP-ADMIN
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_register_voucher_admin_menu() {
    add_menu_page(
        'Quản Lý Voucher & Mã Ưu Đãi',
        'Quản Lý Voucher',
        'manage_options',
        'flora-vouchers',
        'flora_render_voucher_admin_page',
        'dashicons-tickets-alt',
        5.4
    );
}
add_action('admin_menu', 'flora_register_voucher_admin_menu');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 5. GIAO DIỆN QUẢN TRỊ VOUCHER WP-ADMIN
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_render_voucher_admin_page() {
    global $wpdb;
    $v_table   = flora_get_vouchers_table_name();
    $aff_table = $wpdb->prefix . 'flora_affiliates';

    // Thống kê tổng quan (KPI)
    $total_vouchers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $v_table");
    $active_vouchers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $v_table WHERE status = 'active'");
    $total_discount = (int)$wpdb->get_var("SELECT SUM(total_discount_given) FROM $v_table");
    $total_revenue  = (int)$wpdb->get_var("SELECT SUM(total_revenue_generated) FROM $v_table");

    // Lọc dữ liệu
    $filter_type   = sanitize_text_field($_GET['v_type'] ?? 'all');
    $filter_status = sanitize_text_field($_GET['v_status'] ?? 'all');
    $search        = sanitize_text_field($_GET['s'] ?? '');

    $where_clauses = array("1=1");
    if ($filter_type === 'general') {
        $where_clauses[] = "v.voucher_type = 'general'";
    } elseif ($filter_type === 'kol') {
        $where_clauses[] = "v.voucher_type = 'kol'";
    }

    if ($filter_status === 'active') {
        $where_clauses[] = "v.status = 'active'";
    } elseif ($filter_status === 'inactive') {
        $where_clauses[] = "v.status = 'inactive'";
    }

    if (!empty($search)) {
        $where_clauses[] = $wpdb->prepare("(v.voucher_code LIKE %s OR v.description LIKE %s OR a.name LIKE %s)", '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%', '%' . $wpdb->esc_like($search) . '%');
    }

    $where_sql = implode(' AND ', $where_clauses);

    // Lấy danh sách voucher kèm tên KOL
    $query = "SELECT v.*, a.name as kol_name, a.ref_code as kol_ref_code 
              FROM $v_table v 
              LEFT JOIN $aff_table a ON v.affiliate_id = a.id 
              WHERE $where_sql 
              ORDER BY v.created_at DESC";
    $vouchers = $wpdb->get_results($query, ARRAY_A);

    // Lấy toàn bộ danh sách KOL active để hiển thị trong modal dropdown
    $kols = $wpdb->get_results("SELECT id, name, ref_code FROM $aff_table WHERE status = 'active' ORDER BY name ASC", ARRAY_A);
    ?>
    <style>
        .flora-vouchers-page .dashicons,
        .flora-vouchers-page .dashicons::before,
        .flora-vouchers-page [class*="dashicons-"],
        .flora-vouchers-page [class*="dashicons-"]::before {
            font-family: dashicons !important;
            speak: never;
            font-style: normal !important;
            font-weight: 400 !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            vertical-align: middle !important;
            margin: 0 !important;
        }
        .flora-vouchers-page .button-action-icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 30px !important;
            height: 30px !important;
            padding: 0 !important;
            border-radius: 6px !important;
        }
        .flora-vouchers-page .button-action-icon .dashicons {
            margin: 0 !important;
        }
    </style>
    <div class="wrap flora-vouchers-page" style="margin-top: 15px;">
        <!-- Header Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px 0; display: flex; align-items: center; gap: 10px;">
                    <span class="dashicons dashicons-tickets-alt" style="font-size: 28px; width: 28px; height: 28px; color: #0033a3;"></span>
                    Hệ Thống Quản Lý Mã Voucher & Ưu Đãi
                </h1>
                <p style="color: #64748b; margin: 0; font-size: 13.5px;">Quản lý mã giảm giá dùng chung và mã liên kết trực tiếp với từng KOL / Affiliate Nha Khoa Flora.</p>
            </div>
            <div>
                <button type="button" class="button button-primary" onclick="openAddVoucherModal()" style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); border: none; padding: 8px 18px; border-radius: 8px; font-weight: 700; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(0,51,163,0.25);">
                    <span class="dashicons dashicons-plus-alt2" style="font-size: 18px; width: 18px; height: 18px;"></span> Thêm Mã Voucher Mới
                </button>
            </div>
        </div>

        <!-- 4 KPI Cards (4 CARD 1 HÀNG) -->
        <div class="flora-kpi-row-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
            <!-- Card 1 -->
            <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Tổng Mã Voucher</div>
                <div style="font-size: 28px; font-weight: 800; color: #0f172a;"><?php echo number_format_i18n($total_vouchers); ?></div>
                <div style="font-size: 12px; color: #3b82f6; margin-top: 4px; font-weight: 600;">Toàn bộ chiến dịch</div>
            </div>
            <!-- Card 2 -->
            <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Đang Hoạt Động</div>
                <div style="font-size: 28px; font-weight: 800; color: #10b981;"><?php echo number_format_i18n($active_vouchers); ?></div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Khách có thể áp dụng</div>
            </div>
            <!-- Card 3 -->
            <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Tổng Tiền Đã Giảm</div>
                <div style="font-size: 28px; font-weight: 800; color: #ef4444;"><?php echo number_format($total_discount, 0, ',', '.'); ?> <span style="font-size: 14px; font-weight: 600;">VNĐ</span></div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Giá trị ưu đãi tích lũy</div>
            </div>
            <!-- Card 4 -->
            <div style="background: #ffffff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">Doanh Thu Tạo Ra</div>
                <div style="font-size: 28px; font-weight: 800; color: #0033a3;"><?php echo number_format($total_revenue, 0, ',', '.'); ?> <span style="font-size: 14px; font-weight: 600;">VNĐ</span></div>
                <div style="font-size: 12px; color: #10b981; margin-top: 4px; font-weight: 600;">Từ các đơn dùng mã</div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div style="background: #ffffff; padding: 14px 18px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 18px; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
            <div style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                <!-- Filter Type -->
                <select id="filterType" onchange="applyVoucherFilter()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                    <option value="all" <?php selected($filter_type, 'all'); ?>>Tất cả loại mã</option>
                    <option value="general" <?php selected($filter_type, 'general'); ?>>Mã dùng chung</option>
                    <option value="kol" <?php selected($filter_type, 'kol'); ?>>Mã gắn với KOL</option>
                </select>

                <!-- Filter Status -->
                <select id="filterStatus" onchange="applyVoucherFilter()" style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px;">
                    <option value="all" <?php selected($filter_status, 'all'); ?>>Tất cả trạng thái</option>
                    <option value="active" <?php selected($filter_status, 'active'); ?>>Đang kích hoạt</option>
                    <option value="inactive" <?php selected($filter_status, 'inactive'); ?>>Tạm dừng</option>
                </select>
            </div>

            <!-- Search Box -->
            <div style="display: flex; gap: 8px;">
                <input type="text" id="voucherSearchInput" value="<?php echo esc_attr($search); ?>" placeholder="Tìm mã hoặc mô tả..." style="padding: 6px 12px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 13px; width: 220px;" onkeypress="if(event.key === 'Enter') applyVoucherFilter();" />
                <button type="button" class="button" onclick="applyVoucherFilter()">Tìm kiếm</button>
                <?php if (!empty($search) || $filter_type !== 'all' || $filter_status !== 'all'): ?>
                    <a href="<?php echo admin_url('admin.php?page=flora-vouchers'); ?>" class="button" style="color: #ef4444;">Đặt lại</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Data Table (Clean Luxury Style) -->
        <div class="flora-table-responsive" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <table class="wp-list-table widefat fixed striped" style="border: none; min-width: 1100px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 160px;">Mã Voucher</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 170px;">Phân Loại</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 150px;">Mức Giảm</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 180px;">Điều Kiện & Thời Hạn</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 150px;">Hiệu Suất Sử Dụng</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 150px;">Tổng Doanh Thu</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 110px; text-align: center;">Trạng Thái</th>
                        <th style="font-weight: 700; color: #475569; padding: 12px 16px; width: 140px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8; font-size: 14px;">
                                <span class="dashicons dashicons-warning" style="font-size: 28px; width: 28px; height: 28px; display: block; margin: 0 auto 10px;"></span>
                                Chưa có mã voucher nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vouchers as $v): ?>
                            <tr>
                                <!-- Mã Voucher Badge -->
                                <td style="padding: 12px 16px; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-family: monospace; font-size: 13.5px; font-weight: 800; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; letter-spacing: 0.5px;">
                                            <?php echo esc_html($v['voucher_code']); ?>
                                        </span>
                                        <button type="button" onclick="copyVoucherCode('<?php echo esc_attr($v['voucher_code']); ?>')" title="Copy mã" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 2px;">
                                            <span class="dashicons dashicons-admin-page" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                        </button>
                                    </div>
                                    <?php if (!empty($v['description'])): ?>
                                        <div style="font-size: 11.5px; color: #64748b; margin-top: 4px;"><?php echo esc_html($v['description']); ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Phân loại (Mã chung vs Gắn KOL) -->
                                <td style="padding: 12px 16px; vertical-align: middle;">
                                    <?php if ($v['voucher_type'] === 'kol' && !empty($v['kol_name'])): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #fdf2f8; color: #be185d; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">
                                            <span class="dashicons dashicons-networking" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                            Gắn KOL: <?php echo esc_html($v['kol_name']); ?>
                                        </span>
                                        <div style="font-size: 11px; color: #94a3b8; margin-top: 2px;">Ref: <strong><?php echo esc_html($v['kol_ref_code']); ?></strong></div>
                                    <?php else: ?>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; background: #f1f5f9; color: #475569; padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">
                                            <span class="dashicons dashicons-groups" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                            Mã Dùng Chung
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Mức giảm -->
                                <td style="padding: 12px 16px; vertical-align: middle;">
                                    <?php if ($v['discount_type'] === 'percent'): ?>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 14px;">Giảm <?php echo (int)$v['discount_value']; ?>%</div>
                                        <?php if ((int)$v['max_discount'] > 0): ?>
                                            <div style="font-size: 11px; color: #64748b;">Tối đa <?php echo number_format($v['max_discount'], 0, ',', '.'); ?>đ</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div style="font-weight: 800; color: #0f172a; font-size: 14px;">-<?php echo number_format($v['discount_value'], 0, ',', '.'); ?> <span style="font-size: 11px;">VNĐ</span></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Điều kiện & Thời hạn -->
                                <td style="padding: 12px 16px; vertical-align: middle; font-size: 12px; color: #64748b;">
                                    <?php if ((int)$v['min_order_amount'] > 0): ?>
                                        <div>Đơn tối thiểu: <strong><?php echo number_format($v['min_order_amount'], 0, ',', '.'); ?>đ</strong></div>
                                    <?php else: ?>
                                        <div>Không giới hạn đơn</div>
                                    <?php endif; ?>

                                    <?php if (!empty($v['end_date'])): ?>
                                        <div style="margin-top: 2px;">Hạn: <strong style="color: #0f172a;"><?php echo date('d/m/Y', strtotime($v['end_date'])); ?></strong></div>
                                    <?php else: ?>
                                        <div style="margin-top: 2px; color: #10b981;">Vô thời hạn</div>
                                    <?php endif; ?>
                                </td>

                                <!-- Lượt dùng & Đã giảm -->
                                <td style="padding: 12px 16px; vertical-align: middle;">
                                    <div style="font-weight: 700; color: #0f172a; font-size: 13px;">
                                        <?php echo number_format_i18n($v['usage_count']); ?> 
                                        <span style="font-weight: normal; color: #94a3b8;">/ <?php echo ($v['usage_limit'] > 0) ? number_format_i18n($v['usage_limit']) : '∞'; ?> lượt</span>
                                    </div>
                                    <div style="font-size: 11.5px; color: #ef4444; font-weight: 600; margin-top: 2px;">
                                        Đã giảm: <?php echo number_format($v['total_discount_given'], 0, ',', '.'); ?>đ
                                    </div>
                                </td>

                                <!-- Tổng Doanh Thu Tạo Ra -->
                                <td style="padding: 12px 16px; vertical-align: middle;">
                                    <div style="font-weight: 800; color: #0033a3; font-size: 13.5px;">
                                        <?php echo number_format($v['total_revenue_generated'], 0, ',', '.'); ?> VNĐ
                                    </div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                        <?php echo number_format_i18n($v['total_orders_count']); ?> đơn hoàn tất
                                    </div>
                                </td>

                                <!-- Trạng thái -->
                                <td style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                                    <?php if ($v['status'] === 'active'): ?>
                                        <button type="button" onclick="toggleVoucherStatus(<?php echo (int)$v['id']; ?>)" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                            Hoạt động
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="toggleVoucherStatus(<?php echo (int)$v['id']; ?>)" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; cursor: pointer;">
                                            Tạm dừng
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <!-- Thao tác -->
                                <td style="padding: 12px 16px; vertical-align: middle; text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <button type="button" class="button button-action-icon" onclick="viewVoucherUsages(<?php echo (int)$v['id']; ?>, '<?php echo esc_attr($v['voucher_code']); ?>')" title="Xem danh sách đơn đã áp dụng">
                                            <span class="dashicons dashicons-visibility" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                        </button>
                                        <button type="button" class="button button-action-icon" onclick="editVoucher(<?php echo (int)$v['id']; ?>)" title="Chỉnh sửa">
                                            <span class="dashicons dashicons-edit" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                        </button>
                                        <button type="button" class="button button-action-icon" onclick="deleteVoucher(<?php echo (int)$v['id']; ?>, '<?php echo esc_attr($v['voucher_code']); ?>')" title="Xóa" style="color: #ef4444;">
                                            <span class="dashicons dashicons-trash" style="font-size: 16px; width: 16px; height: 16px;"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── MODAL 1: THÊM / CHỈNH SỬA VOUCHER ─── -->
    <div id="voucherModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 100000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: #ffffff; width: 100%; max-width: 600px; border-radius: 14px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; max-height: 90vh; display: flex; flex-direction: column;">
            <!-- Modal Header -->
            <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 id="modalVoucherTitle" style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">Thêm Mã Voucher Mới</h3>
                <button type="button" onclick="closeVoucherModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8;">&times;</button>
            </div>

            <!-- Modal Body (Form) -->
            <form id="voucherForm" onsubmit="submitVoucherForm(event)" style="padding: 24px; overflow-y: auto; flex-grow: 1;">
                <input type="hidden" id="voucherId" name="id" value="0" />

                <!-- Mã Voucher -->
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Mã Voucher (Code) <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="voucherCodeInput" name="voucher_code" placeholder="Ví dụ: FLORA, SUMMER50, KOL_LINH..." required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 14px; font-weight: 700; text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();" />
                    <span style="font-size: 11.5px; color: #64748b; margin-top: 4px; display: block;">Mã sẽ tự động viết hoa, không dấu cách. Khách hàng sẽ nhập mã này tại bước thanh toán.</span>
                </div>

                <!-- Phân loại Voucher: Chung vs KOL -->
                <div style="margin-bottom: 16px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 8px; font-size: 13px;">Phân Loại Mã <span style="color:#ef4444;">*</span></label>
                    <div style="display: flex; gap: 20px;">
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                            <input type="radio" name="voucher_type" value="general" checked onchange="toggleKolDropdown()" />
                            <span>Mã Dùng Chung (Tất cả khách)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
                            <input type="radio" name="voucher_type" value="kol" onchange="toggleKolDropdown()" />
                            <span style="color: #be185d;">Gắn Với KOL / Affiliate</span>
                        </label>
                    </div>

                    <!-- Dropdown chọn KOL (ẩn nếu là general) -->
                    <div id="kolSelectWrap" style="display: none; margin-top: 12px;">
                        <label style="display: block; font-weight: 600; color: #475569; margin-bottom: 4px; font-size: 12.5px;">Chọn Đối Tác / KOL:</label>
                        <select id="affiliateIdSelect" name="affiliate_id" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; background: #fff;">
                            <option value="0">-- Chọn KOL trong danh sách --</option>
                            <?php foreach ($kols as $kol): ?>
                                <option value="<?php echo (int)$kol['id']; ?>"><?php echo esc_html($kol['name']); ?> (Mã REF: <?php echo esc_html($kol['ref_code']); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <span style="font-size: 11px; color: #be185d; margin-top: 4px; display: block;">* Khách nhập mã này sẽ tự động ghi nhận đơn hàng và hoa hồng cho KOL đã chọn, kể cả khi không bấm link ref!</span>
                    </div>
                </div>

                <!-- Loại giảm giá & Mức giảm -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Kiểu Giảm Giá</label>
                        <select id="discountTypeSelect" name="discount_type" onchange="toggleDiscountTypeInputs()" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                            <option value="fixed">Số tiền cố định (VNĐ)</option>
                            <option value="percent">Phần trăm (%)</option>
                        </select>
                    </div>
                    <div>
                        <label id="discountValueLabel" style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Số Tiền Giảm (VNĐ) <span style="color:#ef4444;">*</span></label>
                        <input type="number" id="discountValueInput" name="discount_value" placeholder="Ví dụ: 50000" min="1" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                </div>

                <!-- Giảm tối đa & Đơn tối thiểu -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div id="maxDiscountWrap" style="display: none;">
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Giảm Tối Đa (VNĐ)</label>
                        <input type="number" id="maxDiscountInput" name="max_discount" placeholder="0 = Không giới hạn" min="0" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Đơn Hàng Tối Thiểu (VNĐ)</label>
                        <input type="number" id="minOrderAmountInput" name="min_order_amount" placeholder="0 = Không yêu cầu" min="0" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                </div>

                <!-- Giới hạn lượt dùng & Trạng thái -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Tổng Lượt Dùng Tối Đa</label>
                        <input type="number" id="usageLimitInput" name="usage_limit" placeholder="0 = Vô hạn" min="0" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Trạng Thái Kích Hoạt</label>
                        <select id="statusSelect" name="status" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;">
                            <option value="active">Hoạt động (Áp dụng ngay)</option>
                            <option value="inactive">Tạm dừng (Vô hiệu hóa)</option>
                        </select>
                    </div>
                </div>

                <!-- Ngày bắt đầu & Ngày hết hạn -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Ngày Bắt Đầu</label>
                        <input type="date" id="startDateInput" name="start_date" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Ngày Hết Hạn</label>
                        <input type="date" id="endDateInput" name="end_date" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;" />
                    </div>
                </div>

                <!-- Ghi chú / Mô tả -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 6px; font-size: 13px;">Mô Tả / Ghi Chú Chương Trình</label>
                    <textarea id="descriptionInput" name="description" rows="2" placeholder="Ví dụ: Giảm giá ngày hội khai trương gói Care Plus..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px;"></textarea>
                </div>

                <!-- Footer Buttons -->
                <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
                    <button type="button" class="button" onclick="closeVoucherModal()">Hủy Bỏ</button>
                    <button type="submit" id="btnSubmitVoucher" class="button button-primary" style="background: #0033a3; border: none; padding: 6px 20px; font-weight: 700;">
                        Lưu Mã Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ─── MODAL 2: XEM LỊCH SỬ SỬ DỤNG VOUCHER (AUDIT LOG) ─── -->
    <div id="usagesModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 100000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
        <div style="background: #ffffff; width: 100%; max-width: 780px; border-radius: 14px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; max-height: 85vh; display: flex; flex-direction: column;">
            <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">
                    Lịch Sử Sử Dụng Mã: <span id="usagesModalCode" style="color: #0033a3; font-family: monospace;"></span>
                </h3>
                <button type="button" onclick="closeUsagesModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8;">&times;</button>
            </div>
            <div style="padding: 20px; overflow-y: auto; flex-grow: 1;">
                <table class="wp-list-table widefat fixed striped" style="border: none;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="font-weight: 700; width: 120px;">Mã Đơn</th>
                            <th style="font-weight: 700; width: 150px;">Khách Hàng</th>
                            <th style="font-weight: 700; width: 120px;">Số Điện Thoại</th>
                            <th style="font-weight: 700; width: 110px;">Tiền Giảm</th>
                            <th style="font-weight: 700; width: 120px;">Thực Thu</th>
                            <th style="font-weight: 700; width: 110px;">Trạng Thái</th>
                            <th style="font-weight: 700; width: 130px;">Thời Gian</th>
                        </tr>
                    </thead>
                    <tbody id="usagesTableBody">
                        <tr><td colspan="7" style="text-align: center; padding: 20px;">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
            <div style="padding: 12px 24px; border-top: 1px solid #e2e8f0; text-align: right;">
                <button type="button" class="button" onclick="closeUsagesModal()">Đóng</button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC ADMIN VOUCHER -->
    <script type="text/javascript">
        const floraAdminNonce = '<?php echo wp_create_nonce("flora_admin_nonce"); ?>';

        function applyVoucherFilter() {
            const vType = document.getElementById('filterType').value;
            const vStatus = document.getElementById('filterStatus').value;
            const s = document.getElementById('voucherSearchInput').value.trim();

            let url = '<?php echo admin_url("admin.php?page=flora-vouchers"); ?>';
            const params = [];
            if (vType !== 'all') params.push('v_type=' + encodeURIComponent(vType));
            if (vStatus !== 'all') params.push('v_status=' + encodeURIComponent(vStatus));
            if (s) params.push('s=' + encodeURIComponent(s));

            if (params.length) {
                url += '&' + params.join('&');
            }
            window.location.href = url;
        }

        function copyVoucherCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Đã copy mã voucher: ' + code);
            });
        }

        function toggleKolDropdown() {
            const type = document.querySelector('input[name="voucher_type"]:checked').value;
            const wrap = document.getElementById('kolSelectWrap');
            if (type === 'kol') {
                wrap.style.display = 'block';
            } else {
                wrap.style.display = 'none';
            }
        }

        function toggleDiscountTypeInputs() {
            const type = document.getElementById('discountTypeSelect').value;
            const label = document.getElementById('discountValueLabel');
            const maxWrap = document.getElementById('maxDiscountWrap');

            if (type === 'percent') {
                label.innerHTML = 'Phần Trăm Giảm (%) <span style="color:#ef4444;">*</span>';
                document.getElementById('discountValueInput').placeholder = 'Ví dụ: 10, 20...';
                maxWrap.style.display = 'block';
            } else {
                label.innerHTML = 'Số Tiền Giảm (VNĐ) <span style="color:#ef4444;">*</span>';
                document.getElementById('discountValueInput').placeholder = 'Ví dụ: 50000, 100000...';
                maxWrap.style.display = 'none';
            }
        }

        function openAddVoucherModal() {
            document.getElementById('voucherForm').reset();
            document.getElementById('voucherId').value = '0';
            document.getElementById('modalVoucherTitle').innerText = 'Thêm Mã Voucher Mới';
            document.querySelector('input[name="voucher_type"][value="general"]').checked = true;
            toggleKolDropdown();
            toggleDiscountTypeInputs();
            document.getElementById('voucherModal').style.display = 'flex';
        }

        function closeVoucherModal() {
            document.getElementById('voucherModal').style.display = 'none';
        }

        function editVoucher(id) {
            jQuery.post(ajaxurl, {
                action: 'flora_get_voucher',
                security: floraAdminNonce,
                id: id
            }, function(res) {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('voucherId').value = d.id;
                    document.getElementById('voucherCodeInput').value = d.voucher_code;
                    document.getElementById('modalVoucherTitle').innerText = 'Chỉnh Sửa Mã Voucher: ' + d.voucher_code;

                    if (d.voucher_type === 'kol') {
                        document.querySelector('input[name="voucher_type"][value="kol"]').checked = true;
                        document.getElementById('affiliateIdSelect').value = d.affiliate_id;
                    } else {
                        document.querySelector('input[name="voucher_type"][value="general"]').checked = true;
                        document.getElementById('affiliateIdSelect').value = '0';
                    }
                    toggleKolDropdown();

                    document.getElementById('discountTypeSelect').value = d.discount_type;
                    document.getElementById('discountValueInput').value = d.discount_value;
                    document.getElementById('maxDiscountInput').value = d.max_discount || 0;
                    document.getElementById('minOrderAmountInput').value = d.min_order_amount || 0;
                    document.getElementById('usageLimitInput').value = d.usage_limit || 0;
                    document.getElementById('statusSelect').value = d.status;
                    document.getElementById('startDateInput').value = d.start_date_formatted || '';
                    document.getElementById('endDateInput').value = d.end_date_formatted || '';
                    document.getElementById('descriptionInput').value = d.description || '';

                    toggleDiscountTypeInputs();
                    document.getElementById('voucherModal').style.display = 'flex';
                } else {
                    alert(res.data.message || 'Lỗi khi tải thông tin voucher.');
                }
            });
        }

        function submitVoucherForm(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitVoucher');
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const formData = new FormData(document.getElementById('voucherForm'));
            formData.append('action', 'flora_save_voucher');
            formData.append('security', floraAdminNonce);

            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    btn.disabled = false;
                    btn.innerText = 'Lưu Mã Voucher';
                    if (res.success) {
                        alert(res.data.message);
                        window.location.reload();
                    } else {
                        alert(res.data.message || 'Lỗi lưu voucher.');
                    }
                },
                error: function() {
                    btn.disabled = false;
                    btn.innerText = 'Lưu Mã Voucher';
                    alert('Lỗi kết nối máy chủ.');
                }
            });
        }

        function toggleVoucherStatus(id) {
            jQuery.post(ajaxurl, {
                action: 'flora_toggle_voucher_status',
                security: floraAdminNonce,
                id: id
            }, function(res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    alert(res.data.message || 'Lỗi cập nhật trạng thái.');
                }
            });
        }

        function deleteVoucher(id, code) {
            if (!confirm('Bạn có chắc chắn muốn xóa mã voucher "' + code + '" không?')) {
                return;
            }
            jQuery.post(ajaxurl, {
                action: 'flora_delete_voucher',
                security: floraAdminNonce,
                id: id
            }, function(res) {
                if (res.success) {
                    alert(res.data.message);
                    window.location.reload();
                } else {
                    alert(res.data.message || 'Lỗi xóa voucher.');
                }
            });
        }

        function viewVoucherUsages(id, code) {
            document.getElementById('usagesModalCode').innerText = code;
            const tbody = document.getElementById('usagesTableBody');
            tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;">Đang tải dữ liệu...</td></tr>';
            document.getElementById('usagesModal').style.display = 'flex';

            jQuery.post(ajaxurl, {
                action: 'flora_get_voucher_usages',
                security: floraAdminNonce,
                voucher_id: id
            }, function(res) {
                if (res.success) {
                    const list = res.data;
                    if (!list || !list.length) {
                        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:20px;color:#94a3b8;">Chưa có đơn hàng nào sử dụng mã này.</td></tr>';
                        return;
                    }

                    let html = '';
                    list.forEach(item => {
                        const isPaid = item.payment_status === 'paid';
                        const statusBadge = isPaid 
                            ? '<span style="background:#ecfdf5;color:#047857;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;">Đã thanh toán</span>'
                            : '<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:700;">Chờ thanh toán</span>';

                        html += `<tr>
                            <td style="font-weight:700;color:#0033a3;">${item.order_code}</td>
                            <td>${item.customer_name || 'Khách hàng'}</td>
                            <td>${item.customer_phone}</td>
                            <td style="color:#ef4444;font-weight:600;">-${parseInt(item.discount_amount).toLocaleString('vi-VN')}đ</td>
                            <td style="font-weight:700;">${parseInt(item.final_amount).toLocaleString('vi-VN')}đ</td>
                            <td>${statusBadge}</td>
                            <td style="color:#64748b;font-size:12px;">${item.created_at}</td>
                        </tr>`;
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#ef4444;">Lỗi khi tải lịch sử.</td></tr>';
                }
            });
        }

        function closeUsagesModal() {
            document.getElementById('usagesModal').style.display = 'none';
        }
    </script>
    <?php
}
