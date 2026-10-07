<?php
/**
 * 404 Not Found Template
 */

get_header();
?>

<main id="primary" class="site-main" style="padding: 100px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 5rem; font-weight: 800; color: var(--clr-navy, #0033a3); margin-bottom: 10px;">404</h1>
        <h2 style="font-size: 1.8rem; margin-bottom: 16px;">Không Tìm Thấy Trang</h2>
        <p style="color: var(--clr-text-muted, #64748b); max-width: 540px; margin: 0 auto 30px; line-height: 1.6;">
            Trang bạn đang tìm kiếm có thể đã được chuyển sang địa chỉ mới hoặc tạm thời không khả dụng. Mời bạn quay về trang chủ hoặc xem các dịch vụ chuyên sâu của Flora.
        </p>
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Về Trang Chủ</a>
            <a href="<?php echo esc_url(home_url('/goc-suc-khoe/')); ?>" class="btn btn-outline">Xem Góc Sức Khỏe</a>
        </div>
    </div>
</main>

<?php
get_footer();
