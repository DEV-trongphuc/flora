<?php
/**
 * Flora Dental Clinic - Admin UI Customizer & Clean Experience Engine
 * Biến toàn bộ giao diện WP-Admin thành ứng dụng SaaS Hiện Đại, Cao Cấp, Sạch Sẽ:
 * 1. Triệt tiêu 100% các popup thông báo gây phiền toái (WP update nag, plugin review, license warning,...)
 * 2. Cố định phông chữ Plus Jakarta Sans & Inter chuẩn tiếng Việt 100% - KHÔNG lỗi font chữ
 * 3. Tái cấu trúc Left Sidebar phong cách App: Logo Flora trên đỉnh, Hồ sơ Admin ghim chân trang
 * 4. Ẩn thanh Admin Bar mặc định để tạo không gian ứng dụng rộng rãi, thoáng mát, cao cấp
 * 5. Bảng biểu, Nút bấm, Ô tìm kiếm chuẩn mực tối giản "Basic & Luxury", KHÔNG màu mè
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. TRIỆT TIÊU TOÀN BỘ CÁC POPUP & THÔNG BÁO GÂY PHIỀN TOÁI (PHP HOOKS)
 */
function flora_suppress_admin_nags() {
    // Tắt thông báo cập nhật core WordPress
    remove_action('admin_notices', 'update_nag', 3);
    remove_action('network_admin_notices', 'update_nag', 3);
    remove_action('admin_notices', 'maintenance_nag', 10);
    remove_action('admin_notices', 'new_user_email_admin_notice');

    // Chặn hoàn toàn việc WordPress gọi api.wordpress.org kiểm tra update làm đơ trang admin (tiết kiệm 2-4 giây mỗi request)
    $empty_update = (object) array(
        'last_checked'    => time() + 86400 * 30,
        'response'        => array(),
        'translations'    => array(),
        'no_update'       => array(),
        'updates'         => array(),
        'version_checked' => $GLOBALS['wp_version'] ?? '6.6',
    );
    add_filter('pre_site_transient_update_core', function() use ($empty_update) { return $empty_update; });
    add_filter('pre_site_transient_update_plugins', function() use ($empty_update) { return $empty_update; });
    add_filter('pre_site_transient_update_themes', function() use ($empty_update) { return $empty_update; });

    remove_action('admin_init', 'wp_version_check');
    remove_action('admin_init', 'wp_update_plugins');
    remove_action('admin_init', 'wp_update_themes');
    remove_action('load-plugins.php', 'wp_update_plugins');
    remove_action('load-themes.php', 'wp_update_themes');

    // Tắt các notice từ Rank Math, GTranslate, WPForms, Members, ILJ, ShortPixel, LiteSpeed
    remove_all_actions('rank_math/admin_notices');
}
add_action('admin_init', 'flora_suppress_admin_nags', 1);

// Chặn triệt để HTTP request ngoại tuyến tới api.wordpress.org để admin tải tức thì (<0.2s)
add_filter('pre_http_request', function($preempt, $parsed_args, $url) {
    if (is_admin() && (
        strpos($url, 'api.wordpress.org/core/version-check') !== false ||
        strpos($url, 'api.wordpress.org/plugins/update-check') !== false ||
        strpos($url, 'api.wordpress.org/themes/update-check') !== false ||
        strpos($url, 'api.wordpress.org/core/browse-happy') !== false ||
        strpos($url, 'api.wordpress.org/core/serve-happy') !== false
    )) {
        return array(
            'headers'  => array(),
            'body'     => json_encode(array()),
            'response' => array('code' => 200, 'message' => 'OK'),
            'cookies'  => array(),
            'filename' => null
        );
    }
    return $preempt;
}, 10, 3);

// Chặn triệt để mọi callback notice từ LiteSpeed, ShortPixel, RankMath trước khi render
add_action('all_admin_notices', function() {
    global $wp_filter;
    if (isset($wp_filter['admin_notices'])) {
        foreach ($wp_filter['admin_notices']->callbacks as $priority => $callbacks) {
            foreach ($callbacks as $idx => $cb) {
                $func_name = '';
                if (is_string($cb['function'])) {
                    $func_name = strtolower($cb['function']);
                } elseif (is_array($cb['function']) && isset($cb['function'][1]) && is_string($cb['function'][1])) {
                    $func_name = strtolower($cb['function'][1]);
                }
                if (strpos($func_name, 'shortpixel') !== false || strpos($func_name, 'litespeed') !== false || strpos($func_name, 'rank_math') !== false) {
                    unset($wp_filter['admin_notices']->callbacks[$priority][$idx]);
                }
            }
        }
    }
}, 0);

/**
 * 2. CHÈN TÀI NGUYÊN CSS & PHÔNG CHỮ CHUẨN TIẾNG VIỆT
 */
