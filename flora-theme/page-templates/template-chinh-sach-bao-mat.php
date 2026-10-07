<?php
/**
 * Template Name: Chính Sách Bảo Mật
 * Description: Chính sách bảo mật thông tin khách hàng và dữ liệu y tế tại Nha Khoa Flora tuân thủ Nghị định 13/2023/NĐ-CP
 */

get_header();
?>

<style>
.policy-page-header {
    background: linear-gradient(135deg, #001f66 0%, #0033a3 50%, #0493f1 100%);
    padding: 70px 0 50px;
    color: #ffffff;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.policy-page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 30px;
    background: #f8fafc;
    border-radius: 28px 28px 0 0;
}
.policy-breadcrumb {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.75);
    margin-bottom: 12px;
}
.policy-breadcrumb a {
    color: #ffffff;
    text-decoration: underline;
}
.policy-title {
    font-family: 'Montserrat', var(--font-title, sans-serif);
    font-size: 2.2rem;
    font-weight: 800;
    margin: 0 0 12px;
    letter-spacing: -0.5px;
}
.policy-subtitle {
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.9);
    max-width: 760px;
    margin: 0 auto;
    line-height: 1.6;
}
.policy-meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 6px 16px;
    border-radius: 9999px;
    font-size: 0.82rem;
    font-weight: 600;
    margin-top: 18px;
}
.policy-container {
    max-width: 1140px;
    margin: 0 auto;
    padding: 30px 20px 80px;
}
.policy-grid {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    align-items: start;
}
@media (max-width: 991px) {
    .policy-grid {
        grid-template-columns: 1fr;
    }
}
.policy-sidebar {
    position: sticky;
    top: 90px;
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--clr-border, #e2e8f0);
    padding: 24px;
    box-shadow: 0 4px 20px rgba(0, 51, 163, 0.04);
}
.policy-sidebar-title {
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--clr-navy, #0033a3);
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.policy-toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.policy-toc-link {
    display: block;
    font-size: 0.88rem;
    color: #475569;
    text-decoration: none;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}
.policy-toc-link:hover, .policy-toc-link.active {
    background: #eff6ff;
    color: #0033a3;
    font-weight: 700;
    border-left-color: #0493f1;
}
.policy-content-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid var(--clr-border, #e2e8f0);
    padding: 40px 48px;
    box-shadow: 0 4px 25px rgba(0, 51, 163, 0.03);
    line-height: 1.75;
    color: #334155;
}
@media (max-width: 768px) {
    .policy-content-card {
        padding: 28px 20px;
    }
    .policy-title {
        font-size: 1.7rem;
    }
}
.policy-section {
    margin-bottom: 40px;
    scroll-margin-top: 100px;
}
.policy-section h2 {
    font-family: 'Montserrat', var(--font-title, sans-serif);
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--clr-navy, #0033a3);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 12px;
}
.policy-section h3 {
    font-size: 1.12rem;
    font-weight: 700;
    color: #0f172a;
    margin: 20px 0 10px;
}
.policy-section p {
    margin-bottom: 14px;
}
.policy-section ul, .policy-section ol {
    padding-left: 24px;
    margin-bottom: 16px;
}
.policy-section li {
    margin-bottom: 8px;
}
.policy-alert-box {
    background: #eff6ff;
    border-left: 4px solid #0033a3;
    padding: 16px 20px;
    border-radius: 0 12px 12px 0;
    margin: 20px 0;
    font-size: 0.92rem;
    color: #1e3a8a;
}
.policy-cert-box {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 16px;
    margin: 24px 0;
}
.policy-cert-item {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.policy-cert-icon {
    width: 44px;
    height: 44px;
    background: #dbeafe;
    color: #0033a3;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.contact-highlight-card {
    background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 16px;
    padding: 24px;
    margin-top: 30px;
}
</style>

<!-- HERO HEADER -->
<div class="policy-page-header">
    <div class="container" style="position: relative; z-index: 2;">
        <div class="policy-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a> &nbsp;/&nbsp; <span>Chính Sách Bảo Mật</span>
        </div>
        <h1 class="policy-title">Chính Sách Bảo Mật Thông Tin Khách Hàng</h1>
        <p class="policy-subtitle">
            Flora Dental Clinic cam kết bảo vệ toàn vẹn dữ liệu cá nhân và hồ sơ bệnh án theo tiêu chuẩn Y tế Thụy Sĩ, tuân thủ nghiêm ngặt Nghị định 13/2023/NĐ-CP và Luật Khám bệnh, chữa bệnh 2023.
        </p>
        <div class="policy-meta-badge">
            <i class="fa-solid fa-shield-halved" style="color: #38bdf8;"></i> Cập nhật mới nhất: Tháng 10/2026 | Phiên bản 3.2
        </div>
    </div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div class="policy-container">
    <div class="policy-grid">
        <!-- SIDEBAR TOC -->
        <aside class="policy-sidebar">
            <div class="policy-sidebar-title">
                <i class="fa-solid fa-list-ul"></i> Mục Lục Chính Sách
            </div>
            <ul class="policy-toc-list">
                <li><a href="#can-cu-phap-ly" class="policy-toc-link active">1. Căn Cứ Pháp Lý & Cam Kết</a></li>
                <li><a href="#muc-dich-thu-thap" class="policy-toc-link">2. Mục Đích Thu Thập Dữ Liệu</a></li>
                <li><a href="#loai-du-lieu" class="policy-toc-link">3. Danh Mục Dữ Liệu Thu Thập</a></li>
                <li><a href="#thoi-gian-luu-tru" class="policy-toc-link">4. Thời Gian & Phạm Vi Lưu Trữ</a></li>
                <li><a href="#bao-mat-y-te" class="policy-toc-link">5. Bảo Mật Hồ Sơ Bệnh Án</a></li>
                <li><a href="#chia-se-du-lieu" class="policy-toc-link">6. Nguyên Tắc Không Chia Sẻ Dữ Liệu</a></li>
                <li><a href="#quyen-khach-hang" class="policy-toc-link">7. Quyền Của Khách Hàng</a></li>
                <li><a href="#don-vi-chiu-trach-nhiem" class="policy-toc-link">8. Đơn Vị Xử Lý Dữ Liệu & Liên Hệ</a></li>
            </ul>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 8px;">Tổng đài hỗ trợ bảo mật:</div>
                <a href="tel:02873058999" style="font-weight: 800; color: #0033a3; font-size: 1.1rem; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-phone" style="color: #0493f1;"></i> 028 7305 8999
                </a>
            </div>
        </aside>

        <!-- MAIN ARTICLE -->
        <article class="policy-content-card">
            <!-- SECTION 1 -->
            <section id="can-cu-phap-ly" class="policy-section">
                <h2><span>1.</span> Căn Cứ Pháp Lý & Nguyên Tắc Bảo Mật</h2>
                <p>
                    Chào mừng Quý khách hàng đến với <strong>Nha Khoa Flora</strong> (Phòng khám Răng Hàm Mặt thuộc Công ty Cổ phần Flora Dental Care). Chúng tôi hiểu rằng quyền riêng tư và bí mật y tế là tài sản vô giá của người bệnh.
                </p>
                <p>
                    Chính sách bảo mật này được xây dựng nhằm minh bạch hóa cách thức chúng tôi thu thập, xử lý, lưu trữ và bảo vệ dữ liệu cá nhân cũng như hồ sơ bệnh án của Quý khách khi sử dụng dịch vụ tại phòng khám và trên hệ thống website <code>nhakhoaflora.com</code>.
                </p>
                <div class="policy-alert-box">
                    <strong><i class="fa-solid fa-scale-balanced"></i> Căn cứ pháp lý cốt lõi:</strong>
                    <ul style="margin: 8px 0 0 16px; padding: 0;">
                        <li><strong>Nghị định 13/2023/NĐ-CP</strong> ngày 17/04/2023 của Chính phủ về Bảo vệ dữ liệu cá nhân.</li>
                        <li><strong>Luật Khám bệnh, chữa bệnh số 15/2023/QH15</strong> có hiệu lực từ ngày 01/01/2024 về quyền của người bệnh và bảo mật hồ sơ bệnh án.</li>
                        <li><strong>Luật An ninh mạng số 24/2018/QH14</strong> và các văn bản hướng dẫn thi hành hiện hành.</li>
                        <li><strong>Giấy phép hoạt động khám bệnh, chữa bệnh số:</strong> 09723/HCM-GPHĐ do Sở Y tế TP.HCM cấp ngày 27/07/2026.</li>
                    </ul>
                </div>
            </section>

            <!-- SECTION 2 -->
            <section id="muc-dich-thu-thap" class="policy-section">
                <h2><span>2.</span> Mục Đích Thu Thập Dữ Liệu</h2>
                <p>Nha Khoa Flora chỉ thu thập thông tin của khách hàng trong phạm vi cần thiết cho các mục đích chính đáng sau:</p>
                <ul>
                    <li><strong>Khám chữa bệnh & xây dựng phác đồ điều trị:</strong> Lập hồ sơ bệnh án điện tử, lưu trữ phim chụp X-quang CT Cone Beam 3D, dấu răng kỹ thuật số, lịch sử tiền sử bệnh lý và dị ứng để bác sĩ chẩn đoán chính xác và điều trị an toàn tuyệt đối.</li>
                    <li><strong>Quản lý lịch hẹn & chăm sóc khách hàng:</strong> Nhắc lịch hẹn khám, thông báo lịch tái khám định kỳ, hướng dẫn chăm sóc sau phẫu thuật cấy ghép Implant, niềng răng, bọc sứ.</li>
                    <li><strong>Kích hoạt & đối soát bảo hành chính hãng:</strong> Quản lý hệ thống Thẻ bảo hành điện tử (Flora E-Warranty) cho các dòng trụ Implant Thụy Sĩ, phôi sứ Cercon, E.max, khay niềng Invisalign.</li>
                    <li><strong>Xử lý giao dịch & đối soát hóa đơn:</strong> Xác nhận đặt mua gói khám trực tuyến (Care Plus, White Up), đối soát chuyển khoản ngân hàng qua cổng định danh và xuất hóa đơn tài chính VAT theo yêu cầu.</li>
                    <li><strong>Tư vấn y khoa trực tuyến & Trợ lý Bác Sĩ AI:</strong> Hỗ trợ giải đáp nhanh các thắc mắc về triệu chứng ban đầu, gợi ý gói khám phù hợp trước khi khách hàng đến trực tiếp phòng khám.</li>
                </ul>
            </section>

            <!-- SECTION 3 -->
            <section id="loai-du-lieu" class="policy-section">
                <h2><span>3.</span> Danh Mục Dữ Liệu Thu Thập</h2>
                <p>Tùy theo sự tương tác của Quý khách, chúng tôi có thể thu thập các nhóm thông tin sau:</p>

                <h3>3.1. Dữ liệu cá nhân cơ bản</h3>
                <ul>
                    <li>Họ và tên, giới tính, ngày tháng năm sinh (để lập hồ sơ bệnh nhân theo độ tuổi sinh lý).</li>
                    <li>Số điện thoại liên lạc, địa chỉ email, địa chỉ cư trú hoặc nơi làm việc.</li>
                    <li>Thông tin người giám hộ (đối với trẻ em dưới 18 tuổi hoặc người cần hỗ trợ y tế).</li>
                </ul>

                <h3>3.2. Dữ liệu y tế & lâm sàng (Dữ liệu cá nhân nhạy cảm)</h3>
                <p>Theo Nghị định 13/2023/NĐ-CP, dữ liệu sức khỏe là dữ liệu nhạy cảm được Flora bảo vệ ở cấp độ an ninh cao nhất:</p>
                <ul>
                    <li>Lịch sử bệnh lý toàn thân (tim mạch, huyết áp, tiểu đường, máu khó đông, tiền sử phẫu thuật).</li>
                    <li>Tiền sử dị ứng thuốc gây tê, kháng sinh, vật liệu nha khoa.</li>
                    <li>Dữ liệu chẩn đoán hình ảnh: Phim X-quang Panorex, phim Cephalo, phim cắt lớp CT Cone Beam 3D, hình ảnh khớp cắn cận cảnh và quét mẫu hàm 3D (iTero/Trios).</li>
                    <li>Nhật ký theo dõi điều trị và chỉ định thuốc của Bác sĩ chuyên khoa Răng Hàm Mặt.</li>
                </ul>

                <h3>3.3. Dữ liệu giao dịch & thanh toán</h3>
                <ul>
                    <li>Mã đơn hàng, gói dịch vụ lựa chọn, số tiền thanh toán, hình thức chuyển khoản qua ngân hàng ACB (Số TK: 77779268 - CONG TY CO PHAN FLORA DENTAL CARE).</li>
                    <li>Ảnh chụp minh chứng giao dịch chuyển khoản do khách hàng tự nguyện tải lên nhằm xác thực đơn hàng.</li>
                    <li><em>Lưu ý an toàn:</em> Flora tuyệt đối không thu thập hoặc lưu trữ mật khẩu ngân hàng, mã OTP hay thông tin CVV thẻ tín dụng của khách hàng.</li>
                </ul>
            </section>

            <!-- SECTION 4 -->
            <section id="thoi-gian-luu-tru" class="policy-section">
                <h2><span>4.</span> Thời Gian & Phạm Vi Lưu Trữ</h2>
                <p>
                    Dữ liệu cá nhân và hồ sơ bệnh án của Quý khách được lưu trữ trên hệ thống máy chủ cơ sở dữ liệu chuyên biệt của Flora Dental Clinic, được đặt tại trung tâm dữ liệu tiêu chuẩn Tier 3 tại Việt Nam với hệ thống sao lưu tự động (Automated Daily Backup).
                </p>
                <div class="policy-cert-box">
                    <div class="policy-cert-item">
                        <div class="policy-cert-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">Hồ Sơ Bệnh Án Ngoại Trú</div>
                            <div style="font-size: 0.82rem; color: #64748b;">Lưu trữ tối thiểu 10 năm theo Điều 69 Luật Khám bệnh, chữa bệnh.</div>
                        </div>
                    </div>
                    <div class="policy-cert-item">
                        <div class="policy-cert-icon"><i class="fa-solid fa-certificate"></i></div>
                        <div>
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">Dữ Liệu Thẻ Bảo Hành</div>
                            <div style="font-size: 0.82rem; color: #64748b;">Lưu trữ trọn đời đối với trụ Implant Thụy Sĩ và từ 5 - 10 năm đối với răng sứ.</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- SECTION 5 -->
            <section id="bao-mat-y-te" class="policy-section">
                <h2><span>5.</span> Biện Pháp An Toàn Thông Tin & Bảo Mật Hồ Sơ</h2>
                <p>Flora áp dụng các tiêu chuẩn an ninh kỹ thuật số và an toàn vật lý đa tầng để bảo vệ dữ liệu:</p>
                <ul>
                    <li><strong>Mã hóa đường truyền SSL/TLS 256-bit:</strong> Toàn bộ dữ liệu gửi qua website, cổng thanh toán và trợ lý AI đều được mã hóa bằng chứng chỉ bảo mật cao cấp nhất.</li>
                    <li><strong>Kiểm soát quyền truy cập phân tầng (Role-Based Access Control):</strong> Chỉ có Bác sĩ trực tiếp điều trị và nhân viên y tế được cấp thẩm quyền mới có quyền truy cập hồ sơ bệnh án của từng bệnh nhân.</li>
                    <li><strong>Hệ thống tường lửa (Firewall) & Quét mã độc:</strong> Hệ thống tự động phát hiện xâm nhập và ngăn chặn các hành vi tấn công mạng 24/7.</li>
                    <li><strong>Bảo mật mật khẩu và dữ liệu định danh:</strong> Mật khẩu truy cập cổng và mã token bí mật đều được băm bằng thuật toán mật mã một chiều không thể giải mã ngược.</li>
                </ul>
            </section>

            <!-- SECTION 6 -->
            <section id="chia-se-du-lieu" class="policy-section">
                <h2><span>6.</span> Cam Kết Không Bán Hoặc Chia Sẻ Dữ Liệu Cho Bên Thứ Ba</h2>
                <p>
                    <strong>Nha Khoa Flora cam kết 100% không bán, không cho thuê, không trao đổi dữ liệu cá nhân của Quý khách cho bất kỳ công ty quảng cáo hoặc bên thứ ba nào vì mục đích thương mại.</strong>
                </p>
                <p>Chúng tôi chỉ chia sẻ dữ liệu trong các trường hợp cực kỳ hạn chế sau:</p>
                <ol>
                    <li><strong>Labo chế tác răng sứ & Hãng vật liệu Thụy Sĩ:</strong> Cung cấp mã số vô danh (Anonymized Code) kèm dữ liệu quét răng 3D để kỹ thuật viên chế tác răng sứ hoặc hãng Straumann/Neodent xuất phôi vật liệu chính hãng.</li>
                    <li><strong>Đơn vị vận chuyển / Viễn thông:</strong> Cung cấp số điện thoại và địa chỉ để gửi thư bảo hành hoặc phát tin nhắn SMS nhắc hẹn khám.</li>
                    <li><strong>Yêu cầu từ Cơ quan Nhà nước có thẩm quyền:</strong> Khi có văn bản yêu cầu chính thức từ cơ quan điều tra, Tòa án hoặc Thanh tra Sở Y tế theo đúng trình tự pháp luật quy định.</li>
                </ol>
            </section>

            <!-- SECTION 7 -->
            <section id="quyen-khach-hang" class="policy-section">
                <h2><span>7.</span> Quyền Của Khách Hàng Đối Với Dữ Liệu Cá Nhân</h2>
                <p>Theo Nghị định 13/2023/NĐ-CP, Quý khách với tư cách là chủ thể dữ liệu có đầy đủ các quyền sau:</p>
                <ul>
                    <li><strong>Quyền được biết:</strong> Được thông báo rõ ràng về hoạt động xử lý dữ liệu cá nhân của mình.</li>
                    <li><strong>Quyền truy cập & chỉnh sửa:</strong> Yêu cầu xem lại hồ sơ tóm tắt điều trị, tra cứu thẻ bảo hành hoặc yêu cầu đính chính thông tin liên hệ khi có thay đổi.</li>
                    <li><strong>Quyền rút lại sự đồng ý:</strong> Quý khách có quyền hủy đăng ký nhận bản tin khuyến mãi hoặc thông báo định kỳ bất kỳ lúc nào qua link trong email hoặc gọi điện tới tổng đài.</li>
                    <li><strong>Quyền khiếu nại & bồi thường:</strong> Có quyền gửi khiếu nại tới Ban Giám Đốc nếu phát hiện thông tin của mình bị sử dụng sai mục đích cam kết.</li>
                </ul>
            </section>

            <!-- SECTION 8 -->
            <section id="don-vi-chiu-trach-nhiem" class="policy-section">
                <h2><span>8.</span> Đơn Vị Xử Lý Dữ Liệu & Thông Tin Liên Hệ</h2>
                <p>Mọi thắc mắc, yêu cầu tra cứu hoặc phản ánh liên quan đến chính sách bảo mật dữ liệu, Quý khách vui lòng liên hệ:</p>

                <div class="contact-highlight-card">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #0033a3; margin: 0 0 14px;">
                        <i class="fa-solid fa-hospital-user"></i> CÔNG TY CỔ PHẦN FLORA DENTAL CARE
                    </h4>
                    <p style="margin-bottom: 8px;"><strong>Cơ sở khám bệnh, chữa bệnh:</strong> Phòng khám Răng Hàm Mặt thuộc địa điểm kinh doanh - Công ty Cổ phần Flora Dental Care</p>
                    <p style="margin-bottom: 8px;"><strong>Giấy phép hoạt động số:</strong> 09723/HCM-GPHĐ do Sở Y tế TP.HCM cấp ngày 27/07/2026</p>
                    <p style="margin-bottom: 8px;"><strong>Địa chỉ phòng khám:</strong> 326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh</p>
                    <p style="margin-bottom: 8px;"><strong>Hotline hỗ trợ:</strong> <a href="tel:02873058999" style="font-weight: 700; color: #0033a3;">028 7305 8999</a> (24/7)</p>
                    <p style="margin-bottom: 0;"><strong>Email tiếp nhận bảo mật:</strong> <a href="mailto:support@floraclinic.vn" style="font-weight: 700; color: #0033a3;">support@floraclinic.vn</a></p>
                </div>
            </section>
        </article>
    </div>
</div>

<script>
// Smooth scroll for TOC links
document.querySelectorAll('.policy-toc-link').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetEl = document.querySelector(targetId);
        if (targetEl) {
            document.querySelectorAll('.policy-toc-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>

<?php
get_footer();
