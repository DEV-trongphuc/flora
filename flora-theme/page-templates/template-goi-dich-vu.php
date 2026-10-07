<?php
/**
 * Template Name: Landing Page Gói Dịch Vụ
 * Description: Landing page 2 gói dịch vụ Care Plus & Flora White Up
 */
if (!defined('ABSPATH')) exit;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gói Chăm Sóc Răng Miệng Toàn Diện | Nha Khoa Flora - Tiêu Chuẩn Thụy Sĩ</title>
    <meta name="description" content="Khám phá 2 gói dịch vụ chăm sóc răng miệng Care Plus và Flora White Up tại Nha Khoa Flora. Chăm sóc răng cho cả nhà chỉ từ 250.000đ/người/năm hoặc tẩy trắng răng Plasma công nghệ Thụy Sĩ." />

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <!-- Flora Main Styles -->
    <link href="<?php echo flora_asset('css/flora-style.css'); ?>?v=<?php echo FLORA_VERSION; ?>" rel="stylesheet" />
    <link href="<?php echo flora_asset('css/decor-animations.css'); ?>?v=<?php echo FLORA_VERSION; ?>" rel="stylesheet" />

    <style>
        /* ─── NHA KHOA FLORA BRAND SYSTEM OVERRIDES ─── */
        body {
            font-family: var(--font-body);
            color: var(--clr-text);
            background-color: #ffffff;
            line-height: 1.6;
        }

        /* Clean Navbar without browser outline */
        .header-bar * {
            outline: none !important;
        }
        .nav-links a {
            border: none !important;
            outline: none !important;
        }

        /* ─── 1. HERO BANNER SECTION (BACKGROUND ẢNH NHA SĨ & KHÁCH HÀNG CHUẨN THỤY SĨ) ─── */
        .pkg-hero-banner {
            position: relative;
            background-color: #f0f7fd;
            background-image: url('<?php echo flora_asset("banner_landing_page_goi_2.webp"); ?>');
            background-repeat: no-repeat;
            background-position: right center;
            background-size: cover;
            color: var(--clr-text);
            padding: 85px 0 110px;
            overflow: hidden;
            border-bottom: 1px solid #eef2f6;
        }

        .hero-glow-1,
        .hero-glow-2,
        .hero-glow-center,
        .hero-bg-pattern {
            display: none !important;
        }

        /* Ambient soft atmospheric lighting & decor */
        .hero-glow-1 {
            position: absolute;
            top: -15%;
            left: -8%;
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(4, 147, 241, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .hero-glow-2 {
            position: absolute;
            top: 20%;
            right: -10%;
            width: 750px;
            height: 750px;
            background: radial-gradient(circle, rgba(0, 51, 163, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        .hero-glow-center {
            position: absolute;
            top: 30%;
            right: 15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(4, 147, 241, 0.07) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
        }

        /* Subtle Geometric Background Pattern Decor */
        .hero-bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: radial-gradient(rgba(4, 147, 241, 0.12) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.35;
            pointer-events: none;
            z-index: 1;
            mask-image: linear-gradient(180deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 85%);
            -webkit-mask-image: linear-gradient(180deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 85%);
        }

        .hero-sparkle-decor {
            position: absolute;
            pointer-events: none;
            z-index: 1;
            animation: sparkleFloat 6s ease-in-out infinite;
        }

        .pkg-hero-grid {
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .pkg-hero-content {
            max-width: 650px;
            width: 100%;
        }

        .hero-brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #eff6ff;
            padding: 9px 20px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--clr-primary);
            border: 1px solid #bfdbfe;
            margin-bottom: 22px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0, 51, 163, 0.05);
        }

        .hero-brand-pill .swiss-flag {
            background: #e11d48;
            color: #ffffff;
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            font-weight: 900;
            line-height: 1;
        }

        .pkg-hero-title {
            font-family: var(--font-title);
            font-size: clamp(2.4rem, 4.2vw, 3.6rem);
            font-weight: 800;
            line-height: 1.36;
            color: var(--clr-navy);
            margin-bottom: 22px;
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .hero-title-accent {
            background: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline;
        }

        .pkg-hero-desc {
            font-size: 1.12rem;
            color: #475569;
            line-height: 1.75;
            margin-bottom: 28px;
            max-width: 620px;
        }

        /* 2 Mini Package Highlights in Hero (White Cards with subtle border) */
        .hero-mini-pkgs-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 30px;
            max-width: 620px;
        }

        .hero-mini-pkg-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 4px 14px rgba(0, 51, 163, 0.04);
            transition: var(--transition);
        }

        .hero-mini-pkg-card:hover {
            border-color: #93c5fd;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 51, 163, 0.08);
        }

        .hero-mini-pkg-name {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--clr-primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hero-mini-pkg-price {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin: 5px 0 3px;
            font-family: var(--font-title);
        }

        .hero-mini-pkg-note {
            font-size: 0.78rem;
            color: #64748b;
        }

        .pkg-hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 26px;
        }

        .btn-pkg-primary {
            background: linear-gradient(135deg, #0493f1 0%, #0033a3 100%);
            color: #ffffff;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.02rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(4, 147, 241, 0.35);
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }

        .btn-pkg-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(4, 147, 241, 0.45);
            color: #ffffff;
        }

        .btn-pkg-outline {
            background: #ffffff;
            border: 1.5px solid var(--clr-primary);
            color: var(--clr-primary);
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.98rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            cursor: pointer;
        }

        .btn-pkg-outline:hover {
            background: #eff6ff;
            color: #002270;
            border-color: #002270;
        }

        /* ─── ẢNH HERO BANNER SANG TRỌNG ─── */
        .pkg-hero-visual {
            position: relative;
            width: 100%;
        }

        /* Glow background aura (đồng đều, không xéo lệch) */
        .pkg-hero-visual::before {
            content: '';
            position: absolute;
            inset: -12px;
            background: linear-gradient(135deg, rgba(4, 147, 241, 0.2) 0%, rgba(0, 51, 163, 0.08) 100%);
            border-radius: 32px;
            z-index: 1;
            filter: blur(18px);
            opacity: 0.85;
        }

        .pkg-hero-banner-frame {
            position: relative;
            border-radius: 26px;
            padding: 10px;
            background: #ffffff;
            border: 1.5px solid rgba(4, 147, 241, 0.22);
            box-shadow: 0 25px 65px rgba(0, 51, 163, 0.12), 0 8px 20px rgba(0, 0, 0, 0.04);
            transition: var(--transition);
            z-index: 2;
        }

        .pkg-hero-banner-frame:hover {
            transform: translateY(-3px);
            box-shadow: 0 32px 75px rgba(0, 51, 163, 0.16), 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: rgba(4, 147, 241, 0.45);
        }

        .pkg-hero-banner-img {
            width: 100%;
            aspect-ratio: 16 / 9;
            object-fit: cover;
            border-radius: 20px;
            display: block;
        }

        /* Keyframes nổi êm không xoay góc */
        @keyframes heroCardGentleFloat {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-7px);
            }
        }

        /* Floating Trust Card with Customer Avatar Stack */
        .pkg-hero-card-float {
            position: absolute;
            bottom: -20px;
            left: -15px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1.5px solid #bfdbfe;
            padding: 13px 20px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 18px 40px rgba(0, 51, 163, 0.14);
            z-index: 3;
            animation: heroCardGentleFloat 4s ease-in-out infinite;
        }

        .hero-avatar-stack {
            display: flex;
            align-items: center;
        }

        .hero-avatar-stack img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2.5px solid #ffffff;
            object-fit: cover;
            margin-left: -11px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .hero-avatar-stack img:first-child {
            margin-left: 0;
        }

        .hero-avatar-stack .hero-avatar-more {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0493f1, #0033a3);
            color: #ffffff;
            border: 2.5px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: -11px;
            font-size: 0.72rem;
            font-weight: 800;
        }

        /* Floating Rating Badge on Top Right of Image */
        .pkg-hero-badge-top-right {
            position: absolute;
            top: 22px;
            right: 22px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 9px 18px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.9);
            font-size: 0.88rem;
            z-index: 3;
        }

        /* Floating Swiss Standard Pill on Top Left */
        .pkg-hero-badge-top-left {
            position: absolute;
            top: 22px;
            left: 22px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 8px 16px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.9);
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--clr-primary);
            z-index: 3;
            letter-spacing: 0.3px;
        }

        .pkg-hero-badge-top-left .swiss-flag-sm {
            background: #e11d48;
            color: #ffffff;
            width: 17px;
            height: 17px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 900;
            line-height: 1;
        }

        /* Floating Bottom Right Pill */
        .pkg-hero-stat-pill {
            position: absolute;
            bottom: 22px;
            right: 22px;
            background: rgba(10, 25, 49, 0.88);
            color: #ffffff;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            z-index: 3;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        /* ─── 2. SECTION 1: KHI NÀO BẠN NÊN ĐẾN NHA KHOA ─── */
        .when-visit-section {
            padding: 90px 0 85px;
            background: #ffffff;
        }

        .when-visit-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 45px;
        }

        /* Clean Symptom Box - NO AI TOP BORDER STRIPES */
        .symptom-box {
            background: #ffffff;
            border-radius: 20px;
            padding: 38px 34px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0, 51, 163, 0.04);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .symptom-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 34px rgba(0, 51, 163, 0.08);
            border-color: #cbd5e1;
        }

        .symptom-box-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
        }

        .symptom-badge-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            background: #f0f7ff;
            color: var(--clr-primary);
        }

        .symptom-box-tag {
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--clr-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .symptom-box-title {
            font-family: var(--font-title);
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin: 0;
        }

        .symptom-list {
            list-style: none;
            padding: 0;
            margin: 0 0 24px 0;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .symptom-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.5;
        }

        .symptom-item i {
            margin-top: 3px;
            font-size: 1.05rem;
            flex-shrink: 0;
            color: var(--clr-secondary);
        }

        .btn-symptom-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 13px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.95rem;
            text-decoration: none;
            background: #f8fafc;
            color: var(--clr-primary);
            border: 1px solid #e2e8f0;
            box-sizing: border-box;
            transition: var(--transition);
        }

        .btn-symptom-action:hover {
            background: #eff6ff;
            color: var(--clr-primary);
            border-color: #bfdbfe;
        }

        /* YouTube Video Frame */
        .video-embed-container {
            max-width: 900px;
            margin: 50px auto 0;
            background: #ffffff;
            border-radius: 20px;
            padding: 12px;
            box-shadow: 0 16px 45px rgba(10, 25, 49, 0.08);
            border: 1px solid #e2e8f0;
        }

        .video-embed-wrapper {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 14px;
        }

        .video-embed-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* ─── 3. SECTION 2: 2 GÓI DỊCH VỤ CHI TIẾT (CHUẨN FLORA BRAND BLUE) ─── */
        .pkg-showcase-section {
            padding: 85px 0 30px;
            background: #ffffff;
            overflow: hidden;
        }

        .pkg-showcase-section .container {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }

        .pkg-detailed-card {
            position: relative;
            width: 100%;
            background-color: #f8fafc;
            border-top: 1px solid #eef2f6;
            border-bottom: 1px solid #eef2f6;
            margin-bottom: 0;
            background-repeat: no-repeat;
            background-size: cover;
            background-position: right center;
            display: flex;
            align-items: center;
            transition: var(--transition);
        }

        .pkg-detailed-card#care-plus {
            background-image: url('<?php echo flora_asset("care_plus_2.webp"); ?>');
        }

        .pkg-detailed-card#white-up {
            background-image: url('<?php echo flora_asset("flora_white_up_2.webp"); ?>');
        }

        .pkg-card-full-inner {
            width: 100%;
            padding: 50px max(24px, 6vw);
            display: flex;
            align-items: center;
        }

        .pkg-card-mobile-visual {
            display: none;
        }

        .pkg-card-content {
            position: relative;
            z-index: 2;
            width: 480px;
            max-width: 100%;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 20px;
            padding: 30px 26px;
            box-shadow: 0 16px 40px rgba(0, 51, 163, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.85);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .pkg-type-tag {
            display: inline-block;
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 5px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
            background: rgba(239, 246, 255, 0.85);
            color: var(--clr-primary);
            border: 1px solid rgba(191, 219, 254, 0.6);
        }

        .pkg-card-title {
            font-family: var(--font-title);
            font-size: 2.05rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin-bottom: 4px;
        }

        .pkg-card-subtitle {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--clr-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .pkg-card-target {
            font-size: 0.92rem;
            color: #334155;
            margin-bottom: 18px;
            line-height: 1.55;
            background: rgba(240, 246, 255, 0.75);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid rgba(219, 234, 254, 0.7);
            border-left: 3px solid var(--clr-primary);
        }

        /* Price Row with Strikeout */
        .pkg-price-row {
            display: flex;
            align-items: baseline;
            gap: 14px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .pkg-original-price {
            font-size: 1.15rem;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 600;
        }

        .pkg-main-price {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--clr-primary);
            font-family: var(--font-title);
            line-height: 1;
        }

        .pkg-price-unit {
            font-size: 0.92rem;
            color: #64748b;
            font-weight: 600;
        }

        /* Highlight Box: CHỈ TỪ 250.000 VNĐ/NGƯỜI/NĂM (Clean Flora Style) */
        .pkg-highlight-box {
            background: rgba(238, 246, 255, 0.80);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1px solid rgba(191, 219, 254, 0.8);
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pkg-highlight-icon {
            font-size: 1.6rem;
            color: var(--clr-primary);
        }

        .pkg-highlight-text strong {
            display: block;
            font-size: 1.08rem;
            color: var(--clr-primary);
            font-weight: 800;
        }

        .pkg-highlight-text span {
            font-size: 0.85rem;
            color: #3b82f6;
            font-weight: 600;
        }

        /* Benefits List */
        .pkg-benefits-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: var(--clr-navy);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
            display: block;
        }

        .pkg-benefits-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 24px;
        }

        .pkg-benefit-item {
            background: rgba(255, 255, 255, 0.70);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border: 1px solid rgba(226, 232, 240, 0.75);
            border-radius: 12px;
            padding: 12px 14px;
            transition: var(--transition);
        }

        .pkg-benefit-item:hover {
            background: rgba(255, 255, 255, 0.92);
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        .pkg-benefit-num {
            font-size: 0.78rem;
            font-weight: 800;
            color: var(--clr-secondary);
            margin-bottom: 4px;
            display: block;
        }

        .pkg-benefit-name {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--clr-navy);
            margin-bottom: 4px;
        }

        .pkg-benefit-desc {
            font-size: 0.84rem;
            color: #64748b;
            line-height: 1.45;
            margin: 0;
        }

        /* Media Column */
        .pkg-card-media {
            position: relative;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 24px;
        }

        .pkg-card-img {
            width: 100%;
            height: 100%;
            max-height: 520px;
            object-fit: cover;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.06);
            transition: var(--transition);
        }

        .pkg-detailed-card:hover .pkg-card-img {
            transform: scale(1.02);
        }

        /* Shade Guide Interactive Visual */
        .shade-guide-bar {
            margin-top: 18px;
            background: #ffffff;
            padding: 16px;
            border-radius: 14px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
            width: 100%;
            box-sizing: border-box;
        }

        .shade-guide-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 8px;
        }

        .shade-spectrum {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            border-radius: 8px;
            overflow: hidden;
        }

        .shade-node {
            height: 38px;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.68rem;
            font-weight: 800;
            border: 1px solid rgba(0,0,0,0.06);
            transition: var(--transition);
            cursor: pointer;
        }

        .shade-node:hover {
            transform: translateY(-2px);
        }

        .s-a4 { background: #dfc89d; color: #5a421b; }
        .s-a35 { background: #e8d7b3; color: #695229; }
        .s-a3 { background: #f0e4c8; color: #735d34; }
        .s-a2 { background: #f5eedc; color: #554422; }
        .s-a1 { background: #faf6eb; color: #333333; }
        .s-b1 { background: #ffffff; color: #0033a3; border: 1.5px solid #0033a3; font-weight: 900; }
        .s-bl1 { background: #f0f9ff; color: #0284c7; border: 1.5px solid #0284c7; font-weight: 900; box-shadow: 0 0 8px rgba(2, 132, 199, 0.3); }

        /* ─── 4. SECTION 3: LỢI ÍCH CỦA GÓI (CLEAN ELEGANT CARDS) ─── */
        .pkg-benefits-section {
            padding: 90px 0;
            background: #ffffff;
        }

        .benefits-split-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-top: 45px;
        }

        /* Clean Benefit Column Card - NO AI TOP BORDER STRIPES */
        .benefit-column-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 38px 34px;
            border: 1px solid #e2e8f0;
            transition: var(--transition);
        }

        .benefit-column-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 12px 30px rgba(0, 51, 163, 0.05);
        }

        .benefit-card-header {
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .benefit-card-header span.benefit-cat {
            font-size: 0.8rem;
            font-weight: 800;
            color: var(--clr-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 4px;
        }

        .benefit-card-header h3 {
            font-family: var(--font-title);
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin-bottom: 4px;
        }

        .benefit-card-header p.benefit-sub {
            font-size: 0.95rem;
            color: #64748b;
            margin: 0;
        }

        .benefit-point-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 16px;
            font-size: 0.95rem;
            color: #334155;
            line-height: 1.55;
        }

        .benefit-point-item i {
            font-size: 1.1rem;
            margin-top: 3px;
            flex-shrink: 0;
            color: var(--clr-secondary);
        }

        /* ─── 5. SECTION CTA STRIP ─── */
        .pkg-cta-strip {
            position: relative;
            padding: 85px 0;
            background-color: #002270;
            background-image: linear-gradient(135deg, rgba(0, 34, 112, 0.94) 0%, rgba(0, 51, 163, 0.91) 50%, rgba(4, 147, 241, 0.88) 100%), url('<?php echo flora_asset("banner_landing_page_goi_2.webp"); ?>');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            color: #ffffff;
            text-align: center;
            overflow: hidden;
        }

        .pkg-cta-strip h2 {
            font-family: var(--font-title);
            font-size: 2.3rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 14px;
        }

        .pkg-cta-strip p {
            font-size: 1.1rem;
            color: #e0f2fe;
            max-width: 680px;
            margin: 0 auto 30px;
            line-height: 1.65;
        }

        .btn-cta-gold {
            background: #f59e0b;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 800;
            padding: 16px 42px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
            transition: var(--transition);
            border: none;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-cta-gold:hover {
            background: #d97706;
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(245, 158, 11, 0.55);
            color: #ffffff;
        }

        /* ─── 6. CHECKOUT SECTION ─── */
        .checkout-section {
            padding: 90px 0 100px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
        }

        .checkout-box-main {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 12px 40px rgba(0, 51, 163, 0.06);
            border: 1px solid #e2e8f0;
            padding: 40px;
            margin-top: 35px;
        }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 40px;
        }

        .checkout-heading-step {
            font-family: var(--font-title);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .step-num-badge {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--clr-primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 800;
        }

        /* Package Selector Radio Cards - Clean Brand Style */
        .pkg-select-cards-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 28px;
        }

        .pkg-select-card {
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            padding: 20px 22px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .pkg-select-card:hover {
            border-color: #93c5fd;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 51, 163, 0.08);
        }

        .pkg-select-card.selected {
            border-color: var(--clr-primary);
            background: linear-gradient(145deg, #f0f7ff 0%, #ffffff 100%);
            box-shadow: 0 8px 24px rgba(0, 51, 163, 0.12);
        }

        /* Nền avatar nhẹ mềm mại của 2 ảnh */
        .pkg-card-bg-avatar {
            position: absolute;
            right: -10px;
            bottom: -10px;
            width: 145px;
            height: 145px;
            border-radius: 50%;
            background-size: cover;
            background-position: center 20%;
            pointer-events: none;
            opacity: 0.16;
            transition: all 0.35s ease;
            -webkit-mask-image: radial-gradient(circle at center, rgba(0,0,0,1) 35%, rgba(0,0,0,0) 80%);
            mask-image: radial-gradient(circle at center, rgba(0,0,0,1) 35%, rgba(0,0,0,0) 80%);
            filter: saturate(1.15) contrast(1.05);
            z-index: 1;
        }

        .pkg-select-card:hover .pkg-card-bg-avatar {
            opacity: 0.24;
            transform: scale(1.06) rotate(-2deg);
        }

        .pkg-select-card.selected .pkg-card-bg-avatar {
            opacity: 0.32;
            transform: scale(1.1) rotate(-3deg);
        }

        .pkg-card-content-wrap {
            position: relative;
            z-index: 2;
        }

        .pkg-select-header-flex {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
            padding-right: 28px; /* chừa khoảng trống cho radio */
        }

        .pkg-select-avatar-thumb {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 51, 163, 0.15);
            flex-shrink: 0;
            background: #ffffff;
        }

        .pkg-select-tag-pill {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            background: #eff6ff;
            color: #0033a3;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .pkg-select-card input[type="radio"] {
            position: absolute;
            top: 20px;
            right: 20px;
            accent-color: var(--clr-primary);
            width: 20px;
            height: 20px;
            z-index: 3;
            cursor: pointer;
        }

        .pkg-select-name {
            font-size: 1.08rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin-bottom: 3px;
            font-family: var(--font-title);
        }

        .pkg-select-strike {
            font-size: 0.85rem;
            color: #94a3b8;
            text-decoration: line-through;
            font-weight: 600;
        }

        .pkg-select-price {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--clr-primary);
            margin: 4px 0 6px;
            font-family: var(--font-title);
        }

        .pkg-select-note {
            font-size: 0.82rem;
            color: #475569;
            line-height: 1.4;
        }

        .checkout-form-group {
            margin-bottom: 18px;
        }

        .checkout-label {
            display: block;
            font-size: 0.88rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }

        .checkout-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            border: 1.5px solid #cbd5e1;
            font-size: 0.95rem;
            font-family: inherit;
            transition: var(--transition);
            box-sizing: border-box;
            background: #ffffff;
        }

        .checkout-input:focus {
            outline: none;
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(0, 51, 163, 0.12);
        }

        .checkout-notice-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 0.86rem;
            color: #475569;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            line-height: 1.5;
            margin-top: 18px;
        }

        /* Order Summary Box */
        .order-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 28px;
        }

        .order-summary-title {
            font-family: var(--font-title);
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--clr-navy);
            margin-bottom: 18px;
            padding-bottom: 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .selected-pkg-preview {
            background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 50%, #eff6ff 100%);
            border: 1.5px solid #0493f1;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(4, 147, 241, 0.10);
            transition: all 0.3s ease;
        }

        .selected-pkg-preview::before {
            content: '';
            position: absolute;
            top: -25px;
            right: -25px;
            width: 100px;
            height: 100px;
            background: radial-gradient(circle, rgba(4, 147, 241, 0.16) 0%, rgba(4, 147, 241, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .summary-pkg-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 9999px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 51, 163, 0.18);
        }

        .summary-pkg-inner {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .summary-pkg-thumb-img {
            width: 58px;
            height: 58px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #ffffff;
            box-shadow: 0 3px 10px rgba(0, 51, 163, 0.15);
            flex-shrink: 0;
            background: #ffffff;
        }

        .summary-pkg-info {
            flex: 1;
            min-width: 0;
        }

        .selected-pkg-name {
            font-size: 1.12rem;
            font-weight: 800;
            color: #0033a3;
            margin-bottom: 3px;
            line-height: 1.3;
            font-family: var(--font-title);
        }

        .selected-pkg-desc {
            font-size: 0.82rem;
            color: #334155;
            line-height: 1.45;
            margin-bottom: 6px;
        }

        .summary-pkg-price-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #ffffff;
            color: #0033a3;
            border: 1px solid #bfdbfe;
            font-size: 0.8rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .voucher-input-group {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
        }

        .voucher-input {
            flex: 1;
            padding: 10px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .voucher-btn {
            background: #334155;
            color: #ffffff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
        }

        .voucher-btn:hover {
            background: #0f172a;
        }

        .summary-price-breakdown {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-price-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.92rem;
            color: #475569;
        }

        .summary-price-row.total {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--clr-primary);
            padding-top: 8px;
        }

        .btn-confirm-order {
            width: 100%;
            background: linear-gradient(135deg, #0493f1 0%, #0033a3 100%);
            color: #ffffff;
            font-size: 1.08rem;
            font-weight: 800;
            padding: 16px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(0, 51, 163, 0.25);
            transition: var(--transition);
            margin-bottom: 12px;
        }

        .btn-confirm-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 51, 163, 0.35);
        }

        .security-badge {
            text-align: center;
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* ─── 7. PAYMENT MODAL (QUÉT MÃ & CHUYỂN KHOẢN THỦ CÔNG) ─── */
        .payment-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            z-index: 99999;
            padding: 20px 16px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .payment-modal-overlay.active {
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .payment-modal-card {
            background: #ffffff;
            border-radius: 20px;
            max-width: 880px;
            width: 100%;
            margin: 20px auto;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow: hidden;
            position: relative;
            animation: modalFadeIn 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes modalFadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .payment-modal-header {
            background: #ffffff;
            color: #0f172a;
            padding: 14px 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .modal-header-left {
            min-width: 0;
            flex: 1;
        }

        .payment-modal-header h3 {
            font-family: var(--font-title);
            font-size: 1.18rem;
            font-weight: 800;
            margin: 0;
            color: #0f172a;
            letter-spacing: -0.2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .modal-header-kicker {
            display: block;
            font-size: 0.74rem;
            color: #0033a3;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.6px;
            margin-bottom: 2px;
        }

        .modal-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-security-badges {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sec-badge-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 5px 11px;
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 700;
            color: #334155;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .sec-badge-item:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .sec-badge-item.sec-badge-link {
            cursor: pointer;
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .sec-badge-item.sec-badge-link:hover {
            background: #e0f2fe;
            border-color: #7dd3fc;
        }

        .payment-modal-close {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #64748b;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .payment-modal-close:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            transform: rotate(90deg);
        }

        .payment-modal-body {
            padding: 22px 24px;
            display: block;
        }

        /* 1. SEGMENTED TABS (MOMO VS BANK) */
        .payment-segmented-control {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
            gap: 6px;
            margin-bottom: 18px;
        }

        .seg-tab-btn {
            flex: 1;
            border: none;
            background: transparent;
            padding: 11px 18px;
            border-radius: 9px;
            font-weight: 700;
            font-size: 0.94rem;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .seg-tab-btn:hover {
            color: #0f172a;
        }

        .seg-tab-btn.momo.active {
            background: #ffffff;
            color: #a50064;
            box-shadow: 0 3px 10px rgba(216, 45, 139, 0.18);
        }

        .seg-tab-btn.bank.active {
            background: #ffffff;
            color: #0033a3;
            box-shadow: 0 3px 10px rgba(0, 51, 163, 0.14);
        }

        .payment-tab-panel {
            display: none;
            grid-template-columns: 330px 1fr;
            gap: 20px;
            align-items: stretch;
        }

        .payment-tab-panel.active {
            display: grid;
        }

        /* 2. HERO QR CARD (TO 270px, VIEW FINDER CORNERS, BẮT MẮT) */
        .payment-qr-hero {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            text-align: center;
            position: relative;
        }

        .payment-qr-hero.momo-theme {
            background: linear-gradient(180deg, #fff5f8 0%, #ffffff 100%);
            border-color: #fbcfe8;
        }

        .payment-qr-hero.bank-theme {
            background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 100%);
            border-color: #bfdbfe;
        }

        .qr-badge-header {
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .qr-hero-frame {
            position: relative;
            background: #ffffff;
            border-radius: 18px;
            padding: 10px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            margin: 8px 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Viewfinder camera scan brackets */
        .qr-hero-frame::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            width: 24px;
            height: 24px;
            border-top: 3.5px solid #0033a3;
            border-left: 3.5px solid #0033a3;
            border-top-left-radius: 10px;
            pointer-events: none;
        }

        .qr-hero-frame::after {
            content: '';
            position: absolute;
            bottom: -3px;
            right: -3px;
            width: 24px;
            height: 24px;
            border-bottom: 3.5px solid #0033a3;
            border-right: 3.5px solid #0033a3;
            border-bottom-right-radius: 10px;
            pointer-events: none;
        }

        .momo-theme .qr-hero-frame::before,
        .momo-theme .qr-hero-frame::after {
            border-color: #d82d8b;
        }

        .qr-hero-image {
            width: 270px;
            height: 270px;
            object-fit: contain;
            display: block;
            border-radius: 8px;
        }

        .qr-timer-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fee2e2;
            color: #dc2626;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            margin-top: 4px;
        }

        .btn-open-momo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #d82d8b 0%, #a50064 100%);
            color: #ffffff !important;
            padding: 9px 16px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            text-decoration: none;
            margin-top: 8px;
            width: 100%;
            box-shadow: 0 4px 14px rgba(216, 45, 139, 0.28);
            transition: all 0.2s ease;
        }

        .btn-open-momo:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(216, 45, 139, 0.38);
            color: #fff !important;
        }

        .live-status-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.76rem;
            color: #0284c7;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 4px 12px;
            border-radius: 20px;
            margin-top: 8px;
            font-weight: 600;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #0284c7;
            box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7);
            animation: pulseWave 1.6s infinite;
        }

        @keyframes pulseWave {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(2, 132, 199, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(2, 132, 199, 0); }
        }

        /* 3. UNIFIED LUXURY INVOICE RECEIPT CARD (LIỀN MẠCH, KHÔNG TÁCH RỜI TỪNG HÀNG) */
        .payment-invoice-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        }

        .invoice-card-header {
            background: #f8fafc;
            padding: 12px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .invoice-card-header strong {
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .badge-verified-phone {
            font-size: 0.72rem;
            font-weight: 700;
            color: #15803d;
            background: #dcfce7;
            border: 1px solid #86efac;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Highlight 1: Số tiền */
        .invoice-amount-block {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .invoice-amount-block.momo-amount {
            background: #fff8fb;
        }

        .invoice-amount-val {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0033a3;
            font-family: var(--font-title);
            line-height: 1.2;
        }

        .invoice-amount-val.momo-val {
            color: #a50064;
        }

        /* Highlight 2: Cú pháp chuyển khoản chứa SĐT (HERO MEMO) */
        .invoice-memo-block {
            padding: 12px 18px;
            background: #fffbeb;
            border-top: 1px solid #fef3c7;
            border-bottom: 1px solid #fde68a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .invoice-memo-val {
            font-size: 1.15rem;
            font-weight: 800;
            color: #b45309;
            font-family: monospace;
            letter-spacing: 0.5px;
            word-break: break-all;
        }

        /* Bảng chi tiết người nhận (Liền mạch, kẻ hairline nhẹ, basic & đơn giản) */
        .invoice-details-list {
            padding: 4px 18px;
            display: flex;
            flex-direction: column;
            background: #ffffff;
            flex-grow: 1;
        }

        .invoice-detail-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            gap: 12px;
        }

        .invoice-detail-row:last-child {
            border-bottom: none;
        }

        .invoice-row-label {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .invoice-row-val {
            color: #0f172a;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: right;
            font-size: 0.92rem;
        }

        .val-highlight-amount {
            font-size: 1.18rem;
            color: #0033a3;
            font-weight: 800;
            letter-spacing: -0.2px;
        }

        .val-highlight-syntax {
            font-size: 0.95rem;
            color: #0f172a;
            font-weight: 700;
            letter-spacing: 0;
            font-family: inherit;
        }

        .val-highlight-stk {
            font-size: 1.05rem;
            color: #0033a3;
            font-weight: 800;
            letter-spacing: 0.2px;
        }

        .invoice-notice-bar {
            background: #fef2f2;
            border-top: 1px solid #fecaca;
            padding: 9px 16px;
            font-size: 0.76rem;
            color: #991b1b;
            line-height: 1.4;
        }

        /* Nút icon copy basic phẳng, không viền thô */
        .btn-copy-icon {
            width: 26px;
            height: 26px;
            min-width: 26px;
            border-radius: 6px;
            background: transparent;
            border: none;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.88rem;
            transition: all 0.15s ease;
            padding: 0;
            flex-shrink: 0;
        }

        .btn-copy-icon:hover {
            background: #f1f5f9;
            color: #0033a3;
        }

        .btn-copy-icon.copied {
            background: #dcfce7 !important;
            color: #16a34a !important;
        }

        .btn-copy-compact {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 4px 9px;
            border-radius: 6px;
            font-size: 0.76rem;
            font-weight: 700;
            color: #0033a3;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }

        .btn-copy-compact:hover {
            background: #0033a3;
            color: #ffffff;
            border-color: #0033a3;
        }

        /* Modal điều khoản & bảo mật */
        .terms-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100020;
            padding: 16px;
        }

        .terms-modal-overlay.active {
            display: flex;
        }

        .terms-modal-card {
            background: #ffffff;
            border-radius: 18px;
            max-width: 520px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .terms-modal-header {
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .terms-modal-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            font-size: 0.85rem;
            color: #475569;
            line-height: 1.5;
            max-height: 65vh;
            overflow-y: auto;
        }

        .term-point {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .term-point i {
            font-size: 1.15rem;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .term-point strong {
            color: #0f172a;
            display: block;
            margin-bottom: 3px;
            font-size: 0.88rem;
        }

        .term-point p {
            margin: 0;
            color: #475569;
        }

        /* 4. SUCCESS & AWAITING VIEW STATES */
        .payment-success-view,
        .payment-awaiting-view {
            display: none;
            text-align: center;
            padding: 20px 20px 22px;
            animation: modalFadeIn 0.3s ease;
        }

        .payment-success-view.active,
        .payment-awaiting-view.active {
            display: block;
        }

        .success-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 12px;
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.2);
            animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .awaiting-icon-wrap {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 10px;
            box-shadow: 0 8px 20px rgba(2, 132, 199, 0.18);
            position: relative;
            animation: bounceIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .awaiting-pulse-ring {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2px solid #0284c7;
            animation: ringPulse 2s infinite ease-out;
            pointer-events: none;
        }

        @keyframes ringPulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            100% { transform: scale(1.4); opacity: 0; }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        /* UPLOAD MINH CHỨNG BILL */
        .proof-upload-box {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 18px 20px;
            max-width: 520px;
            margin: 0 auto 20px;
            text-align: center;
            transition: all 0.2s ease;
        }

        .proof-upload-box:hover {
            border-color: #0493f1;
            background: #f0f9ff;
        }

        .btn-choose-proof {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1.5px solid #0033a3;
            color: #0033a3;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-choose-proof:hover {
            background: #0033a3;
            color: #ffffff;
        }

        .proof-preview-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 12px;
            text-align: left;
        }

        .proof-preview-img {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }

        /* SMART RESUME BANNER */
        .smart-resume-banner {
            display: none;
            background: linear-gradient(90deg, #0033a3 0%, #0493f1 100%);
            color: #ffffff;
            padding: 10px 20px;
            font-size: 0.88rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 51, 163, 0.25);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .smart-resume-banner.active {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .smart-resume-btn {
            background: #ffffff;
            color: #0033a3;
            border: none;
            padding: 5px 14px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.82rem;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .smart-resume-btn:hover {
            transform: scale(1.05);
        }

        .smart-resume-close {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0 4px;
        }

        /* Ẩn widget liên hệ cố định và dock mobile; GIỮ NGUYÊN Bác Sĩ AI bên trái & nút Tra Cứu Gói Dịch Vụ bên phải */
        .floating-widgets,
        .mobile-floating-dock {
            display: none !important;
        }

        #flora-ai-widget-container {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 99999 !important;
        }

        /* FLOATING TRACKING BUTTON */
        .btn-floating-tracking {
            position: fixed;
            bottom: 26px;
            right: 26px;
            z-index: 9999 !important;
            background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 12px 22px;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 25px rgba(0, 51, 163, 0.35);
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .btn-floating-tracking:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 30px rgba(0, 51, 163, 0.45);
            color: #ffffff;
        }

        /* MODAL TRA CỨU ĐƠN HÀNG */
        .tracking-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10001;
            padding: 16px;
        }

        .tracking-modal-overlay.active {
            display: flex;
        }

        .tracking-modal-card {
            background: #ffffff;
            width: 100%;
            max-width: 540px;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            border: 1px solid #e2e8f0;
            animation: modalFadeIn 0.3s ease;
        }

        .payment-modal-footer {
            padding: 14px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-paid-success {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(22, 163, 74, 0.3);
            transition: all 0.2s ease;
        }

        .btn-paid-success:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(22, 163, 74, 0.4);
        }

        .btn-cancel-trans {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #64748b;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-cancel-trans:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        @media (max-width: 768px) {
            .payment-tab-panel {
                grid-template-columns: 1fr;
            }
            .qr-hero-image {
                width: 230px;
                height: 230px;
            }
        }

        /* ─── 8. REGISTRATION POPUP MODAL (ROW 2 - 4) ─── */
        .reg-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(6px);
            z-index: 99998;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .reg-modal-overlay.active {
            display: flex;
        }

        .reg-modal-card {
            background: #ffffff;
            border-radius: 24px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            position: relative;
            animation: modalFadeIn 0.3s ease;
        }

        /* TOAST NOTIFICATION */
        .toast-notify {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #0f172a;
            color: #ffffff;
            padding: 14px 22px;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.92rem;
            font-weight: 600;
            z-index: 999999;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .toast-notify.show {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        @media (max-width: 1200px) and (min-width: 993px) {
            .pkg-card-content {
                width: 440px;
                padding: 24px 20px;
            }
            .pkg-card-full-inner {
                padding: 36px 3vw;
            }
        }

        /* RESPONSIVE BREAKPOINTS */
        @media (max-width: 992px) {
            .pkg-hero-banner {
                padding: 60px 0 75px;
                background-position: 78% center;
            }

            .pkg-hero-banner::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.85) 65%, rgba(255,255,255,0.6) 100%);
                pointer-events: none;
                z-index: 1;
            }

            .pkg-hero-grid,
            .when-visit-grid,
            .benefits-split-grid,
            .checkout-grid,
            .payment-modal-body {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .pkg-hero-grid {
                display: block;
            }

            .pkg-hero-content {
                max-width: 100%;
                position: relative;
                z-index: 2;
            }

            .pkg-detailed-card#care-plus,
            .pkg-detailed-card#white-up {
                background-image: none !important;
                padding: 0 !important;
                display: block !important;
            }

            .pkg-card-full-inner {
                padding: 0 !important;
                display: block !important;
            }

            .pkg-card-mobile-visual {
                display: block;
                width: 100%;
                overflow: hidden;
                border-radius: 0 !important;
                background: #f8fafc;
            }

            .pkg-card-mobile-visual img {
                width: 100%;
                height: auto;
                aspect-ratio: 16 / 9;
                object-fit: cover;
                display: block;
            }

            .pkg-card-content {
                width: 100% !important;
                max-width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: none !important;
                padding: 28px 20px !important;
            }

            .pkg-benefits-grid,
            .hero-mini-pkgs-row,
            .pkg-select-cards-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php wp_head(); ?>
</head>

<body>

    <!-- ─── SMART RESUME BANNER (HIỂN THỊ KHI F5 / RELOAD CÓ ĐƠN ĐANG CHỜ ĐỐI SOÁT) ─── -->
    <div class="smart-resume-banner" id="smartResumeBanner">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="pulse-dot" style="background: #ffffff; box-shadow: 0 0 0 0 rgba(255,255,255,0.7);"></span>
            <span>Quý khách có đơn hàng <strong id="smartResumeOrderCode" style="color: #fef08a; font-family: monospace;">FLORA...</strong> đang chờ đối soát từ Flora.</span>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <button type="button" class="smart-resume-btn" onclick="openOrderTrackingModal(localStorage.getItem('flora_last_order_code'))">
                <i class="fa-solid fa-clock-rotate-left"></i> Kiểm Tra Tiến Độ
            </button>
            <button type="button" class="smart-resume-close" onclick="dismissSmartResumeBanner()" title="Đóng">&times;</button>
        </div>
    </div>

    <!-- ─── BRAND NAVBAR (CHUẨN FLORA) ─── -->
    <header class="header-bar" style="position: sticky; top: 0; z-index: 9999; background: rgba(255,255,255,0.98); backdrop-filter: blur(10px); box-shadow: 0 2px 15px rgba(0,0,0,0.04);">
        <div class="container header-inner" style="display: flex; align-items: center; justify-content: space-between; height: 76px;">
            <div class="logo-box">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: flex; align-items: center; text-decoration: none;">
                    <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="Flora Logo" class="logo-img" style="height: 52px; width: auto;" />
                </a>
            </div>

            <ul class="nav-links" style="display: flex; list-style: none; gap: 26px; margin: 0; padding: 0;">
                <li class="nav-link-item"><a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a></li>
                <li class="nav-link-item"><a href="#khi-nao-nen-kham">Khi Nào Nên Khám</a></li>
                <li class="nav-link-item active"><a href="#chi-tiet-goi">2 Gói Dịch Vụ</a></li>
                <li class="nav-link-item"><a href="#loi-ich-goi">Lợi Ích</a></li>
                <li class="nav-link-item"><a href="#gioi-thieu-flora">Về Flora</a></li>
                <li class="nav-link-item"><a href="#bac-si-minh">Bác Sĩ CKI</a></li>
            </ul>

            <div class="header-action" style="display: flex; align-items: center; gap: 14px;">
                <a href="tel:02873058999" class="header-hotline-link" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: #0033a3; text-decoration: none; font-size: 0.92rem;">
                    <i class="fa-solid fa-phone-volume"></i> <span>028 7305 8999</span>
                </a>
                <a href="#thanh-toan" class="btn btn-primary" style="background: linear-gradient(135deg, #0493f1 0%, #0033a3 100%); color: #fff; padding: 11px 24px; border-radius: 50px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 14px rgba(0,51,163,0.25);">
                    Đăng Ký Mua Gói
                </a>
            </div>

            <button class="hamburger-btn" id="drawerTrigger" aria-label="Mở menu" style="display: none; background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #0f172a;">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- ─── 1. HERO BANNER SECTION (ROW 5 - NỀN TRẮNG SANG TRỌNG & DECOR TINH TẾ) ─── -->
    <section class="pkg-hero-banner" id="banner">
        <!-- Floating Decor Lights & Ambient Mesh -->
        <div class="hero-glow-1"></div>
        <div class="hero-glow-2"></div>
        <div class="hero-glow-center"></div>
        <div class="hero-bg-pattern"></div>

        <!-- Floating SVG Sparkles from Flora standard design -->
        <div class="hero-sparkle hero-sparkle-1" style="top: 10%; left: 3%;">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="28" height="28">
                <path d="M12 0L14.8 9.2L24 12L14.8 14.8L12 24L9.2 14.8L0 12L9.2 9.2L12 0Z" fill="url(#heroSparkleGrad)" />
                <defs>
                    <linearGradient id="heroSparkleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#0493f1" />
                        <stop offset="100%" stop-color="#0033a3" />
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div class="hero-sparkle hero-sparkle-2" style="top: 18%; right: 44%;">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="22" height="22">
                <path d="M12 0L14.8 9.2L24 12L14.8 14.8L12 24L9.2 14.8L0 12L9.2 9.2L12 0Z" fill="url(#heroSparkleGrad)" />
            </svg>
        </div>
        <div class="hero-sparkle hero-sparkle-3" style="bottom: 14%; left: 36%;">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                <path d="M12 0L14.8 9.2L24 12L14.8 14.8L12 24L9.2 14.8L0 12L9.2 9.2L12 0Z" fill="url(#heroSparkleGrad)" />
            </svg>
        </div>

        <div class="container" style="max-width: 1420px; width: 95%;">
            <div class="pkg-hero-grid">
                <!-- Left: Content & Value Props -->
                <div class="pkg-hero-content reveal">
                    <div class="hero-brand-pill">
                        <span class="swiss-flag">+</span>
                        <span>TIÊU CHUẨN THỤY SĨ &bull; CHỌN GÓI PHÙ HỢP &bull; TỐI ƯU CHI PHÍ</span>
                    </div>

                    <h1 class="pkg-hero-title">
                        CHỌN ĐÚNG GÓI<br />
                        <span class="highlight-text-container">
                            <span class="hero-title-accent">CHĂM ĐÚNG NHU CẦU</span>
                            <svg class="heading-underline-svg" viewBox="0 0 320 20" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 13 C 110 3, 210 19, 315 11" stroke="url(#heroAccentGrad)" stroke-width="4.5" stroke-linecap="round" />
                                <defs>
                                    <linearGradient id="heroAccentGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#0493f1" />
                                        <stop offset="100%" stop-color="#0033a3" />
                                    </linearGradient>
                                </defs>
                            </svg>
                        </span>
                    </h1>

                    <p class="pkg-hero-desc">
                        Từ chăm sóc răng miệng định kỳ cho cả gia đình đến làm sạch, tẩy trắng răng, Flora mang đến hai lựa chọn phù hợp với nhu cầu và ngân sách của bạn.
                    </p>

                    <!-- 2 Mini Package Quick Anchor Cards -->
                    <div class="hero-mini-pkgs-row">
                        <a href="#care-plus" class="hero-mini-pkg-card" style="text-decoration: none; color: inherit;">
                            <div class="hero-mini-pkg-name"><i class="fa-solid fa-people-roof"></i> Care Plus</div>
                            <div class="hero-mini-pkg-price">999.000đ<span style="font-size: 0.8rem; font-weight: normal; color: #64748b;">/năm</span></div>
                            <div class="hero-mini-pkg-note">Chỉ từ 250.000đ/người &bull; Tối đa 4 người</div>
                        </a>
                        <a href="#white-up" class="hero-mini-pkg-card" style="text-decoration: none; color: inherit;">
                            <div class="hero-mini-pkg-name"><i class="fa-solid fa-wand-magic-sparkles"></i> Flora White Up</div>
                            <div class="hero-mini-pkg-price">1.999.000đ<span style="font-size: 0.8rem; font-weight: normal; color: #64748b;">/gói</span></div>
                            <div class="hero-mini-pkg-note">Tẩy trắng Plasma chuẩn Thụy Sĩ</div>
                        </a>
                    </div>

                    <div class="pkg-hero-cta-group">
                        <a href="#chi-tiet-goi" class="btn-pkg-primary">
                            <i class="fa-solid fa-sparkles"></i> KHÁM PHÁ 2 GÓI DỊCH VỤ
                        </a>
                        <button type="button" class="btn-pkg-outline" onclick="openRegModal()">
                            <i class="fa-solid fa-calendar-check"></i> Đăng Ký Tư Vấn Nhanh
                        </button>
                    </div>

                    <div style="display: flex; align-items: center; gap: 20px; color: #64748b; font-size: 0.88rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="color: #0493f1;"></i> Tiêu chuẩn Thụy Sĩ
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="color: #0493f1;"></i> BS.CKI trực tiếp thăm khám
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-circle-check" style="color: #0493f1;"></i> Không phát sinh chi phí
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 2. SECTION 1: KHI NÀO BẠN NÊN ĐẾN NHA KHOA? (ROW 6, 7, 8, 9) ─── -->
    <section class="when-visit-section" id="khi-nao-nen-kham">
        <div class="container">
            <!-- Chuẩn Section Header Flora (Có blue underline bar tự động h2::after) -->
            <div class="section-header center reveal">
                <h2>Khi Nào Bạn Nên Đến <span class="gradient-text">Nha Khoa?</span></h2>
                <p>Đừng chờ đến khi cơn đau xuất hiện mới đi khám răng. Nếu bạn đang gặp một trong những tình trạng dưới đây, đây là thời điểm phù hợp để kiểm tra và chăm sóc răng miệng:</p>
            </div>

            <!-- 2 Boxes Phân Loại (Clean - Không viền màu AI) -->
            <div class="when-visit-grid">
                <!-- Box 1: CARE PLUS -->
                <div class="symptom-box reveal reveal-delay-1">
                    <div>
                        <div class="symptom-box-header">
                            <div class="symptom-badge-icon">
                                <i class="fa-solid fa-people-roof"></i>
                            </div>
                            <div>
                                <span class="symptom-box-tag">Gói Chăm Sóc Định Kỳ</span>
                                <h3 class="symptom-box-title">Bạn nên chọn CARE PLUS nếu:</h3>
                            </div>
                        </div>

                        <ul class="symptom-list">
                            <li class="symptom-item">
                                <i class="fa-solid fa-clock-rotate-left"></i>
                                <span>Đã lâu chưa cạo vôi hoặc kiểm tra răng miệng định kỳ</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-tooth"></i>
                                <span>Răng có nhiều mảng bám, cao răng tích tụ</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-droplet"></i>
                                <span>Nướu dễ sưng, đỏ hoặc chảy máu khi chải răng</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-wind"></i>
                                <span>Hơi thở có mùi dù đã vệ sinh răng miệng hằng ngày</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-house-chimney-medical"></i>
                                <span>Muốn chăm sóc răng miệng định kỳ cho cả gia đình tiết kiệm</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Cần theo dõi sớm các vấn đề như sâu răng, răng khôn hoặc răng nứt mẻ</span>
                            </li>
                        </ul>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <a href="#care-plus" class="btn-symptom-action">
                            Xem Chi Tiết Gói Care Plus <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Box 2: FLORA WHITE UP -->
                <div class="symptom-box reveal reveal-delay-2">
                    <div>
                        <div class="symptom-box-header">
                            <div class="symptom-badge-icon">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <div>
                                <span class="symptom-box-tag">Tân Trang Nụ Cười</span>
                                <h3 class="symptom-box-title">Bạn nên chọn FLORA WHITE UP nếu:</h3>
                            </div>
                        </div>

                        <ul class="symptom-list">
                            <li class="symptom-item">
                                <i class="fa-solid fa-mug-hot"></i>
                                <span>Răng bị xỉn màu do cà phê, trà, thuốc lá hoặc thực phẩm đậm màu</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-gem"></i>
                                <span>Muốn cải thiện sắc độ răng trước những dịp quan trọng (cưới hỏi, sự kiện)</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-face-smile"></i>
                                <span>Nụ cười kém tươi sáng dù nền răng vẫn khỏe mạnh</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-sparkles"></i>
                                <span>Muốn làm sạch cao răng, đánh bóng men răng kỹ lưỡng trước khi tẩy trắng</span>
                            </li>
                            <li class="symptom-item">
                                <i class="fa-solid fa-user-doctor"></i>
                                <span>Cần một liệu trình tẩy trắng răng an toàn, được kiểm tra & chỉ định bởi Bác sĩ</span>
                            </li>
                        </ul>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 20px;">
                        <a href="#white-up" class="btn-symptom-action">
                            Xem Chi Tiết Gói White Up <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- CTA Button (Row 8) -->
            <div style="text-align: center; margin-top: 40px;" class="reveal">
                <a href="#chi-tiet-goi" class="btn-pkg-primary" style="font-size: 1.05rem; padding: 16px 38px;">
                    <i class="fa-solid fa-list-check"></i> CHỌN GÓI ĐĂNG KÝ PHÙ HỢP
                </a>
            </div>

            <!-- YouTube Video Embed (Row 9 - 16:9 bo góc mềm, shadow nhẹ) -->
            <div class="video-embed-container reveal reveal-delay-2">
                <div class="video-embed-wrapper">
                    <iframe src="https://www.youtube-nocookie.com/embed/iAlU352wxv4?rel=0&modestbranding=1&controls=1" title="Khám phá Dịch Vụ Nha Khoa Flora - Trải nghiệm chuẩn Thụy Sĩ" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 3. SECTION 2: CHỌN GÓI CHĂM SÓC PHÙ HỢP (ROW 10, 11, 12 - CHUẨN FLORA BRAND BLUE) ─── -->
    <section class="pkg-showcase-section" id="chi-tiet-goi">
        <div class="container">
            <!-- Chuẩn Section Header Flora -->
            <div class="section-header center reveal">
                <h2>2 Gói Chăm Sóc &ndash; 2 Lựa Chọn Cho <span class="gradient-text">Nụ Cười</span></h2>
                <p>Từ chăm sóc răng miệng định kỳ cho cả gia đình đến làm mới nụ cười trước những dịp quan trọng, Flora mang đến hai gói dịch vụ với quyền lợi thiết thực và mức chi phí phù hợp.</p>
            </div>
        </div><!-- /.container -->

        <!-- ─── GÓI 1: CARE PLUS (ROW 11 - FULL MÀN) ─── -->
        <div class="pkg-detailed-card reveal" id="care-plus">
            <div class="pkg-card-full-inner">
                <!-- Mobile visual fallback -->
                <div class="pkg-card-mobile-visual">
                    <img src="<?php echo flora_asset('care_plus_2.webp'); ?>" alt="Gói chăm sóc răng miệng gia đình Care Plus tại Flora" />
                </div>

                <div class="pkg-card-content">
                    <div>
                        <span class="pkg-type-tag">Gói Chăm Sóc Định Kỳ</span>
                        <h3 class="pkg-card-title">CARE PLUS</h3>
                        <div class="pkg-card-subtitle">CHĂM RĂNG CHO CẢ NHÀ</div>

                        <div class="pkg-card-target">
                            <i class="fa-solid fa-users" style="color: var(--clr-primary); margin-right: 6px;"></i>
                            <strong>Đối tượng phù hợp:</strong> Gia đình từ 2 thành viên trở lên, mong muốn chủ động chăm sóc răng miệng định kỳ và tiết kiệm chi phí khi phát sinh điều trị.
                        </div>

                        <!-- Price Row with Strikeout -->
                        <div class="pkg-price-row">
                            <span class="pkg-original-price">2.500.000 VNĐ</span>
                            <span class="pkg-main-price">999.000 VNĐ</span>
                            <span class="pkg-price-unit">/ GÓI / NĂM</span>
                        </div>

                        <!-- Highlight Box (Row 11: Điểm nhấn cần làm nổi bật) -->
                        <div class="pkg-highlight-box">
                            <div class="pkg-highlight-icon">
                                <i class="fa-solid fa-piggy-bank"></i>
                            </div>
                            <div class="pkg-highlight-text">
                                <strong>CHỈ TỪ 250.000 VNĐ / NGƯỜI / NĂM</strong>
                                <span>Áp dụng tối đa lên đến 4 thành viên gia đình sử dụng chung</span>
                            </div>
                        </div>

                        <!-- Benefits 4 Boxes -->
                        <span class="pkg-benefits-title"><i class="fa-solid fa-circle-check" style="color: #0493f1; margin-right: 6px;"></i> 4 Quyền Lợi Thiết Thực:</span>
                        <div class="pkg-benefits-grid">
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">01. KHÔNG GIỚI HẠN</span>
                                <div class="pkg-benefit-name">Cạo vôi răng miễn phí</div>
                                <p class="pkg-benefit-desc">Không giới hạn số lần sử dụng trong suốt 1 năm thời hạn của gói.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">02. CHUYÊN MÔN CAO</span>
                                <div class="pkg-benefit-name">Thăm khám cùng BS CKI</div>
                                <p class="pkg-benefit-desc">Kiểm tra tình trạng định kỳ, phát hiện sớm sâu răng & bệnh lý nướu.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">03. TIẾT KIỆM THÊM</span>
                                <div class="pkg-benefit-name">Giảm 10% điều trị phát sinh</div>
                                <p class="pkg-benefit-desc">Ưu đãi giảm ngay 10% chi phí trám răng và điều trị tủy khi phát sinh.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">04. TIỆN LỢI</span>
                                <div class="pkg-benefit-name">Dùng chung cho gia đình</div>
                                <p class="pkg-benefit-desc">Đăng ký tối đa 4 thành viên, thời hạn sử dụng trọn vẹn 1 năm.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" class="btn-pkg-primary" style="width: 100%; justify-content: center;" onclick="selectPackageAndCheckout('careplus')">
                            <i class="fa-solid fa-cart-shopping"></i> ĐĂNG KÝ MUA GÓI CARE PLUS
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── GÓI FLORA WHITE UP (ROW 12 - FULL MÀN) ─── -->
        <div class="pkg-detailed-card reveal reveal-delay-1" id="white-up">
            <div class="pkg-card-full-inner">
                <!-- Mobile visual fallback -->
                <div class="pkg-card-mobile-visual">
                    <img src="<?php echo flora_asset('flora_white_up_2.webp'); ?>" alt="Gói làm sạch và tẩy trắng răng chuyên sâu Flora White Up" />
                </div>

                <div class="pkg-card-content">
                    <div>
                        <span class="pkg-type-tag">Gói Tẩy Trắng Chuyên Sâu</span>
                        <h3 class="pkg-card-title">FLORA WHITE UP</h3>
                        <div class="pkg-card-subtitle">LÀM MỚI NỤ CƯỜI TRƯỚC NHỮNG DỊP QUAN TRỌNG</div>

                        <div class="pkg-card-target">
                            <i class="fa-solid fa-wand-magic-sparkles" style="color: var(--clr-primary); margin-right: 6px;"></i>
                            <strong>Đối tượng phù hợp:</strong> Khách hàng muốn cải thiện sắc độ răng và sở hữu nụ cười tươi sáng hơn với quy trình tẩy trắng tại nha khoa chuẩn y khoa.
                        </div>

                        <!-- Price Row with Strikeout -->
                        <div class="pkg-price-row">
                            <span class="pkg-original-price">3.000.000 VNĐ</span>
                            <span class="pkg-main-price">1.999.000 VNĐ</span>
                            <span class="pkg-price-unit">/ GÓI TRỌN GÓI</span>
                        </div>

                        <!-- Highlight note (Flora Blue Tone) -->
                        <div class="pkg-highlight-box">
                            <div class="pkg-highlight-icon">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div class="pkg-highlight-text">
                                <strong>CÔNG NGHỆ ÁNH SÁNG PLASMA LẠNH</strong>
                                <span>An toàn men răng tuyệt đối, không gây ê buốt kéo dài, bật 2 &ndash; 4 tone</span>
                            </div>
                        </div>

                        <!-- Benefits 4 Boxes -->
                        <span class="pkg-benefits-title"><i class="fa-solid fa-circle-check" style="color: #0493f1; margin-right: 6px;"></i> 4 Quyền Lợi Thiết Thực:</span>
                        <div class="pkg-benefits-grid">
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">01. CHẨN ĐOÁN</span>
                                <div class="pkg-benefit-name">Thăm khám cùng Bác sĩ CKI</div>
                                <p class="pkg-benefit-desc">Kiểm tra men răng và tư vấn nồng độ gel tẩy trắng phù hợp.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">02. LÀM SẠCH</span>
                                <div class="pkg-benefit-name">Cạo vôi & đánh bóng răng</div>
                                <p class="pkg-benefit-desc">Đã bao gồm trong gói, thực hiện kỹ lưỡng trước khi tẩy trắng.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">03. CÔNG NGHỆ CAO</span>
                                <div class="pkg-benefit-name">Tẩy trắng đèn Plasma</div>
                                <p class="pkg-benefit-desc">Kích hoạt phân tử làm trắng sâu, bật 2 &ndash; 4 tone rõ rệt.</p>
                            </div>
                            <div class="pkg-benefit-item">
                                <span class="pkg-benefit-num">04. LINH HOẠT</span>
                                <div class="pkg-benefit-name">Hạn dùng đến 31/12/2026</div>
                                <p class="pkg-benefit-desc">Chủ động sắp xếp thời gian làm đẹp trước các dịp quan trọng.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="button" class="btn-pkg-primary" style="width: 100%; justify-content: center;" onclick="selectPackageAndCheckout('whiteup')">
                            <i class="fa-solid fa-cart-shopping"></i> ĐĂNG KÝ MUA GÓI FLORA WHITE UP
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 4. SECTION 3: LỢI ÍCH CỦA GÓI (ROW 13, 14 - CLEAN ELEGANT STYLE) ─── -->
    <section class="pkg-benefits-section" id="loi-ich-goi">
        <div class="container">
            <!-- Chuẩn Section Header Flora -->
            <div class="section-header center reveal">
                <h2>Chăm Nụ Cười Chủ Động <span class="gradient-text">Hơn Mỗi Ngày</span></h2>
                <p>Với hai lựa chọn phù hợp cho nhu cầu chăm sóc răng miệng và cải thiện nụ cười, Flora giúp bạn dễ dàng bắt đầu một kế hoạch chăm sóc rõ ràng, tiết kiệm và linh hoạt hơn.</p>
            </div>

            <!-- 2 Split Benefit Cards (Clean - Không viền màu AI) -->
            <div class="benefits-split-grid">
                <!-- Care Plus Benefits -->
                <div class="benefit-column-card reveal reveal-delay-1">
                    <div class="benefit-card-header">
                        <span class="benefit-cat">Gói Chăm Sóc Gia Đình</span>
                        <h3>CARE PLUS</h3>
                        <p class="benefit-sub">Duy trì sức khỏe răng miệng cho cả gia đình</p>
                    </div>

                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Hình thành thói quen chăm sóc và kiểm tra răng miệng định kỳ cho từng thành viên.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Hỗ trợ nhận biết sớm các dấu hiệu bất thường ở răng và nướu trước khi phát triển nặng.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Hạn chế tối đa để các vấn đề như sâu răng, viêm nướu hoặc cao răng bám dai dẳng gây tiêu xương.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Giúp gia đình an tâm tuyệt đối khi luôn có bác sĩ chuyên môn theo dõi sức khỏe răng miệng dài hạn.</span>
                    </div>
                </div>

                <!-- White Up Benefits -->
                <div class="benefit-column-card reveal reveal-delay-2">
                    <div class="benefit-card-header">
                        <span class="benefit-cat">Tân Trang Nụ Cười</span>
                        <h3>FLORA WHITE UP</h3>
                        <p class="benefit-sub">Tân trang nụ cười rạng rỡ và tự tin</p>
                    </div>

                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Loại bỏ hoàn toàn mảng bám ố vàng và cặn màu sậm tích tụ lâu năm trên bề mặt men răng.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Hỗ trợ cải thiện rõ rệt từ 2 đến 4 sắc độ răng so với nền răng ban đầu.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Giúp răng sạch bong sáng bóng, khoang miệng sạch sẽ và hơi thở thơm mát dễ chịu hơn.</span>
                    </div>
                    <div class="benefit-point-item">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Mang lại nụ cười sáng bừng đầy cuốn hút và cảm giác tự tin tuyệt đối trong mọi giao tiếp công việc, cuộc sống.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 5. SECTION 5: GIỚI THIỆU NHA KHOA FLORA (ROW 16) ─── -->
    <section class="section-padding fact-section" id="gioi-thieu-flora" style="background: var(--clr-bg-light); border-top: 1px solid var(--clr-border); border-bottom: 1px solid var(--clr-border); padding: 90px 0;">
        <div class="container">
            <div class="fact-split-showcase reveal">
                <!-- Left Column: Story -->
                <div class="fact-showcase-left">
                    <div class="section-header reveal" style="margin-bottom: 24px; max-width: 100%;">
                        <h2>Nha Khoa Flora &ndash; Hơn 7 Năm Kiến Tạo <span class="gradient-text">50.000+ Nụ Cười</span></h2>
                    </div>

                    <p class="fact-lead-text" style="font-size: 1.05rem; color: #334155; line-height: 1.7; margin-bottom: 14px;">
                        Nha khoa Flora là hệ thống nha khoa tiêu chuẩn Thụy Sĩ, chuyên sâu về <strong>trồng răng Implant</strong>, <strong>điều trị cười hở lợi</strong>, <strong>chỉnh nha</strong> và <strong>phục hình thẩm mỹ</strong>.
                    </p>

                    <p class="fact-sub-text" style="font-size: 0.95rem; color: #64748b; line-height: 1.65; margin-bottom: 28px;">
                        Với hơn 7 năm phát triển cùng đội ngũ Bác sĩ giàu kinh nghiệm, Flora đã đồng hành cùng hơn 50.000 khách hàng trong hành trình chăm sóc, phục hồi và cải thiện nụ cười toàn diện.
                    </p>

                    <!-- 2 CTAs required in Row 16 -->
                    <div class="fact-showcase-actions" style="display: flex; gap: 14px; flex-wrap: wrap;">
                        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline" style="border: 2px solid var(--clr-primary); color: var(--clr-primary); font-weight: 700; padding: 13px 26px; border-radius: 50px; text-decoration: none;">
                            <i class="fa-solid fa-globe" style="margin-right: 6px;"></i> Khám Phá Nha Khoa Flora
                        </a>
                        <a href="#thanh-toan" class="btn btn-primary" style="background: linear-gradient(135deg, #0493f1 0%, #0033a3 100%); color: #fff; font-weight: 700; padding: 13px 26px; border-radius: 50px; text-decoration: none; box-shadow: 0 4px 14px rgba(0,51,163,0.25);">
                            <i class="fa-solid fa-cart-shopping" style="margin-right: 6px;"></i> Đăng Ký Gói
                        </a>
                    </div>
                </div>

                <!-- Right Column: Video Showcase -->
                <div class="fact-showcase-right">
                    <div class="flora-video-frame-wrapper" style="margin: 0; width: 100%;">
                        <div class="flora-video-badge-decor" style="background: rgba(15, 23, 42, 0.85); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 10px;">
                            <i class="fa-solid fa-circle-play" style="color: #38bdf8;"></i> Video Phòng Khám Chuẩn Thụy Sĩ
                        </div>
                        <div class="flora-video-frame-inner" style="border-radius: 16px; overflow: hidden; box-shadow: 0 16px 40px rgba(0,0,0,0.12); border: 2px solid #ffffff;">
                            <div class="flora-video-ratio-16-9" style="position: relative; padding-bottom: 56.25%; height: 0;">
                                <iframe src="https://www.youtube-nocookie.com/embed/VhvKj_iMdiM?rel=0&modestbranding=1&controls=1" title="Hành Trình Kiến Tạo 50.000+ Nụ Cười Tại Nha Khoa Flora" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy" style="position: absolute; top:0; left:0; width:100%; height:100%; border:0;"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 6. SECTION 6: BÁC SĨ CKI NGUYỄN ĐẮC MINH (ROW 17) ─── -->
    <section class="section-padding expert-section" id="expert">
        <!-- Background SVG Expert Crest & Abstract Cross Nodes -->
        <div class="expert-bg-decor">
            <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="180" y="100" width="40" height="200" rx="6" fill="rgba(4, 147, 241, 0.04)" />
                <rect x="100" y="180" width="200" height="40" rx="6" fill="rgba(4, 147, 241, 0.04)" />
                <circle cx="200" cy="200" r="160" stroke="rgba(0, 51, 163, 0.03)" stroke-width="1.5" stroke-dasharray="10 6" class="rot-clockwise" />
            </svg>
        </div>
        <div class="container">
            <div class="section-header center reveal">
                <h2>Bác Sĩ Trực Tiếp Điều Trị Tại Flora</h2>
            </div>
            <div class="expert-grid reveal">
                <!-- Left Column: Premium Framed portrait image -->
                <div class="reveal-left expert-portrait-frame">
                    <img src="<?php echo flora_asset('homepage/bs_minh_portrait.webp'); ?>" alt="BS.CKI Nguyễn Đắc Minh - Bác sĩ phụ trách chuyên môn tại Flora" class="expert-portrait-img" loading="lazy" width="480" height="580" style="object-fit: cover; object-position: 55% 10%; border-radius: 16px;" />
                </div>
                
                <!-- Right Column: Biography & Achievements -->
                <div class="reveal-right" style="display: flex; flex-direction: column; gap: 18px;">
                    <div>
                        <h3 style="font-family: var(--font-title); font-size: 1.55rem; font-weight: 800; color: var(--clr-navy); margin-bottom: 4px; line-height: 1.2;">BS.CKI NGUYỄN ĐẮC MINH</h3>
                        <p style="font-size: 0.95rem; font-weight: 700; color: var(--clr-secondary); margin-bottom: 12px;">Chuyên gia Cấy ghép Implant & Phục hình thẩm mỹ</p>
                        
                        <div style="background: var(--clr-bg-light); padding: 14px 18px; border-left: 3.5px solid var(--clr-primary); border-radius: 8px; margin-bottom: 16px;">
                            <strong style="color: var(--clr-navy); display: block; font-size: 0.92rem; margin-bottom: 4px; letter-spacing: 0.5px;">TRIẾT LÝ ĐIỀU TRỊ:</strong>
                            <p style="font-size: 0.95rem; font-weight: 700; color: var(--clr-primary); font-style: italic; margin-bottom: 6px;">“Nha khoa là sự giao thoa giữa y khoa, kỹ thuật và thẩm mỹ.”</p>
                            <p style="font-size: 0.88rem; color: #334155; line-height: 1.6; margin: 0;">Mỗi kế hoạch điều trị được xây dựng trên nền tảng chẩn đoán kỹ lưỡng, chỉ định phù hợp, thao tác có kiểm soát và theo dõi dài hạn, hướng đến sự cân bằng giữa chức năng, thẩm mỹ và trải nghiệm của khách hàng.</p>
                        </div>

                        <!-- 3 Statistics Box -->
                        <div style="margin-bottom: 8px;">
                            <span style="font-size: 0.82rem; font-weight: 700; color: var(--clr-navy); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 8px;">HỒ SƠ ĐIỀU TRỊ NỔI BẬT</span>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                                <div style="background: rgba(4, 147, 241, 0.06); border: 1px solid rgba(4, 147, 241, 0.2); padding: 12px 8px; border-radius: 12px; text-align: center;">
                                    <strong style="display: block; font-size: 1.35rem; font-weight: 800; color: var(--clr-primary); line-height: 1.1;">5.000+</strong>
                                    <span style="font-size: 0.76rem; color: var(--clr-navy); font-weight: 600;">ca cấy ghép & phục hình Implant</span>
                                </div>
                                <div style="background: rgba(4, 147, 241, 0.06); border: 1px solid rgba(4, 147, 241, 0.2); padding: 12px 8px; border-radius: 12px; text-align: center;">
                                    <strong style="display: block; font-size: 1.35rem; font-weight: 800; color: var(--clr-primary); line-height: 1.1;">2.200+</strong>
                                    <span style="font-size: 0.76rem; color: var(--clr-navy); font-weight: 600;">ca phục hình răng sứ thẩm mỹ</span>
                                </div>
                                <div style="background: rgba(4, 147, 241, 0.06); border: 1px solid rgba(4, 147, 241, 0.2); padding: 12px 8px; border-radius: 12px; text-align: center;">
                                    <strong style="display: block; font-size: 1.35rem; font-weight: 800; color: var(--clr-primary); line-height: 1.1;">1.200+</strong>
                                    <span style="font-size: 0.76rem; color: var(--clr-navy); font-weight: 600;">ca điều trị cười hở lợi</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid var(--clr-border); border-bottom: 1px solid var(--clr-border); padding: 16px 0; margin: 0;">
                        <ul style="list-style: none; font-size: 0.88rem; color: var(--clr-text); display: flex; flex-direction: column; gap: 10px; padding: 0; margin: 0;">
                            <li style="display: flex; align-items: flex-start; gap: 10px; margin: 0;">
                                <i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-top: 3px;"></i> 
                                <span><strong>ĐÀO TẠO CHUYÊN MÔN:</strong> Tốt nghiệp chính quy Bác sĩ chuyên khoa Răng Hàm Mặt, nhận chứng chỉ Cấy ghép nha khoa (Bệnh viện RHM Trung ương) và Chỉnh nha nâng cao (Bệnh viện Trung ương Huế).</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 10px; margin: 0;">
                                <i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-top: 3px;"></i> 
                                <span><strong>HOẠT ĐỘNG CHUYÊN MÔN QUỐC TẾ:</strong> Thành viên chính thức ICOI (Hiệp hội Implant Thế giới) và ITI (Hiệp hội Implant Quốc tế).</span>
                            </li>
                            <li style="display: flex; align-items: flex-start; gap: 10px; margin: 0;">
                                <i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-top: 3px;"></i> 
                                <span><strong>TRAO ĐỔI & CHIA SẺ CHUYÊN MÔN:</strong> Đài Phát thanh & Truyền hình Vĩnh Long phỏng vấn về ứng dụng công nghệ trong cấy ghép Implant (2023); trao đổi chuyên môn cùng Dr. Gilles P. Chaumanet.</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div style="text-align: left;">
                        <a href="#thanh-toan" class="btn btn-primary btn-booking" style="display: inline-block;">Đặt lịch tư vấn cùng Bác sĩ Minh <i class="fa-solid fa-calendar-check" style="margin-left: 8px;"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── 7. SECTION 7: CTA ĐĂNG KÝ GÓI CHĂM SÓC (ROW 18) ─── -->
    <section class="pkg-cta-strip">
        <div class="container">
            <h2 class="reveal">CHỦ ĐỘNG CHĂM SÓC RĂNG MIỆNG TOÀN DIỆN</h2>
            <p class="reveal reveal-delay-1">
                Đừng chờ đến khi răng miệng xuất hiện vấn đề mới bắt đầu quan tâm. Lựa chọn gói chăm sóc phù hợp với nhu cầu của bạn và gia đình &ndash; bắt đầu hành trình duy trì nụ cười khỏe đẹp ngay hôm nay.
            </p>
            <div class="reveal reveal-delay-2">
                <a href="#thanh-toan" class="btn-cta-gold">
                    <i class="fa-solid fa-shield-halved"></i> MUA GÓI NGAY HÔM NAY
                </a>
            </div>
        </div>
    </section>

    <!-- ─── 8. SECTION 8: TRANG THANH TOÁN AN TOÀN (ROW 19, 20) ─── -->
    <section class="checkout-section" id="thanh-toan">
        <div class="container">
            <!-- Chuẩn Section Header Flora theo đúng File Excel -->
            <div class="section-header center reveal" style="margin-bottom: 45px;">
                <div style="display: flex; justify-content: center; margin-bottom: 16px;">
                    <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="Nha Khoa Flora" style="max-height: 40px; width: auto; display: block;" />
                </div>
                <h2>THANH TOÁN <span class="gradient-text">AN TOÀN</span></h2>
                <p>Lựa chọn &amp; hoàn tất đăng ký gói dịch vụ của bạn!</p>
            </div>

            <div class="checkout-box-main reveal">
                <form id="checkoutForm" onsubmit="event.preventDefault(); return handleCheckoutSubmit(event);">
                    <div class="checkout-grid">
                        <!-- Left: Package Selection & Customer Info -->
                        <div>
                            <!-- 1. GÓI DỊCH VỤ -->
                            <div class="checkout-heading-step">
                                <span class="step-num-badge">1</span>
                                <span>GÓI DỊCH VỤ</span>
                            </div>

                            <div class="pkg-select-cards-row">
                                <!-- Card Care Plus -->
                                <label class="pkg-select-card selected" id="cardOptionCarePlus" onclick="selectCheckoutPackage('careplus')">
                                    <div class="pkg-card-bg-avatar" style="background-image: url('<?php echo flora_asset('care_plus_2.webp'); ?>');"></div>
                                    <input type="radio" name="selectedPackage" value="careplus" checked />
                                    <div class="pkg-card-content-wrap">
                                        <div class="pkg-select-header-flex">
                                            <img src="<?php echo flora_asset('care_plus_2.webp'); ?>" alt="Gói Care Plus" class="pkg-select-avatar-thumb" />
                                            <div>
                                                <span class="pkg-select-tag-pill">Chăm Sóc Định Kỳ</span>
                                                <div class="pkg-select-name">GÓI CARE PLUS</div>
                                            </div>
                                        </div>
                                        <div class="pkg-select-strike">2.500.000 VNĐ</div>
                                        <div class="pkg-select-price">999.000 VNĐ <span style="font-size: 0.72rem; font-weight: 600; color: #64748b;">/năm</span></div>
                                        <div class="pkg-select-note">Áp dụng tối đa 4 thành viên gia đình sử dụng trong 1 năm.</div>
                                    </div>
                                </label>

                                <!-- Card White Up -->
                                <label class="pkg-select-card" id="cardOptionWhiteUp" onclick="selectCheckoutPackage('whiteup')">
                                    <div class="pkg-card-bg-avatar" style="background-image: url('<?php echo flora_asset('flora_white_up_2.webp'); ?>');"></div>
                                    <input type="radio" name="selectedPackage" value="whiteup" />
                                    <div class="pkg-card-content-wrap">
                                        <div class="pkg-select-header-flex">
                                            <img src="<?php echo flora_asset('flora_white_up_2.webp'); ?>" alt="Gói Flora White Up" class="pkg-select-avatar-thumb" />
                                            <div>
                                                <span class="pkg-select-tag-pill">Tẩy Trắng Chuyên Sâu</span>
                                                <div class="pkg-select-name">GÓI FLORA WHITE UP</div>
                                            </div>
                                        </div>
                                        <div class="pkg-select-strike">3.000.000 VNĐ</div>
                                        <div class="pkg-select-price">1.999.000 VNĐ <span style="font-size: 0.72rem; font-weight: 600; color: #64748b;">/gói</span></div>
                                        <div class="pkg-select-note">Làm sạch & Tẩy trắng răng chuyên sâu Plasma tại nha khoa.</div>
                                    </div>
                                </label>
                            </div>

                            <!-- 2. THÔNG TIN NGƯỜI SỞ HỮU GÓI -->
                            <div class="checkout-heading-step" style="margin-top: 26px;">
                                <span class="step-num-badge">2</span>
                                <span>THÔNG TIN NGƯỜI SỞ HỮU GÓI</span>
                            </div>

                            <div class="checkout-form-group">
                                <label class="checkout-label" for="custName">Họ và tên <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="custName" class="checkout-input" placeholder="Ví dụ: Nguyễn Văn An" required />
                            </div>

                            <div class="checkout-form-group">
                                <label class="checkout-label" for="custPhone">Số điện thoại di động <span style="color:#ef4444;">* (Bắt buộc đúng để xác thực chuyển khoản)</span></label>
                                <input type="tel" id="custPhone" class="checkout-input" placeholder="Ví dụ: 0912 345 678" maxlength="11" required autocomplete="tel" />
                                <div id="phoneErrorMsg" style="color: #ef4444; font-size: 0.8rem; margin-top: 4px; display: none; font-weight: 600;"></div>
                                <span style="font-size: 0.76rem; color: #64748b; margin-top: 3px; display: block;">* Nhập số di động 10 số (03, 05, 07, 08, 09) để đối soát tự động khi chuyển khoản MoMo / Ngân hàng.</span>
                            </div>

                            <input type="hidden" id="orderIdempotencyKey" value="" />

                            <div class="checkout-form-group">
                                <label class="checkout-label" for="custEmail">Địa chỉ email <span style="color:#ef4444;">*</span></label>
                                <input type="email" id="custEmail" class="checkout-input" placeholder="Ví dụ: nguyenvana@gmail.com" required autocomplete="email" />
                                <span style="font-size: 0.76rem; color: #64748b; margin-top: 3px; display: block;">* Email nhận hóa đơn điện tử &amp; mã kích hoạt gói dịch vụ.</span>
                            </div>

                            <div class="checkout-form-group">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.92rem; color: #334155; font-weight: 600;">
                                    <input type="checkbox" id="needConsult" checked style="width: 18px; height: 18px; accent-color: var(--clr-primary);" />
                                    <span>Cần hỗ trợ tư vấn thêm trước khi đến phòng khám</span>
                                </label>
                            </div>

                            <!-- Lưu ý theo file Excel: Gói Care Plus áp dụng tối đa 4 thành viên -->
                            <div class="checkout-notice-box" id="carePlusNotice">
                                <i class="fa-solid fa-circle-info" style="font-size: 1.15rem; flex-shrink: 0; margin-top: 2px; color: var(--clr-primary);"></i>
                                <div>
                                    <strong>Lưu ý về Gói CARE PLUS:</strong> Áp dụng tối đa cho 4 thành viên gia đình. Người sở hữu gói có thể bổ sung hoặc cập nhật thông tin từng thành viên khi đến kích hoạt tại cơ sở Nha Khoa Flora.
                                </div>
                            </div>
                        </div>

                        <!-- Right: Order Summary (Row 20) -->
                        <div>
                            <div class="order-summary-card">
                                <div class="order-summary-title">TÓM TẮT ĐƠN HÀNG</div>

                                <!-- Active Package Display -->
                                <div class="selected-pkg-preview" id="summaryPkgBox">
                                    <div class="summary-pkg-header-badge">
                                        <i class="fa-solid fa-sparkles"></i> GÓI ĐANG CHỌN
                                    </div>
                                    <div class="summary-pkg-inner">
                                        <img id="summaryPkgThumb" src="<?php echo flora_asset('care_plus_2.webp'); ?>" alt="Gói đang chọn" class="summary-pkg-thumb-img" />
                                        <div class="summary-pkg-info">
                                            <div class="selected-pkg-name" id="summaryPkgName">GÓI CARE PLUS</div>
                                            <div class="selected-pkg-desc" id="summaryPkgDesc">Chăm sóc răng miệng định kỳ cho gia đình lên đến 4 thành viên (1 năm).</div>
                                            <div class="summary-pkg-price-pill" id="summaryPkgBadgePrice">999.000 VNĐ / năm</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Voucher Code Input -->
                                <div class="voucher-input-group">
                                    <input type="text" id="voucherCode" class="voucher-input" placeholder="Mã giới thiệu / Ưu đãi" />
                                    <button type="button" class="voucher-btn" onclick="applyVoucher()">Áp dụng</button>
                                </div>
                                <div id="voucherMsg" style="font-size: 0.8rem; margin-top: -14px; margin-bottom: 12px; display: none;"></div>

                                <!-- Price Breakdown -->
                                <div class="summary-price-breakdown">
                                    <div class="summary-price-row">
                                        <span>Giá gốc:</span>
                                        <span id="summaryOriginalPrice" style="color: #0f172a; font-weight: 600;">2.500.000 VNĐ</span>
                                    </div>
                                    <div class="summary-price-row" id="summaryDiscountRow" style="display: none;">
                                        <span id="summaryDiscountLabel" style="color: #10b981; font-weight: 700;">Ưu đãi:</span>
                                        <span id="summaryDiscount" style="color: #10b981; font-weight: 700;">-0 VNĐ</span>
                                    </div>
                                    <div class="summary-price-row">
                                        <span>Tạm tính:</span>
                                        <span id="summarySubtotal" style="font-weight: 600;">2.500.000 VNĐ</span>
                                    </div>
                                    <div class="summary-price-row total">
                                        <span>TỔNG CỘNG:</span>
                                        <span id="summaryTotal">2.500.000 VNĐ</span>
                                    </div>
                                </div>

                                <button type="submit" class="btn-confirm-order">
                                    <i class="fa-solid fa-lock"></i> XÁC NHẬN THANH TOÁN
                                </button>

                                <div class="security-badge">
                                    <i class="fa-solid fa-shield-check" style="color: #10b981;"></i>
                                    <span>Thông tin được bảo mật an toàn 100% &bull; VietQR Napas247</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- ─── 9. MODAL THANH TOÁN ĐƠN HÀNG (VIETQR ACB & AWAITING CONFIRMATION) ─── -->
    <div class="payment-modal-overlay" id="paymentModal">
        <div class="payment-modal-card">
            <div class="payment-modal-header">
                <div class="modal-header-left">
                    <span class="modal-header-kicker">CỔNG THANH TOÁN VIETQR FLORA</span>
                    <h3 id="modalHeaderTitle">THANH TOÁN ĐƠN HÀNG</h3>
                </div>
                <div class="modal-header-right">
                    <div class="modal-security-badges">
                        <span class="sec-badge-item" title="Bảo mật đường truyền 256-bit SSL chuẩn Ngân hàng">
                            <i class="fa-solid fa-shield-halved" style="color: #16a34a;"></i>
                            <span class="sec-badge-text">Bảo Mật SSL</span>
                        </span>
                        <span class="sec-badge-item" title="Cổng chuyển khoản nhanh 24/7 liên ngân hàng Napas">
                            <i class="fa-solid fa-lock" style="color: #0033a3;"></i>
                            <span class="sec-badge-text">Napas247</span>
                        </span>
                        <a href="javascript:void(0)" onclick="openFloraTermsModal()" class="sec-badge-item sec-badge-link" title="Xem cam kết & điều khoản bảo mật y khoa">
                            <i class="fa-solid fa-file-shield" style="color: #0284c7;"></i>
                            <span class="sec-badge-text">Điều Khoản & Bảo Mật</span>
                        </a>
                    </div>
                    <button type="button" class="payment-modal-close" onclick="closePaymentModal()" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </div>

            <!-- 1. SUCCESS CELEBRATION VIEW (HIỂN THỊ KHI TƯ VẤN VIÊN DUYỆT THÀNH CÔNG) -->
            <div class="payment-success-view" id="paymentSuccessView">
                <div class="success-icon-wrap">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span style="font-size: 0.85rem; font-weight: 800; color: #16a34a; letter-spacing: 1px; text-transform: uppercase;">XÁC NHẬN GIAO DỊCH THÀNH CÔNG</span>
                <h3 style="font-family: var(--font-title); font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 6px 0 12px;">ĐÃ KÍCH HOẠT GÓI DỊCH VỤ!</h3>
                <p style="color: #64748b; font-size: 0.95rem; max-width: 540px; margin: 0 auto 20px; line-height: 1.5;">
                    Cảm ơn Quý khách <strong id="successCustName" style="color: #0f172a;"></strong>! Hệ thống đã ghi nhận thanh toán cho đơn hàng <strong id="successOrderCode" style="color: #0033a3;"></strong>.
                </p>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px 24px; max-width: 500px; margin: 0 auto 24px; text-align: left;">
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.88rem;">
                        <span style="color: #64748b;">Gói dịch vụ:</span>
                        <strong id="successPkgName" style="color: #0f172a;">Gói Care Plus</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.88rem;">
                        <span style="color: #64748b;">Số tiền thanh toán:</span>
                        <strong id="successAmount" style="color: #16a34a; font-size: 1.05rem;">999.000 VNĐ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.88rem;">
                        <span style="color: #64748b;">Số điện thoại xác thực:</span>
                        <strong id="successCustPhone" style="color: #0f172a;">0912 345 678</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.88rem;">
                        <span style="color: #64748b;">Tài khoản thụ hưởng:</span>
                        <strong style="color: #0033a3; font-family: monospace;">ACB - 77779268 (CN HOA HUNG)</strong>
                    </div>
                </div>

                <p style="font-size: 0.85rem; color: #0284c7; background: #f0f9ff; border: 1px solid #bae6fd; padding: 10px 16px; border-radius: 8px; max-width: 500px; margin: 0 auto 24px;">
                    <i class="fa-solid fa-circle-info"></i> Chuyên viên CSKH Flora sẽ gọi đến số điện thoại của Quý khách trong ít phút để hoàn tất lưu trữ hồ sơ và xác nhận lịch hẹn khám.
                </p>

                <button type="button" class="btn btn-primary" onclick="closePaymentModal()" style="padding: 12px 36px; font-weight: 700;">
                    <i class="fa-solid fa-check"></i> Hoàn Tất & Đóng Cửa Sổ
                </button>
            </div>

            <!-- 2. AWAITING CONFIRMATION VIEW (HIỂN THỊ KHI KHÁCH BẤM "TÔI ĐÃ CHUYỂN KHOẢN") -->
            <div class="payment-awaiting-view" id="paymentAwaitingView">
                <div class="awaiting-icon-wrap">
                    <div class="awaiting-pulse-ring"></div>
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <span style="font-size: 0.8rem; font-weight: 800; color: #0284c7; letter-spacing: 0.8px; text-transform: uppercase;">ĐANG CHỜ XÁC NHẬN TỪ NHA KHOA FLORA</span>
                <h3 style="font-family: var(--font-title); font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 4px 0 8px;">YÊU CẦU ĐÃ ĐƯỢC TIẾP NHẬN!</h3>
                <p style="color: #64748b; font-size: 0.88rem; max-width: 520px; margin: 0 auto 12px; line-height: 1.45;">
                    Cảm ơn Quý khách <strong id="awaitingCustName" style="color: #0f172a;"></strong>! Hệ thống Flora đã ghi nhận thông tin đăng ký cho gói dịch vụ <strong id="awaitingOrderCode" style="color: #0033a3;"></strong>.
                </p>

                <!-- Tóm tắt gói dịch vụ đang chờ -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 18px; max-width: 520px; margin: 0 auto 12px; text-align: left;">
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.86rem;">
                        <span style="color: #64748b;">Mã gói dịch vụ:</span>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <strong id="awaitingOrderCodeText" style="color: #0033a3; font-family: monospace; font-size: 0.95rem;">FLORA...</strong>
                            <button type="button" class="btn-copy-icon" onclick="copyTextDirect(this, document.getElementById('awaitingOrderCodeText').innerText, 'Mã gói dịch vụ')" title="Sao chép mã gói"><i class="fa-regular fa-copy"></i></button>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.86rem;">
                        <span style="color: #64748b;">Gói dịch vụ:</span>
                        <strong id="awaitingPkgName" style="color: #0f172a;">Gói Care Plus</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.86rem;">
                        <span style="color: #64748b;">Số tiền thanh toán:</span>
                        <strong id="awaitingAmount" style="color: #0033a3; font-size: 1.02rem;">999.000 VNĐ</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px dashed #e2e8f0; font-size: 0.86rem;">
                        <span style="color: #64748b;">Cú pháp chuyển khoản:</span>
                        <strong id="awaitingSyntax" style="color: #b45309; font-family: monospace;">FLORA...</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 4px 0; font-size: 0.86rem;">
                        <span style="color: #64748b;">Tài khoản thụ hưởng:</span>
                        <strong style="color: #0f172a;">77779268 - ACB (CN HOA HUNG)</strong>
                    </div>
                </div>

                <!-- CAM KẾT UY TÍN MINH BẠCH Y KHOA -->
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 10px 16px; max-width: 520px; margin: 0 auto 12px; text-align: left;">
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <i class="fa-solid fa-shield-halved" style="color: #16a34a; font-size: 1.15rem; margin-top: 2px;"></i>
                        <div>
                            <strong style="color: #166534; font-size: 0.85rem; display: block; margin-bottom: 2px;">CAM KẾT MINH BẠCH & BẢO ĐẢM QUYỀN LỢI 100%</strong>
                            <p style="color: #14532d; font-size: 0.8rem; margin: 0; line-height: 1.4;">
                                Giao dịch được bảo chứng pháp lý bởi Công ty Cổ phần Flora Dental Care. Tư vấn viên đang kiểm tra sao kê ngân hàng và sẽ kích hoạt hồ sơ trong vòng <strong>5 - 15 phút</strong>. Quý khách hoàn toàn yên tâm quyền lợi được đảm bảo tuyệt đối.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- KHU VỰC GỬI KÈM MINH CHỨNG HÌNH ẢNH (TÙY CHỌN, HOÀN TOÀN KHÔNG BẮT BUỘC) -->
                <div class="proof-upload-box" style="background: #f8fafc; border: 1.5px dashed #93c5fd; border-radius: 12px; padding: 12px 16px; max-width: 520px; margin: 0 auto 12px; text-align: center;">
                    <div style="margin-bottom: 6px;">
                        <span style="display: inline-flex; align-items: center; gap: 5px; background: #e0f2fe; color: #0284c7; padding: 3px 12px; border-radius: 9999px; font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-circle-check"></i> TÙY CHỌN - HOÀN TOÀN KHÔNG BẮT BUỘC
                        </span>
                    </div>
                    <strong style="font-size: 0.88rem; color: #0f172a; display: block; margin-bottom: 4px;">
                        <i class="fa-solid fa-camera" style="color: #0033a3; margin-right: 4px;"></i> Gửi kèm ảnh biên lai chuyển khoản (Nếu muốn)
                    </strong>
                    <p style="color: #64748b; font-size: 0.78rem; margin: 0 0 10px; line-height: 1.4;">
                        Yêu cầu của Quý khách <strong>đã được hệ thống ghi nhận thành công</strong>! Quý khách có thể bấm <strong>"Đóng & Lưu Mã Gói"</strong> bên dưới mà không cần gửi ảnh. Nếu thuận tiện, đính kèm bill chụp màn hình sẽ giúp tư vấn viên kích hoạt siêu tốc trong <strong>3 phút</strong>.
                    </p>

                    <input type="file" id="proofFileInput" accept="image/*" style="display: none;" onchange="handleProofFileSelected(this)" />
                    <label for="proofFileInput" class="btn-choose-proof">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Chọn ảnh chụp màn hình bill (Tùy chọn)
                    </label>

                    <div id="proofPreviewWrap" style="display: none;" class="proof-preview-wrap">
                        <img id="proofPreviewImg" src="" alt="Minh chứng" class="proof-preview-img" />
                        <div style="flex: 1; min-width: 0;">
                            <div id="proofFileName" style="font-size: 0.82rem; font-weight: 700; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"></div>
                            <div id="proofFileSize" style="font-size: 0.75rem; color: #64748b;"></div>
                        </div>
                        <button type="button" id="btnUploadProofAction" class="btn btn-primary" onclick="submitProofUpload()" style="padding: 6px 14px; font-size: 0.82rem;">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Gửi ảnh
                        </button>
                    </div>

                    <div id="proofUploadStatus" style="margin-top: 8px; font-size: 0.82rem; font-weight: 700; display: none;"></div>
                </div>

                <!-- LIVE POLLING STATUS -->
                <div style="display: inline-flex; align-items: center; gap: 8px; background: #f0f9ff; border: 1px solid #bae6fd; padding: 6px 14px; border-radius: 9999px; font-size: 0.8rem; color: #0369a1; margin-bottom: 14px;">
                    <span class="pulse-dot"></span>
                    <span>Hệ thống đang tự động kiểm tra sao kê ACB thời gian thực...</span>
                </div>

                <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary" onclick="closePaymentModal()" style="padding: 9px 22px; font-size: 0.86rem; font-weight: 600;">
                        Đóng & Lưu Mã Gói
                    </button>
                    <button type="button" class="btn btn-primary" onclick="openOrderTrackingModal(currentOrderCode)" style="padding: 9px 22px; font-size: 0.86rem; font-weight: 700;">
                        <i class="fa-solid fa-magnifying-glass"></i> Kiểm Tra Tiến Độ Gói
                    </button>
                </div>
            </div>

            <!-- 3. PAYMENT ACTIVE VIEW (CHUYỂN KHOẢN NGÂN HÀNG ACB DUY NHẤT 1 TÀI KHOẢN) -->
            <div class="payment-modal-body" id="paymentActiveView">
                <div class="payment-tab-panel active" style="display: grid; grid-template-columns: 320px 1fr; gap: 20px;">
                    <!-- Left: VietQR ACB -->
                    <div class="payment-qr-hero bank-theme">
                        <div class="qr-badge-header" style="color: #0033a3;">
                            <i class="fa-solid fa-qrcode"></i> QUÉT MÃ VIETQR ACB
                        </div>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 2px 0 6px;">
                            Quét bằng mọi ứng dụng Mobile Banking
                        </p>

                        <div class="qr-hero-frame">
                            <img id="bankQrImg" src="" alt="Mã VietQR Thanh Toán Flora" class="qr-hero-image" />
                        </div>

                        <div class="qr-timer-pill">
                            <i class="fa-regular fa-clock"></i>
                            <span>Thời gian giữ ưu đãi: <strong id="bankTimer">14:59</strong></span>
                        </div>

                        <div class="live-status-pill">
                            <span class="pulse-dot"></span>
                            <span>Mở App Ngân hàng quét mã chuyển khoản</span>
                        </div>
                    </div>

                    <!-- Right: Chi tiết Ngân Hàng ACB -->
                    <div class="payment-invoice-card">
                        <div class="invoice-card-header">
                            <strong>THÔNG TIN CHUYỂN KHOẢN NGÂN HÀNG</strong>
                            <span class="badge-verified-phone"><i class="fa-solid fa-circle-check"></i> Xác Thực Qua SĐT</span>
                        </div>

                        <!-- Chi tiết chuyển khoản ngân hàng ACB (Basic, Tinh Gọn, Đơn Giản, Icon Copy) -->
                        <div class="invoice-details-list">
                            <div class="invoice-detail-row">
                                <span class="invoice-row-label">Số tiền thanh toán:</span>
                                <span class="invoice-row-val">
                                    <strong id="modalBankAmount" class="val-highlight-amount">999.000 VNĐ</strong>
                                    <button type="button" class="btn-copy-icon" onclick="copyTransferAmount(this)" title="Sao chép số tiền">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="invoice-detail-row">
                                <div>
                                    <span class="invoice-row-label">Nội dung chuyển khoản:</span>
                                    <small style="display: block; font-size: 0.72rem; color: #dc2626; font-weight: 600;">(Bắt buộc chính xác)</small>
                                </div>
                                <span class="invoice-row-val">
                                    <strong id="modalBankSyntax" class="val-highlight-syntax">FLORA 0378859736 89214</strong>
                                    <button type="button" class="btn-copy-icon" onclick="copyTransferSyntax(this)" title="Sao chép nội dung">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="invoice-detail-row">
                                <span class="invoice-row-label">Ngân hàng thụ hưởng:</span>
                                <span class="invoice-row-val">
                                    <span>Ngân hàng TMCP Á Châu (ACB)</span>
                                    <button type="button" class="btn-copy-icon" onclick="copyTextDirect(this, 'Ngân hàng TMCP Á Châu (ACB)', 'Ngân hàng')" title="Sao chép tên ngân hàng">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="invoice-detail-row">
                                <span class="invoice-row-label">Số tài khoản:</span>
                                <span class="invoice-row-val">
                                    <strong class="val-highlight-stk">77779268</strong>
                                    <button type="button" class="btn-copy-icon" onclick="copyTextDirect(this, '77779268', 'Số tài khoản')" title="Sao chép số tài khoản">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="invoice-detail-row">
                                <span class="invoice-row-label">Tên tài khoản:</span>
                                <span class="invoice-row-val">
                                    <span>CONG TY CO PHAN FLORA DENTAL CARE</span>
                                    <button type="button" class="btn-copy-icon" onclick="copyTextDirect(this, 'CONG TY CO PHAN FLORA DENTAL CARE', 'Tên tài khoản')" title="Sao chép tên tài khoản">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </span>
                            </div>

                            <div class="invoice-detail-row">
                                <span class="invoice-row-label">Chi nhánh:</span>
                                <span class="invoice-row-val">
                                    <span style="color: #64748b;">ACB – CN HOA HUNG</span>
                                </span>
                            </div>
                        </div>

                        <div class="invoice-notice-bar">
                            <i class="fa-solid fa-triangle-exclamation"></i> Quý khách chuyển khoản xong, vui lòng bấm nút <strong>"Tôi đã chuyển khoản"</strong> bên dưới để tạo đơn và nhận xác nhận từ Flora.
                        </div>
                    </div>
                </div>
            </div>

            <div class="payment-modal-footer" id="paymentModalFooter">
                <button type="button" class="btn-cancel-trans" onclick="closePaymentModal()">Huỷ giao dịch</button>
                <button type="button" class="btn-paid-success" id="btnConfirmTransferred" onclick="handleCustomerConfirmedTransfer()">
                    <i class="fa-solid fa-circle-check"></i> Tôi đã chuyển khoản
                </button>
            </div>
        </div>
    </div>

    <!-- ─── 9b. MODAL TRA CỨU ĐƠN HÀNG FLORA (THEO MÃ HOẶC SĐT) ─── -->
    <div class="tracking-modal-overlay" id="orderTrackingModal">
        <div class="tracking-modal-card">
            <div style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); color: #fff; padding: 20px 24px; position: relative;">
                <button type="button" onclick="closeOrderTrackingModal()" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <span style="font-size: 0.78rem; font-weight: 700; text-transform: uppercase; color: #bae6fd; letter-spacing: 0.5px;">NHA KHOA FLORA</span>
                <h3 style="margin: 4px 0 0; font-size: 1.25rem; font-weight: 800; color: #fff;">
                    🔍 TRA CỨU TRẠNG THÁI ĐƠN HÀNG
                </h3>
            </div>

            <div style="padding: 24px;">
                <p style="font-size: 0.88rem; color: #64748b; margin: 0 0 14px;">
                    Nhập <strong>Mã đơn hàng (ví dụ: FLORA89214)</strong> hoặc <strong>Số điện thoại</strong> đăng ký để kiểm tra tiến độ kích hoạt gói dịch vụ:
                </p>

                <div style="display: flex; gap: 8px; margin-bottom: 18px;">
                    <input type="text" id="orderTrackingInput" placeholder="Ví dụ: FLORA89214 hoặc 0912345678" style="flex: 1; padding: 11px 14px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.92rem;" onkeyup="if(event.key==='Enter') executeOrderLookup()" />
                    <button type="button" id="btnExecuteLookup" class="btn btn-primary" onclick="executeOrderLookup()" style="padding: 11px 20px; font-weight: 700; white-space: nowrap;">
                        <i class="fa-solid fa-magnifying-glass"></i> Tra Cứu
                    </button>
                </div>

                <!-- Vùng hiển thị kết quả tra cứu -->
                <div id="orderTrackingResult" style="display: none;"></div>
            </div>
        </div>
    </div>

    <!-- ─── 9c. MODAL ĐIỀU KHOẢN & BẢO MẬT GIAO DỊCH ─── -->
    <div class="terms-modal-overlay" id="floraTermsModal">
        <div class="terms-modal-card">
            <div class="terms-modal-header">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                        <i class="fa-solid fa-file-shield"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1.08rem; color: #0f172a; font-weight: 800;">Chính Sách & Điều Khoản Bảo Mật</h4>
                        <span style="font-size: 0.76rem; color: #64748b;">Nha Khoa Flora cam kết bảo đảm quyền lợi tối đa cho khách hàng</span>
                    </div>
                </div>
                <button type="button" class="payment-modal-close" onclick="closeFloraTermsModal()" aria-label="Đóng"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="terms-modal-body">
                <div class="term-point">
                    <i class="fa-solid fa-circle-check" style="color: #16a34a;"></i>
                    <div>
                        <strong>1. Tài khoản thụ hưởng pháp nhân minh bạch</strong>
                        <p>Mọi giao dịch thanh toán chuyển khoản đều chuyển trực tiếp vào tài khoản ACB của <strong>CÔNG TY CỔ PHẦN FLORA DENTAL CARE</strong> (STK: 77779268), có đầy đủ hóa đơn GTGT y tế và biên lai điện tử.</p>
                    </div>
                </div>
                <div class="term-point">
                    <i class="fa-solid fa-shield-halved" style="color: #0284c7;"></i>
                    <div>
                        <strong>2. Bảo mật hồ sơ bệnh án chuẩn Bộ Y Tế</strong>
                        <p>Thông tin cá nhân, số điện thoại đăng ký và bệnh án điều trị của khách hàng được mã hóa 256-bit SSL, cam kết bảo mật tuyệt đối và không chia sẻ cho bất kỳ bên thứ ba nào.</p>
                    </div>
                </div>
                <div class="term-point">
                    <i class="fa-solid fa-rotate-left" style="color: #ea580c;"></i>
                    <div>
                        <strong>3. Chính sách chuyển nhượng & hoàn tiền minh bạch</strong>
                        <p>Khách hàng được quyền chuyển nhượng thẻ quà tặng/gói dịch vụ cho người thân trong gia đình hoặc bảo lưu ưu đãi trong vòng 30 ngày nếu chưa kích hoạt điều trị.</p>
                    </div>
                </div>
            </div>
            <div style="padding: 14px 20px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-primary" onclick="closeFloraTermsModal()" style="padding: 8px 24px; font-size: 0.86rem; font-weight: 700;">
                    Đã Hiểu & Đóng
                </button>
            </div>
        </div>
    </div>

    <!-- ─── FLOATING BUTTON TRA CỨU ĐƠN HÀNG ─── -->
    <button type="button" class="btn-floating-tracking" onclick="openOrderTrackingModal()" title="Tra cứu tiến độ gói dịch vụ">
        <i class="fa-solid fa-magnifying-glass"></i> Tra cứu gói dịch vụ
    </button>



    <!-- ─── 10. REGISTRATION POPUP MODAL (ROW 2, 3, 4) ─── -->
    <div class="reg-modal-overlay" id="regModal">
        <div class="reg-modal-card">
            <div style="background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%); color: #fff; padding: 24px; position: relative;">
                <button type="button" onclick="closeRegModal()" style="position: absolute; top: 16px; right: 16px; background: rgba(255,255,255,0.2); border: none; color: #fff; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #bae6fd; letter-spacing: 0.5px;">NHA KHOA FLORA</div>
                <h3 style="font-family: var(--font-title); font-size: 1.35rem; font-weight: 800; margin: 4px 0 6px; color: #fff;">
                    ĐĂNG KÝ GÓI CHĂM SÓC RĂNG MIỆNG
                </h3>
                <p style="font-size: 0.88rem; color: #e0f2fe; margin: 0;">
                    Chọn gói phù hợp với nhu cầu và nhận tư vấn chi tiết từ Flora.
                </p>
            </div>

            <div style="padding: 24px;">
                <!-- 2 Khối thông tin riêng biệt làm nổi bật tên gói và mức giá (Row 3 - Chuẩn Flora Blue) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: center;">
                        <strong style="color: #0033a3; font-size: 0.95rem; display: block;">GÓI CARE PLUS</strong>
                        <span style="font-size: 0.78rem; text-decoration: line-through; color: #94a3b8;">2.500.000Đ</span>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #0033a3;">999.000Đ<span style="font-size: 0.75rem;">/năm</span></div>
                        <p style="font-size: 0.74rem; color: #475569; margin: 4px 0 0; line-height: 1.3;">Chăm sóc định kỳ gia đình đến 4 thành viên.</p>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px; text-align: center;">
                        <strong style="color: #0033a3; font-size: 0.95rem; display: block;">GÓI FLORA WHITE UP</strong>
                        <span style="font-size: 0.78rem; text-decoration: line-through; color: #94a3b8;">3.000.000Đ</span>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #0033a3;">1.999.000Đ<span style="font-size: 0.75rem;">/gói</span></div>
                        <p style="font-size: 0.74rem; color: #475569; margin: 4px 0 0; line-height: 1.3;">Làm sạch & tẩy trắng răng chuyên sâu.</p>
                    </div>
                </div>

                <form onsubmit="handleQuickRegSubmit(event)">
                    <div style="margin-bottom: 12px;">
                        <input type="text" id="quickName" placeholder="Họ và tên của bạn *" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" />
                    </div>
                    <div style="margin-bottom: 12px;">
                        <input type="tel" id="quickPhone" placeholder="Số điện thoại liên hệ *" required pattern="[0-9]{10,11}" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box;" />
                    </div>
                    <div style="margin-bottom: 16px;">
                        <select id="quickService" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; box-sizing: border-box; background: #fff;">
                            <option value="Gói Care Plus (999.000Đ/năm)">GÓI CARE PLUS (999.000Đ/năm - 4 người)</option>
                            <option value="Gói Flora White Up (1.999.000Đ/gói)">GÓI FLORA WHITE UP (1.999.000Đ/gói)</option>
                            <option value="Cần tư vấn cả 2 gói">Tôi cần tư vấn thêm cả 2 gói</option>
                        </select>
                    </div>

                    <button type="submit" style="width: 100%; background: linear-gradient(135deg, #0493f1 0%, #0033a3 100%); color: #fff; border: none; padding: 14px; border-radius: 10px; font-weight: 800; font-size: 0.98rem; cursor: pointer;">
                        ĐĂNG KÝ NGAY
                    </button>
                    <p style="font-size: 0.78rem; color: #64748b; text-align: center; margin: 10px 0 0;">
                        Flora sẽ liên hệ xác nhận thông tin ngay khi nhận được đăng ký.
                    </p>
                </form>
            </div>
        </div>
    </div>



    <!-- Toast Notification -->
    <div id="toastAlert" class="toast-notify">
        <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
        <span id="toastMsg">Thành công!</span>
    </div>

    <!-- ─── JAVASCRIPT LOGIC ─── -->
    <script data-no-optimize="1">
        // Package Data Configuration
        const PACKAGES = {
            careplus: {
                id: 'careplus',
                name: 'GÓI CARE PLUS',
                desc: 'Chăm sóc răng miệng định kỳ cho gia đình lên đến 4 thành viên (1 năm).',
                originalPrice: 2500000,
                stdDiscount: 1501000,
                promoPrice: 999000,
                unit: 'VNĐ/năm',
                amountStr: '2.500.000 VNĐ',
                badgePrice: '2.500.000 VNĐ / năm',
                thumb: '<?php echo flora_asset('care_plus_2.webp'); ?>'
            },
            whiteup: {
                id: 'whiteup',
                name: 'GÓI FLORA WHITE UP',
                desc: 'Làm sạch và tẩy trắng răng chuyên sâu công nghệ Plasma tại nha khoa.',
                originalPrice: 3000000,
                stdDiscount: 1001000,
                promoPrice: 1999000,
                unit: 'VNĐ/gói',
                amountStr: '3.000.000 VNĐ',
                badgePrice: '3.000.000 VNĐ / gói',
                thumb: '<?php echo flora_asset('flora_white_up_2.webp'); ?>'
            }
        };

        let currentSelectedPkg = 'careplus';
        let currentOrderCode = '';
        let timerInterval = null;
        let appliedVoucherData = null; // Lưu trữ { valid, code, isKol, kolName, kolRef, discountLabel, discountAmount }

        function selectCheckoutPackage(pkgKey) {
            currentSelectedPkg = pkgKey;

            const cardCare = document.getElementById('cardOptionCarePlus');
            const cardWhite = document.getElementById('cardOptionWhiteUp');
            const noticeBox = document.getElementById('carePlusNotice');

            if (pkgKey === 'careplus') {
                cardCare.classList.add('selected');
                cardWhite.classList.remove('selected');
                noticeBox.style.display = 'flex';
                noticeBox.innerHTML = '<i class="fa-solid fa-circle-info" style="font-size: 1.15rem; flex-shrink: 0; margin-top: 2px; color: var(--clr-primary);"></i><div><strong>Lưu ý về Gói CARE PLUS:</strong> Áp dụng tối đa cho 4 thành viên gia đình. Người sở hữu gói có thể bổ sung hoặc cập nhật thông tin từng thành viên khi đến kích hoạt tại cơ sở Nha Khoa Flora.</div>';
                document.querySelector('input[name="selectedPackage"][value="careplus"]').checked = true;
            } else {
                cardWhite.classList.add('selected');
                cardCare.classList.remove('selected');
                noticeBox.style.display = 'flex';
                noticeBox.innerHTML = '<i class="fa-solid fa-circle-info" style="font-size: 1.15rem; flex-shrink: 0; margin-top: 2px; color: var(--clr-primary);"></i><div><strong>Lưu ý về Gói FLORA WHITE UP:</strong> Đã bao gồm cạo vôi, đánh bóng và tẩy trắng răng Plasma với Bác sĩ CKI. Thời hạn sử dụng linh hoạt đến hết ngày <strong>31/12/2026</strong>.</div>';
                document.querySelector('input[name="selectedPackage"][value="whiteup"]').checked = true;
            }

            updateOrderSummary();
        }

        function selectPackageAndCheckout(pkgKey) {
            selectCheckoutPackage(pkgKey);
            const el = document.getElementById('thanh-toan');
            if (el) {
                el.scrollIntoView({ behavior: 'smooth' });
            }
        }

        function updateOrderSummary() {
            const pkg = PACKAGES[currentSelectedPkg];
            const nameEl = document.getElementById('summaryPkgName');
            const descEl = document.getElementById('summaryPkgDesc');
            const thumbEl = document.getElementById('summaryPkgThumb');
            const badgeEl = document.getElementById('summaryPkgBadgePrice');

            if (nameEl) nameEl.innerText = pkg.name;
            if (descEl) descEl.innerText = pkg.desc;
            if (thumbEl && pkg.thumb) thumbEl.src = pkg.thumb;

            const orig = pkg.originalPrice;
            let discount = 0;
            let discountLabel = 'Ưu đãi:';

            if (appliedVoucherData && appliedVoucherData.valid) {
                if (appliedVoucherData.isKol) {
                    discount = pkg.stdDiscount; // 1.001.000đ cho White Up, 1.501.000đ cho Care Plus
                    discountLabel = 'Ưu đãi của ' + appliedVoucherData.kolName + ':';
                } else if (appliedVoucherData.discountAmount > 0) {
                    discount = Math.min(appliedVoucherData.discountAmount, orig);
                    discountLabel = appliedVoucherData.discountLabel || 'Ưu đãi Flora:';
                } else {
                    discount = pkg.stdDiscount;
                    discountLabel = appliedVoucherData.discountLabel || 'Ưu đãi Flora:';
                }
            }

            const total = Math.max(0, orig - discount);

            if (badgeEl) {
                if (discount > 0) {
                    badgeEl.innerText = total.toLocaleString('vi-VN') + ' VNĐ / ' + (currentSelectedPkg === 'careplus' ? 'năm' : 'gói');
                } else {
                    badgeEl.innerText = orig.toLocaleString('vi-VN') + ' VNĐ / ' + (currentSelectedPkg === 'careplus' ? 'năm' : 'gói');
                }
            }

            const origPriceEl = document.getElementById('summaryOriginalPrice');
            const discountRowEl = document.getElementById('summaryDiscountRow');
            const discountLabelEl = document.getElementById('summaryDiscountLabel');
            const discountValEl = document.getElementById('summaryDiscount');
            const subtotalEl = document.getElementById('summarySubtotal');
            const totalEl = document.getElementById('summaryTotal');

            if (origPriceEl) {
                origPriceEl.innerText = orig.toLocaleString('vi-VN') + ' VNĐ';
                origPriceEl.style.textDecoration = discount > 0 ? 'line-through' : 'none';
                origPriceEl.style.color = discount > 0 ? '#94a3b8' : '#0f172a';
            }

            if (discountRowEl) {
                if (discount > 0) {
                    discountRowEl.style.display = 'flex';
                    if (discountLabelEl) discountLabelEl.innerText = discountLabel;
                    if (discountValEl) discountValEl.innerText = '-' + discount.toLocaleString('vi-VN') + ' VNĐ';
                } else {
                    discountRowEl.style.display = 'none';
                }
            }

            if (subtotalEl) {
                subtotalEl.innerText = (discount > 0 ? total.toLocaleString('vi-VN') : orig.toLocaleString('vi-VN')) + ' VNĐ';
            }
            if (totalEl) {
                totalEl.innerText = total.toLocaleString('vi-VN') + ' VNĐ';
            }
        }

        async function applyVoucher(manualCode = '', isAuto = false) {
            const codeInput = document.getElementById('voucherCode');
            let code = manualCode ? manualCode.trim().toUpperCase() : (codeInput ? codeInput.value.trim().toUpperCase() : '');
            const msg = document.getElementById('voucherMsg');
            const btn = document.querySelector('.voucher-btn');
            const origBtnText = btn ? btn.innerText : 'Áp dụng';

            if (msg) msg.style.display = 'block';

            if (!code) {
                appliedVoucherData = null;
                if (codeInput) codeInput.style.borderColor = '';
                if (msg) {
                    msg.style.color = '#dc2626';
                    msg.innerText = 'Vui lòng nhập mã ưu đãi hoặc mã giới thiệu.';
                }
                updateOrderSummary();
                return;
            }

            if (btn && !isAuto) {
                btn.disabled = true;
                btn.innerText = '...';
            }

            const pkg = PACKAGES[currentSelectedPkg];
            const phone = (document.getElementById('custPhone') ? document.getElementById('custPhone').value : '');
            const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

            try {
                const formData = new FormData();
                formData.append('action', 'flora_validate_voucher');
                formData.append('voucher_code', code);
                formData.append('subtotal', pkg.originalPrice);
                formData.append('phone', phone);
                formData.append('package_id', currentSelectedPkg);

                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.success && res.data && res.data.valid) {
                    appliedVoucherData = {
                        valid: true,
                        code: code,
                        isKol: res.data.is_kol || false,
                        kolName: res.data.kol_name || '',
                        kolRef: res.data.kol_ref || code,
                        discountLabel: res.data.discount_label || (res.data.is_kol ? ('Ưu đãi của ' + res.data.kol_name) : 'Ưu đãi Flora'),
                        discountAmount: Number(res.data.discount_amount) || pkg.stdDiscount,
                        voucher: res.data.voucher
                    };

                    if (codeInput) {
                        codeInput.value = code;
                        codeInput.style.borderColor = '#10b981';
                    }

                    if (msg) {
                        msg.style.color = '#10b981';
                        msg.innerText = res.data.message || ('Áp dụng mã ' + code + ' thành công!');
                    }

                    if (!isAuto) {
                        showToast('🎉 ' + (res.data.message || 'Đã áp dụng mã ưu đãi thành công!'));
                    } else if (res.data.is_kol && res.data.kol_name) {
                        showToast('🎉 Đã kích hoạt ưu đãi độc quyền từ ' + res.data.kol_name + '!');
                    }

                    // Nếu mã thuộc về KOL: Lưu ref code vào cookie để đồng bộ tracking
                    if (res.data.is_kol && res.data.kol_ref) {
                        try {
                            const d = new Date();
                            d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
                            document.cookie = "flora_kol_ref=" + encodeURIComponent(res.data.kol_ref) + ";expires=" + d.toUTCString() + ";path=/";
                            localStorage.setItem('flora_kol_ref', res.data.kol_ref);
                        } catch(e) {}
                    }
                } else {
                    appliedVoucherData = null;
                    if (codeInput) {
                        codeInput.style.borderColor = '#dc2626';
                    }
                    if (msg) {
                        msg.style.color = '#dc2626';
                        msg.innerText = (res.data && res.data.message) ? res.data.message : 'Mã không hợp lệ hoặc đã hết lượt áp dụng.';
                    }
                }
            } catch (err) {
                // Fallback nếu mạng gặp trục trặc
                if (code === 'FLORA' || code === 'FLORA50' || code === 'TRIAN50' || code === 'FLORA100') {
                    appliedVoucherData = {
                        valid: true,
                        code: code,
                        isKol: false,
                        discountLabel: 'Ưu đãi Flora',
                        discountAmount: pkg.stdDiscount
                    };
                    if (codeInput) codeInput.style.borderColor = '#10b981';
                    if (msg) {
                        msg.style.color = '#10b981';
                        msg.innerText = 'Áp dụng mã thành công!';
                    }
                } else {
                    appliedVoucherData = null;
                    if (msg) {
                        msg.style.color = '#dc2626';
                        msg.innerText = 'Mã không hợp lệ hoặc đã hết lượt áp dụng.';
                    }
                }
            } finally {
                if (btn && !isAuto) {
                    btn.disabled = false;
                    btn.innerText = origBtnText;
                }
                updateOrderSummary();
            }
        }

        // ─── PHONE VALIDATION (CHUẨN 10 SỐ DI ĐỘNG VIỆT NAM) ───
        function validateVnPhone(phone) {
            if (!phone) return false;
            let clean = phone.replace(/[^0-9]/g, '');
            if (clean.startsWith('84') && clean.length === 11) {
                clean = '0' + clean.slice(2);
            }
            const regex = /^(03[2-9]|05[2689]|07[06-9]|08[1-9]|09[0-9])[0-9]{7}$/;
            return regex.test(clean) ? clean : false;
        }

        // Live input validation listener for custPhone
        document.addEventListener('DOMContentLoaded', () => {
            const pInput = document.getElementById('custPhone');
            if (pInput) {
                pInput.addEventListener('input', function() {
                    const errEl = document.getElementById('phoneErrorMsg');
                    const val = this.value.trim();
                    if (!val) {
                        errEl.style.display = 'none';
                        return;
                    }
                    const valid = validateVnPhone(val);
                    if (!valid && val.replace(/[^0-9]/g, '').length >= 10) {
                        errEl.style.display = 'block';
                        errEl.innerText = 'Số điện thoại không đúng chuẩn di động Việt Nam (Bắt đầu 03, 05, 07, 08, 09 và gồm 10 số).';
                    } else {
                        errEl.style.display = 'none';
                    }
                });
            }
        });

        // ─── AFFILIATE / KOL TRACKING & ATTRIBUTION ───
        function getFloraActiveRef() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const ref = urlParams.get('ref');
                if (ref && ref.trim()) {
                    const clean = ref.trim().toUpperCase();
                    localStorage.setItem('flora_kol_ref', clean);
                    document.cookie = "flora_kol_ref=" + encodeURIComponent(clean) + "; path=/; max-age=" + (30 * 24 * 60 * 60) + "; SameSite=Lax";
                    return clean;
                }
                const stored = localStorage.getItem('flora_kol_ref');
                if (stored && stored.trim()) return stored.trim().toUpperCase();
                const match = document.cookie.match(new RegExp('(^| )flora_kol_ref=([^;]+)'));
                if (match) return decodeURIComponent(match[2]).trim().toUpperCase();
            } catch(e) {}
            return '';
        }

        // Tự động ghi nhận click khi có ?ref=... và tự động áp dụng ưu đãi của KOL
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const urlRef = urlParams.get('ref');
                let effectiveRef = '';

                if (urlRef && urlRef.trim()) {
                    effectiveRef = urlRef.trim().toUpperCase();
                    // Lưu cookie 30 ngày + localStorage
                    localStorage.setItem('flora_kol_ref', effectiveRef);
                    document.cookie = "flora_kol_ref=" + encodeURIComponent(effectiveRef) + "; path=/; max-age=" + (30 * 24 * 60 * 60) + "; SameSite=Lax";

                    const sessionKey = 'flora_ref_session_' + effectiveRef;
                    const alreadyRecorded = sessionStorage.getItem(sessionKey);

                    // Làm sạch thanh địa chỉ ngay lập tức để F5 không bao giờ re-trigger
                    if (window.history && window.history.replaceState) {
                        urlParams.delete('ref');
                        const newSearch = urlParams.toString() ? ('?' + urlParams.toString()) : '';
                        const cleanUrl = window.location.pathname + newSearch + window.location.hash;
                        window.history.replaceState({ path: cleanUrl }, document.title, cleanUrl);
                    }

                    // Nếu chưa ghi nhận trong session này và chưa có cookie debounce, gửi AJAX fallback
                    if (!alreadyRecorded && document.cookie.indexOf('flora_clk_') === -1) {
                        sessionStorage.setItem(sessionKey, '1');
                        const fd = new FormData();
                        fd.append('action', 'flora_track_affiliate_click');
                        fd.append('ref', effectiveRef);
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            body: fd
                        }).catch(() => {});
                    } else {
                        sessionStorage.setItem(sessionKey, '1');
                    }
                } else {
                    effectiveRef = getFloraActiveRef();
                }

                // Nếu có mã REF từ URL hoặc Cookie: Tự động điền và kích hoạt ưu đãi độc quyền của KOL
                if (effectiveRef) {
                    const voucherInput = document.getElementById('voucherCode');
                    if (voucherInput && !voucherInput.value) {
                        voucherInput.value = effectiveRef;
                    }
                    applyVoucher(effectiveRef, true);
                } else {
                    updateOrderSummary();
                }
            } catch(e) {
                updateOrderSummary();
            }
        });

        // ─── IDEMPOTENCY KEY GENERATION (CHỐNG TRÙNG LẶP ĐƠN) ───
        let currentIdempotencyKey = '';
        function getOrCreateIdempotencyKey() {
            if (!currentIdempotencyKey) {
                currentIdempotencyKey = 'FLORA-IK-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9);
                const hid = document.getElementById('orderIdempotencyKey');
                if (hid) hid.value = currentIdempotencyKey;
            }
            return currentIdempotencyKey;
        }

        let pollInterval = null;
        let currentOrderData = null;
        let stagedOrderData = null;
        let selectedProofFile = null;

        // ─── BƯỚC 1: XỬ LÝ SUBMIT FORM ĐẶT MUA (CHỈ MỞ MODAL VIETQR ACB, CHƯA TẠO ĐƠN TRONG DB) ───
        function handleCheckoutSubmit(e) {
            e.preventDefault();

            const name = document.getElementById('custName').value.trim();
            const rawPhone = document.getElementById('custPhone').value.trim();
            const email = document.getElementById('custEmail') ? document.getElementById('custEmail').value.trim() : '';
            const needConsult = document.getElementById('needConsult') ? document.getElementById('needConsult').checked : true;
            const voucherCode = document.getElementById('voucherCode') ? document.getElementById('voucherCode').value.trim() : '';

            // 1. Kiểm tra họ tên
            if (!name || name.length < 2) {
                showToast('⚠️ Vui lòng nhập đầy đủ Họ và tên người sở hữu gói!');
                document.getElementById('custName').focus();
                return;
            }

            // 2. Bắt buộc nhập đúng số điện thoại di động Việt Nam chuẩn 10 số
            const cleanPhone = validateVnPhone(rawPhone);
            const errEl = document.getElementById('phoneErrorMsg');
            if (!cleanPhone) {
                if (errEl) {
                    errEl.style.display = 'block';
                    errEl.innerText = 'Số điện thoại không đúng chuẩn di động Việt Nam! Vui lòng nhập đúng 10 số (bắt đầu 03, 05, 07, 08, 09) để nhận xác thực.';
                }
                showToast('⚠️ Số điện thoại không hợp lệ! Vui lòng nhập đúng 10 số (03, 05, 07, 08, 09)');
                document.getElementById('custPhone').focus();
                return false;
            } else if (errEl) {
                errEl.style.display = 'none';
            }

            // 3. Bắt buộc nhập địa chỉ Email hợp lệ
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                showToast('⚠️ Vui lòng nhập địa chỉ Email hợp lệ để nhận kích hoạt & hóa đơn!');
                document.getElementById('custEmail').focus();
                return false;
            }

            // 3. Tính toán gói và số tiền thanh toán (Mặc định Giá gốc nếu không áp mã)
            const pkg = PACKAGES[currentSelectedPkg];
            const origPrice = pkg.originalPrice;
            let discountAmount = 0;

            if (appliedVoucherData && appliedVoucherData.valid) {
                if (appliedVoucherData.isKol) {
                    discountAmount = pkg.stdDiscount;
                } else if (appliedVoucherData.discountAmount > 0) {
                    discountAmount = Math.min(appliedVoucherData.discountAmount, origPrice);
                } else {
                    discountAmount = pkg.stdDiscount;
                }
            }

            const finalAmount = Math.max(0, origPrice - discountAmount);
            const finalAmountFmt = finalAmount.toLocaleString('vi-VN') + ' VNĐ';

            // Tạo mã ngẫu nhiên cho cú pháp chuyển khoản chứa SĐT
            const randomSuffix = Math.floor(10000 + Math.random() * 90000);
            const orderDraftCode = 'FLORA' + randomSuffix;
            const transferSyntax = 'FLORA ' + cleanPhone + ' ' + randomSuffix;

            // Xác định mã giới thiệu / voucher hợp lệ để gán đơn
            const effectiveRef = (appliedVoucherData && appliedVoucherData.isKol && appliedVoucherData.kolRef)
                ? appliedVoucherData.kolRef
                : (getFloraActiveRef() || '');
            const effectiveVoucher = appliedVoucherData ? appliedVoucherData.code : (voucherCode || '');

            // Lưu dữ liệu tạm (Staging) - Chưa tạo vào Database theo yêu cầu!
            stagedOrderData = {
                name: name,
                phone: cleanPhone,
                email: email,
                packageId: currentSelectedPkg,
                pkgName: pkg.name,
                voucherCode: effectiveVoucher,
                needConsult: needConsult,
                refCode: effectiveRef,
                originalPrice: origPrice,
                discountVoucher: discountAmount,
                finalAmount: finalAmount,
                finalAmountFmt: finalAmountFmt,
                orderDraftCode: orderDraftCode,
                syntax: transferSyntax
            };

            // 4. Tạo URL VietQR ACB duy nhất 1 tài khoản Flora
            const vietQrUrl = `https://img.vietqr.io/image/ACB-77779268-compact2.png?amount=${finalAmount}&addInfo=${encodeURIComponent(transferSyntax)}&accountName=CONG%20TY%20CO%20PHAN%20FLORA%20DENTAL%20CARE`;

            // 5. Cập nhật giao diện Modal VietQR ACB
            document.getElementById('modalHeaderTitle').innerText = 'THANH TOÁN: ' + orderDraftCode;
            document.getElementById('bankQrImg').src = vietQrUrl;
            document.getElementById('modalBankAmount').innerText = finalAmountFmt;
            document.getElementById('modalBankSyntax').innerText = transferSyntax;

            // Đặt lại các view
            document.getElementById('paymentActiveView').style.display = 'block';
            document.getElementById('paymentAwaitingView').style.display = 'none';
            document.getElementById('paymentSuccessView').classList.remove('active');
            document.getElementById('paymentModalFooter').style.display = 'flex';

            // Mở Modal
            document.getElementById('paymentModal').classList.add('active');

            // Bắt đầu đếm ngược 15 phút
            startCountdownTimer(15 * 60);

            showToast('👉 Vui lòng mở App Ngân hàng quét mã VietQR và chuyển khoản.');
            return false;
        }

        // ─── BƯỚC 2: KHÁCH BẤM "TÔI ĐÃ CHUYỂN KHOẢN" -> MỚI TẠO ĐƠN TRONG DB & GỬI EMAIL CHỜ XÁC NHẬN ───
        async function handleCustomerConfirmedTransfer() {
            if (!stagedOrderData) {
                showToast('⚠️ Vui lòng điền thông tin đăng ký trước.');
                return;
            }

            const btn = document.getElementById('btnConfirmTransferred');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tạo đơn & thông báo Flora...';
            }

            const idempotencyKey = getOrCreateIdempotencyKey();
            const ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

            try {
                const formData = new FormData();
                formData.append('action', 'flora_create_order');
                formData.append('name', stagedOrderData.name);
                formData.append('phone', stagedOrderData.phone);
                formData.append('email', stagedOrderData.email);
                formData.append('package_id', stagedOrderData.packageId);
                formData.append('voucher_code', stagedOrderData.voucherCode);
                formData.append('need_consult', stagedOrderData.needConsult ? 1 : 0);
                formData.append('transfer_syntax', stagedOrderData.syntax);
                formData.append('idempotency_key', idempotencyKey);

                if (stagedOrderData.refCode) {
                    formData.append('ref_code', stagedOrderData.refCode);
                }

                const response = await fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                });

                const res = await response.json();

                if (!res.success) {
                    showToast('❌ ' + (res.data ? res.data.message : 'Có lỗi khi tạo đơn hàng. Vui lòng thử lại!'));
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = origHtml;
                    }
                    return;
                }

                const data = res.data;
                currentOrderCode = data.order_code;
                currentOrderData = data;

                // Lưu mã đơn và SĐT vào localStorage để khách reload trang (F5) vẫn tra cứu được ngay
                try {
                    localStorage.setItem('flora_last_order_code', currentOrderCode);
                    localStorage.setItem('flora_last_order_phone', stagedOrderData.phone);
                } catch(e) {}

                // Cập nhật giao diện "Đang chờ xác nhận từ Flora"
                document.getElementById('modalHeaderTitle').innerText = 'GÓI DỊCH VỤ #' + currentOrderCode;
                document.getElementById('awaitingCustName').innerText = stagedOrderData.name;
                document.getElementById('awaitingOrderCode').innerText = currentOrderCode;
                document.getElementById('awaitingOrderCodeText').innerText = currentOrderCode;
                document.getElementById('awaitingPkgName').innerText = stagedOrderData.pkgName;
                document.getElementById('awaitingAmount').innerText = stagedOrderData.finalAmountFmt;
                document.getElementById('awaitingSyntax').innerText = stagedOrderData.syntax;

                // Reset vùng gửi ảnh minh chứng
                selectedProofFile = null;
                const proofInput = document.getElementById('proofFileInput');
                if (proofInput) proofInput.value = '';
                const proofPreview = document.getElementById('proofPreviewWrap');
                if (proofPreview) proofPreview.style.display = 'none';
                const proofStatus = document.getElementById('proofUploadStatus');
                if (proofStatus) proofStatus.style.display = 'none';

                // Chuyển sang màn hình Đang chờ xác nhận
                document.getElementById('paymentActiveView').style.display = 'none';
                document.getElementById('paymentModalFooter').style.display = 'none';
                document.getElementById('paymentAwaitingView').style.display = 'block';

                // Cuộn mượt lên đầu modal để thấy trọn vẹn tiêu đề
                const modalOverlay = document.getElementById('paymentModal');
                if (modalOverlay) modalOverlay.scrollTop = 0;

                showToast('✅ Đã tiếp nhận đăng ký gói dịch vụ & gửi xác nhận tới Flora!');

                // Bắt đầu Polling kiểm tra trạng thái thời gian thực
                startPaymentStatusPolling(currentOrderCode);

            } catch (err) {
                console.error('Create order error:', err);
                showToast('❌ Lỗi kết nối máy chủ. Vui lòng gọi Hotline 028 7305 8999 để xác nhận trực tiếp.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        // Nén ảnh sang định dạng WebP trực tiếp trên trình duyệt trước khi upload
        function compressImageToWebpClient(file, maxDim = 1600, quality = 0.82) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        let w = img.width;
                        let h = img.height;
                        if (w > maxDim || h > maxDim) {
                            if (w > h) {
                                h = Math.round((h * maxDim) / w);
                                w = maxDim;
                            } else {
                                w = Math.round((w * maxDim) / h);
                                h = maxDim;
                            }
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);
                        canvas.toBlob(function(blob) {
                            if (blob) {
                                const newName = file.name.replace(/\.[^/.]+$/, "") + ".webp";
                                resolve(new File([blob], newName, { type: 'image/webp' }));
                            } else {
                                resolve(file);
                            }
                        }, 'image/webp', quality);
                    };
                    img.onerror = function() { resolve(file); };
                    img.src = e.target.result;
                };
                reader.onerror = function() { resolve(file); };
                reader.readAsDataURL(file);
            });
        }

        // ─── BƯỚC 3: GỬI KÈM ẢNH MINH CHỨNG (TÙY CHỌN, HOÀN TOÀN KHÔNG BẮT BUỘC) ───
        async function handleProofFileSelected(input) {
            const rawFile = input.files && input.files[0];
            if (!rawFile) return;

            let file = rawFile;
            try {
                file = await compressImageToWebpClient(rawFile, 1600, 0.82);
            } catch(e) {
                file = rawFile;
            }

            selectedProofFile = file;

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('proofPreviewImg').src = e.target.result;
                document.getElementById('proofFileName').innerText = file.name;
                document.getElementById('proofFileSize').innerText = (file.size / 1024).toFixed(1) + ' KB (Nén WebP)';
                document.getElementById('proofPreviewWrap').style.display = 'flex';
                const st = document.getElementById('proofUploadStatus');
                if (st) st.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        async function submitProofUpload() {
            if (!selectedProofFile) {
                showToast('⚠️ Vui lòng bấm "Chọn ảnh chụp màn hình bill" trước.');
                return;
            }
            if (!currentOrderCode) {
                showToast('⚠️ Không tìm thấy mã đơn hàng cần đính kèm ảnh.');
                return;
            }

            const btn = document.getElementById('btnUploadProofAction');
            const origHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải lên...';
            }

            try {
                const formData = new FormData();
                formData.append('action', 'flora_upload_payment_proof');
                formData.append('order_code', currentOrderCode);
                formData.append('proof_image', selectedProofFile);

                const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData
                });

                const res = await response.json();

                if (res.success) {
                    const st = document.getElementById('proofUploadStatus');
                    st.style.display = 'block';
                    st.style.color = '#16a34a';
                    st.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đã gửi ảnh biên lai thành công! Tư vấn viên Flora đã nhận được thông báo.';
                    document.getElementById('proofPreviewWrap').style.display = 'none';
                    showToast('📸 Đã gửi ảnh biên lai cho Flora thành công!');
                } else {
                    showToast('❌ ' + (res.data ? res.data.message : 'Lỗi tải ảnh lên'));
                }
            } catch (err) {
                console.error('Upload proof error:', err);
                showToast('❌ Lỗi kết nối tải ảnh.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origHtml;
                }
            }
        }

        // ─── POLLING KIỂM TRA TRẠNG THÁI ĐƠN HÀNG THỜI GIAN THỰC (MỖI 4 GIÂY) ───
        function startPaymentStatusPolling(orderCode) {
            if (pollInterval) clearInterval(pollInterval);
            const checkUrl = '<?php echo admin_url('admin-ajax.php'); ?>?action=flora_check_order_status&order_code=' + encodeURIComponent(orderCode);

            pollInterval = setInterval(async () => {
                try {
                    const res = await fetch(checkUrl);
                    const data = await res.json();
                    if (data && data.success && data.data) {
                        if (data.data.is_paid || data.data.status === 'paid') {
                            clearInterval(pollInterval);
                            triggerPaymentSuccessUI(data.data);
                        } else if (data.data.status === 'rejected') {
                            clearInterval(pollInterval);
                            showToast('⚠️ Đơn hàng đã bị từ chối hoặc hết hạn!');
                            alert(`Thông báo từ Nha Khoa Flora:\n\nĐơn hàng ${orderCode} chưa được xác nhận thành công.\nLý do: ${data.data.reject_reason || 'Quá hạn 24 giờ đối soát hoặc thông tin không khớp'}.\n\nNếu Quý khách đã bị trừ tiền, vui lòng liên hệ ngay Hotline 028 7305 8999 để được kiểm tra và kích hoạt ngay!`);
                        }
                    }
                } catch (e) {
                    // Ignore network polling blip
                }
            }, 4000);
        }

        // ─── HIỂN THỊ MÀN HÌNH THANH TOÁN THÀNH CÔNG (SUCCESS VIEW) ───
        function triggerPaymentSuccessUI(data) {
            if (timerInterval) clearInterval(timerInterval);
            if (pollInterval) clearInterval(pollInterval);

            showToast('🎉 GIAO DỊCH THÀNH CÔNG! Đơn hàng đã được xác nhận.');

            const custName = stagedOrderData ? stagedOrderData.name : (data.customer_name || '');
            const custPhone = stagedOrderData ? stagedOrderData.phone : (data.customer_phone || '');
            const pkgName = stagedOrderData ? stagedOrderData.pkgName : (data.package_name || 'Gói dịch vụ Flora');
            const displayAmt = stagedOrderData ? stagedOrderData.finalAmountFmt : (data.final_amount ? (Number(data.final_amount).toLocaleString('vi-VN') + ' VNĐ') : '');

            document.getElementById('successCustName').innerText = custName;
            document.getElementById('successOrderCode').innerText = data.order_code || currentOrderCode;
            document.getElementById('successPkgName').innerText = pkgName;
            document.getElementById('successAmount').innerText = displayAmt;
            document.getElementById('successCustPhone').innerText = custPhone;
            document.getElementById('successTransId').innerText = data.momo_trans_id || ('ACB-' + Date.now().toString().slice(-6));

            // Đổi màn hình
            document.getElementById('paymentActiveView').style.display = 'none';
            document.getElementById('paymentAwaitingView').style.display = 'none';
            document.getElementById('paymentModalFooter').style.display = 'none';
            document.getElementById('paymentSuccessView').classList.add('active');

            // Xóa mã đơn chờ trong localStorage khi đã hoàn tất
            try {
                localStorage.removeItem('flora_last_order_code');
            } catch(e) {}
            currentIdempotencyKey = '';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('active');
            if (timerInterval) clearInterval(timerInterval);
            if (pollInterval) clearInterval(pollInterval);
        }

        function startCountdownTimer(seconds) {
            if (timerInterval) clearInterval(timerInterval);
            let remain = seconds;
            const timerBank = document.getElementById('bankTimer');

            function tick() {
                const mins = Math.floor(remain / 60);
                const secs = remain % 60;
                const formatted = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                if (timerBank) timerBank.innerText = formatted;

                if (remain <= 0) {
                    clearInterval(timerInterval);
                    if (timerBank) timerBank.innerText = 'Hết hạn';
                }
                remain--;
            }

            tick();
            timerInterval = setInterval(tick, 1000);
        }

        // ─── TIỆN ÍCH COPY THÔNG TIN (HỖ TRỢ ICON COPY & FEEDBACK TRỰC QUAN) ───
        function copyTextDirect(btnEl, txt, label) {
            if (!txt) return;
            const cleanTxt = String(txt).trim();

            function onSuccess() {
                showToast(`Đã sao chép ${label ? label + ': ' : ''}${cleanTxt}`);
                if (btnEl && btnEl.classList) {
                    btnEl.classList.add('copied');
                    const origIcon = btnEl.innerHTML;
                    btnEl.innerHTML = '<i class="fa-solid fa-check"></i>';
                    setTimeout(() => {
                        btnEl.classList.remove('copied');
                        btnEl.innerHTML = origIcon;
                    }, 1600);
                }
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(cleanTxt).then(onSuccess).catch(() => {
                    fallbackCopyExec(cleanTxt, onSuccess, label);
                });
            } else {
                fallbackCopyExec(cleanTxt, onSuccess, label);
            }
        }

        function fallbackCopyExec(text, cb, label) {
            try {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                const successful = document.execCommand('copy');
                document.body.removeChild(textArea);
                if (successful && cb) cb();
                else prompt(`Sao chép ${label || ''}:`, text);
            } catch (err) {
                prompt(`Sao chép ${label || ''}:`, text);
            }
        }

        function copyText(txt, label) {
            copyTextDirect(null, txt, label);
        }

        function copyTransferAmount(btnEl) {
            const el = document.getElementById('modalBankAmount');
            if (el) {
                copyTextDirect(btnEl || null, el.innerText.trim(), 'Số tiền thanh toán');
            }
        }

        function copyTransferSyntax(btnEl) {
            const el = document.getElementById('modalBankSyntax');
            if (el) {
                copyTextDirect(btnEl || null, el.innerText.trim(), 'Nội dung chuyển khoản');
            }
        }

        function openFloraTermsModal() {
            const modal = document.getElementById('floraTermsModal');
            if (modal) modal.classList.add('active');
        }

        function closeFloraTermsModal() {
            const modal = document.getElementById('floraTermsModal');
            if (modal) modal.classList.remove('active');
        }

        // ─── 9b. TRA CỨU ĐƠN HÀNG (MODAL THEO DÕI TIẾN ĐỘ) ───
        function openOrderTrackingModal(prefillCode) {
            const modal = document.getElementById('orderTrackingModal');
            if (!modal) return;
            modal.classList.add('active');

            const input = document.getElementById('orderTrackingInput');
            if (prefillCode && input) {
                input.value = prefillCode;
                executeOrderLookup();
            } else if (input && !input.value) {
                const lastCode = localStorage.getItem('flora_last_order_code') || localStorage.getItem('flora_last_order_phone');
                if (lastCode) {
                    input.value = lastCode;
                    executeOrderLookup();
                }
            }
        }

        function closeOrderTrackingModal() {
            const modal = document.getElementById('orderTrackingModal');
            if (modal) modal.classList.remove('active');
        }

        async function executeOrderLookup() {
            const input = document.getElementById('orderTrackingInput');
            const resultBox = document.getElementById('orderTrackingResult');
            const btn = document.getElementById('btnExecuteLookup');
            if (!input || !resultBox) return;

            const query = input.value.trim();
            if (!query) {
                showToast('⚠️ Vui lòng nhập Mã đơn hàng hoặc Số điện thoại để tra cứu.');
                input.focus();
                return;
            }

            const origBtnHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Tra cứu...';
            }

            resultBox.style.display = 'block';
            resultBox.innerHTML = '<div style="text-align: center; padding: 24px; color: #64748b;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i><p style="margin: 10px 0 0;">Đang kiểm tra dữ liệu đối soát...</p></div>';

            try {
                const checkUrl = '<?php echo admin_url('admin-ajax.php'); ?>?action=flora_lookup_order&query=' + encodeURIComponent(query);
                const response = await fetch(checkUrl);
                const res = await response.json();

                if (!res.success) {
                    resultBox.innerHTML = `
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 18px; text-align: center;">
                            <i class="fa-solid fa-circle-exclamation" style="color: #ef4444; font-size: 1.8rem; margin-bottom: 8px;"></i>
                            <h4 style="margin: 0 0 6px; color: #991b1b; font-size: 1rem;">Không tìm thấy thông tin đơn hàng</h4>
                            <p style="margin: 0; color: #b91c1c; font-size: 0.85rem; line-height: 1.4;">
                                ${res.data ? res.data.message : 'Vui lòng kiểm tra lại Mã đơn hoặc Số điện thoại đăng ký.'}
                            </p>
                            <div style="margin-top: 14px; font-size: 0.84rem; color: #475569;">
                                Cần hỗ trợ khẩn cấp? Gọi Hotline: <a href="tel:02873058999" style="font-weight: 800; color: #0033a3;">028 7305 8999</a>
                            </div>
                        </div>
                    `;
                    return;
                }

                const order = res.data;
                let statusBadgeHtml = '';
                let statusNoticeHtml = '';

                if (order.payment_status === 'paid') {
                    statusBadgeHtml = '<span style="background: #dcfce7; color: #15803d; padding: 5px 12px; border-radius: 9999px; font-weight: 800; font-size: 0.82rem;"><i class="fa-solid fa-circle-check"></i> ĐÃ THANH TOÁN THÀNH CÔNG</span>';
                    statusNoticeHtml = `
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 14px; margin-top: 14px; font-size: 0.82rem; color: #166534;">
                            <i class="fa-solid fa-circle-check"></i> Gói dịch vụ đã được kích hoạt trên hệ thống Flora Dental Care. Quý khách vui lòng đến phòng khám hoặc liên hệ để đặt hẹn ưu tiên!
                        </div>
                    `;
                } else if (order.payment_status === 'rejected' || order.payment_status === 'cancelled') {
                    statusBadgeHtml = '<span style="background: #fee2e2; color: #b91c1c; padding: 5px 12px; border-radius: 9999px; font-weight: 800; font-size: 0.82rem;"><i class="fa-solid fa-circle-xmark"></i> TỪ CHỐI / HẾT HẠN</span>';
                    statusNoticeHtml = `
                        <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 10px; padding: 12px 14px; margin-top: 14px; font-size: 0.82rem; color: #9f1239;">
                            <strong>Lý do:</strong> ${order.reject_reason || 'Quá thời hạn 24 giờ hoặc sai lệch nội dung chuyển khoản'}.<br>
                            ⚠️ <em>Nếu Quý khách đã bị trừ tiền từ tài khoản ACB, Flora cam kết hoàn tiền 100% hoặc hỗ trợ kích hoạt thủ công ngay:</em><br>
                            📞 Hotline Khiếu Nại: <strong>028 7305 8999</strong> | Zalo: <strong>0902 535 068</strong>
                        </div>
                    `;
                } else {
                    statusBadgeHtml = '<span style="background: #fef3c7; color: #b45309; padding: 5px 12px; border-radius: 9999px; font-weight: 800; font-size: 0.82rem;"><i class="fa-solid fa-clock-rotate-left"></i> ĐANG CHỜ XÁC NHẬN</span>';
                    statusNoticeHtml = `
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 12px 14px; margin-top: 14px; font-size: 0.82rem; color: #1e40af;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span class="pulse-dot"></span>
                                <strong>Tư vấn viên Flora đang kiểm tra sao kê ngân hàng</strong>
                            </div>
                            Thời gian xác nhận thông thường từ <strong>5 - 15 phút</strong>. Quý khách hoàn toàn yên tâm quyền lợi được bảo đảm 100%.
                        </div>
                    `;
                }

                const proofHtml = order.proof_image_url
                    ? `<div style="margin-top: 8px;"><a href="${order.proof_image_url}" target="_blank" style="font-size: 0.82rem; color: #0284c7; text-decoration: underline; font-weight: 700;"><i class="fa-solid fa-image"></i> Xem ảnh biên lai đã gửi</a></div>`
                    : `<div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">(Chưa gửi ảnh biên lai - Tư vấn viên sẽ đối soát theo sao kê tài khoản ACB)</div>`;

                resultBox.innerHTML = `
                    <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 18px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                            <div>
                                <span style="font-size: 0.76rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Mã Đơn Hàng</span>
                                <div style="font-size: 1.25rem; font-weight: 900; color: #0033a3; font-family: monospace;">${order.order_code}</div>
                            </div>
                            <div>${statusBadgeHtml}</div>
                        </div>

                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; font-size: 0.88rem; line-height: 1.7;">
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px; margin-bottom: 8px;">
                                <span style="color: #64748b;"><i class="fa-solid fa-user"></i> Khách hàng:</span>
                                <strong style="color: #0f172a;">${order.customer_name} (${order.customer_phone_mask || order.customer_phone})</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px; margin-bottom: 8px;">
                                <span style="color: #64748b;"><i class="fa-solid fa-box-open"></i> Gói dịch vụ:</span>
                                <strong style="color: #0033a3; font-weight: 800;">${order.package_name}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b;"><i class="fa-solid fa-money-bill-wave"></i> Số tiền thanh toán:</span>
                                <strong style="color: #16a34a; font-size: 1.05rem; font-weight: 900;">${order.final_amount_fmt}</strong>
                            </div>
                        </div>

                        ${statusNoticeHtml}

                        <div style="margin-top: 16px; display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap;">
                            <a href="tel:02873058999" class="btn btn-secondary" style="padding: 8px 14px; font-size: 0.82rem; text-decoration: none;">
                                <i class="fa-solid fa-phone"></i> Hotline 028 7305 8999
                            </a>
                            <a href="https://zalo.me/0902535068" target="_blank" class="btn btn-primary" style="padding: 8px 14px; font-size: 0.82rem; text-decoration: none; background: #0068ff;">
                                <i class="fa-solid fa-comment-dots"></i> Chat Zalo Hỗ Trợ
                            </a>
                        </div>
                    </div>
                `;

            } catch (err) {
                console.error('Lookup error:', err);
                resultBox.innerHTML = '<div style="color: #ef4444; text-align: center; padding: 16px;">Lỗi kết nối máy chủ tra cứu. Vui lòng thử lại sau!</div>';
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = origBtnHtml;
                }
            }
        }


        function openRegModal() {
            document.getElementById('regModal').classList.add('active');
        }

        function closeRegModal() {
            document.getElementById('regModal').classList.remove('active');
        }

        function handleQuickRegSubmit(e) {
            e.preventDefault();
            const name = document.getElementById('quickName').value.trim();
            const phone = document.getElementById('quickPhone').value.trim();
            const serv = document.getElementById('quickService').value;

            closeRegModal();
            showToast('🎉 Đăng ký thành công! Flora sẽ liên hệ xác nhận ngay.');
            alert(`Cảm ơn Quý khách ${name}!\nFlora đã tiếp nhận đăng ký ${serv}. Chuyên viên sẽ gọi đến số ${phone} trong thời gian sớm nhất.`);
        }

        function showToast(msg) {
            const t = document.getElementById('toastAlert');
            const txt = document.getElementById('toastMsg');
            if (txt) txt.innerText = msg;
            t.classList.add('show');
            clearTimeout(t._timer);
            t._timer = setTimeout(() => {
                t.classList.remove('show');
            }, 3500);
        }

        const drawerTrigger = document.getElementById('drawerTrigger');
        if (drawerTrigger) {
            drawerTrigger.addEventListener('click', () => {
                const nav = document.querySelector('.nav-links');
                if (nav) {
                    if (nav.style.display === 'flex') {
                        nav.style.display = 'none';
                    } else {
                        nav.style.display = 'flex';
                        nav.style.flexDirection = 'column';
                        nav.style.position = 'absolute';
                        nav.style.top = '74px';
                        nav.style.left = '0';
                        nav.style.width = '100%';
                        nav.style.background = '#ffffff';
                        nav.style.padding = '20px';
                        nav.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
                    }
                }
            });
        }

        function revealElements() {
            const reveals = document.querySelectorAll('.reveal');
            reveals.forEach(el => {
                const windowHeight = window.innerHeight;
                const elementTop = el.getBoundingClientRect().top;
                const elementVisible = 100;
                if (elementTop < windowHeight - elementVisible) {
                    el.classList.add('active');
                }
            });
        }

        // ─── KIỂM TRA ĐƠN HÀNG CHỜ ĐỐI SOÁT KHI TẢI LẠI TRANG (SMART RESUME) ───
        function checkSmartResumeBanner() {
            try {
                const lastCode = localStorage.getItem('flora_last_order_code');
                const banner = document.getElementById('smartResumeBanner');
                if (lastCode && banner) {
                    const codeEl = document.getElementById('smartResumeOrderCode');
                    if (codeEl) codeEl.innerText = lastCode;
                    banner.classList.add('active');
                }
            } catch(e) {}
        }

        function dismissSmartResumeBanner() {
            const banner = document.getElementById('smartResumeBanner');
            if (banner) banner.classList.remove('active');
        }

        window.addEventListener('scroll', revealElements);
        window.addEventListener('DOMContentLoaded', () => {
            revealElements();
            updateOrderSummary();
            checkSmartResumeBanner();
        });
    </script>
    <style>
        .floating-widgets, .mobile-floating-dock, #flora-ai-chat-btn-wrapper, .flora-ai-chat-btn-wrapper, .ai-consultation-btn-wrapper {
            display: none !important;
        }
    </style>
    <?php get_footer(); ?>
