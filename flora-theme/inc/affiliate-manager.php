<?php
/**
 * Flora Dental Clinic - Affiliate & KOL Management Engine
 * Hệ thống Quản trị Tiếp thị Liên kết, KOL & Tích hợp Cổng SeaPay / MoMo
 *
 * Tính năng chính:
 * 1. Cơ sở dữ liệu wp_flora_affiliates, wp_flora_affiliate_clicks, wp_flora_affiliate_payouts
 * 2. Cú pháp chuyển khoản định danh tuyệt đối:
 *    - Đơn có REF: FLORA <MÃ_KOL> <SĐT> <MÃ_ĐƠN>
 *    - Đơn mồ côi: FLORA <SĐT> <MÃ_ĐƠN>
 * 3. Nguyên tắc chốt hoa hồng: CHỈ GHI NHẬN KHI ĐƠN HÀNG THÀNH CÔNG (payment_status = 'paid')
 * 4. Hệ thống Email HTML tự động gửi KOL khi khách thanh toán thành công
 * 5. WP-Admin Dashboard thống kê doanh thu toàn diện, modal chi tiết từng KOL, xuất Excel (CSV BOM)
 * 6. Public Portal bảo mật bằng Secret Token dành riêng cho KOL theo dõi cá nhân
 * 7. Kiến trúc Webhook chuẩn sẵn sàng tích hợp SeaPay
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 1. ĐỊNH NGHĨA TÊN BẢNG & TỰ ĐỘNG MIGRATION (SAFE DB DELTA)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_get_affiliates_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_affiliates';
}

function flora_get_affiliate_clicks_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_affiliate_clicks';
}

function flora_get_affiliate_payouts_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'flora_affiliate_payouts';
}

function flora_create_affiliate_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $aff_table    = flora_get_affiliates_table_name();
    $clicks_table = flora_get_affiliate_clicks_table_name();
    $payout_table = flora_get_affiliate_payouts_table_name();
    $orders_table = $wpdb->prefix . 'flora_orders';

    // 1. Bảng KOL / Affiliates
    $sql_aff = "CREATE TABLE IF NOT EXISTS $aff_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        phone varchar(50) NOT NULL,
        email varchar(255) NOT NULL,
        ref_code varchar(50) NOT NULL,
        secret_token varchar(64) NOT NULL,
        commission_type enum('percent', 'fixed') NOT NULL DEFAULT 'percent',
        commission_rate decimal(10,2) NOT NULL DEFAULT 10.00,
        bank_name varchar(100) DEFAULT '',
        bank_account varchar(100) DEFAULT '',
        bank_owner varchar(255) DEFAULT '',
        status enum('active', 'inactive') NOT NULL DEFAULT 'active',
        total_clicks bigint(20) unsigned NOT NULL DEFAULT 0,
        total_orders bigint(20) unsigned NOT NULL DEFAULT 0,
        paid_orders bigint(20) unsigned NOT NULL DEFAULT 0,
        total_revenue bigint(20) unsigned NOT NULL DEFAULT 0,
        total_commission bigint(20) unsigned NOT NULL DEFAULT 0,
        paid_commission bigint(20) unsigned NOT NULL DEFAULT 0,
        notes text DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_ref_code (ref_code),
        UNIQUE KEY uq_secret_token (secret_token),
        KEY idx_phone (phone),
        KEY idx_email (email),
        KEY idx_status (status)
    ) $charset_collate;";
    dbDelta($sql_aff);

    // 2. Bảng Lịch sử Clicks
    $sql_clicks = "CREATE TABLE IF NOT EXISTS $clicks_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        affiliate_id bigint(20) unsigned NOT NULL,
        ref_code varchar(50) NOT NULL,
        ip_address varchar(100) NOT NULL,
        user_agent varchar(500) DEFAULT '',
        referer_url text DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_affiliate_time (affiliate_id, created_at),
        KEY idx_ip_time (ip_address, created_at),
        KEY idx_ip_aff_time (ip_address, affiliate_id, created_at)
    ) $charset_collate;";
    dbDelta($sql_clicks);

    // 3. Bảng Lịch sử Quyết toán & Yêu Cầu Rút Tiền Hoa Hồng
    $sql_payouts = "CREATE TABLE IF NOT EXISTS $payout_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        payout_code varchar(50) DEFAULT '',
        affiliate_id bigint(20) unsigned NOT NULL,
        amount bigint(20) NOT NULL,
        status varchar(30) NOT NULL DEFAULT 'completed',
        payment_method varchar(50) DEFAULT 'bank_transfer',
        bank_name varchar(100) DEFAULT '',
        bank_account varchar(50) DEFAULT '',
        bank_owner varchar(150) DEFAULT '',
        transaction_reference varchar(100) DEFAULT '',
        notes text DEFAULT NULL,
        requested_at datetime DEFAULT CURRENT_TIMESTAMP,
        processed_at datetime DEFAULT NULL,
        created_by bigint(20) unsigned NOT NULL DEFAULT 0,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_affiliate_id (affiliate_id),
        KEY idx_status (status),
        KEY idx_payout_code (payout_code)
    ) $charset_collate;";
    dbDelta($sql_payouts);

    // Safe Migration cho bảng wp_flora_affiliate_payouts (Hỗ trợ đơn yêu cầu rút tiền từ đối tác)
    if ($wpdb->get_var("SHOW TABLES LIKE '$payout_table'") == $payout_table) {
        $existing_payout_cols = $wpdb->get_col("DESC $payout_table", 0);
        if (!empty($existing_payout_cols)) {
            if (!in_array('payout_code', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN payout_code varchar(50) DEFAULT '' AFTER id");
            }
            if (!in_array('status', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN status varchar(30) NOT NULL DEFAULT 'completed' AFTER amount");
            }
            if (!in_array('bank_name', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN bank_name varchar(100) DEFAULT '' AFTER payment_method");
            }
            if (!in_array('bank_account', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN bank_account varchar(50) DEFAULT '' AFTER bank_name");
            }
            if (!in_array('bank_owner', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN bank_owner varchar(150) DEFAULT '' AFTER bank_account");
            }
            if (!in_array('requested_at', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN requested_at datetime DEFAULT CURRENT_TIMESTAMP AFTER notes");
            }
            if (!in_array('processed_at', $existing_payout_cols)) {
                $wpdb->query("ALTER TABLE $payout_table ADD COLUMN processed_at datetime DEFAULT NULL AFTER requested_at");
            }
        }
    }

    // 4. Safe Migration cho bảng wp_flora_affiliates (Hỗ trợ mật khẩu portal, kênh truyền thông, duyệt đối tác)
    if ($wpdb->get_var("SHOW TABLES LIKE '$aff_table'") == $aff_table) {
        $existing_aff_cols = $wpdb->get_col("DESC $aff_table", 0);
        if (!empty($existing_aff_cols)) {
            if (!in_array('password_hash', $existing_aff_cols)) {
                $wpdb->query("ALTER TABLE $aff_table ADD COLUMN password_hash varchar(255) DEFAULT '' AFTER secret_token");
            }
            if (!in_array('channel_url', $existing_aff_cols)) {
                $wpdb->query("ALTER TABLE $aff_table ADD COLUMN channel_url varchar(500) DEFAULT '' AFTER email");
            }
            if (!in_array('approved_at', $existing_aff_cols)) {
                $wpdb->query("ALTER TABLE $aff_table ADD COLUMN approved_at datetime DEFAULT NULL AFTER notes");
            }
            if (!in_array('approved_by', $existing_aff_cols)) {
                $wpdb->query("ALTER TABLE $aff_table ADD COLUMN approved_by bigint(20) unsigned DEFAULT 0 AFTER approved_at");
            }
            if (!in_array('zalo_chat_id', $existing_aff_cols)) {
                $wpdb->query("ALTER TABLE $aff_table ADD COLUMN zalo_chat_id varchar(100) DEFAULT '' AFTER bank_owner");
            }
            $wpdb->query("ALTER TABLE $aff_table MODIFY COLUMN status varchar(30) NOT NULL DEFAULT 'pending'");
        }
    }

    // 5. Safe Migration cột mới cho bảng wp_flora_orders
    if ($wpdb->get_var("SHOW TABLES LIKE '$orders_table'") == $orders_table) {
        $existing_cols = $wpdb->get_col("DESC $orders_table", 0);
        if (!empty($existing_cols)) {
            if (!in_array('affiliate_code', $existing_cols)) {
                $wpdb->query("ALTER TABLE $orders_table ADD COLUMN affiliate_code varchar(50) DEFAULT '' AFTER voucher_code");
            }
            if (!in_array('affiliate_id', $existing_cols)) {
                $wpdb->query("ALTER TABLE $orders_table ADD COLUMN affiliate_id bigint(20) unsigned DEFAULT 0 AFTER affiliate_code");
            }
            if (!in_array('commission_amount', $existing_cols)) {
                $wpdb->query("ALTER TABLE $orders_table ADD COLUMN commission_amount bigint(20) NOT NULL DEFAULT 0 AFTER final_amount");
            }
            if (!in_array('commission_status', $existing_cols)) {
                $wpdb->query("ALTER TABLE $orders_table ADD COLUMN commission_status varchar(30) NOT NULL DEFAULT 'pending' AFTER commission_amount");
            }
            if (!in_array('bank_reference_code', $existing_cols)) {
                $wpdb->query("ALTER TABLE $orders_table ADD COLUMN bank_reference_code varchar(100) DEFAULT '' AFTER momo_trans_id");
            }
        }

        // Tối ưu indexes bảng wp_flora_orders
        $existing_order_keys = $wpdb->get_results("SHOW INDEX FROM $orders_table", ARRAY_A);
        $order_key_names = !empty($existing_order_keys) ? wp_list_pluck($existing_order_keys, 'Key_name') : array();
        if (!in_array('idx_affiliate', $order_key_names)) {
            $wpdb->query("ALTER TABLE $orders_table ADD INDEX idx_affiliate (affiliate_id)");
        }
        if (!in_array('idx_voucher', $order_key_names)) {
            $wpdb->query("ALTER TABLE $orders_table ADD INDEX idx_voucher (voucher_code)");
        }
        if (!in_array('idx_aff_status', $order_key_names)) {
            $wpdb->query("ALTER TABLE $orders_table ADD INDEX idx_aff_status (affiliate_id, payment_status)");
        }
    }

    // Tối ưu index bảng wp_flora_affiliate_clicks
    if ($wpdb->get_var("SHOW TABLES LIKE '$clicks_table'") == $clicks_table) {
        $existing_click_keys = $wpdb->get_results("SHOW INDEX FROM $clicks_table", ARRAY_A);
        $click_key_names = !empty($existing_click_keys) ? wp_list_pluck($existing_click_keys, 'Key_name') : array();
        if (!in_array('idx_ip_aff_time', $click_key_names)) {
            $wpdb->query("ALTER TABLE $clicks_table ADD INDEX idx_ip_aff_time (ip_address, affiliate_id, created_at)");
        }
    }
}
add_action('after_switch_theme', 'flora_create_affiliate_tables');

add_action('admin_init', function() {
    global $wpdb;
    $aff_table = flora_get_affiliates_table_name();
    if ($wpdb->get_var("SHOW TABLES LIKE '$aff_table'") != $aff_table) {
        flora_create_affiliate_tables();
    } else {
        $cols = $wpdb->get_col("DESC $aff_table", 0);
        if (!empty($cols) && !in_array('password_hash', $cols)) {
            flora_create_affiliate_tables();
        }
    }
});

add_action('init', function() {
    global $wpdb;
    $aff_table = flora_get_affiliates_table_name();
    if ($wpdb->get_var("SHOW TABLES LIKE '$aff_table'") == $aff_table) {
        $cols = $wpdb->get_col("DESC $aff_table", 0);
        if (!empty($cols) && !in_array('password_hash', $cols)) {
            flora_create_affiliate_tables();
        }
    }
});

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 2. CÁC HÀM TIỆN ÍCH CỐT LÕI (HELPERS & ATTRIBUTION LOGIC)
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Tự sinh mã REF chuẩn hóa không dấu, viết hoa (Vd: LINH, DANGKHOA, BSNGUYEN)
 */
function flora_affiliate_generate_ref_code($name = '') {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    // Loại bỏ dấu tiếng Việt và ký tự đặc biệt
    $str = remove_accents($name);
    $str = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $str));

    if (empty($str)) {
        $str = 'KOL';
    } else {
        // Lấy tối đa 10 ký tự đầu
        $str = substr($str, 0, 10);
    }

    $candidate = $str;
    $tries = 0;
    while ($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ref_code = %s", $candidate)) > 0 && $tries < 100) {
        $candidate = $str . rand(10, 999);
        $tries++;
    }

    return $candidate;
}

/**
 * Sinh Secret Token 64 ký tự an toàn cho Public Portal
 */
function flora_affiliate_generate_secret_token() {
    try {
        return bin2hex(random_bytes(32));
    } catch (Exception $e) {
        return md5(uniqid(wp_rand(), true)) . md5(microtime());
    }
}

/**
 * Tìm KOL bằng mã REF
 */
function flora_affiliate_get_by_ref($ref_code) {
    if (empty($ref_code)) return null;
    global $wpdb;
    $table = flora_get_affiliates_table_name();
    $clean_ref = strtoupper(trim(sanitize_text_field($ref_code)));
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ref_code = %s AND status = 'active' LIMIT 1", $clean_ref), ARRAY_A);
}

/**
 * Tìm KOL bằng Secret Token
 */
function flora_affiliate_get_by_token($token) {
    if (empty($token)) return null;
    global $wpdb;
    $table = flora_get_affiliates_table_name();
    $clean_token = trim(sanitize_text_field($token));
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE secret_token = %s LIMIT 1", $clean_token), ARRAY_A);
}

/**
 * Kiểm tra xem User-Agent có phải là Bot / Crawler / Scraper / Công cụ tự động không
 */
function flora_is_bot_or_crawler($ua) {
    if (empty($ua) || strlen($ua) < 16) {
        return true; // Chặn request không có User-Agent hoặc quá ngắn dị thường
    }

    $bot_patterns = array(
        // Search Engines
        'googlebot', 'google-inspectiontool', 'storebot-google', 'googleother', 'apis-google', 'feedfetcher-google',
        'bingbot', 'bingpreview', 'msnbot', 'yandex', 'baidu', 'duckduckbot', 'sogou', 'exabot', 'ia_archiver', 'seznambot', 'qwantify',
        // Social Media Crawlers & Link Preview Fetchers
        'facebookexternalhit', 'facebot', 'meta-externalagent', 'meta-externalfetcher', 'twitterbot', 'telegrambot',
        'whatsapp', 'slackbot', 'discordbot', 'skypeuripreview', 'applebot', 'pinterest', 'linkedinbot',
        'zalo', 'zalopay', 'zalo-bot', 'bytephotobot', 'bytespider', 'tiktok', 'bytedance', 'snapchat', 'viber', 'wechat',
        // SEO Tools & Audit Scrapers
        'semrushbot', 'semrush', 'ahrefsbot', 'ahrefs', 'mj12bot', 'dotbot', 'screaming frog', 'screaming',
        'rogerbot', 'sitebulb', 'blexbot', 'megaindex', 'serpstatbot', 'zoominfobot', 'woorank', 'deepcrawl',
        // Automation Tools, Headless & Script Libraries
        'curl', 'wget', 'python', 'postman', 'headless', 'phantomjs', 'selenium', 'puppeteer', 'playwright',
        'cypress', 'axios', 'go-http', 'httpclient', 'java/', 'apache-http', 'okhttp', 'guzzle', 'urllib',
        'requests', 'aiohttp', 'feedfetcher', 'restsharp', 'http_request', 'winhttp', 'faraday', 'typhoeus',
        'scrapy', 'node-fetch', 'undici', 'got/', 'superagent',
        // Uptime & Performance Monitoring
        'lighthouse', 'pagespeed', 'gtmetrix', 'uptimerobot', 'pingdom', 'site24x7', 'statuscake', 'better uptime', 'datadog', 'newrelic',
        // Security Scanners
        'scan', 'zgrab', 'nikto', 'nmap', 'masscan', 'sqlmap', 'acunetix', 'nessus', 'qualys', 'openvas', 'dirbuster', 'gobuster'
    );

    $ua_lower = strtolower($ua);
    foreach ($bot_patterns as $pattern) {
        if (strpos($ua_lower, $pattern) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * Lấy địa chỉ IP thật của khách hàng (Hỗ trợ Cloudflare, Nginx Reverse Proxy, Load Balancer)
 */
function flora_get_client_ip() {
    $ip_headers = array(
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_REAL_IP',        // Nginx proxy
        'HTTP_X_FORWARDED_FOR',  // Standard proxy
        'HTTP_CLIENT_IP',
        'REMOTE_ADDR'            // Direct connection
    );

    foreach ($ip_headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ip_list = explode(',', $_SERVER[$header]);
            $ip = trim($ip_list[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }

    return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '127.0.0.1';
}

/**
 * Ghi nhận Lượt Click / Lượt Xem REF chuẩn xác
 * Hệ thống chống Spam 8 lớp:
 * Layer 1: Chỉ cho phép HTTP Method GET hoặc POST
 * Layer 2: Chặn Browser Prefetch / Prerender / Subresource Fetch
 * Layer 3: Chặn toàn diện Bot, Web Crawler, Social Previews, SEO & Scrapers
 * Layer 4: Chặn Admin / Biên tập viên / Nhân viên phòng khám tự bấm link
 * Layer 5: Xác thực KOL tồn tại và đang hoạt động (active)
 * Layer 6: Chống Spam Cookie Debounce 24h & Rapid-click Cooldown
 * Layer 7: Bộ đệm In-Memory / Transient Fast Lock (0 MySQL query khi bị spam F5 liên tục) + Giới hạn tốc độ IP
 * Layer 8: Chuẩn công nghiệp 24 Giờ Unique Click trong Database
 */
function flora_affiliate_record_click($ref_code) {
    if (empty($ref_code)) return false;

    // Layer 1: Chỉ chấp nhận phương thức GET (landing) hoặc POST (AJAX fallback)
    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if (!in_array($method, array('GET', 'POST'))) {
        return false;
    }

    // Layer 2: Chặn trình duyệt prefetch, prerender, preview ngầm
    if (isset($_SERVER['HTTP_PURPOSE']) && in_array(strtolower($_SERVER['HTTP_PURPOSE']), array('prefetch', 'preview'))) return false;
    if (isset($_SERVER['HTTP_SEC_PURPOSE']) && preg_match('/prefetch|prerender|preview/i', $_SERVER['HTTP_SEC_PURPOSE'])) return false;
    if (isset($_SERVER['HTTP_X_MOZ']) && strtolower($_SERVER['HTTP_X_MOZ']) === 'prefetch') return false;
    if (isset($_SERVER['HTTP_X_PURPOSE']) && preg_match('/prefetch|preview/i', $_SERVER['HTTP_X_PURPOSE'])) return false;

    // Chặn fetch tài nguyên phụ (subresource / image / script)
    if (isset($_SERVER['HTTP_SEC_FETCH_DEST']) && !in_array($_SERVER['HTTP_SEC_FETCH_DEST'], array('document', 'empty', ''))) {
        return false;
    }

    // Layer 3: Chặn Robot, Web Crawler, Bot mạng xã hội tự quét link
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(substr($_SERVER['HTTP_USER_AGENT'], 0, 500)) : '';
    if (flora_is_bot_or_crawler($ua)) {
        return false;
    }

    // Layer 4: Chặn Admin / Biên tập viên / Nhân viên phòng khám tự bấm link
    if (is_user_logged_in() && current_user_can('edit_posts')) {
        return false;
    }

    // Layer 5: Xác thực KOL tồn tại và trạng thái Active
    $kol = flora_affiliate_get_by_ref($ref_code);
    if (!$kol || empty($kol['id'])) return false;

    $kol_id = (int)$kol['id'];

    // Layer 6: Chặn Spam bằng Cookie cục bộ (Zero Database Query - Tiết kiệm tối đa CPU/RAM VPS)
    $cookie_key = 'flora_clk_' . $kol_id;
    if (isset($_COOKIE[$cookie_key])) {
        return false;
    }

    // Chặn rapid-clicks (bấm liên tục nhiều link khác nhau trong vòng 5 giây)
    if (isset($_COOKIE['flora_last_click_ts']) && (time() - (int)$_COOKIE['flora_last_click_ts']) < 5) {
        return false;
    }

    $ip = flora_get_client_ip();

    // Layer 7: Bộ đệm In-Memory / Transient Fast Lock (0 MySQL query khi bị spam dồn dập)
    $ip_hash = md5($ip);
    $ip_kol_key = 'flora_aclk_' . md5($ip . '_' . $kol_id);

    // Kiểm tra cooldown 24h của IP + KOL này trong Transient
    if (get_transient($ip_kol_key)) {
        return false;
    }

    // Rate Limit tốc độ của IP: Tối đa 15 lượt click / giờ trên toàn bộ hệ thống
    $rate_key = 'flora_ip_rate_' . $ip_hash;
    $current_rate = (int) get_transient($rate_key);
    if ($current_rate >= 15) {
        return false; // Chặn triệt để click farm / flood tool
    }

    global $wpdb;
    $clicks_table = flora_get_affiliate_clicks_table_name();
    $aff_table    = flora_get_affiliates_table_name();

    // Layer 8: Kiểm tra chuẩn công nghiệp 24 Giờ Unique Click trong Database:
    // Cùng 1 IP + 1 KOL chỉ được tính 1 lượt Click duy nhất trong vòng 24 Giờ
    $recent_unique = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $clicks_table WHERE affiliate_id = %d AND ip_address = %s AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)",
        $kol_id,
        $ip
    ));

    if ($recent_unique > 0) {
        // Lưu lại transient để các request tiếp theo không cần quét MySQL
        set_transient($ip_kol_key, 1, 24 * HOUR_IN_SECONDS);
        return false;
    }

    // Ghi nhận Click thành công
    $referer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field(substr($_SERVER['HTTP_REFERER'], 0, 1000)) : '';
    $wpdb->insert($clicks_table, array(
        'affiliate_id' => $kol_id,
        'ref_code'     => $kol['ref_code'],
        'ip_address'   => $ip,
        'user_agent'   => $ua,
        'referer_url'  => $referer,
        'created_at'   => current_time('mysql')
    ), array('%d', '%s', '%s', '%s', '%s', '%s'));

    // Tăng tổng click hợp lệ
    $wpdb->query($wpdb->prepare("UPDATE $aff_table SET total_clicks = total_clicks + 1 WHERE id = %d", $kol_id));

    // Cập nhật Transients
    set_transient($ip_kol_key, 1, 24 * HOUR_IN_SECONDS);
    set_transient($rate_key, $current_rate + 1, HOUR_IN_SECONDS);

    // Đặt cookie 24h trên trình duyệt người dùng để chặn spam F5 / chuyển trang
    if (!headers_sent()) {
        $cookie_path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
        $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
        setcookie($cookie_key, '1', time() + 86400, $cookie_path, $cookie_domain, is_ssl(), true);
        setcookie('flora_last_click_ts', (string)time(), time() + 60, $cookie_path, $cookie_domain, is_ssl(), true);
        $_COOKIE[$cookie_key] = '1';
        $_COOKIE['flora_last_click_ts'] = (string)time();
    }

    return true;
}

/**
 * Tính số tiền hoa hồng của đơn hàng dựa trên thiết lập của KOL
 */
function flora_affiliate_calculate_commission($kol, $final_amount) {
    if (!$kol) return 0;
    if ($kol['commission_type'] === 'fixed') {
        return (int) $kol['commission_rate'];
    } else {
        // Tỷ lệ phần trăm
        $rate = (float) $kol['commission_rate'];
        return (int) round(($final_amount * $rate) / 100);
    }
}

/**
 * KÍCH HOẠT HOA HỒNG KHI ĐƠN HÀNG CHUYỂN SANG 'PAID' (ĐÃ THANH TOÁN THÀNH CÔNG)
 * Đảm bảo tính toàn vẹn (Atomic & Idempotent)
 */
function flora_affiliate_mark_order_paid($order_id_or_code) {
    global $wpdb;
    $orders_table = $wpdb->prefix . 'flora_orders';
    $aff_table    = flora_get_affiliates_table_name();

    // Tìm đơn hàng
    if (is_numeric($order_id_or_code)) {
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $orders_table WHERE id = %d LIMIT 1", (int)$order_id_or_code), ARRAY_A);
    } else {
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $orders_table WHERE order_code = %s LIMIT 1", sanitize_text_field($order_id_or_code)), ARRAY_A);
    }

    if (!$order) return false;

    // Nếu đơn hàng không có mã KOL (đơn mồ côi), bỏ qua không cộng hoa hồng
    if (empty($order['affiliate_code']) || empty($order['affiliate_id'])) {
        return false;
    }

    // IDEMPOTENCY: Nếu hoa hồng đã được duyệt (approved) hoặc đã chi trả (paid), không cộng dồn lại
    if ($order['commission_status'] === 'approved' || $order['commission_status'] === 'paid') {
        return false;
    }

    $kol_id = (int)$order['affiliate_id'];
    $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE id = %d LIMIT 1", $kol_id), ARRAY_A);
    if (!$kol) return false;

    $final_amount = (int)$order['final_amount'];
    $commission   = (int)$order['commission_amount'];
    if ($commission <= 0) {
        $commission = flora_affiliate_calculate_commission($kol, $final_amount);
    }

    // 1. Cập nhật trạng thái hoa hồng của đơn hàng thành 'approved'
    $wpdb->update(
        $orders_table,
        array(
            'commission_amount' => $commission,
            'commission_status' => 'approved'
        ),
        array('id' => $order['id']),
        array('%d', '%s'),
        array('%d')
    );

    // 2. Cập nhật tích lũy vào hồ sơ KOL
    $wpdb->query($wpdb->prepare(
        "UPDATE $aff_table 
         SET paid_orders = paid_orders + 1,
             total_revenue = total_revenue + %d,
             total_commission = total_commission + %d
         WHERE id = %d",
        $final_amount,
        $commission,
        $kol_id
    ));

    // 3. Gửi Email thông báo đẹp mắt cho KOL
    flora_affiliate_send_commission_email($order, $kol, $commission);

    // 4. Bắn Zalo Bot thông báo hoa hồng đã duyệt cho đối tác (KHI ĐƠN ĐƯỢC DUYỆT THÀNH CÔNG)
    if (!empty($kol['zalo_chat_id'])) {
        flora_send_zalo_affiliate_commission_approved_notification($order, $kol, $commission);
    }

    return true;
}

/**
 * Che số điện thoại bảo mật Y Khoa (Nghị định 13/2023/NĐ-CP): 091****892
 */
function flora_affiliate_mask_phone($phone) {
    $clean = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean) >= 10) {
        return substr($clean, 0, 3) . '****' . substr($clean, -3);
    }
    return $phone;
}

