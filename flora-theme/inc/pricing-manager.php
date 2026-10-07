<?php
/**
 * Flora Dental Clinic - Pricing Manager
 * Quản lý Bảng Giá Dịch Vụ trong WP-Admin, lưu trữ Database & Render động
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Dữ liệu Bảng Giá Mặc Định Chuẩn Y Khoa Flora
 */
function flora_default_pricing_data() {
    return array(
        'implant' => array(
            'title' => 'Cấy Ghép Implant Thụy Sĩ',
            'icon'  => 'fa-syringe',
            'tables' => array(
                'single' => array(
                    'title'   => 'Báo Giá Trụ Đơn Lẻ (Đã bao gồm khớp nối Abutment & Mão răng sứ đi kèm)',
                    'headers' => array('Dòng Implant', 'Xuất xứ', 'Giá trọn gói'),
                    'rows'    => array(
                        array('name' => 'Osstem / Dentium', 'origin' => 'Hàn Quốc', 'price' => '16.000.000đ', 'highlight' => 0),
                        array('name' => 'Dentium Superline', 'origin' => 'Mỹ', 'price' => '22.000.000đ', 'highlight' => 0),
                        array('name' => 'Neodent', 'origin' => 'Thụy Sĩ', 'price' => '26.000.000đ', 'highlight' => 0),
                        array('name' => 'Implantswiss', 'origin' => 'Thụy Sĩ', 'price' => '29.000.000đ', 'highlight' => 0),
                        array('name' => 'Straumann SLA', 'origin' => 'Thụy Sĩ', 'price' => '32.000.000đ', 'highlight' => 0),
                        array('name' => 'Nobel Biocare', 'origin' => 'Mỹ / Thụy Điển', 'price' => '35.000.000đ', 'highlight' => 0),
                        array('name' => 'Straumann SLActive (Màng nước)', 'origin' => 'Thụy Sĩ', 'price' => '39.000.000đ', 'highlight' => 1)
                    )
                ),
                'allonx' => array(
                    'title'   => 'Báo Giá Trồng Răng Toàn Hàm (All-on-4 / All-on-6)',
                    'headers' => array('Dòng Implant', 'All-on-4 (4 trụ)', 'All-on-6 (6 trụ)'),
                    'rows'    => array(
                        array('name' => 'Dentium Hàn Quốc', 'price_4' => '150.000.000đ', 'price_6' => '180.000.000đ', 'highlight' => 0),
                        array('name' => 'Dentium USA', 'price_4' => '180.000.000đ', 'price_6' => '240.000.000đ', 'highlight' => 0),
                        array('name' => 'Implantswiss Thụy Sĩ', 'price_4' => '200.000.000đ', 'price_6' => '250.000.000đ', 'highlight' => 0),
                        array('name' => 'Straumann SLA Thụy Sĩ', 'price_4' => '250.000.000đ', 'price_6' => '290.000.000đ', 'highlight' => 0),
                        array('name' => 'Straumann SLActive Thụy Sĩ', 'price_4' => '280.000.000đ', 'price_6' => '320.000.000đ', 'highlight' => 1)
                    )
                )
            )
        ),
        'nieng_rang' => array(
            'title' => 'Chỉnh Nha Niềng Răng',
            'icon'  => 'fa-wand-magic-sparkles',
            'tables' => array(
                'mac_cai' => array(
                    'title'   => 'Niềng Răng Mắc Cài (Trọn gói 2 hàm)',
                    'headers' => array('Phương pháp niềng', 'Giá trọn gói'),
                    'rows'    => array(
                        array('name' => 'Mắc cài kim loại thường', 'price' => '35.000.000đ', 'highlight' => 0),
                        array('name' => 'Mắc cài kim loại tự buộc', 'price' => '45.000.000đ', 'highlight' => 0),
                        array('name' => 'Mắc cài sứ thường', 'price' => '50.000.000đ', 'highlight' => 0),
                        array('name' => 'Mắc cài sứ tự buộc', 'price' => '60.000.000đ', 'highlight' => 0)
                    )
                ),
                'invisalign' => array(
                    'title'   => 'Niềng Khay Trong Suốt Invisalign (Mỹ)',
                    'headers' => array('Gói Invisalign', 'Giá trọn gói'),
                    'rows'    => array(
                        array('name' => 'Invisalign Mức 1 (Express)', 'price' => '59.000.000đ', 'highlight' => 0),
                        array('name' => 'Invisalign Mức 2 (Lite)', 'price' => '79.000.000đ', 'highlight' => 0),
                        array('name' => 'Invisalign Mức 3 (Moderate)', 'price' => '99.000.000đ', 'highlight' => 0),
                        array('name' => 'Invisalign Mức 4 (Comprehensive)', 'price' => '120.000.000đ', 'highlight' => 1)
                    )
                )
            )
        ),
        'rang_su_veneer' => array(
            'title' => 'Răng Sứ Thẩm Mỹ & Mặt Dán Veneer',
            'icon'  => 'fa-tooth',
            'tables' => array(
                'main' => array(
                    'title'   => 'Bảng Giá Răng Sứ & Veneer',
                    'headers' => array('Chất liệu phục hình', 'Đơn vị', 'Giá niêm yết'),
                    'rows'    => array(
                        array('name' => 'Răng sứ kim loại Cr-Co', 'unit' => '1 răng', 'price' => '1.200.000đ', 'highlight' => 0),
                        array('name' => 'Răng sứ Titan', 'unit' => '1 răng', 'price' => '2.500.000đ', 'highlight' => 0),
                        array('name' => 'Răng toàn sứ Zirconia', 'unit' => '1 răng', 'price' => '5.000.000đ', 'highlight' => 0),
                        array('name' => 'Răng toàn sứ E.max ZirCAD / Răng sứ Cercon', 'unit' => '1 răng', 'price' => '6.000.000đ', 'highlight' => 0),
                        array('name' => 'Răng sứ Cercon HT / Răng toàn sứ E.max Press', 'unit' => '1 răng', 'price' => '7.000.000đ', 'highlight' => 0),
                        array('name' => 'Dán sứ Veneer thẩm mỹ E.max (German)', 'unit' => '1 răng', 'price' => '7.000.000đ - 8.000.000đ', 'highlight' => 0),
                        array('name' => 'Răng toàn sứ Lava Plus 3M Mỹ cao cấp', 'unit' => '1 răng', 'price' => '8.000.000đ', 'highlight' => 1)
                    )
                )
            )
        ),
        'cuoi_ho_loi' => array(
            'title' => 'Điều Trị Cười Hở Lợi',
            'icon'  => 'fa-smile',
            'tables' => array(
                'main' => array(
                    'title'   => 'Bảng Giá Phẫu Thuật & Chữa Cười Hở Lợi',
                    'headers' => array('Phương pháp can thiệp', 'Đơn vị', 'Giá trọn gói'),
                    'rows'    => array(
                        array('name' => 'Làm dài thân răng đơn giản', 'unit' => '1 răng', 'price' => '1.000.000đ', 'highlight' => 0),
                        array('name' => 'Làm dài thân răng phức tạp (có mài xương ổ)', 'unit' => '1 răng', 'price' => '2.000.000đ', 'highlight' => 0),
                        array('name' => 'Phẫu thuật định vị lại môi', 'unit' => 'Trọn gói', 'price' => '20.000.000đ', 'highlight' => 0),
                        array('name' => 'Ghép mô liên kết', 'unit' => '1 răng', 'price' => '3.000.000đ', 'highlight' => 0),
                        array('name' => 'Ghép mô liên kết kết hợp biểu mô', 'unit' => '1 răng', 'price' => '4.000.000đ', 'highlight' => 0)
                    )
                )
            )
        ),
        'tong_quat' => array(
            'title' => 'Nha Khoa Tổng Quát',
            'icon'  => 'fa-kit-medical',
            'tables' => array(
                'main' => array(
                    'title'   => 'Bảng Giá Dịch Vụ Cơ Bản & Điều Trị',
                    'headers' => array('Các dịch vụ cơ bản', 'Đơn vị', 'Giá công bố'),
                    'rows'    => array(
                        array('name' => 'Cạo vôi răng và đánh bóng', 'unit' => '1 ca', 'price' => '300.000đ - 500.000đ', 'highlight' => 0),
                        array('name' => 'Trám Composite thẩm mỹ 3M Mỹ', 'unit' => '1 răng', 'price' => '400.000đ', 'highlight' => 0),
                        array('name' => 'Nhổ chân răng / răng lung lay', 'unit' => '1 răng', 'price' => '500.000đ', 'highlight' => 0),
                        array('name' => 'Nhổ răng khôn mọc lệch / mọc ngầm', 'unit' => '1 răng', 'price' => '2.000.000đ - 5.000.000đ', 'highlight' => 0),
                        array('name' => 'Chữa tủy răng một ống tủy', 'unit' => '1 răng', 'price' => '600.000đ', 'highlight' => 0),
                        array('name' => 'Tẩy trắng răng bằng công nghệ Laser tại phòng khám', 'unit' => '1 ca', 'price' => '2.500.000đ', 'highlight' => 0)
                    )
                )
            )
        )
    );
}

