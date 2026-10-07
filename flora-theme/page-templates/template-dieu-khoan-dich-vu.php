<?php
/**
 * Template Name: Điều Khoản Dịch Vụ
 * Description: Điều khoản dịch vụ, quy định đặt lịch, thanh toán gói khám và sử dụng website Nha Khoa Flora
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
            <a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a> &nbsp;/&nbsp; <span>Điều Khoản Dịch Vụ</span>
        </div>
        <h1 class="policy-title">Điều Khoản Dịch Vụ & Sử Dụng</h1>
        <p class="policy-subtitle">
            Quy định chung về đặt lịch khám, đăng ký mua gói dịch vụ, tư vấn y tế trực tuyến và nguyên tắc hợp tác tại Nha Khoa Flora.
        </p>
        <div class="policy-meta-badge">
            <i class="fa-solid fa-file-contract" style="color: #38bdf8;"></i> Hiệu lực áp dụng: Năm 2026 | Nha Khoa Flora
        </div>
    </div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div class="policy-container">
    <div class="policy-grid">
        <!-- SIDEBAR TOC -->
        <aside class="policy-sidebar">
            <div class="policy-sidebar-title">
                <i class="fa-solid fa-list-ul"></i> Mục Lục Điều Khoản
            </div>
            <ul class="policy-toc-list">
                <li><a href="#chap-thuan" class="policy-toc-link active">1. Chấp Thuận Điều Khoản</a></li>
                <li><a href="#pham-vi-dich-vu" class="policy-toc-link">2. Phạm Vi Dịch Vụ & Tư Vấn AI</a></li>
                <li><a href="#quy-trinh-dat-lich" class="policy-toc-link">3. Quy Trình Đặt Hẹn & Giữ Chỗ</a></li>
                <li><a href="#quy-dinh-thanh-toan" class="policy-toc-link">4. Thanh Toán Gói Dịch Vụ</a></li>
                <li><a href="#doi-lich-hoan-tien" class="policy-toc-link">5. Đổi Lịch & Hoàn Phí</a></li>
                <li><a href="#ban-quyen-thuong-hieu" class="policy-toc-link">6. Bản Quyền & Sở Hữu Trí Tuệ</a></li>
                <li><a href="#trach-nhiem-phap-ly" class="policy-toc-link">7. Giới Hạn Trách Nhiệm Pháp Lý</a></li>
                <li><a href="#thong-tin-phap-ly" class="policy-toc-link">8. Đơn Vị Quản Lý & Liên Hệ</a></li>
            </ul>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 8px;">Hotline giải đáp thắc mắc:</div>
                <a href="tel:02873058999" style="font-weight: 800; color: #0033a3; font-size: 1.1rem; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-phone" style="color: #0493f1;"></i> 028 7305 8999
                </a>
            </div>
        </aside>

        <!-- MAIN ARTICLE -->
        <article class="policy-content-card">
            <!-- SECTION 1 -->
            <section id="chap-thuan" class="policy-section">
                <h2><span>1.</span> Chấp Thuận Các Điều Khoản</h2>
                <p>
                    Khi Quý khách truy cập, tham khảo thông tin, đặt lịch hẹn khám hoặc thực hiện giao dịch mua gói dịch vụ trên website <code>nhakhoaflora.com</code>, Quý khách được xem là đã đọc, hiểu rõ và đồng ý tuân thủ toàn bộ các điều khoản được quy định dưới đây.
                </p>
                <p>
                    Nếu Quý khách không đồng ý với bất kỳ phần nào của điều khoản này, vui lòng ngừng sử dụng dịch vụ hoặc liên hệ trực tiếp với bộ phận chăm sóc khách hàng của Flora để được giải đáp chi tiết.
                </p>
            </section>

            <!-- SECTION 2 -->
            <section id="pham-vi-dich-vu" class="policy-section">
                <h2><span>2.</span> Phạm Vi Dịch Vụ & Tư Vấn Y Tế Trực Tuyến</h2>
                <p>
                    Nha Khoa Flora cung cấp các dịch vụ chuyên sâu Răng Hàm Mặt: Cấy ghép Implant Thụy Sĩ, Chỉnh nha niềng răng, Răng sứ thẩm mỹ & Mặt dán sứ Veneer, Điều trị cười hở lợi, cùng các Gói Khám Chăm Sóc Định Kỳ (Care Plus, White Up).
                </p>
                <div class="policy-alert-box">
                    <strong><i class="fa-solid fa-robot"></i> Lưu ý đặc biệt về Trợ lý Bác Sĩ AI & Tư vấn trực tuyến:</strong>
                    <ul style="margin: 8px 0 0 16px; padding: 0;">
                        <li>Các thông tin tư vấn từ tính năng Bác Sĩ AI trên website chỉ mang tính chất <em>tham khảo, định hướng triệu chứng và cung cấp kiến thức nha khoa tổng quát</em>.</li>
                        <li>Trợ lý AI <strong>không thay thế</strong> việc khám lâm sàng trực tiếp, chụp phim X-quang CT Cone Beam 3D và phác đồ điều trị của Bác sĩ chuyên khoa tại phòng khám.</li>
                        <li>Bệnh nhân cần đến trực tiếp Nha Khoa Flora để được Bác sĩ kiểm tra thực tế trước khi thực hiện bất kỳ can thiệp nha khoa nào.</li>
                    </ul>
                </div>
            </section>

            <!-- SECTION 3 -->
            <section id="quy-trinh-dat-lich" class="policy-section">
                <h2><span>3.</span> Quy Trình Đặt Hẹn & Xác Nhận Lịch Khám</h2>
                <ul>
                    <li><strong>Đăng ký lịch khám:</strong> Khách hàng điền thông tin họ tên, số điện thoại, nhu cầu khám và khung giờ mong muốn qua website hoặc hotline.</li>
                    <li><strong>Xác nhận lịch hẹn:</strong> Chuyên viên CSKH của Flora sẽ gọi điện hoặc gửi tin nhắn SMS/Zalo để xác nhận giờ khám chính xác trong vòng 15 - 30 phút sau khi tiếp nhận thông tin.</li>
                    <li><strong>Giữ giờ hẹn:</strong> Để đảm bảo chất lượng phục vụ tiêu chuẩn Thụy Sĩ và không phải chờ đợi lâu, Quý khách vui lòng đến đúng giờ đã hẹn hoặc trước 10 phút. Nếu đến trễ quá 20 phút mà không thông báo trước, phòng khám có thể sắp xếp điều chỉnh giờ khám theo tình hình thực tế.</li>
                </ul>
            </section>

            <!-- SECTION 4 -->
            <section id="quy-dinh-thanh-toan" class="policy-section">
                <h2><span>4.</span> Quy Định Mua Gói Dịch Vụ & Thanh Toán</h2>
                <p>
                    Đối với các gói dịch vụ trực tuyến (Flora Care Plus, Flora White Up), Quý khách thực hiện thanh toán theo hình thức chuyển khoản ngân hàng an toàn:
                </p>
                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 20px; margin: 16px 0;">
                    <div style="font-weight: 800; color: #0033a3; font-size: 1rem; margin-bottom: 8px;">
                        <i class="fa-solid fa-building-columns"></i> TÀI KHOẢN NGÂN HÀNG CHÍNH THỨC CỦA NHA KHOA FLORA
                    </div>
                    <ul style="list-style: none; padding: 0; margin: 0; line-height: 1.8; font-size: 0.95rem;">
                        <li>Ngân hàng: <strong>Ngân hàng TMCP Á Châu (ACB)</strong></li>
                        <li>Chi nhánh: <strong>ACB - CN HOA HUNG</strong></li>
                        <li>Số tài khoản: <strong style="color: #0033a3; font-size: 1.1rem; font-family: monospace;">77779268</strong></li>
                        <li>Tên chủ tài khoản: <strong>CONG TY CO PHAN FLORA DENTAL CARE</strong></li>
                    </ul>
                </div>
                <p>
                    <strong>Cú pháp chuyển khoản định danh:</strong> Khi đặt đơn trên hệ thống, màn hình sẽ hiển thị mã định danh đơn hàng (Vd: <code>FLORA 0912345678 12345</code> hoặc <code>FLORA KOLNAME 0912345678 12345</code>). Quý khách vui lòng điền đúng cú pháp để hệ thống ghi nhận đối soát tự động.
                </p>
                <p>
                    Sau khi chuyển khoản thành công, Quý khách bấm nút <strong>"Tôi Đã Chuyển Khoản"</strong> trên màn hình và có thể tải kèm ảnh chụp màn hình giao dịch làm minh chứng để đơn hàng được duyệt ưu tiên.
                </p>
            </section>

            <!-- SECTION 5 -->
            <section id="doi-lich-hoan-tien" class="policy-section">
                <h2><span>5.</span> Chính Sách Đổi Lịch, Bảo Lưu & Hoàn Tiền</h2>
                <ul>
                    <li><strong>Đổi lịch hẹn:</strong> Khách hàng đã thanh toán gói dịch vụ có quyền dời lịch khám miễn phí bằng cách thông báo cho Flora trước tối thiểu 04 tiếng so với giờ hẹn đã định.</li>
                    <li><strong>Thời hạn sử dụng gói khám:</strong> Các gói dịch vụ mua trực tuyến có thời hạn kích hoạt và sử dụng trong vòng <strong>12 tháng</strong> kể từ ngày thanh toán. Quý khách có thể chuyển nhượng gói khám cho người thân hoặc bạn bè bằng cách thông báo cho tổng đài Flora.</li>
                    <li><strong>Chính sách hoàn tiền:</strong> Nếu khách hàng chưa sử dụng bất kỳ dịch vụ nào trong gói và có lý do bất khả kháng (chuyển công tác xa, điều kiện sức khỏe không phù hợp theo chỉ định của bác sĩ chuyên khoa), Flora sẽ tiếp nhận hồ sơ hoàn phí qua tài khoản ngân hàng trong vòng 03 - 05 ngày làm việc theo quy chế kế toán doanh nghiệp.</li>
                </ul>
            </section>

            <!-- SECTION 6 -->
            <section id="ban-quyen-thuong-hieu" class="policy-section">
                <h2><span>6.</span> Bản Quyền & Sở Hữu Trí Tuệ</h2>
                <p>
                    Toàn bộ nội dung xuất hiện trên website này bao gồm nhưng không giới hạn: hình ảnh ca lâm sàng thực tế, logo Flora Dental Care, video chuyên môn của Bác sĩ, cấu trúc bảng giá, tài liệu hướng dẫn chăm sóc răng miệng đều thuộc quyền sở hữu trí tuệ hợp pháp của Công ty Cổ phần Flora Dental Care hoặc các đối tác cung cấp thiết bị y tế Thụy Sĩ đã được cấp phép.
                </p>
                <p>
                    Mọi hành vi sao chép, trích dẫn, phát hành lại nội dung hoặc sử dụng thương hiệu Flora cho mục đích thương mại mà chưa có sự đồng ý bằng văn bản từ Ban Giám Đốc đều là hành vi vi phạm pháp luật Sở hữu trí tuệ.
                </p>
            </section>

            <!-- SECTION 7 -->
            <section id="trach-nhiem-phap-ly" class="policy-section">
                <h2><span>7.</span> Giới Hạn Trách Nhiệm Pháp Lý</h2>
                <ul>
                    <li>Flora nỗ lực tối đa để đảm bảo thông tin trên website là chính xác, cập nhật và hữu ích nhất. Tuy nhiên, sự phát triển của y học và tình trạng sinh lý của mỗi cá nhân là khác nhau, kết quả điều trị thực tế sẽ được bác sĩ xác định cụ thể trong phác đồ điều trị.</li>
                    <li>Chúng tôi không chịu trách nhiệm trong trường hợp khách hàng tự ý sử dụng các gợi ý trên website để tự điều trị tại nhà mà không qua thăm khám của bác sĩ.</li>
                    <li>Website có thể tạm gián đoạn dịch vụ trong khoảng thời gian ngắn để bảo trì kỹ thuật định kỳ nhằm nâng cấp hệ thống an toàn thông tin.</li>
                </ul>
            </section>

            <!-- SECTION 8 -->
            <section id="thong-tin-phap-ly" class="policy-section">
                <h2><span>8.</span> Đơn Vị Quản Lý & Thông Tin Liên Hệ</h2>
                <div class="contact-highlight-card">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #0033a3; margin: 0 0 14px;">
                        <i class="fa-solid fa-shield-heart"></i> HỆ THỐNG NHA KHOA FLORA
                    </h4>
                    <p style="margin-bottom: 8px;"><strong>Đơn vị chủ quản:</strong> Công ty Cổ phần Flora Dental Care</p>
                    <p style="margin-bottom: 8px;"><strong>Cơ sở khám bệnh, chữa bệnh:</strong> Phòng khám Răng Hàm Mặt thuộc địa điểm kinh doanh - Công ty Cổ phần Flora Dental Care</p>
                    <p style="margin-bottom: 8px;"><strong>Giấy phép hoạt động:</strong> Số 09723/HCM-GPHĐ do Sở Y tế TP.HCM cấp ngày 27/07/2026</p>
                    <p style="margin-bottom: 8px;"><strong>Địa chỉ:</strong> 326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh</p>
                    <p style="margin-bottom: 8px;"><strong>Tổng đài tư vấn:</strong> <a href="tel:02873058999" style="font-weight: 700; color: #0033a3;">028 7305 8999</a> (24/7)</p>
                    <p style="margin-bottom: 0;"><strong>Email chính thức:</strong> <a href="mailto:support@floraclinic.vn" style="font-weight: 700; color: #0033a3;">support@floraclinic.vn</a></p>
                </div>
            </section>
        </article>
    </div>
</div>

<script>
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
