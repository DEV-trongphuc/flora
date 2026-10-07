<?php
/**
 * Template Name: Public Affiliate / KOL Portal
 * Description: Cổng thông tin đối tác & KOL theo dõi lượt click, doanh thu, hoa hồng và chủ động cập nhật tài khoản ngân hàng
 */

if (!defined('ABSPATH')) exit;

// Xác thực Token từ URL hoặc Cookie phiên làm việc
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : (isset($_GET['view_kol']) ? sanitize_text_field($_GET['view_kol']) : (isset($_COOKIE['flora_partner_token']) ? sanitize_text_field($_COOKIE['flora_partner_token']) : ''));
$kol = null;
if (!empty($token) && function_exists('flora_affiliate_get_by_token')) {
    $found = flora_affiliate_get_by_token($token);
    if ($found && $found['status'] === 'active') {
        $kol = $found;
    }
}

// Lấy danh sách đơn hàng và lịch sử payout nếu có KOL
// Lấy danh sách đơn hàng, thống kê tài chính và lịch sử payout nếu có KOL
$orders = array();
$payouts = array();
$financial_stats = array(
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

if ($kol) {
    global $wpdb;
    $orders_table = function_exists('flora_get_orders_table_name') ? flora_get_orders_table_name() : $wpdb->prefix . 'flora_orders';
    $payout_table = function_exists('flora_get_affiliate_payouts_table_name') ? flora_get_affiliate_payouts_table_name() : $wpdb->prefix . 'flora_affiliate_payouts';

    $orders = $wpdb->get_results($wpdb->prepare(
        "SELECT order_code, customer_name, customer_phone, package_name, final_amount, commission_amount, payment_status, commission_status, created_at, paid_at 
         FROM $orders_table 
         WHERE affiliate_id = %d 
         ORDER BY created_at DESC LIMIT 150",
        $kol['id']
    ), ARRAY_A);

    if (function_exists('flora_affiliate_get_financial_stats')) {
        $financial_stats = flora_affiliate_get_financial_stats($kol['id']);
    } else {
        $financial_stats['available_balance'] = max(0, $kol['total_commission'] - $kol['paid_commission']);
        $financial_stats['confirmed_commission'] = $kol['total_commission'];
        $financial_stats['paid_commission'] = $kol['paid_commission'];
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$payout_table'") == $payout_table) {
        $payouts = $wpdb->get_results($wpdb->prepare(
            "SELECT id, payout_code, amount, status, payment_method, bank_name, bank_account, bank_owner, transaction_reference, notes, requested_at, created_at 
             FROM $payout_table 
             WHERE affiliate_id = %d 
             ORDER BY created_at DESC LIMIT 50",
            $kol['id']
        ), ARRAY_A);
    }
}

$ref_url = $kol ? home_url('/goi-dich-vu?ref=' . $kol['ref_code']) : '';
$qr_code_url = $kol ? 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ref_url) : '';
$available_balance = $financial_stats['available_balance'];
$pending_commission = $financial_stats['pending_commission'];
$confirmed_commission = $financial_stats['confirmed_commission'];
$comm_text = $kol ? (($kol['commission_type'] === 'fixed') ? number_format($kol['commission_rate'], 0, ',', '.') . 'đ/đơn' : $kol['commission_rate'] . '%') : '10%';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $kol ? 'Cổng Đối Tác: ' . esc_html($kol['name']) : 'Cổng Thông Tin Đối Tác & KOL'; ?> | Nha Khoa Flora</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo flora_asset('fonts/fontawesome/css/all.min.css'); ?>" data-no-optimize="1">

    <style>
        :root {
            --flora-primary: #0033a3;
            --flora-primary-hover: #002277;
            --flora-accent: #0493f1;
            --flora-emerald: #16a34a;
            --flora-gold: #d97706;
            --flora-dark: #0f172a;
            --flora-gray-bg: #f8fafc;
            --flora-border: #e2e8f0;
        }

        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }

        /* Typography: Standard UI text with Plus Jakarta Sans without breaking icon fonts */
        html, body, button, input, select, textarea, optgroup, a, p, strong, b, h1, h2, h3, h4, h5, h6, table, th, td {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        /* FontAwesome Protection: Never let general font-family override icon glyphs */
        .fa, .fas, .far, .fab, .fa-solid, .fa-regular, .fa-brands, [class^="fa-"], [class*=" fa-"], i[class*="fa-"] {
            font-family: "Font Awesome 6 Free", "FontAwesome" !important;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
        }
        .fa-brands, .fab {
            font-family: "Font Awesome 6 Brands", "FontAwesome" !important;
        }

        body {
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        button, input, select, textarea {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .portal-container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 24px 20px 60px;
        }

        /* Top Brand Navigation */
        .portal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 14px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 51, 163, 0.04);
            border: 1px solid var(--flora-border);
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .portal-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .portal-logo-img {
            height: 46px;
            width: auto;
            max-width: 190px;
            object-fit: contain;
            display: block;
        }
        .portal-logo-divider {
            width: 1.5px;
            height: 28px;
            background: #e2e8f0;
        }
        .portal-logo-badge {
            display: flex;
            flex-direction: column;
        }
        .portal-logo-title {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--flora-primary);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }
        .portal-logo-sub {
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .portal-nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-portal-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .btn-portal-primary {
            background: #eff6ff;
            color: #0033a3;
            border-color: #bfdbfe;
        }
        .btn-portal-primary:hover {
            background: #dbeafe;
        }
        .btn-portal-logout {
            background: #fff;
            color: #dc2626;
            border-color: #fecaca;
        }
        .btn-portal-logout:hover {
            background: #fee2e2;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #001f66 0%, #0033a3 50%, #0493f1 100%);
            border-radius: 20px;
            padding: 32px 36px;
            color: #ffffff;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(0, 51, 163, 0.12);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        .hero-banner h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .hero-banner p {
            color: rgba(255, 255, 255, 0.88);
            font-size: 0.92rem;
            max-width: 640px;
            line-height: 1.6;
        }
        .kol-ref-tag {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-family: monospace;
            letter-spacing: 1px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            font-weight: 700;
        }
        .hero-badge-rate {
            background: #fef3c7;
            color: #92400e;
            padding: 8px 18px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        /* 4 KPI Grid */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        @media (max-width: 991px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 550px) {
            .kpi-grid {
                grid-template-columns: 1fr;
            }
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 20px 18px;
            border: 1px solid var(--flora-border);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }
        .kpi-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .kpi-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
        }
        .kpi-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .kpi-icon-wrap svg {
            display: block;
        }
        .kpi-number {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
            color: #0f172a;
        }
        .kpi-unit {
            font-size: 0.95rem;
            font-weight: 600;
            color: #64748b;
            margin-left: 2px;
        }
        .kpi-sub {
            font-size: 0.78rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* Referral Box Card */
        .ref-box-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 26px 28px;
            border: 1px solid var(--flora-border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            margin-bottom: 24px;
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 28px;
            align-items: center;
        }
        @media (max-width: 850px) {
            .ref-box-card {
                grid-template-columns: 1fr;
            }
        }
        .ref-input-group {
            display: flex;
            gap: 8px;
            margin-top: 14px;
        }
        .ref-input {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            font-weight: 600;
            color: #0033a3;
        }
        .btn-copy {
            background: #0033a3;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0 20px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-copy:hover {
            background: #002277;
        }
        .qr-box {
            text-align: center;
            background: #f8fafc;
            padding: 14px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
        }
        .qr-box img {
            width: 140px;
            height: 140px;
            display: block;
            margin: 0 auto 10px;
            border-radius: 8px;
            background: #ffffff;
            padding: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        /* Data Tables */
        .data-card {
            background: #ffffff;
            border-radius: 18px;
            border: 1px solid var(--flora-border);
            box-shadow: 0 2px 12px rgba(0,0,0,0.02);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .data-header {
            padding: 20px 26px;
            border-bottom: 1px solid var(--flora-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .data-header h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--flora-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .table-responsive {
            overflow-x: auto;
            width: 100%;
        }
        .data-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            font-size: 0.88rem;
            text-align: left;
        }
        .data-table th {
            background: #f8fafc;
            padding: 12px 16px;
            color: #64748b;
            font-weight: 700;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--flora-border);
            white-space: nowrap;
        }
        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }
        .data-table tr:hover td {
            background: #f8fafc;
        }
        .status-badge-paid {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap !important;
            letter-spacing: 0.3px;
        }
        .status-badge-pending {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap !important;
        /* Zalo Bot Integration Card */
        .zalo-bot-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0, 51, 163, 0.04);
            border: 1px solid var(--flora-border);
            margin-bottom: 24px;
        }
        .zalo-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 14px;
        }
        .zalo-badge-connected {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .zalo-badge-disconnected {
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .zalo-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #1e293b;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .zalo-action-btn:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }
        .zalo-action-btn.btn-primary-blue {
            background: #0033a3;
            color: #ffffff;
            border-color: #0033a3;
        }
        .zalo-action-btn.btn-primary-blue:hover {
            background: #002277;
        }
        .zalo-syntax-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 14px;
        }
        .zalo-commands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
            margin-top: 16px;
        }
        .zalo-cmd-item {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.82rem;
            color: #475569;
        }
        .zalo-cmd-code {
            font-family: monospace;
            font-weight: 800;
            color: #0033a3;
            background: #eff6ff;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .btn-zalo-one-touch {
            background: linear-gradient(135deg, #0068ff 0%, #0033a3 100%);
            color: #ffffff !important;
            border: none;
            padding: 9px 18px;
            border-radius: 9px;
            font-size: 0.86rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(0, 104, 255, 0.22);
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-zalo-one-touch:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 104, 255, 0.32);
        }
        .zalo-spinner-ring {
            width: 22px;
            height: 22px;
            border: 2.5px solid #bfdbfe;
            border-top-color: #0068ff;
            border-radius: 50%;
            animation: zaloSpin 0.8s linear infinite;
            flex-shrink: 0;
        }
        @keyframes zaloSpin {
            to { transform: rotate(360deg); }
        }

        /* Modal Styles */
        .portal-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .portal-modal-content {
            background: #ffffff;
            border-radius: 20px;
            max-width: 540px;
            width: 100%;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        .portal-modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.1rem;
            cursor: pointer;
        }
        .portal-modal-close:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* ══════════════════════════════════════════════════════════
           LUXURY SPLIT AUTHENTICATION PORTAL (GATEWAY)
           ══════════════════════════════════════════════════════════ */
        .portal-auth-split-wrapper {
            display: grid;
            grid-template-columns: 1.15fr 1fr;
            background: #ffffff;
            border-radius: 26px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 31, 102, 0.08);
            border: 1px solid var(--flora-border);
            margin: 10px auto 50px;
            min-height: 620px;
        }
        @media (max-width: 960px) {
            .portal-auth-split-wrapper {
                grid-template-columns: 1fr;
                border-radius: 20px;
            }
        }

        /* Left Visual Showcase */
        .auth-visual-panel {
            position: relative;
            padding: 48px 42px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .auth-visual-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            transform: scale(1.04);
            transition: transform 0.6s ease;
        }
        .portal-auth-split-wrapper:hover .auth-visual-bg {
            transform: scale(1.08);
        }
        .auth-visual-gradient {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 24, 82, 0.95) 0%, rgba(0, 51, 163, 0.88) 60%, rgba(4, 147, 241, 0.82) 100%);
        }
        .auth-visual-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            gap: 24px;
        }
        .auth-visual-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .auth-visual-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.95rem;
            font-weight: 800;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin: 0;
        }
        .auth-visual-title .text-gold {
            background: linear-gradient(90deg, #fde68a 0%, #fbbf24 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-visual-desc {
            font-size: 0.92rem;
            color: rgba(255, 255, 255, 0.88);
            line-height: 1.6;
            margin: 0;
        }
        .auth-perks-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin: 6px 0;
        }
        .auth-perk-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            padding: 12px 16px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .auth-perk-icon-wrap {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            color: #ffffff !important;
            flex-shrink: 0;
        }
        .auth-perk-icon-wrap svg {
            display: block;
            stroke: #ffffff;
        }
        .auth-perk-icon-wrap i {
            color: #ffffff !important;
            font-size: 1.15rem;
        }
        .auth-perk-head {
            font-weight: 800;
            font-size: 0.92rem;
            color: #ffffff;
            margin-bottom: 2px;
        }
        .auth-perk-text {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.45;
        }
        .auth-visual-doctor-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }
        .auth-doc-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .auth-doc-name {
            font-weight: 800;
            font-size: 0.92rem;
            color: #ffffff;
        }
        .auth-doc-role {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Right Auth Panel */
        .auth-card-panel {
            background: #ffffff;
            padding: 44px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        @media (max-width: 600px) {
            .auth-card-panel {
                padding: 30px 20px;
            }
            .auth-visual-panel {
                padding: 36px 22px;
            }
        }
        .auth-card-inner {
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
        }
        .auth-card-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-header-logo-wrap {
            display: inline-block;
            margin-bottom: 12px;
        }
        .auth-card-logo {
            height: 48px;
            width: auto;
            max-width: 190px;
            object-fit: contain;
        }
        .auth-card-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--flora-primary);
            margin: 0 0 6px;
            letter-spacing: -0.3px;
        }
        .auth-card-sub {
            font-size: 0.84rem;
            color: #64748b;
            margin: 0;
            line-height: 1.5;
        }
        .auth-tabs {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .auth-tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background: transparent;
            font-size: 0.92rem;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .auth-tab-btn.active {
            background: #ffffff;
            color: #0033a3;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }
        .auth-input-group {
            margin-bottom: 18px;
        }
        .auth-label {
            display: block;
            font-size: 0.84rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .auth-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .auth-input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 0.95rem;
            pointer-events: none;
        }
        .auth-input {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.92rem;
            box-sizing: border-box;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .auth-input.has-icon {
            padding-left: 40px;
        }
        .auth-input.has-eye {
            padding-right: 42px;
        }
        .auth-input:focus {
            outline: none;
            border-color: #0033a3;
            box-shadow: 0 0 0 3px rgba(0, 51, 163, 0.1);
        }
        .btn-toggle-password {
            position: absolute;
            right: 8px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 0.95rem;
            cursor: pointer;
            padding: 6px 8px;
            border-radius: 6px;
            transition: color 0.2s ease;
        }
        .btn-toggle-password:hover {
            color: var(--flora-primary);
        }
        .auth-label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .auth-forgot-link {
            font-size: 0.78rem;
            color: #0284c7;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .auth-forgot-link:hover {
            text-decoration: underline;
        }
        .auth-security-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 0.78rem;
            color: #64748b;
            margin-bottom: 18px;
            line-height: 1.4;
        }
        .auth-security-badge i {
            color: #16a34a;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .auth-email-security-note {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.8rem;
            color: #166534;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.45;
        }
        .auth-email-security-note i {
            color: #16a34a;
            font-size: 1.1rem;
            margin-top: 2px;
            flex-shrink: 0;
        }
        .auth-card-footer {
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.84rem;
            color: #64748b;
        }
        .auth-hotline-link {
            color: var(--flora-primary);
            font-weight: 800;
            text-decoration: none;
        }
        .btn-auth-submit {
            width: 100%;
            background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 13px 20px;
            font-size: 0.98rem;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 51, 163, 0.2);
            margin-top: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-auth-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 163, 0.28);
        }
        .btn-auth-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }
        .btn-action-withdraw {
            font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #0033a3;
            color: #ffffff !important;
            border: 1px solid #00257a;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 51, 163, 0.15);
            transition: all 0.2s ease;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            -webkit-font-smoothing: antialiased;
        }
        .btn-action-withdraw:hover {
            background: #002277;
            border-color: #001a5e;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 51, 163, 0.25);
            color: #ffffff !important;
        }
        .btn-action-withdraw.is-disabled {
            background: #e2e8f0;
            border-color: #cbd5e1;
            color: #64748b !important;
            box-shadow: none;
            cursor: pointer;
            transform: none;
        }
        .btn-action-withdraw.is-disabled:hover {
            background: #cbd5e1;
            color: #334155 !important;
            transform: none;
            box-shadow: none;
        }
    </style>
