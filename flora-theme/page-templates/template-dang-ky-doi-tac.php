<?php
/**
 * Template Name: Đăng Ký Đối Tác / KOL
 * Description: Trang giới thiệu chương trình tiếp thị liên kết và form đăng ký đối tác gửi duyệt
 */

get_header();
?>

<style>
.aff-reg-hero {
    background: linear-gradient(135deg, rgba(0, 24, 82, 0.94) 0%, rgba(0, 51, 163, 0.88) 60%, rgba(4, 147, 241, 0.82) 100%), url('<?php echo flora_asset('assets/homepage/bac_si_minh_doi_ngu.webp'); ?>') center/cover no-repeat;
    padding: 76px 0 68px;
    color: #ffffff !important;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.aff-reg-hero h1,
.aff-reg-hero h2,
.aff-reg-hero h3,
.aff-reg-hero p,
.aff-reg-hero span,
.aff-reg-hero .aff-reg-title,
.aff-reg-hero .aff-reg-subtitle {
    color: #ffffff !important;
}
.aff-reg-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 32px;
    background: #f8fafc;
    border-radius: 32px 32px 0 0;
}
.aff-reg-title {
    font-family: 'Montserrat', var(--font-title, sans-serif);
    font-size: 2.3rem;
    font-weight: 800;
    color: #ffffff !important;
    margin: 0 0 14px;
    letter-spacing: -0.5px;
    text-shadow: 0 2px 14px rgba(0, 15, 60, 0.7);
}
.aff-reg-subtitle {
    font-size: 1.05rem;
    color: #f1f5f9 !important;
    max-width: 780px;
    margin: 0 auto;
    line-height: 1.6;
    text-shadow: 0 1px 8px rgba(0, 15, 60, 0.6);
}
.aff-reg-container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 20px 20px 80px;
}
.aff-perks-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 50px;
}
.aff-perk-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--clr-border, #e2e8f0);
    padding: 26px 22px;
    box-shadow: 0 4px 20px rgba(0, 51, 163, 0.04);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.aff-perk-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 51, 163, 0.08);
}
.aff-perk-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    color: #0033a3;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    margin-bottom: 16px;
}
.aff-perk-card h3 {
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--clr-navy, #0033a3);
    margin: 0 0 8px;
}
.aff-perk-card p {
    font-size: 0.88rem;
    color: #64748b;
    margin: 0;
    line-height: 1.6;
}
.aff-form-wrapper {
    display: grid;
    grid-template-columns: 1fr 1.25fr;
    gap: 40px;
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid var(--clr-border, #e2e8f0);
    box-shadow: 0 10px 40px rgba(0, 51, 163, 0.05);
    overflow: hidden;
}
@media (max-width: 991px) {
    .aff-form-wrapper {
        grid-template-columns: 1fr;
    }
}
.aff-form-sidebar {
    background: linear-gradient(145deg, #001f66 0%, #0033a3 100%);
    color: #ffffff !important;
    padding: 44px 36px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.aff-form-sidebar h3,
.aff-form-sidebar h4,
.aff-form-sidebar p,
.aff-form-sidebar span {
    color: #ffffff !important;
}
.aff-steps-list {
    list-style: none;
    padding: 0;
    margin: 28px 0;
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.aff-step-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}
.aff-step-num {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
    color: #ffffff !important;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.aff-step-text h4 {
    font-size: 1rem;
    font-weight: 700;
    margin: 0 0 4px;
    color: #ffffff !important;
}
.aff-step-text p {
    font-size: 0.84rem;
    color: rgba(255, 255, 255, 0.9) !important;
    margin: 0;
    line-height: 1.5;
}
.aff-form-content {
    padding: 44px;
}
@media (max-width: 600px) {
    .aff-form-content {
        padding: 28px 20px;
    }
    .aff-reg-title {
        font-size: 1.7rem;
    }
}
.aff-form-title {
    font-family: 'Montserrat', var(--font-title, sans-serif);
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--clr-navy, #0033a3);
    margin: 0 0 8px;
}
.aff-form-sub {
    font-size: 0.88rem;
    color: #64748b;
    margin-bottom: 28px;
}
.aff-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}
@media (max-width: 600px) {
    .aff-form-grid {
        grid-template-columns: 1fr;
    }
}
.aff-field-full {
    grid-column: 1 / -1;
}
.aff-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 6px;
}
.aff-input, .aff-select, .aff-textarea {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    width: 100%;
    padding: 11px 14px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font-size: 0.92rem;
    background: #ffffff;
    color: #0f172a;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}
.aff-input:focus, .aff-select:focus, .aff-textarea:focus {
    outline: none;
    border-color: #0033a3;
    box-shadow: 0 0 0 3px rgba(0, 51, 163, 0.1);
}
.btn-aff-submit {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    width: 100%;
    background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
    color: #ffffff;
    border: none;
    border-radius: 12px;
    padding: 14px 20px;
    font-size: 1.05rem;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.25s ease;
    box-shadow: 0 4px 15px rgba(0, 51, 163, 0.2);
    margin-top: 10px;
}
.btn-aff-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 51, 163, 0.3);
}
.btn-aff-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
    transform: none;
}
.aff-success-box {
    display: none;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 16px;
    padding: 32px 24px;
    text-align: center;
}
.aff-success-icon {
    width: 64px;
    height: 64px;
    background: #dcfce7;
    color: #16a34a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 16px;
}
</style>