/**
 * Che tên khách hàng lịch sự: Nguyễn Văn An -> Nguyễn V** A**
 */
function flora_affiliate_mask_name($name) {
    $words = explode(' ', trim($name));
    if (count($words) <= 1) return $name;
    $first = $words[0];
    $masked = array($first);
    for ($i = 1; $i < count($words); $i++) {
        $w = $words[$i];
        if (mb_strlen($w, 'UTF-8') > 1) {
            $masked[] = mb_substr($w, 0, 1, 'UTF-8') . '**';
        } else {
            $masked[] = $w;
        }
    }
    return implode(' ', $masked);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 3. HỆ THỐNG EMAIL TỰ ĐỘNG THÔNG BÁO HOA HỒNG CHO KOL
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_affiliate_send_commission_email($order, $kol, $commission) {
    if (empty($kol['email']) || !is_email($kol['email'])) {
        return false;
    }

    $kol_name     = esc_html($kol['name']);
    $masked_name  = esc_html(flora_affiliate_mask_name($order['customer_name']));
    $masked_phone = esc_html(flora_affiliate_mask_phone($order['customer_phone']));
    $pkg_name     = esc_html($order['package_name']);
    $final_fmt    = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
    $comm_fmt     = number_format($commission, 0, ',', '.') . ' VNĐ';
    $order_code   = esc_html($order['order_code']);
    $time_paid    = !empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : current_time('d/m/Y H:i:s');
    
    // Link Public Dashboard cá nhân
    $portal_url   = home_url('/affiliate-portal/?token=' . $kol['secret_token']);

    $subject = "🎉 [Hoa Hồng Flora] Bạn vừa nhận được {$comm_fmt} từ đơn hàng {$order_code}!";

    // Template HTML Email Đẳng Cấp Thụy Sĩ
    $body = '
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; background-color: #f4f6fa; margin: 0; padding: 20px; color: #1e293b; }
            .email-card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 51, 163, 0.08); border: 1px solid #e2e8f0; }
            .email-header { background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); padding: 32px 28px; text-align: center; color: #ffffff; }
            .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px; }
            .email-header p { margin: 8px 0 0; font-size: 14px; opacity: 0.9; }
            .email-body { padding: 32px 28px; }
            .highlight-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 24px; }
            .highlight-label { font-size: 13px; color: #166534; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
            .highlight-val { font-size: 28px; color: #15803d; font-weight: 900; margin-top: 4px; }
            .tbl-info { width: 100%; border-collapse: collapse; margin-bottom: 28px; }
            .tbl-info td { padding: 12px 14px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
            .tbl-info td.lbl { color: #64748b; font-weight: 600; width: 40%; }
            .tbl-info td.val { color: #0f172a; font-weight: 700; text-align: right; }
            .btn-cta { display: block; text-align: center; background: #0033a3; color: #ffffff !important; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 700; font-size: 15px; margin: 20px 0; box-shadow: 0 4px 12px rgba(0, 51, 163, 0.25); }
            .email-footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
        </style>
    </head>
    <body>
        <div class="email-card">
            <div class="email-header">
                <h1>NHA KHOA FLORA</h1>
                <p>Hệ thống Đối tác & Tiếp thị Liên kết Tiêu chuẩn Thụy Sĩ</p>
            </div>
            <div class="email-body">
                <p style="font-size: 16px; margin-top: 0;">Xin chào <strong>' . $kol_name . '</strong>,</p>
                <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                    Chúc mừng bạn! Khách hàng được giới thiệu qua liên kết REF của bạn vừa hoàn tất thanh toán gói khám răng miệng tại <strong>Nha Khoa Flora</strong>. Hệ thống đã ghi nhận hoa hồng thành công:
                </p>

                <div class="highlight-box">
                    <div class="highlight-label">Hoa hồng thực nhận của bạn</div>
                    <div class="highlight-val">+' . $comm_fmt . '</div>
                </div>

                <table class="tbl-info">
                    <tr>
                        <td class="lbl">Mã đơn hàng:</td>
                        <td class="val" style="font-family: monospace; color: #0033a3;">' . $order_code . '</td>
                    </tr>
                    <tr>
                        <td class="lbl">Khách hàng:</td>
                        <td class="val">' . $masked_name . ' (' . $masked_phone . ')</td>
                    </tr>
                    <tr>
                        <td class="lbl">Gói dịch vụ:</td>
                        <td class="val">' . $pkg_name . '</td>
                    </tr>
                    <tr>
                        <td class="lbl">Giá trị đơn hàng:</td>
                        <td class="val">' . $final_fmt . '</td>
                    </tr>
                    <tr>
                        <td class="lbl">Thời gian thanh toán:</td>
                        <td class="val">' . $time_paid . '</td>
                    </tr>
                </table>

                <a href="' . esc_url($portal_url) . '" class="btn-cta" target="_blank">
                    👉 XEM DASHBOARD HOA HỒNG CỦA BẠN
                </a>

                <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-top: 24px;">
                    * Lưu ý: Hoa hồng sẽ được Nha Khoa Flora quyết toán và chuyển khoản trực tiếp vào tài khoản ngân hàng của bạn theo đúng định kỳ quy định.
                </p>
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 4px;"><strong>Nha Khoa Flora - Nụ cười rạng rỡ, tiêu chuẩn Thụy Sĩ</strong></p>
                <p style="margin: 0;">Hotline Hỗ Trợ Đối Tác / KOL: <strong>028 7305 8999</strong> | Website: nhakhoaflora.com</p>
            </div>
        </div>
    </body>
    </html>
    ';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return @wp_mail($kol['email'], $subject, $body, $headers);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 4. PUBLIC AJAX TRACKING & REWRITE CHO PUBLIC PORTAL
 * ─────────────────────────────────────────────────────────────────────────────
 */

// Endpoint AJAX Front-end để ghi nhận lượt Click bất đồng bộ
add_action('wp_ajax_flora_track_affiliate_click', 'flora_ajax_track_affiliate_click');
add_action('wp_ajax_nopriv_flora_track_affiliate_click', 'flora_ajax_track_affiliate_click');
function flora_ajax_track_affiliate_click() {
    $ref = isset($_POST['ref']) ? sanitize_text_field($_POST['ref']) : '';
    if (!empty($ref)) {
        flora_affiliate_record_click($ref);
        wp_send_json_success(array('status' => 'recorded'));
    }
    wp_send_json_error();
}

/**
 * Tự động ghi nhận REF trên toàn bộ các trang của Website (Global Site-wide REF Tracking)
 * Bất kể khách hàng vào trang chủ, bài viết, dịch vụ hay giỏ hàng kèm ?ref=XXX
 */
function flora_affiliate_capture_ref_cookie() {
    if (is_admin()) return;

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
    if ($method !== 'GET') return;

    if (isset($_GET['ref']) && !empty($_GET['ref'])) {
        $ref = strtoupper(trim(sanitize_text_field($_GET['ref'])));
        $kol = flora_affiliate_get_by_ref($ref);
        if ($kol) {
            // Set Cookie lưu trữ 30 ngày cho toàn bộ Domain
            if (!headers_sent()) {
                $cookie_path = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
                $cookie_domain = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';
                setcookie('flora_kol_ref', $ref, time() + 30 * DAY_IN_SECONDS, $cookie_path, $cookie_domain, is_ssl(), false);
                $_COOKIE['flora_kol_ref'] = $ref;
            }
            // Ghi nhận lượt click / lượt xem REF chống spam 8 lớp
            flora_affiliate_record_click($ref);
        }
    }
}
add_action('init', 'flora_affiliate_capture_ref_cookie', 1);

/**
 * Script Frontend toàn trang:
 * 1. Lưu mã REF vào LocalStorage + Cookie 30 ngày
 * 2. Đánh dấu Session để không bao giờ trigger lặp lại trong phiên
 * 3. Làm sạch URL thanh địa chỉ (History replaceState) để F5 / Reload không spam request
 */
add_action('wp_footer', 'flora_affiliate_frontend_script', 99);
function flora_affiliate_frontend_script() {
    if (is_admin()) return;
    ?>
    <script>
    (function() {
        try {
            var urlParams = new URLSearchParams(window.location.search);
            var ref = urlParams.get('ref');
            if (ref && ref.trim()) {
                var clean = ref.trim().toUpperCase();
                // 1. Lưu LocalStorage & Cookie 30 ngày cho toàn domain
                try {
                    localStorage.setItem('flora_kol_ref', clean);
                    var maxAge = 30 * 24 * 60 * 60;
                    document.cookie = "flora_kol_ref=" + encodeURIComponent(clean) + "; path=/; max-age=" + maxAge + "; SameSite=Lax";
                } catch(e) {}

                // 2. Đánh dấu Session
                try {
                    sessionStorage.setItem('flora_ref_session_' + clean, '1');
                } catch(e) {}

                // 3. Làm sạch tham số ?ref khỏi URL bằng History API để F5 không bao giờ re-trigger
                if (window.history && window.history.replaceState) {
                    urlParams.delete('ref');
                    var newSearch = urlParams.toString() ? ('?' + urlParams.toString()) : '';
                    var cleanUrl = window.location.pathname + newSearch + window.location.hash;
                    window.history.replaceState({ path: cleanUrl }, document.title, cleanUrl);
                }
            }
        } catch(err) {}
    })();
    </script>
    <?php
}

// Redirect hiển thị Public Portal nếu truy cập /affiliate-portal/, /doi-tac/, /dang-ky-doi-tac/ hoặc các trang chính sách
function flora_handle_affiliate_portal_routes() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = trim(parse_url($uri, PHP_URL_PATH), '/');

    // 1. Chính sách bảo mật thông tin khách hàng
    if ($path === 'chinh-sach-bao-mat' || $path === 'chinh-sach-bao-mat-thong-tin-khach-hang') {
        $template = get_template_directory() . '/page-templates/template-chinh-sach-bao-mat.php';
        if (file_exists($template)) {
            status_header(200);
            include $template;
            exit;
        }
    }

    // 2. Điều khoản dịch vụ & sử dụng website
    if ($path === 'dieu-khoan-dich-vu') {
        $template = get_template_directory() . '/page-templates/template-dieu-khoan-dich-vu.php';
        if (file_exists($template)) {
            status_header(200);
            include $template;
            exit;
        }
    }

    // 3. Chính sách bảo hành dịch vụ nha khoa
    if ($path === 'chinh-sach-bao-hanh') {
        $template = get_template_directory() . '/page-templates/template-chinh-sach-bao-hanh.php';
        if (file_exists($template)) {
            status_header(200);
            include $template;
            exit;
        }
    }

    // 4. Đăng ký đối tác / KOL
    if ($path === 'dang-ky-doi-tac') {
        $template = get_template_directory() . '/page-templates/template-dang-ky-doi-tac.php';
        if (file_exists($template)) {
            status_header(200);
            include $template;
            exit;
        }
    }

    // 5. Cổng thông tin đối tác / Public Portal
    if (strpos($uri, '/affiliate-portal') !== false || $path === 'doi-tac' || $path === 'cong-thong-tin-doi-tac' || isset($_GET['view_kol'])) {
        $template = get_template_directory() . '/page-templates/template-affiliate-portal.php';
        if (file_exists($template)) {
            status_header(200);
            include $template;
            exit;
        }
    }
}
add_action('template_redirect', 'flora_handle_affiliate_portal_routes', 1);

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 5. SEAPAY WEBHOOK INTEGRATION (SEAPAY-READY ENDPOINT)
 * ─────────────────────────────────────────────────────────────────────────────
 */
add_action('rest_api_init', function () {
    register_rest_route('flora/v1', '/seapay-webhook', array(
        'methods'  => array('GET', 'POST'),
        'callback' => 'flora_handle_seapay_webhook',
        'permission_callback' => '__return_true'
    ));
});

function flora_parse_payment_content($content) {
    if (empty($content)) return false;
    $content = strtoupper(trim($content));

    // Pattern 1 (Đơn chuẩn có mã KOL & SĐT): FLORA <MÃ_KOL> <SĐT> <MÃ_ĐƠN_5_SỐ>
    // Vd: FLORA LINH 0912345678 89214 hoặc FLORA BSNAM 0903333444 12345
    if (preg_match('/FLORA\s+([A-Z0-9_-]+)\s+(0[35789][0-9]{8})\s+([0-9]{5})/i', $content, $matches)) {
        return array(
            'has_ref'    => true,
            'ref_code'   => strtoupper($matches[1]),
            'phone'      => $matches[2],
            'order_code' => 'FLORA' . $matches[3]
        );
    }

    // Pattern 2 (Có mã KOL & mã đơn, khách quên hoặc ngân hàng cắt bớt SĐT): FLORA <MÃ_KOL> <MÃ_ĐƠN_5_SỐ>
    // Vd: FLORA LINH 89214
    if (preg_match('/FLORA\s+([A-Z0-9_-]{2,20})\s+([0-9]{5})/i', $content, $matches)) {
        $possible_ref = strtoupper($matches[1]);
        if (function_exists('flora_affiliate_get_by_ref') && flora_affiliate_get_by_ref($possible_ref)) {
            return array(
                'has_ref'    => true,
                'ref_code'   => $possible_ref,
                'phone'      => '',
                'order_code' => 'FLORA' . $matches[2]
            );
        }
    }

    // Pattern 3 (Đơn mồ côi không có REF): FLORA <SĐT> <MÃ_ĐƠN_5_SỐ>
    // Vd: FLORA 0912345678 89214
    if (preg_match('/FLORA\s+(0[35789][0-9]{8})\s+([0-9]{5})/i', $content, $matches)) {
        return array(
            'has_ref'    => false,
            'ref_code'   => '',
            'phone'      => $matches[1],
            'order_code' => 'FLORA' . $matches[2]
        );
    }

    // Pattern 4: Fallback bắt mã đơn FLORA 12345 hoặc FLORA12345
    if (preg_match('/FLORA\s*([0-9]{5})/i', $content, $matches)) {
        return array(
            'has_ref'    => false,
            'ref_code'   => '',
            'phone'      => '',
            'order_code' => 'FLORA' . $matches[1]
        );
    }

    return false;
}

function flora_handle_seapay_webhook($request) {
    $raw_input = file_get_contents('php://input');
    $data = json_decode($raw_input, true);
    if (empty($data)) {
        $data = $_POST;
    }

    // Xác thực API Key nếu có cấu hình trong Admin
    $seapay_secret = get_option('flora_seapay_api_secret', '');
    if (!empty($seapay_secret)) {
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (strpos($auth_header, $seapay_secret) === false && (!isset($_GET['secret']) || $_GET['secret'] !== $seapay_secret)) {
            return new WP_REST_Response(array('success' => false, 'message' => 'Unauthorized SeaPay Request'), 401);
        }
    }

    if (empty($data) || !isset($data['content'])) {
        return new WP_REST_Response(array('success' => false, 'message' => 'Invalid SeaPay Payload'), 400);
    }

    $transfer_content = $data['content'];
    $transfer_amount  = isset($data['transferAmount']) ? (int)$data['transferAmount'] : 0;
    $ref_bank         = isset($data['referenceCode']) ? sanitize_text_field($data['referenceCode']) : (isset($data['id']) ? sanitize_text_field($data['id']) : '');

    $parsed = flora_parse_payment_content($transfer_content);
    if (!$parsed) {
        return new WP_REST_Response(array('success' => false, 'message' => 'Transfer content does not match Flora syntax'), 200);
    }

    global $wpdb;
    $orders_table = $wpdb->prefix . 'flora_orders';

    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $orders_table WHERE order_code = %s LIMIT 1", $parsed['order_code']), ARRAY_A);
    if (!$order) {
        return new WP_REST_Response(array('success' => false, 'message' => 'Order not found: ' . $parsed['order_code']), 200);
    }

    // Nếu đơn đã thanh toán rồi, báo thành công luôn
    if ($order['payment_status'] === 'paid') {
        return new WP_REST_Response(array('success' => true, 'message' => 'Order was already processed successfully'), 200);
    }

    // Kiểm tra số tiền chuyển có đủ đơn hàng không
    if ($transfer_amount > 0 && $transfer_amount < (int)$order['final_amount']) {
        return new WP_REST_Response(array('success' => false, 'message' => 'Transfer amount is less than order amount'), 200);
    }

    // Gán bù KOL nếu đơn hàng lúc tạo bị mất cookie nhưng nội dung chuyển khoản có mã KOL chuẩn xác
    if (empty($order['affiliate_id']) && !empty($parsed['has_ref']) && !empty($parsed['ref_code'])) {
        $kol = flora_affiliate_get_by_ref($parsed['ref_code']);
        if ($kol) {
            $comm = flora_affiliate_calculate_commission($kol, (int)$order['final_amount']);
            $wpdb->update(
                $orders_table,
                array(
                    'affiliate_id'      => (int)$kol['id'],
                    'affiliate_code'    => $kol['ref_code'],
                    'commission_amount' => $comm
                ),
                array('id' => $order['id']),
                array('%d', '%s', '%d'),
                array('%d')
            );
            $order['affiliate_id']      = $kol['id'];
            $order['affiliate_code']    = $kol['ref_code'];
            $order['commission_amount'] = $comm;
        }
    }

    // Cập nhật trạng thái đơn hàng thành Paid
    $wpdb->update(
        $orders_table,
        array(
            'payment_status'      => 'paid',
            'paid_at'             => current_time('mysql'),
            'bank_reference_code' => $ref_bank,
            'payment_method'      => 'seapay_bank'
        ),
        array('id' => $order['id']),
        array('%s', '%s', '%s', '%s'),
        array('%d')
    );

    // Kích hoạt hoa hồng và gửi Email cho KOL
    flora_affiliate_mark_order_paid($order['id']);

    // Cập nhật doanh thu và tiền giảm cho Voucher Engine
    if (function_exists('flora_voucher_update_stats_on_order_paid')) {
        flora_voucher_update_stats_on_order_paid($order['order_code'], $transfer_amount > 0 ? $transfer_amount : $order['final_amount']);
    }

    // Gửi email thông báo cho Admin
    if (function_exists('flora_notify_admin_order_paid')) {
        flora_notify_admin_order_paid($order, $ref_bank);
    }

    return new WP_REST_Response(array(
        'success'    => true,
        'message'    => 'Order ' . $order['order_code'] . ' marked as paid successfully via SeaPay',
        'order_code' => $order['order_code']
    ), 200);
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 6. GIAO DIỆN WP-ADMIN: AFFILIATE DASHBOARD, MODAL CHI TIẾT & CÀI ĐẶT
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_register_affiliates_admin_menu() {
    add_menu_page(
        'Quản Lý Affiliate & KOL - Flora Clinic',
        'KOL / Affiliate',
        'manage_options',
        'flora-affiliates',
        'flora_render_affiliates_admin_page',
        'dashicons-networking',
        5.2
    );

    add_submenu_page(
        'flora-affiliates',
        'Tổng Quan & Danh Sách KOL',
        'Tổng Quan & KOLs',
        'manage_options',
        'flora-affiliates',
        'flora_render_affiliates_admin_page'
    );

    global $wpdb;
    $payout_tbl = flora_get_affiliate_payouts_table_name();
    $pending_payout_cnt = 0;
    if ($wpdb->get_var("SHOW TABLES LIKE '$payout_tbl'") == $payout_tbl) {
        $pending_payout_cnt = (int)$wpdb->get_var("SELECT COUNT(*) FROM $payout_tbl WHERE status = 'requested'");
    }
    $payout_badge = $pending_payout_cnt > 0 ? " <span class='awaiting-mod count-$pending_payout_cnt' style='background:#d97706;color:#ffffff;border-radius:9999px;padding:2px 7px;font-size:10px;font-weight:700;'>$pending_payout_cnt</span>" : '';

    add_submenu_page(
        'flora-affiliates',
        'Yêu Cầu Rút Tiền & Quyết Toán Hoa Hồng',
        'Đơn Rút Tiền' . $payout_badge,
        'manage_options',
        'flora-affiliate-payouts',
        'flora_render_affiliate_payouts_admin_page'
    );

    add_submenu_page(
        'flora-affiliates',
        'Quản Lý Voucher & Mã Ưu Đãi',
        'Quản Lý Voucher',
        'manage_options',
        'flora-vouchers',
        'flora_render_voucher_admin_page'
    );

    add_submenu_page(
        'flora-affiliates',
        'Cài Đặt Cổng SeaPay',
        'Cài Đặt SeaPay',
        'manage_options',
        'flora-seapay-settings',
        'flora_render_seapay_settings_page'
    );
}
add_action('admin_menu', 'flora_register_affiliates_admin_menu');

/**
 * Xuất danh sách đơn hàng đối soát của KOL sang file CSV (UTF-8 BOM)
 */
function flora_export_affiliate_orders_csv() {
    if (!isset($_GET['page']) || $_GET['page'] !== 'flora-affiliates' || !isset($_GET['action']) || $_GET['action'] !== 'export_kol_csv') {
        return;
    }

    check_admin_referer('flora_export_kol_nonce');
    if (!current_user_can('manage_options')) {
        wp_die('Bạn không có quyền truy cập trang này.');
    }

    global $wpdb;
    $kol_id = isset($_GET['kol_id']) ? (int)$_GET['kol_id'] : 0;
    $orders_table = $wpdb->prefix . 'flora_orders';
    $aff_table    = flora_get_affiliates_table_name();

    if ($kol_id > 0) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE id = %d LIMIT 1", $kol_id), ARRAY_A);
        $filename = 'Flora_Doi_Soat_KOL_' . ($kol ? $kol['ref_code'] : $kol_id) . '_' . date('Y-m-d') . '.csv';
        $orders = $wpdb->get_results($wpdb->prepare("SELECT * FROM $orders_table WHERE affiliate_id = %d ORDER BY created_at DESC", $kol_id), ARRAY_A);
    } else {
        $filename = 'Flora_Doi_Soat_Tat_Ca_Affiliates_' . date('Y-m-d') . '.csv';
        $orders = $wpdb->get_results("SELECT * FROM $orders_table WHERE affiliate_id > 0 ORDER BY created_at DESC", ARRAY_A);
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // UTF-8 BOM
    echo "\xEF\xBB\xBF";

    $output = fopen('php://output', 'w');
    fputcsv($output, array('ID', 'Mã Đơn', 'Mã REF KOL', 'Khách Hàng', 'Số Điện Thoại', 'Gói Dịch Vụ', 'Số Tiền (VNĐ)', 'Hoa Hồng KOL (VNĐ)', 'Trạng Thái Đơn', 'Trạng Thái Hoa Hồng', 'Ngày Tạo', 'Ngày Thanh Toán'));

    if (!empty($orders)) {
        foreach ($orders as $o) {
            $status_str = 'Chờ thanh toán';
            if ($o['payment_status'] === 'paid') $status_str = 'Đã thanh toán';
            elseif ($o['payment_status'] === 'failed') $status_str = 'Thất bại';
            elseif ($o['payment_status'] === 'cancelled') $status_str = 'Đã hủy';

            $comm_status_str = 'Chờ duyệt';
            if ($o['commission_status'] === 'approved') $comm_status_str = 'Đã duyệt';
            elseif ($o['commission_status'] === 'paid') $comm_status_str = 'Đã chuyển khoản';

            fputcsv($output, array(
                $o['id'],
                $o['order_code'],
                $o['affiliate_code'],
                $o['customer_name'],
                $o['customer_phone'],
                $o['package_name'],
                $o['final_amount'],
                $o['commission_amount'],
                $status_str,
                $comm_status_str,
                $o['created_at'],
                $o['paid_at']
            ));
        }
    }
    fclose($output);
    exit;
}
add_action('admin_init', 'flora_export_affiliate_orders_csv');

/**
 * Xử lý AJAX Thêm mới / Cập nhật KOL
 */
add_action('wp_ajax_flora_save_affiliate', 'flora_ajax_save_affiliate');
function flora_ajax_save_affiliate() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền thực hiện.'));
    }

    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name      = sanitize_text_field($_POST['name'] ?? '');
    $phone     = sanitize_text_field($_POST['phone'] ?? '');
    $email     = sanitize_email($_POST['email'] ?? '');
    $ref_code  = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', trim($_POST['ref_code'] ?? '')));
    $comm_type = in_array($_POST['commission_type'] ?? '', array('percent', 'fixed')) ? $_POST['commission_type'] : 'percent';
    $comm_rate = floatval($_POST['commission_rate'] ?? 10);
    $bank_name = sanitize_text_field($_POST['bank_name'] ?? '');
    $bank_acc  = sanitize_text_field($_POST['bank_account'] ?? '');
    $bank_own  = sanitize_text_field($_POST['bank_owner'] ?? '');
    $status    = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';
    $notes     = sanitize_textarea_field($_POST['notes'] ?? '');

    if (empty($name) || empty($phone) || empty($email)) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ Tên, Số điện thoại và Email của KOL!'));
    }

    if (empty($ref_code)) {
        $ref_code = flora_affiliate_generate_ref_code($name);
    }

    // Kiểm tra trùng ref_code
    if ($id > 0) {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ref_code = %s AND id != %d", $ref_code, $id));
    } else {
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE ref_code = %s", $ref_code));
    }

    if ($exists > 0) {
        wp_send_json_error(array('message' => 'Mã REF "' . $ref_code . '" đã được sử dụng. Vui lòng chọn mã khác!'));
    }

    if ($id > 0) {
        // Cập nhật
        $wpdb->update(
            $table,
            array(
                'name'            => $name,
                'phone'           => $phone,
                'email'           => $email,
                'ref_code'        => $ref_code,
                'commission_type' => $comm_type,
                'commission_rate' => $comm_rate,
                'bank_name'       => $bank_name,
                'bank_account'    => $bank_acc,
                'bank_owner'      => $bank_own,
                'status'          => $status,
                'notes'           => $notes
            ),
            array('id' => $id),
            array('%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s'),
            array('%d')
        );
        wp_send_json_success(array('message' => 'Cập nhật thông tin KOL thành công!'));
    } else {
        // Tạo mới
        $secret_token = flora_affiliate_generate_secret_token();
        $wpdb->insert(
            $table,
            array(
                'name'            => $name,
                'phone'           => $phone,
                'email'           => $email,
                'ref_code'        => $ref_code,
                'secret_token'    => $secret_token,
                'commission_type' => $comm_type,
                'commission_rate' => $comm_rate,
                'bank_name'       => $bank_name,
                'bank_account'    => $bank_acc,
                'bank_owner'      => $bank_own,
                'status'          => $status,
                'notes'           => $notes,
                'created_at'      => current_time('mysql')
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
        );
        wp_send_json_success(array('message' => 'Tạo mới KOL thành công!'));
    }
}

/**
 * Xử lý AJAX Lấy chi tiết KOL & Danh sách đơn hàng
 */
add_action('wp_ajax_flora_get_affiliate_detail', 'flora_ajax_get_affiliate_detail');
function flora_ajax_get_affiliate_detail() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền.'));
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) wp_send_json_error(array('message' => 'Thiếu ID KOL.'));

    global $wpdb;
    $aff_table    = flora_get_affiliates_table_name();
    $orders_table = $wpdb->prefix . 'flora_orders';

    $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE id = %d LIMIT 1", $id), ARRAY_A);
    if (!$kol) wp_send_json_error(array('message' => 'Không tìm thấy KOL.'));

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT id, order_code, customer_name, customer_phone, package_name, final_amount, commission_amount, payment_status, commission_status, created_at, paid_at 
         FROM $orders_table 
         WHERE affiliate_id = %d 
         ORDER BY created_at DESC LIMIT 100",
        $id
    ), ARRAY_A);

    $pending_payout = max(0, $kol['total_commission'] - $kol['paid_commission']);
    $ref_link       = home_url('/goi-dich-vu?ref=' . $kol['ref_code']);
    $portal_link    = home_url('/affiliate-portal/?token=' . $kol['secret_token']);
    $export_url     = wp_nonce_url(admin_url('admin.php?page=flora-affiliates&action=export_kol_csv&kol_id=' . $id), 'flora_export_kol_nonce');

    wp_send_json_success(array(
        'kol'            => $kol,
        'orders'         => $orders,
        'pending_payout' => $pending_payout,
        'ref_link'       => $ref_link,
        'portal_link'    => $portal_link,
        'export_url'     => $export_url
    ));
}