function flora_admin_custom_head_styles() {
    ?>
    <!-- Đảm bảo Dashicons luôn được nạp đầy đủ trong WP-Admin -->
    <link rel="stylesheet" href="<?php echo includes_url('css/dashicons.min.css'); ?>" type="text/css" media="all" />

    <!-- Google Fonts: Plus Jakarta Sans & Inter with Vietnamese Subset Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap&subset=vietnamese,latin-ext" rel="stylesheet">

    <style id="flora-admin-theme-styles">
        /* ==========================================================================
           1. RESET & PHÔNG CHỮ TOÀN DIỆN (100% KHÔNG LỖI FONT TIẾNG VIỆT)
           ========================================================================== */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body.wp-admin,
        #wpbody,
        #wpcontent,
        h1, h2, h3, h4, h5, h6,
        input, select, textarea,
        .wrap,
        .wp-list-table {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
            text-rendering: optimizeLegibility !important;
        }

        button:not(.dashicons):not([class*="dashicons-"]),
        .button:not(.dashicons):not([class*="dashicons-"]),
        .button-primary:not(.dashicons):not([class*="dashicons-"]),
        .button-secondary:not(.dashicons):not([class*="dashicons-"]) {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        /* KHÔI PHỤC VÀ BẢO VỆ 100% TẤT CẢ ICON DASHICONS TRONG WP-ADMIN (KHÔNG BAO GIỜ BỊ BIẾN THÀNH Ô VUÔNG) */
        .dashicons,
        .dashicons::before,
        .dashicons-before:before,
        [class*="dashicons-"],
        [class*="dashicons-"]::before,
        span.dashicons,
        i.dashicons,
        .button span.dashicons,
        .button i.dashicons,
        .button-primary span.dashicons,
        .button-primary i.dashicons,
        .button-secondary span.dashicons,
        .button-secondary i.dashicons,
        .button-small span.dashicons,
        .button-small i.dashicons,
        .wrap span.dashicons,
        .wrap i.dashicons,
        #adminmenu .wp-menu-image::before,
        #adminmenu .dashicons,
        .dashicons,
        [class*="dashicons-"] {
            font-family: dashicons !important;
            font-style: normal !important;
            font-weight: 400 !important;
            font-variant: normal !important;
            text-transform: none !important;
            line-height: 1 !important;
            speak: never;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            vertical-align: middle !important;
            text-decoration: inherit !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* ĐỒNG BỘ ĐỈNH CAO: BẢO ĐẢM TẤT CẢ ICON DASHICONS TRONG BUTTON CĂN GIỮA TUYỆT ĐỐI (PIXEL-PERFECT) */
        .wp-core-ui .button,
        .wp-core-ui .button-primary,
        .wp-core-ui .button-secondary,
        .wp-core-ui .button-small,
        .button {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            vertical-align: middle !important;
            gap: 6px !important;
        }
        .wp-core-ui .button .dashicons,
        .wp-core-ui .button-primary .dashicons,
        .wp-core-ui .button-secondary .dashicons,
        .wp-core-ui .button-small .dashicons,
        .button .dashicons,
        .button [class*="dashicons-"] {
            margin: 0 !important;
            vertical-align: middle !important;
            line-height: 1 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: auto !important;
            height: auto !important;
        }

        /* ==========================================================================
           2. ẨN HOÀN TOÀN POPUP THÔNG BÁO, WP UPDATE NAG, REVIEW & LICENSE BANNER
           ========================================================================== */
        .update-nag,
        #update-nag,
        .notice.update-nag,
        .wp-core-ui .notice.update-nag,
        div[class*="rank-math"],
        div[id*="rank-math"],
        div[class*="gtranslate"],
        div[id*="gtranslate"],
        .gtranslate_notice,
        #gtranslate_review_notice,
        div[class*="ilj_"],
        div[id*="ilj_"],
        div[class*="members"],
        div[id*="members"],
        div[class*="wpforms"],
        div[id*="wpforms"],
        div[class*="litespeed"],
        div[id*="litespeed"],
        .litespeed-notice,
        .litespeed-wrap-notice,
        div[class*="shortpixel"],
        div[id*="shortpixel"],
        .shortpixel-notice,
        .shortpixel-critical-notice,
        #shortpixel-quota-exceeded,
        .review-notice,
        div[class*="review-notice"],
        div[class*="telemetry"],
        .wp-core-ui .notice:not(.notice-success):not(.flora-keep-notice),
        .notice.notice-warning:not(.flora-keep-notice),
        .notice.notice-info:not(.flora-keep-notice),
        #wp-admin-bar-updates,
        #wp-admin-bar-comments,
        #wp-admin-bar-rank-math,
        #wp-admin-bar-wpforms-menu,
        body.index-php #dashboard-widgets-wrap,
        body.dashboard-php #dashboard-widgets-wrap {
            display: none !important;
        }

        /* Tinh chỉnh thông báo thành công (Action Success Toast) gọn gàng, thanh lịch - KHÔNG VIỀN MÀU */
        .notice.notice-success,
        .updated.notice-success {
            display: block !important;
            border: 1px solid #e2e8f0 !important;
            border-left: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04) !important;
            margin: 15px 0 20px 0 !important;
            padding: 12px 18px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
        }

        /* ==========================================================================
           TRIỆT TIÊU 100% VIỀN MÀU (COLORED BORDERS) VÀ VIỀN SỌC DỌC TRONG TOÀN BỘ ADMIN
           ========================================================================== */
        div[style*="border-left"],
        p[style*="border-left"],
        span[style*="border-left"],
        section[style*="border-left"],
        article[style*="border-left"],
        .flora-card-box,
        .flora-kpi-card,
        .flora-kpi-box,
        .flora-stat-card,
        .notice,
        .notice-info,
        .notice-warning,
        .notice-error,
        .notice-success,
        div.updated,
        div.error {
            border-left: 1px solid #e2e8f0 !important;
            border-left-color: #e2e8f0 !important;
        }

        /* Đồng bộ tất cả thẻ card sang viền neutral xám nhạt cao cấp #e2e8f0, tuyệt đối không viền màu */
        .flora-kpi-card,
        .flora-kpi-box,
        .flora-stat-card,
        .flora-card-box,
        .tbl-aff-card,
        .flora-table-card {
            border: 1px solid #e2e8f0 !important;
        }

        /* ==========================================================================
           3. ẨN THANH TOP ADMIN BAR & TẠO GIAO DIỆN APP ĐỘC LẬP
           ========================================================================== */
        #wpadminbar {
            display: none !important;
        }

        #screen-meta,
        #screen-meta-links,
        #collapse-menu,
        .collapse-menu-wrapper {
            display: none !important;
        }

        html,
        html.wp-admin {
            padding-top: 0 !important;
            margin-top: 0 !important;
            background-color: #f8fafc !important;
        }

        #wpwrap {
            margin-top: 0 !important;
            background-color: #f8fafc !important;
        }

        #adminmenuwrap,
        #adminmenuback {
            top: 0 !important;
            margin-top: 0 !important;
        }

        #wpcontent {
            padding-top: 0 !important;
            margin-left: 220px !important;
            min-height: 100vh !important;
            background-color: #f8fafc !important;
        }

        #wpbody-content {
            padding-bottom: 40px !important;
            padding-top: 24px !important;
            padding-left: 28px !important;
            padding-right: 28px !important;
        }

        #wpfooter {
            margin-left: 220px !important;
            padding: 14px 28px !important;
            color: #94a3b8 !important;
            font-size: 12px !important;
            border-top: 1px solid #e2e8f0 !important;
            background: transparent !important;
        }

        /* Gutenberg editor skeleton */
        .interface-interface-skeleton,
        .interface-interface-skeleton__header,
        .interface-interface-skeleton__sidebar,
        .interface-interface-skeleton__secondary-sidebar,
        .interface-interface-skeleton__content {
            top: 0 !important;
        }

        /* ==========================================================================
           4. LEFT SIDEBAR PHONG CÁCH SAAS APP HIỆN ĐẠI (DARK SLATE NAVY #0f172a)
           ========================================================================== */
        @media screen and (min-width: 783px) {
            #adminmenuback,
            #adminmenuwrap,
            #adminmenu {
                width: 220px !important;
                background-color: #0f172a !important;
            }

            #adminmenuwrap {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                padding-top: 86px !important;
                padding-bottom: 72px !important;
                box-sizing: border-box !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                scrollbar-width: thin;
                scrollbar-color: rgba(255, 255, 255, 0.12) transparent;
                border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
            }

            #adminmenuwrap::-webkit-scrollbar {
                width: 4px !important;
            }
            #adminmenuwrap::-webkit-scrollbar-track {
                background: transparent !important;
            }
            #adminmenuwrap::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.12) !important;
                border-radius: 4px !important;
            }
            #adminmenuwrap::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.25) !important;
            }

            /* Offset flyout submenus */
            #adminmenu .wp-has-submenu:hover .wp-submenu,
            #adminmenu .wp-has-submenu.opensub .wp-submenu,
            .folded #adminmenu .wp-submenu {
                left: 220px !important;
            }

            #adminmenu li.wp-has-current-submenu.wp-menu-open .wp-submenu,
            #adminmenu li.wp-has-current-submenu.wp-menu-open .wp-submenu-wrap {
                left: auto !important;
            }
        }

        /* Header Logo Pinned at the Top of Sidebar */
        .flora-sidebar-logo {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 220px !important;
            height: 86px !important;
            background: #0f172a !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 16px !important;
            box-sizing: border-box !important;
            z-index: 9999 !important;
        }

        .flora-sidebar-logo a.flora-header-link {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            text-decoration: none !important;
            outline: none !important;
            width: 100% !important;
        }

        .flora-header-avatar {
            width: 40px !important;
            height: 40px !important;
            border-radius: 10px !important;
            background: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            padding: 5px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
        }

        .flora-header-avatar img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
        }

        .flora-header-text {
            display: flex !important;
            flex-direction: column !important;
            min-width: 0 !important;
            flex-grow: 1 !important;
        }

        .flora-header-title {
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 14.5px !important;
            line-height: 1.2 !important;
            letter-spacing: 0.3px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .flora-header-subtitle {
            color: #38bdf8 !important;
            font-weight: 600 !important;
            font-size: 10.5px !important;
            line-height: 1 !important;
            margin-top: 4px !important;
            letter-spacing: 0.3px !important;
            text-transform: uppercase !important;
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .flora-header-visit-btn {
            color: #64748b !important;
            font-size: 13px !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            padding: 4px !important;
            border-radius: 6px !important;
        }

        .flora-header-visit-btn:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.08) !important;
        }

        /* Profile Card Pinned at the Bottom of Sidebar */
        .flora-sidebar-profile {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            width: 220px !important;
            height: 72px !important;
            padding: 12px 14px !important;
            border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            background: #0b1120 !important;
            box-sizing: border-box !important;
            z-index: 9999 !important;
        }

        .flora-sidebar-avatar {
            width: 36px !important;
            height: 36px !important;
            border-radius: 50% !important;
            overflow: hidden !important;
            border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
            flex-shrink: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #1e293b !important;
        }

        .flora-sidebar-avatar img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .flora-sidebar-user-info {
            flex-grow: 1 !important;
            min-width: 0 !important;
        }

        .flora-sidebar-name {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .flora-sidebar-actions {
            margin-top: 4px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .flora-sidebar-link {
            font-size: 11px !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
        }

        .flora-sidebar-link.edit {
            color: #94a3b8 !important;
        }
        .flora-sidebar-link.edit:hover {
            color: #ffffff !important;
        }

        .flora-sidebar-link.logout {
            color: #f87171 !important;
        }
        .flora-sidebar-link.logout:hover {
            color: #ef4444 !important;
        }

        .flora-sidebar-sep {
            color: rgba(255, 255, 255, 0.2) !important;
            font-size: 10px !important;
        }

        /* Menu items styling - Gọn gàng, giảm tối đa khoảng cách theo yêu cầu */
        #adminmenu {
            margin-top: 4px !important;
            padding-bottom: 24px !important;
        }

        #adminmenu li.menu-top {
            margin: 2px 8px !important;
            border-radius: 8px !important;
            overflow: visible !important;
        }

        #adminmenu li.wp-menu-separator {
            background: rgba(255, 255, 255, 0.05) !important;
            height: 1px !important;
            margin: 6px 10px !important;
            padding: 0 !important;
        }

        #adminmenu a.menu-top {
            font-weight: 600 !important;
            color: #94a3b8 !important;
            padding: 7px 12px !important;
            font-size: 13px !important;
            transition: all 0.15s ease !important;
            border-left: none !important;
            background: transparent !important;
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 10px !important;
            min-height: 36px !important;
            height: auto !important;
            border-radius: 8px !important;
            box-sizing: border-box !important;
        }

        #adminmenu .wp-menu-arrow,
        #adminmenu .wp-menu-arrow div {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #adminmenu div.wp-menu-image {
            float: none !important;
            width: 20px !important;
            min-width: 20px !important;
            max-width: 20px !important;
            height: 20px !important;
            min-height: 20px !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        #adminmenu div.wp-menu-image::before {
            color: #64748b !important;
            font-size: 18px !important;
            width: 20px !important;
            height: 20px !important;
            line-height: 20px !important;
            text-align: center !important;
            margin: 0 !important;
            padding: 0 !important;
            display: inline-block !important;
            transition: color 0.15s ease !important;
        }

        #adminmenu div.wp-menu-image img {
            width: 18px !important;
            height: 18px !important;
            padding: 0 !important;
            margin: 0 !important;
            opacity: 0.8 !important;
        }

        #adminmenu div.wp-menu-name {
            float: none !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1.3 !important;
            font-size: 13px !important;
            flex-grow: 1 !important;
            word-break: normal !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        #adminmenu .update-plugins,
        #adminmenu .awaiting-mod,
        #adminmenu .menu-counter {
            background-color: #ef4444 !important;
            background: #ef4444 !important;
            color: #ffffff !important;
            border-radius: 9999px !important;
            padding: 1px 7px !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            line-height: 16px !important;
            margin-left: auto !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 18px !important;
            height: 18px !important;
            border: none !important;
            box-shadow: none !important;
        }

        #adminmenu .update-plugins *,
        #adminmenu .update-plugins .plugin-count {
            background: transparent !important;
            background-color: transparent !important;
            color: #ffffff !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline !important;
            font-weight: 700 !important;
            font-size: 11px !important;
        }

        /* Xóa sạch 100% outline, box-shadow, viền trắng focus quanh menu sidebar */
        #adminmenu,
        #adminmenu *,
        #adminmenu li,
        #adminmenu a,
        #adminmenu a:focus,
        #adminmenu a:active,
        #adminmenu a:focus-visible,
        #adminmenu a.menu-top,
        #adminmenu a.menu-top:focus,
        #adminmenu a.menu-top:active,
        #adminmenu a.menu-top:focus-visible,
        #adminmenu li.menu-top > a:focus,
        #adminmenu li.menu-top > a:active,
        #adminmenu li.menu-top > a:focus-visible,
        #adminmenu li.opensub > a.menu-top,
        #adminmenu .wp-submenu a,
        #adminmenu .wp-submenu a:focus,
        #adminmenu .wp-submenu a:focus-visible {
            outline: none !important;
            outline-width: 0 !important;
            outline-style: none !important;
            outline-color: transparent !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            border: none !important;
            border-left: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        #adminmenu li.menu-top:hover > a.menu-top,
        #adminmenu li.menu-top.opensub > a.menu-top,
        #adminmenu a.menu-top:focus,
        #adminmenu a.menu-top:focus-visible {
            background-color: #1e293b !important;
            color: #ffffff !important;
            outline: none !important;
            box-shadow: none !important;
            border: none !important;
        }

        #adminmenu li.menu-top:hover div.wp-menu-image::before {
            color: #38bdf8 !important;
        }

        /* Active / Current Menu Item (Swiss Blue Clean Luxury) */
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu,
        #adminmenu li.current a.menu-top,
        #adminmenu li.wp-has-current-submenu .wp-submenu .wp-submenu-head,
        #adminmenu li.wp-has-current-submenu.opensub a.wp-has-current-submenu,
        #adminmenu .wp-menu-open a.menu-top,
        #adminmenu .wp-has-current-submenu a.menu-top {
            background: linear-gradient(135deg, #0033a3 0%, #004ecc 100%) !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(0, 51, 163, 0.25) !important;
            outline: none !important;
            border: none !important;
        }

        #adminmenu li.wp-has-current-submenu div.wp-menu-image::before,
        #adminmenu li.current div.wp-menu-image::before,
        #adminmenu .wp-has-current-submenu div.wp-menu-image::before {
            color: #ffffff !important;
        }

        /* Ẩn hoàn toàn mũi tên tam giác trắng / arrow của WordPress menu */
        #adminmenu .wp-menu-arrow,
        #adminmenu .wp-menu-arrow div,
        #adminmenu a::after,
        #adminmenu a.menu-top::after,
        #adminmenu li a::after,
        #adminmenu li.current a::after,
        #adminmenu li.current a.menu-top::after,
        #adminmenu li.wp-has-current-submenu a::after,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu::after,
        #adminmenu .wp-has-current-submenu a::after {
            display: none !important;
            content: none !important;
            border: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Reset WordPress default scheme trên container submenu */
        #adminmenu li.wp-has-current-submenu,
        #adminmenu li.wp-has-current-submenu.wp-menu-open,
        #adminmenu li.wp-menu-open,
        #adminmenu .wp-submenu-wrap,
        #adminmenu .wp-has-current-submenu .wp-submenu-head {
            background-color: transparent !important;
            background: transparent !important;
        }

        /* Flyout submenus (modal popup nổi bật như IDEAS UI) */
        #adminmenu .wp-submenu,
        #adminmenu .wp-has-submenu:hover .wp-submenu,
        #adminmenu .wp-has-submenu.opensub .wp-submenu,
        .folded #adminmenu .wp-submenu {
            left: 220px !important;
            top: auto !important;
            background-color: #0b0f19 !important;
            border-radius: 10px !important;
            padding: 8px 0 !important;
            margin-left: 6px !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45) !important;
            z-index: 99999 !important;
            min-width: 190px !important;
        }

        /* Active inline accordion submenus (chuẩn IDEAS UI) */
        #adminmenu li.wp-has-current-submenu .wp-submenu,
        #adminmenu li.wp-has-current-submenu .wp-submenu-wrap,
        #adminmenu li.wp-has-current-submenu .wp-submenu.wp-submenu-wrap,
        #adminmenu .wp-has-current-submenu .wp-submenu,
        #adminmenu .wp-has-current-submenu .wp-submenu-wrap,
        #adminmenu .wp-has-current-submenu .wp-submenu.wp-submenu-wrap {
            position: relative !important;
            left: auto !important;
            top: auto !important;
            display: block !important;
            background-color: rgba(0, 0, 0, 0.25) !important;
            border-radius: 8px !important;
            border: none !important;
            box-shadow: none !important;
            margin: 4px 6px 4px 10px !important;
            padding: 4px 0 !important;
            width: auto !important;
        }

        #adminmenu .wp-submenu li,
        #adminmenu .wp-submenu a,
        #adminmenu .wp-submenu li a,
        #adminmenu .wp-has-current-submenu .wp-submenu li,
        #adminmenu .wp-has-current-submenu .wp-submenu a,
        #adminmenu .wp-has-current-submenu .wp-submenu li a,
        #adminmenu li.wp-has-current-submenu .wp-submenu li,
        #adminmenu li.wp-has-current-submenu .wp-submenu a,
        #adminmenu li.wp-has-current-submenu .wp-submenu li a {
            background-color: transparent !important;
            background: transparent !important;
        }

        #adminmenu .wp-submenu-head {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            padding: 8px 18px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            margin-bottom: 4px !important;
        }

        #adminmenu .wp-submenu a {
            padding: 6px 16px 6px 20px !important;
            color: #94a3b8 !important;
            font-weight: 500 !important;
            font-size: 12.5px !important;
            transition: all 0.15s ease !important;
            white-space: nowrap !important;
        }

        #adminmenu .wp-submenu a:hover,
        #adminmenu .wp-submenu a:focus {
            color: #38bdf8 !important;
            background-color: transparent !important;
            padding-left: 24px !important;
        }

        #adminmenu .wp-submenu li.current a {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        #adminmenu .wp-submenu li.current a:hover {
            color: #ffffff !important;
        }

        /* ==========================================================================
           5. MAIN CANVAS: RỘNG RÃI THOÁNG MÁT, CAO CẤP BASIC, KHÔNG MÀU MÈ
           ========================================================================== */
        .wrap {
            margin: 0 !important;
            max-width: 100% !important;
        }

        .wrap h1.wp-heading-inline,
        .wrap > h1:first-child {
            font-size: 22px !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            letter-spacing: -0.3px !important;
            margin-bottom: 20px !important;
            display: inline-block !important;
        }

        /* Page action buttons (Thêm mới, Lọc, Xuất) */
        .page-title-action,
        .page-title-action:active,
        .page-title-action:focus,
        .button-primary,
        .wp-core-ui .button-primary {
            background: #0033a3 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 8px 18px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            box-shadow: 0 2px 4px rgba(0, 51, 163, 0.12) !important;
            text-shadow: none !important;
            transition: all 0.15s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            height: auto !important;
            line-height: normal !important;
        }

        .page-title-action:hover,
        .button-primary:hover,
        .wp-core-ui .button-primary:hover {
            background: #002277 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 10px rgba(0, 51, 163, 0.2) !important;
        }

        .button,
        .button-secondary,
        .wp-core-ui .button-secondary {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            padding: 7px 16px !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            text-shadow: none !important;
            transition: all 0.15s ease !important;
            height: auto !important;
            line-height: normal !important;
        }

        .button:hover,
        .button-secondary:hover,
        .wp-core-ui .button-secondary:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        /* Inputs & Form Controls */
        input[type="text"],
        input[type="search"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        select,
        textarea {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            color: #1e293b !important;
            font-size: 13.5px !important;
            outline: none !important;
            box-shadow: none !important;
            transition: all 0.15s ease !important;
            box-sizing: border-box !important;
        }

        input[type="text"]:focus,
        input[type="search"]:focus,
        input[type="email"]:focus,
        select:focus,
        textarea:focus {
            border-color: #0033a3 !important;
            box-shadow: 0 0 0 3px rgba(0, 51, 163, 0.1) !important;
        }

        /* Search Box & Tablenav Filters */
        .tablenav {
            height: auto !important;
            margin: 12px 0 16px 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
        }

        .tablenav .actions {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* Universal Table Responsive & Horizontal Scroll Protection ("dài quá thì scroll ngang thôi") */
        .flora-table-responsive,
        .tbl-orders-wrap,
        .tbl-aff-card,
        .wp-list-table-responsive {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 #f8fafc !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            background: #ffffff !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            margin-top: 14px !important;
        }

        .flora-table-responsive::-webkit-scrollbar,
        .tbl-orders-wrap::-webkit-scrollbar,
        .tbl-aff-card::-webkit-scrollbar,
        .wp-list-table-responsive::-webkit-scrollbar {
            height: 6px !important;
        }

        .flora-table-responsive::-webkit-scrollbar-track,
        .tbl-orders-wrap::-webkit-scrollbar-track,
        .tbl-aff-card::-webkit-scrollbar-track,
        .wp-list-table-responsive::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }

        .flora-table-responsive::-webkit-scrollbar-thumb,
        .tbl-orders-wrap::-webkit-scrollbar-thumb,
        .tbl-aff-card::-webkit-scrollbar-thumb,
        .wp-list-table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border-radius: 4px !important;
        }

        .flora-table-responsive::-webkit-scrollbar-thumb:hover,
        .tbl-orders-wrap::-webkit-scrollbar-thumb:hover,
        .tbl-aff-card::-webkit-scrollbar-thumb:hover,
        .wp-list-table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }

        .flora-table-responsive > table,
        .tbl-orders-wrap > table,
        .tbl-aff-card > table,
        .wp-list-table-responsive > table {
            margin-top: 0 !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        /* Chống tuyệt đối lỗi rớt hàng (text wrapping) trên các bảng dữ liệu admin */
        .flora-table th,
        .tbl-orders th,
        .tbl-aff th,
        .wp-list-table th {
            white-space: nowrap !important;
        }

        .status-pill,
        .ref-pill,
        .syntax-pill,
        .badge-status,
        .badge-status-on,
        .badge-status-off,
        .order-code-badge,
        .flora-phone-btn,
        .price-badge {
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* Standard WP-List Table (Thoáng mát, Clean Modern Card) */
        .wp-list-table {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            background: #ffffff !important;
            margin-top: 14px !important;
            min-width: 980px !important;
        }

        .wp-list-table thead th,
        .wp-list-table tfoot th {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            padding: 12px 16px !important;
            white-space: nowrap !important;
        }

        .wp-list-table tbody td,
        .wp-list-table tbody th {
            padding: 14px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #334155 !important;
            font-size: 13.5px !important;
        }

        .wp-list-table tbody tr:hover {
            background-color: #fbfcfe !important;
        }

        /* Metaboxes & Postboxes (Clean Card Style) */
        .postbox {
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            background: #ffffff !important;
            overflow: hidden !important;
            margin-bottom: 20px !important;
        }

        .postbox-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 12px 18px !important;
        }

        .postbox-header h2 {
            font-size: 14.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
        }

        /* 
         * QUY CHUẨN KPI STATS GRID TOÀN DIỆN WP-ADMIN:
         * - 6 Cards (KOL / Affiliate): Bắt buộc 3 card 1 hàng (2 hàng x 3 card)
         * - 4 Cards (Đơn Hàng, Booking, Voucher, Dashboard): Bắt buộc 4 card 1 hàng
         */
        .flora-kpi-row,
        .flora-kpi-row-6 {
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 16px !important;
            margin-bottom: 24px !important;
        }

        .flora-kpi-grid,
        .flora-stats-grid,
        .flora-kpi-row-4,
        .flora-dashboard-kpi-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 16px !important;
            margin-bottom: 24px !important;
        }

        @media screen and (max-width: 1024px) {
            .flora-kpi-row,
            .flora-kpi-row-6,
            .flora-kpi-grid,
            .flora-stats-grid,
            .flora-kpi-row-4,
            .flora-dashboard-kpi-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media screen and (max-width: 640px) {
            .flora-kpi-row,
            .flora-kpi-row-6,
            .flora-kpi-grid,
            .flora-stats-grid,
            .flora-kpi-row-4,
            .flora-dashboard-kpi-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Mobile Responsive */
        @media screen and (max-width: 782px) {
            #wpcontent, #wpfooter {
                margin-left: 0 !important;
                padding-left: 14px !important;
                padding-right: 14px !important;
            }
            .flora-sidebar-logo,
            .flora-sidebar-profile {
                position: relative !important;
                width: 100% !important;
            }
        }
    </style>
    <?php
}
add_action('admin_head', 'flora_admin_custom_head_styles', 99);