<!-- HERO BANNER -->
<section class="aff-reg-hero">
    <div class="container" style="position: relative; z-index: 2; max-width: 900px; margin: 0 auto; padding: 0 20px;">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.18); backdrop-filter: blur(8px); padding: 6px 18px; border-radius: 9999px; margin-bottom: 18px; border: 1px solid rgba(255,255,255,0.32); font-size: 0.84rem; font-weight: 800; letter-spacing: 0.5px; color: #ffffff !important;">
            <i class="fa-solid fa-crown" style="color: #fbbf24;"></i> MẠNG LƯỚI ĐỐI TÁC TIẾP THỊ NHA KHOA FLORA
        </div>
        <h1 class="aff-reg-title" style="color: #ffffff !important; text-shadow: 0 2px 14px rgba(0, 15, 60, 0.7);">Hợp Tác Đối Tác & Đại Sứ Nụ Cười</h1>
        <p class="aff-reg-subtitle" style="color: #ffffff !important; opacity: 0.95; text-shadow: 0 1px 8px rgba(0, 15, 60, 0.6);">
            Đồng hành cùng Nha Khoa Flora lan tỏa giải pháp chăm sóc nụ cười tiêu chuẩn Thụy Sĩ. Nhận mức hoa hồng hấp dẫn, quản lý đơn hàng và hoa hồng minh bạch qua Cổng Thông Tin Đối Tác.
        </p>
        <div style="margin-top: 26px; display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
            <a href="#dang-ky-form" style="background: #ffffff; color: #0033a3 !important; padding: 12px 28px; border-radius: 9999px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); font-size: 0.95rem;">
                <i class="fa-solid fa-file-signature" style="color: #0033a3;"></i> Đăng Ký Hồ Sơ Trực Tuyến
            </a>
            <a href="<?php echo esc_url(home_url('/doi-tac/')); ?>" style="background: rgba(255,255,255,0.16); color: #ffffff !important; border: 1.5px solid rgba(255,255,255,0.4); padding: 12px 24px; border-radius: 9999px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.95rem;">
                <i class="fa-solid fa-arrow-right-to-bracket" style="color: #ffffff;"></i> Đã Có Tài Khoản? Đăng Nhập
            </a>
        </div>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="aff-reg-container">
    <!-- 4 PERKS CARDS -->
    <div class="aff-perks-grid">
        <div class="aff-perk-card">
            <div class="aff-perk-icon"><i class="fa-solid fa-coins"></i></div>
            <h3>Hoa Hồng Hấp Dẫn 10%</h3>
            <p>Thu nhập không giới hạn trên mỗi khách hàng đặt mua gói khám hoặc hoàn tất phác đồ điều trị từ liên kết của bạn.</p>
        </div>

        <div class="aff-perk-card">
            <div class="aff-perk-icon"><i class="fa-solid fa-chart-line"></i></div>
            <h3>Cổng Quản Lý Đối Tác</h3>
            <p>Trang Dashboard cá nhân riêng biệt theo dõi từng lượt click, danh sách đơn hàng đối soát và hoa hồng tức thì.</p>
        </div>

        <div class="aff-perk-card">
            <div class="aff-perk-icon"><i class="fa-solid fa-building-columns"></i></div>
            <h3>Chủ Động Đổi STK Ngân Hàng</h3>
            <p>Tự do quản lý và cập nhật tài khoản ngân hàng thụ hưởng trực tiếp trong Cổng Đối Tác mà không cần thủ tục rườm rà.</p>
        </div>

        <div class="aff-perk-card">
            <div class="aff-perk-icon"><i class="fa-solid fa-shield-halved"></i></div>
            <h3>Uy Tín Y Khoa Thụy Sĩ</h3>
            <p>Tự hào đồng hành cùng phòng khám nha khoa có Giấy phép 09723/HCM-GPHĐ, bảo vệ tối đa uy tín cá nhân của bạn.</p>
        </div>
    </div>

    <!-- REGISTRATION SPLIT -->
    <div class="aff-form-wrapper">
        <!-- SIDEBAR GUIDELINES -->
        <div class="aff-form-sidebar">
            <div>
                <div style="margin-bottom: 20px; border-radius: 14px; overflow: hidden; box-shadow: 0 6px 20px rgba(0,0,0,0.25); border: 1.5px solid rgba(255,255,255,0.2);">
                    <img src="<?php echo flora_asset('assets/homepage/bac_si_minh_doi_ngu.webp'); ?>" alt="Đội ngũ Bác sĩ Nha Khoa Flora" style="width: 100%; height: auto; display: block; object-fit: cover;">
                    <div style="background: rgba(0, 24, 82, 0.9); padding: 8px 12px; font-size: 0.78rem; color: #cbd5e1; text-align: center;">
                        <i class="fa-solid fa-user-doctor" style="color: #38bdf8;"></i> Đội ngũ Bác sĩ CKI Nha Khoa Flora
                    </div>
                </div>

                <span style="display: inline-block; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    QUY TRÌNH 3 BƯỚC THAM GIA
                </span>
                <h3 style="font-size: 1.3rem; font-weight: 800; margin: 0 0 4px; color: #ffffff;">Cách Thức Hợp Tác Cùng Flora</h3>

                <ul class="aff-steps-list">
                    <li class="aff-step-item">
                        <div class="aff-step-num">1</div>
                        <div class="aff-step-text">
                            <h4>Gửi Hồ Sơ Đăng Ký</h4>
                            <p>Điền đầy đủ thông tin liên hệ, kênh truyền thông và số tài khoản ngân hàng nhận hoa hồng.</p>
                        </div>
                    </li>
                    <li class="aff-step-item">
                        <div class="aff-step-num">2</div>
                        <div class="aff-step-text">
                            <h4>Flora Xét Duyệt Trong 24h</h4>
                            <p>Ban Quản Trị phê duyệt hồ sơ trong 24h, tự động cấp Mã REF, Link giới thiệu và gửi email thông báo mật khẩu kích hoạt.</p>
                        </div>
                    </li>
                    <li class="aff-step-item">
                        <div class="aff-step-num">3</div>
                        <div class="aff-step-text">
                            <h4>Chia Sẻ & Rút Hoa Hồng 24/7</h4>
                            <p>Đăng nhập Cổng Đối Tác (/doi-tac/) để theo dõi lượt khách, đối soát đơn hàng và nhận chuyển khoản linh hoạt.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div style="background: rgba(255,255,255,0.1); border-radius: 12px; padding: 18px; border: 1px solid rgba(255,255,255,0.15); margin-top: 20px;">
                <div style="font-size: 0.82rem; color: rgba(255,255,255,0.85); margin-bottom: 6px;">Bạn đã có tài khoản đối tác?</div>
                <a href="<?php echo esc_url(home_url('/doi-tac/')); ?>" style="color: #38bdf8; font-weight: 800; text-decoration: none; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-door-open"></i> Đăng nhập Cổng Thông Tin Đối Tác &rarr;
                </a>
            </div>
        </div>

        <!-- FORM CONTAINER -->
        <div class="aff-form-content">
            <!-- Form State -->
            <div id="affFormState">
                <a id="dang-ky-form"></a>
                <h2 class="aff-form-title">Đăng Ký Trở Thành Đối Tác Flora</h2>
                <p class="aff-form-sub">Vui lòng điền chính xác thông tin để Ban Quản Trị liên hệ xét duyệt và gửi thông tin kích hoạt.</p>

                <form id="formPartnerRegister" onsubmit="handlePartnerRegister(event)">
                    <div class="aff-form-grid">
                        <!-- Họ tên -->
                        <div class="aff-field-full">
                            <label class="aff-label">Họ và Tên Đối Tác / KOL (*):</label>
                            <input type="text" name="name" class="aff-input" required placeholder="Vd: Nguyễn Thu Trang (Trang Review)">
                        </div>

                        <!-- Số điện thoại -->
                        <div>
                            <label class="aff-label">Số Điện Thoại (*):</label>
                            <input type="tel" name="phone" class="aff-input" required placeholder="Vd: 0912345678">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="aff-label">Email Nhận Kích Hoạt (*):</label>
                            <input type="email" name="email" class="aff-input" required placeholder="Vd: trangnguyen@gmail.com">
                        </div>

                        <!-- Bảo mật & Tài khoản đăng nhập -->
                        <div class="aff-field-full">
                            <div style="background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 12px; padding: 14px 18px; display: flex; align-items: flex-start; gap: 12px;">
                                <i class="fa-solid fa-shield-check" style="color: #16a34a; font-size: 1.3rem; margin-top: 2px;"></i>
                                <div style="font-size: 0.88rem; color: #166534; line-height: 1.5;">
                                    <strong>Bảo mật thông tin & Kích hoạt tài khoản:</strong><br>
                                    • Tên đăng nhập Cổng Đối Tác: <strong>Số điện thoại của bạn</strong><br>
                                    • Mật khẩu đăng nhập và thông tin kích hoạt sẽ được bảo mật gửi trực tiếp đến <strong>Email</strong> của bạn ngay khi hồ sơ được Ban Quản Trị phê duyệt trong 24 giờ.
                                </div>
                            </div>
                        </div>

                        <!-- Kênh truyền thông -->
                        <div class="aff-field-full">
                            <label class="aff-label">Kênh Truyền Thông / Mạng Xã Hội (Facebook / TikTok / YouTube / Web):</label>
                            <input type="text" name="channel_url" class="aff-input" placeholder="Vd: tiktok.com/@trangreview hoặc facebook.com/trang.nguyen">
                        </div>

                        <!-- Ngân hàng -->
                        <div>
                            <label class="aff-label">Tên Ngân Hàng Thụ Hưởng:</label>
                            <input type="text" name="bank_name" class="aff-input" list="bankList" placeholder="Vd: ACB, Vietcombank, MB Bank...">
                            <datalist id="bankList">
                                <option value="Ngân hàng ACB">
                                <option value="Ngân hàng Vietcombank">
                                <option value="Ngân hàng MB Bank">
                                <option value="Ngân hàng Techcombank">
                                <option value="Ngân hàng BIDV">
                                <option value="Ngân hàng Vietinbank">
                                <option value="Ngân hàng VPBank">
                                <option value="Ngân hàng TPBank">
                            </datalist>
                        </div>

                        <!-- Số tài khoản -->
                        <div>
                            <label class="aff-label">Số Tài Khoản (STK) Ngân Hàng:</label>
                            <input type="text" name="bank_account" class="aff-input" placeholder="Nhập số tài khoản nhận hoa hồng">
                        </div>

                        <!-- Chủ tài khoản -->
                        <div class="aff-field-full">
                            <label class="aff-label">Tên Chủ Tài Khoản (In hoa không dấu):</label>
                            <input type="text" name="bank_owner" class="aff-input" placeholder="Vd: NGUYEN THU TRANG" style="text-transform: uppercase;">
                        </div>

                        <!-- Ghi chú -->
                        <div class="aff-field-full">
                            <label class="aff-label">Giới Thiệu Hoặc Đề Xuất Hợp Tác (Không bắt buộc):</label>
                            <textarea name="notes" rows="3" class="aff-textarea" placeholder="Chia sẻ định hướng hoặc tệp người theo dõi của bạn..."></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="aff-field-full">
                            <button type="submit" id="btnSubmitPartner" class="btn-aff-submit">
                                <i class="fa-solid fa-paper-plane"></i> GỬI HỒ SƠ ĐĂNG KÝ XÉT DUYỆT
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Success State -->
            <div id="affSuccessState" class="aff-success-box">
                <div class="aff-success-icon"><i class="fa-solid fa-check"></i></div>
                <h3 style="font-size: 1.35rem; font-weight: 800; color: #16a34a; margin: 0 0 10px;">Hồ SƠ ĐÃ GỬI THÀNH CÔNG!</h3>
                <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                    Cảm ơn bạn đã đăng ký tham gia Mạng Lưới Đối Tác Nha Khoa Flora. Email xác nhận tiếp nhận hồ sơ đã được gửi đến hộp thư của bạn. Ban Quản Trị sẽ thẩm duyệt và kích hoạt hồ sơ trong vòng <strong>24 giờ làm việc</strong>.
                </p>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-bottom: 24px; font-size: 0.88rem; color: #15803d; text-align: left; line-height: 1.6;">
                    <i class="fa-solid fa-circle-check"></i> <strong>Quy trình kích hoạt tài khoản:</strong><br>
                    • Tên đăng nhập Cổng Đối Tác: <strong>Số điện thoại bạn đã đăng ký</strong><br>
                    • Mật khẩu đăng nhập khởi tạo và hướng dẫn truy cập sẽ được gửi bảo mật về email của bạn ngay khi hồ sơ được phê duyệt.
                </div>
                <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap;">
                    <a href="<?php echo esc_url(home_url('/doi-tac/')); ?>" class="btn-aff-submit" style="width: auto; padding: 12px 28px; text-decoration: none; font-size: 0.95rem;">
                        <i class="fa-solid fa-door-open"></i> Đến Cổng Đăng Nhập Đối Tác
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function handlePartnerRegister(e) {
    e.preventDefault();
    const form = document.getElementById('formPartnerRegister');
    const btn = document.getElementById('btnSubmitPartner');
    const origText = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi hồ sơ...';

    const formData = new FormData(form);
    formData.append('action', 'flora_ajax_partner_register');

    try {
        const res = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('affFormState').style.display = 'none';
            document.getElementById('affSuccessState').style.display = 'block';
            window.scrollTo({ top: document.getElementById('affSuccessState').offsetTop - 100, behavior: 'smooth' });
        } else {
            alert('Thông báo: ' + (data.data ? data.data.message : 'Có lỗi xảy ra, vui lòng thử lại!'));
            btn.disabled = false;
            btn.innerHTML = origText;
        }
    } catch (err) {
        alert('Lỗi kết nối máy chủ! Vui lòng thử lại sau.');
        btn.disabled = false;
        btn.innerHTML = origText;
    }
}
</script>

<?php
get_footer();