/**
 * Xử lý AJAX Quyết toán / Ghi nhận chuyển tiền hoa hồng cho KOL
 */
add_action('wp_ajax_flora_save_affiliate_payout', 'flora_ajax_save_affiliate_payout');
function flora_ajax_save_affiliate_payout() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền.'));
    }

    $kol_id = isset($_POST['kol_id']) ? (int)$_POST['kol_id'] : 0;
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
    $ref    = sanitize_text_field($_POST['reference'] ?? '');
    $notes  = sanitize_textarea_field($_POST['notes'] ?? '');

    if ($kol_id <= 0 || $amount <= 0) {
        wp_send_json_error(array('message' => 'Số tiền quyết toán không hợp lệ!'));
    }

    global $wpdb;
    $aff_table    = flora_get_affiliates_table_name();
    $payout_table = flora_get_affiliate_payouts_table_name();

    $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE id = %d LIMIT 1", $kol_id), ARRAY_A);
    if (!$kol) wp_send_json_error(array('message' => 'Không tìm thấy KOL.'));

    // Ghi log payout với status = 'completed'
    $wpdb->insert($payout_table, array(
        'payout_code'           => 'PO' . date('ymd') . '-' . rand(1000, 9999),
        'affiliate_id'          => $kol_id,
        'amount'                => $amount,
        'status'                => 'completed',
        'payment_method'        => 'bank_transfer',
        'bank_name'             => $kol['bank_name'],
        'bank_account'          => $kol['bank_account'],
        'bank_owner'            => $kol['bank_owner'],
        'transaction_reference' => $ref,
        'notes'                 => $notes,
        'requested_at'          => current_time('mysql'),
        'processed_at'          => current_time('mysql'),
        'created_by'            => get_current_user_id(),
        'created_at'            => current_time('mysql')
    ), array('%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'));

    // Đánh dấu các đơn yêu cầu rút tiền đang chờ của đối tác này thành completed
    $pending_wds = $wpdb->get_results($wpdb->prepare(
        "SELECT id, amount FROM $payout_table WHERE affiliate_id = %d AND status = 'requested' ORDER BY id ASC",
        $kol_id
    ), ARRAY_A);
    $remaining_settle = $amount;
    if (!empty($pending_wds)) {
        foreach ($pending_wds as $pwd) {
            if ($remaining_settle >= $pwd['amount']) {
                $wpdb->update($payout_table, array(
                    'status' => 'completed',
                    'transaction_reference' => $ref,
                    'processed_at' => current_time('mysql')
                ), array('id' => $pwd['id']));
                $remaining_settle -= $pwd['amount'];
            }
        }
    }

    // Cập nhật paid_commission
    $wpdb->query($wpdb->prepare(
        "UPDATE $aff_table SET paid_commission = paid_commission + %d WHERE id = %d",
        $amount,
        $kol_id
    ));

    wp_send_json_success(array('message' => 'Đã ghi nhận quyết toán ' . number_format($amount, 0, ',', '.') . 'đ cho KOL thành công!'));
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * TÍNH TOÁN DÒNG TIỀN HOA HỒNG CHI TIẾT CỦA ĐỐI TÁC:
 * - Hoa hồng dự kiến (Đơn chờ đối soát / pending)
 * - Hoa hồng xác thực (Đơn đã thanh toán thành công / approved)
 * - Hoa hồng đã chi trả (Đã quyết toán)
 * - Hoa hồng đang có đơn yêu cầu rút tiền (status = 'requested')
 * - Số dư khả dụng thực tế để gửi đơn rút tiền
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_affiliate_get_financial_stats($kol_id) {
    global $wpdb;
    $kol_id = (int)$kol_id;
    if ($kol_id <= 0) {
        return array(
            'confirmed_commission' => 0,
            'confirmed_orders'     => 0,
            'pending_commission'   => 0,
            'pending_orders'       => 0,
            'total_orders'         => 0,
            'total_revenue'        => 0,
            'paid_commission'      => 0,
            'requested_payout'     => 0,
            'available_balance'    => 0
        );
    }

    $orders_table = function_exists('flora_get_orders_table_name') ? flora_get_orders_table_name() : $wpdb->prefix . 'flora_orders';
    $payout_table = flora_get_affiliate_payouts_table_name();
    $aff_table    = flora_get_affiliates_table_name();

    // 1. Thống kê từ đơn hàng
    $order_stats = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            COALESCE(SUM(CASE WHEN payment_status IN ('paid', 'completed') OR commission_status = 'confirmed' THEN commission_amount ELSE 0 END), 0) as confirmed_commission,
            COALESCE(SUM(CASE WHEN payment_status IN ('paid', 'completed') OR commission_status = 'confirmed' THEN 1 ELSE 0 END), 0) as confirmed_orders_count,
            COALESCE(SUM(CASE WHEN payment_status NOT IN ('paid', 'completed', 'rejected', 'cancelled') AND commission_status != 'confirmed' THEN commission_amount ELSE 0 END), 0) as pending_commission,
            COALESCE(SUM(CASE WHEN payment_status NOT IN ('paid', 'completed', 'rejected', 'cancelled') AND commission_status != 'confirmed' THEN 1 ELSE 0 END), 0) as pending_orders_count,
            COALESCE(SUM(CASE WHEN payment_status IN ('paid', 'completed') OR commission_status = 'confirmed' THEN final_amount ELSE 0 END), 0) as total_revenue,
            COUNT(*) as total_orders_count
         FROM $orders_table 
         WHERE affiliate_id = %d",
        $kol_id
    ), ARRAY_A);

    $confirmed_commission = (int)($order_stats['confirmed_commission'] ?? 0);
    $pending_commission   = (int)($order_stats['pending_commission'] ?? 0);
    $confirmed_orders     = (int)($order_stats['confirmed_orders_count'] ?? 0);
    $pending_orders       = (int)($order_stats['pending_orders_count'] ?? 0);
    $total_orders         = (int)($order_stats['total_orders_count'] ?? 0);
    $total_revenue        = (int)($order_stats['total_revenue'] ?? 0);

    // 2. Thống kê từ bảng quyết toán / rút tiền
    $payout_stats = array('paid_commission' => 0, 'requested_payout' => 0);
    if ($wpdb->get_var("SHOW TABLES LIKE '$payout_table'") == $payout_table) {
        $payout_stats = $wpdb->get_row($wpdb->prepare(
            "SELECT 
                COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as paid_commission,
                COALESCE(SUM(CASE WHEN status IN ('requested', 'pending') THEN amount ELSE 0 END), 0) as requested_payout
             FROM $payout_table 
             WHERE affiliate_id = %d",
            $kol_id
        ), ARRAY_A);
    }

    $kol_record = $wpdb->get_row($wpdb->prepare("SELECT paid_commission FROM $aff_table WHERE id = %d LIMIT 1", $kol_id), ARRAY_A);
    $recorded_paid = (int)($kol_record['paid_commission'] ?? 0);

    $paid_commission  = max($recorded_paid, (int)($payout_stats['paid_commission'] ?? 0));
    $requested_payout = (int)($payout_stats['requested_payout'] ?? 0);

    // Số dư xác thực khả dụng để rút = Hoa hồng đã duyệt - Đã chuyển - Đang có đơn chờ chuyển
    $available_balance = max(0, $confirmed_commission - $paid_commission - $requested_payout);

    return array(
        'confirmed_commission' => $confirmed_commission, // Hoa hồng xác thực (Đã duyệt)
        'confirmed_orders'     => $confirmed_orders,
        'pending_commission'   => $pending_commission,   // Hoa hồng dự kiến (Đơn chờ đối soát)
        'pending_orders'       => $pending_orders,
        'total_orders'         => $total_orders,
        'total_revenue'        => $total_revenue,
        'paid_commission'      => $paid_commission,      // Đã thanh toán thành công
        'requested_payout'     => $requested_payout,     // Đang chờ Flora chuyển khoản
        'available_balance'    => $available_balance     // Số dư có thể gửi đơn rút ngay
    );
}

/**
 * BẮN NOTIFICATION VÀO GROUP ZALO KHI ĐỐI TÁC GỬI ĐƠN RÚT TIỀN
 * (Chỉ gửi thông báo kiểm tra đối soát, KHÔNG gắn nút duyệt)
 */