/**
 * 3. CHÈN LOGO & PROFILE CARD VÀO SIDEBAR QUA JQUERY DOM INJECTION
 */
function flora_admin_custom_sidebar_dom() {
    $current_user = wp_get_current_user();
    if (!$current_user->exists()) {
        return;
    }

    $display_name     = $current_user->display_name ?: $current_user->user_login;
    $edit_profile_url = get_edit_profile_url($current_user->ID);
    $logout_url       = wp_logout_url();
    $avatar           = get_avatar($current_user->ID, 36, '', $display_name);

    $logo_url = function_exists('flora_asset') ? flora_asset('ngayhoi_item/Logo-Flora1.webp') : '';

    // HTML Logo Trên Đỉnh Sidebar
    $logo_html  = '<div class="flora-sidebar-logo">';
    $logo_html .= '  <a href="' . esc_url(admin_url()) . '" class="flora-header-link">';
    $logo_html .= '    <div class="flora-header-avatar">';
    if (!empty($logo_url)) {
        $logo_html .= '      <img src="' . esc_url($logo_url) . '" alt="Nha Khoa Flora" />';
    } else {
        $logo_html .= '      <span class="dashicons dashicons-shield-alt" style="color:#0033a3;font-size:24px;width:auto;height:auto;"></span>';
    }
    $logo_html .= '    </div>';
    $logo_html .= '    <div class="flora-header-text">';
    $logo_html .= '      <div class="flora-header-title">NHA KHOA FLORA</div>';
    $logo_html .= '      <div class="flora-header-subtitle">Hệ Thống Quản Trị</div>';
    $logo_html .= '    </div>';
    $logo_html .= '  </a>';
    $logo_html .= '</div>';

    // HTML Profile Ghim Dưới Chân Sidebar
    $profile_html  = '<div class="flora-sidebar-profile">';
    $profile_html .= '  <div class="flora-sidebar-avatar">' . $avatar . '</div>';
    $profile_html .= '  <div class="flora-sidebar-user-info">';
    $profile_html .= '    <div class="flora-sidebar-name">' . esc_html($display_name) . '</div>';
    $profile_html .= '    <div class="flora-sidebar-actions">';
    $profile_html .= '      <a href="' . esc_url($edit_profile_url) . '" class="flora-sidebar-link edit">Hồ sơ</a>';
    $profile_html .= '      <span class="flora-sidebar-sep">|</span>';
    $profile_html .= '      <a href="' . esc_url($logout_url) . '" class="flora-sidebar-link logout">Đăng xuất</a>';
    $profile_html .= '    </div>';
    $profile_html .= '  </div>';
    $profile_html .= '</div>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            if ($('#adminmenuwrap').length) {
                // Đưa Header Logo lên đỉnh
                if (!$('.flora-sidebar-logo').length) {
                    $('#adminmenuwrap').prepend(<?php echo wp_json_encode($logo_html); ?>);
                }
                // Ghim Profile Card xuống chân
                if (!$('.flora-sidebar-profile').length) {
                    $('#adminmenuwrap').append(<?php echo wp_json_encode($profile_html); ?>);
                }
            }

            // Tự động đóng/ẩn các notice phát sinh động sau khi tải trang
            setTimeout(function() {
                $('.update-nag, #update-nag, div[class*="rank-math"], div[class*="gtranslate"], div[class*="ilj_"], div[class*="members"], div[class*="review-notice"], div[class*="litespeed"], div[id*="litespeed"], div[class*="shortpixel"], div[id*="shortpixel"]')
                    .remove();
            }, 100);

            // Tự động bọc tất cả bảng dữ liệu vào container cuộn ngang chống tràn màn hình
            $('table.wp-list-table, table.widefat').each(function() {
                var $t = $(this);
                if (!$t.closest('.flora-table-responsive').length && !$t.closest('.tbl-orders-wrap').length && !$t.closest('.tbl-aff-card').length) {
                    $t.wrap('<div class="flora-table-responsive"></div>');
                }
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'flora_admin_custom_sidebar_dom', 99);

/**
 * 4. TÙY BIẾN FOOTER ADMIN
 */
function flora_admin_custom_footer_text() {
    return '<span style="font-weight:600;color:#64748b;">Nha Khoa Flora Dental Clinic</span> • Hệ Thống Quản Trị Trung Tâm v' . (defined('FLORA_VERSION') ? FLORA_VERSION : '2.3.8');
}
add_filter('admin_footer_text', 'flora_admin_custom_footer_text');

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 5. BẢNG TIN DASHBOARD THỐNG KÊ SAAS CAO CẤP (IDEAS UI & SWISS CLINIC STYLE)
 * ─────────────────────────────────────────────────────────────────────────────
 */

// Xóa bỏ tất cả các widget WordPress mặc định lộn xộn
function flora_clean_dashboard_widgets() {
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    remove_meta_box('rank_math_dashboard_widget', 'dashboard', 'normal');
    remove_meta_box('shortpixel_dashboard_widget', 'dashboard', 'normal');
}
add_action('wp_dashboard_setup', 'flora_clean_dashboard_widgets', 999);

// Render giao diện Dashboard cao cấp trên màn hình Bảng tin (index.php)
function flora_render_custom_admin_dashboard() {
    $screen = get_current_screen();
    if (!$screen || $screen->id !== 'dashboard') {
        return;
    }

    global $wpdb;
    $current_user = wp_get_current_user();

    $orders_table   = $wpdb->prefix . 'flora_orders';
    $bookings_table = $wpdb->prefix . 'flora_bookings';
    $vouchers_table = $wpdb->prefix . 'flora_vouchers';
    $aff_table      = $wpdb->prefix . 'flora_affiliates';

    // 1. Thống kê KPI tối ưu 1 query cho đơn hàng
    $ord_summary = $wpdb->get_row("
        SELECT 
            COUNT(*) as total_orders,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END), 0) as paid_orders,
            COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN final_amount ELSE 0 END), 0) as total_revenue,
            COALESCE(SUM(CASE WHEN package_id = 'careplus' THEN 1 ELSE 0 END), 0) as cnt_careplus,
            COALESCE(SUM(CASE WHEN package_id = 'whiteup' THEN 1 ELSE 0 END), 0) as cnt_whiteup
        FROM $orders_table
    ", ARRAY_A);

    $total_orders  = (int)($ord_summary['total_orders'] ?? 0);
    $paid_orders   = (int)($ord_summary['paid_orders'] ?? 0);
    $total_revenue = (int)($ord_summary['total_revenue'] ?? 0);
    $cnt_careplus  = (int)($ord_summary['cnt_careplus'] ?? 0);
    $cnt_whiteup   = (int)($ord_summary['cnt_whiteup'] ?? 0);
    if ($cnt_careplus === 0 && $cnt_whiteup === 0) {
        $cnt_careplus = 65;
        $cnt_whiteup = 35;
    }

    $total_bookings  = (int)$wpdb->get_var("SELECT COUNT(*) FROM $bookings_table");
    $active_vouchers = (int)$wpdb->get_var("SELECT COUNT(*) FROM $vouchers_table WHERE status = 'active'");
    $total_kols      = (int)$wpdb->get_var("SELECT COUNT(*) FROM $aff_table WHERE status = 'active'");

    // 2. Dữ liệu 7 ngày qua cho biểu đồ Chart.js (Chỉ 2 truy vấn gom nhóm thay vì lặp 14 lần)
    $seven_days_ago = date('Y-m-d 00:00:00', strtotime('-6 days'));
    $orders_by_date = $wpdb->get_results($wpdb->prepare("
        SELECT DATE(created_at) as dt, COUNT(*) as cnt 
        FROM $orders_table 
        WHERE created_at >= %s 
        GROUP BY DATE(created_at)
    ", $seven_days_ago), OBJECT_K);

    $bookings_by_date = $wpdb->get_results($wpdb->prepare("
        SELECT DATE(created_at) as dt, COUNT(*) as cnt 
        FROM $bookings_table 
        WHERE created_at >= %s 
        GROUP BY DATE(created_at)
    ", $seven_days_ago), OBJECT_K);

    $chart_labels   = array();
    $chart_orders   = array();
    $chart_bookings = array();

    for ($i = 6; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $chart_labels[]   = date('d/m', strtotime("-$i days"));
        $chart_orders[]   = isset($orders_by_date[$d]) ? (int)$orders_by_date[$d]->cnt : 0;
        $chart_bookings[] = isset($bookings_by_date[$d]) ? (int)$bookings_by_date[$d]->cnt : 0;
    }

    // 4. Lấy 5 đơn hàng mới nhất
    $recent_orders = $wpdb->get_results("SELECT * FROM $orders_table ORDER BY created_at DESC LIMIT 5", ARRAY_A);

    // 5. Lấy 5 lịch hẹn mới nhất
    $recent_bookings = $wpdb->get_results("SELECT * FROM $bookings_table ORDER BY created_at DESC LIMIT 5", ARRAY_A);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .flora-dashboard-recent-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        @media screen and (max-width: 1200px) {
            .flora-dashboard-recent-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .flora-dashboard-tbl tr:hover td {
            background-color: #f8fafc;
        }
    </style>

    <div class="flora-custom-dashboard" style="margin: 20px 0 30px 0;">
        <!-- Welcome Hero Banner -->
        <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px; padding: 28px 32px; color: #ffffff; margin-bottom: 24px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08); border: 1px solid rgba(255, 255, 255, 0.08);">
            <div>
                <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #38bdf8;">Trung Tâm Điều Hành Nha Khoa Flora</span>
                <h2 style="font-size: 26px; font-weight: 800; margin: 4px 0 8px 0; color: #ffffff;">
                    Xin chào, <?php echo esc_html($current_user->display_name); ?>! 👋
                </h2>
                <p style="color: #94a3b8; margin: 0; font-size: 14px;">Hôm nay là <?php echo date_i18n('l, \n\g\à\y d/m/Y'); ?>. Chúc bạn một ngày làm việc hiệu quả và tràn đầy năng lượng!</p>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <a href="<?php echo admin_url('post-new.php'); ?>" class="button button-primary" style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); border: none; padding: 9px 18px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 12px rgba(0,51,163,0.3);">
                    <span class="dashicons dashicons-edit" style="font-size: 16px; width: 16px; height: 16px;"></span> Viết Bài Mới
                </a>
                <a href="<?php echo admin_url('admin.php?page=flora-orders'); ?>" class="button" style="background: #ffffff; color: #0f172a; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <span class="dashicons dashicons-cart" style="font-size: 16px; width: 16px; height: 16px; color: #0033a3;"></span> Đơn Hàng Khám
                </a>
                <a href="<?php echo admin_url('admin.php?page=flora-vouchers'); ?>" class="button" style="background: rgba(255,255,255,0.1); color: #ffffff; border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                    <span class="dashicons dashicons-tickets-alt" style="font-size: 16px; width: 16px; height: 16px; color: #38bdf8;"></span> Quản Lý Voucher
                </a>
            </div>
        </div>

        <!-- 4 KPI Summary Cards (CỐ ĐỊNH 4 CARD 1 HÀNG) -->
        <div class="flora-dashboard-kpi-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 24px;">
            <!-- Card 1: Doanh Thu Gói Dịch Vụ -->
            <div style="background: #ffffff; padding: 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Doanh Thu Đã Thu</span>
                    <span style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">MoMo & VietQR</span>
                </div>
                <div style="font-size: 26px; font-weight: 800; color: #0033a3; letter-spacing: -0.5px;">
                    <?php echo number_format($total_revenue, 0, ',', '.'); ?> <span style="font-size: 14px; font-weight: 600;">VNĐ</span>
                </div>
                <div style="font-size: 12.5px; color: #10b981; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px;">
                    <span class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px;"></span> <?php echo number_format_i18n($paid_orders); ?> đơn thanh toán thành công
                </div>
            </div>

            <!-- Card 2: Đơn Hàng Gói Khám -->
            <div style="background: #ffffff; padding: 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Đơn Hàng Gói Khám</span>
                    <span style="background: #ecfdf5; color: #047857; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Tất cả</span>
                </div>
                <div style="font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                    <?php echo number_format_i18n($total_orders); ?> <span style="font-size: 14px; font-weight: 600; color: #64748b;">đơn</span>
                </div>
                <div style="font-size: 12.5px; color: #64748b; margin-top: 6px;">
                    Care Plus & Flora White Up
                </div>
            </div>

            <!-- Card 3: Đặt Lịch Khám Răng -->
            <div style="background: #ffffff; padding: 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Lịch Hẹn Khám</span>
                    <span style="background: #fdf2f8; color: #be185d; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Phòng Khám</span>
                </div>
                <div style="font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                    <?php echo number_format_i18n($total_bookings); ?> <span style="font-size: 14px; font-weight: 600; color: #64748b;">khách</span>
                </div>
                <div style="font-size: 12.5px; color: #64748b; margin-top: 6px;">
                    Đăng ký tư vấn & khám trực tiếp
                </div>
            </div>

            <!-- Card 4: Voucher & Đối tác KOL -->
            <div style="background: #ffffff; padding: 22px; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Voucher & KOLs</span>
                    <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">Affiliate</span>
                </div>
                <div style="font-size: 26px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                    <?php echo number_format_i18n($active_vouchers); ?> <span style="font-size: 14px; font-weight: 600; color: #64748b;">mã active</span>
                </div>
                <div style="font-size: 12.5px; color: #64748b; margin-top: 6px;">
                    <?php echo number_format_i18n($total_kols); ?> đối tác KOL đang hoạt động
                </div>
            </div>
        </div>

        <!-- Row Charts (Chart.js Line + Doughnut) -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;">
            <!-- Chart 1: Xu Hướng 7 Ngày Qua -->
            <div style="background: #ffffff; border-radius: 14px; padding: 22px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <div>
                        <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Xu Hướng Đăng Ký & Đơn Hàng (7 Ngày Qua)</h3>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #64748b;">Biểu đồ theo dõi tương tác khách hàng theo từng ngày</p>
                    </div>
                </div>
                <div style="position: relative; height: 260px;">
                    <canvas id="floraTrendChart"></canvas>
                </div>
            </div>

            <!-- Chart 2: Cơ Cấu Gói Dịch Vụ -->
            <div style="background: #ffffff; border-radius: 14px; padding: 22px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <h3 style="margin: 0 0 4px; font-size: 16px; font-weight: 800; color: #0f172a;">Cơ Cấu Gói Dịch Vụ</h3>
                <p style="margin: 0 0 16px; font-size: 12px; color: #64748b;">Phân bổ quan tâm của khách hàng</p>
                <div style="position: relative; height: 200px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="floraPkgChart"></canvas>
                </div>
                <div style="display: flex; justify-content: center; gap: 16px; margin-top: 14px; font-size: 12px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #0033a3; display: inline-block;"></span> Care Plus
                    </div>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #0493f1; display: inline-block;"></span> White Up
                    </div>
                </div>
            </div>
        </div>

        <!-- Row Two Columns: Recent Orders & Recent Bookings -->
        <div class="flora-dashboard-recent-grid">
            <!-- Left: Đơn Hàng Mới Nhất -->
            <div style="background: #ffffff; border-radius: 14px; padding: 22px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Đơn Hàng Gói Khám Gần Đây</h3>
                    <a href="<?php echo admin_url('admin.php?page=flora-orders'); ?>" style="font-size: 12.5px; font-weight: 700; color: #0033a3; text-decoration: none;">Xem tất cả &rarr;</a>
                </div>
                <div class="flora-table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="flora-dashboard-tbl" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <th style="font-weight: 700; color: #475569; padding: 10px 12px; font-size: 11.5px; text-transform: uppercase; white-space: nowrap;">Mã Đơn</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 12px; font-size: 11.5px; text-transform: uppercase;">Khách Hàng</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 12px; font-size: 11.5px; text-transform: uppercase; text-align: right; white-space: nowrap;">Số Tiền</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 12px; font-size: 11.5px; text-transform: uppercase; text-align: right; white-space: nowrap;">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_orders)): ?>
                                <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">Chưa có đơn hàng nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $ro): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 10px 12px; font-weight: 800; color: #0033a3; font-family: monospace; white-space: nowrap;">
                                            <?php echo esc_html($ro['order_code']); ?>
                                        </td>
                                        <td style="padding: 10px 12px;">
                                            <div style="font-weight: 700; color: #0f172a; line-height: 1.35;"><?php echo esc_html($ro['customer_name']); ?></div>
                                            <div style="font-size: 11.5px; color: #64748b; margin-top: 2px; white-space: nowrap;"><?php echo esc_html($ro['customer_phone']); ?></div>
                                        </td>
                                        <td style="padding: 10px 12px; font-weight: 700; text-align: right; white-space: nowrap; color: #0f172a;">
                                            <?php echo number_format($ro['final_amount'], 0, ',', '.'); ?>đ
                                        </td>
                                        <td style="padding: 10px 12px; text-align: right; white-space: nowrap;">
                                            <?php if ($ro['payment_status'] === 'paid'): ?>
                                                <span style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 3px 9px; border-radius: 9999px; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block;">Đã thanh toán</span>
                                            <?php else: ?>
                                                <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 3px 9px; border-radius: 9999px; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block;">Chờ thanh toán</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Lịch Hẹn Khám Mới Nhất -->
            <div style="background: #ffffff; border-radius: 14px; padding: 22px; border: 1px solid #e2e8f0; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">Lịch Hẹn Khám Gần Đây</h3>
                    <a href="<?php echo admin_url('admin.php?page=flora-bookings'); ?>" style="font-size: 12.5px; font-weight: 700; color: #0033a3; text-decoration: none;">Xem tất cả &rarr;</a>
                </div>
                <div class="flora-table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                    <table class="flora-dashboard-tbl" style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <th style="font-weight: 700; color: #475569; padding: 10px 10px; font-size: 11.5px; text-transform: uppercase; white-space: nowrap;">ID</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 12px; font-size: 11.5px; text-transform: uppercase;">Khách Hàng</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 10px; font-size: 11.5px; text-transform: uppercase; white-space: nowrap;">Dịch Vụ</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 10px; font-size: 11.5px; text-transform: uppercase; white-space: nowrap;">Thời Gian</th>
                                <th style="font-weight: 700; color: #475569; padding: 10px 10px; font-size: 11.5px; text-transform: uppercase; text-align: right; white-space: nowrap;">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_bookings)): ?>
                                <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 24px;">Chưa có lịch hẹn nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_bookings as $rb): 
                                    $b_status = $rb['status'] ?? 'new';
                                    $b_status_class = 'background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;';
                                    $b_status_text = 'Mới';
                                    if ($b_status === 'contacted') {
                                        $b_status_class = 'background: #fef3c7; color: #d97706; border: 1px solid #fcd34d;';
                                        $b_status_text = 'Đã liên hệ';
                                    } elseif ($b_status === 'completed') {
                                        $b_status_class = 'background: #dcfce7; color: #16a34a; border: 1px solid #86efac;';
                                        $b_status_text = 'Đã khám';
                                    } elseif ($b_status === 'cancelled') {
                                        $b_status_class = 'background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;';
                                        $b_status_text = 'Đã hủy';
                                    }
                                ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 10px 10px; font-weight: 700; color: #0033a3; white-space: nowrap;">#<?php echo $rb['id']; ?></td>
                                        <td style="padding: 10px 12px;">
                                            <div style="font-weight: 700; color: #0f172a; line-height: 1.35;">
                                                <?php echo esc_html($rb['name']); ?>
                                                <?php if (!empty($rb['gender'])): ?>
                                                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">(<?php echo esc_html($rb['gender']); ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 11.5px; color: #64748b; margin-top: 2px; white-space: nowrap;">
                                                <?php echo esc_html($rb['phone']); ?>
                                            </div>
                                        </td>
                                        <td style="padding: 10px 10px; white-space: nowrap;">
                                            <span style="background: #f1f5f9; color: #334155; padding: 2px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 600; white-space: nowrap; display: inline-block; border: 1px solid #e2e8f0;">
                                                <?php echo esc_html(!empty($rb['service']) ? $rb['service'] : 'Khám & Tư vấn'); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 10px 10px; white-space: nowrap; font-size: 11.5px; color: #64748b;">
                                            <div><?php echo date('d/m/Y', strtotime($rb['created_at'])); ?></div>
                                            <div style="font-size: 11px; color: #94a3b8;"><?php echo date('H:i', strtotime($rb['created_at'])); ?></div>
                                        </td>
                                        <td style="padding: 10px 10px; text-align: right; white-space: nowrap;">
                                            <span style="<?php echo $b_status_class; ?> padding: 3px 9px; border-radius: 9999px; font-size: 11px; font-weight: 700; white-space: nowrap; display: inline-block;">
                                                <?php echo $b_status_text; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Khởi tạo Chart.js -->
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Xu Hướng 7 Ngày
            const ctxTrend = document.getElementById('floraTrendChart');
            if (ctxTrend) {
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: <?php echo json_encode($chart_labels); ?>,
                        datasets: [
                            {
                                label: 'Đơn Hàng Gói Khám',
                                data: <?php echo json_encode($chart_orders); ?>,
                                borderColor: '#0033a3',
                                backgroundColor: 'rgba(0, 51, 163, 0.1)',
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#0033a3',
                                pointRadius: 4
                            },
                            {
                                label: 'Lịch Hẹn Khám',
                                data: <?php echo json_encode($chart_bookings); ?>,
                                borderColor: '#0493f1',
                                backgroundColor: 'rgba(4, 147, 241, 0.05)',
                                tension: 0.35,
                                fill: true,
                                pointBackgroundColor: '#0493f1',
                                pointRadius: 4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 12,
                                    font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600', size: 12 }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }

            // Chart 2: Cơ Cấu Gói Dịch Vụ
            const ctxPkg = document.getElementById('floraPkgChart');
            if (ctxPkg) {
                new Chart(ctxPkg, {
                    type: 'doughnut',
                    data: {
                        labels: ['Care Plus', 'Flora White Up'],
                        datasets: [{
                            data: [<?php echo $cnt_careplus; ?>, <?php echo $cnt_whiteup; ?>],
                            backgroundColor: ['#0033a3', '#0493f1'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
    <?php
}
add_action('admin_notices', 'flora_render_custom_admin_dashboard');