/**
 * 2. Lấy dữ liệu Bảng Giá từ Database (Fallback về Mặc Định)
 */
function flora_get_pricing_data() {
    $data = get_option('flora_pricing_data');
    if (empty($data) || !is_array($data)) {
        $default = flora_default_pricing_data();
        update_option('flora_pricing_data', $default);
        return $default;
    }
    return $data;
}

/**
 * 3. Đăng ký Menu Admin Quản Lý Bảng Giá
 */
function flora_pricing_admin_menu() {
    add_menu_page(
        'Quản Lý Bảng Giá',
        'Bảng Giá Flora',
        'manage_options',
        'flora-pricing',
        'flora_render_pricing_admin_page',
        'dashicons-tag',
        25
    );
}
add_action('admin_menu', 'flora_pricing_admin_menu');

/**
 * 4. Xử lý AJAX Lưu Bảng Giá từ Admin
 */
function flora_ajax_save_pricing() {
    check_ajax_referer('flora_pricing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền cập nhật bảng giá.'));
    }

    $pricing_json = isset($_POST['pricing_data']) ? wp_unslash($_POST['pricing_data']) : '';
    if (empty($pricing_json)) {
        wp_send_json_error(array('message' => 'Dữ liệu không hợp lệ.'));
    }

    $pricing_data = json_decode($pricing_json, true);
    if (!is_array($pricing_data)) {
        wp_send_json_error(array('message' => 'Định dạng dữ liệu JSON không đúng.'));
    }

    // Cập nhật option database
    update_option('flora_pricing_data', $pricing_data);

    // Xóa cache LiteSpeed nếu có
    if (defined('LSCWP_V')) {
        do_action('litespeed_purge_all');
    }

    wp_send_json_success(array('message' => 'Đã lưu thay đổi bảng giá thành công và tự động đồng bộ vào tri thức AI!'));
}
add_action('wp_ajax_flora_save_pricing', 'flora_ajax_save_pricing');

/**
 * 5. Xử lý AJAX Khôi Phục Bảng Giá Mặc Định
 */
function flora_ajax_reset_pricing() {
    check_ajax_referer('flora_pricing_nonce', 'nonce');

    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện hành động này.'));
    }

    $default = flora_default_pricing_data();
    update_option('flora_pricing_data', $default);

    if (defined('LSCWP_V')) {
        do_action('litespeed_purge_all');
    }

    wp_send_json_success(array('message' => 'Đã khôi phục bảng giá chuẩn mặc định thành công!', 'data' => $default));
}
add_action('wp_ajax_flora_reset_pricing', 'flora_ajax_reset_pricing');

/**
 * 6. Giao Diện Admin Quản Lý Bảng Giá
 */