function flora_send_zalo_withdrawal_notification($payout, $kol, $financial_stats = array()) {
    $config = function_exists('flora_get_payment_config') ? flora_get_payment_config() : array();
    $bot_token     = trim($config['zalo_bot_token'] ?? get_option('flora_zalo_bot_token', ''));
    $group_chat_id = trim($config['zalo_group_chat_id'] ?? get_option('flora_zalo_group_chat_id', ''));
    $webhook_url   = trim($config['zalo_webhook_url'] ?? get_option('flora_zalo_webhook_url', ''));

    if (empty($bot_token) && empty($webhook_url)) {
        return false;
    }

    $kol_name   = $kol['name'] ?? 'Đối tác Flora';
    $kol_phone  = $kol['phone'] ?? 'Chưa rõ';
    $ref_code   = $kol['ref_code'] ?? 'Chưa có';
    $payout_code= $payout['payout_code'] ?? ('WD' . time());
    $amount     = (int)($payout['amount'] ?? 0);
    $amount_fmt = number_format($amount, 0, ',', '.') . ' VNĐ';

    $bank_name   = !empty($payout['bank_name']) ? $payout['bank_name'] : ($kol['bank_name'] ?? 'Chưa cập nhật');
    $bank_account= !empty($payout['bank_account']) ? $payout['bank_account'] : ($kol['bank_account'] ?? 'Chưa cập nhật');
    $bank_owner  = !empty($payout['bank_owner']) ? $payout['bank_owner'] : ($kol['bank_owner'] ?? 'Chưa cập nhật');
    $notes       = !empty($payout['notes']) ? $payout['notes'] : 'Yêu cầu rút hoa hồng đối tác';

    $available_fmt = isset($financial_stats['available_balance']) ? number_format($financial_stats['available_balance'], 0, ',', '.') . ' VNĐ' : 'Chưa rõ';
    $pending_fmt   = isset($financial_stats['pending_commission']) ? number_format($financial_stats['pending_commission'], 0, ',', '.') . ' VNĐ' : '0 VNĐ';

    $message = "[ YÊU CẦU RÚT TIỀN HOA HỒNG ĐỐI TÁC ]\n"
             . "━━━━━━━\n"
             . "Mã đơn rút: #" . $payout_code . "\n"
             . "- Đối tác: " . $kol_name . " (" . $kol_phone . ") - REF: " . $ref_code . "\n"
             . "- Số tiền yêu cầu rút: " . $amount_fmt . "\n"
             . "- Số dư khả dụng: " . $available_fmt . "\n"
             . "- Hoa hồng chờ đối soát: " . $pending_fmt . "\n"
             . "- Tài khoản thụ hưởng: " . $bank_name . " - STK: " . $bank_account . " (" . $bank_owner . ")\n"
             . "- Ghi chú đối tác: " . $notes . "\n"
             . "━━━━━━━\n"
             . "LƯU Ý BỘ PHẬN KẾ TOÁN:\n"
             . "  └─ Thông báo kiểm tra đối soát (kế toán đối soát sao kê và chuyển khoản).\n"
             . "  └─ Thời gian gửi: " . current_time('d/m/Y H:i:s');

    // 1. Gửi qua Zalo Bot Platform (nhóm CSKH/Kế toán)
    if (!empty($bot_token) && !empty($group_chat_id)) {
        $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
        wp_remote_post($zalo_api_url, array(
            'method'      => 'POST',
            'timeout'     => 5,
            'blocking'    => true,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'chat_id' => $group_chat_id,
                'text'    => $message
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    // 2. Gửi tiếp qua webhook trung gian nếu có
    if (!empty($webhook_url)) {
        $payload = array(
            'event'             => 'affiliate_withdrawal_requested',
            'title'             => '[ YÊU CẦU RÚT TIỀN HOA HỒNG ]',
            'message'           => $message,
            'text'              => $message,
            'payout_code'       => $payout_code,
            'affiliate_id'      => $kol['id'],
            'affiliate_name'    => $kol_name,
            'affiliate_phone'   => $kol_phone,
            'affiliate_ref'     => $ref_code,
            'amount'            => $amount,
            'amount_formatted'  => $amount_fmt,
            'bank_name'         => $bank_name,
            'bank_account'      => $bank_account,
            'bank_owner'        => $bank_owner,
            'notes'             => $notes,
            'created_at'        => current_time('mysql')
        );

        wp_remote_post($webhook_url, array(
            'method'      => 'POST',
            'timeout'     => 5,
            'blocking'    => false,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode($payload, JSON_UNESCAPED_UNICODE)
        ));
    }

    return true;
}

/**
 * BẮN NOTIFICATION VÀO ZALO CHO ĐỐI TÁC KHI ĐƠN HÀNG GIỚI THIỆU ĐƯỢC DUYỆT THÀNH CÔNG
 * (Chỉ gửi khi đơn hàng chuyển sang trạng thái Paid / Approved)
 */
function flora_send_zalo_affiliate_commission_approved_notification($order, $kol, $commission) {
    if (empty($kol['zalo_chat_id'])) return false;

    $config = function_exists('flora_get_payment_config') ? flora_get_payment_config() : array();
    $bot_token = trim($config['zalo_bot_token'] ?? get_option('flora_zalo_bot_token', ''));
    if (empty($bot_token)) return false;

    $kol_name     = $kol['name'] ?? 'Đối tác Flora';
    $order_code   = $order['order_code'] ?? 'N/A';
    $package_name = $order['package_name'] ?? 'Gói dịch vụ nha khoa';
    $final_amt    = number_format((int)($order['final_amount'] ?? 0), 0, ',', '.') . ' VNĐ';
    $comm_amt     = number_format((int)$commission, 0, ',', '.') . ' VNĐ';
    $rate_text    = $kol['commission_rate'] . ($kol['commission_type'] === 'fixed' ? 'đ' : '%');

    // Số dư ví khả dụng mới nhất
    $stats = flora_affiliate_get_financial_stats($kol['id']);
    $avail_fmt = number_format($stats['available_balance'], 0, ',', '.') . ' VNĐ';
    $portal_url = home_url('/doi-tac/?token=' . ($kol['secret_token'] ?? ''));

    $msg = "🎉 [ HOA HỒNG MỚI ĐÃ ĐƯỢC DUYỆT ] 🎉\n"
         . "━━━━━━━\n"
         . "Chào " . $kol_name . "! Đơn hàng từ liên kết của bạn vừa được Flora đối soát & duyệt thanh toán thành công:\n\n"
         . "• Mã đơn hàng: #" . $order_code . "\n"
         . "• Gói dịch vụ: " . $package_name . "\n"
         . "• Doanh thu đơn: " . $final_amt . "\n"
         . "💰 HOA HỒNG BẠN NHẬN: +" . $comm_amt . " (" . $rate_text . ")\n"
         . "━━━━━━━\n"
         . "💳 Số dư khả dụng hiện tại: " . $avail_fmt . "\n"
         . "👉 Vào Cổng Đối Tác rút tiền: " . $portal_url . "\n"
         . "⏰ Thời gian duyệt: " . current_time('d/m/Y H:i:s');

    $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
    wp_remote_post($zalo_api_url, array(
        'method'      => 'POST',
        'timeout'     => 5,
        'blocking'    => false,
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => wp_json_encode(array(
            'chat_id' => $kol['zalo_chat_id'],
            'text'    => $msg
        ), JSON_UNESCAPED_UNICODE)
    ));

    return true;
}

/**
 * BẮN THÔNG BÁO REALTIME ZALO BOT KHI ADMIN DUYỆT HOẶC TỪ CHỐI ĐƠN RÚT TIỀN
 * (Gửi cho nhóm Kế Toán Flora VÀ gửi thông báo kết quả cho chính Đối Tác nếu đã liên kết Zalo)
 */
function flora_send_zalo_payout_processed_notification($payout, $kol, $action = 'completed', $reason = '') {
    $config = function_exists('flora_get_payment_config') ? flora_get_payment_config() : array();
    $bot_token     = trim($config['zalo_bot_token'] ?? get_option('flora_zalo_bot_token', ''));
    $group_chat_id = trim($config['zalo_group_chat_id'] ?? get_option('flora_zalo_group_chat_id', ''));

    if (empty($bot_token)) return false;

    $kol_name    = $kol['name'] ?? 'Đối tác Flora';
    $kol_phone   = $kol['phone'] ?? 'Chưa rõ';
    $ref_code    = $kol['ref_code'] ?? 'Chưa có';
    $payout_code = $payout['payout_code'] ?? ('WD' . time());
    $amount_fmt  = number_format((int)($payout['amount'] ?? 0), 0, ',', '.') . ' VNĐ';

    if ($action === 'completed') {
        $message = "[ ĐÃ CHUYỂN TIỀN HOA HỒNG THÀNH CÔNG ]\n"
                 . "━━━━━━━\n"
                 . "Mã đơn rút: #" . $payout_code . "\n"
                 . "- Đối tác: " . $kol_name . " (" . $kol_phone . ") - REF: " . $ref_code . "\n"
                 . "- Số tiền chuyển khoản: " . $amount_fmt . "\n"
                 . "- Tài khoản thụ hưởng: " . ($payout['bank_name'] ?: $kol['bank_name']) . " - STK: " . ($payout['bank_account'] ?: $kol['bank_account']) . " (" . ($payout['bank_owner'] ?: $kol['bank_owner']) . ")\n"
                 . "- Mã giao dịch ngân hàng: " . (!empty($payout['transaction_reference']) ? $payout['transaction_reference'] : 'Chuyển khoản thành công') . "\n"
                 . "- Ghi chú kế toán: " . (!empty($payout['notes']) ? $payout['notes'] : 'Thanh toán hoa hồng đối tác') . "\n"
                 . "- Thời gian xử lý: " . current_time('d/m/Y H:i:s');
    } else {
        $message = "[ ĐÃ TỪ CHỐI ĐƠN RÚT TIỀN HOA HỒNG ]\n"
                 . "━━━━━━━\n"
                 . "Mã đơn rút: #" . $payout_code . "\n"
                 . "- Đối tác: " . $kol_name . " (" . $kol_phone . ") - REF: " . $ref_code . "\n"
                 . "- Số tiền: " . $amount_fmt . "\n"
                 . "- Lý do từ chối: " . ($reason ?: 'Thông tin tài khoản thụ hưởng không chính xác') . "\n"
                 . "- Lưu ý: Số tiền này đã được tự động hoàn lại vào Ví Khả Dụng của đối tác.\n"
                 . "- Thời gian xử lý: " . current_time('d/m/Y H:i:s');
    }

    $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";

    // 1. Gửi vào nhóm Kế toán Flora
    if (!empty($group_chat_id)) {
        wp_remote_post($zalo_api_url, array(
            'method'      => 'POST',
            'timeout'     => 5,
            'blocking'    => true,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'chat_id' => $group_chat_id,
                'text'    => $message
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    // 2. Gửi thông báo kết quả làm phiếu rút tiền cho chính đối tác nếu đã liên kết Zalo
    if (!empty($kol['zalo_chat_id']) && $kol['zalo_chat_id'] !== $group_chat_id) {
        if ($action === 'completed') {
            $partner_msg = "✅ [ ĐÃ CHUYỂN TIỀN HOA HỒNG THÀNH CÔNG ] ✅\n"
                         . "━━━━━━━\n"
                         . "Chào " . $kol_name . "! Phiếu rút tiền hoa hồng của bạn đã được Flora chuyển khoản thành công:\n\n"
                         . "• Mã đơn rút: #" . $payout_code . "\n"
                         . "💰 Số tiền nhận: " . $amount_fmt . "\n"
                         . "🏦 Tài khoản thụ hưởng: " . ($payout['bank_name'] ?: $kol['bank_name']) . " - STK: " . ($payout['bank_account'] ?: $kol['bank_account']) . " (" . ($payout['bank_owner'] ?: $kol['bank_owner']) . ")\n"
                         . "📌 Mã giao dịch ngân hàng: " . (!empty($payout['transaction_reference']) ? $payout['transaction_reference'] : 'Chuyển khoản thành công') . "\n"
                         . "📝 Ghi chú: " . (!empty($payout['notes']) ? $payout['notes'] : 'Thanh toán hoa hồng đối tác') . "\n"
                         . "━━━━━━━\n"
                         . "⏰ Thời gian xử lý: " . current_time('d/m/Y H:i:s');
        } else {
            $partner_msg = "⚠️ [ THÔNG BÁO VỀ ĐƠN RÚT TIỀN HOA HỒNG ] ⚠️\n"
                         . "━━━━━━━\n"
                         . "Chào " . $kol_name . "! Đơn rút tiền #" . $payout_code . " của bạn cần điều chỉnh:\n\n"
                         . "• Số tiền yêu cầu: " . $amount_fmt . "\n"
                         . "❌ Lý do từ chối: " . ($reason ?: 'Thông tin tài khoản thụ hưởng chưa chính xác') . "\n\n"
                         . "💡 Toàn bộ số tiền trên đã được tự động hoàn lại vào Ví Khả Dụng của bạn trên Cổng Đối Tác. Quý đối tác vui lòng kiểm tra lại STK ngân hàng và tạo lại lệnh rút mới.\n"
                         . "━━━━━━━\n"
                         . "⏰ Thời gian xử lý: " . current_time('d/m/Y H:i:s');
        }

        wp_remote_post($zalo_api_url, array(
            'method'      => 'POST',
            'timeout'     => 5,
            'blocking'    => false,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode(array(
                'chat_id' => $kol['zalo_chat_id'],
                'text'    => $partner_msg
            ), JSON_UNESCAPED_UNICODE)
        ));
    }

    return true;
}

/**
 * Gửi Email xác nhận đơn yêu cầu rút tiền cho Đối tác
 */
function flora_send_affiliate_withdrawal_email($payout, $kol) {
    if (empty($kol['email']) || !is_email($kol['email'])) {
        return false;
    }

    $kol_name   = esc_html($kol['name']);
    $amount_fmt = number_format($payout['amount'], 0, ',', '.') . ' VNĐ';
    $payout_code= esc_html($payout['payout_code']);
    $portal_url = home_url('/doi-tac/?token=' . $kol['secret_token']);
    $time_req   = current_time('d/m/Y H:i:s');

    $subject = "💸 [Flora Clinic] Đã tiếp nhận yêu cầu rút tiền {$amount_fmt} (Mã: {$payout_code})";

    $content = '
        <h3 style="color: #0033a3; margin-top: 0; font-size: 18px;">Kính gửi Quý đối tác ' . $kol_name . ',</h3>
        <p style="font-size: 14px; line-height: 1.6; color: #334155;">
            Hệ thống Nha Khoa Flora trân trọng xác nhận đã tiếp nhận thành công đơn yêu cầu rút tiền hoa hồng của bạn. Bộ phận Kế toán Flora đang tiến hành đối soát và chuẩn bị lệnh chuyển khoản.
        </p>

        <div style="background: #f0fdf4; border: 1.5px dashed #16a34a; border-radius: 12px; padding: 18px; margin: 20px 0; text-align: center;">
            <div style="font-size: 13px; color: #166534; font-weight: 700; text-transform: uppercase;">Số tiền yêu cầu rút</div>
            <div style="font-size: 26px; color: #15803d; font-weight: 900; margin: 4px 0;">' . $amount_fmt . '</div>
            <div style="font-size: 13px; color: #475569;">Mã giao dịch: <strong>' . $payout_code . '</strong></div>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 14px; margin-bottom: 20px;">
            <tr><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #64748b; width: 40%;">Ngân hàng thụ hưởng:</td><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; font-weight: 700;">' . esc_html($payout['bank_name']) . '</td></tr>
            <tr><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Số tài khoản:</td><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #0033a3;">' . esc_html($payout['bank_account']) . '</td></tr>
            <tr><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Tên chủ tài khoản:</td><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; font-weight: 700;">' . esc_html($payout['bank_owner']) . '</td></tr>
            <tr><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Thời gian gửi yêu cầu:</td><td style="padding: 10px; border-bottom: 1px solid #f1f5f9;">' . $time_req . '</td></tr>
            <tr><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #64748b;">Thời gian xử lý dự kiến:</td><td style="padding: 10px; border-bottom: 1px solid #f1f5f9; color: #15803d; font-weight: 700;">Trong vòng 24 - 48 giờ làm việc</td></tr>
        </table>

        <div style="text-align: center; margin: 25px 0;">
            <a href="' . esc_url($portal_url) . '" style="background: #0033a3; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: 700; display: inline-block;">
                👉 XEM TRẠNG THÁI TRÊN CỔNG ĐỐI TÁC
            </a>
        </div>
    ';

    if (function_exists('flora_send_branded_html_mail')) {
        return flora_send_branded_html_mail($kol['email'], $subject, $content);
    } else {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        return @wp_mail($kol['email'], $subject, $content, $headers);
    }
}

/**
 * AJAX XỬ LÝ GỬI ĐƠN YÊU CẦU RÚT TIỀN TỪ CỔNG ĐỐI TÁC
 */
add_action('wp_ajax_flora_ajax_partner_request_withdrawal', 'flora_ajax_partner_request_withdrawal');
add_action('wp_ajax_nopriv_flora_ajax_partner_request_withdrawal', 'flora_ajax_partner_request_withdrawal');
function flora_ajax_partner_request_withdrawal() {
    $token  = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
    $notes  = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';

    if (empty($token)) {
        wp_send_json_error(array('message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.'));
    }

    $kol = flora_affiliate_get_by_token($token);
    if (!$kol || $kol['status'] !== 'active') {
        wp_send_json_error(array('message' => 'Tài khoản đối tác không tồn tại hoặc chưa được kích hoạt.'));
    }

    // Kiểm tra tài khoản ngân hàng thụ hưởng
    if (empty($kol['bank_name']) || empty($kol['bank_account']) || empty($kol['bank_owner'])) {
        wp_send_json_error(array('message' => 'Vui lòng cập nhật đầy đủ Tên Ngân hàng, Số tài khoản (STK) và Tên chủ tài khoản trong mục Thông Tin Cá Nhân trước khi gửi yêu cầu rút tiền!'));
    }

    // Tính toán số dư khả dụng
    $stats = flora_affiliate_get_financial_stats($kol['id']);
    $available = $stats['available_balance'];

    if ($amount < 100000) {
        wp_send_json_error(array('message' => 'Số tiền yêu cầu rút tối thiểu là 100.000 VNĐ.'));
    }

    if ($amount > $available) {
        wp_send_json_error(array('message' => 'Số tiền rút (' . number_format($amount, 0, ',', '.') . 'đ) vượt quá số dư xác thực khả dụng hiện tại (' . number_format($available, 0, ',', '.') . 'đ)!'));
    }

    global $wpdb;
    $payout_table = flora_get_affiliate_payouts_table_name();

    // Sinh mã đơn rút tiền
    $payout_code = 'WD' . date('ymd') . '-' . rand(1000, 9999);

    $inserted = $wpdb->insert($payout_table, array(
        'payout_code'           => $payout_code,
        'affiliate_id'          => $kol['id'],
        'amount'                => $amount,
        'status'                => 'requested',
        'payment_method'        => 'bank_transfer',
        'bank_name'             => $kol['bank_name'],
        'bank_account'          => $kol['bank_account'],
        'bank_owner'            => $kol['bank_owner'],
        'transaction_reference' => '',
        'notes'                 => $notes,
        'requested_at'          => current_time('mysql'),
        'created_by'            => 0,
        'created_at'            => current_time('mysql')
    ), array('%s', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'));

    if ($inserted === false) {
        wp_send_json_error(array('message' => 'Lỗi kết nối cơ sở dữ liệu khi tạo yêu cầu. Vui lòng liên hệ Hotline 028 7305 8999!'));
    }

    $payout_data = array(
        'id'           => $wpdb->insert_id,
        'payout_code'  => $payout_code,
        'amount'       => $amount,
        'bank_name'    => $kol['bank_name'],
        'bank_account' => $kol['bank_account'],
        'bank_owner'   => $kol['bank_owner'],
        'notes'        => $notes,
        'requested_at' => current_time('mysql')
    );

    // 1. BẮN NOTI VÀO GROUP ZALO ĐỂ KẾ TOÁN CHECK (KO CẦN NÚT DUYỆT BOT)
    flora_send_zalo_withdrawal_notification($payout_data, $kol, $stats);

    // 2. Gửi Email thông báo tiếp nhận đơn rút cho đối tác
    flora_send_affiliate_withdrawal_email($payout_data, $kol);

    // Lấy lại stats mới
    $new_stats = flora_affiliate_get_financial_stats($kol['id']);

    wp_send_json_success(array(
        'message'           => '🎉 Gửi đơn yêu cầu rút tiền thành công! Kế toán Flora đã nhận được thông báo đối soát và sẽ thực hiện chuyển khoản trong vòng 24 - 48 giờ làm việc.',
        'payout_code'       => $payout_code,
        'amount'            => $amount,
        'amount_fmt'        => number_format($amount, 0, ',', '.') . ' VNĐ',
        'available_balance' => $new_stats['available_balance'],
        'available_fmt'     => number_format($new_stats['available_balance'], 0, ',', '.') . ' VNĐ',
        'requested_payout'  => $new_stats['requested_payout']
    ));
}

/**
 * AJAX XỬ LÝ LIÊN KẾT / HỦY LIÊN KẾT ZALO BOT TỪ CỔNG ĐỐI TÁC
 */
add_action('wp_ajax_flora_ajax_partner_update_zalo', 'flora_ajax_partner_update_zalo');
add_action('wp_ajax_nopriv_flora_ajax_partner_update_zalo', 'flora_ajax_partner_update_zalo');
function flora_ajax_partner_update_zalo() {
    $token   = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    $chat_id = isset($_POST['chat_id']) ? sanitize_text_field(trim($_POST['chat_id'])) : '';

    if (empty($token)) {
        wp_send_json_error(array('message' => 'Phiên đăng nhập hết hạn. Vui lòng đăng nhập lại.'));
    }

    $kol = flora_affiliate_get_by_token($token);
    if (!$kol || $kol['status'] !== 'active') {
        wp_send_json_error(array('message' => 'Tài khoản đối tác không tồn tại hoặc chưa kích hoạt.'));
    }

    global $wpdb;
    $table = flora_get_affiliates_table_name();

    // Cập nhật zalo_chat_id
    $wpdb->update(
        $table,
        array('zalo_chat_id' => $chat_id),
        array('id' => $kol['id']),
        array('%s'),
        array('%d')
    );

    // Nếu vừa liên kết và có chat_id, bắn tin nhắn chào mừng & xác nhận kết nối
    if (!empty($chat_id)) {
        $config = function_exists('flora_get_payment_config') ? flora_get_payment_config() : array();
        $bot_token = trim($config['zalo_bot_token'] ?? get_option('flora_zalo_bot_token', ''));

        if (!empty($bot_token)) {
            $welcome_msg = "🎉 [ LIÊN KẾT ZALO BOT THÀNH CÔNG ] 🎉\n"
                         . "━━━━━━━\n"
                         . "Chào Đối tác " . ($kol['name'] ?? 'Flora') . "!\n"
                         . "Hệ thống Nha Khoa Flora xác nhận tài khoản Zalo của bạn đã được liên kết thành công với Cổng Đối Tác:\n\n"
                         . "• Mã REF: " . ($kol['ref_code'] ?? 'Chưa rõ') . "\n"
                         . "• Số điện thoại: " . ($kol['phone'] ?? 'Chưa rõ') . "\n"
                         . "• Chat ID: " . $chat_id . "\n\n"
                         . "Từ bây giờ bạn sẽ tự động nhận thông báo tức thì khi:\n"
                         . "1. Đơn hàng từ link giới thiệu ĐƯỢC DUYỆT THÀNH CÔNG (kèm số tiền hoa hồng).\n"
                         . "2. Thông báo kết quả khi bạn gửi phiếu yêu cầu rút tiền.\n"
                         . "━━━━━━━\n"
                         . "💡 Tra cứu nhanh qua Zalo Bot bất kỳ lúc nào:\n"
                         . "• Soạn SODU : Xem số dư hoa hồng khả dụng\n"
                         . "• Soạn LINK : Lấy lại link giới thiệu & voucher\n"
                         . "• Soạn HOTRO : Kết nối chuyên viên đối tác\n"
                         . "⏰ Lúc: " . current_time('d/m/Y H:i:s');

            $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
            wp_remote_post($zalo_api_url, array(
                'method'      => 'POST',
                'timeout'     => 6,
                'blocking'    => false,
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => wp_json_encode(array(
                    'chat_id' => $chat_id,
                    'text'    => $welcome_msg
                ), JSON_UNESCAPED_UNICODE)
            ));
        }
    }

    if (empty($chat_id)) {
        wp_send_json_success(array('message' => 'Đã hủy liên kết Zalo Bot cho tài khoản đối tác.', 'zalo_chat_id' => ''));
    } else {
        wp_send_json_success(array('message' => 'Liên kết Zalo Bot thành công! Tin nhắn xác nhận đã được gửi vào Zalo của bạn.', 'zalo_chat_id' => $chat_id));
    }
}

/**
 * AJAX GỬI THÔNG BÁO TEST CHO ĐỐI TÁC QUA ZALO BOT
 */
add_action('wp_ajax_flora_ajax_partner_test_zalo', 'flora_ajax_partner_test_zalo');
add_action('wp_ajax_nopriv_flora_ajax_partner_test_zalo', 'flora_ajax_partner_test_zalo');
function flora_ajax_partner_test_zalo() {
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    if (empty($token)) {
        wp_send_json_error(array('message' => 'Phiên đăng nhập hết hạn.'));
    }

    $kol = flora_affiliate_get_by_token($token);
    if (!$kol || $kol['status'] !== 'active') {
        wp_send_json_error(array('message' => 'Tài khoản đối tác không tồn tại.'));
    }

    if (empty($kol['zalo_chat_id'])) {
        wp_send_json_error(array('message' => 'Bạn chưa liên kết tài khoản Zalo. Vui lòng làm theo hướng dẫn liên kết trước.'));
    }

    $config = function_exists('flora_get_payment_config') ? flora_get_payment_config() : array();
    $bot_token = trim($config['zalo_bot_token'] ?? get_option('flora_zalo_bot_token', ''));
    if (empty($bot_token)) {
        wp_send_json_error(array('message' => 'Hệ thống Zalo Bot chưa được cấu hình token. Vui lòng liên hệ Hotline Flora.'));
    }

    $stats = flora_affiliate_get_financial_stats($kol['id']);
    $avail_fmt = number_format($stats['available_balance'], 0, ',', '.') . ' VNĐ';

    $test_msg = "🔔 [ THỬ NGHIỆM KẾT NỐI ZALO BOT THÀNH CÔNG ] 🔔\n"
              . "━━━━━━━\n"
              . "Chào Đối tác " . $kol['name'] . "!\n"
              . "Đây là tin nhắn thử nghiệm kiểm tra đường truyền kết nối giữa Cổng Đối Tác và Zalo của bạn:\n\n"
              . "• Mã đối tác: #" . $kol['ref_code'] . "\n"
              . "• Số dư khả dụng hiện tại: " . $avail_fmt . "\n"
              . "• Trạng thái kết nối: Hoạt động hoàn hảo 🟢\n"
              . "━━━━━━━\n"
              . "✨ Khi có đơn hàng từ link của bạn ĐƯỢC DUYỆT hoặc khi có kết quả phiếu rút tiền, thông báo sẽ gửi về đây.\n"
              . "⏰ Thời gian test: " . current_time('d/m/Y H:i:s');

    $zalo_api_url = "https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage";
    $res = wp_remote_post($zalo_api_url, array(
        'method'      => 'POST',
        'timeout'     => 10,
        'blocking'    => true,
        'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
        'body'        => wp_json_encode(array(
            'chat_id' => $kol['zalo_chat_id'],
            'text'    => $test_msg
        ), JSON_UNESCAPED_UNICODE)
    ));

    if (is_wp_error($res)) {
        wp_send_json_error(array('message' => 'Không thể gửi tin nhắn qua Zalo Bot: ' . $res->get_error_message()));
    }

    $code = wp_remote_retrieve_response_code($res);
    $body = wp_remote_retrieve_body($res);
    $data = json_decode($body, true);

    if ($code >= 200 && $code < 300) {
        wp_send_json_success(array('message' => 'Đã gửi thông báo test thành công vào Zalo của bạn! Vui lòng mở Zalo kiểm tra.'));
    } else {
        $desc = !empty($data['description']) ? $data['description'] : ('Lỗi HTTP ' . $code);
        wp_send_json_error(array('message' => 'Gửi test thất bại từ Zalo Bot: ' . $desc . '. Hãy chắc chắn bạn đã bắt đầu trò chuyện với Zalo Bot trước đó.'));
    }
}

/**
 * AJAX KIỂM TRA TRẠNG THÁI LIÊN KẾT ZALO BOT (POLLING TỰ ĐỘNG 1 CHẠM)
 */
add_action('wp_ajax_flora_ajax_partner_check_zalo_status', 'flora_ajax_partner_check_zalo_status');
add_action('wp_ajax_nopriv_flora_ajax_partner_check_zalo_status', 'flora_ajax_partner_check_zalo_status');
function flora_ajax_partner_check_zalo_status() {
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    if (empty($token)) {
        wp_send_json_error(array('message' => 'Phiên đăng nhập hết hạn.'));
    }

    $kol = flora_affiliate_get_by_token($token);
    if (!$kol) {
        wp_send_json_error(array('message' => 'Tài khoản đối tác không tồn tại.'));
    }

    $is_linked = !empty($kol['zalo_chat_id']);
    wp_send_json_success(array(
        'is_linked'    => $is_linked,
        'zalo_chat_id' => $kol['zalo_chat_id'] ?? '',
        'ref_code'     => $kol['ref_code'] ?? '',
        'name'         => $kol['name'] ?? ''
    ));
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * HỆ THỐNG ĐỐI TÁC & KOL: PHÊ DUYỆT, ĐĂNG KÝ, ĐĂNG NHẬP & CẬP NHẬT HỒ SƠ
 * Lưu trữ độc lập trong wp_flora_affiliates (KHÔNG lưu vào wp_users)
 * ─────────────────────────────────────────────────────────────────────────────
 */

/**
 * Tìm kiếm đối tác bằng Số điện thoại hoặc Email hoặc Mã Ref
 */
function flora_affiliate_get_by_login($login) {
    if (empty($login)) return null;
    global $wpdb;
    $table = flora_get_affiliates_table_name();
    $clean = trim(sanitize_text_field($login));
    $clean_digits = preg_replace('/[^0-9]/', '', $clean);

    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE phone = %s OR phone = %s OR email = %s OR ref_code = %s LIMIT 1",
        $clean, $clean_digits, $clean, strtoupper($clean)
    ), ARRAY_A);
}

/**
 * Gửi Email thông báo đã tiếp nhận hồ sơ đăng ký đối tác
 */
function flora_send_affiliate_registration_received_email($kol) {
    if (empty($kol['email'])) return false;

    $subject = '📋 [Nha Khoa Flora] Đã tiếp nhận hồ sơ đăng ký Đối Tác Tiếp Thị / KOL';
    $portal_url = home_url('/doi-tac/');

    $body = '<!DOCTYPE html>
    <html lang="vi">
    <head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .email-container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .email-header { background: linear-gradient(135deg, #001f66 0%, #0033a3 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .email-header h1 { margin: 0; font-size: 21px; font-weight: 800; letter-spacing: -0.5px; }
        .email-content { padding: 32px 28px; line-height: 1.6; }
        .info-card { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 18px 20px; margin: 20px 0; }
        .tbl-creds { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        .tbl-creds td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .lbl { font-weight: 700; color: #64748b; width: 40%; }
        .val { color: #0f172a; font-weight: 600; }
        .email-footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #38bdf8; margin-bottom: 6px;">MẠNG LƯỚI ĐỐI TÁC TIẾP THỊ NHA KHOA FLORA</div>
                <h1>ĐÃ TIẾP NHẬN HỒ SƠ ĐĂNG KÝ</h1>
            </div>
            <div class="email-content">
                <p>Xin chào Quý đối tác <strong>' . esc_html($kol['name']) . '</strong>,</p>
                <p>Hệ thống Nha Khoa Flora trân trọng xác nhận đã tiếp nhận thành công hồ sơ đăng ký tham gia <strong>Mạng Lưới Đối Tác Tiếp Thị & Đại Sứ Thương Hiệu</strong> của bạn.</p>

                <div class="info-card">
                    <div style="font-weight: 800; color: #0033a3; font-size: 15px; margin-bottom: 8px;">🔑 THÔNG TIN ĐĂNG NHẬP CỔNG ĐỐI TÁC (KÍCH HOẠT KHI DUYỆT):</div>
                    <div style="font-size: 14px; color: #1e3a8a; line-height: 1.6;">
                      &bull; <strong>Cổng thông tin đối tác:</strong> <a href="' . esc_url($portal_url) . '" target="_blank" style="color: #0033a3; font-weight: 700;">' . esc_url($portal_url) . '</a><br>
                      &bull; <strong>Tên đăng nhập (Username):</strong> <strong style="color: #0033a3;">' . esc_html($kol['phone']) . '</strong> (Số điện thoại của bạn)<br>
                      &bull; <strong>Mật khẩu mặc định:</strong> <strong style="color: #0033a3; font-family: monospace; font-size: 15px;">Flora@2026</strong><br>
                      <span style="font-size: 12.5px; color: #64748b; margin-top: 6px; display: inline-block;"><em>(Sau khi tài khoản được kích hoạt, Quý đối tác có thể đăng nhập và chủ động đổi mật khẩu mới trong mục Cài đặt tài khoản)</em></span>
                    </div>
                </div>

                <p style="font-size: 14px; color: #475569; margin-bottom: 8px;"><strong>Tóm tắt thông tin đăng ký của bạn:</strong></p>
                <table class="tbl-creds">
                    <tr><td class="lbl">Họ và tên:</td><td class="val">' . esc_html($kol['name']) . '</td></tr>
                    <tr><td class="lbl">Số điện thoại đăng nhập:</td><td class="val">' . esc_html($kol['phone']) . '</td></tr>
                    <tr><td class="lbl">Email nhận thông báo:</td><td class="val">' . esc_html($kol['email']) . '</td></tr>
                    <tr><td class="lbl">Kênh truyền thông / Review:</td><td class="val">' . esc_html($kol['channel_url'] ?: 'Chưa cập nhật') . '</td></tr>
                    <tr><td class="lbl">Tài khoản nhận hoa hồng:</td><td class="val">' . esc_html($kol['bank_name'] ?: 'Chưa nhập') . ' - ' . esc_html($kol['bank_account'] ?: 'Chưa nhập') . ' (' . esc_html($kol['bank_owner'] ?: 'Chưa nhập') . ')</td></tr>
                    <tr><td class="lbl">Trạng thái hồ sơ:</td><td class="val"><span style="color: #d97706; font-weight: 700; background: #fef3c7; padding: 2px 8px; border-radius: 6px;">Đang chờ xét duyệt (Trong vòng 24 giờ làm việc)</span></td></tr>
                </table>

                <p style="font-size: 13.5px; color: #64748b; line-height: 1.5;">
                    ⏳ Ban Quản Trị Flora đang tiến hành kiểm tra hồ sơ trong vòng <strong>24 giờ làm việc</strong>. Ngay khi được phê duyệt, bạn sẽ nhận được Email thông báo chính thức kèm <strong>Mã giới thiệu (Ref Code) độc quyền</strong> và <strong>Đường link riêng</strong> để bắt đầu tiếp thị và theo dõi doanh thu hoa hồng tự động.
                </p>
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 4px;"><strong>Nha Khoa Flora - Tiêu Chuẩn Thụy Sĩ Êm Ái</strong></p>
                <p style="margin: 0;">Hotline Đối Tác 24/7: <strong>028 7305 8999</strong> | Địa chỉ: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, TP.HCM</p>
            </div>
        </div>
    </body>
    </html>';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return @wp_mail($kol['email'], $subject, $body, $headers);
}

/**
 * Gửi Email chúc mừng & kích hoạt tài khoản khi đối tác được duyệt
 */
function flora_send_affiliate_approval_email($kol) {
    if (empty($kol['email'])) return false;

    $subject = '🎉 [Nha Khoa Flora] Chúc mừng! Hồ sơ Đối Tác của bạn đã được PHÊ DUYỆT';
    $ref_url = home_url('/goi-dich-vu/?ref=' . $kol['ref_code']);
    $portal_url = home_url('/doi-tac/');
    $portal_quick_url = home_url('/doi-tac/?token=' . $kol['secret_token']);
    $comm_rate = $kol['commission_rate'] . ($kol['commission_type'] === 'fixed' ? 'đ/đơn' : '%');

    $body = '<!DOCTYPE html>
    <html lang="vi">
    <head>
    <meta charset="UTF-8">
    <style>
        body { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .email-container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        .email-header { background: linear-gradient(135deg, #001f66 0%, #0033a3 100%); padding: 32px 24px; text-align: center; color: #ffffff; }
        .email-header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }
        .email-content { padding: 32px 28px; line-height: 1.6; }
        .ref-box { background: #eff6ff; border: 2px dashed #0033a3; border-radius: 14px; padding: 20px; text-align: center; margin: 24px 0; }
        .ref-code { font-size: 26px; font-weight: 800; color: #0033a3; letter-spacing: 2px; font-family: monospace; }
        .btn-cta { display: block; width: fit-content; margin: 24px auto; background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); color: #ffffff !important; padding: 14px 34px; border-radius: 9999px; text-decoration: none; font-weight: 800; font-size: 15px; text-align: center; box-shadow: 0 4px 15px rgba(0,51,163,0.25); }
        .tbl-creds { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 14px; }
        .tbl-creds td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
        .lbl { font-weight: 700; color: #64748b; width: 38%; }
        .val { color: #0f172a; font-weight: 600; }
        .notice-card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; margin: 18px 0; font-size: 13.5px; color: #166534; line-height: 1.5; }
        .email-footer { background: #f8fafc; padding: 20px; text-align: center; font-size: 13px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <div style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #38bdf8; margin-bottom: 6px;">MẠNG LƯỚI ĐỐI TÁC TIẾP THỊ FLORA CLINIC</div>
                <h1>HỒ SƠ ĐỐI TÁC ĐÃ ĐƯỢC PHÊ DUYỆT!</h1>
            </div>
            <div class="email-content">
                <p>Xin chào Quý đối tác <strong>' . esc_html($kol['name']) . '</strong>,</p>
                <p>Ban Giám Đốc Nha Khoa Flora trân trọng chúc mừng và thông báo: Hồ sơ đăng ký của bạn đã được <strong>PHÊ DUYỆT CHÍNH THỨC</strong>!</p>
                
                <div class="ref-box">
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 4px; font-weight: 700;">MÃ GIỚI THIỆU ĐỘC QUYỀN (REF CODE):</div>
                    <div class="ref-code">' . esc_html($kol['ref_code']) . '</div>
                    <div style="font-size: 14px; color: #0284c7; margin-top: 8px; font-weight: 700;">Tỷ lệ hoa hồng: ' . esc_html($comm_rate) . ' / đơn thành công</div>
                </div>

                <p style="font-size: 14px; color: #475569; margin-bottom: 8px;"><strong>Thông tin tài khoản đăng nhập Cổng Quản Lý Đối Tác:</strong></p>
                <table class="tbl-creds">
                    <tr><td class="lbl">Cổng thông tin đối tác:</td><td class="val"><a href="' . esc_url($portal_url) . '" target="_blank" style="color: #0033a3; font-weight: 700;">' . esc_url($portal_url) . '</a></td></tr>
                    <tr><td class="lbl">Tên đăng nhập (Username):</td><td class="val"><strong style="color: #0033a3;">' . esc_html($kol['phone']) . '</strong> (Số điện thoại của bạn)</td></tr>
                    <tr><td class="lbl">Mật khẩu mặc định:</td><td class="val"><strong style="color: #0033a3; font-family: monospace; font-size: 16px;">Flora@2026</strong></td></tr>
                    <tr><td class="lbl">Mã giới thiệu (Ref):</td><td class="val"><strong style="color: #0033a3;">' . esc_html($kol['ref_code']) . '</strong></td></tr>
                    <tr><td class="lbl">Link giới thiệu khách:</td><td class="val"><a href="' . esc_url($ref_url) . '" target="_blank" style="color: #0033a3; word-break: break-all;">' . esc_url($ref_url) . '</a></td></tr>
                    <tr><td class="lbl">Tài khoản thụ hưởng:</td><td class="val">' . esc_html($kol['bank_name'] ?: 'Chưa cập nhật') . ' - STK: ' . esc_html($kol['bank_account'] ?: 'Chưa cập nhật') . ' (' . esc_html($kol['bank_owner']) . ')</td></tr>
                </table>

                <div class="notice-card">
                    💡 <strong>HƯỚNG DẪN ĐỔI MẬT KHẨU:</strong><br>
                    Quý đối tác vui lòng đăng nhập vào Cổng Đối Tác bằng Số điện thoại <strong>' . esc_html($kol['phone']) . '</strong> và mật khẩu mặc định <strong>Flora@2026</strong>. Sau đó vào mục <strong>Thông tin tài khoản</strong> để chủ động đổi sang mật khẩu cá nhân mới nhằm bảo mật tài khoản.
                </div>

                <a href="' . esc_url($portal_quick_url) . '" class="btn-cta" target="_blank">
                    👉 TRUY CẬP CỔNG ĐỐI TÁC NGAY
                </a>
            </div>
            <div class="email-footer">
                <p style="margin: 0 0 4px;"><strong>Nha Khoa Flora - Tiêu Chuẩn Thụy Sĩ Êm Ái</strong></p>
                <p style="margin: 0;">Hotline Hỗ Trợ Đối Tác: <strong>028 7305 8999</strong> | Website: nhakhoaflora.com</p>
            </div>
        </div>
    </body>
    </html>';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    return @wp_mail($kol['email'], $subject, $body, $headers);
}

/**
 * Hàm Phê Duyệt Đối Tác (Admin hoặc Tự Động / Zalo Bot)
 */
function flora_approve_affiliate($id_or_phone, $admin_id = 0, $operator_name = '') {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $kol = null;
    if (is_numeric($id_or_phone) && (int)$id_or_phone > 0) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d LIMIT 1", (int)$id_or_phone), ARRAY_A);
    }
    if (!$kol) {
        $clean_phone = preg_replace('/[^0-9]/', '', (string)$id_or_phone);
        if (!empty($clean_phone)) {
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE phone = %s LIMIT 1", $clean_phone), ARRAY_A);
        }
    }
    if (!$kol && !empty($id_or_phone)) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ref_code = %s OR email = %s LIMIT 1", sanitize_text_field($id_or_phone), sanitize_email($id_or_phone)), ARRAY_A);
    }

    if (!$kol) {
        return new WP_Error('not_found', 'Không tìm thấy hồ sơ đối tác với thông tin: ' . $id_or_phone);
    }

    // 1. Kiểm tra nếu ĐỐI TÁC ĐÃ ĐƯỢC DUYỆT TRƯỚC ĐÓ RỒI
    if ($kol['status'] === 'active') {
        $approved_time_fmt = !empty($kol['approved_at']) ? date('d/m/Y H:i:s', strtotime($kol['approved_at'])) : 'trước đó';
        $ref_url = home_url('/goi-dich-vu?ref=' . $kol['ref_code']);
        return array(
            'success'          => false,
            'already_approved' => true,
            'approved_time'    => $approved_time_fmt,
            'ref_code'         => $kol['ref_code'],
            'ref_url'          => $ref_url,
            'message'          => 'Đối tác "' . $kol['name'] . '" ĐÃ ĐƯỢC DUYỆT TRƯỚC ĐÓ lúc ' . $approved_time_fmt . ' với Mã REF: ' . $kol['ref_code'] . '. Không cần duyệt lại!',
            'kol'              => $kol
        );
    }

    // Nếu chưa có ref_code hợp lệ thì sinh
    $ref_code = trim($kol['ref_code']);
    if (empty($ref_code) || strpos($ref_code, 'PENDING_') === 0) {
        $ref_code = flora_affiliate_generate_ref_code($kol['name']);
    }

    // Nếu chưa có secret token thì sinh
    $secret_token = trim($kol['secret_token']);
    if (empty($secret_token)) {
        $secret_token = flora_affiliate_generate_secret_token();
    }

    $now = current_time('mysql');
    $log_op = !empty($operator_name) ? $operator_name : ($admin_id > 0 ? 'Admin ID ' . $admin_id : 'Ban Quản Trị');
    $log_note = ' | Duyệt kích hoạt bởi ' . esc_html($log_op) . ' lúc ' . current_time('d/m/Y H:i:s');
    $new_notes = trim(($kol['notes'] ?? '') . $log_note);

    $wpdb->update(
        $table,
        array(
            'status'       => 'active',
            'ref_code'     => $ref_code,
            'secret_token' => $secret_token,
            'approved_at'  => $now,
            'approved_by'  => $admin_id,
            'notes'        => $new_notes
        ),
        array('id' => $kol['id']),
        array('%s', '%s', '%s', '%s', '%d', '%s'),
        array('%d')
    );

    $kol['ref_code']     = $ref_code;
    $kol['secret_token'] = $secret_token;
    $kol['status']       = 'active';
    $kol['approved_at']  = $now;
    $kol['notes']        = $new_notes;

    // 1. Gửi Email thông báo duyệt & cấp Link REF + Portal
    flora_send_affiliate_approval_email($kol);

    // 2. Gửi thông báo Zalo Bot Group theo style chuẩn (nếu không phải thao tác trực tiếp từ Zalo)
    $admin_user = $admin_id > 0 ? get_userdata($admin_id) : null;
    $admin_name = !empty($operator_name) ? $operator_name : ($admin_user ? $admin_user->display_name : 'Ban Quản Trị Flora');
    $ref_url    = home_url('/goi-dich-vu?ref=' . $ref_code);
    $comm_rate  = $kol['commission_rate'] . ($kol['commission_type'] === 'fixed' ? 'đ' : '%');

    if (strpos($admin_name, 'Zalo') === false && function_exists('flora_send_zalo_lead_notification')) {
        $zalo_msg = "🔔 [ ĐỐI TÁC ĐÃ ĐƯỢC DUYỆT ] 🔔\n"
                  . "━━━━━━\n"
                  . "Dự án: Tiếp Thị Liên Kết - Nha Khoa Flora\n\n"
                  . "👤 Thông Tin Đối Tác:\n"
                  . "  ▸ Họ tên: {$kol['name']}\n"
                  . "  ▸ Số ĐT: {$kol['phone']}\n"
                  . "  ▸ Email: {$kol['email']}\n"
                  . "  ▸ Mã REF: {$ref_code}\n"
                  . "  ▸ Mức hoa hồng: {$comm_rate}\n"
                  . "  └─ Link REF: {$ref_url}\n"
                  . "  └─ Trạng thái: Đã kích hoạt hoạt động\n\n"
                  . "━━━━━━\n"
                  . "  └─ Người duyệt: {$admin_name}\n"
                  . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');

        $config = flora_get_payment_config();
        $bot_token     = trim($config['zalo_bot_token'] ?? '');
        $group_chat_id = trim($config['zalo_group_chat_id'] ?? '');
        if (!empty($bot_token) && !empty($group_chat_id)) {
            wp_remote_post("https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage", array(
                'method'      => 'POST',
                'timeout'     => 10,
                'blocking'    => false,
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => wp_json_encode(array('chat_id' => $group_chat_id, 'text' => $zalo_msg), JSON_UNESCAPED_UNICODE)
            ));
        }
    }

    return array(
        'success'  => true,
        'message'  => 'Đã phê duyệt đối tác "' . $kol['name'] . '" thành công!',
        'ref_code' => $ref_code,
        'ref_url'  => $ref_url,
        'kol'      => $kol
    );
}

/**
 * Hàm Từ Chối Đối Tác (Admin hoặc Zalo Bot)
 */
function flora_reject_affiliate($id_or_phone, $reason = '', $operator_name = '') {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $kol = null;
    if (is_numeric($id_or_phone) && (int)$id_or_phone > 0) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d LIMIT 1", (int)$id_or_phone), ARRAY_A);
    }
    if (!$kol) {
        $clean_phone = preg_replace('/[^0-9]/', '', (string)$id_or_phone);
        if (!empty($clean_phone)) {
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE phone = %s LIMIT 1", $clean_phone), ARRAY_A);
        }
    }
    if (!$kol && !empty($id_or_phone)) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE ref_code = %s OR email = %s LIMIT 1", sanitize_text_field($id_or_phone), sanitize_email($id_or_phone)), ARRAY_A);
    }

    if (!$kol) {
        return new WP_Error('not_found', 'Không tìm thấy hồ sơ đối tác với thông tin: ' . $id_or_phone);
    }

    // 1. Kiểm tra nếu ĐỐI TÁC ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ RỒI
    if ($kol['status'] === 'inactive') {
        return array(
            'success'          => false,
            'already_rejected' => true,
            'message'          => 'Đối tác "' . $kol['name'] . '" ĐÃ Ở TRẠNG THÁI TỪ CHỐI / TẠM DỪNG TRƯỚC ĐÓ. Không cần thao tác lại!',
            'kol'              => $kol
        );
    }

    $log_op = !empty($operator_name) ? $operator_name : 'Ban Quản Trị Flora';
    $log_reason = !empty($reason) ? $reason : 'Không phù hợp tiêu chí hợp tác hiện tại';
    $log_note = ' | Từ chối bởi ' . esc_html($log_op) . ' lúc ' . current_time('d/m/Y H:i:s') . ' (Lý do: ' . esc_html($log_reason) . ')';
    $new_notes = trim(($kol['notes'] ?? '') . $log_note);

    $wpdb->update(
        $table,
        array(
            'status' => 'inactive',
            'notes'  => $new_notes
        ),
        array('id' => $kol['id']),
        array('%s', '%s'),
        array('%d')
    );

    $kol['status'] = 'inactive';
    $kol['notes']  = $new_notes;

    return array(
        'success' => true,
        'message' => 'Đã chuyển trạng thái đối tác "' . $kol['name'] . '" sang từ chối / tạm ngưng.',
        'kol'     => $kol
    );
}

/**
 * AJAX 1: Đăng ký đối tác công khai (Front-end form)
 */
add_action('wp_ajax_flora_ajax_partner_register', 'flora_ajax_partner_register');
add_action('wp_ajax_nopriv_flora_ajax_partner_register', 'flora_ajax_partner_register');
function flora_ajax_partner_register() {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $name        = sanitize_text_field($_POST['name'] ?? '');
    $phone       = sanitize_text_field($_POST['phone'] ?? '');
    $email       = sanitize_email($_POST['email'] ?? '');
    $password    = sanitize_text_field($_POST['password'] ?? '');
    $channel_url = sanitize_text_field($_POST['channel_url'] ?? '');
    $bank_name   = sanitize_text_field($_POST['bank_name'] ?? '');
    $bank_acc    = sanitize_text_field($_POST['bank_account'] ?? '');
    $bank_own    = strtoupper(sanitize_text_field($_POST['bank_owner'] ?? ''));
    $notes       = sanitize_textarea_field($_POST['notes'] ?? '');

    if (empty($name) || empty($phone) || empty($email)) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Email!'));
    }

    // Kiểm tra định dạng SĐT
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean_phone) < 9 || strlen($clean_phone) > 12) {
        wp_send_json_error(array('message' => 'Số điện thoại không hợp lệ, vui lòng kiểm tra lại.'));
    }

    // Kiểm tra trùng SĐT hoặc Email trong bảng wp_flora_affiliates
    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE phone = %s OR email = %s LIMIT 1",
        $clean_phone, $email
    ), ARRAY_A);

    if ($existing) {
        if ($existing['status'] === 'pending') {
            wp_send_json_error(array('message' => 'Hồ sơ với Số điện thoại hoặc Email này đang trong quá trình xét duyệt. Ban Quản Trị sẽ thông báo qua email sớm nhất!'));
        } elseif ($existing['status'] === 'active') {
            wp_send_json_error(array('message' => 'Số điện thoại hoặc Email này đã là Đối tác của Flora! Vui lòng chuyển sang tab Đăng Nhập để vào cổng.'));
        } else {
            wp_send_json_error(array('message' => 'Tài khoản này đang trong trạng thái tạm khóa. Vui lòng liên hệ Hotline: 028 7305 8999 để được hỗ trợ.'));
        }
    }

    // Sinh candidate ref_code và secret_token
    $ref_candidate = flora_affiliate_generate_ref_code($name);
    $secret_token  = flora_affiliate_generate_secret_token();
    
    // Mật khẩu mặc định là Flora@2026 nếu không nhập
    $default_pass = 'Flora@2026';
    $password_to_hash = !empty($password) ? $password : $default_pass;
    $password_hash = wp_hash_password($password_to_hash);

    $inserted = $wpdb->insert(
        $table,
        array(
            'name'            => $name,
            'phone'           => $clean_phone,
            'email'           => $email,
            'channel_url'     => $channel_url,
            'ref_code'        => $ref_candidate,
            'secret_token'    => $secret_token,
            'password_hash'   => $password_hash,
            'commission_type' => 'percent',
            'commission_rate' => 10.00,
            'bank_name'       => $bank_name,
            'bank_account'    => $bank_acc,
            'bank_owner'      => $bank_own,
            'status'          => 'pending',
            'notes'           => $notes,
            'created_at'      => current_time('mysql')
        ),
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if (!$inserted) {
        wp_send_json_error(array('message' => 'Không thể lưu hồ sơ, vui lòng thử lại sau!'));
    }

    $aff_id = $wpdb->insert_id;
    $new_kol = array(
        'id'           => $aff_id,
        'name'         => $name,
        'phone'        => $clean_phone,
        'email'        => $email,
        'channel_url'  => $channel_url,
        'ref_code'     => $ref_candidate,
        'secret_token' => $secret_token,
        'bank_name'    => $bank_name,
        'bank_account' => $bank_acc,
        'bank_owner'   => $bank_own
    );

    // 1. Gửi email tiếp nhận hồ sơ cho đối tác
    flora_send_affiliate_registration_received_email($new_kol);

    // 2. Bắn thông báo Zalo vào nhóm Bot
    $zalo_msg = "🔔 [ CÓ ĐĂNG KÝ ĐỐI TÁC MỚI ] 🔔\n"
              . "━━━━━━\n"
              . "Dự án: Đối Tác Tiếp Thị Nha Khoa Flora\n\n"
              . "👤 Thông Tin Đối Tác:\n"
              . "  ▸ Họ tên: {$name}\n"
              . "  ▸ Số ĐT: {$clean_phone}\n"
              . "  ▸ Email: {$email}\n"
              . "  ▸ Kênh: " . ($channel_url ?: 'Chưa cập nhật') . "\n"
              . "  ▸ Ngân hàng: " . ($bank_name ?: 'Chưa nhập') . " - STK: " . ($bank_acc ?: 'Chưa nhập') . " (" . ($bank_own ?: 'Chưa nhập') . ")\n"
              . "  └─ Trạng thái: Chờ duyệt hồ sơ\n\n"
              . "━━━━━━\n"
              . "  └─ Nguồn: Form Đăng Ký Đối Tác\n"
              . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');

    if (function_exists('flora_send_zalo_lead_notification')) {
        $config = flora_get_payment_config();
        $bot_token     = trim($config['zalo_bot_token'] ?? '');
        $group_chat_id = trim($config['zalo_group_chat_id'] ?? '');
        if (!empty($bot_token) && !empty($group_chat_id)) {
            wp_remote_post("https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage", array(
                'method'      => 'POST',
                'timeout'     => 10,
                'blocking'    => false,
                'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'        => wp_json_encode(array('chat_id' => $group_chat_id, 'text' => $zalo_msg), JSON_UNESCAPED_UNICODE)
            ));
        }
    }

    wp_send_json_success(array(
        'message' => 'Đăng ký thành công! Hồ sơ của bạn đã được chuyển tới Ban Quản Trị để duyệt. Thông tin kích hoạt sẽ được gửi tới email của bạn trong vòng 24h.'
    ));
}

/**
 * AJAX 2: Đăng nhập Cổng thông tin đối tác
 */
add_action('wp_ajax_flora_ajax_partner_login', 'flora_ajax_partner_login');
add_action('wp_ajax_nopriv_flora_ajax_partner_login', 'flora_ajax_partner_login');
function flora_ajax_partner_login() {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $login_id = sanitize_text_field($_POST['login_id'] ?? '');
    $password = sanitize_text_field($_POST['password'] ?? '');
    $token    = sanitize_text_field($_POST['token'] ?? '');

    if (empty($token) && (empty($login_id) || empty($password))) {
        wp_send_json_error(array('message' => 'Vui lòng nhập Số điện thoại/Email và Mật khẩu!'));
    }

    $kol = null;
    if (!empty($token)) {
        $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE secret_token = %s LIMIT 1", $token), ARRAY_A);
    } else {
        $kol = flora_affiliate_get_by_login($login_id);
    }

    if (!$kol) {
        wp_send_json_error(array('message' => 'Thông tin đăng nhập không chính xác hoặc tài khoản không tồn tại.'));
    }

    // Nếu đăng nhập bằng mật khẩu, kiểm tra password_hash
    if (empty($token)) {
        $valid_pass = false;
        if (!empty($kol['password_hash'])) {
            $valid_pass = wp_check_password($password, $kol['password_hash']);
        }
        // Cho phép mật khẩu mặc định là Flora@2026
        if (!$valid_pass && $password === 'Flora@2026') {
            $valid_pass = true;
        }
        // Cho phép fallback nếu nhập đúng secret_token làm mật khẩu
        if (!$valid_pass && $password === $kol['secret_token']) {
            $valid_pass = true;
        }

        if (!$valid_pass) {
            wp_send_json_error(array('message' => 'Mật khẩu không chính xác. Quý đối tác vui lòng kiểm tra email kích hoạt hoặc liên hệ Hotline: 028 7305 8999!'));
        }
    }

    // Kiểm tra trạng thái
    if ($kol['status'] === 'pending') {
        wp_send_json_error(array('message' => 'Hồ sơ của bạn đang được Ban Quản Trị xem xét duyệt trong 24h. Vui lòng kiểm tra email hoặc liên hệ hotline: 028 7305 8999!'));
    }
    if ($kol['status'] === 'inactive') {
        wp_send_json_error(array('message' => 'Tài khoản đối tác đang tạm ngưng hoạt động. Vui lòng liên hệ hỗ trợ.'));
    }

    // Lưu cookie xác thực đối tác trong 30 ngày
    $max_age = 30 * 24 * 60 * 60;
    setcookie('flora_partner_token', $kol['secret_token'], time() + $max_age, '/', '', is_ssl(), false);

    wp_send_json_success(array(
        'message'      => 'Đăng nhập thành công!',
        'token'        => $kol['secret_token'],
        'name'         => $kol['name'],
        'ref_code'     => $kol['ref_code'],
        'redirect_url' => home_url('/doi-tac/?token=' . $kol['secret_token'])
    ));
}

/**
 * AJAX 3: Đăng xuất Cổng thông tin đối tác
 */
add_action('wp_ajax_flora_ajax_partner_logout', 'flora_ajax_partner_logout');
add_action('wp_ajax_nopriv_flora_ajax_partner_logout', 'flora_ajax_partner_logout');
function flora_ajax_partner_logout() {
    setcookie('flora_partner_token', '', time() - 3600, '/', '', is_ssl(), false);
    wp_send_json_success(array('redirect_url' => home_url('/doi-tac/')));
}

/**
 * AJAX 4: Đối tác chủ động đổi STK, Tên ngân hàng, Email, Mật khẩu
 */
add_action('wp_ajax_flora_ajax_partner_update_profile', 'flora_ajax_partner_update_profile');
add_action('wp_ajax_nopriv_flora_ajax_partner_update_profile', 'flora_ajax_partner_update_profile');
function flora_ajax_partner_update_profile() {
    global $wpdb;
    $table = flora_get_affiliates_table_name();

    $token        = sanitize_text_field($_POST['token'] ?? '');
    $bank_name    = sanitize_text_field($_POST['bank_name'] ?? '');
    $bank_account = sanitize_text_field($_POST['bank_account'] ?? '');
    $bank_owner   = strtoupper(sanitize_text_field($_POST['bank_owner'] ?? ''));
    $email        = sanitize_email($_POST['email'] ?? '');
    $cur_password = sanitize_text_field($_POST['current_password'] ?? '');
    $new_password = sanitize_text_field($_POST['new_password'] ?? '');

    if (empty($token)) {
        wp_send_json_error(array('message' => 'Phiên làm việc hết hạn, vui lòng đăng nhập lại!'));
    }

    $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE secret_token = %s LIMIT 1", $token), ARRAY_A);
    if (!$kol) {
        wp_send_json_error(array('message' => 'Không tìm thấy tài khoản đối tác hợp lệ.'));
    }

    // Nếu có đổi mật khẩu mới, kiểm tra mật khẩu hiện tại
    $update_data   = array();
    $update_format = array();

    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            wp_send_json_error(array('message' => 'Mật khẩu mới phải có tối thiểu 6 ký tự!'));
        }
        $cur_valid = false;
        if (!empty($kol['password_hash']) && wp_check_password($cur_password, $kol['password_hash'])) {
            $cur_valid = true;
        } elseif ($cur_password === 'Flora@2026') {
            $cur_valid = true;
        } elseif ($cur_password === $kol['secret_token']) {
            $cur_valid = true;
        }

        if (!$cur_valid) {
            wp_send_json_error(array('message' => 'Mật khẩu hiện tại không chính xác! Quý đối tác vui lòng kiểm tra lại.'));
        }
        $update_data['password_hash'] = wp_hash_password($new_password);
        $update_format[] = '%s';
    }

    // Cập nhật thông tin ngân hàng & email
    $old_bank_acc = $kol['bank_account'];
    $bank_changed = (!empty($bank_account) && $bank_account !== $old_bank_acc);

    $update_data['bank_name']    = $bank_name;
    $update_data['bank_account'] = $bank_account;
    $update_data['bank_owner']   = $bank_owner;
    $update_format[] = '%s';
    $update_format[] = '%s';
    $update_format[] = '%s';

    if (!empty($email) && is_email($email)) {
        // Kiểm tra xem email có bị trùng với KOL khác không
        $exist_email = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s AND id != %d", $email, $kol['id']));
        if ($exist_email > 0) {
            wp_send_json_error(array('message' => 'Email này đã được sử dụng bởi một đối tác khác.'));
        }
        $update_data['email'] = $email;
        $update_format[] = '%s';
    }

    $wpdb->update($table, $update_data, array('id' => $kol['id']), $update_format, array('%d'));

    // Nếu đối tác đổi STK, bắn Zalo cảnh báo bảo mật tài chính
    if ($bank_changed) {
        $zalo_alert = "🔔 [ ĐỐI TÁC CẬP NHẬT TÀI KHOẢN NGÂN HÀNG ] 🔔\n"
                    . "━━━━━━\n"
                    . "Dự án: Đối Tác Tiếp Thị Nha Khoa Flora\n\n"
                    . "👤 Thông Tin Đối Tác:\n"
                    . "  ▸ Họ tên: {$kol['name']}\n"
                    . "  ▸ Mã REF: {$kol['ref_code']}\n"
                    . "  ▸ Ngân hàng mới: {$bank_name}\n"
                    . "  ▸ STK mới: {$bank_account}\n"
                    . "  ▸ Chủ TK: {$bank_owner}\n"
                    . "  └─ STK cũ: " . ($old_bank_acc ?: 'Trống') . "\n\n"
                    . "━━━━━━\n"
                    . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');

        if (function_exists('flora_send_zalo_lead_notification')) {
            $config = flora_get_payment_config();
            $bot_token     = trim($config['zalo_bot_token'] ?? '');
            $group_chat_id = trim($config['zalo_group_chat_id'] ?? '');
            if (!empty($bot_token) && !empty($group_chat_id)) {
                wp_remote_post("https://bot-api.zaloplatforms.com/bot" . $bot_token . "/sendMessage", array(
                    'method'      => 'POST',
                    'timeout'     => 10,
                    'blocking'    => false,
                    'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
                    'body'        => wp_json_encode(array('chat_id' => $group_chat_id, 'text' => $zalo_alert), JSON_UNESCAPED_UNICODE)
                ));
            }
        }
    }

    wp_send_json_success(array('message' => 'Cập nhật thông tin đối tác & tài khoản ngân hàng thành công!'));
}

/**
 * AJAX 5: Admin Phê Duyệt Đối Tác (Kèm cấp link REF & gửi mail kích hoạt)
 */
add_action('wp_ajax_flora_ajax_approve_affiliate', 'flora_ajax_approve_affiliate');
function flora_ajax_approve_affiliate() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền thực hiện.'));
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        wp_send_json_error(array('message' => 'ID đối tác không hợp lệ.'));
    }

    $result = flora_approve_affiliate($id, get_current_user_id());
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    if (is_array($result) && !empty($result['already_approved'])) {
        wp_send_json_error(array('message' => $result['message']));
    }

    wp_send_json_success(array('message' => 'Đã phê duyệt đối tác thành công! Hệ thống đã tạo link REF và gửi email thông báo kích hoạt.'));
}

/**
 * AJAX 6: Admin Từ Chối Đối Tác
 */
add_action('wp_ajax_flora_ajax_reject_affiliate', 'flora_ajax_reject_affiliate');
function flora_ajax_reject_affiliate() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền thực hiện.'));
    }

    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $reason = sanitize_text_field($_POST['reason'] ?? '');

    $result = flora_reject_affiliate($id, $reason);
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    if (is_array($result) && !empty($result['already_rejected'])) {
        wp_send_json_error(array('message' => $result['message']));
    }
    wp_send_json_success(array('message' => 'Đã chuyển trạng thái đối tác sang từ chối.'));
}

/**
 * GIAO DIỆN CHÍNH WP-ADMIN: AFFILIATE DASHBOARD
 */
function flora_render_affiliates_admin_page() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $aff_table    = flora_get_affiliates_table_name();
    $orders_table = $wpdb->prefix . 'flora_orders';

    // Thống kê toàn diện tối ưu hiệu suất (Chỉ 2 câu truy vấn tổng thay vì 10 câu quét bảng liên tục)
    $aff_stats = $wpdb->get_row("
        SELECT 
            COUNT(*) as total_kols,
            COALESCE(SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END), 0) as active_kols,
            COALESCE(SUM(total_clicks), 0) as total_clicks,
            COALESCE(SUM(paid_commission), 0) as paid_commission
        FROM $aff_table
    ", ARRAY_A);

    $total_kols      = (int)($aff_stats['total_kols'] ?? 0);
    $active_kols     = (int)($aff_stats['active_kols'] ?? 0);
    $total_clicks    = (int)($aff_stats['total_clicks'] ?? 0);
    $paid_commission = (int)($aff_stats['paid_commission'] ?? 0);

    $ord_stats = $wpdb->get_row("
        SELECT 
            COALESCE(SUM(CASE WHEN affiliate_id > 0 THEN 1 ELSE 0 END), 0) as total_ref_orders,
            COALESCE(SUM(CASE WHEN affiliate_id > 0 AND payment_status = 'paid' THEN 1 ELSE 0 END), 0) as paid_ref_orders,
            COALESCE(SUM(CASE WHEN affiliate_id > 0 AND payment_status = 'paid' THEN final_amount ELSE 0 END), 0) as total_ref_revenue,
            COALESCE(SUM(CASE WHEN affiliate_id > 0 AND payment_status = 'paid' THEN commission_amount ELSE 0 END), 0) as total_commission,
            COALESCE(SUM(CASE WHEN (affiliate_id = 0 OR affiliate_id IS NULL) AND payment_status = 'paid' THEN 1 ELSE 0 END), 0) as orphan_orders_count,
            COALESCE(SUM(CASE WHEN (affiliate_id = 0 OR affiliate_id IS NULL) AND payment_status = 'paid' THEN final_amount ELSE 0 END), 0) as orphan_revenue
        FROM $orders_table
    ", ARRAY_A);

    $total_ref_orders    = (int)($ord_stats['total_ref_orders'] ?? 0);
    $paid_ref_orders     = (int)($ord_stats['paid_ref_orders'] ?? 0);
    $total_ref_revenue   = (int)($ord_stats['total_ref_revenue'] ?? 0);
    $total_commission    = (int)($ord_stats['total_commission'] ?? 0);
    $orphan_orders_count = (int)($ord_stats['orphan_orders_count'] ?? 0);
    $orphan_revenue      = (int)($ord_stats['orphan_revenue'] ?? 0);
    $pending_payout      = max(0, $total_commission - $paid_commission);

    // Danh sách KOLs & Bộ lọc trạng thái
    $search        = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    $status_filter = isset($_GET['status_filter']) ? sanitize_text_field($_GET['status_filter']) : '';
    $where  = "1=1";
    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(" AND (name LIKE %s OR phone LIKE %s OR email LIKE %s OR ref_code LIKE %s)", $like, $like, $like, $like);
    }
    if (!empty($status_filter) && in_array($status_filter, array('pending', 'active', 'inactive'))) {
        $where .= $wpdb->prepare(" AND status = %s", $status_filter);
    }

    $all_cnt      = (int)$wpdb->get_var("SELECT COUNT(*) FROM $aff_table");
    $pending_cnt  = (int)$wpdb->get_var("SELECT COUNT(*) FROM $aff_table WHERE status = 'pending'");
    $active_cnt   = (int)$wpdb->get_var("SELECT COUNT(*) FROM $aff_table WHERE status = 'active'");
    $inactive_cnt = (int)$wpdb->get_var("SELECT COUNT(*) FROM $aff_table WHERE status = 'inactive'");

    $kols = $wpdb->get_results("SELECT * FROM $aff_table WHERE $where ORDER BY id DESC", ARRAY_A);
    $admin_nonce = wp_create_nonce('flora_affiliate_admin_nonce');
    $export_all_url = wp_nonce_url(admin_url('admin.php?page=flora-affiliates&action=export_kol_csv&kol_id=0'), 'flora_export_kol_nonce');
    ?>

    <style>
        .flora-aff-wrap { 
            margin: 0; 
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; 
            color: #0f172a; 
        }
        .flora-aff-header { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            flex-wrap: wrap; 
            gap: 16px; 
            margin-bottom: 24px; 
            padding: 24px 28px; 
            background: #ffffff; 
            border-radius: 14px; 
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04); 
            border: 1px solid #e2e8f0; 
        }
        .flora-aff-header h1 { 
            font-size: 1.45rem; 
            font-weight: 800; 
            color: #0f172a; 
            margin: 0; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            letter-spacing: -0.3px;
        }
        .flora-aff-header h1 .dashicons {
            color: #0033a3;
        }
        
        .flora-kpi-row { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr) !important; 
            gap: 16px; 
            margin-bottom: 24px; 
        }
        @media screen and (max-width: 1024px) {
            .flora-kpi-row {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media screen and (max-width: 640px) {
            .flora-kpi-row {
                grid-template-columns: 1fr !important;
            }
        }
        .flora-kpi-box { 
            background: #ffffff; 
            padding: 20px 22px; 
            border-radius: 12px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); 
            display: flex; 
            align-items: center; 
            gap: 16px; 
            transition: all 0.2s ease;
        }
        .flora-kpi-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
            border-color: #cbd5e1;
        }
        /* Scoped Icon & Button Alignment */
        .flora-aff-wrap .dashicons,
        .flora-modal .dashicons {
            font-family: dashicons !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            vertical-align: middle !important;
            line-height: 1 !important;
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
        }

        .flora-kpi-ico { 
            width: 44px; 
            height: 44px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0; 
            background: #f1f5f9;
            color: #334155;
        }
        .flora-kpi-ico .dashicons {
            font-size: 22px !important;
            width: 22px !important;
            height: 22px !important;
        }
        .flora-kpi-val { 
            font-size: 1.55rem; 
            font-weight: 800; 
            line-height: 1.15; 
            margin-bottom: 4px; 
            color: #0f172a;
            letter-spacing: -0.5px;
        }
        .flora-kpi-lbl { 
            font-size: 0.76rem; 
            color: #64748b; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
        }

        .tbl-aff-card { 
            background: #ffffff; 
            border-radius: 12px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); 
            overflow: hidden; 
        }
        .tbl-aff { 
            width: 100%; 
            border-collapse: collapse; 
            text-align: left; 
            font-size: 0.88rem; 
        }
        .tbl-aff th { 
            background: #f8fafc; 
            color: #475569; 
            font-weight: 700; 
            padding: 14px 18px; 
            border-bottom: 1px solid #e2e8f0; 
            font-size: 0.78rem; 
            text-transform: uppercase; 
            letter-spacing: 0.5px; 
            white-space: nowrap; 
        }
        .tbl-aff td { 
            padding: 14px 18px; 
            border-bottom: 1px solid #f1f5f9; 
            vertical-align: middle; 
            color: #334155;
        }
        .tbl-aff tr:hover td { 
            background: #fbfcfe; 
        }

        .ref-pill { 
            background: #eff6ff; 
            border: 1px solid #bfdbfe; 
            color: #0033a3; 
            padding: 3px 8px; 
            border-radius: 6px; 
            font-weight: 700; 
            font-family: monospace; 
            font-size: 0.88rem; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            line-height: 1.2;
            vertical-align: middle;
        }
        .btn-copy-ref { 
            cursor: pointer; 
            color: #3b82f6; 
            border: none; 
            background: transparent; 
            padding: 0; 
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            line-height: 1;
            transition: color 0.15s ease;
        }
        .btn-copy-ref:hover { 
            color: #1d4ed8; 
        }
        .btn-copy-ref .dashicons {
            font-size: 15px !important;
            width: 15px !important;
            height: 15px !important;
            line-height: 1 !important;
            margin: 0 !important;
        }
        
        .badge-status-on { 
            background: #f0fdf4; 
            color: #166534; 
            border: 1px solid #bbf7d0; 
            padding: 3px 10px; 
            border-radius: 9999px; 
            font-size: 0.72rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .badge-status-pending {
            background: #fffbeb; 
            color: #92400e; 
            border: 1px solid #fde68a; 
            padding: 3px 10px; 
            border-radius: 9999px; 
            font-size: 0.72rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
        }
        .badge-status-off { 
            background: #f8fafc; 
            color: #64748b; 
            border: 1px solid #cbd5e1; 
            padding: 3px 10px; 
            border-radius: 9999px; 
            font-size: 0.72rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        .btn-flora-primary { 
            background: #0033a3 !important; 
            border: 1px solid #0033a3 !important; 
            color: #ffffff !important; 
            font-weight: 600; 
            border-radius: 8px; 
            height: 38px; 
            padding: 0 18px; 
            cursor: pointer; 
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important;
            gap: 6px !important; 
            line-height: 1 !important;
            vertical-align: middle !important;
            text-decoration: none; 
            box-shadow: 0 2px 4px rgba(0, 51, 163, 0.12);
            transition: all 0.15s ease;
        }
        .btn-flora-primary:hover { 
            background: #002277 !important; 
            border-color: #002277 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 51, 163, 0.2);
            color: #ffffff !important;
        }
        .btn-flora-sec { 
            background: #ffffff !important; 
            border: 1px solid #cbd5e1 !important; 
            color: #334155 !important; 
            font-weight: 600; 
            border-radius: 8px; 
            height: 38px; 
            padding: 0 16px; 
            cursor: pointer; 
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important;
            gap: 6px !important; 
            line-height: 1 !important;
            vertical-align: middle !important;
            text-decoration: none; 
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.15s ease;
        }
        .btn-flora-sec:hover { 
            background: #f8fafc !important; 
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }
        .btn-flora-primary .dashicons,
        .btn-flora-sec .dashicons {
            font-size: 18px !important;
            width: 18px !important;
            height: 18px !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        /* Nút hành động trong bảng (Chi Tiết, Sửa, Xóa) */
        .btn-action-detail {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            height: 32px !important;
            line-height: 1 !important;
            padding: 0 14px !important;
            border-radius: 6px !important;
            background: #0033a3 !important;
            border: 1px solid #0033a3 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 12.5px !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(0, 51, 163, 0.15) !important;
            transition: all 0.15s ease !important;
            text-decoration: none !important;
        }
        .btn-action-detail:hover {
            background: #002277 !important;
            border-color: #002277 !important;
            color: #ffffff !important;
        }
        .btn-action-detail .dashicons {
            font-size: 16px !important;
            width: 16px !important;
            height: 16px !important;
            line-height: 1 !important;
            margin: 0 !important;
        }
        .btn-action-icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px !important;
            height: 32px !important;
            padding: 0 !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            background: #ffffff !important;
            color: #475569 !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
            transition: all 0.15s ease !important;
            text-decoration: none !important;
        }
        .btn-action-icon:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0033a3 !important;
        }
        .btn-action-icon .dashicons {
            font-size: 16px !important;
            width: 16px !important;
            height: 16px !important;
            line-height: 1 !important;
            margin: 0 !important;
        }
        .btn-action-del {
            color: #dc2626 !important;
            border-color: #fecaca !important;
        }
        .btn-action-del:hover {
            background: #fef2f2 !important;
            border-color: #f87171 !important;
            color: #b91c1c !important;
        }

        /* Modal Styles */
        .flora-modal { 
            display: none; 
            position: fixed; 
            z-index: 100000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background: rgba(15, 23, 42, 0.65); 
            backdrop-filter: blur(4px); 
            align-items: center; 
            justify-content: center; 
        }
        .flora-modal-content { 
            background: #ffffff; 
            border-radius: 16px; 
            width: 90%; 
            max-width: 680px; 
            max-height: 90vh; 
            overflow-y: auto; 
            padding: 30px; 
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15); 
            position: relative; 
            border: 1px solid #e2e8f0;
        }
        .flora-modal-drawer { 
            max-width: 1200px; 
            width: 95vw; 
            box-sizing: border-box; 
        }
        .flora-modal-close { 
            position: absolute; 
            right: 18px; 
            top: 18px; 
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px; 
            line-height: 1;
            cursor: pointer; 
            color: #64748b; 
            transition: all 0.15s ease;
            z-index: 100;
        }
        .flora-modal-close:hover { 
            background: #e2e8f0;
            color: #0f172a; 
        }
        .form-row { margin-bottom: 16px; }
        .form-row label { display: block; font-weight: 700; font-size: 0.88rem; color: #334155; margin-bottom: 6px; }
        .form-row input, .form-row select, .form-row textarea { 
            width: 100%; 
            padding: 9px 12px; 
            border-radius: 8px; 
            border: 1px solid #cbd5e1; 
            font-size: 0.9rem; 
            box-sizing: border-box; 
        }
        .form-row input:focus, .form-row select:focus, .form-row textarea:focus {
            border-color: #0033a3;
            box-shadow: 0 0 0 3px rgba(0, 51, 163, 0.1);
            outline: none;
        }
        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    </style>

    <div class="flora-aff-wrap">
        <?php if (isset($_GET['deleted'])): ?>
            <div class="notice notice-success is-dismissible"><p>Đã xóa KOL thành công!</p></div>
        <?php endif; ?>

        <!-- HEADER -->
        <div class="flora-aff-header">
            <div>
                <h1><i class="dashicons dashicons-networking" style="font-size: 1.8rem; width: auto; height: auto;"></i> HỆ THỐNG QUẢN LÝ AFFILIATE & KOL</h1>
                <p style="margin: 6px 0 0; color: #64748b; font-size: 0.88rem;">Tạo mã REF định danh, theo dõi lượt click, doanh thu, hoa hồng tự động và xuất dữ liệu đối soát.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="<?php echo esc_url($export_all_url); ?>" class="btn-flora-sec">
                    <i class="dashicons dashicons-download"></i> Xuất Đối Soát Toàn Bộ
                </a>
                <button type="button" class="btn-flora-primary" onclick="openAddKolModal()">
                    <i class="dashicons dashicons-plus-alt2"></i> Thêm Mới KOL
                </button>
            </div>
        </div>

        <!-- 6 KPI CARDS (BẮT BUỘC 3 CARD 1 HÀNG - 2 HÀNG TỔNG 6 CARD) -->
        <div class="flora-kpi-row" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-groups"></i></div>
                <div>
                    <div class="flora-kpi-val"><?php echo $active_kols; ?> <span style="font-size: 0.9rem; font-weight: 500; color: #94a3b8;">/ <?php echo $total_kols; ?></span></div>
                    <div class="flora-kpi-lbl">KOL Hoạt Động</div>
                </div>
            </div>
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-visibility"></i></div>
                <div>
                    <div class="flora-kpi-val"><?php echo number_format($total_clicks); ?></div>
                    <div class="flora-kpi-lbl">Lượt Truy Cập Ref</div>
                </div>
            </div>
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-cart"></i></div>
                <div>
                    <div class="flora-kpi-val"><?php echo $paid_ref_orders; ?> <span style="font-size: 0.9rem; font-weight: 500; color: #94a3b8;">/ <?php echo $total_ref_orders; ?></span></div>
                    <div class="flora-kpi-lbl">Đơn KOL Thành Công</div>
                </div>
            </div>
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-money-alt"></i></div>
                <div>
                    <div class="flora-kpi-val" style="color: #0033a3;"><?php echo number_format($total_ref_revenue, 0, ',', '.'); ?>đ</div>
                    <div class="flora-kpi-lbl">Doanh Thu Từ KOL</div>
                </div>
            </div>
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-chart-pie"></i></div>
                <div>
                    <div class="flora-kpi-val" style="color: #dc2626;"><?php echo number_format($pending_payout, 0, ',', '.'); ?>đ</div>
                    <div class="flora-kpi-lbl">Hoa Hồng Cần Chi Trả</div>
                </div>
            </div>
            <div class="flora-kpi-box">
                <div class="flora-kpi-ico"><i class="dashicons dashicons-store"></i></div>
                <div>
                    <div class="flora-kpi-val"><?php echo number_format($orphan_revenue, 0, ',', '.'); ?>đ</div>
                    <div class="flora-kpi-lbl">Đơn Trực Tiếp Flora (<?php echo $orphan_orders_count; ?>)</div>
                </div>
            </div>
        </div>

        <!-- STATUS TABS -->
        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates')); ?>" class="button <?php echo empty($status_filter) ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Tất Cả (<?php echo $all_cnt; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates&status_filter=pending')); ?>" class="button <?php echo ($status_filter === 'pending') ? 'button-primary' : ''; ?>" style="font-weight: 700; <?php echo ($pending_cnt > 0) ? 'background: #fff7ed; border-color: #fdba74; color: #c2410c;' : ''; ?>">
                <i class="dashicons dashicons-clock" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></i> Chờ Duyệt (<?php echo $pending_cnt; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates&status_filter=active')); ?>" class="button <?php echo ($status_filter === 'active') ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Đang Hoạt Động (<?php echo $active_cnt; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates&status_filter=inactive')); ?>" class="button <?php echo ($status_filter === 'inactive') ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Tạm Dừng (<?php echo $inactive_cnt; ?>)
            </a>

            <?php 
            $payout_tbl_quick = flora_get_affiliate_payouts_table_name();
            $pending_po_cnt = (int)$wpdb->get_var("SELECT COUNT(*) FROM $payout_tbl_quick WHERE status = 'requested'");
            ?>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliate-payouts')); ?>" class="button" style="font-weight: 700; background: #f0fdf4; border-color: #86efac; color: #166534; margin-left: auto;">
                <i class="dashicons dashicons-money-alt" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></i> Quản Lý Đơn Rút Tiền <?php echo ($pending_po_cnt > 0) ? "<span style='background:#d97706;color:#ffffff;border-radius:10px;padding:2px 7px;font-size:11px;margin-left:4px;'>$pending_po_cnt</span>" : ''; ?>
            </a>
        </div>

        <!-- SEARCH -->
        <div style="background: #fff; padding: 14px 20px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <form method="get" action="<?php echo esc_url(admin_url('admin.php')); ?>" style="display: flex; gap: 8px; width: 100%; max-width: 450px; align-items: center;">
                <input type="hidden" name="page" value="flora-affiliates">
                <?php if (!empty($status_filter)): ?>
                    <input type="hidden" name="status_filter" value="<?php echo esc_attr($status_filter); ?>">
                <?php endif; ?>
                <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Tìm kiếm theo Tên, SĐT, Email hoặc Mã REF..." style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1; height: 38px; box-sizing: border-box;">
                <button type="submit" class="btn-flora-sec" style="padding: 0 16px; height: 38px; white-space: nowrap;"><i class="dashicons dashicons-search"></i> Tìm</button>
                <?php if (!empty($search) || !empty($status_filter)): ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates')); ?>" class="button" style="height: 38px; display: inline-flex; align-items: center;">Bỏ lọc</a>
                <?php endif; ?>
            </form>
            <div style="color: #64748b; font-size: 0.85rem;">
                Hiển thị <strong><?php echo count($kols); ?></strong> đối tác KOL
            </div>
        </div>

        <!-- TABLE OF KOLS (THOÁNG ĐÃNG, ĐƠN GIẢN CỘT, CHI TIẾT MỞ MODAL) -->
        <div class="tbl-aff-card" style="overflow-x: auto; -webkit-overflow-scrolling: touch; width: 100%;">
            <table class="tbl-aff" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">KOL / Đối Tác</th>
                        <th style="min-width: 160px; white-space: nowrap;">Mã REF Giới Thiệu</th>
                        <th style="min-width: 130px; white-space: nowrap;">Mức Hoa Hồng</th>
                        <th style="min-width: 180px; white-space: nowrap;">Doanh Thu & Đơn</th>
                        <th style="min-width: 170px; white-space: nowrap;">Hoa Hồng Tích Lũy</th>
                        <th style="min-width: 110px; white-space: nowrap;">Trạng Thái</th>
                        <th style="min-width: 190px; text-align: right; white-space: nowrap;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kols)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 36px; color: #94a3b8;">Không tìm thấy đối tác nào phù hợp. Bấm "Thêm Mới KOL" để bắt đầu!</td></tr>
                    <?php else: ?>
                        <?php foreach ($kols as $k): 
                            $ref_url = home_url('/goi-dich-vu?ref=' . $k['ref_code']);
                            $portal_url = home_url('/affiliate-portal/?token=' . $k['secret_token']);
                            $comm_text = ($k['commission_type'] === 'fixed') ? number_format($k['commission_rate'], 0, ',', '.') . 'đ/đơn' : $k['commission_rate'] . '%';
                            $del_url = wp_nonce_url(admin_url('admin.php?page=flora-affiliates&action=delete_kol&id=' . $k['id']), 'flora_delete_kol_action');
                        ?>
                        <tr>
                            <td>
                                <strong style="color: #0033a3; font-size: 0.95rem;"><?php echo esc_html($k['name']); ?></strong><br>
                                <span style="color: #64748b; font-size: 0.82rem;"><?php echo esc_html($k['phone']); ?> | <?php echo esc_html($k['email']); ?></span>
                                <?php if (!empty($k['channel_url'])): ?>
                                    <div style="font-size: 0.78rem; color: #0493f1; margin-top: 2px;"><i class="dashicons dashicons-admin-links" style="font-size: 13px; width: 13px; height: 13px;"></i> <?php echo esc_html($k['channel_url']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php if ($k['status'] === 'pending'): ?>
                                    <span style="color: #94a3b8; font-style: italic; font-size: 0.85rem;">(Sẽ cấp khi duyệt)</span>
                                <?php else: ?>
                                    <span class="ref-pill">
                                        <?php echo esc_html($k['ref_code']); ?>
                                        <button type="button" class="btn-copy-ref" title="Sao chép Link REF" onclick="copyToClipboard('<?php echo esc_url($ref_url); ?>')">
                                            <i class="dashicons dashicons-admin-page"></i>
                                        </button>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;"><strong style="color: #d97706;"><?php echo $comm_text; ?></strong></td>
                            <td>
                                <div style="font-weight: 700; color: #0033a3; font-size: 0.95rem;"><?php echo number_format($k['total_revenue'], 0, ',', '.'); ?>đ</div>
                                <div style="font-size: 0.78rem; color: #64748b; margin-top: 2px;">
                                    <span style="color: #16a34a; font-weight: 600;"><?php echo $k['paid_orders']; ?> đơn paid</span> | <?php echo number_format($k['total_clicks']); ?> clicks
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #16a34a;"><?php echo number_format($k['total_commission'], 0, ',', '.'); ?>đ</div>
                                <div style="font-size: 0.75rem; color: #dc2626;">Còn nợ: <?php echo number_format(max(0, $k['total_commission'] - $k['paid_commission']), 0, ',', '.'); ?>đ</div>
                            </td>
                            <td style="white-space: nowrap;">
                                <?php if ($k['status'] === 'pending'): ?>
                                    <span style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; padding: 4px 10px; border-radius: 9999px; font-weight: 800; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="dashicons dashicons-clock" style="font-size: 14px; width: 14px; height: 14px; line-height: 1;"></i> Chờ duyệt
                                    </span>
                                <?php elseif ($k['status'] === 'active'): ?>
                                    <span class="badge-status-on">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge-status-off">Tạm dừng</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <div style="display: inline-flex; align-items: center; gap: 6px; justify-content: flex-end;">
                                    <?php if ($k['status'] === 'pending'): ?>
                                        <button type="button" class="button button-primary" onclick="approveAffiliate(<?php echo $k['id']; ?>, '<?php echo esc_js($k['name']); ?>')" style="background: #16a34a; border-color: #15803d; font-size: 0.8rem; font-weight: 700; height: 32px; display: inline-flex; align-items: center; gap: 4px;" title="Duyệt đối tác, cấp mã REF và gửi email kích hoạt">
                                            <i class="dashicons dashicons-yes-alt" style="font-size: 16px; width: 16px; height: 16px; line-height: 1;"></i> Duyệt Ngay
                                        </button>
                                        <button type="button" class="button" onclick="rejectAffiliate(<?php echo $k['id']; ?>, '<?php echo esc_js($k['name']); ?>')" style="color: #dc2626; font-size: 0.8rem; height: 32px; display: inline-flex; align-items: center; gap: 4px;" title="Từ chối hồ sơ đối tác">
                                            <i class="dashicons dashicons-dismiss" style="font-size: 16px; width: 16px; height: 16px; line-height: 1;"></i> Từ chối
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-action-detail" onclick="viewKolDetail(<?php echo $k['id']; ?>)" title="Xem chi tiết đơn, khách hàng, link portal và quyết toán">
                                            <i class="dashicons dashicons-visibility"></i> Chi Tiết
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" class="btn-action-icon" onclick='editKolData(<?php echo json_encode($k); ?>)' title="Chỉnh sửa">
                                        <i class="dashicons dashicons-edit"></i>
                                    </button>
                                    <a href="<?php echo esc_url($del_url); ?>" class="btn-action-icon btn-action-del" onclick="return confirm('Bạn có chắc chắn muốn xóa hồ sơ này không?');" title="Xóa">
                                        <i class="dashicons dashicons-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </div>

    <!-- MODAL 1: THÊM MỚI / SỬA KOL -->
    <div id="modalKolForm" class="flora-modal">
        <div class="flora-modal-content">
            <span class="flora-modal-close" onclick="closeModal('modalKolForm')">&times;</span>
            <h2 id="modalKolTitle" style="color: #0033a3; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <i class="dashicons dashicons-id-alt" style="font-size: 22px; width: 22px; height: 22px;"></i> 
                <span>Thêm Mới KOL / Đối Tác</span>
            </h2>
            <form id="formSaveKol" onsubmit="handleSaveKol(event)">
                <input type="hidden" name="id" id="kolId" value="0">
                <input type="hidden" name="nonce" value="<?php echo $admin_nonce; ?>">

                <div class="form-grid-2">
                    <div class="form-row">
                        <label>Họ và Tên KOL (*):</label>
                        <input type="text" name="name" id="kolName" required placeholder="Vd: Nguyễn Thu Trang (Reviewer)">
                    </div>
                    <div class="form-row">
                        <label>Số Điện Thoại (*):</label>
                        <input type="text" name="phone" id="kolPhone" required placeholder="0912345678">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-row">
                        <label>Email Nhận Thông Báo Đơn (*):</label>
                        <input type="email" name="email" id="kolEmail" required placeholder="kol@gmail.com">
                    </div>
                    <div class="form-row">
                        <label>Mã REF Tùy Chỉnh (Tự sinh nếu để trống):</label>
                        <input type="text" name="ref_code" id="kolRefCode" placeholder="Vd: TRANGFLORA, BSNAM...">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-row">
                        <label>Loại Hoa Hồng:</label>
                        <select name="commission_type" id="kolCommType">
                            <option value="percent">Phần trăm (%) trên giá trị đơn</option>
                            <option value="fixed">Số tiền cố định (VNĐ/đơn)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Mức Hoa Hồng (*):</label>
                        <input type="number" name="commission_rate" id="kolCommRate" step="0.1" value="10" required placeholder="Vd: 10 hoặc 200000">
                    </div>
                </div>

                <div style="background: #f8fafc; padding: 14px; border-radius: 10px; margin-bottom: 16px; border: 1px solid #e2e8f0;">
                    <div style="font-weight: 700; color: #0033a3; margin-bottom: 10px; font-size: 0.88rem;"><i class="dashicons dashicons-bank"></i> Thông Tin Ngân Hàng Nhận Hoa Hồng</div>
                    <div class="form-grid-2">
                        <div class="form-row">
                            <label>Tên Ngân Hàng:</label>
                            <input type="text" name="bank_name" id="kolBankName" placeholder="Vd: Vietcombank, ACB, MBBank...">
                        </div>
                        <div class="form-row">
                            <label>Số Tài Khoản:</label>
                            <input type="text" name="bank_account" id="kolBankAcc" placeholder="1029384756">
                        </div>
                    </div>
                    <div class="form-row" style="margin-bottom: 0;">
                        <label>Chủ Tài Khoản (Viết hoa không dấu):</label>
                        <input type="text" name="bank_owner" id="kolBankOwner" placeholder="NGUYEN THU TRANG">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-row">
                        <label>Trạng Thái:</label>
                        <select name="status" id="kolStatus">
                            <option value="active">Đang Hoạt Động (Cho phép tính ref)</option>
                            <option value="inactive">Tạm Dừng</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>Ghi Chú Nội Bộ:</label>
                        <input type="text" name="notes" id="kolNotes" placeholder="Ghi chú hợp đồng, cam kết...">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn-flora-sec" onclick="closeModal('modalKolForm')">Hủy</button>
                    <button type="submit" class="btn-flora-primary" id="btnSubmitKol">Lưu Thông Tin KOL</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CHI TIẾT TỪNG KOL & DANH SÁCH ĐƠN HÀNG -->
    <div id="modalKolDetail" class="flora-modal">
        <div class="flora-modal-content flora-modal-drawer">
            <span class="flora-modal-close" onclick="closeModal('modalKolDetail')">&times;</span>
            <div id="kolDetailContent">
                <p style="text-align: center; padding: 40px;"><i class="dashicons dashicons-update spin"></i> Đang tải dữ liệu chi tiết...</p>
            </div>
        </div>
    </div>

    <!-- MODAL 3: GHI NHẬN QUYẾT TOÁN HOA HỒNG -->
    <div id="modalPayoutForm" class="flora-modal">
        <div class="flora-modal-content" style="max-width: 480px;">
            <span class="flora-modal-close" onclick="closeModal('modalPayoutForm')">&times;</span>
            <h3 style="color: #0033a3; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <i class="dashicons dashicons-money-alt" style="font-size: 22px; width: 22px; height: 22px;"></i> 
                <span>Ghi Nhận Thanh Toán Hoa Hồng</span>
            </h3>
            <form id="formPayout" onsubmit="handleSavePayout(event)">
                <input type="hidden" name="kol_id" id="payoutKolId" value="0">
                <input type="hidden" name="nonce" value="<?php echo $admin_nonce; ?>">

                <div class="form-row">
                    <label>Số Tiền Đã Chuyển Khoản (VNĐ) (*):</label>
                    <input type="number" name="amount" id="payoutAmount" required placeholder="Vd: 1500000">
                </div>
                <div class="form-row">
                    <label>Mã Giao Dịch Ngân Hàng:</label>
                    <input type="text" name="reference" id="payoutRef" placeholder="Vd: FT2610293847">
                </div>
                <div class="form-row">
                    <label>Ghi Chú Đối Soát:</label>
                    <textarea name="notes" id="payoutNotes" rows="2" placeholder="Vd: Thanh toán hoa hồng tháng 10/2026"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button type="button" class="btn-flora-sec" onclick="closeModal('modalPayoutForm')">Hủy</button>
                    <button type="submit" class="btn-flora-primary" id="btnSubmitPayout">Xác Nhận Đã Chuyển Tiền</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT XỬ LÝ ADMIN -->
    <script>
        const AJAX_URL = '<?php echo admin_url('admin-ajax.php'); ?>';

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã sao chép liên kết vào bộ nhớ tạm: ' + text);
            }).catch(() => {
                prompt('Sao chép liên kết:', text);
            });
        }

        function openAddKolModal() {
            document.getElementById('modalKolTitle').innerHTML = '<i class="dashicons dashicons-id-alt"></i> Thêm Mới KOL / Đối Tác';
            document.getElementById('formSaveKol').reset();
            document.getElementById('kolId').value = 0;
            document.getElementById('modalKolForm').style.display = 'flex';
        }

        function editKolData(kol) {
            document.getElementById('modalKolTitle').innerHTML = '<i class="dashicons dashicons-edit"></i> Chỉnh Sửa KOL #' + kol.id + ' - ' + kol.name;
            document.getElementById('kolId').value = kol.id;
            document.getElementById('kolName').value = kol.name;
            document.getElementById('kolPhone').value = kol.phone;
            document.getElementById('kolEmail').value = kol.email;
            document.getElementById('kolRefCode').value = kol.ref_code;
            document.getElementById('kolCommType').value = kol.commission_type;
            document.getElementById('kolCommRate').value = kol.commission_rate;
            document.getElementById('kolBankName').value = kol.bank_name || '';
            document.getElementById('kolBankAcc').value = kol.bank_account || '';
            document.getElementById('kolBankOwner').value = kol.bank_owner || '';
            document.getElementById('kolStatus').value = kol.status;
            document.getElementById('kolNotes').value = kol.notes || '';
            document.getElementById('modalKolForm').style.display = 'flex';
        }

        async function handleSaveKol(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitKol');
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const form = document.getElementById('formSaveKol');
            const formData = new FormData(form);
            formData.append('action', 'flora_save_affiliate');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể lưu KOL'));
                    btn.disabled = false;
                    btn.innerText = 'Lưu Thông Tin KOL';
                }
            } catch (err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerText = 'Lưu Thông Tin KOL';
            }
        }

        async function viewKolDetail(kolId) {
            document.getElementById('modalKolDetail').style.display = 'flex';
            const container = document.getElementById('kolDetailContent');
            container.innerHTML = '<p style="text-align: center; padding: 40px;"><i class="dashicons dashicons-update spin"></i> Đang tải dữ liệu chi tiết...</p>';

            const formData = new FormData();
            formData.append('action', 'flora_get_affiliate_detail');
            formData.append('id', kolId);
            formData.append('nonce', '<?php echo $admin_nonce; ?>');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    renderKolDetailHtml(data.data);
                } else {
                    container.innerHTML = '<p style="color: #dc2626; text-align: center;">' + data.data.message + '</p>';
                }
            } catch (err) {
                container.innerHTML = '<p style="color: #dc2626; text-align: center;">Lỗi tải dữ liệu!</p>';
            }
        }

        function renderKolDetailHtml(info) {
            const kol = info.kol;
            const orders = info.orders || [];
            let ordersHtml = '';

            if (orders.length === 0) {
                ordersHtml = '<tr><td colspan="7" style="text-align: center; padding: 24px; color: #94a3b8;">Chưa có khách hàng nào đặt đơn từ mã REF này.</td></tr>';
            } else {
                orders.forEach(o => {
                    const isPaid = (o.payment_status === 'paid');
                    const badge = isPaid 
                        ? '<span class="badge-status-on">ĐÃ THANH TOÁN</span>' 
                        : '<span class="badge-status-pending">CHỜ THANH TOÁN</span>';
                    const commFmt = parseInt(o.commission_amount).toLocaleString('vi-VN') + 'đ';
                    const amountFmt = parseInt(o.final_amount).toLocaleString('vi-VN') + 'đ';
                    
                    ordersHtml += `
                        <tr>
                            <td style="white-space: nowrap;"><strong style="color: #0033a3; font-family: monospace; font-size: 0.88rem;">${o.order_code}</strong></td>
                            <td style="white-space: nowrap;"><strong>${o.customer_name}</strong><br><span style="color: #64748b; font-size: 0.8rem;">${o.customer_phone}</span></td>
                            <td style="min-width: 220px; line-height: 1.4;">${o.package_name}</td>
                            <td style="white-space: nowrap;"><strong style="color: #0f172a; font-family: monospace; font-size: 0.9rem;">${amountFmt}</strong></td>
                            <td style="white-space: nowrap; text-align: center;">${badge}</td>
                            <td style="white-space: nowrap;"><strong style="color: #16a34a; font-family: monospace; font-size: 0.9rem;">+${commFmt}</strong></td>
                            <td style="white-space: nowrap; color: #64748b; font-size: 0.82rem;">${o.created_at}</td>
                        </tr>
                    `;
                });
            }

            const html = `
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 16px; padding-right: 48px;">
                    <div>
                        <h2 style="color: #0033a3; margin: 0 0 6px; display: flex; align-items: center; gap: 8px;">
                            <i class="dashicons dashicons-businessman" style="font-size: 24px; width: 24px; height: 24px;"></i> 
                            <span>${kol.name}</span> 
                            <span class="ref-pill">${kol.ref_code}</span>
                        </h2>
                        <div style="color: #64748b; font-size: 0.88rem;">SĐT: <strong>${kol.phone}</strong> | Email: <strong>${kol.email}</strong></div>
                        <div style="margin-top: 4px; font-size: 0.85rem; color: #334155;">
                            Ngân hàng: <strong>${kol.bank_name || 'Chưa cập nhật'}</strong> - STK: <strong>${kol.bank_account || 'Chưa cập nhật'}</strong> (${kol.bank_owner || ''})
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <a href="${info.export_url}" class="btn-flora-sec" style="height: 36px; padding: 0 14px; font-size: 13px;">
                            <i class="dashicons dashicons-download"></i> Xuất Excel KOL
                        </a>
                        <button type="button" class="btn-flora-primary" onclick="openPayoutModal(${kol.id}, ${info.pending_payout})" style="height: 36px; padding: 0 16px; font-size: 13px;">
                            <i class="dashicons dashicons-money-alt"></i> Quyết Toán
                        </button>
                    </div>
                </div>

                <!-- 4 METRIC CARDS (4 CARD 1 HÀNG) -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 24px;">
                    <div style="background: #ffffff; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Lượt Click Ref</div>
                        <div style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin-top: 4px;">${kol.total_clicks}</div>
                    </div>
                    <div style="background: #ffffff; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Đơn Thành Công</div>
                        <div style="font-size: 1.45rem; font-weight: 800; color: #0f172a; margin-top: 4px;">${kol.paid_orders} <span style="font-size: 0.85rem; font-weight: 500; color: #94a3b8;">/ ${kol.total_orders}</span></div>
                    </div>
                    <div style="background: #ffffff; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Tổng Doanh Thu</div>
                        <div style="font-size: 1.45rem; font-weight: 800; color: #0033a3; margin-top: 4px;">${parseInt(kol.total_revenue).toLocaleString('vi-VN')}đ</div>
                    </div>
                    <div style="background: #ffffff; padding: 16px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Chưa Quyết Toán</div>
                        <div style="font-size: 1.45rem; font-weight: 800; color: #dc2626; margin-top: 4px;">${parseInt(info.pending_payout).toLocaleString('vi-VN')}đ</div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="margin: 0; color: #0033a3; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <i class="dashicons dashicons-list-view" style="font-size: 20px; width: 20px; height: 20px;"></i> 
                        <span>Danh Sách Khách Hàng & Đơn Hàng (${orders.length})</span>
                    </h3>
                    <div style="font-size: 0.82rem; color: #64748b;">(Chỉ tính hoa hồng thực nhận khi đơn đã thanh toán)</div>
                </div>

                <div style="border: 1px solid #e2e8f0; border-radius: 10px; overflow-x: auto; background: #fff;">
                    <table class="tbl-aff" style="width: 100%; min-width: 980px;">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">Mã Đơn</th>
                                <th style="white-space: nowrap;">Khách Hàng</th>
                                <th>Gói Dịch Vụ</th>
                                <th style="white-space: nowrap;">Số Tiền</th>
                                <th style="white-space: nowrap; text-align: center;">Thanh Toán</th>
                                <th style="white-space: nowrap;">Hoa Hồng</th>
                                <th style="white-space: nowrap;">Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>${ordersHtml}</tbody>
                    </table>
                </div>
            `;

            document.getElementById('kolDetailContent').innerHTML = html;
        }

        function openPayoutModal(kolId, suggestedAmount) {
            document.getElementById('payoutKolId').value = kolId;
            document.getElementById('payoutAmount').value = suggestedAmount > 0 ? suggestedAmount : '';
            document.getElementById('formPayout').reset();
            document.getElementById('payoutKolId').value = kolId;
            document.getElementById('payoutAmount').value = suggestedAmount > 0 ? suggestedAmount : '';
            document.getElementById('modalPayoutForm').style.display = 'flex';
        }

        async function handleSavePayout(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitPayout');
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const form = document.getElementById('formPayout');
            const formData = new FormData(form);
            formData.append('action', 'flora_save_affiliate_payout');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message);
                    closeModal('modalPayoutForm');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể lưu quyết toán'));
                    btn.disabled = false;
                    btn.innerText = 'Xác Nhận Chi Trả';
                }
            } catch (err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerText = 'Xác Nhận Chi Trả';
            }
        }

        async function approveAffiliate(id, name) {
            if (!confirm('Bạn có chắc chắn muốn PHÊ DUYỆT đối tác "' + name + '"?\n\nHệ thống sẽ tự động kích hoạt tài khoản, cấp Mã Link REF độc quyền và gửi Email thông báo kích hoạt tới đối tác.')) {
                return;
            }
            const formData = new FormData();
            formData.append('action', 'flora_ajax_approve_affiliate');
            formData.append('id', id);
            formData.append('nonce', '<?php echo $admin_nonce; ?>');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message || 'Phê duyệt đối tác thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể phê duyệt'));
                }
            } catch(e) {
                alert('Lỗi kết nối máy chủ!');
            }
        }

        async function rejectAffiliate(id, name) {
            const reason = prompt('Nhập lý do từ chối đối tác "' + name + '" (tùy chọn):', '');
            if (reason === null) return;

            const formData = new FormData();
            formData.append('action', 'flora_ajax_reject_affiliate');
            formData.append('id', id);
            formData.append('reason', reason);
            formData.append('nonce', '<?php echo $admin_nonce; ?>');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message || 'Đã chuyển trạng thái đối tác sang từ chối.');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể từ chối'));
                }
            } catch(e) {
                alert('Lỗi kết nối máy chủ!');
            }
        }
    </script>
    <?php
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 7. TRANG CÀI ĐẶT CỔNG SEAPAY TRONG WP-ADMIN
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_render_seapay_settings_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['flora_save_seapay_settings'])) {
        check_admin_referer('flora_seapay_settings_nonce');
        update_option('flora_seapay_api_secret', sanitize_text_field($_POST['flora_seapay_api_secret'] ?? ''));
        update_option('flora_seapay_auto_confirm', isset($_POST['flora_seapay_auto_confirm']) ? 1 : 0);
        echo '<div class="notice notice-success is-dismissible"><p>Đã lưu cấu hình SeaPay thành công!</p></div>';
    }

    $secret   = get_option('flora_seapay_api_secret', '');
    $auto_cnf = get_option('flora_seapay_auto_confirm', 1);
    $webhook_url = home_url('/wp-json/flora/v1/seapay-webhook');
    ?>
    <div class="wrap" style="max-width: 850px;">
        <h1 style="color: #0f172a; font-weight: 800; font-size: 1.45rem; display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <i class="dashicons dashicons-admin-generic" style="color: #0033a3;"></i> CẤU HÌNH CỔNG BIẾN ĐỘNG SỐ DƯ SEAPAY
        </h1>
        <p style="color: #64748b; font-size: 0.9rem; margin-top: 0;">Hệ thống đã chuẩn hóa tự động đối soát nội dung chuyển khoản <strong>FLORA &lt;MÃ_KOL&gt; &lt;SĐT&gt; &lt;MÃ_ĐƠN&gt;</strong> và đơn mồ côi <strong>FLORA &lt;SĐT&gt; &lt;MÃ_ĐƠN&gt;</strong>.</p>

        <form method="post" action="" style="background: #ffffff; padding: 28px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-top: 20px;">
            <?php wp_nonce_field('flora_seapay_settings_nonce'); ?>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px;">Đường Dẫn Webhook Tiếp Nhận SeaPay (Copy vào bảng điều khiển SeaPay):</label>
                <div style="display: flex; gap: 8px;">
                    <input type="text" readonly value="<?php echo esc_url($webhook_url); ?>" style="width: 100%; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; font-family: monospace; font-size: 0.95rem; color: #0033a3; font-weight: 700;">
                    <button type="button" class="button button-secondary" onclick="navigator.clipboard.writeText('<?php echo esc_url($webhook_url); ?>'); alert('Đã sao chép Webhook URL!');" style="padding: 0 16px; font-weight: 600;">Sao chép</button>
                </div>
            </div>

            <div style="margin-bottom: 22px;">
                <label style="display: block; font-weight: 700; color: #1e293b; margin-bottom: 8px;">SeaPay API Secret / Authorization Token (Tùy chọn bảo mật):</label>
                <input type="text" name="flora_seapay_api_secret" value="<?php echo esc_attr($secret); ?>" placeholder="Nhập Token bí mật từ SeaPay (hoặc để trống nếu chưa bật token)" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem;">
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 4px;">Nếu nhập, SeaPay Webhook phải gửi kèm Bearer Token tương ứng hoặc tham số <code>?secret=...</code></p>
            </div>

            <div style="margin-bottom: 28px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; color: #1e293b; cursor: pointer;">
                    <input type="checkbox" name="flora_seapay_auto_confirm" value="1" <?php checked($auto_cnf, 1); ?>>
                    Tự động chuyển trạng thái đơn hàng sang "Đã thanh toán" và kích hoạt hoa hồng KOL khi nhận Webhook khớp tiền
                </label>
            </div>

            <button type="submit" name="flora_save_seapay_settings" class="button button-primary">
                Lưu Cấu Hình SeaPay
            </button>
        </form>

        <div style="background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; margin-top: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <h3 style="color: #0f172a; margin-top: 0; font-weight: 700;"><i class="dashicons dashicons-info" style="color: #0033a3;"></i> Quy Trình Tích Hợp SeaPay Trong 3 Bước:</h3>
            <ol style="margin: 0; padding-left: 20px; color: #475569; line-height: 1.7; font-size: 0.92rem;">
                <li>Đăng ký tài khoản tại <a href="https://seapay.vn" target="_blank" style="font-weight: 700; color: #0033a3;">SeaPay.vn</a> và liên kết số tài khoản Ngân hàng nhận tiền của Nha Khoa Flora (ACB, VCB, MBBank...).</li>
                <li>Vào mục <strong>Cấu hình Webhook</strong> trên SeaPay, dán đường dẫn Webhook ở trên vào.</li>
                <li>Hệ thống Flora tự động xử lý chuỗi <code>FLORA &lt;MÃ_KOL&gt; &lt;SĐT&gt; &lt;MÃ_ĐƠN&gt;</code>, chốt đơn trong 1 giây, tính hoa hồng cho KOL và gửi email tự động!</li>
            </ol>
        </div>
    </div>
    <?php
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 8. AJAX ADMIN DUYỆT & TỪ CHỐI ĐƠN RÚT TIỀN CỦA ĐỐI TÁC
 * ─────────────────────────────────────────────────────────────────────────────
 */
