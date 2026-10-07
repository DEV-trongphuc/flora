<?php
/**
 * Flora Dental Clinic Theme Functions
 * Chuẩn SEO YMYL Y khoa & Tối ưu tốc độ tải trang
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('FLORA_VERSION', '2.3.7');

/**
 * 1. Theme Setup
 */
function flora_theme_setup() {
    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable Featured Images (Thumbnails)
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(800, 500, true);
    add_image_size('flora-blog-thumb', 600, 380, true);
    add_image_size('flora-blog-featured', 1200, 630, true);

    // HTML5 semantic markup support
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));

    // Custom Logo
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register Nav Menus
    register_nav_menus(array(
        'primary-menu' => __('Menu Chính (Header)', 'flora'),
        'footer-menu'  => __('Menu Chân Trang (Footer)', 'flora'),
    ));
}
add_action('after_setup_theme', 'flora_theme_setup');

/**
 * 1b. Add Subpage Body Class for Correct Spacing & Header Styling
 */
function flora_body_classes($classes) {
    if (!is_front_page()) {
        $classes[] = 'subpage-body';
    }
    return $classes;
}
add_filter('body_class', 'flora_body_classes');

/**
 * 2. Enqueue Styles and Scripts
 */
function flora_enqueue_assets() {
    $theme_uri = get_template_directory_uri();

    // Google Fonts - Plus Jakarta Sans & Montserrat
    wp_enqueue_style(
        'flora-google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600;0,700;0,800;1,600&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600;1,700&display=swap',
        array(),
        null
    );

    // Font Awesome 6 (Self-Hosted Locally)
    wp_enqueue_style(
        'flora-fontawesome',
        $theme_uri . '/assets/fonts/fontawesome/css/all.min.css',
        array(),
        FLORA_VERSION
    );

    // Main Theme Styles
    wp_enqueue_style(
        'flora-main-style',
        $theme_uri . '/assets/css/flora-style.css',
        array(),
        FLORA_VERSION
    );

    // Decor Animations
    wp_enqueue_style(
        'flora-animations',
        $theme_uri . '/assets/css/decor-animations.css',
        array('flora-main-style'),
        FLORA_VERSION
    );

    // Blog & Single Post Typography Styles
    wp_enqueue_style(
        'flora-blog-single',
        $theme_uri . '/assets/css/blog-single.css',
        array('flora-main-style'),
        FLORA_VERSION
    );

    // Base style.css
    wp_enqueue_style(
        'flora-theme-root',
        get_stylesheet_uri(),
        array('flora-main-style'),
        FLORA_VERSION
    );

    // Main JS Logic
    wp_enqueue_script(
        'flora-main-js',
        $theme_uri . '/assets/js/flora-main.js',
        array('jquery'),
        FLORA_VERSION,
        true
    );

    // AJAX Booking Script & Variables
    wp_localize_script('flora-main-js', 'floraData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('flora_booking_nonce'),
        'themeUrl'=> $theme_uri
    ));
}
add_action('wp_enqueue_scripts', 'flora_enqueue_assets');

/**
 * 3. Helper: Asset URL Resolver
 */
function flora_asset($path) {
    $clean_path = ltrim($path, '/');
    if (strpos($clean_path, 'assets/') === 0) {
        $clean_path = substr($clean_path, 7);
    }
    return esc_url(get_template_directory_uri() . '/assets/' . $clean_path);
}

/**
 * 4. Helper: Estimated Reading Time (Chuẩn YMYL & Helpful Content)
 */
function flora_reading_time($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $content = get_post_field('post_content', $post_id);
    $word_count = count(preg_split('/\s+/', strip_tags($content)));
    $reading_time = ceil($word_count / 200);
    return max(1, $reading_time) . ' phút đọc';
}

/**
 * 5. Helper: Breadcrumbs Renderer (Hỗ trợ Rank Math và Fallback)
 */