function flora_render_pricing_admin_page() {
    $pricing_data = flora_get_pricing_data();
    $ai_config    = flora_get_ai_config();
    $nonce = wp_create_nonce('flora_pricing_nonce');
    ?>
    <div class="wrap flora-pricing-admin-wrap">
        <div class="flora-pricing-header">
            <div class="flora-header-left">
                <h1><span class="dashicons dashicons-tag" style="font-size: 28px; vertical-align: middle; margin-right: 8px; color: #0493f1;"></span> Quản Lý Bảng Giá Dịch Vụ Nha Khoa Flora</h1>
                <p class="description">Chỉnh sửa giá dịch vụ trực tiếp. Mọi thay đổi sẽ được tự động cập nhật toàn bộ website và đào tạo tức thì cho <strong>Bác Sĩ AI (Gemini 3.5 Flash Lite)</strong>.</p>
            </div>
            <div class="flora-header-actions">
                <button type="button" class="button button-secondary" id="btn-reset-pricing" style="margin-right: 10px;">
                    <span class="dashicons dashicons-image-rotate" style="vertical-align: middle;"></span> Khôi phục mặc định
                </button>
                <button type="button" class="button button-primary button-hero" id="btn-save-pricing">
                    <span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Thay Đổi Bảng Giá
                </button>
            </div>
        </div>

        <div id="flora-pricing-notice" class="notice" style="display: none; margin: 15px 0;">
            <p></p>
        </div>

        <div class="flora-pricing-container">
            <!-- Tabs Navigation -->
            <ul class="flora-nav-tabs">
                <li class="active" data-tab="tab-implant"><i class="dashicons dashicons-location-alt"></i> Implant Thụy Sĩ</li>
                <li data-tab="tab-nieng_rang"><i class="dashicons dashicons-art"></i> Chỉnh Nha Niềng Răng</li>
                <li data-tab="tab-rang_su_veneer"><i class="dashicons dashicons-shield"></i> Răng Sứ & Veneer</li>
                <li data-tab="tab-cuoi_ho_loi"><i class="dashicons dashicons-smiley"></i> Cười Hở Lợi</li>
                <li data-tab="tab-tong_quat"><i class="dashicons dashicons-heart"></i> Nha Khoa Tổng Quát</li>
                <li data-tab="tab-ai-config" class="tab-ai-highlight"><i class="dashicons dashicons-superhero-alt"></i> Cài Đặt AI (Gemini 3.5 Flash Lite)</li>
            </ul>

            <div class="flora-tabs-content">
                <!-- 1. TAB IMPLANT -->
                <div class="flora-tab-pane active" id="tab-implant">
                    <h2><i class="dashicons dashicons-arrow-right-alt2" style="color: #0493f1;"></i> Cấy Ghép Implant Thụy Sĩ</h2>
                    
                    <!-- Table 1: Trụ Đơn Lẻ -->
                    <div class="flora-card-box">
                        <h3>1. Báo Giá Trụ Đơn Lẻ (Kèm Abutment & Mão Sứ)</h3>
                        <table class="wp-list-table widefat fixed striped" id="table-implant-single">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Dòng Implant</th>
                                    <th style="width: 25%;">Xuất xứ</th>
                                    <th style="width: 25%;">Giá trọn gói</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $implant_single = $pricing_data['implant']['tables']['single']['rows'] ?? array();
                                foreach ($implant_single as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-origin" value="<?php echo esc_attr($row['origin']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-implant-single" data-type="single" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng trụ Implant
                        </button>
                    </div>

                    <!-- Table 2: Toàn Hàm -->
                    <div class="flora-card-box" style="margin-top: 30px;">
                        <h3>2. Báo Giá Trồng Răng Toàn Hàm (All-on-4 / All-on-6)</h3>
                        <table class="wp-list-table widefat fixed striped" id="table-implant-allonx">
                            <thead>
                                <tr>
                                    <th style="width: 35%;">Dòng Implant</th>
                                    <th style="width: 25%;">All-on-4 (4 trụ)</th>
                                    <th style="width: 25%;">All-on-6 (6 trụ)</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $implant_allonx = $pricing_data['implant']['tables']['allonx']['rows'] ?? array();
                                foreach ($implant_allonx as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price-4" value="<?php echo esc_attr($row['price_4']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price-6" value="<?php echo esc_attr($row['price_6']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-implant-allonx" data-type="allonx" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng toàn hàm
                        </button>
                    </div>
                </div>

                <!-- 2. TAB NIỀNG RĂNG -->
                <div class="flora-tab-pane" id="tab-nieng_rang">
                    <h2><i class="dashicons dashicons-arrow-right-alt2" style="color: #0493f1;"></i> Chỉnh Nha Niềng Răng</h2>
                    
                    <!-- Table 1: Mắc Cài -->
                    <div class="flora-card-box">
                        <h3>1. Niềng Răng Mắc Cài (Trọn gói 2 hàm)</h3>
                        <table class="wp-list-table widefat fixed striped" id="table-nieng-maccai">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Phương pháp niềng</th>
                                    <th style="width: 35%;">Giá trọn gói</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $nieng_maccai = $pricing_data['nieng_rang']['tables']['mac_cai']['rows'] ?? array();
                                foreach ($nieng_maccai as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-nieng-maccai" data-type="single_price" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng mắc cài
                        </button>
                    </div>

                    <!-- Table 2: Invisalign -->
                    <div class="flora-card-box" style="margin-top: 30px;">
                        <h3>2. Niềng Khay Trong Suốt Invisalign (Mỹ)</h3>
                        <table class="wp-list-table widefat fixed striped" id="table-nieng-invisalign">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Gói Invisalign</th>
                                    <th style="width: 35%;">Giá trọn gói</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $nieng_invisalign = $pricing_data['nieng_rang']['tables']['invisalign']['rows'] ?? array();
                                foreach ($nieng_invisalign as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-nieng-invisalign" data-type="single_price" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng gói Invisalign
                        </button>
                    </div>
                </div>

                <!-- 3. TAB RĂNG SỨ & VENEER -->
                <div class="flora-tab-pane" id="tab-rang_su_veneer">
                    <h2><i class="dashicons dashicons-arrow-right-alt2" style="color: #0493f1;"></i> Răng Sứ Thẩm Mỹ & Mặt Dán Veneer</h2>
                    <div class="flora-card-box">
                        <table class="wp-list-table widefat fixed striped" id="table-rang-su">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Chất liệu phục hình</th>
                                    <th style="width: 20%;">Đơn vị</th>
                                    <th style="width: 20%;">Giá niêm yết</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $rang_su_rows = $pricing_data['rang_su_veneer']['tables']['main']['rows'] ?? array();
                                foreach ($rang_su_rows as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-unit" value="<?php echo esc_attr($row['unit'] ?? '1 răng'); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-rang-su" data-type="unit_price" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng răng sứ / Veneer
                        </button>
                    </div>
                </div>

                <!-- 4. TAB CƯỜI HỞ LỢI -->
                <div class="flora-tab-pane" id="tab-cuoi_ho_loi">
                    <h2><i class="dashicons dashicons-arrow-right-alt2" style="color: #0493f1;"></i> Điều Trị Cười Hở Lợi</h2>
                    <div class="flora-card-box">
                        <table class="wp-list-table widefat fixed striped" id="table-cuoi-ho-loi">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Phương pháp can thiệp</th>
                                    <th style="width: 20%;">Đơn vị</th>
                                    <th style="width: 20%;">Giá trọn gói</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $cuoi_ho_loi_rows = $pricing_data['cuoi_ho_loi']['tables']['main']['rows'] ?? array();
                                foreach ($cuoi_ho_loi_rows as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-unit" value="<?php echo esc_attr($row['unit'] ?? '1 răng'); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-cuoi-ho-loi" data-type="unit_price" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng điều trị
                        </button>
                    </div>
                </div>

                <!-- 5. TAB TỔNG QUÁT -->
                <div class="flora-tab-pane" id="tab-tong_quat">
                    <h2><i class="dashicons dashicons-arrow-right-alt2" style="color: #0493f1;"></i> Nha Khoa Tổng Quát</h2>
                    <div class="flora-card-box">
                        <table class="wp-list-table widefat fixed striped" id="table-tong-quat">
                            <thead>
                                <tr>
                                    <th style="width: 45%;">Dịch vụ cơ bản & điều trị</th>
                                    <th style="width: 20%;">Đơn vị</th>
                                    <th style="width: 20%;">Giá công bố</th>
                                    <th style="width: 10%; text-align: center;">Nổi bật</th>
                                    <th style="width: 5%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $tong_quat_rows = $pricing_data['tong_quat']['tables']['main']['rows'] ?? array();
                                foreach ($tong_quat_rows as $index => $row): ?>
                                    <tr>
                                        <td><input type="text" class="regular-text row-name" value="<?php echo esc_attr($row['name']); ?>" /></td>
                                        <td><input type="text" class="regular-text row-unit" value="<?php echo esc_attr($row['unit'] ?? '1 răng'); ?>" /></td>
                                        <td><input type="text" class="regular-text row-price" value="<?php echo esc_attr($row['price']); ?>" /></td>
                                        <td style="text-align: center;"><input type="checkbox" class="row-highlight" <?php checked(!empty($row['highlight'])); ?> /></td>
                                        <td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="button button-secondary btn-add-row" data-target="#table-tong-quat" data-type="unit_price" style="margin-top: 12px;">
                            <span class="dashicons dashicons-plus-alt2" style="vertical-align: middle;"></span> Thêm dòng tổng quát
                        </button>
                    </div>
                </div>

                <!-- 6. TAB CÀI ĐẶT AI -->
                <div class="flora-tab-pane" id="tab-ai-config">
                    <h2><i class="dashicons dashicons-superhero-alt" style="color: #0493f1;"></i> Cấu Hình Trí Tuệ Nhân Tạo & Tri Thức Bác Sĩ AI</h2>
                    
                    <!-- Box 1: Thông tin liên hệ -->
                    <div class="flora-card-box" style="margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #0033a3; font-size: 15px;"><i class="dashicons dashicons-id" style="vertical-align: text-top;"></i> 1. Thông Tin Đại Diện & Liên Hệ</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="flora-ai-doctor-name">Tên Bác Sĩ / Người Tư Vấn</label></th>
                                <td>
                                    <input type="text" id="flora-ai-doctor-name" class="regular-text" value="<?php echo esc_attr($ai_config['doctor_name']); ?>" placeholder="Ví dụ: Bác Sĩ Tư Vấn Flora" style="width: 100%; max-width: 500px;" />
                                    <p class="description">Tên xuất hiện trên tiêu đề chatbox và danh xưng khi AI trò chuyện với bệnh nhân.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flora-ai-clinic-address">Địa Chỉ Phòng Khám</label></th>
                                <td>
                                    <input type="text" id="flora-ai-clinic-address" class="regular-text" value="<?php echo esc_attr($ai_config['clinic_address']); ?>" style="width: 100%; max-width: 500px;" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flora-ai-clinic-hotline">Hotline / Zalo Tư Vấn</label></th>
                                <td>
                                    <input type="text" id="flora-ai-clinic-hotline" class="regular-text" value="<?php echo esc_attr($ai_config['clinic_hotline']); ?>" style="width: 100%; max-width: 500px;" />
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Box 2: Chính sách & Ưu đãi thực tế -->
                    <div class="flora-card-box" style="margin-bottom: 20px; border: 1px solid #e2e8f0;">
                        <h3 style="margin-top: 0; color: #0f172a; font-size: 15px;"><i class="dashicons dashicons-awards" style="vertical-align: text-top; color: #0033a3;"></i> 2. Chính Sách Ưu Đãi & Quyền Lợi Thực Tế (Tuyệt Đối Không Bịa Đặt)</h3>
                        <p style="font-size: 13px; line-height: 1.6; color: #475569; margin-bottom: 8px;">
                            <strong>Quy tắc chống bịa đặt:</strong> AI sẽ <em>CHỈ</em> tư vấn các quyền lợi/ưu đãi được bạn liệt kê dưới đây. Nếu bạn để trống, AI sẽ hoàn toàn không tự bịa bất kỳ ưu đãi miễn phí, máy móc hay chính sách nào (như quét 3D TRIOS miễn phí, chụp CT 3D miễn phí...).
                        </p>
                        <textarea id="flora-ai-clinic-perks" class="large-text" rows="3" placeholder="Nhập các quyền lợi/ưu đãi thực tế nếu có (ví dụ: Hỗ trợ trả góp qua thẻ tín dụng ngân hàng, cam kết bảo hành chính hãng...). Nếu không có ưu đãi đặc thù, hãy để trống."><?php echo esc_textarea($ai_config['clinic_perks']); ?></textarea>
                    </div>

                    <!-- Box 3: Lời chào & Gợi ý nhanh -->
                    <div class="flora-card-box" style="margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #0033a3; font-size: 15px;"><i class="dashicons dashicons-format-chat" style="vertical-align: text-top;"></i> 3. Lời Chào Mở Đầu & Câu Hỏi Gợi Ý Nhanh</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="flora-ai-welcome-msg">Lời Chào Khi Mở Chat</label></th>
                                <td>
                                    <textarea id="flora-ai-welcome-msg" class="large-text" rows="3" style="width: 100%; max-width: 650px;"><?php echo esc_textarea($ai_config['welcome_msg']); ?></textarea>
                                    <p class="description">Tin nhắn đầu tiên Bác Sĩ AI gửi khi người dùng mở khung chat.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">4 Câu Hỏi Gợi Ý Nhanh</th>
                                <td>
                                    <div id="flora-ai-sug-list" style="max-width: 700px;">
                                        <?php 
                                        $sugs = $ai_config['suggestions'];
                                        for ($i = 0; $i < 4; $i++): 
                                            $s_label = $sugs[$i]['label'] ?? '';
                                            $s_query = $sugs[$i]['query'] ?? '';
                                        ?>
                                        <div style="display: flex; gap: 8px; margin-bottom: 8px; align-items: center;">
                                            <span style="font-weight: 700; color: #64748b; width: 20px;"><?php echo $i + 1; ?>.</span>
                                            <input type="text" class="regular-text ai-sug-label" placeholder="Nhãn nút (VD: Mất 1 răng hàm)" value="<?php echo esc_attr($s_label); ?>" style="width: 35%;" />
                                            <input type="text" class="regular-text ai-sug-query" placeholder="Nội dung câu hỏi gửi đến AI" value="<?php echo esc_attr($s_query); ?>" style="width: 60%;" />
                                        </div>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="description">Các nút gợi ý bấm nhanh nằm bên trên ô nhập tin nhắn ngoài website.</p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Box 4: Cấu hình API -->
                    <div class="flora-card-box" style="margin-bottom: 20px;">
                        <h3 style="margin-top: 0; color: #0033a3; font-size: 15px;"><i class="dashicons dashicons-admin-generic" style="vertical-align: text-top;"></i> 4. Cấu Hình Kỹ Thuật AI</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="flora-ai-api-key">Gemini API Key</label></th>
                                <td>
                                    <input type="password" id="flora-ai-api-key" class="regular-text" value="<?php echo esc_attr($ai_config['api_key']); ?>" style="width: 380px; font-family: monospace;" />
                                    <button type="button" class="button" id="btn-toggle-key-visibility"><span class="dashicons dashicons-visibility" style="vertical-align: middle;"></span></button>
                                    <p class="description">API Key được lưu bảo mật trong Database và chỉ gọi qua Backend PHP, tuyệt đối không lộ ở mã nguồn giao diện.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flora-ai-model">Model AI Sử Dụng</label></th>
                                <td>
                                    <select id="flora-ai-model" style="min-width: 280px; font-weight: 600;">
                                        <option value="gemini-3.5-flash-lite" <?php selected($ai_config['model'], 'gemini-3.5-flash-lite'); ?>>gemini-3.5-flash-lite (Khuyên dùng - Siêu tốc & Chuẩn)</option>
                                        <option value="gemini-2.5-flash" <?php selected($ai_config['model'], 'gemini-2.5-flash'); ?>>gemini-2.5-flash</option>
                                        <option value="gemini-2.5-pro" <?php selected($ai_config['model'], 'gemini-2.5-pro'); ?>>gemini-2.5-pro</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flora-ai-custom-instructions">Chỉ Thị Bổ Sung (Tùy chọn)</label></th>
                                <td>
                                    <textarea id="flora-ai-custom-instructions" class="large-text" rows="3" placeholder="Nhập thêm các quy tắc tư vấn riêng của phòng khám nếu cần..."><?php echo esc_textarea($ai_config['custom_instructions']); ?></textarea>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                            <button type="button" class="button button-primary button-large" id="btn-save-ai-only">
                                <span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Riêng Cài Đặt AI
                            </button>
                        </div>
                    </div>

                    <!-- Box 5: Thử nghiệm -->
                    <div class="flora-card-box">
                        <h3 style="margin-top: 0; color: #0033a3; font-size: 15px;"><i class="dashicons dashicons-admin-comments" style="vertical-align: text-top;"></i> 5. Thử Nghiệm Trực Tiếp AI</h3>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; max-width: 750px;">
                            <div style="margin-bottom: 10px;">
                                <input type="text" id="test-ai-input" class="regular-text" placeholder="Ví dụ: Tôi mất 1 răng hàm hoặc Báo giá niềng răng..." style="width: 75%;" />
                                <button type="button" class="button button-primary" id="btn-test-ai-advisor">
                                    <span class="dashicons dashicons-admin-comments" style="vertical-align: middle;"></span> Hỏi Bác Sĩ AI
                                </button>
                            </div>
                            <div id="test-ai-result" style="display: none; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; margin-top: 10px; font-size: 13px; line-height: 1.6; white-space: pre-wrap; max-height: 250px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Styling & Script -->
    <style>
        .flora-pricing-admin-wrap { max-width: 1200px; margin-top: 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
        .flora-pricing-header { display: flex; justify-content: space-between; align-items: center; background: #ffffff; padding: 20px 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px; }
        .flora-pricing-header h1 { margin: 0 0 6px 0; font-size: 20px; font-weight: 700; color: #0033a3; }
        .flora-pricing-header p { margin: 0; color: #64748b; font-size: 13px; }
        
        .flora-nav-tabs { display: flex; gap: 8px; margin: 0; padding: 0; list-style: none; border-bottom: 2px solid #e2e8f0; }
        .flora-nav-tabs li { padding: 12px 20px; background: #f1f5f9; border-radius: 8px 8px 0 0; cursor: pointer; font-weight: 600; color: #475569; display: flex; align-items: center; gap: 6px; border: 1px solid #e2e8f0; border-bottom: none; margin-bottom: -2px; transition: all 0.2s ease; }
        .flora-nav-tabs li:hover { background: #e2e8f0; color: #0033a3; }
        .flora-nav-tabs li.active { background: #ffffff; color: #0493f1; border-top: 3px solid #0493f1; border-bottom: 2px solid #ffffff; font-weight: 700; }
        .flora-nav-tabs li.tab-ai-highlight { color: #8b5cf6; }
        .flora-nav-tabs li.tab-ai-highlight.active { border-top-color: #8b5cf6; color: #8b5cf6; }
        
        .flora-tabs-content { background: #ffffff; padding: 25px; border-radius: 0 0 12px 12px; border: 1px solid #e2e8f0; border-top: none; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
        .flora-tab-pane { display: none; }
        .flora-tab-pane.active { display: block; }
        
        .flora-card-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; }
        .flora-card-box h3 { margin-top: 0; font-size: 16px; color: #0033a3; margin-bottom: 15px; }
        .flora-card-box table input.regular-text { width: 100%; box-sizing: border-box; }
        .btn-remove-row { color: #ef4444 !important; border-color: #fca5a5 !important; }
        .btn-remove-row:hover { background: #ef4444 !important; color: #ffffff !important; }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Tab switching
        $('.flora-nav-tabs li').on('click', function() {
            var targetTab = $(this).data('tab');
            $('.flora-nav-tabs li').removeClass('active');
            $(this).addClass('active');
            $('.flora-tab-pane').removeClass('active');
            $('#' + targetTab).addClass('active');
        });

        // Toggle Key Visibility
        $('#btn-toggle-key-visibility').on('click', function() {
            var $input = $('#flora-ai-api-key');
            var type = $input.attr('type') === 'password' ? 'text' : 'password';
            $input.attr('type', type);
        });

        // Remove row
        $(document).on('click', '.btn-remove-row', function() {
            if (confirm('Bạn có chắc muốn xóa dòng này?')) {
                $(this).closest('tr').remove();
            }
        });

        // Add row
        $('.btn-add-row').on('click', function() {
            var targetTable = $(this).data('target');
            var type = $(this).data('type');
            var html = '';

            if (type === 'single') {
                html = '<tr>' +
                    '<td><input type="text" class="regular-text row-name" value="Dòng Implant mới" /></td>' +
                    '<td><input type="text" class="regular-text row-origin" value="Thụy Sĩ" /></td>' +
                    '<td><input type="text" class="regular-text row-price" value="30.000.000đ" /></td>' +
                    '<td style="text-align: center;"><input type="checkbox" class="row-highlight" /></td>' +
                    '<td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>' +
                '</tr>';
            } else if (type === 'allonx') {
                html = '<tr>' +
                    '<td><input type="text" class="regular-text row-name" value="Dòng toàn hàm mới" /></td>' +
                    '<td><input type="text" class="regular-text row-price-4" value="200.000.000đ" /></td>' +
                    '<td><input type="text" class="regular-text row-price-6" value="250.000.000đ" /></td>' +
                    '<td style="text-align: center;"><input type="checkbox" class="row-highlight" /></td>' +
                    '<td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>' +
                '</tr>';
            } else if (type === 'single_price') {
                html = '<tr>' +
                    '<td><input type="text" class="regular-text row-name" value="Phương pháp / Gói mới" /></td>' +
                    '<td><input type="text" class="regular-text row-price" value="50.000.000đ" /></td>' +
                    '<td style="text-align: center;"><input type="checkbox" class="row-highlight" /></td>' +
                    '<td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>' +
                '</tr>';
            } else if (type === 'unit_price') {
                html = '<tr>' +
                    '<td><input type="text" class="regular-text row-name" value="Dịch vụ mới" /></td>' +
                    '<td><input type="text" class="regular-text row-unit" value="1 răng" /></td>' +
                    '<td><input type="text" class="regular-text row-price" value="1.000.000đ" /></td>' +
                    '<td style="text-align: center;"><input type="checkbox" class="row-highlight" /></td>' +
                    '<td style="text-align: center;"><button type="button" class="button btn-remove-row"><span class="dashicons dashicons-trash"></span></button></td>' +
                '</tr>';
            }

            $(targetTable).find('tbody').append(html);
        });

        // Collect JSON data from DOM
        function collectPricingData() {
            var data = {
                implant: {
                    title: 'Cấy Ghép Implant Thụy Sĩ',
                    icon: 'fa-syringe',
                    tables: {
                        single: {
                            title: 'Báo Giá Trụ Đơn Lẻ (Đã bao gồm khớp nối Abutment & Mão răng sứ đi kèm)',
                            headers: ['Dòng Implant', 'Xuất xứ', 'Giá trọn gói'],
                            rows: []
                        },
                        allonx: {
                            title: 'Báo Giá Trồng Răng Toàn Hàm (All-on-4 / All-on-6)',
                            headers: ['Dòng Implant', 'All-on-4 (4 trụ)', 'All-on-6 (6 trụ)'],
                            rows: []
                        }
                    }
                },
                nieng_rang: {
                    title: 'Chỉnh Nha Niềng Răng',
                    icon: 'fa-wand-magic-sparkles',
                    tables: {
                        mac_cai: {
                            title: 'Niềng Răng Mắc Cài (Trọn gói 2 hàm)',
                            headers: ['Phương pháp niềng', 'Giá trọn gói'],
                            rows: []
                        },
                        invisalign: {
                            title: 'Niềng Khay Trong Suốt Invisalign (Mỹ)',
                            headers: ['Gói Invisalign', 'Giá trọn gói'],
                            rows: []
                        }
                    }
                },
                rang_su_veneer: {
                    title: 'Răng Sứ Thẩm Mỹ & Mặt Dán Veneer',
                    icon: 'fa-tooth',
                    tables: {
                        main: {
                            title: 'Bảng Giá Răng Sứ & Veneer',
                            headers: ['Chất liệu phục hình', 'Đơn vị', 'Giá niêm yết'],
                            rows: []
                        }
                    }
                },
                cuoi_ho_loi: {
                    title: 'Điều Trị Cười Hở Lợi',
                    icon: 'fa-smile',
                    tables: {
                        main: {
                            title: 'Bảng Giá Phẫu Thuật & Chữa Cười Hở Lợi',
                            headers: ['Phương pháp can thiệp', 'Đơn vị', 'Giá trọn gói'],
                            rows: []
                        }
                    }
                },
                tong_quat: {
                    title: 'Nha Khoa Tổng Quát',
                    icon: 'fa-kit-medical',
                    tables: {
                        main: {
                            title: 'Bảng Giá Dịch Vụ Cơ Bản & Điều Trị',
                            headers: ['Các dịch vụ cơ bản', 'Đơn vị', 'Giá công bố'],
                            rows: []
                        }
                    }
                }
            };

            // 1. Implant single
            $('#table-implant-single tbody tr').each(function() {
                data.implant.tables.single.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    origin: $(this).find('.row-origin').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 2. Implant allonx
            $('#table-implant-allonx tbody tr').each(function() {
                data.implant.tables.allonx.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    price_4: $(this).find('.row-price-4').val().trim(),
                    price_6: $(this).find('.row-price-6').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 3. Niềng mắc cài
            $('#table-nieng-maccai tbody tr').each(function() {
                data.nieng_rang.tables.mac_cai.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 4. Niềng invisalign
            $('#table-nieng-invisalign tbody tr').each(function() {
                data.nieng_rang.tables.invisalign.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 5. Răng sứ
            $('#table-rang-su tbody tr').each(function() {
                data.rang_su_veneer.tables.main.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    unit: $(this).find('.row-unit').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 6. Cười hở lợi
            $('#table-cuoi-ho-loi tbody tr').each(function() {
                data.cuoi_ho_loi.tables.main.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    unit: $(this).find('.row-unit').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            // 7. Tổng quát
            $('#table-tong-quat tbody tr').each(function() {
                data.tong_quat.tables.main.rows.push({
                    name: $(this).find('.row-name').val().trim(),
                    unit: $(this).find('.row-unit').val().trim(),
                    price: $(this).find('.row-price').val().trim(),
                    highlight: $(this).find('.row-highlight').is(':checked') ? 1 : 0
                });
            });

            return data;
        }

        // Thu thập cấu hình AI
        function collectAiConfig() {
            var suggestions = [];
            $('#flora-ai-sug-list > div').each(function() {
                var label = $(this).find('.ai-sug-label').val().trim();
                var query = $(this).find('.ai-sug-query').val().trim();
                if (label) {
                    suggestions.push({ label: label, query: query || label });
                }
            });

            return {
                api_key: $('#flora-ai-api-key').val().trim(),
                model: $('#flora-ai-model').val(),
                doctor_name: $('#flora-ai-doctor-name').val().trim(),
                clinic_address: $('#flora-ai-clinic-address').val().trim(),
                clinic_hotline: $('#flora-ai-clinic-hotline').val().trim(),
                clinic_perks: $('#flora-ai-clinic-perks').val().trim(),
                welcome_msg: $('#flora-ai-welcome-msg').val().trim(),
                custom_instructions: $('#flora-ai-custom-instructions').val().trim(),
                suggestions: suggestions
            };
        }

        // Lưu riêng cài đặt AI
        $('#btn-save-ai-only').on('click', function() {
            var $btn = $(this);
            var aiConfig = collectAiConfig();
            $btn.prop('disabled', true).text('Đang lưu...');
            $('#flora-pricing-notice').hide();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $.extend({
                    action: 'flora_save_ai_config',
                    nonce: '<?php echo $nonce; ?>'
                }, aiConfig),
                success: function(response) {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Riêng Cài Đặt AI');
                    $('#flora-pricing-notice').removeClass('notice-error').addClass('notice-success').show().find('p').text(response.data.message || 'Đã lưu cấu hình AI thành công!');
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Riêng Cài Đặt AI');
                    $('#flora-pricing-notice').removeClass('notice-success').addClass('notice-error').show().find('p').text('Có lỗi xảy ra khi lưu cấu hình AI.');
                }
            });
        });

        // Save action
        $('#btn-save-pricing').on('click', function() {
            var $btn = $(this);
            var pricingData = collectPricingData();
            var aiConfig = collectAiConfig();

            $btn.prop('disabled', true).text('Đang lưu dữ liệu...');
            $('#flora-pricing-notice').hide();

            // Save pricing data
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'flora_save_pricing',
                    nonce: '<?php echo $nonce; ?>',
                    pricing_data: JSON.stringify(pricingData)
                },
                success: function(response) {
                    // Save AI Config simultaneously
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: $.extend({
                            action: 'flora_save_ai_config',
                            nonce: '<?php echo $nonce; ?>'
                        }, aiConfig)
                    });

                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Thay Đổi Bảng Giá');
                    $('#flora-pricing-notice').removeClass('notice-error').addClass('notice-success').show().find('p').text(response.data.message || 'Đã lưu thành công bảng giá và đồng bộ tri thức AI!');
                    $('html, body').animate({ scrollTop: 0 }, 'fast');
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-saved" style="vertical-align: middle;"></span> Lưu Thay Đổi Bảng Giá');
                    $('#flora-pricing-notice').removeClass('notice-success').addClass('notice-error').show().find('p').text('Có lỗi xảy ra khi lưu bảng giá.');
                }
            });
        });

        // Reset action
        $('#btn-reset-pricing').on('click', function() {
            if (!confirm('Bạn có chắc muốn khôi phục toàn bộ bảng giá về thiết lập chuẩn ban đầu? Mọi tùy chỉnh hiện tại sẽ bị ghi đè.')) {
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'flora_reset_pricing',
                    nonce: '<?php echo $nonce; ?>'
                },
                success: function(response) {
                    alert('Đã khôi phục bảng giá mặc định!');
                    location.reload();
                }
            });
        });

        // Test AI Advisor
        $('#btn-test-ai-advisor').on('click', function() {
            var prompt = $('#test-ai-input').val().trim();
            if (!prompt) {
                alert('Vui lòng nhập câu hỏi thử nghiệm.');
                return;
            }

            var $btn = $(this);
            var $res = $('#test-ai-result');

            $btn.prop('disabled', true).text('AI Đang suy nghĩ...');
            $res.show().text('Đang kết nối Gemini 3.5 Flash Lite và phân tích dữ liệu bảng giá...');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'flora_ai_diagnose',
                    nonce: '<?php echo wp_create_nonce('flora_ai_nonce'); ?>',
                    message: prompt
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-comments" style="vertical-align: middle;"></span> Hỏi Bác Sĩ AI');
                    if (response.success) {
                        $res.text(response.data.reply);
                    } else {
                        $res.text('Lỗi AI: ' + (response.data.message || 'Không thể phản hồi.'));
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-admin-comments" style="vertical-align: middle;"></span> Hỏi Bác Sĩ AI');
                    $res.text('Lỗi kết nối máy chủ.');
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * 7. Helper Render Bảng Giá Động cho Frontend
 */
function flora_render_pricing_card_implant() {
    $data = flora_get_pricing_data();
    $implant = $data['implant'] ?? array();
    $single = $implant['tables']['single'] ?? array();
    $allonx = $implant['tables']['allonx'] ?? array();
    ?>
    <div class="pricing-card">
        <h3 class="pricing-card-title"><i class="fa-solid fa-syringe" style="margin-right: 8px; color: var(--clr-secondary);"></i> <?php echo esc_html($implant['title'] ?? 'Cấy Ghép Implant Thụy Sĩ'); ?></h3>
        
        <h4 class="pricing-card-subtitle"><?php echo esc_html($single['title'] ?? 'Báo Giá Trụ Đơn Lẻ'); ?></h4>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Dòng Implant</th>
                    <th>Xuất xứ</th>
                    <th style="text-align: right;">Giá trọn gói</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($single['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td><?php echo esc_html($row['origin'] ?? ''); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4 class="pricing-card-subtitle"><?php echo esc_html($allonx['title'] ?? 'Báo Giá Trồng Răng Toàn Hàm (All-on-4 / All-on-6)'); ?></h4>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Dòng Implant</th>
                    <th style="text-align: center;">All-on-4 (4 trụ)</th>
                    <th style="text-align: center;">All-on-6 (6 trụ)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allonx['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>" style="text-align: center;"><?php echo esc_html($row['price_4']); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>" style="text-align: center;"><?php echo esc_html($row['price_6']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function flora_render_pricing_card_braces() {
    $data = flora_get_pricing_data();
    $nieng_rang = $data['nieng_rang'] ?? array();
    $mac_cai = $nieng_rang['tables']['mac_cai'] ?? array();
    $invisalign = $nieng_rang['tables']['invisalign'] ?? array();
    ?>
    <div class="pricing-card">
        <h3 class="pricing-card-title"><i class="fa-solid fa-wand-magic-sparkles" style="margin-right: 8px; color: var(--clr-secondary);"></i> <?php echo esc_html($nieng_rang['title'] ?? 'Chỉnh Nha Niềng Răng'); ?></h3>
        
        <h4 class="pricing-card-subtitle"><?php echo esc_html($mac_cai['title'] ?? 'Niềng Răng Mắc Cài (Trọn gói 2 hàm)'); ?></h4>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Phương pháp niềng</th>
                    <th style="text-align: right;">Giá trọn gói</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mac_cai['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h4 class="pricing-card-subtitle"><?php echo esc_html($invisalign['title'] ?? 'Niềng Khay Trong Suốt Invisalign (Mỹ)'); ?></h4>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Gói Invisalign</th>
                    <th style="text-align: right;">Giá trọn gói</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invisalign['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function flora_render_pricing_card_porcelain_veneer() {
    $data = flora_get_pricing_data();
    $cat = $data['rang_su_veneer'] ?? array();
    $main = $cat['tables']['main'] ?? array();
    ?>
    <div class="pricing-card">
        <h3 class="pricing-card-title"><i class="fa-solid fa-tooth" style="margin-right: 8px; color: var(--clr-secondary);"></i> <?php echo esc_html($cat['title'] ?? 'Răng Sứ Thẩm Mỹ & Mặt Dán Veneer'); ?></h3>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Chất liệu phục hình</th>
                    <th>Đơn vị</th>
                    <th style="text-align: right;">Giá niêm yết</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($main['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-unit"><?php echo esc_html($row['unit'] ?? '1 răng'); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function flora_render_pricing_card_gummy_smile() {
    $data = flora_get_pricing_data();
    $cat = $data['cuoi_ho_loi'] ?? array();
    $main = $cat['tables']['main'] ?? array();
    ?>
    <div class="pricing-card">
        <h3 class="pricing-card-title"><i class="fa-solid fa-smile" style="margin-right: 8px; color: var(--clr-secondary);"></i> <?php echo esc_html($cat['title'] ?? 'Điều Trị Cười Hở Lợi'); ?></h3>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Phương pháp can thiệp</th>
                    <th>Đơn vị</th>
                    <th style="text-align: right;">Giá trọn gói</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($main['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-unit"><?php echo esc_html($row['unit'] ?? '1 răng'); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

function flora_render_pricing_card_general() {
    $data = flora_get_pricing_data();
    $cat = $data['tong_quat'] ?? array();
    $main = $cat['tables']['main'] ?? array();
    ?>
    <div class="pricing-card">
        <h3 class="pricing-card-title"><i class="fa-solid fa-kit-medical" style="margin-right: 8px; color: var(--clr-secondary);"></i> <?php echo esc_html($cat['title'] ?? 'Nha Khoa Tổng Quát'); ?></h3>
        <p style="font-size: 0.9rem; color: var(--clr-text-muted); margin-bottom: 15px;">Xem đầy đủ bảng giá và chẩn đoán chi tiết tại trang <a href="<?php echo esc_url(home_url('/dieu-tri-nha-khoa-tong-quat/')); ?>" style="color: var(--clr-secondary); font-weight: bold; text-decoration: underline;">Nha Khoa Tổng Quát</a>.</p>
        <table class="premium-price-table">
            <thead>
                <tr>
                    <th>Các dịch vụ cơ bản</th>
                    <th>Đơn vị</th>
                    <th style="text-align: right;">Giá công bố</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($main['rows'] ?? array() as $row): 
                    $is_hl = !empty($row['highlight']);
                ?>
                <tr>
                    <td class="service-name" <?php if ($is_hl) echo 'style="color: var(--clr-secondary); font-weight: 700;"'; ?>><?php echo esc_html($row['name']); ?></td>
                    <td class="service-unit"><?php echo esc_html($row['unit'] ?? '1 răng'); ?></td>
                    <td class="service-price <?php echo $is_hl ? 'highlight' : ''; ?>"><?php echo esc_html($row['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
