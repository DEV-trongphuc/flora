<?php
/**
 * Template Name: Trang Bảng Giá
 */

get_header();
?>

<!-- ─── HERO BANNER ─── -->
    <div class="subpage-hero-banner" id="banner">
        <picture>
            <source srcset="<?php echo flora_asset('assets/banner_bang_gia.webp'); ?>" type="image/webp">
            <img src="<?php echo flora_asset('assets/banner_bang_gia.png'); ?>" alt="Flora - Hơn 7 Năm Kiến Tạo 50.000+ Nụ Cười - Bảng Giá Dịch Vụ Nha Khoa" width="3760" height="1175" loading="eager" fetchpriority="high">
        </picture>
    </div>

    <!-- ─── BẢNG GIÁ CHÍNH ─── -->
    <section class="section-padding pricing-page-section">
        <div class="container">
            <div class="section-header center reveal">
                <h2>Chi Phí Dịch Vụ Nha Khoa Chi Tiết</h2>
                <p style="max-width: 600px; margin: 10px auto 0; font-size: 0.95rem; color: var(--clr-text-muted);">Cam kết không phát sinh bất kỳ khoản chi phí ẩn nào ngoài hợp đồng điều trị.</p>
            </div>

             <div style="max-width: 950px; margin: 40px auto; display: flex; flex-direction: column; gap: 40px;">
                <!-- 1. Bảng giá Implant -->
                <?php flora_render_pricing_card_implant(); ?>

                <!-- 2. Bảng giá Niềng răng -->
                <?php flora_render_pricing_card_braces(); ?>

                <!-- 3. Răng sứ & Veneer -->
                <?php flora_render_pricing_card_porcelain_veneer(); ?>

                <!-- 4. Cười hở lợi -->
                <?php flora_render_pricing_card_gummy_smile(); ?>

                <!-- 5. Nha khoa tổng quát -->
                <?php flora_render_pricing_card_general(); ?>
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
                        <p style="font-size: 0.9rem; color: var(--clr-text-muted); margin: 0 0 20px 0; line-height: 1.6;">Những băn khoăn phổ biến của khách hàng khi tìm hiểu dịch vụ tại Nha khoa Flora.</p>
                    </div>
                    <div class="faq-list" style="margin-top: 0;">
                        <div class="faq-item active">
                            <button class="faq-question">Chi phí trên bảng giá đã bao gồm trọn gói chưa? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Có. Chi phí điều trị tại Nha khoa Flora được niêm yết trọn gói công khai, cam kết không phát sinh phụ phí chụp phim hay vật liệu phụ trong suốt quá trình bác sĩ thực hiện phác đồ điều trị.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">Nha khoa Flora hỗ trợ các hình thức thanh toán nào? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Flora hỗ trợ thanh toán linh hoạt qua tiền mặt, chuyển khoản, quẹt thẻ POS hoặc hỗ trợ trả góp 0% lãi suất liên kết trực tiếp với hơn 25 ngân hàng đối tác trên toàn quốc.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">Chính sách bảo hành tại Flora được cam kết như thế nào? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Tất cả dịch vụ phục hình răng sứ và Implant đều được cam kết bảo hành bằng văn bản và cấp thẻ bảo hành chính hãng từ nhà sản xuất. Bạn có thể dễ dàng tra cứu mã bảo hành online.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Registration Form -->
                <div class="cta-form reveal reveal-delay-2" style="background: #ffffff; border: 1px solid var(--clr-border); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-premium);">
                    <h3 style="color: var(--clr-navy); font-size: 1.4rem; margin-bottom: 8px; text-align: center;">Đăng Ký Khám & Tư Vấn Miễn Phí</h3>
                    <p style="font-size: 0.88rem; color: var(--clr-text-muted); text-align: center; margin-bottom: 24px;">Miễn phí chụp phim CT 3D & Chẩn đoán y khoa cùng Bác sĩ Cấp Cao</p>
                    
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
<option value="Nhận báo giá tổng hợp" selected>Nhận báo giá tổng hợp</option>
                                <option value="Trồng răng Implant">Trồng răng Implant Thụy Sĩ</option>
                                <option value="Niềng răng chỉnh nha">Niềng răng Invisalign / Mắc cài</option>
                                <option value="Điều trị cười hở lợi">Điều trị cười hở lợi</option>
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

    <!-- FOOTER -->

<?php
get_footer();
