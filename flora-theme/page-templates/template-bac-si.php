<?php
/**
 * Template Name: Trang Đội Ngũ Bác Sĩ
 */

get_header();
?>

<!-- ─── DANH SÁCH BÁC SĨ (DOCTORS SECTION) ─── -->
    <section class="section-padding" id="doctors" style="background: var(--clr-white);">
        <div class="container">
            <div class="section-header center reveal">
                <h2>Hội Đồng 5 Bác Sĩ Chuyên Khoa Mũi Nhọn</h2>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 60px; margin-top: 50px;">
                <!-- Doctor 1 -->
                <div class="expert-grid reveal">
                    <div class="expert-card" style="padding: 0; overflow: hidden; border: 1px solid var(--clr-border);">
                        <img src="<?php echo flora_asset('assets/homepage/bs_minh_portrait.webp'); ?>" alt="BS.CKI Nguyễn Đắc Minh" style="width: 100%; height: auto; display: block; object-fit: cover;" />
                        <div style="padding: 24px; text-align: center;">
                            <h3 style="font-family: var(--font-title); font-weight: 700; color: var(--clr-navy);">Bác sĩ Nguyễn Đắc Minh</h3>
                            <p style="color: var(--clr-secondary); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Chuyên khoa Cấy ghép Implant & Cười hở lợi</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 15px;">
                        <h4 style="font-family: var(--font-title); font-size: 1.25rem; color: var(--clr-navy);">Hồ sơ chuyên môn Bác sĩ Minh</h4>
                        <p style="color: var(--clr-text-muted); font-size: 0.95rem; line-height: 1.7;">Đã thực hiện cấy ghép thành công hơn 3.000 ca Implant và tạo hình thẩm mỹ hơn 950 ca cười hở lợi. Thành viên danh dự Hiệp hội Implant thế giới ICOI và ITI Thụy Sĩ.</p>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: var(--clr-text);">
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> 10+ năm kinh nghiệm cấy cắm không đau</li>
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Tốt nghiệp Răng Hàm Mặt, chứng chỉ RHM Trung Ương</li>
                        </ul>
                    </div>
                </div>

                <!-- Doctor 2 -->
                <div class="expert-grid reveal" style="direction: rtl;">
                    <div class="expert-card" style="padding: 0; overflow: hidden; border: 1px solid var(--clr-border); direction: ltr;">
                        <img src="https://placehold.co/500x600/0a1931/ffffff?text=%E1%BA%A2nh+B%C3%A1c+S%C4%A9+Ch%E1%BB%89nh+Nha+500x600" alt="Bác sĩ Chỉnh Nha" style="width: 100%; height: auto; display: block;" />
                        <div style="padding: 24px; text-align: center;">
                            <h3 style="font-family: var(--font-title); font-weight: 700; color: var(--clr-navy);">Bác sĩ Chuyên Khoa Chỉnh Nha</h3>
                            <p style="color: var(--clr-secondary); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Chỉnh nha Invisalign & Mắc cài</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 15px; direction: ltr;">
                        <h4 style="font-family: var(--font-title); font-size: 1.25rem; color: var(--clr-navy);">Chuyên Gia Niềng Răng Công Nghệ Số</h4>
                        <p style="color: var(--clr-text-muted); font-size: 0.95rem; line-height: 1.7;">Bác sĩ được đào tạo chuyên môn sâu về chỉnh khay niềng trong suốt Invisalign chính hãng Hoa Kỳ. Thực hiện thành công hàng ngàn ca khớp cắn lệch lạc phức tạp.</p>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: var(--clr-text);">
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Chứng nhận đào tạo ClinCheck Invisalign toàn cầu</li>
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Định vị chuẩn khớp cắn số hóa</li>
                        </ul>
                    </div>
                </div>

                <!-- Doctor 3 -->
                <div class="expert-grid reveal">
                    <div class="expert-card" style="padding: 0; overflow: hidden; border: 1px solid var(--clr-border);">
                        <img src="https://placehold.co/500x600/0033a3/ffffff?text=%E1%BA%A2nh+B%C3%A1c+S%C4%A9+R%C4%83ng+S%E1%BB%A9+500x600" alt="Bác sĩ Răng Sứ" style="width: 100%; height: auto; display: block;" />
                        <div style="padding: 24px; text-align: center;">
                            <h3 style="font-family: var(--font-title); font-weight: 700; color: var(--clr-navy);">Bác sĩ Phục Hình Răng Sứ</h3>
                            <p style="color: var(--clr-secondary); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Dán sứ Veneer & Phục hình thẩm mỹ</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 15px;">
                        <h4 style="font-family: var(--font-title); font-size: 1.25rem; color: var(--clr-navy);">Chuyên Gia Dán Sứ Veneer Bảo Tồn</h4>
                        <p style="color: var(--clr-text-muted); font-size: 0.95rem; line-height: 1.7;">Thiết kế nụ cười cá nhân hóa theo từng khuôn mặt bệnh nhân, chuyên sâu dán sứ E.max siêu mỏng không mài nhỏ răng gốc.</p>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: var(--clr-text);">
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Đào tạo Smile Design thẩm mỹ châu Âu</li>
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> 3.000+ ca dán sứ Veneer bảo tồn tủy răng</li>
                        </ul>
                    </div>
                </div>

                <!-- Doctor 4 & 5 (General Dentist & Oral Surgery) -->
                <div class="expert-grid reveal" style="direction: rtl;">
                    <div class="expert-card" style="padding: 0; overflow: hidden; border: 1px solid var(--clr-border); direction: ltr;">
                        <img src="https://placehold.co/500x600/0a1931/ffffff?text=%E1%BA%A2nh+B%C3%A1c+S%C4%A9+T%E1%BB%95ng+Qu%C3%A1t+500x600" alt="Bác sĩ Tổng Quát" style="width: 100%; height: auto; display: block;" />
                        <div style="padding: 24px; text-align: center;">
                            <h3 style="font-family: var(--font-title); font-weight: 700; color: var(--clr-navy);">Bác sĩ Điều Trị Tổng Quát</h3>
                            <p style="color: var(--clr-secondary); font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Nha khoa tổng quát & Nội nha điều trị tủy</p>
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; gap: 15px; direction: ltr;">
                        <h4 style="font-family: var(--font-title); font-size: 1.25rem; color: var(--clr-navy);">Chuyên Gia Nội Nha & Nhổ Răng Khôn</h4>
                        <p style="color: var(--clr-text-muted); font-size: 0.95rem; line-height: 1.7;">Thực hiện lấy tủy không đau, trám răng thẩm mỹ và tiểu phẫu nhổ răng khôn không sưng nề bằng sóng siêu âm Piezotome.</p>
                        <ul style="list-style: none; display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: var(--clr-text);">
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Máy đo chiều dài ống tủy định vị số hóa</li>
                            <li><i class="fa-solid fa-circle-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Chăm sóc sức khỏe răng miệng gia đình toàn diện</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CHỨNG NHẬN & CHỨNG CHỈ (CERTIFICATIONS SECTION) ─── -->
    <section class="section-padding" id="certifications" style="background-color: var(--clr-bg-light);">
        <div class="container">
            <div class="section-header center reveal">
                <h2>Chứng Nhận - Chứng Chỉ - Thành Tựu</h2>
            </div>
            
            <div class="cert-grid mobile-slider">
                <!-- Cert 1 -->
                <div class="cert-card reveal-scale">
                    <img src="https://placehold.co/800x600/0033a3/ffffff?text=Ch%E1%BB%A9ng+Ch%E1%BB%89+C%E1%BA%A5y+Gh%C3%A9p+Implant+800x600" alt="Cert 1" class="cert-placeholder" />
                    <h4 style="font-family: var(--font-title); font-weight: 700; margin-top: 15px; color: var(--clr-navy);">Chứng Chỉ Cấy Ghép Nha Khoa</h4>
                    <p style="font-size: 0.85rem; color: var(--clr-text-muted); margin-top: 5px;">Chứng chỉ chính quy do Bệnh viện RHM Trung ương cấp cho hội đồng bác sĩ.</p>
                </div>
                <!-- Cert 2 -->
                <div class="cert-card reveal-scale">
                    <img src="https://placehold.co/800x600/0493f1/ffffff?text=Th%C3%A0nh+Vi%C3%AAn+Hi%E1%BB%87p+H%E1%BB%99i+ITI+800x600" alt="Cert 2" class="cert-placeholder" />
                    <h4 style="font-family: var(--font-title); font-weight: 700; margin-top: 15px; color: var(--clr-navy);">Chứng Nhận Thành Viên ITI</h4>
                    <p style="font-size: 0.85rem; color: var(--clr-text-muted); margin-top: 5px;">Chứng nhận thành viên Hiệp hội Implant quốc tế ITI Thụy Sĩ của Bác sĩ Minh.</p>
                </div>
                <!-- Cert 3 -->
                <div class="cert-card reveal-scale">
                    <img src="https://placehold.co/800x600/0a1931/ffffff?text=Ch%E1%BB%A9ng+Nh%E1%BB%B9+Ch%E1%BB%89nh+Nha+Invisalign+800x600" alt="Cert 3" class="cert-placeholder" />
                    <h4 style="font-family: var(--font-title); font-weight: 700; margin-top: 15px; color: var(--clr-navy);">Chứng Nhận Invisalign USA</h4>
                    <p style="font-size: 0.85rem; color: var(--clr-text-muted); margin-top: 5px;">Chứng nhận đào tạo phác đồ di chuyển răng ClinCheck do Invisalign Mỹ cấp.</p>
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
                        <p style="font-size: 0.9rem; color: var(--clr-text-muted); margin: 0 0 20px 0; line-height: 1.6;">Những băn khoăn phổ biến của khách hàng khi tìm hiểu dịch vụ tại Nha khoa Flora.</p>
                    </div>
                    <div class="faq-list" style="margin-top: 0;">
                        <div class="faq-item active">
                            <button class="faq-question">Khám răng với bác sĩ tại Flora có mất phí thăm khám không? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Hoàn toàn miễn phí. Khách hàng đặt lịch hẹn trước sẽ được miễn phí chụp phim CT Cone Beam 3D, scan răng 3D và được bác sĩ RHM trực tiếp tư vấn phác đồ điều trị mà không tốn phí dịch vụ.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">Đội ngũ bác sĩ tại Nha khoa Flora có chứng chỉ gì? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    100% bác sĩ tại Flora tốt nghiệp khoa Răng Hàm Mặt tại các trường Đại học Y Dược uy tín, có đầy đủ chứng chỉ hành nghề và chứng chỉ cấy ghép Implant chuyên sâu được Bộ Y Tế cấp phép.
                                </div>
                            </div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question">Làm sao để đặt lịch hẹn chính xác với bác sĩ mong muốn? <i class="fa-solid fa-chevron-down"></i></button>
                            <div class="faq-answer">
                                <div class="faq-answer-content">
                                    Bạn chỉ cần điền thông tin vào form đăng ký hoặc gọi hotline. Đội ngũ tư vấn sẽ liên hệ để xác nhận khung giờ trống của bác sĩ và đặt lịch hẹn ưu tiên cho bạn mà không phải chờ đợi.
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
<option value="Trồng răng Implant">Trồng răng Implant Thụy Sĩ</option>
                                <option value="Niềng răng chỉnh nha">Niềng răng Invisalign / Mắc cài</option>
                                <option value="Điều trị cười hở lợi">Điều trị cười hở lợi</option>
                                <option value="Dán sứ thẩm mỹ Veneer">Dán sứ Veneer E.max</option>
                                <option value="Bọc răng sứ">Bọc răng sứ thẩm mỹ</option>
                                <option value="Nha khoa tổng quát">Khám răng tổng quát / Lấy cao răng</option>
                                <option value="Đặt lịch tư vấn Bác sĩ" selected>Đặt lịch tư vấn Bác sĩ RHM</option>
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
        <!-- ─── FOOTER ─── -->

<?php
get_footer();
