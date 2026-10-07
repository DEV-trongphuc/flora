<?php
/**
 * Template Name: Trang Điều Trị Cười Hở Lợi
 */

get_header();
?>

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

    <!-- ─── SUBPAGE HERO SECTION (STORY GALLERY HERO) ─── -->
    <div class="hero-wrapper subpage-hero-wrapper hero-story-gallery">
        <!-- Floating Sparkles -->
        <div class="hero-sparkle hero-sparkle-1">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 0L14.8 9.2L24 12L14.8 14.8L12 24L9.2 14.8L0 12L9.2 9.2L12 0Z" fill="url(#sparkleGrad)"/></svg>
        </div>
        <div class="hero-sparkle hero-sparkle-2">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 0L14.8 9.2L24 12L14.8 14.8L12 24L9.2 14.8L0 12L9.2 9.2L12 0Z" fill="url(#sparkleGrad)"/></svg>
        </div>
        
        <section class="hero-section" id="banner" style="padding-top: 10px; padding-bottom: 20px;">
            <div class="container">
                <!-- Arched Gallery Strip -->
                <div class="hero-gallery-arc reveal" aria-label="Hình ảnh nụ cười và kết quả điều trị cười hở lợi tại Flora">
                    <div class="hero-arc-photo hero-arc-photo-1">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/1.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 1" loading="lazy" />
                    </div>
                    <div class="hero-arc-photo hero-arc-photo-2">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/2.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 2" loading="lazy" />
                    </div>
                    <div class="hero-arc-photo hero-arc-photo-3">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/3.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 3" loading="lazy" />
                    </div>
                    <div class="hero-arc-photo hero-arc-photo-4">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/4.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 4" loading="lazy" />
                    </div>
                    <div class="hero-arc-photo hero-arc-photo-5">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/5.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 5" loading="lazy" />
                    </div>
                    <div class="hero-arc-photo hero-arc-photo-6">
                        <img src="<?php echo flora_asset('assets/ho-loi/hero/6.webp'); ?>" alt="Khách hàng điều trị cười hở lợi 6" loading="lazy" />
                    </div>
                </div>

                <!-- Central Content -->
                <div class="hero-story-content reveal reveal-delay-1">
                    <h1 class="hero-title" style="color: var(--clr-navy);">
                        <span class="hero-title-line">Điều Trị Cười Hở Lợi</span>
                        <span class="highlight-text-container hero-title-line">
                            <span style="background: linear-gradient(135deg, var(--clr-primary) 0%, var(--clr-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Chuẩn Tỷ Lệ Vàng</span>
                            <svg class="heading-underline-svg" viewBox="0 0 300 20" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 12 C 100 2, 200 18, 295 10" stroke="url(#accentGrad)" stroke-width="4" stroke-linecap="round" />
                            </svg>
                        </span>
                    </h1>
                    
                    <p class="hero-description">
                        Không chỉ điều chỉnh nướu, Flora tái lập sự hài hòa giữa răng, nướu, môi và khuôn mặt. Laser hỗ trợ tạo hình viền nướu chính xác, kết hợp quy trình kiểm soát cảm giác để trải nghiệm điều trị nhẹ nhàng hơn.
                    </p>
                    
                    <div class="hero-story-actions">
                        <a href="#dang-ky" class="btn btn-primary"><span class="btn-text-desktop">Đăng Ký </span>Đặt Hẹn <i class="fa-solid fa-calendar-check" style="margin-left: 6px;"></i></a>
                        <a href="#pricing-tables" class="btn btn-outline"><span class="btn-text-desktop">Xem </span>Bảng Giá<span class="btn-text-desktop"> Trọn Gói</span></a>
                    </div>

                    <!-- Date & Location (Capsule Pills) -->
                    <div class="hero-info-pills">
                        <div class="hero-pill">
                            <i class="fa-solid fa-clock"></i>
                            <span class="pill-time">8h30 - 18h30</span>
                            <span class="pill-days">(T2 - CN)</span>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query=326+Nguy%E1%BB%85n+Th%E1%BB%8B+Minh+Khai,+Ph%C6%B0%E1%BB%9Dng+B%C3%A0n+C%E1%BB%9D,+Qu%E1%BA%ADn+3,+TP.HCM" target="_blank" class="hero-pill clickable">
                            <i class="fa-solid fa-location-dot"></i>
                            <span class="pill-label">ĐỊA ĐIỂM:</span>
                            <span class="pill-address">326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh</span>
                        </a>
                    </div>

                    <!-- 3 Benefit Cards -->
                    <div class="hero-features-single-card" style="background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1.5px solid rgba(4, 147, 241, 0.2); padding: 16px 20px; border-radius: 18px; display: flex; justify-content: space-between; align-items: center; gap: 16px; box-shadow: 0 12px 36px rgba(0, 51, 163, 0.08); width: 100%; max-width: 880px; margin: 0 auto; box-sizing: border-box;">
                        <div class="hero-feature-item" style="flex: 1 1 0; min-width: 0; display: flex; align-items: center; gap: 10px; text-align: left;">
                            <img src="<?php echo flora_asset('ngayhoi_item/HERO/sponsorship_icon.webp'); ?>" alt="Công nghệ Laser" style="width: 42px; height: 42px; flex-shrink: 0;" />
                            <div class="hero-feature-text" style="display: flex; flex-direction: column;">
                                <span style="font-size: 0.76rem; color: #475569;">Công nghệ Laser</span>
                                <strong style="font-size: 1.1rem; font-weight: 800; color: #0033a3; line-height: 1.1;">KHÔNG ĐAU</strong>
                                <span style="font-size: 0.76rem; color: #475569;">êm ái nhanh chóng</span>
                            </div>
                        </div>
                        <div class="feature-card-divider" style="width: 1px; height: 40px; background: rgba(0, 51, 163, 0.12); flex-shrink: 0;"></div>
                        <div class="hero-feature-item" style="flex: 1 1 0; min-width: 0; display: flex; align-items: center; gap: 10px; text-align: left;">
                            <img src="<?php echo flora_asset('ngayhoi_item/HERO/gift_icon.webp'); ?>" alt="Thời gian" style="width: 42px; height: 42px; flex-shrink: 0;" />
                            <div class="hero-feature-text" style="display: flex; flex-direction: column;">
                                <span style="font-size: 0.76rem; color: #475569;">Thời gian thực hiện</span>
                                <strong style="font-size: 0.95rem; font-weight: 800; color: #0033a3; line-height: 1.2; text-transform: uppercase;">CHỈ 30 PHÚT</strong>
                                <span style="font-size: 0.76rem; color: #475569;">về ngay trong ngày</span>
                            </div>
                        </div>
                        <div class="feature-card-divider" style="width: 1px; height: 40px; background: rgba(0, 51, 163, 0.12); flex-shrink: 0;"></div>
                        <div class="hero-feature-item" style="flex: 1 1 0; min-width: 0; display: flex; align-items: center; gap: 10px; text-align: left;">
                            <img src="<?php echo flora_asset('ngayhoi_item/HERO/join_icon.webp'); ?>" alt="Cam kết" style="width: 42px; height: 42px; flex-shrink: 0;" />
                            <div class="hero-feature-text" style="display: flex; flex-direction: column;">
                                <span style="font-size: 0.76rem; color: #475569;">Cam kết hiệu quả</span>
                                <strong style="font-size: 0.95rem; font-weight: 800; color: #0493f1; line-height: 1.2; text-transform: uppercase;">TRỌN ĐỜI</strong>
                                <span style="font-size: 0.76rem; color: #475569;">nướu không bò lại</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- ─── NGUYÊN NHÂN CƯỜI HỞ LỢI (SECTION 3 - FACT SECTION) ─── -->
    <section class="section-padding fact-section" id="fact">
        <div class="container">
            <div class="section-header center reveal">
                <h2>
                    Bạn Đang Gặp Tình Trạng<br/>
                    <span style="color: var(--clr-secondary);">Cười Hở Lợi Do Đâu?</span>
                </h2>
                <p style="max-width: 680px; margin: 12px auto 0; font-size: 0.95rem; color: var(--clr-text-muted);">
                    Nhận diện chính xác 3 nguyên nhân từ nướu, cơ môi và xương hàm để áp dụng phác đồ điều trị chuẩn xác, bảo tồn tối đa và ngăn ngừa tái phát.
                </p>
            </div>
            
            <div class="fact-grid grid-3-cols mobile-slider reveal reveal-delay-1" style="margin-bottom: 35px;">
                <!-- Card 1: Do nướu (răng ngắn) -->
                <div class="fact-card text-center">
                    <img src="<?php echo flora_asset('assets/ho-loi/section3/ho-loi-do-nuou-rang-cua-ngan.webp'); ?>" alt="Cười hở lợi do nướu (răng ngắn)" class="fact-card-img" width="400" height="300" loading="lazy" />
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--clr-navy); margin-bottom: 8px;">Cười hở lợi do nướu</h3>
                    <p style="font-size: 0.88rem; color: var(--clr-text-muted); line-height: 1.55;">Phần nướu che phủ nhiều khiến thân răng trông ngắn, vuông hoặc không cân đối dù vị trí môi và xương hàm bình thường.</p>
                </div>

                <!-- Card 2: Do nâng môi trên -->
                <div class="fact-card text-center">
                    <img src="<?php echo flora_asset('assets/ho-loi/section3/ho-loi-do-nang-moi-tren.webp'); ?>" alt="Cười hở lợi do nâng môi trên" class="fact-card-img" width="400" height="300" loading="lazy" />
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--clr-navy); margin-bottom: 8px;">Do cơ nâng môi trên</h3>
                    <p style="font-size: 0.88rem; color: var(--clr-text-muted); line-height: 1.55;">Cơ nâng môi trên hoạt động quá mức kéo môi lên rất cao khi cười, dù kích thước răng và viền nướu bình thường.</p>
                </div>

                <!-- Card 3: Do xương hàm trên phát triển (sử dụng hình ho-loi-da-yeu-to.webp) -->
                <div class="fact-card text-center">
                    <img src="<?php echo flora_asset('assets/ho-loi/section3/ho-loi-da-yeu-to.webp'); ?>" alt="Nụ cười lộ lợi do xương hàm trên phát triển" class="fact-card-img" width="400" height="300" loading="lazy" />
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--clr-navy); margin-bottom: 8px;">Xương hàm trên phát triển</h3>
                    <p style="font-size: 0.88rem; color: var(--clr-text-muted); line-height: 1.55;">Xương hàm trên phát triển quá mức theo chiều dọc khiến cả cung răng và mô nướu lộ rõ khi nhìn trực diện.</p>
                </div>
            </div>
            
            <div style="text-align: center; font-size: 0.88rem; color: var(--clr-text-muted); max-width: 700px; margin: 0 auto 25px;">
                <p><i class="fa-solid fa-circle-info" style="margin-right: 6px; color: var(--clr-secondary);"></i> Đúng nguyên nhân – Đúng phương pháp – Đúng tỷ lệ nụ cười chuẩn tỷ lệ vàng.</p>
            </div>
            
            <div class="text-center">
                <a href="#dang-ky" class="btn btn-primary btn-booking">Kiểm tra nguyên nhân hở lợi của tôi</a>
            </div>
        </div>
    </section>

    <!-- ─── PHƯƠNG PHÁP ĐIỀU TRỊ (SECTION 4 - VALUE SECTION) ─── -->
    <section class="section-padding value-section" id="value">
        <div class="container">
            <div class="section-header center reveal">
                <h2>
                    Giải Pháp Điều Trị Hở Lợi<br/>
                    <span style="color: var(--clr-secondary);">Toàn Diện Tại Flora</span>
                </h2>
                <p style="max-width: 720px; margin: 12px auto 0; font-size: 0.95rem; color: var(--clr-text-muted);">
                    Mỗi phương pháp được cá nhân hóa chính xác theo nguyên nhân gốc rễ, mang lại nụ cười tự nhiên, cân đối và kết quả ổn định dài lâu.
                </p>
            </div>
            <div class="value-layout">
                <div class="value-cards-grid reveal reveal-delay-1">
                    <div class="value-card">
                        <div class="value-num">01</div>
                        <div class="value-info">
                            <h3>Tạo hình nướu laser</h3>
                            <p>Sử dụng bước sóng laser tạo hình đường viền nướu nhẹ nhàng, không chảy máu, không sưng đau và hồi phục cực nhanh.</p>
                        </div>
                    </div>
                    <div class="value-card">
                        <div class="value-num">02</div>
                        <div class="value-info">
                            <h3>Làm dài thân răng + Mài xương ổ</h3>
                            <p>Cắt nướu kết hợp mài sinh lý bờ xương ổ răng bằng máy siêu âm Piezotome, tái lập khoảng sinh học, nướu bám ổn định vĩnh viễn.</p>
                        </div>
                    </div>
                    <div class="value-card">
                        <div class="value-num">03</div>
                        <div class="value-info">
                            <h3>Phẫu thuật định vị môi trên</h3>
                            <p>Can thiệp điều chỉnh cơ nâng môi trên, kiểm soát biên độ co kéo môi khi cười lớn một cách hoàn toàn tự nhiên.</p>
                        </div>
                    </div>
                    <div class="value-card">
                        <div class="value-num">04</div>
                        <div class="value-info">
                            <h3>Chỉnh nha hoặc phẫu thuật hàm</h3>
                            <p>Áp dụng đánh lún răng bằng minivis hoặc hội chẩn phẫu thuật xương hàm đối với các ca do cấu trúc xương ổ phức tạp.</p>
                        </div>
                    </div>
                </div>
                <div class="value-image-group reveal reveal-delay-2">
                    <img src="<?php echo flora_asset('assets/ho-loi/section4/tao-hinh-nuou-laser.webp'); ?>" alt="Công nghệ Laser cắt nướu Epic X" class="value-float-img img-1" style="object-fit: cover;" />
                    <img src="<?php echo flora_asset('assets/ho-loi/section4/lam-dai-than-rang-mai-xuong-o.webp'); ?>" alt="Tạo hình viền nướu thẩm mỹ hở lợi" class="value-float-img img-2" style="object-fit: cover;" />
                    <img src="<?php echo flora_asset('assets/ho-loi/section4/phau-thuat-dinh-vi-moi-tren.webp'); ?>" alt="Nụ cười hoàn mỹ chuẩn tỷ lệ vàng sau điều trị" class="value-float-img img-3" style="object-fit: cover;" />
                </div>
            </div>

        </div>
    </section>

    <!-- ─── BẢNG GIÁ CHI TIẾT ─── -->
    <section class="section-padding" id="pricing-tables" style="background-color: var(--clr-bg-light);">
        <div class="container">
            <div class="section-header center reveal">
                <h2>
                    Bảng Giá Chi Phí<br/>
                    <span style="color: var(--clr-secondary);">Điều Trị Cười Hở Lợi</span>
                </h2>
                <p style="max-width: 680px; margin: 12px auto 0; font-size: 0.95rem; color: var(--clr-text-muted);">
                    Minh bạch chi phí trọn gói trước điều trị, cam kết không phát sinh chi phí phụ.
                </p>
            </div>
            
            <?php 
            $pricing_all = flora_get_pricing_data();
            $ho_loi_data = $pricing_all['cuoi_ho_loi'] ?? array();
            $ho_loi_rows = $ho_loi_data['tables']['main']['rows'] ?? array();
            ?>
            <div class="reveal reveal-delay-1" style="max-width: 900px; margin: 0 auto 50px;">
                <h3 style="text-align: center; margin-bottom: 20px; color: var(--clr-navy); font-size: 1.2rem;"><i class="fa-solid fa-list"></i> <?php echo esc_html($ho_loi_data['tables']['main']['title'] ?? 'Đơn Giá Chi Tiết Theo Răng & Dịch Vụ'); ?></h3>
                <div style="background: var(--clr-white); border-radius: var(--radius-md); overflow-x: auto; box-shadow: var(--shadow-sm); border: 1px solid var(--clr-border);">
                    <div class="responsive-table-wrapper">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                        <thead>
                            <tr style="background: var(--clr-navy); color: var(--clr-white);">
                                <th style="padding: 15px 20px;">Dịch Vụ Điều Trị</th>
                                <th style="padding: 15px 20px;">Đơn Vị</th>
                                <th style="padding: 15px 20px; text-align: right;">Đơn Giá Trọn Gói</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ho_loi_rows as $row): 
                                $is_hl = !empty($row['highlight']);
                            ?>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td style="padding: 15px 20px; font-weight: 700; <?php if ($is_hl) echo 'color: var(--clr-secondary);'; ?>"><?php echo esc_html($row['name']); ?></td>
                                <td style="padding: 15px 20px;"><?php echo esc_html($row['unit'] ?? '1 răng'); ?></td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: <?php echo $is_hl ? 'var(--clr-secondary)' : 'var(--clr-primary)'; ?>;"><?php echo esc_html($row['price']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table></div>
                </div>
            </div>

            <!-- Price by scope (Page 44) -->
            <div class="reveal reveal-delay-2" style="max-width: 900px; margin: 0 auto;">
                <h3 style="text-align: center; margin-bottom: 20px; color: var(--clr-navy); font-size: 1.2rem;"><i class="fa-solid fa-crop"></i> Ước Tính Chi Phí Theo Phạm Vi Điều Trị</h3>
                <div style="background: var(--clr-white); border-radius: var(--radius-md); overflow-x: auto; box-shadow: var(--shadow-sm); border: 1px solid var(--clr-border);">
                    <div class="responsive-table-wrapper">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                        <thead>
                            <tr style="background: var(--clr-navy); color: var(--clr-white);">
                                <th style="padding: 15px 20px;">Phạm vi điều trị</th>
                                <th style="padding: 15px 20px; text-align: right;">Chi phí trọn gói dự kiến</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td style="padding: 15px 20px; font-weight: 700;">Làm dài thân răng đơn giản (6 răng cửa)</td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: var(--clr-primary);">~6.000.000đ</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td style="padding: 15px 20px; font-weight: 700;">Làm dài thân răng đơn giản (8 răng)</td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: var(--clr-primary);">~8.000.000đ</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border); background: rgba(4,147,241,0.02);">
                                <td style="padding: 15px 20px; font-weight: 700;">Làm dài thân răng có mài xương ổ (6 răng)</td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: var(--clr-secondary);">~12.000.000đ</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border); background: rgba(4,147,241,0.05);">
                                <td style="padding: 15px 20px; font-weight: 700;">Làm dài thân răng có mài xương ổ (8 răng)</td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: var(--clr-secondary);">~16.000.000đ</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--clr-border);">
                                <td style="padding: 15px 20px; font-weight: 700;">Phẫu thuật định vị lại môi (Bao gồm cơ nâng môi)</td>
                                <td style="padding: 15px 20px; text-align: right; font-weight: 700; color: var(--clr-primary);">~20.000.000đ</td>
                            </tr>
                        </tbody>
                    </table></div>
                </div>
            </div>

            <div class="reveal reveal-delay-3" style="max-width: 900px; margin: 30px auto 0; text-align: center; background: rgba(245, 158, 11, 0.08); padding: 20px; border-radius: var(--radius-md); border: 1px dashed rgba(245, 158, 11, 0.4);">
                <p>
                    <i class="fa-solid fa-credit-card"></i> <strong>Chính sách trả góp 0%:</strong> Hỗ trợ chia nhỏ chi phí thanh toán linh hoạt qua ngân hàng liên kết, không lãi suất.
                </p>
            </div>
        </div>
    </section>

    <!-- ─── QUY TRÌNH ĐIỀU TRỊ (TIMELINE SECTION) ─── -->
    <section class="section-padding timeline-section" id="subpage-journey">
        <div class="container">
            <div class="section-header center reveal">
                <h2>
                    6 BƯỚC ĐIỀU TRỊ<br/>
                    <span style="color: var(--clr-secondary);">CƯỜI HỞ LỢI</span>
                </h2>
                <p style="max-width: 680px; margin: 12px auto 0; font-size: 0.95rem; color: #cbd5e1;">
                    Quy trình vô trùng khép kín tiêu chuẩn Thụy Sĩ, nhẹ nhàng, an toàn và chính xác tuyệt đối.
                </p>
            </div>
            
            <div class="steps-grid steps-6 mobile-slider">
                <!-- Step 1 -->
                <div class="step-card reveal">
                    <div class="step-card-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <span class="step-badge">BƯỚC 1</span>
                    <h3>THĂM KHÁM & GHI NHẬN ĐƯỜNG CƯỜI</h3>
                    <p>Khám lâm sàng răng nướu, chụp ảnh nụ cười ở nhiều góc độ và trạng thái cơ mặt khi cười lớn.</p>
                </div>
                <!-- Step 2 -->
                <div class="step-card reveal reveal-delay-1">
                    <div class="step-card-icon"><i class="fa-solid fa-stethoscope"></i></div>
                    <span class="step-badge">BƯỚC 2</span>
                    <h3>ĐO ĐẠC XÁC ĐỊNH NGUYÊN NHÂN</h3>
                    <p>Đo độ dày mô nướu, chụp phim CT 3D khi cần thiết để đánh giá bờ xương ổ răng sinh lý dưới viền nướu.</p>
                </div>
                <!-- Step 3 -->
                <div class="step-card reveal reveal-delay-2">
                    <div class="step-card-icon"><i class="fa-solid fa-pen-ruler"></i></div>
                    <span class="step-badge">BƯỚC 3</span>
                    <h3>THIẾT KẾ TỶ LỆ & PHÁC ĐỒ</h3>
                    <p>Xác định phương pháp can thiệp, số răng cần thực hiện và mô phỏng tỷ lệ răng nướu chuẩn cung cười.</p>
                </div>
                <!-- Step 4 -->
                <div class="step-card reveal reveal-delay-3">
                    <div class="step-card-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <span class="step-badge">BƯỚC 4</span>
                    <h3>MINH BẠCH BÁO GIÁ KÝ CAM KẾT</h3>
                    <p>Thông báo kế hoạch điều trị chi tiết, ký kết cam kết không phát sinh chi phí và bắt đầu điều trị.</p>
                </div>
                <!-- Step 5 -->
                <div class="step-card reveal reveal-delay-4">
                    <div class="step-card-icon"><i class="fa-solid fa-scissors"></i></div>
                    <span class="step-badge">BƯỚC 5</span>
                    <h3>TIẾN HÀNH ĐIỀU TRỊ ÊM ÁI</h3>
                    <p>Tiến hành tạo hình nướu laser, mài xương ổ răng hoặc định vị môi dưới công nghệ gây tê DentalVibe không đau.</p>
                </div>
                <!-- Step 6 -->
                <div class="step-card reveal reveal-delay-5">
                    <div class="step-card-icon"><i class="fa-solid fa-circle-check"></i></div>
                    <span class="step-badge">BƯỚC 6</span>
                    <h3>TÁI KHÁM & THEO DÕI LÀNH THƯƠNG</h3>
                    <p>Theo dõi sát tình trạng lành thương của mô nướu, vệ sinh sát khuẩn viền lợi và hướng dẫn chăm sóc tại nhà.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── ĐIỀU TRỊ CÓ ĐAU & THỜI GIAN HỒI PHỤC (SECTION HỒI PHỤC) ─── -->
    <section class="section-padding recovery-section" id="hoi-phuc" style="background: #ffffff; border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="recovery-grid reveal reveal-delay-1">
                <!-- Cột trái: Video phương pháp kiểm soát cảm giác êm ái -->
                <div class="flora-video-frame-wrapper" style="margin: 0; width: 100%; max-width: 100%;">
                    <div class="flora-video-badge-decor">
                        <i class="fa-solid fa-play"></i> Video Trải Nghiệm Không Đau
                    </div>
                    <div class="flora-video-frame-inner">
                        <div class="flora-video-ratio-16-9">
                            <iframe src="https://www.youtube-nocookie.com/embed/68m5G1u-Ewk?rel=0&modestbranding=1&controls=1" title="Phương pháp điều trị không đau tại Nha Khoa Flora" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Nội dung giải đáp hồi phục & đau đớn -->
                <div>
                    <h2 style="font-family: var(--font-title); font-size: 1.75rem; font-weight: 700; color: var(--clr-navy); margin: 0 0 20px 0; line-height: 1.35;">
                        Điều Trị Có Đau Và Mất Nhiều Thời Gian Hồi Phục Không?
                    </h2>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px; font-size: 0.95rem; color: var(--clr-text); line-height: 1.7;">
                        <p style="margin: 0; background: var(--clr-bg-light); padding: 18px 22px; border-radius: var(--radius-sm); border-left: 4px solid var(--clr-primary);">
                            Trong quá trình thực hiện, khách hàng được gây tê tại chỗ để kiểm soát cảm giác. Sau điều trị có thể xuất hiện ê, căng tức hoặc sưng nhẹ tùy phương pháp, phạm vi can thiệp và cơ địa.
                        </p>
                        <p style="margin: 0; background: var(--clr-bg-light); padding: 18px 22px; border-radius: var(--radius-sm); border-left: 4px solid var(--clr-secondary);">
                            Tạo hình mô nướu đơn thuần thường có quá trình hồi phục nhẹ nhàng hơn so với trường hợp cần điều chỉnh xương hoặc phẫu thuật định vị môi. Bác sĩ sẽ thông báo thời gian dự kiến và hướng dẫn chăm sóc riêng sau khi xác định phương pháp.
                        </p>
                    </div>

                    <div style="margin-top: 24px; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; font-weight: 600; color: var(--clr-navy);">
                            <i class="fa-solid fa-clock-rotate-left" style="color: var(--clr-secondary); font-size: 1.1rem;"></i>
                            <span>Thời gian thực hiện: 45 - 60 phút</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; font-weight: 600; color: var(--clr-navy);">
                            <i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 1.1rem;"></i>
                            <span>Lành thương nhanh sau 3 - 5 ngày</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CA ĐIỀU TRỊ THỰC TẾ (CLINICAL CASES) ─── -->
    <section class="section-padding cases-gallery-section" id="cases" style="background-color: var(--clr-bg-light);">
        <div class="container">
            <div class="section-header center reveal">
                <h2>
                    Hình Ảnh Ca Điều Trị<br/>
                    <span style="color: var(--clr-secondary);">Cười Hở Lợi Thành Công</span>
                </h2>
                <p>Khách hàng thực tế tại Flora: Minh bạch nguyên nhân, phương pháp thực hiện và kết quả nụ cười tự nhiên.</p>
            </div>
            
            <div class="cases-content-wrapper reveal reveal-delay-1" style="margin-top: 40px;">
                <div class="cases-tab-panel mobile-slider active" id="cases-ho-loi">
                    <!-- Case 1: Chị Thanh Thư -->
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_thanh_thu.webp'); ?>" alt="Khách hàng Chị Thanh Thư - Điều trị cười hở lợi" loading="lazy" />
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
                                <span>45 phút (Lành thương sau 3 ngày).</span>
                            </div>
                        </div>
                    </div>

                    <!-- Case 2: Anh Huy Cường -->
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_huy_cuong.webp'); ?>" alt="Khách hàng Anh Huy Cường - Điều trị cười hở lợi" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Anh Huy Cường (33 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Cười hở lợi nặng do cơ nâng môi trên hoạt động quá mức, lộ nướu sẫm màu.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Phẫu thuật định vị lại môi trên kết hợp tạo hình viền nướu.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>60 phút (Kết quả tự nhiên trọn đời).</span>
                            </div>
                        </div>
                    </div>

                    <!-- Case 3: Chị Khiết Đan -->
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_khiet_dan.webp'); ?>" alt="Khách hàng Chị Khiết Đan - Điều trị cười hở lợi" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Chị Khiết Đan</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Nướu che phủ khiến chiều dài thân răng ngắn, giao tiếp lộ rõ nướu. Xương ổ răng dày, gồ ghề ảnh hưởng thẩm mỹ nụ cười.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Phẫu thuật tạo hình nướu và mài chỉnh xương ổ 10 răng.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>45 phút.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Case 4: Chị Mộng Tuyền -->
                    <div class="case-card">
                        <div class="case-img-wrap">
                            <img src="<?php echo flora_asset('ngayhoi_item/bf_at/case_holoi_mong_tuyen.webp'); ?>" alt="Khách hàng Chị Mộng Tuyền - Điều trị cười hở lợi" loading="lazy" />
                        </div>
                        <div class="case-card-body">
                            <h4>Chị Mộng Tuyền (31 tuổi)</h4>
                            <div class="case-detail-row">
                                <strong>Tình trạng ban đầu:</strong>
                                <span>Răng xỉn màu vàng ố, viền nướu phì đại không cân xứng trục cung cười.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Phương pháp điều trị:</strong>
                                <span>Tạo hình viền nướu bằng laser kết hợp tẩy trắng răng công nghệ cao.</span>
                            </div>
                            <div class="case-detail-row">
                                <strong>Thời gian thực hiện:</strong>
                                <span>45 phút (Răng trắng sáng, viền nướu hồng hào).</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── BÁC SĨ PHỤ TRÁCH CHUYÊN MÔN (EXPERT SECTION) ─── -->
    <section class="section-padding expert-section" id="expert" style="background-color: var(--clr-bg-light); border-top: 1px solid var(--clr-border);">
        <div class="container">
            <div class="section-header center reveal">
                <h2>Bác Sĩ Trực Tiếp Điều Trị Tại Flora</h2>
            </div>
            <div class="expert-grid reveal">
                <!-- Left Column: Premium Framed portrait image -->
                <div class="reveal-left expert-portrait-frame">
                    <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS.CKI Nguyễn Đắc Minh - Bác sĩ phụ trách chuyên môn tại Flora" class="expert-portrait-img" loading="lazy" width="480" height="580" style="object-fit: cover; object-position: 55% 10%; border-radius: 16px;" />
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
                        <a href="#dang-ky" class="btn btn-primary btn-booking" style="display: inline-block;">Đặt lịch tư vấn cùng Bác sĩ Minh <i class="fa-solid fa-calendar-check" style="margin-left: 8px;"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── REGISTRATION SECTION (FAQ + FORM SPLIT) ─── -->
    <section class="section-padding registration-section" id="dang-ky">
        <div class="container">
            <div class="reg-split-container">
                <!-- Left Column: FAQ Accordion -->
                <div class="reveal" style="display: flex; flex-direction: column; justify-content: flex-start; gap: 20px;">
                    <div>
                        <h3 style="font-family: var(--font-title); font-size: 1.6rem; font-weight: 700; color: var(--clr-navy); margin: 0 0 10px 0;">Câu Hỏi Thường Gặp</h3>
                        <p style="font-size: 0.9rem; color: var(--clr-text-muted); margin: 0 0 20px 0; line-height: 1.6;">Những băn khoăn phổ biến của khách hàng khi điều trị cười hở lợi tại Nha khoa Flora.</p>
                    </div>
                    <div class="faq-list" style="margin-top: 0;">
                        <div class="faq-item active">
                            <button class="faq-question">1. Có phải cười hở lợi nào cũng cần cắt nướu không? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Không. Nếu nguyên nhân nằm ở cơ môi hoạt động quá mức, sai khớp cắn hoặc do xương hàm trên phát triển theo chiều dọc, cắt lợi đơn thuần sẽ không giải quyết được vấn đề chính. Bác sĩ Flora sẽ khám và tư vấn phương pháp can thiệp đúng nguyên nhân gốc rễ.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">2. Cắt lợi bằng Laser có tốt hơn các phương pháp khác? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Laser rất hữu ích trong trường hợp điều chỉnh mô mềm, giúp cầm máu tức thì và không sưng đau. Tuy nhiên, nếu cần điều chỉnh khoảng sinh học bờ xương ổ răng, bác sĩ sẽ kết hợp máy siêu âm Piezotome để đạt kết quả bền vững nhất.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">3. Điều trị cười hở lợi xong nướu có bị bò lại không? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Mức độ ổn định phụ thuộc vào tương quan giữa nướu và xương sinh lý. Tại Flora, bác sĩ luôn đánh giá khoảng sinh học trước khi thực hiện. Khi được lập phác đồ chuẩn xác và tái lập đúng bờ xương ổ, viền nướu sẽ ổn định vĩnh viễn và không bị bò lại.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">4. Botulinum toxin có điều trị hở lợi vĩnh viễn không? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Không. Hiệu quả của Botulinum toxin mang tính tạm thời (khoảng 4 - 6 tháng) và cần tiêm nhắc lại. Đối với khách hàng muốn kết quả ổn định vĩnh viễn do cơ môi, phẫu thuật định vị môi trên là giải pháp tối ưu.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">5. Cần điều chỉnh bao nhiêu răng khi cắt nướu? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Thông thường bác sĩ sẽ đánh giá toàn bộ các răng nằm trong vùng cung cười (khoảng 6 đến 8 răng cửa hàm trên) thay vì chỉ một răng riêng lẻ, nhằm đảm bảo đường cười đối xứng và tỷ lệ nụ cười hoàn mỹ nhất.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">6. Thời gian điều trị và lành thương là bao lâu? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Quá trình tạo hình viền nướu diễn ra êm ái trong khoảng 45 - 60 phút dưới gây tê không đau DentalVibe. Bạn có thể ăn uống và sinh hoạt bình thường ngay sau phẫu thuật; mô nướu lành thương đẹp tự nhiên sau 3 - 7 ngày.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Registration Form -->
                <div class="cta-form reveal reveal-delay-2" style="background: #ffffff; border: 1px solid var(--clr-border); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-premium);">
                    <h3 style="color: var(--clr-navy); font-size: 1.35rem; margin-bottom: 8px; text-align: center; font-weight: 800; line-height: 1.3;">ĐĂNG KÝ THĂM KHÁM 1:1 CÙNG BÁC SĨ</h3>
                    <p style="font-size: 0.92rem; color: var(--clr-secondary); text-align: center; margin-bottom: 24px; font-weight: 700; line-height: 1.4;">Miễn phí chụp phim CT Cone Beam và lập phác đồ điều trị cá nhân</p>
                    
                    <form id="floraRegistrationForm" class="modal-form" style="display: flex; flex-direction: column; gap: 15px;">
                        <input type="hidden" id="note" name="note" value="" />
                        <div class="form-group">
                            <input class="form-input" name="name" placeholder="Họ và tên của bạn" required type="text" style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);" />
                        </div>
                        <div class="form-group">
                            <input class="form-input" name="phone" placeholder="Số điện thoại liên hệ" required type="tel" style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);" />
                        </div>
                        <div class="form-group">
                            <input class="form-input" name="email" placeholder="Địa chỉ Email" required type="email" style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);" />
                        </div>
                        <div class="form-group">
                            <input class="form-input" name="location" placeholder="Nơi ở (Tỉnh / Thành phố / Quận)" type="text" style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);" />
                        </div>
                        <div class="form-group">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; color: var(--clr-navy); margin-bottom: 6px; text-align: left;">Giới tính:</label>
                            <div style="display: flex; gap: 10px;">
                                <label style="flex: 1; text-align: center; border: 1px solid var(--clr-border); padding: 10px; border-radius: var(--radius-sm); background: var(--clr-white); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; font-size: 0.9rem; transition: var(--transition);">
                                    <input type="radio" name="gender" value="Nam" checked style="display: none;" />
                                    <i class="fa-solid fa-mars" style="color: #0084ff;"></i> Nam
                                </label>
                                <label style="flex: 1; text-align: center; border: 1px solid var(--clr-border); padding: 10px; border-radius: var(--radius-sm); background: var(--clr-white); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-weight: 600; font-size: 0.9rem; transition: var(--transition);">
                                    <input type="radio" name="gender" value="Nữ" style="display: none;" />
                                    <i class="fa-solid fa-venus" style="color: #ff007f;"></i> Nữ
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-select" name="service" required style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);">
                                <option value="Điều trị cười hở lợi" selected>Điều trị cười hở lợi</option>
                                <option value="Trồng răng Implant">Trồng răng Implant Thụy Sĩ</option>
                                <option value="Niềng răng chỉnh nha">Niềng răng Invisalign / Mắc cài</option>
                                <option value="Dán sứ thẩm mỹ Veneer">Dán sứ Veneer E.max</option>
                                <option value="Bọc răng sứ">Bọc răng sứ thẩm mỹ</option>
                                <option value="Nha khoa tổng quát">Khám răng tổng quát / Lấy cao răng</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-select" name="preferredTime" required style="background: #ffffff; border-color: var(--clr-border); color: var(--clr-text);">
                                <option value="" disabled selected>Thời gian muốn được liên hệ</option>
                                <option value="Sáng (8h00 - 12h00)">Sáng (8h00 - 12h00)</option>
                                <option value="Chiều (13h30 - 17h30)">Chiều (13h30 - 17h30)</option>
                                <option value="Tối (18h00 - 20h00)">Tối (18h00 - 20h00)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding: 14px; margin-top: 10px; font-size: 1rem; font-weight: bold; width: 100%;">Gửi Đăng Ký Đặt Lịch</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── FOOTER ─── -->

<?php
get_footer();
