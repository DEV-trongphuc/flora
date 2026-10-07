<?php
/**
 * Archive & Category & Blog Template (Góc Sức Khỏe & Cẩm Nang Y Khoa)
 * Tự động đồng bộ và hiển thị toàn bộ 917 bài viết cũ theo phong cách Tạp chí Y khoa Thụy Sĩ hiện đại
 */

get_header();

$current_cat_id = is_category() ? get_queried_object_id() : 0;
$total_posts_found = $wp_query->found_posts ?? 0;
$blog_home_url = get_permalink(get_option('page_for_posts')) ? get_permalink(get_option('page_for_posts')) : home_url('/goc-suc-khoe/');
?>

<main id="primary" class="site-main blog-archive-page">

    <!-- ─── HERO HEADER ─── -->
    <section class="blog-archive-header">
        <div class="container" style="max-width: 1200px !important;">
            <?php if (is_category()) : 
                $term = get_queried_object();
            ?>
                <span class="hero-cat-badge">
                    <i class="fa-solid fa-tooth"></i> Chuyên Mục Y Khoa
                </span>
                <h1><?php single_cat_title(); ?></h1>
                <p><?php echo category_description() ? category_description() : 'Tổng hợp các bài viết chuyên môn, tư vấn điều trị và hướng dẫn chăm sóc răng miệng từ đội ngũ Bác sĩ CKI Nha khoa Flora.'; ?></p>

            <?php elseif (is_tag()) : ?>
                <span class="hero-cat-badge">
                    <i class="fa-solid fa-hashtag"></i> Chủ Đề Quan Tâm
                </span>
                <h1>Chủ đề: #<?php single_tag_title(); ?></h1>
                <p>Tất cả bài viết và hướng dẫn chuyên môn liên quan đến chủ đề #<?php single_tag_title(); ?>.</p>

            <?php elseif (is_search()) : ?>
                <span class="hero-cat-badge">
                    <i class="fa-solid fa-magnifying-glass"></i> Kết Quả Tìm Kiếm
                </span>
                <h1>Từ khóa: "<?php echo esc_html(get_search_query()); ?>"</h1>
                <p>Tìm thấy <strong><?php echo esc_html($total_posts_found); ?></strong> bài viết phù hợp với thông tin bạn đang tra cứu.</p>

            <?php elseif (is_author()) : ?>
                <span class="hero-cat-badge">
                    <i class="fa-solid fa-user-doctor"></i> Tác Giả Chuyên Môn
                </span>
                <h1>Bác sĩ: <?php echo get_the_author(); ?></h1>
                <p>Các bài viết được biên soạn và thẩm định y khoa bởi <?php echo get_the_author(); ?>.</p>

            <?php else : ?>
                <span class="hero-cat-badge">
                    <i class="fa-solid fa-shield-heart"></i> Cẩm Nang Y Khoa Chuẩn Thụy Sĩ
                </span>
                <h1>Góc Sức Khỏe & Ca Điều Trị Thực Tế</h1>
                <p>Cung cấp kiến thức y khoa chính thống, phác đồ điều trị tiêu chuẩn châu Âu và minh chứng lâm sàng thực tế từ đội ngũ Bác sĩ CKI Nha khoa Flora.</p>
            <?php endif; ?>

            <!-- Trust Badges Row -->
            <div class="blog-hero-trust-row">
                <span class="trust-item"><i class="fa-solid fa-book-medical"></i> 900+ Bài Viết Y Khoa</span>
                <span class="trust-item"><i class="fa-solid fa-user-doctor"></i> 100% Thẩm Định Bác Sĩ CKI</span>
                <span class="trust-item"><i class="fa-solid fa-award"></i> Tiêu Chuẩn Thụy Sĩ</span>
            </div>

            <!-- Integrated Search Form -->
            <div class="blog-hero-search-wrap">
                <form role="search" method="get" class="blog-hero-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="search" class="search-field" placeholder="Tìm kiếm bệnh lý, niềng răng, bọc sứ, giá trồng răng implant..." value="<?php echo get_search_query(); ?>" name="s" required />
                    <button type="submit" class="search-btn">Tìm Kiếm</button>
                </form>
            </div>
        </div>
    </section>

    <!-- ─── BREADCRUMBS & CATEGORY PILLS ─── -->
    <div class="blog-subnav-section">
        <div class="container" style="max-width: 1280px !important;">
            <div class="single-breadcrumbs-wrapper">
                <?php flora_render_breadcrumbs(); ?>
            </div>

            <!-- Category Filter Pills -->
            <div class="category-filter-pills">
                <a href="<?php echo esc_url($blog_home_url); ?>" class="cat-pill <?php echo (!is_category() && !is_tag() && !is_search()) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-list-ul"></i> Tất Cả Bài Viết
                </a>
                <?php
                $popular_cats = array(
                    'nieng-rang'         => array('name' => 'Niềng Răng', 'icon' => 'fa-teeth-open'),
                    'boc-rang-su'        => array('name' => 'Bọc Răng Sứ', 'icon' => 'fa-tooth'),
                    'trong-rang-implant' => array('name' => 'Implant Thụy Sĩ', 'icon' => 'fa-screwdriver'),
                    'cuoi-ho-loi'        => array('name' => 'Cười Hở Lợi', 'icon' => 'fa-face-smile'),
                    'nho-rang-khon'      => array('name' => 'Nhổ Răng Khôn', 'icon' => 'fa-wand-magic-sparkles'),
                    'kien-thuc'          => array('name' => 'Kiến Thức Chung', 'icon' => 'fa-notes-medical'),
                    'tin-tuc-su-kien'    => array('name' => 'Tin Tức & Sự Kiện', 'icon' => 'fa-bullhorn'),
                );

                foreach ($popular_cats as $slug => $cat_data) {
                    $term = get_category_by_slug($slug);
                    if ($term) {
                        $is_active = ($current_cat_id === $term->term_id) ? 'active' : '';
                        echo '<a href="' . esc_url(get_category_link($term->term_id)) . '" class="cat-pill ' . $is_active . '">';
                        echo '<i class="fa-solid ' . esc_attr($cat_data['icon']) . '"></i> ';
                        echo esc_html($cat_data['name']) . ' <span class="cat-count">(' . $term->count . ')</span>';
                        echo '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <div class="container" style="max-width: 1280px !important; padding-bottom: 60px;">

        <?php if (have_posts()) : ?>

            <!-- ─── FEATURED SPOTLIGHT POST (Only Page 1 of Blog Index) ─── -->
            <?php if (!is_paged() && !is_search() && have_posts()) : 
                the_post();
                $featured_cats = get_the_category();
                $featured_cat_name = !empty($featured_cats) ? esc_html($featured_cats[0]->name) : 'Tiêu điểm';
            ?>
                <section class="blog-featured-section">
                    <div class="featured-post-card">
                        <div class="featured-post-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('flora-blog-featured', array('alt' => get_the_title(), 'loading' => 'eager')); ?>
                                <?php else : ?>
                                    <img src="<?php echo flora_asset('ngayhoi_item/Kit.webp'); ?>" alt="<?php the_title(); ?>" />
                                <?php endif; ?>
                            </a>
                            <span class="featured-badge"><i class="fa-solid fa-star"></i> BÀI VIẾT NỔI BẬT</span>
                        </div>
                        <div class="featured-post-content">
                            <span class="post-category-tag"><?php echo $featured_cat_name; ?></span>
                            <h2 class="featured-post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            <p class="featured-post-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 28, '...'); ?>
                            </p>
                            <div class="featured-post-author-row">
                                <div class="author-info-group">
                                    <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS. CKI Nguyễn Đắc Minh" class="author-avatar-img" />
                                    <div>
                                        <div class="author-name">BS. CKI Nguyễn Đắc Minh</div>
                                        <div class="post-meta-sub">
                                            <span><i class="fa-regular fa-calendar"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                            <span>•</span>
                                            <span><i class="fa-regular fa-clock"></i> <?php echo flora_reading_time(); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary featured-btn">
                                    <span>Đọc Bài Viết</span> <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- ─── TWO COLUMN MAIN LAYOUT ─── -->
            <div class="single-post-layout archive-layout-grid">
                
                <!-- LEFT COLUMN: Articles Grid & Pagination -->
                <div class="archive-main-col">
                    
                    <div class="archive-list-header">
                        <h2 class="archive-list-title">
                            <?php if (is_search()) : ?>
                                <i class="fa-solid fa-magnifying-glass"></i> Danh sách bài viết phù hợp
                            <?php elseif (is_category()) : ?>
                                <i class="fa-solid fa-layer-group"></i> Bài viết chuyên mục: <?php single_cat_title(); ?>
                            <?php else : ?>
                                <i class="fa-solid fa-newspaper"></i> Danh Sách Bài Viết Mới Nhất
                            <?php endif; ?>
                        </h2>
                        <span class="archive-count-badge">
                            <i class="fa-regular fa-folder-open"></i> <?php echo esc_html($total_posts_found); ?> bài viết
                        </span>
                    </div>

                    <!-- Posts Grid (2-column layout inside main area) -->
                    <div class="archive-posts-grid">
                        <?php while (have_posts()) : the_post(); 
                            $card_cats = get_the_category();
                            $card_cat_name = !empty($card_cats) ? esc_html($card_cats[0]->name) : 'Kiến thức';
                        ?>
                            <article class="post-card">
                                <div class="post-card-thumb">
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                        <?php if (has_post_thumbnail()) : ?>
                                            <?php the_post_thumbnail('flora-blog-thumb', array('alt' => get_the_title(), 'loading' => 'lazy')); ?>
                                        <?php else : ?>
                                            <img src="<?php echo flora_asset('ngayhoi_item/Kit.webp'); ?>" alt="<?php the_title(); ?>" loading="lazy" />
                                        <?php endif; ?>
                                    </a>
                                    <span class="post-card-badge"><?php echo $card_cat_name; ?></span>
                                </div>
                                <div class="post-card-content">
                                    <div class="post-card-meta">
                                        <span><i class="fa-regular fa-calendar"></i> <?php echo get_the_date('d/m/Y'); ?></span>
                                        <span><i class="fa-regular fa-clock"></i> <?php echo flora_reading_time(); ?></span>
                                    </div>
                                    <h3 class="post-card-title">
                                        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="post-card-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?>
                                    </div>
                                    <div class="post-card-footer">
                                        <span class="author-mini">
                                            <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="Bác sĩ Flora" />
                                            <span>BS. CKI Minh</span>
                                        </span>
                                        <a href="<?php the_permalink(); ?>" class="post-read-more" title="Đọc tiếp bài viết">
                                            Đọc tiếp <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <!-- Modern Pagination -->
                    <div class="flora-pagination">
                        <?php
                        echo paginate_links(array(
                            'prev_text' => '<i class="fa-solid fa-chevron-left"></i> Trang trước',
                            'next_text' => 'Trang sau <i class="fa-solid fa-chevron-right"></i>',
                            'type'      => 'plain',
                            'mid_size'  => 2,
                            'end_size'  => 1,
                        ));
                        ?>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Sticky Sidebar (Independently Scrollable) -->
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
                                <h4 class="doctor-widget-name">BS. CKI Nguyễn Đắc Minh</h4>
                                <p class="doctor-widget-sub">Giám đốc chuyên môn Nha khoa Flora</p>
                            </div>
                        </div>
                        <ul class="doctor-widget-bullets">
                            <li><i class="fa-solid fa-certificate"></i> Tốt nghiệp ĐH Y Dược TP.HCM</li>
                            <li><i class="fa-solid fa-award"></i> Thành viên Hiệp hội Implant Quốc tế (ITI)</li>
                            <li><i class="fa-solid fa-user-check"></i> Hơn 10 năm kinh nghiệm lâm sàng</li>
                        </ul>
                        <a href="#dang-ky" class="btn btn-primary btn-fullwidth btn-booking">
                            <i class="fa-regular fa-calendar-check"></i> Đặt Lịch Tư Vấn Với Bác Sĩ
                        </a>
                    </div>

                    <!-- Widget 2: Fast Consultation Booking Form (Saves directly to Database) -->
                    <div class="sidebar-widget booking-widget-card" id="sidebarBookingSection">
                        <div class="booking-widget-badge">
                            <i class="fa-solid fa-bolt"></i> Đăng Ký Nhanh
                        </div>
                        <h4 class="booking-widget-title">Đặt Lịch Hẹn Thăm Khám</h4>
                        <p class="booking-widget-desc">Bác sĩ thăm khám 1:1, chụp phim CT 3D và tư vấn lộ trình điều trị chuẩn y khoa miễn phí.</p>
                        
                        <form class="sidebar-booking-form" id="sidebarBookingForm">
                            <input type="text" name="name" class="sidebar-input" placeholder="Họ và tên của bạn *" required />
                            <input type="tel" name="phone" class="sidebar-input" placeholder="Số điện thoại liên hệ *" required />
                            <select name="service" class="sidebar-select">
                                <option value="Cấy ghép Implant Thụy Sĩ">Cấy ghép Implant Thụy Sĩ</option>
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

                    <!-- Widget 3: Popular Categories -->
                    <div class="sidebar-widget categories-widget-card">
                        <h4 class="sidebar-widget-title">
                            <i class="fa-solid fa-folder-tree"></i> Chuyên Mục Nổi Bật
                        </h4>
                        <div class="sidebar-categories-list">
                            <?php
                            $sidebar_cats = get_categories(array(
                                'orderby' => 'count',
                                'order'   => 'DESC',
                                'number'  => 6,
                                'hide_empty' => 1
                            ));
                            foreach ($sidebar_cats as $s_cat) :
                            ?>
                                <a href="<?php echo esc_url(get_category_link($s_cat->term_id)); ?>" class="sidebar-category-link">
                                    <span class="cat-link-title">
                                        <i class="fa-solid fa-chevron-right"></i> <?php echo esc_html($s_cat->name); ?>
                                    </span>
                                    <span class="cat-link-count"><?php echo $s_cat->count; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Widget 4: Featured Clinical Services -->
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
                                <div class="service-item-icon"><i class="fa-solid fa-face-smile"></i></div>
                                <div class="service-item-info">
                                    <strong>Điều Trị Cười Hở Lợi</strong>
                                    <span>Chỉ 45 phút • Đẹp trọn đời</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/nieng-rang/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-teeth-open"></i></div>
                                <div class="service-item-info">
                                    <strong>Niềng Răng Thẩm Mỹ</strong>
                                    <span>Mắc cài & Khay trong suốt Invisalign</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/dan-rang-su-veneer-tham-my/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-gem"></i></div>
                                <div class="service-item-info">
                                    <strong>Dán Sứ Veneer Cao Cấp</strong>
                                    <span>Bảo tồn răng thật tối đa</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                            <a href="<?php echo esc_url(home_url('/bang-gia/')); ?>" class="sidebar-service-item">
                                <div class="service-item-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                <div class="service-item-info">
                                    <strong>Bảng Giá Trọn Gói 2026</strong>
                                    <span>Minh bạch, không phát sinh chi phí</span>
                                </div>
                                <i class="fa-solid fa-chevron-right arrow-icon"></i>
                            </a>
                        </div>
                    </div>

                </aside>

            </div>

        <?php else : ?>

            <!-- EMPTY STATE -->
            <div class="archive-empty-state">
                <div class="empty-icon-wrap">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <h3>Không tìm thấy bài viết phù hợp</h3>
                <p>Nội dung bạn đang tra cứu hiện chưa có hoặc từ khóa tìm kiếm chưa chính xác. Bạn vui lòng thử lại với từ khóa khác hoặc quay về trang chủ cẩm nang.</p>
                <div class="empty-actions">
                    <a href="<?php echo esc_url($blog_home_url); ?>" class="btn btn-primary">
                        <i class="fa-solid fa-house"></i> Xem Tất Cả Bài Viết
                    </a>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <!-- ─── CA ĐIỀU TRỊ THỰC TẾ (LÂM SÀNG TIÊU BIỂU) ─── -->
    <section class="section-padding cases-gallery-section" id="cases" style="background-color: #f1f5f9; padding: 70px 0;">
        <div class="container" style="max-width: 1200px !important;">
            <div class="section-header center" style="text-align: center; margin-bottom: 35px;">
                <span class="curved-badge" style="background: rgba(0,51,163,0.1); color: var(--clr-navy, #0033a3); font-weight: 700; font-size: 0.8rem; margin-bottom: 10px; display: inline-block;">Minh Chứng Thực Tế</span>
                <h2 style="font-size: 2rem; color: var(--clr-navy, #0033a3); font-weight: 800; margin-bottom: 10px;">Ca Điều Trị Lâm Sàng Tiêu Biểu</h2>
                <p style="max-width: 650px; margin: 0 auto; font-size: 0.95rem; color: var(--clr-text-muted, #64748b);">Hình ảnh thực tế minh chứng hiệu quả điều trị từ hồ sơ bệnh án khách hàng Nha khoa Flora.</p>
            </div>

            <!-- Tab Selectors -->
            <div class="cases-tabs-container">
                <button class="cases-tab-btn active" data-tab="implant"><i class="fa-solid fa-screwdriver"></i> Cấy Ghép Implant</button>
                <button class="cases-tab-btn" data-tab="ho-loi"><i class="fa-solid fa-face-smile"></i> Điều Trị Cười Hở Lợi</button>
                <button class="cases-tab-btn" data-tab="nieng-rang"><i class="fa-solid fa-teeth-open"></i> Niềng Răng Thẩm Mỹ</button>
                <button class="cases-tab-btn" data-tab="rang-su"><i class="fa-solid fa-gem"></i> Răng Sứ & Veneer</button>
            </div>

            <!-- Tab Panels -->
            <div class="cases-content-wrapper">
                <!-- Panel 1: Implant -->
                <div class="cases-tab-panel mobile-slider active" id="cases-implant">
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_implant_ah_sen.webp'); ?>" alt="Khách hàng Chú Ah Sen - Phục hình Implant Thụy Sĩ" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Chú Ah Sen (55 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Mất nhiều răng cả 2 hàm, tiêu xương nặng gây ăn nhai kém. Mất thẩm mỹ.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Ghép xương 0.5cc, cấy ghép 4 trụ Implant hàm trên, 5 trụ hàm dưới.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>20 tuần hoàn tất phục hình cố định.</span>
                            </div>
                        </div>
                    </div>
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_implant_co_hoa.webp'); ?>" alt="Khách hàng Cô Hoa - Phục hình Implant Thụy Sĩ" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Cô Hoa (56 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Mất nhiều răng hàm ở hàm trên và hàm dưới; mô mềm săn chắc, đủ khoảng cấy ghép.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Cấy ghép 9 trụ Implant Thụy Sĩ đơn lẻ, 12 răng sứ Zirconia trên Implant.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>24 tuần phục hồi toàn diện chức năng ăn nhai.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 2: Cười hở lợi -->
                <div class="cases-tab-panel mobile-slider" id="cases-ho-loi">
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_thanh_thu.webp'); ?>" alt="Khách hàng Chị Thanh Thư" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Chị Thanh Thư (28 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Cười lộ nướu nhiều > 4mm, thân răng ngắn, viền nướu không đều màu.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Phẫu thuật tạo hình viền nướu thẩm mỹ bằng Laser không đau.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>45 phút (Lành thương sau 3 ngày, không tái phát).</span>
                            </div>
                        </div>
                    </div>
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_huy_cuong.webp'); ?>" alt="Khách hàng Anh Huy Cường" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Anh Huy Cường (33 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Cười hở lợi nặng do cơ nâng môi trên hoạt động quá mức, lộ nướu sẫm màu.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Phẫu thuật định vị lại môi trên kết hợp tạo hình viền nướu chuẩn tỷ lệ vàng.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>60 phút thực hiện nhẹ nhàng.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 3: Niềng răng -->
                <div class="cases-tab-panel mobile-slider" id="cases-nieng-rang">
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_niengrang_1.webp'); ?>" alt="Ca niềng răng Invisalign" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Bạn Quỳnh Trang (22 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Răng khấp khểnh nặng hàm trên, khớp cắn sâu, cản trở phát âm.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Niềng răng máng trong suốt Invisalign SmartTrack công nghệ TRIOS 3D.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>18 tháng sở hữu nụ cười đều tăm tắp.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel 4: Răng sứ & Veneer -->
                <div class="cases-tab-panel mobile-slider" id="cases-rang-su">
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/veneer_bich_huyen_at.webp'); ?>" alt="Dán sứ Veneer Emax" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Chị Lê Thị Bích Huyền</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Răng nhiễm màu vàng ố, bề mặt men răng mòn không đều.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Dán sứ Veneer E.max CAD bảo tồn tối đa răng thật, phủ màu nụ cười tươi sáng.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>3 ngày (2 lần hẹn thẩm mỹ).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Consultation Banner -->
            <div class="cases-cta-box" style="margin-top: 50px;">
                <div class="cta-inner-card" style="background: white; border: 1px solid var(--clr-border, #e2e8f0); border-radius: 20px; padding: 35px 40px; box-shadow: 0 10px 30px rgba(0, 51, 163, 0.08); display: flex; align-items: center; justify-content: space-between; gap: 30px; flex-wrap: wrap;">
                    <div class="cta-content" style="max-width: 750px;">
                        <h3 style="color: var(--clr-navy, #0033a3); font-size: 1.4rem; font-weight: 800; margin-bottom: 8px;">Bạn Đang Gặp Tình Trạng Răng Miệng Tương Tự?</h3>
                        <p style="font-size: 0.95rem; color: var(--clr-text-muted, #64748b); margin: 0; line-height: 1.6;">Đăng ký thăm khám ngay hôm nay để được BS. CKI Nguyễn Đắc Minh trực tiếp kiểm tra, chụp phim CT Cone Beam 3D và lên phác đồ điều trị hoàn toàn miễn phí!</p>
                    </div>
                    <a href="#dang-ky" class="btn btn-primary btn-booking cta-btn" style="padding: 14px 32px; border-radius: 30px; font-weight: 700; white-space: nowrap;">
                        <i class="fa-solid fa-calendar-check"></i> Đăng Ký Khám Miễn Phí
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Cases Tab Switching
    const tabBtns = document.querySelectorAll('.cases-tab-btn');
    const tabPanels = document.querySelectorAll('.cases-tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = 'cases-' + this.getAttribute('data-tab');
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            const targetPanel = document.getElementById(targetId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });

    // 2. Sidebar Booking Form AJAX
    const sidebarForm = document.getElementById('sidebarBookingForm');
    const alertBox = document.getElementById('sidebarBookingAlert');
    if (sidebarForm) {
        sidebarForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = sidebarForm.querySelector('button[type="submit"]');
            const originalBtnText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Đang gửi...';
            btn.disabled = true;

            const formData = new FormData(sidebarForm);
            const name = formData.get('name') || '';
            const phone = formData.get('phone') || '';
            const service = formData.get('service') || '';
            const email = formData.get('email') || '';

            // Dual sync to Google Sheets if available
            if (typeof submitLeadToSheets === 'function') {
                submitLeadToSheets({
                    formType: "Đăng ký sidebar chuyên mục (Archive Blog)",
                    fullname: name,
                    phone: phone,
                    email: email,
                    clinic: service,
                    city: 'Hồ Chí Minh',
                    interest: service,
                    date: new Date().toLocaleDateString('vi-VN'),
                    timeSlot: 'Bất kỳ lúc nào',
                    note: 'Đăng ký từ sidebar chuyên mục: ' + window.location.href,
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
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
                if (data.success) {
                    sidebarForm.reset();
                    alertBox.className = 'sidebar-booking-alert success';
                    alertBox.style.display = 'block';
                    alertBox.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + (data.data.message || 'Đăng ký thành công!');
                } else {
                    alertBox.className = 'sidebar-booking-alert error';
                    alertBox.style.display = 'block';
                    alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> ' + (data.data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(err => {
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
                alertBox.className = 'sidebar-booking-alert error';
                alertBox.style.display = 'block';
                alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Không thể gửi yêu cầu. Vui lòng liên hệ lại sau!';
            });
        });
    }
});
</script>

<?php
get_footer();