</head>
<body>

    <div class="portal-container">
        <!-- TOP BRAND NAVIGATION -->
        <header class="portal-nav">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="portal-logo" title="Trang chủ Nha Khoa Flora">
                <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="Nha Khoa Flora" class="portal-logo-img">
                <div class="portal-logo-divider"></div>
                <div class="portal-logo-badge">
                    <div class="portal-logo-title">CỔNG ĐỐI TÁC</div>
                    <div class="portal-logo-sub">Affiliate Partner Network</div>
                </div>
            </a>
            
            <div class="portal-nav-actions">
                <?php if ($kol): ?>
                    <button type="button" class="btn-portal-action btn-portal-primary" onclick="openProfileModal()">
                        <i class="fa-solid fa-building-columns"></i> Cập Nhật STK / Hồ Sơ
                    </button>
                    <button type="button" class="btn-portal-action btn-portal-logout" onclick="handlePartnerLogout()">
                        <i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất
                    </button>
                <?php else: ?>
                    <a href="tel:02873058999" class="btn-portal-action" style="background: #f8fafc; color: #334155; border: 1px solid #e2e8f0;">
                        <i class="fa-solid fa-phone" style="color: #0033a3;"></i> <span>Hotline: <strong>028 7305 8999</strong></span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/dang-ky-doi-tac/')); ?>" class="btn-portal-action" style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); color: #ffffff; border: none; padding: 9px 18px; box-shadow: 0 4px 12px rgba(0,51,163,0.2);">
                        <i class="fa-solid fa-user-plus"></i> Đăng Ký Đối Tác
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (!$kol): ?>
            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- LUXURY SPLIT GATEWAY: ĐĂNG NHẬP / ĐĂNG KÝ ĐỐI TÁC -->
            <!-- ══════════════════════════════════════════════════════════ -->
            <div class="portal-auth-split-wrapper">
                <!-- LEFT: LUXURY BRAND VISUAL PANEL -->
                <div class="auth-visual-panel">
                    <div class="auth-visual-bg" style="background-image: url('<?php echo flora_asset('assets/homepage/bac_si_minh_doi_ngu.webp'); ?>');"></div>
                    <div class="auth-visual-gradient"></div>
                    <div class="auth-visual-content">
                        <div>
                            <div class="auth-visual-eyebrow">
                                <i class="fa-solid fa-crown" style="color: #fbbf24;"></i> HỆ THỐNG ĐỐI TÁC NHA KHOA THỤY SĨ
                            </div>
                            <h2 class="auth-visual-title" style="margin-top: 14px;">
                                Gia Tăng Thu Nhập Cùng <br><span class="text-gold">Nha Khoa Flora</span>
                            </h2>
                            <p class="auth-visual-desc" style="margin-top: 10px;">
                                Cổng quản trị chuyên nghiệp dành riêng cho Đối tác & KOLs. Theo dõi lượt giới thiệu, đơn kích hoạt và hoa hồng tức thì với độ chính xác 100%.
                            </p>
                        </div>

                        <div class="auth-perks-list">
                            <div class="auth-perk-item">
                                <div class="auth-perk-icon-wrap">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="20" x2="18" y2="10"></line>
                                        <line x1="12" y1="20" x2="12" y2="4"></line>
                                        <line x1="6" y1="20" x2="6" y2="14"></line>
                                        <polyline points="4 6 12 2 20 6"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <div class="auth-perk-head">Đối Soát Tự Động</div>
                                    <div class="auth-perk-text">Dữ liệu đơn hàng cập nhật tự động ngay khi khách hoàn tất dịch vụ.</div>
                                </div>
                            </div>
                            <div class="auth-perk-item">
                                <div class="auth-perk-icon-wrap">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"></path>
                                        <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"></path>
                                        <circle cx="18" cy="14" r="1.5" fill="currentColor"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <div class="auth-perk-head">Hoa Hồng Hấp Dẫn & Rút Linh Hoạt</div>
                                    <div class="auth-perk-text">Yêu cầu rút hoa hồng 24/7 trực tiếp về tài khoản ngân hàng của bạn.</div>
                                </div>
                            </div>
                            <div class="auth-perk-item">
                                <div class="auth-perk-icon-wrap">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                        <polyline points="9 12 11 14 15 10"></polyline>
                                    </svg>
                                </div>
                                <div>
                                    <div class="auth-perk-head">Uy Tín Y Khoa Thụy Sĩ</div>
                                    <div class="auth-perk-text">Đội ngũ Bác sĩ CKI chuyên môn cao, mang lại sự an tâm tuyệt đối cho khách hàng.</div>
                                </div>
                            </div>
                        </div>

                        <div class="auth-visual-doctor-box">
                            <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS. CKI Nguyễn Đắc Minh" class="auth-doc-avatar">
                            <div>
                                <div class="auth-doc-name">BS. CKI Nguyễn Đắc Minh</div>
                                <div class="auth-doc-role">Giám đốc chuyên môn & Kỹ thuật Nha Khoa Flora</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: AUTH CARD PANEL -->
                <div class="auth-card-panel">
                    <div class="auth-card-inner">
                        <div class="auth-card-header">
                            <div class="auth-header-logo-wrap">
                                <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="Nha Khoa Flora" class="auth-card-logo">
                            </div>
                            <h1 class="auth-card-title">Cổng Thông Tin Đối Tác</h1>
                            <p class="auth-card-sub">Đăng nhập tài khoản để quản lý lượt giới thiệu & hoa hồng thụ hưởng</p>
                        </div>

                        <div class="auth-tabs">
                            <button type="button" id="tabBtnLogin" class="auth-tab-btn active" onclick="switchAuthTab('login')">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập
                            </button>
                            <button type="button" id="tabBtnRegister" class="auth-tab-btn" onclick="switchAuthTab('register')">
                                <i class="fa-solid fa-user-plus"></i> Đăng Ký Mới
                            </button>
                        </div>

                        <!-- FORM ĐĂNG NHẬP -->
                        <form id="formPortalLogin" onsubmit="handlePartnerLogin(event)">
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-circle-user"></i> Tên Đăng Nhập:</label>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-mobile-screen-button auth-input-icon"></i>
                                    <input type="text" name="login_id" class="auth-input has-icon" required placeholder="Số điện thoại hoặc email đối tác">
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <div class="auth-label-row">
                                    <label class="auth-label"><i class="fa-solid fa-lock"></i> Mật Khẩu Đăng Nhập:</label>
                                    <a href="tel:02873058999" class="auth-forgot-link" title="Gọi hotline hỗ trợ cấp lại"><i class="fa-solid fa-circle-question"></i> Quên mật khẩu?</a>
                                </div>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-key auth-input-icon"></i>
                                    <input type="password" id="inputPortalPassword" name="password" class="auth-input has-icon has-eye" required placeholder="Nhập mật khẩu tài khoản">
                                    <button type="button" class="btn-toggle-password" onclick="togglePortalPassword('inputPortalPassword', this)" title="Ẩn / Hiện mật khẩu">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="auth-security-badge">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Hệ thống bảo mật dữ liệu đối tác theo tiêu chuẩn mã hóa SSL 256-bit.</span>
                            </div>

                            <button type="submit" id="btnSubmitLogin" class="btn-auth-submit">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> ĐĂNG NHẬP CỔNG ĐỐI TÁC
                            </button>
                        </form>

                        <!-- FORM ĐĂNG KÝ TRỰC TIẾP -->
                        <form id="formPortalRegister" style="display: none;" onsubmit="handlePortalRegister(event)">
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-user"></i> Họ và Tên Đối Tác (*):</label>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-id-card auth-input-icon"></i>
                                    <input type="text" name="name" class="auth-input has-icon" required placeholder="Vd: Nguyễn Thu Trang">
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-phone"></i> Số Điện Thoại (Tên đăng nhập) (*):</label>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-phone auth-input-icon"></i>
                                    <input type="tel" name="phone" class="auth-input has-icon" required placeholder="Vd: 0912345678">
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-envelope"></i> Email Nhận Kích Hoạt (*):</label>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-envelope auth-input-icon"></i>
                                    <input type="email" name="email" class="auth-input has-icon" required placeholder="Vd: trang@gmail.com">
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <div class="auth-email-security-note">
                                    <i class="fa-solid fa-shield-check"></i>
                                    <div>
                                        <strong>Kích hoạt & Mật khẩu:</strong> Thông tin đăng nhập và mật khẩu khởi tạo sẽ được gửi bảo mật vào Email của Quý đối tác sau khi Ban Quản Trị xét duyệt trong 24h.
                                    </div>
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-hashtag"></i> Kênh Truyền Thông / Mạng Xã Hội:</label>
                                <div class="auth-input-wrapper">
                                    <i class="fa-solid fa-share-nodes auth-input-icon"></i>
                                    <input type="text" name="channel_url" class="auth-input has-icon" placeholder="Vd: tiktok.com/@trang hoặc facebook.com/...">
                                </div>
                            </div>
                            <div class="auth-input-group">
                                <label class="auth-label"><i class="fa-solid fa-building-columns"></i> Tài Khoản Ngân Hàng Nhận Hoa Hồng:</label>
                                <input type="text" name="bank_name" class="auth-input" placeholder="Tên ngân hàng (Vd: ACB, Vietcombank...)">
                                <input type="text" name="bank_account" class="auth-input" placeholder="Số tài khoản (STK)" style="margin-top: 8px;">
                                <input type="text" name="bank_owner" class="auth-input" placeholder="Tên chủ tài khoản (In hoa không dấu)" style="margin-top: 8px; text-transform: uppercase;">
                            </div>
                            <button type="submit" id="btnSubmitRegister" class="btn-auth-submit">
                                <i class="fa-solid fa-paper-plane"></i> GỬI HỒ SƠ ĐĂNG KÝ DUYỆT
                            </button>
                        </form>

                        <div class="auth-card-footer">
                            Cần hỗ trợ gấp? Hotline Ban Quản Trị: <a href="tel:02873058999" class="auth-hotline-link">028 7305 8999</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- ══════════════════════════════════════════════════════════ -->
            <!-- AUTHENTICATED DASHBOARD -->
            <!-- ══════════════════════════════════════════════════════════ -->

            <!-- HERO GREETING BANNER -->
            <section class="hero-banner">
                <div>
                    <h1>
                        <span>Xin chào, <?php echo esc_html($kol['name']); ?>!</span>
                        <span class="kol-ref-tag"><i class="fa-solid fa-tag"></i> REF: <?php echo esc_html($kol['ref_code']); ?></span>
                    </h1>
                    <p>
                        Chào mừng bạn đến với Cổng Thống Kê Đối Tác Nha Khoa Flora. Toàn bộ lượt click, đơn đặt và hoa hồng được đồng bộ tự động và minh bạch.
                    </p>
                </div>
                <div>
                    <span class="hero-badge-rate">
                        <i class="fa-solid fa-percent"></i> Mức Hoa Hồng: <?php echo esc_html($comm_text); ?>
                    </span>
                </div>
            </section>

            <!-- 4 KPI SUMMARY CARDS (DÒNG TIỀN HOA HỒNG CHI TIẾT - PHONG CÁCH TỐI GIẢN CHUẨN THỤY SĨ) -->
            <section class="kpi-grid">
                <!-- 1. HOA HỒNG XÁC THỰC -->
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Hoa Hồng Xác Thực</span>
                        <div class="kpi-icon-wrap" title="Đơn hàng đã thanh toán thành công">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </div>
                    </div>
                    <div class="kpi-number"><?php echo number_format($financial_stats['confirmed_commission'], 0, ',', '.'); ?><span class="kpi-unit">đ</span></div>
                    <div class="kpi-sub">Đã xác nhận từ <strong><?php echo $financial_stats['confirmed_orders']; ?> đơn</strong> thanh toán thành công</div>
                </div>

                <!-- 2. HOA HỒNG DỰ KIẾN -->
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Hoa Hồng Dự Kiến</span>
                        <div class="kpi-icon-wrap" title="Đang chờ đối soát sao kê">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        </div>
                    </div>
                    <div class="kpi-number"><?php echo number_format($financial_stats['pending_commission'], 0, ',', '.'); ?><span class="kpi-unit">đ</span></div>
                    <div class="kpi-sub">Từ <strong><?php echo $financial_stats['pending_orders']; ?> đơn</strong> đang chờ đối soát sao kê</div>
                </div>

                <!-- 3. VÍ KHẢ DỤNG & RÚT TIỀN -->
                <div class="kpi-card" style="border-color: #cbd5e1;">
                    <div class="kpi-header">
                        <span class="kpi-title" style="color: #0033a3;">Ví Khả Dụng Để Rút</span>
                        <div class="kpi-icon-wrap" style="background: #eff6ff; border-color: #bfdbfe; color: #0033a3;" title="Số dư có thể rút ngay">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 12V8H6a2 2 0 0 1-2-2c0-1.1.9-2 2-2h12v4"></path><path d="M4 6v12c0 1.1.9 2 2 2h14v-4"></path><circle cx="18" cy="14" r="1"></circle></svg>
                        </div>
                    </div>
                    <div class="kpi-number" style="color: #0033a3;"><?php echo number_format($financial_stats['available_balance'], 0, ',', '.'); ?><span class="kpi-unit" style="color: #0033a3;">đ</span></div>
                    <div class="kpi-sub" style="margin-bottom: 6px;">
                        Đã chi trả: <?php echo number_format($financial_stats['paid_commission'], 0, ',', '.'); ?>đ
                        <?php if ($financial_stats['requested_payout'] > 0): ?>
                            <span style="color: #64748b; font-weight: 600;"> • Đang chờ chuyển: <strong style="color: #0f172a;"><?php echo number_format($financial_stats['requested_payout'], 0, ',', '.'); ?>đ</strong></span>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-action-withdraw <?php echo ($financial_stats['available_balance'] < 100000) ? 'is-disabled' : ''; ?>" onclick="openWithdrawModal()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -2px;"><path d="M17 1l4 4-4 4"></path><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><path d="M7 23l-4-4 4-4"></path><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg>
                        GỬI ĐƠN RÚT TIỀN
                    </button>
                </div>

                <!-- 4. HIỆU SUẤT TIẾP THỊ -->
                <div class="kpi-card">
                    <div class="kpi-header">
                        <span class="kpi-title">Hiệu Suất Tiếp Thị</span>
                        <div class="kpi-icon-wrap" title="Lượt truy cập & chuyển đổi">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        </div>
                    </div>
                    <div class="kpi-number"><?php echo number_format($kol['total_clicks']); ?><span class="kpi-unit" style="font-size: 0.85rem;">click</span></div>
                    <div class="kpi-sub">
                        <?php echo $financial_stats['confirmed_orders']; ?>/<?php echo $financial_stats['total_orders']; ?> đơn hoàn tất • Doanh thu: <strong><?php echo number_format($financial_stats['total_revenue'], 0, ',', '.'); ?>đ</strong>
                    </div>
                </div>
            </section>

            <!-- REF LINK & QR BOX -->
            <section class="ref-box-card">
                <div>
                    <h3 style="color: var(--flora-primary); font-size: 1.15rem; font-weight: 800; margin-bottom: 6px;">
                        <i class="fa-solid fa-link"></i> Link Giới Thiệu Độc Quyền Của Bạn
                    </h3>
                    <p style="color: #64748b; font-size: 0.9rem;">
                        Chia sẻ liên kết này trên mạng xã hội (Facebook, TikTok, Instagram, YouTube). Khi khách hàng đặt mua gói khám hoặc đăng ký từ link này, hệ thống sẽ tự động đối soát và cộng hoa hồng cho bạn ngay khi đơn được thanh toán thành công.
                    </p>
                    <div class="ref-input-group">
                        <input type="text" id="kolRefUrl" readonly value="<?php echo esc_url($ref_url); ?>" class="ref-input">
                        <button type="button" class="btn-copy" onclick="copyRefLink()">
                            <i class="fa-regular fa-copy"></i> Sao Chép Link
                        </button>
                    </div>

                    <!-- THÔNG TIN NGÂN HÀNG HIỆN TẠI -->
                    <div style="margin-top: 18px; padding-top: 14px; border-top: 1px dashed #e2e8f0; font-size: 0.85rem; color: #475569; display: flex; align-items: center; justify-content: space-between; flex-wrap: gap; gap: 8px;">
                        <div>
                            <i class="fa-solid fa-building-columns" style="color: #0033a3;"></i> 
                            STK nhận hoa hồng: <strong><?php echo esc_html($kol['bank_name'] ?: 'Chưa cập nhật'); ?> - <?php echo esc_html($kol['bank_account'] ?: 'Chưa cập nhật'); ?></strong> (<?php echo esc_html($kol['bank_owner']); ?>)
                        </div>
                        <button type="button" onclick="openProfileModal()" style="background: none; border: none; color: #0493f1; font-weight: 700; cursor: pointer; text-decoration: underline; font-size: 0.85rem;">
                            Thay đổi STK &rarr;
                        </button>
                    </div>
                </div>
                <div class="qr-box">
                    <img src="<?php echo esc_url($qr_code_url); ?>" alt="QR Code REF" title="Quét QR để mở trang giới thiệu">
                    <a href="<?php echo esc_url($qr_code_url); ?>" download="Flora_QR_<?php echo esc_attr($kol['ref_code']); ?>.png" style="font-size: 0.78rem; font-weight: 700; color: var(--flora-primary); text-decoration: none;">
                        <i class="fa-solid fa-download"></i> Tải Mã QR Về Máy
                    </a>
                </div>
            </section>

            <!-- ZALO BOT NOTIFICATION INTEGRATION (2 CHIỀU) -->
            <section class="zalo-bot-card">
                <div class="zalo-card-header">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; background: #e0f2fe; color: #0284c7; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <h3 style="color: var(--flora-primary); font-size: 1.15rem; font-weight: 800; margin: 0;">
                                Thông Báo Zalo Bot Tự Động (2 Chiều)
                            </h3>
                            <p style="color: #64748b; font-size: 0.84rem; margin: 0;">
                                Nhận tin nhắn thông báo tức thì khi đơn hàng được duyệt thanh toán & kết quả phiếu rút tiền hoa hồng
                            </p>
                        </div>
                    </div>
                    <div>
                        <?php if (!empty($kol['zalo_chat_id'])): ?>
                            <span class="zalo-badge-connected">
                                <i class="fa-solid fa-circle-check"></i> ĐÃ LIÊN KẾT ZALO BOT
                            </span>
                        <?php else: ?>
                            <span class="zalo-badge-disconnected">
                                <i class="fa-solid fa-circle-exclamation"></i> CHƯA LIÊN KẾT ZALO BOT
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($kol['zalo_chat_id'])): ?>
                    <!-- KHI ĐÃ LIÊN KẾT -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <div style="font-size: 0.88rem; color: #0f172a; font-weight: 700;">
                                    Tài khoản Zalo nhận thông báo: <code style="color: #0033a3; background: #eff6ff; padding: 2px 8px; border-radius: 6px;"><?php echo esc_html($kol['zalo_chat_id']); ?></code>
                                </div>
                                <div style="font-size: 0.8rem; color: #16a34a; margin-top: 4px;">
                                    <i class="fa-solid fa-shield-check"></i> Đang hoạt động: Tự động rung chuông khi có đơn được duyệt thanh toán thành công và khi nhận tiền chuyển khoản.
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <button type="button" class="zalo-action-btn btn-primary-blue" onclick="handleTestZaloBot()">
                                    <i class="fa-solid fa-paper-plane"></i> Gửi Thử Thông Báo Test
                                </button>
                                <button type="button" class="zalo-action-btn" onclick="openZaloModal()">
                                    <i class="fa-solid fa-gear"></i> Đổi Zalo
                                </button>
                                <button type="button" class="zalo-action-btn" onclick="handleUnlinkZaloBot()" style="color: #dc2626; border-color: #fecaca;">
                                    <i class="fa-solid fa-link-slash"></i> Hủy
                                </button>
                            </div>
                        </div>

                        <!-- CÚ PHÁP TRA CỨU 2 CHIỀU -->
                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #cbd5e1;">
                            <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 8px;">
                                <i class="fa-solid fa-terminal"></i> Tra Cứu Chủ Động Qua Zalo Bot (Nhắn tin trực tiếp cho Bot):
                            </div>
                            <div class="zalo-commands-grid">
                                <div class="zalo-cmd-item">
                                    Soạn <span class="zalo-cmd-code">SODU</span> &rarr; Xem số dư khả dụng, chờ duyệt & tổng hoa hồng.
                                </div>
                                <div class="zalo-cmd-item">
                                    Soạn <span class="zalo-cmd-code">LINK</span> &rarr; Lấy lại đường link giới thiệu & mã voucher riêng.
                                </div>
                                <div class="zalo-cmd-item">
                                    Soạn <span class="zalo-cmd-code">HOTRO</span> &rarr; Kết nối đường dây nóng chuyên viên hỗ trợ Flora.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- KHI CHƯA LIÊN KẾT -->
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px;">
                        <p style="font-size: 0.88rem; color: #334155; margin-bottom: 14px; line-height: 1.5;">
                            Liên kết Zalo Bot giúp Quý đối tác nhận thông báo ngay trong tích tắc khi:
                            <br>• <strong>Đơn hàng từ link giới thiệu ĐƯỢC DUYỆT THÀNH CÔNG</strong> (kèm chi tiết gói khám và số tiền hoa hồng).
                            <br>• <strong>Kết quả chuyển khoản ngân hàng</strong> khi bạn gửi phiếu yêu cầu rút tiền hoa hồng.
                        </p>

                        <!-- KHỐI 1 CHẠM & CÚ PHÁP -->
                        <div class="zalo-syntax-box" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 14px 18px;">
                            <div>
                                <div style="font-size: 0.78rem; font-weight: 800; color: #0033a3; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-bolt" style="color: #f59e0b;"></i> KẾT NỐI 1-CHẠM CỰC NHANH:
                                </div>
                                <div style="font-size: 0.88rem; color: #1e293b; line-height: 1.5;">
                                    Ấn nút <strong>"Mở Zalo & Kết Nối"</strong> &rarr; Zalo mở ra, bạn chỉ cần <strong>Dán</strong> hoặc gõ:
                                    <code id="syntaxRefCode" style="font-size: 1.05rem; font-weight: 800; color: #0033a3; background: #ffffff; padding: 3px 8px; border-radius: 6px; margin: 0 4px; border: 1.5px solid #93c5fd;">/<?php echo esc_html($kol['ref_code']); ?></code>
                                    (hoặc <code style="font-size: 0.88rem; font-weight: 700; color: #475569; background: #ffffff; padding: 2px 6px; border-radius: 4px; border: 1px solid #cbd5e1;"><?php echo esc_html($kol['ref_code']); ?></code>) gửi cho Bot là xong ngay!
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                <button type="button" class="btn-zalo-one-touch" onclick="openZaloOneTouch('<?php echo esc_js($kol['ref_code']); ?>')">
                                    <i class="fa-solid fa-comment-dots" style="font-size: 1.05rem;"></i> Mở Zalo & Kết Nối Ngay
                                </button>
                                <button type="button" class="zalo-action-btn" onclick="copyZaloSyntax('/<?php echo esc_js($kol['ref_code']); ?>')">
                                    <i class="fa-regular fa-copy"></i> Sao Chép Mã
                                </button>
                                <button type="button" class="zalo-action-btn" onclick="openZaloModal()" title="Dành cho đối tác muốn tự dán Chat ID">
                                    <i class="fa-solid fa-pen-to-square"></i> Nhập Chat ID
                                </button>
                            </div>
                        </div>

                        <!-- BANNER ĐANG LẮNG NGHE KẾT NỐI TỰ ĐỘNG (ẨN MẶC ĐỊNH, HIỆN KHI BẤM 1 CHẠM) -->
                        <div id="zaloConnectingBanner" style="display: none; margin-top: 14px; background: #f0fdf4; border: 1.5px dashed #4ade80; border-radius: 10px; padding: 14px 18px; align-items: center; justify-content: space-between; gap: 14px;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div class="zalo-spinner-ring"></div>
                                <div>
                                    <div style="font-size: 0.9rem; font-weight: 800; color: #166534; display: flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-satellite-dish fa-fade"></i> Đang chờ xác nhận từ Zalo Bot...
                                    </div>
                                    <div style="font-size: 0.82rem; color: #15803d; margin-top: 3px;">
                                        Đã mở Zalo & sao chép sẵn mã <strong>/<?php echo esc_html($kol['ref_code']); ?></strong> vào clipboard. Bạn chỉ cần gửi tin nhắn này cho Bot, trang sẽ tự động kết nối ngay!
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="cancelZaloAutoCheck()" style="background: #ffffff; border: 1px solid #86efac; color: #166534; font-size: 0.78rem; font-weight: 700; padding: 6px 12px; border-radius: 6px; cursor: pointer; white-space: nowrap;">
                                <i class="fa-solid fa-xmark"></i> Hủy Chờ
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- ORDERS LIST -->
            <section class="data-card">
                <div class="data-header">
                    <h3><i class="fa-solid fa-list-check"></i> Danh Sách Khách Hàng & Đơn Hàng Từ Bạn (<?php echo count($orders); ?>)</h3>
                    <span style="font-size: 0.82rem; color: #64748b;">
                        <i class="fa-solid fa-circle-info"></i> Thông tin khách hàng được mã hóa bảo mật theo tiêu chuẩn Y Khoa
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mã Đơn</th>
                                <th>Khách Hàng</th>
                                <th>Gói Dịch Vụ</th>
                                <th>Giá Trị Đơn</th>
                                <th>Trạng Thái</th>
                                <th>Hoa Hồng Bạn Nhận</th>
                                <th>Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                        <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                                        Chưa có đơn hàng nào được ghi nhận từ liên kết của bạn. Hãy chia sẻ link để bắt đầu nhận hoa hồng!
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): 
                                    $is_paid = in_array($o['payment_status'], array('paid', 'completed')) || ($o['commission_status'] === 'confirmed');
                                    $masked_name = function_exists('flora_affiliate_mask_name') ? flora_affiliate_mask_name($o['customer_name']) : $o['customer_name'];
                                    $masked_phone = function_exists('flora_affiliate_mask_phone') ? flora_affiliate_mask_phone($o['customer_phone']) : $o['customer_phone'];
                                    $comm_amount = (int)$o['commission_amount'];
                                ?>
                                <tr>
                                    <td style="white-space: nowrap;"><strong style="color: var(--flora-primary); font-family: monospace; font-size: 0.88rem;"><?php echo esc_html($o['order_code']); ?></strong></td>
                                    <td style="white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a; line-height: 1.3;"><?php echo esc_html($masked_name); ?></div>
                                        <div style="color: #64748b; font-size: 0.78rem;"><?php echo esc_html($masked_phone); ?></div>
                                    </td>
                                    <td style="max-width: 250px; font-weight: 600; line-height: 1.35;"><?php echo esc_html($o['package_name']); ?></td>
                                    <td style="white-space: nowrap;"><strong style="color: var(--flora-dark); font-size: 0.95rem;"><?php echo number_format($o['final_amount'], 0, ',', '.'); ?>đ</strong></td>
                                    <td style="white-space: nowrap; text-align: center;">
                                        <?php if ($is_paid): ?>
                                            <span class="status-badge-paid"><i class="fa-solid fa-circle-check"></i> ĐÃ THANH TOÁN</span>
                                        <?php else: ?>
                                            <span class="status-badge-pending"><i class="fa-solid fa-clock"></i> CHỜ THANH TOÁN</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <?php if ($is_paid): ?>
                                            <strong style="color: var(--flora-emerald); font-size: 0.96rem;">+<?php echo number_format($comm_amount, 0, ',', '.'); ?>đ</strong>
                                        <?php else: ?>
                                            <span style="color: #94a3b8; font-size: 0.84rem;">Tạm tính: +<?php echo number_format($comm_amount, 0, ',', '.'); ?>đ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="white-space: nowrap; color: #64748b; font-size: 0.82rem;"><?php echo esc_html(date('d/m/Y H:i', strtotime($o['created_at']))); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- PAYOUT & WITHDRAWAL HISTORY -->
            <section class="data-card">
                <div class="data-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <h3><i class="fa-solid fa-receipt"></i> Lịch Sử Đơn Rút Tiền & Quyết Toán Hoa Hồng</h3>
                    <button type="button" class="btn-action-withdraw <?php echo ($financial_stats['available_balance'] < 100000) ? 'is-disabled' : ''; ?>" style="margin-top: 0; padding: 7px 14px; font-size: 0.82rem;" onclick="openWithdrawModal()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px; vertical-align: -2px;"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Tạo Đơn Rút Tiền
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Mã Đơn Rút</th>
                                <th>Thời Gian</th>
                                <th>Số Tiền</th>
                                <th>Tài Khoản Thụ Hưởng</th>
                                <th>Trạng Thái</th>
                                <th>Mã GD / Ghi Chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payouts)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 36px 20px;">
                                        <div style="width: 48px; height: 48px; background: #f1f5f9; color: #94a3b8; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem; margin-bottom: 10px;">
                                            <i class="fa-solid fa-inbox"></i>
                                        </div>
                                        <p style="margin: 0; font-size: 0.92rem; color: #64748b;">Bạn chưa có yêu cầu rút tiền nào.</p>
                                        <p style="margin: 6px 0 0; font-size: 0.82rem; color: #94a3b8;">Khi tích lũy từ 100.000đ hoa hồng xác thực, bạn có thể bấm <strong>"Gửi Đơn Rút Tiền"</strong> bất cứ lúc nào!</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payouts as $p): 
                                    $p_status = !empty($p['status']) ? $p['status'] : 'completed';
                                    $p_code = !empty($p['payout_code']) ? $p['payout_code'] : ('WD-' . $p['id']);
                                    $p_time = !empty($p['requested_at']) ? $p['requested_at'] : $p['created_at'];
                                    $bank_info = (!empty($p['bank_name']) ? $p['bank_name'] : $kol['bank_name']) . ' - ' . (!empty($p['bank_account']) ? $p['bank_account'] : $kol['bank_account']);
                                ?>
                                <tr>
                                    <td style="white-space: nowrap;"><strong style="color: #0033a3; font-family: monospace; font-size: 0.88rem;"><?php echo esc_html($p_code); ?></strong></td>
                                    <td style="white-space: nowrap; color: #64748b; font-size: 0.82rem;"><?php echo esc_html(date('d/m/Y H:i', strtotime($p_time))); ?></td>
                                    <td style="white-space: nowrap;"><strong style="color: var(--flora-emerald); font-size: 0.96rem;"><?php echo number_format($p['amount'], 0, ',', '.'); ?> VNĐ</strong></td>
                                    <td style="white-space: nowrap; font-size: 0.84rem; color: #0f172a;"><?php echo esc_html($bank_info); ?></td>
                                    <td style="white-space: nowrap; text-align: center;">
                                        <?php if ($p_status === 'requested' || $p_status === 'pending'): ?>
                                            <span style="background: #fef3c7; color: #b45309; padding: 5px 12px; border-radius: 9999px; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                                <i class="fa-solid fa-clock-rotate-left"></i> Chờ chuyển khoản
                                            </span>
                                        <?php elseif ($p_status === 'completed'): ?>
                                            <span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 9999px; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                                <i class="fa-solid fa-circle-check"></i> Đã chuyển tiền
                                            </span>
                                        <?php elseif ($p_status === 'rejected'): ?>
                                            <span style="background: #fee2e2; color: #b91c1c; padding: 5px 12px; border-radius: 9999px; font-weight: 700; font-size: 0.76rem; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;">
                                                <i class="fa-solid fa-circle-xmark"></i> Bị từ chối
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color: #475569; font-size: 0.84rem;">
                                        <?php if (!empty($p['transaction_reference'])): ?>
                                            <code style="color: #0033a3; font-weight: 700; background: #eff6ff; padding: 2px 6px; border-radius: 4px; white-space: nowrap;"><?php echo esc_html($p['transaction_reference']); ?></code><br>
                                        <?php endif; ?>
                                        <span><?php echo esc_html($p['notes'] ?: 'Rút hoa hồng đối tác'); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- MODAL: GỬI ĐƠN YÊU CẦU RÚT TIỀN -->
            <div id="modalWithdrawForm" class="portal-modal">
                <div class="portal-modal-content" style="max-width: 520px;">
                    <button type="button" class="portal-modal-close" onclick="closeWithdrawModal()">&times;</button>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 46px; height: 46px; background: #f0fdf4; color: #16a34a; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                        </div>
                        <div>
                            <h3 style="color: #0033a3; font-size: 1.25rem; font-weight: 800; margin: 0;">Gửi Đơn Yêu Cầu Rút Tiền</h3>
                            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Chuyển hoa hồng về tài khoản ngân hàng chính chủ của bạn</p>
                        </div>
                    </div>

                    <!-- BOX THÔNG TIN SỐ DƯ -->
                    <div style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border: 1.5px solid #bbf7d0; border-radius: 14px; padding: 16px; margin: 16px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-size: 0.84rem; color: #166534; font-weight: 700; text-transform: uppercase;">Số Dư Khả Dụng Có Thể Rút:</span>
                            <span id="withdrawModalAvail" style="font-size: 1.35rem; font-weight: 900; color: #15803d;"><?php echo number_format($financial_stats['available_balance'], 0, ',', '.'); ?>đ</span>
                        </div>
                        <div style="font-size: 0.8rem; color: #0284c7; display: flex; align-items: center; gap: 6px;">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Hoa hồng dự kiến (đơn chờ duyệt): <strong><?php echo number_format($financial_stats['pending_commission'], 0, ',', '.'); ?>đ</strong></span>
                        </div>
                    </div>

                    <?php if ($financial_stats['available_balance'] < 100000): ?>
                        <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 12px; padding: 14px; margin-bottom: 16px;">
                            <div style="color: #b45309; font-weight: 800; font-size: 0.88rem; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-circle-exclamation"></i> Số Dư Chưa Đạt Mức Rút Tối Thiểu
                            </div>
                            <p style="font-size: 0.82rem; color: #78350f; margin: 0; line-height: 1.45;">
                                Số dư khả dụng hiện tại: <strong><?php echo number_format($financial_stats['available_balance'], 0, ',', '.'); ?>đ</strong> (mức rút tối thiểu là <strong>100.000 VNĐ</strong>).<br>
                                Bạn có <strong><?php echo number_format($financial_stats['pending_commission'], 0, ',', '.'); ?>đ</strong> hoa hồng từ các đơn đang chờ đối soát. Sau khi hoàn tất đối soát, số tiền sẽ được tự động cộng vào ví khả dụng để bạn rút!
                            </p>
                        </div>
                    <?php endif; ?>

                    <form id="formRequestWithdraw" onsubmit="handleRequestWithdraw(event)">
                        <input type="hidden" name="token" value="<?php echo esc_attr($kol['secret_token']); ?>">

                        <?php 
                        $input_min = 100000;
                        $input_max = max(100000, (int)$financial_stats['available_balance']);
                        $input_val = ($financial_stats['available_balance'] >= 100000) ? (int)$financial_stats['available_balance'] : 100000;
                        $can_withdraw = ($financial_stats['available_balance'] >= 100000) && !empty($kol['bank_account']);
                        ?>

                        <div class="auth-input-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                <label class="auth-label" style="margin-bottom: 0;">Số Tiền Muốn Rút (VNĐ) (*):</label>
                                <?php if ($financial_stats['available_balance'] >= 100000): ?>
                                <button type="button" onclick="fillWithdrawAll()" style="background: none; border: none; color: #0033a3; font-weight: 700; font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                                    Rút toàn bộ số dư
                                </button>
                                <?php endif; ?>
                            </div>
                            <input type="number" name="amount" id="withdrawAmountInput" class="auth-input" required min="<?php echo $input_min; ?>" max="<?php echo $input_max; ?>" step="1000" value="<?php echo $input_val; ?>" placeholder="Tối thiểu 100.000đ" <?php echo ($financial_stats['available_balance'] < 100000) ? 'readonly style="background: #f1f5f9;"' : ''; ?>>
                            <span style="font-size: 0.78rem; color: #64748b; margin-top: 4px; display: block;">Mức rút tối thiểu: 100.000 VNĐ.</span>
                        </div>

                        <!-- TÀI KHOẢN NGÂN HÀNG THỤ HƯỞNG -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-bottom: 14px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span style="font-size: 0.82rem; font-weight: 700; color: #0f172a;">Tài khoản nhận tiền:</span>
                                <button type="button" onclick="closeWithdrawModal(); openProfileModal();" style="background: none; border: none; color: #0284c7; font-size: 0.78rem; font-weight: 700; cursor: pointer; text-decoration: underline;">
                                    <i class="fa-solid fa-pen-to-square"></i> Đổi STK
                                </button>
                            </div>
                            <?php if (!empty($kol['bank_account']) && !empty($kol['bank_name'])): ?>
                                <div style="font-size: 0.88rem; color: #0f172a; line-height: 1.5;">
                                    <strong><?php echo esc_html($kol['bank_name']); ?></strong><br>
                                    STK: <strong style="color: #0033a3; font-family: monospace; font-size: 1rem;"><?php echo esc_html($kol['bank_account']); ?></strong><br>
                                    Chủ TK: <strong style="text-transform: uppercase;"><?php echo esc_html($kol['bank_owner']); ?></strong>
                                </div>
                            <?php else: ?>
                                <div style="color: #dc2626; font-size: 0.84rem; line-height: 1.4;">
                                    ⚠️ Bạn chưa cài đặt tài khoản ngân hàng thụ hưởng. Vui lòng bấm <strong>"Đổi STK"</strong> để cập nhật trước khi rút tiền!
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="auth-input-group">
                            <label class="auth-label">Ghi Chú Cho Kế Toán (Tùy chọn):</label>
                            <textarea name="notes" id="withdrawNotes" rows="2" class="auth-input" style="height: auto; padding: 10px;" placeholder="Vd: Rút hoa hồng chiến dịch tháng 10/2026..."></textarea>
                        </div>

                        <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 12px; margin-bottom: 18px; font-size: 0.8rem; color: #92400e; line-height: 1.4;">
                            <i class="fa-solid fa-shield-halved" style="color: #d97706;"></i> <strong>Quy trình xử lý:</strong> Sau khi bạn gửi yêu cầu, thông báo sẽ gửi ngay đến Bộ phận Kế toán Flora để đối soát. Tiền sẽ được chuyển khoản trực tiếp vào STK trên trong vòng <strong>24 - 48 giờ làm việc</strong>.
                        </div>

                        <button type="submit" id="btnSubmitWithdraw" class="btn-auth-submit" <?php echo (!$can_withdraw) ? 'disabled style="opacity: 0.65; cursor: not-allowed; background: #94a3b8;"' : ''; ?>>
                            <i class="fa-solid fa-paper-plane"></i> <?php echo ($financial_stats['available_balance'] < 100000) ? 'CHƯA ĐỦ ĐIỀU KIỆN RÚT (TỐI THIỂU 100.000đ)' : 'XÁC NHẬN GỬI YÊU CẦU RÚT TIỀN'; ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- MODAL: CẬP NHẬT TÀI KHOẢN NGÂN HÀNG & HỒ SƠ -->
            <div id="modalProfileEdit" class="portal-modal">
                <div class="portal-modal-content">
                    <button type="button" class="portal-modal-close" onclick="closeProfileModal()">&times;</button>
                    <h3 style="color: #0033a3; font-size: 1.25rem; font-weight: 800; margin: 0 0 6px; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-building-columns"></i> Cập Nhật Tài Khoản Ngân Hàng
                    </h3>
                    <p style="font-size: 0.84rem; color: #64748b; margin-bottom: 20px;">
                        Thông tin tài khoản ngân hàng dùng để Nha Khoa Flora chuyển khoản quyết toán hoa hồng định kỳ cho bạn.
                    </p>

                    <form id="formUpdatePartnerProfile" onsubmit="handleUpdateProfile(event)">
                        <input type="hidden" name="token" value="<?php echo esc_attr($kol['secret_token']); ?>">

                        <div class="auth-input-group">
                            <label class="auth-label">Tên Ngân Hàng Thụ Hưởng (*):</label>
                            <input type="text" name="bank_name" class="auth-input" required value="<?php echo esc_attr($kol['bank_name']); ?>" placeholder="Vd: ACB, Vietcombank, MB Bank, Techcombank...">
                        </div>

                        <div class="auth-input-group">
                            <label class="auth-label">Số Tài Khoản (STK) Ngân Hàng (*):</label>
                            <input type="text" name="bank_account" class="auth-input" required value="<?php echo esc_attr($kol['bank_account']); ?>" placeholder="Nhập số tài khoản ngân hàng chính xác">
                        </div>

                        <div class="auth-input-group">
                            <label class="auth-label">Tên Chủ Tài Khoản (In hoa không dấu) (*):</label>
                            <input type="text" name="bank_owner" class="auth-input" required value="<?php echo esc_attr($kol['bank_owner']); ?>" placeholder="Vd: NGUYEN THU TRANG" style="text-transform: uppercase;">
                        </div>

                        <div class="auth-input-group">
                            <label class="auth-label">Email Nhận Thông Báo Hoa Hồng:</label>
                            <input type="email" name="email" class="auth-input" value="<?php echo esc_attr($kol['email']); ?>" placeholder="Email nhận thông báo">
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; margin-top: 14px;">
                            <div style="font-weight: 700; font-size: 0.84rem; color: #0f172a; margin-bottom: 8px;">Đổi Mật Khẩu Đăng Nhập (Tùy chọn):</div>
                            <div class="auth-input-group" style="margin-bottom: 8px;">
                                <input type="password" name="new_password" class="auth-input" placeholder="Mật khẩu mới (để trống nếu không đổi)">
                            </div>
                            <div class="auth-input-group" style="margin-bottom: 0;">
                                <input type="password" name="current_password" class="auth-input" placeholder="Mật khẩu hiện tại của bạn">
                            </div>
                        </div>

                        <button type="submit" id="btnSaveProfile" class="btn-auth-submit" style="margin-top: 20px;">
                            <i class="fa-solid fa-floppy-disk"></i> LƯU THAY ĐỔI TÀI KHOẢN
                        </button>
                    </form>
                </div>
            </div>

            <!-- MODAL: KẾT NỐI / ĐỔI ZALO BOT CHAT ID -->
            <div id="modalZaloBotConnect" class="portal-modal">
                <div class="portal-modal-content" style="max-width: 500px;">
                    <button type="button" class="portal-modal-close" onclick="closeZaloModal()">&times;</button>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div style="width: 44px; height: 44px; background: #eff6ff; color: #0033a3; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                            <i class="fa-solid fa-link"></i>
                        </div>
                        <div>
                            <h3 style="color: #0033a3; font-size: 1.2rem; font-weight: 800; margin: 0;">Cài Đặt Zalo Bot Thông Báo</h3>
                            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Nhận tin báo đơn được duyệt và kết quả rút tiền</p>
                        </div>
                    </div>

                    <form id="formUpdateZaloBot" onsubmit="handleSaveZaloChatId(event)">
                        <input type="hidden" name="token" value="<?php echo esc_attr($kol['secret_token']); ?>">

                        <div class="auth-input-group" style="margin-top: 16px;">
                            <label class="auth-label">Chat ID Zalo Của Bạn (*):</label>
                            <input type="text" name="chat_id" id="inputZaloChatId" class="auth-input" required value="<?php echo esc_attr($kol['zalo_chat_id']); ?>" placeholder="Vd: 394484717784... hoặc zgr-b5a415...">
                            <span style="font-size: 0.78rem; color: #64748b; margin-top: 6px; display: block; line-height: 1.45;">
                                💡 <strong>Cách lấy Chat ID:</strong> Bạn chỉ cần mở Zalo Bot Flora và gõ <code>/chatid</code> (Bot sẽ gửi ngay ID của bạn), hoặc đơn giản hơn là gửi trực tiếp <code>LINK <?php echo esc_html($kol['ref_code']); ?></code> cho Bot.
                            </span>
                        </div>

                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px; margin: 16px 0; font-size: 0.8rem; color: #166534; line-height: 1.45;">
                            <i class="fa-solid fa-circle-check"></i> Sau khi lưu, hệ thống sẽ tự động gửi 1 tin nhắn chào mừng và xác nhận kết nối vào Zalo của bạn.
                        </div>

                        <button type="submit" id="btnSaveZaloChatId" class="btn-auth-submit">
                            <i class="fa-solid fa-floppy-disk"></i> LƯU & XÁC NHẬN KẾT NỐI
                        </button>
                    </form>
                </div>
            </div>

            <!-- FOOTER INFO -->
            <footer style="text-align: center; color: #94a3b8; font-size: 0.85rem; padding: 20px 0;">
                <p>Mạng Lưới Tiếp Thị Liên Kết & Đối Tác Nha Khoa Flora - Tiêu Chuẩn Thụy Sĩ</p>
                <p style="margin-top: 4px;">Hotline đối tác 24/7: <strong>028 7305 8999</strong> | Địa chỉ: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, TP.HCM</p>
            </footer>
        <?php endif; ?>
    </div>

    <script>
        const AJAX_URL = '<?php echo admin_url('admin-ajax.php'); ?>';

        function copyRefLink() {
            const input = document.getElementById('kolRefUrl');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                alert('Đã sao chép link giới thiệu của bạn: ' + input.value);
            }).catch(() => {
                prompt('Sao chép liên kết:', input.value);
            });
        }

        function togglePortalPassword(id, btn) {
            const el = document.getElementById(id);
            if (!el) return;
            const isPass = el.type === 'password';
            el.type = isPass ? 'text' : 'password';
            btn.innerHTML = isPass ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
        }

        function switchAuthTab(tab) {
            const btnLogin = document.getElementById('tabBtnLogin');
            const btnReg   = document.getElementById('tabBtnRegister');
            const formLogin = document.getElementById('formPortalLogin');
            const formReg   = document.getElementById('formPortalRegister');

            if (tab === 'login') {
                btnLogin.classList.add('active');
                btnReg.classList.remove('active');
                formLogin.style.display = 'block';
                formReg.style.display = 'none';
            } else {
                btnReg.classList.add('active');
                btnLogin.classList.remove('active');
                formReg.style.display = 'block';
                formLogin.style.display = 'none';
            }
        }

        async function handlePartnerLogin(e) {
            e.preventDefault();
            const form = document.getElementById('formPortalLogin');
            const btn = document.getElementById('btnSubmitLogin');
            const origText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang đăng nhập...';

            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_partner_login');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    if (data.data && data.data.token) {
                        document.cookie = "flora_partner_token=" + encodeURIComponent(data.data.token) + "; path=/; max-age=" + (30*24*60*60);
                        localStorage.setItem('flora_partner_token', data.data.token);
                    }
                    window.location.href = data.data.redirect_url || '<?php echo home_url('/doi-tac/'); ?>';
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Đăng nhập không thành công'));
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }

        async function handlePortalRegister(e) {
            e.preventDefault();
            const form = document.getElementById('formPortalRegister');
            const btn = document.getElementById('btnSubmitRegister');
            const origText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi hồ sơ...';

            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_partner_register');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message || 'Đăng ký thành công! Ban Quản Trị sẽ duyệt và gửi email kích hoạt trong 24h.');
                    switchAuthTab('login');
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể gửi đăng ký'));
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }

        async function handlePartnerLogout() {
            if (!confirm('Bạn có chắc chắn muốn đăng xuất khỏi Cổng Đối Tác?')) return;
            document.cookie = "flora_partner_token=; path=/; max-age=0";
            localStorage.removeItem('flora_partner_token');
            try {
                const formData = new FormData();
                formData.append('action', 'flora_ajax_partner_logout');
                await fetch(AJAX_URL, { method: 'POST', body: formData });
            } catch(e) {}
            window.location.href = '<?php echo home_url('/doi-tac/'); ?>';
        }

        function openProfileModal() {
            const modal = document.getElementById('modalProfileEdit');
            if (modal) modal.style.display = 'flex';
        }

        function closeProfileModal() {
            const modal = document.getElementById('modalProfileEdit');
            if (modal) modal.style.display = 'none';
        }

        async function handleUpdateProfile(e) {
            e.preventDefault();
            const form = document.getElementById('formUpdatePartnerProfile');
            const btn = document.getElementById('btnSaveProfile');
            const origText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang lưu thay đổi...';

            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_partner_update_profile');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message || 'Cập nhật tài khoản ngân hàng thành công!');
                    closeProfileModal();
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể lưu thay đổi'));
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }

        // ─── XỬ LÝ YÊU CẦU RÚT TIỀN HOA HỒNG ───
        function openWithdrawModal() {
            const modal = document.getElementById('modalWithdrawForm');
            if (modal) modal.style.display = 'flex';
        }

        function closeWithdrawModal() {
            const modal = document.getElementById('modalWithdrawForm');
            if (modal) modal.style.display = 'none';
        }

        function fillWithdrawAll() {
            const inp = document.getElementById('withdrawAmountInput');
            if (inp) {
                const maxVal = inp.getAttribute('max');
                if (maxVal) inp.value = maxVal;
            }
        }

        async function handleRequestWithdraw(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitWithdraw');
            const origHtml = btn ? btn.innerHTML : '';
            const form = document.getElementById('formRequestWithdraw');
            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_partner_request_withdrawal');

            const amount = Number(formData.get('amount'));
            if (!amount || amount < 100000) {
                alert('⚠️ Số tiền yêu cầu rút tối thiểu là 100.000 VNĐ.');
                return;
            }

            if (!confirm('Bạn có chắc chắn muốn gửi yêu cầu rút ' + amount.toLocaleString('vi-VN') + ' VNĐ về tài khoản ngân hàng thụ hưởng?')) {
                return;
            }

            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi yêu cầu...';
            }

            try {
                const response = await fetch(AJAX_URL, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.success) {
                    alert('🎉 ' + res.data.message);
                    closeWithdrawModal();
                    window.location.reload();
                } else {
                    alert('⚠️ ' + (res.data ? res.data.message : 'Có lỗi khi gửi yêu cầu. Vui lòng thử lại!'));
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    }
                }
            } catch(err) {
                alert('❌ Lỗi kết nối máy chủ. Vui lòng thử lại sau!');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        // ─── XỬ LÝ LIÊN KẾT & TEST ZALO BOT ───
        let zaloCheckTimer = null;
        let zaloCheckAttempts = 0;

        function openZaloOneTouch(refCode) {
            const command = '/' + refCode;
            
            // 1. Copy cú pháp vào bộ nhớ tạm
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(command).catch(() => {});
            }

            // 2. Mở cửa sổ Zalo Bot Flora
            window.open('https://zalo.me/bot.yyusEkXl', '_blank');

            // 3. Hiển thị banner trạng thái chờ
            const banner = document.getElementById('zaloConnectingBanner');
            if (banner) {
                banner.style.display = 'flex';
                banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            // 4. Khởi động polling kiểm tra trạng thái tự động (mỗi 2.5s)
            startZaloPolling();
        }

        function startZaloPolling() {
            if (zaloCheckTimer) clearInterval(zaloCheckTimer);
            zaloCheckAttempts = 0;

            zaloCheckTimer = setInterval(async () => {
                zaloCheckAttempts++;
                // Giới hạn kiểm tra 120 lần (~5 phút)
                if (zaloCheckAttempts > 120) {
                    cancelZaloAutoCheck();
                    return;
                }

                try {
                    const token = '<?php echo esc_js($kol['secret_token'] ?? ''); ?>';
                    if (!token) return;

                    const formData = new FormData();
                    formData.append('action', 'flora_ajax_partner_check_zalo_status');
                    formData.append('token', token);

                    const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                    const data = await res.json();

                    if (data && data.success && data.data && data.data.is_linked) {
                        clearInterval(zaloCheckTimer);
                        zaloCheckTimer = null;
                        alert('🎉 CHÚC MỪNG!\n\nTài khoản Zalo của bạn (' + data.data.zalo_chat_id + ') đã được liên kết thành công với Cổng Đối Tác Flora!');
                        window.location.reload();
                    }
                } catch(e) {
                    console.warn('Lỗi kiểm tra kết nối Zalo:', e);
                }
            }, 2500);
        }

        function cancelZaloAutoCheck() {
            if (zaloCheckTimer) {
                clearInterval(zaloCheckTimer);
                zaloCheckTimer = null;
            }
            const banner = document.getElementById('zaloConnectingBanner');
            if (banner) banner.style.display = 'none';
        }

        function openZaloModal() {
            const modal = document.getElementById('modalZaloBotConnect');
            if (modal) modal.style.display = 'flex';
        }

        function closeZaloModal() {
            const modal = document.getElementById('modalZaloBotConnect');
            if (modal) modal.style.display = 'none';
        }

        function copyZaloSyntax(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Đã sao chép mã: "' + text + '"!\n\nBây giờ bạn chỉ cần mở Zalo Bot Flora và dán gửi tin nhắn này để liên kết tự động.');
            }).catch(() => {
                prompt('Sao chép cú pháp:', text);
            });
        }

        async function handleSaveZaloChatId(e) {
            e.preventDefault();
            const form = document.getElementById('formUpdateZaloBot');
            const btn = document.getElementById('btnSaveZaloChatId');
            const origText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang kết nối...';

            const formData = new FormData(form);
            formData.append('action', 'flora_ajax_partner_update_zalo');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert(data.data.message || 'Liên kết Zalo Bot thành công!');
                    closeZaloModal();
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể lưu Chat ID'));
                    btn.disabled = false;
                    btn.innerHTML = origText;
                }
            } catch(err) {
                alert('Lỗi kết nối máy chủ!');
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }

        async function handleTestZaloBot() {
            const token = '<?php echo esc_js($kol['secret_token'] ?? ''); ?>';
            if (!token) return;

            if (!confirm('Gửi một thông báo thử nghiệm mẫu vào Zalo của bạn ngay bây giờ?')) return;

            const formData = new FormData();
            formData.append('action', 'flora_ajax_partner_test_zalo');
            formData.append('token', token);

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert('🔔 ' + (data.data ? data.data.message : 'Đã gửi thông báo test thành công!'));
                } else {
                    alert('⚠️ ' + (data.data ? data.data.message : 'Không thể gửi test'));
                }
            } catch(e) {
                alert('Lỗi kết nối máy chủ!');
            }
        }

        async function handleUnlinkZaloBot() {
            const token = '<?php echo esc_js($kol['secret_token'] ?? ''); ?>';
            if (!token) return;

            if (!confirm('Bạn có chắc chắn muốn hủy liên kết Zalo Bot? Sau khi hủy, bạn sẽ không nhận được thông báo Zalo khi có đơn duyệt hay rút tiền.')) return;

            const formData = new FormData();
            formData.append('action', 'flora_ajax_partner_update_zalo');
            formData.append('token', token);
            formData.append('chat_id', '');

            try {
                const res = await fetch(AJAX_URL, { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    alert('Đã hủy liên kết Zalo Bot thành công.');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.data ? data.data.message : 'Không thể hủy liên kết'));
                }
            } catch(e) {
                alert('Lỗi kết nối máy chủ!');
            }
        }
    </script>
</body>
</html>