add_action('wp_ajax_flora_ajax_admin_process_payout', 'flora_ajax_admin_process_payout');
function flora_ajax_admin_process_payout() {
    check_ajax_referer('flora_affiliate_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Không có quyền thực hiện.'));
    }

    $payout_id   = isset($_POST['payout_id']) ? (int)$_POST['payout_id'] : 0;
    $action_type = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
    $reference   = isset($_POST['reference']) ? sanitize_text_field($_POST['reference']) : '';
    $notes       = isset($_POST['notes']) ? sanitize_textarea_field($_POST['notes']) : '';
    $reason      = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';

    if ($payout_id <= 0 || !in_array($action_type, array('completed', 'rejected'))) {
        wp_send_json_error(array('message' => 'Dữ liệu yêu cầu không hợp lệ.'));
    }

    global $wpdb;
    $payout_table = flora_get_affiliate_payouts_table_name();
    $aff_table    = flora_get_affiliates_table_name();

    $payout = $wpdb->get_row($wpdb->prepare("SELECT * FROM $payout_table WHERE id = %d LIMIT 1", $payout_id), ARRAY_A);
    if (!$payout) {
        wp_send_json_error(array('message' => 'Không tìm thấy đơn rút tiền này.'));
    }

    if ($payout['status'] === 'completed') {
        wp_send_json_error(array('message' => 'Đơn rút này đã được chuyển khoản trước đó!'));
    }

    $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE id = %d LIMIT 1", $payout['affiliate_id']), ARRAY_A);
    if (!$kol) {
        wp_send_json_error(array('message' => 'Không tìm thấy hồ sơ đối tác của đơn này.'));
    }

    if ($action_type === 'completed') {
        // Cập nhật payout thành completed
        $wpdb->update($payout_table, array(
            'status'                => 'completed',
            'transaction_reference' => $reference,
            'notes'                 => !empty($notes) ? $notes : ($payout['notes'] ?: 'Kế toán đã chuyển khoản thành công'),
            'processed_at'          => current_time('mysql'),
            'created_by'            => get_current_user_id()
        ), array('id' => $payout_id));

        // Cập nhật paid_commission cho KOL
        $wpdb->query($wpdb->prepare(
            "UPDATE $aff_table SET paid_commission = paid_commission + %d WHERE id = %d",
            (int)$payout['amount'],
            $kol['id']
        ));

        // Bắn thông báo Zalo Bot
        $payout['transaction_reference'] = $reference;
        $payout['notes'] = $notes;
        flora_send_zalo_payout_processed_notification($payout, $kol, 'completed');

        wp_send_json_success(array(
            'message' => '🎉 Đã duyệt và xác nhận chuyển ' . number_format($payout['amount'], 0, ',', '.') . 'đ cho đối tác ' . esc_html($kol['name']) . '! Zalo Bot đã gửi thông báo đến nhóm kế toán.'
        ));
    } else {
        // Từ chối đơn rút tiền
        $reject_note = !empty($reason) ? $reason : 'Thông tin tài khoản hoặc điều kiện rút chưa hợp lệ';
        $wpdb->update($payout_table, array(
            'status'       => 'rejected',
            'notes'        => $reject_note,
            'processed_at' => current_time('mysql'),
            'created_by'   => get_current_user_id()
        ), array('id' => $payout_id));

        // Bắn thông báo Zalo Bot
        flora_send_zalo_payout_processed_notification($payout, $kol, 'rejected', $reject_note);

        wp_send_json_success(array(
            'message' => '⚠️ Đã từ chối đơn rút tiền #' . esc_html($payout['payout_code']) . '. Số tiền đã được tự động hoàn lại Ví Khả Dụng của đối tác.'
        ));
    }
}

