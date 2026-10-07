<?php
/**
 * Template Name: Trang Liên Hệ
 */

get_header();
?>

<!-- ─── THÔNG TIN LIÊN HỆ & FORM ─── -->
    <section class="section-padding contact-page-section">
        <div class="container">
            <div class="section-header center reveal">
                <h2>Kết Nối Với Chúng Tôi</h2>
                <p style="max-width: 600px; margin: 10px auto 0; font-size: 0.95rem; color: var(--clr-text-muted);">Hãy chọn phương thức liên hệ thuận tiện nhất để nhận hỗ trợ nhanh chóng từ Flora.</p>
            </div>

            <div class="expert-wrapper" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-top: 40px; align-items: start;">
                <!-- Left: Contact Details & Map -->
                <div class="reveal">
                    <div style="background: white; border: 1px solid var(--clr-border); border-radius: 12px; padding: 30px; box-shadow: var(--shadow); margin-bottom: 30px;">
                        <h3 style="color: var(--clr-navy); margin-bottom: 20px; font-size: 1.25rem;"><i class="fa-solid fa-hospital" style="color: var(--clr-secondary); margin-right: 8px;"></i> Hệ Thống Nha Khoa Flora</h3>
                        
                        <div style="display: flex; gap: 15px; margin-bottom: 18px;">
                            <div style="font-size: 1.2rem; color: var(--clr-primary);"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <h4 style="font-weight: bold; margin-bottom: 5px;">Địa chỉ phòng khám:</h4>
                                <p style="font-size: 0.9rem; color: var(--clr-text-muted);">326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh</p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 15px; margin-bottom: 18px;">
                            <div style="font-size: 1.2rem; color: var(--clr-primary);"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <h4 style="font-weight: bold; margin-bottom: 5px;">Hotline tư vấn (24/7):</h4>
                                <p style="font-size: 0.9rem; color: var(--clr-text-muted);">028 7305 8999</p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 15px; margin-bottom: 18px;">
                            <div style="font-size: 1.2rem; color: var(--clr-primary);"><i class="fa-solid fa-clock"></i></div>
                            <div>
                                <h4 style="font-weight: bold; margin-bottom: 5px;">Giờ mở cửa hoạt động:</h4>
                                <p style="font-size: 0.9rem; color: var(--clr-text-muted);">8h30 - 18h30 (T2 - CN)</p>
                            </div>
                        </div>

                        <div style="display: flex; gap: 15px;">
                            <div style="font-size: 1.2rem; color: var(--clr-primary);"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <h4 style="font-weight: bold; margin-bottom: 5px;">Email tiếp nhận thông tin:</h4>
                                <p style="font-size: 0.9rem; color: var(--clr-text-muted);">support@floraclinic.vn</p>
                            </div>
                        </div>
                    </div>

                    <!-- Map Placeholder -->
                    <div style="background: white; border: 1px solid var(--clr-border); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); height: 300px;">
                        <iframe src="https://maps.google.com/maps?q=326+Nguy%E1%BB%85n+Th%E1%BB%8B+Minh+Khai,+Ph%C6%B0%E1%BB%9Dng+B%C3%A0n+C%E1%BB%9D,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh&amp;t=&amp;z=16&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <!-- Right: Form Booking -->
                <div class="reveal reveal-delay-1" id="dang-ky" style="background: white; padding: 40px; border-radius: 12px; border: 1px solid var(--clr-border); box-shadow: var(--shadow);">
                    <h3 style="color: var(--clr-navy); margin-bottom: 10px; font-size: 1.25rem;"><i class="fa-solid fa-calendar-check" style="color: var(--clr-secondary); margin-right: 8px;"></i> Đặt Lịch Hẹn Với Bác Sĩ</h3>
                    <p style="font-size: 0.88rem; color: var(--clr-text-muted); margin-bottom: 20px;">Vui lòng điền thông tin để đội ngũ CSKH của chúng tôi liên hệ hỗ trợ bạn chọn giờ hẹn khám thuận lợi nhất.</p>
                    
                    <form id="floraRegistrationForm" class="modal-form" style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
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
                                <option value="Khám tổng quát" selected>Khám và tư vấn răng miệng</option>
                                <option value="Trồng răng Implant">Cấy ghép Implant Thụy Sĩ</option>
                                <option value="Niềng răng">Chỉnh nha Niềng răng</option>
                                <option value="Thẩm mỹ răng sứ">Thẩm mỹ răng sứ / Veneer</option>
                                <option value="Cười hở lợi">Điều trị cười hở lợi</option>
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
                        <button type="submit" class="btn btn-primary" style="padding: 14px; margin-top: 10px; font-size: 1rem; font-weight: bold; width: 100%;">Gửi Yêu Cầu Đặt Lịch</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->

<?php
get_footer();
