<?php
/**
 * Single Post Template (Chuẩn SEO Y khoa YMYL & Helpful Content)
 * Tự động đồng bộ toàn bộ 917 bài viết cũ của WordPress với giao diện chuyên nghiệp
 */

get_header();
?>

<main id="primary" class="site-main single-post-page">
<?php while (have_posts()) : the_post(); 
    $post_id = get_the_ID();
    $categories = get_the_category();
    $cat_name = !empty($categories) ? esc_html($categories[0]->name) : 'Kiến thức';
    $cat_link = !empty($categories) ? esc_url(get_category_link($categories[0]->term_id)) : '#';
?>

    <div class="single-post-wrapper">
        <div class="container" style="max-width: 1280px !important;">
            
            <!-- Breadcrumbs Header -->
            <div class="single-breadcrumbs-wrapper">
                <?php flora_render_breadcrumbs(); ?>
            </div>

            <!-- Two Column Main Grid -->
            <div class="single-post-layout">
                
                <!-- LEFT COLUMN: Main Article Body -->
                <article id="post-<?php the_ID(); ?>" <?php post_class('single-main-col'); ?>>
                    
                    <!-- Article Header -->
                    <header class="single-post-header">
                        <div class="single-header-top">
                            <a href="<?php echo $cat_link; ?>" class="single-category-badge">
                                <i class="fa-solid fa-tooth"></i> <?php echo $cat_name; ?>
                            </a>
                            <div class="single-quick-shares">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="quick-share-btn" title="Chia sẻ Facebook">
                                    <i class="fa-brands fa-facebook-f"></i>
                                </a>
                                <a href="https://zalo.me/share?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="quick-share-btn" title="Chia sẻ Zalo">
                                    <span style="font-weight: 800; font-size: 0.7rem;">Zalo</span>
                                </a>
                                <button type="button" class="quick-share-btn" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép link bài viết!');" title="Sao chép link">
                                    <i class="fa-solid fa-link"></i>
                                </button>
                            </div>
                        </div>

                        <h1 class="single-post-title"><?php the_title(); ?></h1>

                        <!-- E-E-A-T Medical Editorial Authority Bar -->
                        <div class="single-eeat-bar">
                            <div class="eeat-author-group">
                                <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS. CKI Nguyễn Đắc Minh" class="eeat-avatar" />
                                <div class="eeat-author-details">
                                    <div class="eeat-author-name">
                                        <strong>BS. CKI Nguyễn Đắc Minh</strong>
                                        <span class="eeat-author-role">• Bác sĩ chuyên khoa Flora</span>
                                    </div>
                                    <div class="eeat-meta-dates">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                        <span class="dot-sep">•</span>
                                        <span><i class="fa-solid fa-rotate"></i> Cập nhật: <?php echo get_the_modified_date('d/m/Y'); ?></span>
                                        <span class="dot-sep">•</span>
                                        <span><i class="fa-regular fa-clock"></i> <?php echo flora_reading_time(); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="meta-verified-badge" title="Bài viết được thẩm định chuyên môn y khoa bởi bác sĩ chuyên khoa Răng Hàm Mặt">
                                <i class="fa-solid fa-circle-check"></i> Đã Thẩm Định Y Khoa
                            </div>
                        </div>
                    </header>

                    <!-- Featured Image -->
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="single-featured-image">
                            <?php the_post_thumbnail('flora-blog-featured', array('alt' => get_the_title(), 'loading' => 'eager')); ?>
                            <?php if ($caption = get_the_post_thumbnail_caption()) : ?>
                                <p class="wp-caption-text"><i class="fa-solid fa-camera"></i> <?php echo esc_html($caption); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Dynamic Table of Contents Box (Tự động tạo từ H2/H3) -->
                    <div class="flora-toc-card" id="floraTocBox" style="display: none;">
                        <div class="flora-toc-header" onclick="document.getElementById('floraTocBox').classList.toggle('collapsed');">
                            <div class="flora-toc-title">
                                <i class="fa-solid fa-list-check"></i> Mục Lục Nội Dung Bài Viết
                            </div>
                            <button type="button" class="flora-toc-toggle" aria-label="Đóng mở mục lục">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>
                        <nav class="flora-toc-body" id="floraTocList"></nav>
                    </div>

                    <!-- Article Body Content -->
                    <div class="single-content entry-content" id="singleContent">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Trang:', 'flora'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <!-- Medical Reviewer Authority Card -->
                    <div class="medical-reviewer-box">
                        <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="Bác sĩ cố vấn chuyên môn" class="reviewer-avatar" />
                        <div class="reviewer-info">
                            <div class="reviewer-badge"><i class="fa-solid fa-shield-heart"></i> Cố Vấn Chuyên Môn</div>
                            <h4>BS. CKI NGUYỄN ĐẮC MINH</h4>
                            <div class="reviewer-title">Giám Đốc Chuyên Môn — Hệ Thống Nha Khoa Flora</div>
                            <p class="reviewer-desc">
                                Tốt nghiệp ĐH Y Dược TP.HCM, Thành viên Hiệp hội Implant Quốc tế (ITI). Toàn bộ kiến thức y khoa trên Flora được thẩm định cẩn trọng, tôn trọng nguyên tắc bảo tồn mô răng thật tối đa và phác đồ điều trị vô trùng chuẩn Thụy Sĩ.
                            </p>
                        </div>
                    </div>

                    <!-- In-Article Consultation CTA Banner -->
                    <div class="article-cta-box">
                        <div class="article-cta-content">
                            <div class="cta-pill"><i class="fa-solid fa-star"></i> Tư Vấn 1:1 Với Bác Sĩ Chuyên Khoa</div>
                            <h3>Bạn Đang Cần Tư Vấn Về Tình Trạng Răng Miệng?</h3>
                            <p>Đội ngũ bác sĩ Flora sẵn sàng thăm khám trực tiếp, chụp phim CT Cone Beam 3D và lên phác đồ điều trị cá nhân hóa hoàn toàn miễn phí.</p>
                            <a href="#dang-ky" class="article-cta-btn btn-booking">
                                <i class="fa-regular fa-calendar-check"></i> Đăng Ký Đặt Hẹn Khám Miễn Phí
                            </a>
                        </div>
                    </div>

                    <!-- Social Share Bottom Bar -->
                    <div class="single-share-bottom">
                        <div class="share-bottom-title">
                            <i class="fa-solid fa-share-nodes"></i> Chia sẻ bài viết hữu ích này:
                        </div>
                        <div class="share-bottom-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="btn-share-fb">
                                <i class="fa-brands fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://zalo.me/share?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="btn-share-zalo">
                                <strong>Zalo</strong>
                            </a>
                            <button type="button" onclick="navigator.clipboard.writeText(window.location.href); alert('Đã sao chép link bài viết thành công!');" class="btn-share-copy">
                                <i class="fa-solid fa-link"></i> Sao chép link
                            </button>
                        </div>
                    </div>

                    <!-- Related Posts Section -->
                    <?php
                    $related_query = flora_get_related_posts($post_id, 3);
                    if ($related_query->have_posts()) :
                    ?>
                        <section class="related-posts-section">
                            <div class="related-section-header">
                                <h3 class="related-posts-title">
                                    <i class="fa-solid fa-newspaper"></i> Bài Viết Cùng Chuyên Mục
                                </h3>
                                <a href="<?php echo $cat_link; ?>" class="related-view-all">Xem tất cả &rarr;</a>
                            </div>
                            <div class="posts-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 0;">
                                <?php while ($related_query->have_posts()) : $related_query->the_post(); 
                                    $r_categories = get_the_category();
                                    $r_cat_name = !empty($r_categories) ? esc_html($r_categories[0]->name) : 'Kiến thức';
                                ?>
                                    <article class="post-card">
                                        <div class="post-card-thumb">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <?php the_post_thumbnail('flora-blog-thumb', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
                                                <?php else : ?>
                                                    <img src="<?php echo flora_asset('ngayhoi_item/Kit.webp'); ?>" alt="<?php the_title(); ?>" loading="lazy" />
                                                <?php endif; ?>
                                            </a>
                                            <span class="post-card-badge"><?php echo $r_cat_name; ?></span>
                                        </div>
                                        <div class="post-card-content">
                                            <div class="post-card-meta">
                                                <span><i class="fa-regular fa-calendar"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                                <span><i class="fa-regular fa-clock"></i> <?php echo flora_reading_time(); ?></span>
                                            </div>
                                            <h4 class="post-card-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>
                                            <div class="post-card-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt(), 14, '...'); ?>
                                            </div>
                                            <div class="post-card-footer">
                                                <a href="<?php the_permalink(); ?>" class="post-read-more">
                                                    Đọc tiếp <i class="fa-solid fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </div>
                        </section>
                    <?php endif; ?>

                </article>

                <!-- RIGHT COLUMN: Sticky Sidebar Widgets -->
                <aside class="single-sidebar-col">
                    
                    <!-- Widget 1: Doctor Profile Card -->
                    <div class="sidebar-widget doctor-card-widget">
                        <div class="doctor-widget-top">
                            <div class="doctor-widget-avatar-wrap">
                                <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS. CKI Nguyễn Đắc Minh" class="doctor-widget-avatar" />
                                <span class="doctor-widget-online" title="Đang trực tư vấn"></span>
                            </div>
                            <div class="doctor-widget-header">
                                <span class="doctor-widget-tag">Bác Sĩ Cố Vấn</span>
                                <h4>BS. CKI Nguyễn Đắc Minh</h4>
                                <p>Giám đốc chuyên môn Nha khoa Flora</p>
                            </div>
                        </div>
                        <div class="doctor-widget-body">
                            <ul class="doctor-widget-creds">
                                <li><i class="fa-solid fa-certificate"></i> Tốt nghiệp ĐH Y Dược TP.HCM</li>
                                <li><i class="fa-solid fa-award"></i> Thành viên Hiệp hội Implant Quốc tế (ITI)</li>
                                <li><i class="fa-solid fa-user-check"></i> Hơn 10 năm kinh nghiệm lâm sàng</li>
                            </ul>
                            <a href="#dang-ky" class="btn btn-primary btn-booking btn-fullwidth">
                                <i class="fa-regular fa-calendar-check"></i> Đặt Lịch Tư Vấn Với Bác Sĩ
                            </a>
                        </div>
                    </div>

                    <!-- Widget 2: Fast Booking Consultation Form -->
                    <div class="sidebar-widget booking-widget-card">
                        <div class="booking-widget-header">
                            <div class="booking-badge"><i class="fa-solid fa-bolt"></i> Đăng Ký Nhanh</div>
                            <h3>Đặt Lịch Hẹn Thăm Khám</h3>
                            <p>Bác sĩ thăm khám 1:1 và tư vấn lộ trình phù hợp</p>
                        </div>
                        <form class="sidebar-booking-form" id="sidebarBookingForm">
                            <input type="text" name="name" class="sidebar-input" placeholder="Họ và tên của bạn *" required />
                            <input type="tel" name="phone" class="sidebar-input" placeholder="Số điện thoại liên hệ *" required />
                            <select name="service" class="sidebar-select">
                                <option value="">Chọn dịch vụ quan tâm...</option>
                                <option value="Trồng răng Implant">Trồng răng Implant</option>
                                <option value="Điều trị Cười hở lợi">Điều trị Cười hở lợi</option>
                                <option value="Niềng răng Chỉnh nha">Niềng răng Chỉnh nha</option>
                                <option value="Bọc răng sứ thẩm mỹ">Bọc răng sứ thẩm mỹ</option>
                                <option value="Dán sứ Veneer">Dán sứ Veneer</option>
                                <option value="Khám nha khoa tổng quát">Khám nha khoa tổng quát</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-fullwidth">
                                <span>Gửi Đăng Ký Khám Ngay</span> <i class="fa-solid fa-paper-plane" style="margin-left: 6px;"></i>
                            </button>
                            <div class="sidebar-booking-alert" id="sidebarBookingAlert"></div>
                        </form>
                    </div>

                    <!-- Widget 3: Featured Clinical Services -->
                    <div class="sidebar-widget services-widget-card">
                        <h4 class="sidebar-widget-title">
                            <i class="fa-solid fa-tooth"></i> Dịch Vụ Nha Khoa Hàng Đầu
                        </h4>
                        <div class="sidebar-services-list">
                            <a href="<?php echo esc_url(home_url('/trong-rang-implant/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-screwdriver"></i></div>
                                <div class="service-item-info">
                                    <strong>Cấy Ghép Implant Thụy Sĩ</strong>
                                    <span>Bảo hành trọn đời • Không đau</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/dieu-tri-cuoi-ho-loi/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                                <div class="service-item-info">
                                    <strong>Điều Trị Cười Hở Lợi</strong>
                                    <span>Công nghệ Laser vi phẫu 1 lần</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/nieng-rang/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-teeth"></i></div>
                                <div class="service-item-info">
                                    <strong>Niềng Răng Chỉnh Nha</strong>
                                    <span>Xem trước kết quả 3D Clincheck</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/dan-rang-su-veneer-tham-my/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-gem"></i></div>
                                <div class="service-item-info">
                                    <strong>Dán Sứ Veneer Emax</strong>
                                    <span>Bảo tồn tối đa 100% răng thật</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/bang-gia/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-tag"></i></div>
                                <div class="service-item-info">
                                    <strong>Bảng Giá Nha Khoa Mới Nhất</strong>
                                    <span>Minh bạch • Không phát sinh phí</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        </div>
                    </div>

                </aside>

            </div>
        </div>
    </div>

<?php endwhile; ?>
</main>

<!-- Script tạo Mục Lục Tự Động & Xử lý Form Sidebar -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Tự động sinh Mục Lục (TOC) từ các thẻ H2, H3
    const content = document.getElementById('singleContent');
    const tocBox = document.getElementById('floraTocBox');
    const tocList = document.getElementById('floraTocList');
    
    if (content && tocBox && tocList) {
        const headings = content.querySelectorAll('h2, h3');
        if (headings.length >= 2) {
            tocBox.style.display = 'block';
            const ul = document.createElement('ul');
            ul.className = 'flora-toc-ul';

            headings.forEach(function(heading, index) {
                let id = heading.id;
                if (!id) {
                    id = 'heading-section-' + index;
                    heading.id = id;
                }
                const li = document.createElement('li');
                li.className = heading.tagName.toLowerCase() === 'h3' ? 'toc-sub-item' : 'toc-main-item';

                const a = document.createElement('a');
                a.href = '#' + id;
                a.textContent = heading.textContent.trim();
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.getElementById(id);
                    if (target) {
                        const headerOffset = 90;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                        window.scrollTo({ top: offsetPosition, behavior: 'smooth' });
                    }
                });

                li.appendChild(a);
                ul.appendChild(li);
            });
            tocList.appendChild(ul);
        }
    }

    // 2. Xử lý Form đặt hẹn tại Sidebar
    const sidebarForm = document.getElementById('sidebarBookingForm');
    const alertBox = document.getElementById('sidebarBookingAlert');
    if (sidebarForm) {
        sidebarForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = sidebarForm.querySelector('button[type="submit"]');
            const origHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';

            const formData = new FormData(sidebarForm);
            const name = formData.get('name') || '';
            const phone = formData.get('phone') || '';
            const service = formData.get('service') || '';
            const email = formData.get('email') || '';

            // Dual sync to Google Sheets if available
            if (typeof submitLeadToSheets === 'function') {
                submitLeadToSheets({
                    formType: "Đăng ký sidebar bài viết (Single Post)",
                    fullname: name,
                    phone: phone,
                    email: email,
                    clinic: service,
                    city: 'Hồ Chí Minh',
                    interest: service,
                    date: new Date().toLocaleDateString('vi-VN'),
                    timeSlot: 'Bất kỳ lúc nào',
                    note: 'Đăng ký từ sidebar bài viết: ' + window.location.href,
                    eventSourceUrl: window.location.href
                });
            }

            formData.append('action', 'flora_booking');
            if (typeof floraData !== 'undefined' && floraData.nonce) {
                formData.append('nonce', floraData.nonce);
            }
            formData.append('source', window.location.href);

            const ajaxUrl = (typeof floraData !== 'undefined' && floraData.ajaxUrl) ? floraData.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                if (data.success) {
                    sidebarForm.reset();
                    alertBox.style.display = 'block';
                    alertBox.className = 'sidebar-booking-alert success';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + (data.data.message || 'Đăng ký thành công!');
                } else {
                    alertBox.style.display = 'block';
                    alertBox.className = 'sidebar-booking-alert error';
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> ' + (data.data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = origHtml;
                alertBox.style.display = 'block';
                alertBox.className = 'sidebar-booking-alert error';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Không thể gửi yêu cầu. Vui lòng gọi 028 7305 8999.';
            });
        });
    }
});
</script>

<?php
get_footer();