function flora_render_breadcrumbs() {
    if (function_exists('rank_math_the_breadcrumbs')) {
        rank_math_the_breadcrumbs();
        return;
    }

    // Fallback breadcrumb
    echo '<nav class="flora-breadcrumbs" aria-label="Breadcrumb">';
    echo '<a href="' . esc_url(home_url('/')) . '"><i class="fa-solid fa-house"></i> Trang Chủ</a>';
    
    if (is_category() || is_single()) {
        echo '<span class="sep"><i class="fa-solid fa-chevron-right"></i></span>';
        $cats = get_the_category();
        if (!empty($cats)) {
            echo '<a href="' . esc_url(get_category_link($cats[0]->term_id)) . '">' . esc_html($cats[0]->name) . '</a>';
        }
    }
    
    if (is_single()) {
        echo '<span class="sep"><i class="fa-solid fa-chevron-right"></i></span>';
        echo '<span class="current">' . esc_html(wp_trim_words(get_the_title(), 8)) . '</span>';
    } elseif (is_page()) {
        echo '<span class="sep"><i class="fa-solid fa-chevron-right"></i></span>';
        echo '<span class="current">' . esc_html(get_the_title()) . '</span>';
    } elseif (is_archive()) {
        echo '<span class="sep"><i class="fa-solid fa-chevron-right"></i></span>';
        echo '<span class="current">Góc Sức Khỏe</span>';
    }
    echo '</nav>';
}

/**
 * 6. Helper: Related Posts Query
 */
function flora_get_related_posts($post_id = null, $count = 3) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    $categories = wp_get_post_categories($post_id);
    if (empty($categories)) {
        return new WP_Query();
    }
    return new WP_Query(array(
        'category__in'   => $categories,
        'post__not_in'   => array($post_id),
        'posts_per_page' => $count,
        'ignore_sticky_posts' => 1
    ));
}

/**
 * 7. Booking Manager & Database Handler (Lưu trữ Database & WP-Admin Dashboard)
 */
require_once get_template_directory() . '/inc/booking-manager.php';

/**
 * 7a. Order & ACB VietQR Payment Manager (Đơn hàng Gói khám & Cổng thanh toán VietQR ACB)
 */
require_once get_template_directory() . '/inc/order-manager.php';

/**
 * 7b. Pricing Manager & Dynamic Price Tables
 */
require_once get_template_directory() . '/inc/pricing-manager.php';

/**
 * 7c. AI Doctor & Price Advisor (Google Gemini 3.5 Flash Lite)
 */
require_once get_template_directory() . '/inc/ai-advisor.php';

/**
 * 7d. Affiliate & KOL Management Engine (Cổng Đối Tác & Tích hợp SeaPay)
 */
require_once get_template_directory() . '/inc/affiliate-manager.php';

/**
 * 7e. Voucher & Promotion Manager (Mã Chung & Mã Gắn KOL)
 */
require_once get_template_directory() . '/inc/voucher-manager.php';

/**
 * 7f. Flora Admin UI Customizer & Clean Experience Engine
 */
require_once get_template_directory() . '/inc/admin-customizer.php';

/**
 * 8. Redirect Old Blog URL to /goc-suc-khoe/
 */
function flora_redirect_old_blog_url() {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($uri, '/kien-thuc-nha-khoa') !== false) {
        wp_redirect(home_url('/goc-suc-khoe/'), 301);
        exit;
    }
}
add_action('template_redirect', 'flora_redirect_old_blog_url');

/**
 * 9. Font Awesome & Static Asset Optimization Exclusions
 */
function flora_font_awesome_tag_filter($tag, $handle) {
    if ($handle === 'flora-fontawesome') {
        $tag = str_replace("<link ", "<link data-no-optimize=\"1\" crossorigin=\"anonymous\" referrerpolicy=\"no-referrer\" ", $tag);
    }
    return $tag;
}
add_filter('style_loader_tag', 'flora_font_awesome_tag_filter', 10, 2);

function flora_litespeed_css_excludes($excludes) {
    if (!is_array($excludes)) {
        $excludes = array();
    }
    $excludes[] = 'font-awesome';
    $excludes[] = 'all.min.css';
    $excludes[] = 'cdnjs.cloudflare.com';
    return $excludes;
}
add_filter('litespeed_optimize_css_excludes', 'flora_litespeed_css_excludes');
add_filter('litespeed_optm_css_exc', 'flora_litespeed_css_excludes');


