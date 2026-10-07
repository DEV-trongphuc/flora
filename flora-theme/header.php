<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap" />
    <link rel="stylesheet" href="<?php echo flora_asset('fonts/fontawesome/css/all.min.css'); ?>?v=<?php echo FLORA_VERSION; ?>" />

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- Global SVG Gradient Definitions -->
    <svg style="display: none;" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="sparkleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#0493f1" />
                <stop offset="100%" stop-color="#0033a3" />
            </linearGradient>
            <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#0493f1" />
                <stop offset="100%" stop-color="#0033a3" />
            </linearGradient>
            <linearGradient id="swissGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#e20613" />
                <stop offset="100%" stop-color="#9f040d" />
            </linearGradient>
            <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#ffd700" />
                <stop offset="100%" stop-color="#ffa500" />
            </linearGradient>
        </defs>
    </svg>

    <!-- HEADER -->
    <header class="header-bar">
        <div class="container header-inner">
            <div class="logo-box">
                <a href="<?php echo esc_url(home_url('/')); ?>" style="display: flex; align-items: center; text-decoration: none;" title="<?php bloginfo('name'); ?>">
                    <?php 
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else { ?>
                        <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="<?php bloginfo('name'); ?>" class="logo-img" style="height: 52px; width: auto;" height="52" />
                    <?php } ?>
                </a>
            </div>
            
            <ul class="nav-links">
                <li class="nav-link-item <?php echo is_front_page() ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a>
                </li>
                <li class="nav-link-item <?php echo (is_page('trong-rang-implant') || is_page('implant')) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/trong-rang-implant/')); ?>">Implant</a>
                </li>
                <li class="nav-link-item <?php echo (is_page('dieu-tri-cuoi-ho-loi') || is_page('ho-loi')) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/dieu-tri-cuoi-ho-loi/')); ?>">Cười Hở Lợi</a>
                </li>
                <li class="nav-link-item has-dropdown <?php echo (is_page(array('nieng-rang', 'dan-rang-su-veneer-tham-my', 'veneer', 'boc-rang-su-tham-my', 'boc-rang-su', 'dieu-tri-nha-khoa-tong-quat', 'tong-quat'))) ? 'active' : ''; ?>">
                    <a href="javascript:void(0)">Dịch Vụ Khác <i class="fa-solid fa-chevron-down dropdown-icon"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo esc_url(home_url('/nieng-rang/')); ?>">Niềng Răng</a></li>
                        <li><a href="<?php echo esc_url(home_url('/dan-rang-su-veneer-tham-my/')); ?>">Dán Sứ Veneer</a></li>
                        <li><a href="<?php echo esc_url(home_url('/boc-rang-su-tham-my/')); ?>">Bọc Răng Sứ</a></li>
                        <li><a href="<?php echo esc_url(home_url('/dieu-tri-nha-khoa-tong-quat/')); ?>">Nha Khoa Tổng Quát</a></li>
                    </ul>
                </li>
                <li class="nav-link-item <?php echo is_page('bang-gia') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/bang-gia/')); ?>">Bảng Giá</a>
                </li>
                <li class="nav-link-item has-dropdown <?php echo (is_home() || is_archive() || is_single() || is_category() || is_page('goc-suc-khoe')) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/goc-suc-khoe/')); ?>">Góc Sức Khỏe <i class="fa-solid fa-chevron-down dropdown-icon"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo esc_url(home_url('/goc-suc-khoe/')); ?>">Tất Cả Bài Viết</a></li>
                        <li><a href="<?php echo esc_url(home_url('/category/kien-thuc/')); ?>">Kiến Thức Y Khoa</a></li>
                        <li><a href="<?php echo esc_url(home_url('/goc-suc-khoe/#cases')); ?>">Ca Điều Trị Thực Tế</a></li>
                    </ul>
                </li>
            </ul>

            <div class="header-action">
                <a href="<?php echo esc_url(home_url('/warranty/')); ?>" class="btn-check-toggle <?php echo (is_page('warranty') || is_page('tra-cuu-bao-hanh')) ? 'active' : ''; ?>" title="Kiểm tra chính hãng Implant &amp; Răng Sứ">
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Kiểm Tra Chính Hãng</span>
                </a>
                <a href="#dang-ky" class="btn btn-primary btn-booking">Đăng Ký Đặt Hẹn</a>
            </div>
            <button class="hamburger-btn" id="drawerTrigger" aria-label="Menu Mobile">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- MOBILE OVERLAY & DRAWER -->
    <div class="drawer-overlay" id="drawerOverlay"></div>
    <nav class="drawer-nav" id="drawerNav">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid rgba(10,25,49,0.06);">
            <div class="logo-box">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <img src="<?php echo flora_asset('ngayhoi_item/Logo-Flora1.webp'); ?>" alt="<?php bloginfo('name'); ?>" class="logo-img" style="height: 44px; width: auto;" />
                </a>
            </div>
            <button type="button" id="drawerCloseBtn" class="drawer-close-btn" aria-label="Đóng menu">&times;</button>
        </div>
        <a href="<?php echo esc_url(home_url('/warranty/')); ?>" class="drawer-auth-link">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-shield-halved" style="color: #0493f1; font-size: 1.05rem;"></i>
                <span style="font-weight: 700; color: #0033a3;">Kiểm Tra Chính Hãng</span>
            </div>
            <i class="fa-solid fa-chevron-right" style="color: #0033a3; font-size: 0.85rem;"></i>
        </a>
        <a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a>
        <a href="<?php echo esc_url(home_url('/trong-rang-implant/')); ?>">Implant</a>
        <a href="<?php echo esc_url(home_url('/dieu-tri-cuoi-ho-loi/')); ?>">Cười Hở Lợi</a>
        
        <!-- Accordion Dịch Vụ Khác -->
        <div class="drawer-accordion">
            <div class="drawer-accordion-header">
                <span>Dịch Vụ Khác</span>
                <i class="fa-solid fa-chevron-down accordion-icon"></i>
            </div>
            <div class="drawer-accordion-body">
                <a href="<?php echo esc_url(home_url('/nieng-rang/')); ?>">Niềng Răng</a>
                <a href="<?php echo esc_url(home_url('/dan-rang-su-veneer-tham-my/')); ?>">Dán Sứ Veneer</a>
                <a href="<?php echo esc_url(home_url('/boc-rang-su-tham-my/')); ?>">Bọc Răng Sứ</a>
                <a href="<?php echo esc_url(home_url('/dieu-tri-nha-khoa-tong-quat/')); ?>">Nha Khoa Tổng Quát</a>
            </div>
        </div>

        <a href="<?php echo esc_url(home_url('/bang-gia/')); ?>">Bảng Giá</a>

        <!-- Accordion Góc Sức Khỏe -->
        <div class="drawer-accordion">
            <div class="drawer-accordion-header">
                <span>Góc Sức Khỏe</span>
                <i class="fa-solid fa-chevron-down accordion-icon"></i>
            </div>
            <div class="drawer-accordion-body">
                <a href="<?php echo esc_url(home_url('/goc-suc-khoe/')); ?>">Tất Cả Bài Viết</a>
                <a href="<?php echo esc_url(home_url('/category/kien-thuc/')); ?>">Kiến Thức Y Khoa</a>
                <a href="<?php echo esc_url(home_url('/goc-suc-khoe/#cases')); ?>">Ca Điều Trị Thực Tế</a>
            </div>
        </div>

        <a href="#dang-ky" class="btn btn-primary btn-booking" style="margin-top: 15px;">Đăng Ký Đặt Hẹn</a>
    </nav>