/**
 * ─────────────────────────────────────────────────────────────────────────────
 * 9. GIAO DIỆN QUẢN LÝ ĐƠN RÚT TIỀN TRONG WP-ADMIN (SUBMENU)
 * ─────────────────────────────────────────────────────────────────────────────
 */
function flora_render_affiliate_payouts_admin_page() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $payout_table = flora_get_affiliate_payouts_table_name();
    $aff_table    = flora_get_affiliates_table_name();

    $filter_status = isset($_GET['payout_status']) ? sanitize_text_field($_GET['payout_status']) : '';
    $search        = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    $where = "1=1";
    if (!empty($filter_status) && in_array($filter_status, array('requested', 'completed', 'rejected'))) {
        $where .= $wpdb->prepare(" AND p.status = %s", $filter_status);
    }
    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(" AND (p.payout_code LIKE %s OR k.name LIKE %s OR k.phone LIKE %s OR k.ref_code LIKE %s OR p.bank_account LIKE %s)", $like, $like, $like, $like, $like);
    }

    // Thống kê nhanh
    $stats = $wpdb->get_row("
        SELECT 
            COUNT(*) as total_requests,
            COALESCE(SUM(CASE WHEN status IN ('requested', 'pending') THEN 1 ELSE 0 END), 0) as pending_cnt,
            COALESCE(SUM(CASE WHEN status IN ('requested', 'pending') THEN amount ELSE 0 END), 0) as pending_amount,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END), 0) as completed_cnt,
            COALESCE(SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END), 0) as completed_amount,
            COALESCE(SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_cnt
        FROM $payout_table
    ", ARRAY_A);

    $payouts = $wpdb->get_results("
        SELECT p.*, k.name as kol_name, k.phone as kol_phone, k.ref_code, k.email as kol_email
        FROM $payout_table p
        LEFT JOIN $aff_table k ON p.affiliate_id = k.id
        WHERE $where
        ORDER BY CASE WHEN p.status = 'requested' THEN 0 ELSE 1 END, p.id DESC
        LIMIT 100
    ", ARRAY_A);

    $admin_nonce = wp_create_nonce('flora_affiliate_admin_nonce');
    ?>

    <style>
        .flora-payouts-wrap {
            margin: 0;
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a;
        }
        .flora-payouts-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding: 24px 28px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
        }
        .flora-payouts-header h1 {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .flora-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .flora-kpi-item {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 18px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .flora-kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .btn-payout-approve {
            background: #16a34a !important;
            color: #ffffff !important;
            border: 1px solid #16a34a !important;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            padding: 5px 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }
        .btn-payout-approve:hover {
            background: #15803d !important;
        }
        .btn-payout-reject {
            background: #ffffff !important;
            color: #dc2626 !important;
            border: 1px solid #fca5a5 !important;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            padding: 5px 10px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }
        .btn-payout-reject:hover {
            background: #fef2f2 !important;
        }
    </style>

    <div class="flora-payouts-wrap">
        <div class="flora-payouts-header">
            <div>
                <h1><i class="dashicons dashicons-money-alt" style="color: #0033a3; font-size: 1.8rem; width: auto; height: auto;"></i> QUẢN LÝ ĐƠN YÊU CẦU RÚT TIỀN & QUYẾT TOÁN</h1>
                <p style="margin: 6px 0 0; color: #64748b; font-size: 0.88rem;">Theo dõi danh sách yêu cầu rút hoa hồng từ đối tác, xác nhận chuyển khoản và gửi thông báo Zalo Bot tự động.</p>
            </div>
            <div>
                <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliates')); ?>" class="button button-secondary" style="font-weight: 600;">
                    &larr; Quay lại Danh Sách KOL
                </a>
            </div>
        </div>

        <!-- 3 KPI BOXES -->
        <div class="flora-kpi-grid">
            <div class="flora-kpi-item" style="border-left: 4px solid #d97706;">
                <div class="flora-kpi-icon" style="background: #fffbeb; color: #d97706;"><i class="dashicons dashicons-clock"></i></div>
                <div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: #b45309;"><?php echo number_format($stats['pending_amount'], 0, ',', '.'); ?>đ</div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Chờ Chuyển Khoản: <strong><?php echo $stats['pending_cnt']; ?> đơn</strong></div>
                </div>
            </div>
            <div class="flora-kpi-item" style="border-left: 4px solid #16a34a;">
                <div class="flora-kpi-icon" style="background: #f0fdf4; color: #16a34a;"><i class="dashicons dashicons-yes-alt"></i></div>
                <div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: #15803d;"><?php echo number_format($stats['completed_amount'], 0, ',', '.'); ?>đ</div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Đã Quyết Toán: <strong><?php echo $stats['completed_cnt']; ?> đơn</strong></div>
                </div>
            </div>
            <div class="flora-kpi-item" style="border-left: 4px solid #0033a3;">
                <div class="flora-kpi-icon" style="background: #eff6ff; color: #0033a3;"><i class="dashicons dashicons-media-spreadsheet"></i></div>
                <div>
                    <div style="font-size: 1.35rem; font-weight: 800; color: #0033a3;"><?php echo $stats['total_requests']; ?> đơn</div>
                    <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Tổng Yêu Cầu Rút Tiền</div>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliate-payouts')); ?>" class="button <?php echo empty($filter_status) ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Tất Cả (<?php echo $stats['total_requests']; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliate-payouts&payout_status=requested')); ?>" class="button <?php echo ($filter_status === 'requested') ? 'button-primary' : ''; ?>" style="font-weight: 700; <?php echo ($stats['pending_cnt'] > 0) ? 'background: #fff7ed; border-color: #fdba74; color: #c2410c;' : ''; ?>">
                <i class="dashicons dashicons-clock" style="font-size: 16px; width: 16px; height: 16px; vertical-align: middle;"></i> Chờ Chuyển Khoản (<?php echo $stats['pending_cnt']; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliate-payouts&payout_status=completed')); ?>" class="button <?php echo ($filter_status === 'completed') ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Đã Chuyển Tiền (<?php echo $stats['completed_cnt']; ?>)
            </a>
            <a href="<?php echo esc_url(admin_url('admin.php?page=flora-affiliate-payouts&payout_status=rejected')); ?>" class="button <?php echo ($filter_status === 'rejected') ? 'button-primary' : ''; ?>" style="font-weight: 600;">
                Bị Từ Chối (<?php echo $stats['rejected_cnt']; ?>)
            </a>
        </div>

        <!-- TABLE OF PAYOUTS -->
        <div class="tbl-aff-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow-x: auto;">
            <table class="tbl-aff" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Mã Đơn Rút</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Thời Gian</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Đối Tác</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Số Tiền Rút</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Tài Khoản Thụ Hưởng</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Trạng Thái</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase;">Mã GD / Ghi Chú</th>
                        <th style="padding: 12px 16px; font-weight: 700; color: #64748b; font-size: 0.78rem; text-transform: uppercase; text-align: right;">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payouts)): ?>
                        <tr><td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">Không có đơn yêu cầu rút tiền nào trong danh sách này.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payouts as $p): 
                            $is_pending = in_array($p['status'], array('requested', 'pending'));
                            $is_done    = ($p['status'] === 'completed');
                            $is_rej     = ($p['status'] === 'rejected');
                            $time_fmt   = !empty($p['requested_at']) ? date('d/m/Y H:i', strtotime($p['requested_at'])) : date('d/m/Y H:i', strtotime($p['created_at']));
                        ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 14px 16px; white-space: nowrap;">
                                <strong style="color: #0033a3; font-family: monospace; font-size: 0.92rem;"><?php echo esc_html($p['payout_code'] ?: ('WD-' . $p['id'])); ?></strong>
                            </td>
                            <td style="padding: 14px 16px; white-space: nowrap; color: #64748b; font-size: 0.82rem;">
                                <?php echo esc_html($time_fmt); ?>
                            </td>
                            <td style="padding: 14px 16px; white-space: nowrap;">
                                <strong style="color: #0f172a; font-size: 0.92rem;"><?php echo esc_html($p['kol_name'] ?: 'Đối tác #' . $p['affiliate_id']); ?></strong>
                                <div style="color: #64748b; font-size: 0.8rem;"><?php echo esc_html($p['kol_phone']); ?> • REF: <span style="font-weight: 700; color: #0033a3;"><?php echo esc_html($p['ref_code']); ?></span></div>
                            </td>
                            <td style="padding: 14px 16px; white-space: nowrap;">
                                <strong style="color: #16a34a; font-size: 1rem;"><?php echo number_format($p['amount'], 0, ',', '.'); ?> VNĐ</strong>
                            </td>
                            <td style="padding: 14px 16px; font-size: 0.85rem; line-height: 1.4;">
                                <strong><?php echo esc_html($p['bank_name']); ?></strong><br>
                                STK: <strong style="color: #0033a3; font-family: monospace;"><?php echo esc_html($p['bank_account']); ?></strong><br>
                                Chủ TK: <span style="text-transform: uppercase; font-weight: 700;"><?php echo esc_html($p['bank_owner']); ?></span>
                            </td>
                            <td style="padding: 14px 16px; white-space: nowrap;">
                                <?php if ($is_pending): ?>
                                    <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 9999px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="dashicons dashicons-clock"></i> Chờ chuyển khoản
                                    </span>
                                <?php elseif ($is_done): ?>
                                    <span style="background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 9999px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="dashicons dashicons-yes-alt"></i> Đã chuyển tiền
                                    </span>
                                <?php else: ?>
                                    <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 9999px; font-weight: 700; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="dashicons dashicons-dismiss"></i> Bị từ chối
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 14px 16px; font-size: 0.84rem; color: #475569; max-width: 200px;">
                                <?php if (!empty($p['transaction_reference'])): ?>
                                    <code style="background: #eff6ff; color: #0033a3; padding: 2px 6px; border-radius: 4px; font-weight: 700;"><?php echo esc_html($p['transaction_reference']); ?></code><br>
                                <?php endif; ?>
                                <span><?php echo esc_html($p['notes'] ?: '—'); ?></span>
                            </td>
                            <td style="padding: 14px 16px; white-space: nowrap; text-align: right;">
                                <?php if ($is_pending): ?>
                                    <button type="button" class="btn-payout-approve" onclick="openAdminApproveModal(<?php echo $p['id']; ?>, '<?php echo esc_js($p['payout_code']); ?>', <?php echo (int)$p['amount']; ?>, '<?php echo esc_js($p['kol_name']); ?>')">
                                        <i class="dashicons dashicons-yes"></i> Duyệt & Chuyển Tiền
                                    </button>
                                    <button type="button" class="btn-payout-reject" onclick="openAdminRejectModal(<?php echo $p['id']; ?>, '<?php echo esc_js($p['payout_code']); ?>')">
                                        <i class="dashicons dashicons-no"></i> Từ Chối
                                    </button>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.8rem;"><i class="dashicons dashicons-saved"></i> Đã hoàn tất</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DUYỆT ĐƠN RÚT TIỀN -->
    <div id="modalAdminApprovePayout" class="flora-modal">
        <div class="flora-modal-content" style="max-width: 480px;">
            <span class="flora-modal-close" onclick="closeAdminPayoutModal('modalAdminApprovePayout')">&times;</span>
            <h3 style="color: #15803d; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <i class="dashicons dashicons-yes-alt" style="font-size: 24px; width: 24px; height: 24px;"></i> 
                <span>Xác Nhận Đã Chuyển Khoản Hoa Hồng</span>
            </h3>
            <p id="approveModalDesc" style="color: #64748b; font-size: 0.88rem; margin-bottom: 18px;"></p>
            <form id="formAdminApprovePayout" onsubmit="handleAdminApprovePayout(event)">
                <input type="hidden" name="payout_id" id="approvePayoutId" value="0">
                <input type="hidden" name="action_type" value="completed">
                <input type="hidden" name="nonce" value="<?php echo $admin_nonce; ?>">

                <div class="form-row">
                    <label>Mã Giao Dịch Ngân Hàng (FT / Ủy Nhiệm Chi) (*):</label>
                    <input type="text" name="reference" id="approvePayoutRef" required placeholder="Vd: FT261029481729 hoặc UNC-8899">
                </div>
                <div class="form-row">
                    <label>Ghi Chú Kế Toán (Tùy chọn):</label>
                    <textarea name="notes" id="approvePayoutNotes" rows="2" placeholder="Vd: Đã chuyển khoản qua App ACB lúc 14:30..."></textarea>
                </div>

                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #166534; margin-bottom: 18px;">
                    <i class="dashicons dashicons-megaphone"></i> Hệ thống sẽ tự động <strong>bắn thông báo Zalo Bot</strong> và cập nhật trạng thái "Đã chuyển tiền" trong Cổng Đối Tác ngay lập tức.
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-flora-sec" onclick="closeAdminPayoutModal('modalAdminApprovePayout')">Hủy</button>
                    <button type="submit" class="btn-payout-approve" id="btnConfirmApprove" style="padding: 8px 18px; font-size: 13px;">
                        Xác Nhận Hoàn Tất Chuyển Tiền
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TỪ CHỐI ĐƠN RÚT TIỀN -->
    <div id="modalAdminRejectPayout" class="flora-modal">
        <div class="flora-modal-content" style="max-width: 480px;">
            <span class="flora-modal-close" onclick="closeAdminPayoutModal('modalAdminRejectPayout')">&times;</span>
            <h3 style="color: #b91c1c; margin-top: 0; display: flex; align-items: center; gap: 8px;">
                <i class="dashicons dashicons-dismiss" style="font-size: 24px; width: 24px; height: 24px;"></i> 
                <span>Từ Chối Đơn Rút Tiền</span>
            </h3>
            <p id="rejectModalDesc" style="color: #64748b; font-size: 0.88rem; margin-bottom: 18px;"></p>
            <form id="formAdminRejectPayout" onsubmit="handleAdminRejectPayout(event)">
                <input type="hidden" name="payout_id" id="rejectPayoutId" value="0">
                <input type="hidden" name="action_type" value="rejected">
                <input type="hidden" name="nonce" value="<?php echo $admin_nonce; ?>">

                <div class="form-row">
                    <label>Lý Do Từ Chối (*):</label>
                    <textarea name="reason" id="rejectPayoutReason" rows="3" required placeholder="Vd: Tên chủ thẻ không trùng khớp với họ tên đăng ký đối tác, vui lòng cập nhật lại STK..."></textarea>
                </div>

                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; color: #991b1b; margin-bottom: 18px;">
                    <i class="dashicons dashicons-warning"></i> Số tiền rút sẽ được <strong>hoàn lại vào Ví Khả Dụng</strong> của đối tác. Thông báo sẽ gửi qua Zalo Bot.
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-flora-sec" onclick="closeAdminPayoutModal('modalAdminRejectPayout')">Hủy</button>
                    <button type="submit" class="btn-payout-reject" id="btnConfirmReject" style="padding: 8px 18px; font-size: 13px;">
                        Xác Nhận Từ Chối
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const AJAX_URL_PAYOUT = '<?php echo admin_url('admin-ajax.php'); ?>';

        function closeAdminPayoutModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function openAdminApproveModal(id, code, amount, kolName) {
            document.getElementById('approvePayoutId').value = id;
            document.getElementById('approveModalDesc').innerHTML = 'Duyệt đơn <strong>#' + code + '</strong> của đối tác <strong>' + kolName + '</strong> số tiền <strong>' + amount.toLocaleString('vi-VN') + ' VNĐ</strong>.';
            document.getElementById('approvePayoutRef').value = '';
            document.getElementById('approvePayoutNotes').value = '';
            document.getElementById('modalAdminApprovePayout').style.display = 'flex';
        }

        function openAdminRejectModal(id, code) {
            document.getElementById('rejectPayoutId').value = id;
            document.getElementById('rejectModalDesc').innerHTML = 'Bạn đang thao tác từ chối đơn rút tiền <strong>#' + code + '</strong>.';
            document.getElementById('rejectPayoutReason').value = '';
            document.getElementById('modalAdminRejectPayout').style.display = 'flex';
        }

        async function handleAdminApprovePayout(e) {
            e.preventDefault();
            const btn = document.getElementById('btnConfirmApprove');
            const orig = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Đang lưu...';

            const form = document.getElementById('formAdminApprovePayout');
            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_admin_process_payout');

            try {
                const res = await fetch(AJAX_URL_PAYOUT, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể duyệt đơn'));
                    btn.disabled = false;
                    btn.innerText = orig;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerText = orig;
            }
        }

        async function handleAdminRejectPayout(e) {
            e.preventDefault();
            const btn = document.getElementById('btnConfirmReject');
            const orig = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Đang xử lý...';

            const form = document.getElementById('formAdminRejectPayout');
            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_admin_process_payout');

            try {
                const res = await fetch(AJAX_URL_PAYOUT, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể từ chối đơn'));
                    btn.disabled = false;
                    btn.innerText = orig;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerText = orig;
            }
        }
    </script>
    <?php
}

