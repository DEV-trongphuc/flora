<?php
/**
 * Template Name: Chính Sách Bảo Hành
 * Description: Chính sách bảo hành dịch vụ nha khoa, cấy ghép Implant Thụy Sĩ, răng sứ và tra cứu E-Warranty tại Nha Khoa Flora
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
.policy-table-responsive {
    overflow-x: auto;
    margin: 20px 0;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.warranty-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.92rem;
    text-align: left;
}
.warranty-table th {
    background: #f1f5f9;
    color: #0f172a;
    padding: 12px 16px;
    font-weight: 700;
    border-bottom: 2px solid #cbd5e1;
}
.warranty-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
}
.warranty-table tr:hover td {
    background: #f8fafc;
}
.warranty-badge-lifetime {
    background: #dcfce7;
    color: #15803d;
    padding: 4px 10px;
    border-radius: 9999px;
    font-weight: 800;
    font-size: 0.78rem;
    display: inline-block;
}
.warranty-badge-years {
    background: #eff6ff;
    color: #1e40af;
    padding: 4px 10px;
    border-radius: 9999px;
    font-weight: 800;
    font-size: 0.78rem;
    display: inline-block;
}
.warranty-cta-box {
    background: linear-gradient(135deg, #0033a3 0%, #0493f1 100%);
    color: #ffffff;
    border-radius: 16px;
    padding: 28px;
    text-align: center;
    margin: 30px 0;
}
.warranty-cta-box h3 {
    color: #ffffff;
    margin: 0 0 10px;
    font-size: 1.3rem;
    font-weight: 800;
}
.warranty-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    color: #0033a3;
    padding: 12px 28px;
    border-radius: 9999px;
    font-weight: 800;
    text-decoration: none;
    margin-top: 14px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
    transition: transform 0.2s ease;
}
.warranty-cta-btn:hover {
    transform: translateY(-2px);
    color: #002277;
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
            <a href="<?php echo esc_url(home_url('/')); ?>">Trang Chủ</a> &nbsp;/&nbsp; <span>Chính Sách Bảo Hành</span>
        </div>
        <h1 class="policy-title">Chính Sách Bảo Hành & Cam Kết Dịch Vụ</h1>
        <p class="policy-subtitle">
            Hệ thống bảo hành điện tử chính hãng Flora E-Warranty tiêu chuẩn Thụy Sĩ. Bảo hành trọn đời cho trụ Implant Thụy Sĩ và cam kết minh bạch chất lượng điều trị.
        </p>
        <div class="policy-meta-badge">
            <i class="fa-solid fa-certificate" style="color: #38bdf8;"></i> Tiêu chuẩn Thụy Sĩ | Bảo hành điện tử toàn cầu
        </div>
    </div>
</div>

<!-- MAIN CONTENT WRAPPER -->
<div class="policy-container">
    <div class="policy-grid">
        <!-- SIDEBAR TOC -->
        <aside class="policy-sidebar">
            <div class="policy-sidebar-title">
                <i class="fa-solid fa-list-ul"></i> Mục Lục Bảo Hành
            </div>
            <ul class="policy-toc-list">
                <li><a href="#nguyen-tac" class="policy-toc-link active">1. Nguyên Tắc Bảo Hành</a></li>
                <li><a href="#e-warranty" class="policy-toc-link">2. Hệ Thống Flora E-Warranty</a></li>
                <li><a href="#bang-thoi-han" class="policy-toc-link">3. Bảng Thời Hạn Bảo Hành Chi Tiết</a></li>
                <li><a href="#dieu-kien" class="policy-toc-link">4. Điều Kiện Áp Dụng Bảo Hành</a></li>
                <li><a href="#tu-choi" class="policy-toc-link">5. Trường Hợp Miễn Trừ Bảo Hành</a></li>
                <li><a href="#quy-trinh" class="policy-toc-link">6. Quy Trình Tiếp Nhận Bảo Hành</a></li>
                <li><a href="#lien-he" class="policy-toc-link">7. Trung Tâm Bảo Hành & Liên Hệ</a></li>
            </ul>

            <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 8px;">Tra cứu bảo hành nhanh:</div>
                <a href="<?php echo esc_url(home_url('/warranty/')); ?>" style="font-weight: 700; color: #0493f1; font-size: 0.95rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-magnifying-glass"></i> Cổng Tra Cứu Thẻ Bảo Hành
                </a>
            </div>
        </aside>

        <!-- MAIN ARTICLE -->
        <article class="policy-content-card">
            <!-- SECTION 1 -->
            <section id="nguyen-tac" class="policy-section">
                <h2><span>1.</span> Nguyên Tắc Bảo Hành Tiêu Chuẩn Thụy Sĩ</h2>
                <p>
                    Tại <strong>Nha Khoa Flora</strong>, mọi phác đồ can thiệp điều trị đều hướng tới việc bảo tồn tối đa răng thật, phục hồi chức năng ăn nhai hoàn hảo và mang lại nụ cười rạng rỡ, tự nhiên.
                </p>
                <p>
                    Chúng tôi áp dụng chế độ bảo hành chính hãng bằng hợp đồng cam kết văn bản và thẻ bảo hành điện tử nhằm mang lại sự an tâm tuyệt đối và bảo vệ quyền lợi trọn đời cho khách hàng.
                </p>
            </section>

            <!-- SECTION 2 -->
            <section id="e-warranty" class="policy-section">
                <h2><span>2.</span> Hệ Thống Thẻ Bảo Hành Điện Tử Flora E-Warranty</h2>
                <p>
                    Toàn bộ khách hàng sau khi hoàn tất cấy ghép Implant hoặc phục hình răng sứ thẩm mỹ đều được cấp <strong>Mã Thẻ Bảo Hành Điện Tử</strong> đồng bộ trên hệ thống đám mây của phòng khám.
                </p>
                <ul>
                    <li>Quý khách có thể tự tra cứu tình trạng bảo hành, vật liệu sứ/trụ Implant sử dụng, bác sĩ điều trị và thời hạn bảo hành bất cứ lúc nào qua số điện thoại cá nhân.</li>
                    <li>Thẻ bảo hành điện tử giúp tránh thất lạc, hư hỏng thẻ giấy truyền thống và có giá trị tương đương tại tất cả các điểm khám của hệ thống Flora.</li>
                </ul>

                <div class="warranty-cta-box">
                    <h3><i class="fa-solid fa-shield-halved"></i> Bạn Đang Sở Hữu Thẻ Bảo Hành Flora?</h3>
                    <p style="margin: 0; font-size: 0.95rem; opacity: 0.92;">Kiểm tra thông tin phôi sứ chính hãng, dòng trụ Implant và hạn bảo hành trực tuyến chỉ với số điện thoại của bạn.</p>
                    <a href="<?php echo esc_url(home_url('/warranty/')); ?>" class="warranty-cta-btn">
                        <i class="fa-solid fa-magnifying-glass"></i> Tra Cứu Bảo Hành Ngay
                    </a>
                </div>
            </section>

            <!-- SECTION 3 -->
            <section id="bang-thoi-han" class="policy-section">
                <h2><span>3.</span> Bảng Thời Hạn Bảo Hành Chi Tiết Từng Dịch Vụ</h2>
                <div class="policy-table-responsive">
                    <table class="warranty-table">
                        <thead>
                            <tr>
                                <th>Nhóm Dịch Vụ</th>
                                <th>Vật Liệu / Thương Hiệu</th>
                                <th>Thời Hạn Bảo Hành</th>
                                <th>Phạm Vi Cam Kết</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Cấy Ghép Implant</strong></td>
                                <td>Straumann (Thụy Sĩ), Neodent (Thụy Sĩ), Nobel Biocare (Mỹ)</td>
                                <td><span class="warranty-badge-lifetime">TRỌN ĐỜI</span></td>
                                <td>Bảo hành tích hợp xương, không đào thải trụ, bảo hành linh kiện chính hãng toàn cầu.</td>
                            </tr>
                            <tr>
                                <td><strong>Cấy Ghép Implant</strong></td>
                                <td>MIS C1 (Đức), Osstem (Hàn Quốc)</td>
                                <td><span class="warranty-badge-years">10 NĂM</span></td>
                                <td>Bảo hành cấu trúc trụ, độ ổn định cơ học và khớp nối Abutment.</td>
                            </tr>
                            <tr>
                                <td><strong>Răng Sứ Thẩm Mỹ</strong></td>
                                <td>Zirconia HT, Cercon XT, Lava Plus (3M Mỹ)</td>
                                <td><span class="warranty-badge-years">10 NĂM</span></td>
                                <td>Bảo hành độ bền chịu lực, không nứt vỡ, không đổi màu, chống ố vàng và khít sát đường viền nướu.</td>
                            </tr>
                            <tr>
                                <td><strong>Mặt Dán Sứ Veneer</strong></td>
                                <td>E.max Press (Ivoclar Vivadent - Thụy Sĩ/Liechtenstein)</td>
                                <td><span class="warranty-badge-years">05 - 10 NĂM</span></td>
                                <td>Bảo hành độ trong bóng quang học, không sứt mẻ và liên kết dán dính vĩnh viễn.</td>
                            </tr>
                            <tr>
                                <td><strong>Chỉnh Nha / Niềng Răng</strong></td>
                                <td>Invisalign (Mỹ), Mắc cài sứ pha lê</td>
                                <td><span class="warranty-badge-years">THEO PHÁC ĐỒ</span></td>
                                <td>Cam kết khớp cắn chuẩn, nụ cười hài hòa theo kế hoạch điều trị ClinCheck đã ký duyệt.</td>
                            </tr>
                            <tr>
                                <td><strong>Gói Khám Trực Tuyến</strong></td>
                                <td>Flora Care Plus, Flora White Up</td>
                                <td><span class="warranty-badge-years">12 THÁNG</span></td>
                                <td>Bảo lưu quyền lợi khám, cạo vôi, tẩy trắng và tái khám miễn phí trong 1 năm.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- SECTION 4 -->
            <section id="dieu-kien" class="policy-section">
                <h2><span>4.</span> Điều Kiện Áp Dụng Bảo Hành</h2>
                <p>Khách hàng được áp dụng chế độ bảo hành miễn phí khi đáp ứng các điều kiện sau:</p>
                <ul>
                    <li>Dịch vụ nha khoa được thực hiện và hoàn tất tại Nha Khoa Flora, có ghi nhận trong hồ sơ bệnh án hoặc thẻ bảo hành điện tử.</li>
                    <li>Khách hàng tuân thủ lịch tái khám định kỳ theo chỉ định của Bác sĩ (tối thiểu 06 tháng/lần) để kiểm tra khớp cắn, vệ sinh cạo vôi răng mảng bám.</li>
                    <li>Tuân thủ đúng hướng dẫn vệ sinh răng miệng hàng ngày, sử dụng chỉ nha khoa, tăm nước và đeo máng bảo vệ khớp cắn / hàm duy trì (đối với niềng răng hoặc người có tật nghiến răng).</li>
                </ul>
            </section>

            <!-- SECTION 5 -->
            <section id="tu-choi" class="policy-section">
                <h2><span>5.</span> Các Trường Hợp Miễn Trừ Bảo Hành</h2>
                <p>Nha Khoa Flora có quyền từ chối bảo hành miễn phí (nhưng vẫn hỗ trợ khắc phục với chi phí ưu đãi) trong các trường hợp:</p>
                <ul>
                    <li>Khách hàng gặp tai nạn giao thông, té ngã, va đập mạnh hoặc chấn thương vùng mặt từ ngoại cảnh sau khi điều trị.</li>
                    <li>Sử dụng răng phục hình để cắn các vật quá cứng dị thường (khui nắp chai, cắn xương, cắn đá lạnh, cắn kim loại).</li>
                    <li>Khách hàng tự ý can thiệp, chỉnh sửa, mài mòn răng phục hình hoặc tháo lắp trụ tại các cơ sở nha khoa khác mà không có sự đồng ý của bác sĩ Flora.</li>
                    <li>Bệnh nhân không tham gia tái khám định kỳ theo lịch hẹn trong thời gian liên tục quá 12 tháng.</li>
                    <li>Phát sinh các bệnh lý toàn thân mới không báo trước ảnh hưởng nghiêm trọng đến xương hàm (xạ trị vùng đầu mặt cổ, loãng xương nặng giai đoạn cuối, tiểu đường mất kiểm soát).</li>
                </ul>
            </section>

            <!-- SECTION 6 -->
            <section id="quy-trinh" class="policy-section">
                <h2><span>6.</span> Quy Trình Tiếp Nhận & Xử Lý Bảo Hành</h2>
                <ol>
                    <li><strong>Bước 1 - Tiếp nhận thông tin:</strong> Khách hàng gọi tới Hotline <code>028 7305 8999</code> hoặc mang mã thẻ bảo hành tới trực tiếp phòng khám.</li>
                    <li><strong>Bước 2 - Thăm khám & Kiểm tra:</strong> Bác sĩ chuyên khoa tiến hành chụp phim X-quang kiểm tra thực trạng, đối chiếu dữ liệu ban đầu trên phần mềm.</li>
                    <li><strong>Bước 3 - Xử lý trong vòng 24 - 48h:</strong>
                        <ul style="margin-top: 6px;">
                            <li>Đối với răng sứ nứt mẻ: Lấy dấu lại và gửi Labo chế tác mới hoàn toàn miễn phí.</li>
                            <li>Đối với trụ Implant: Xử lý vệ sinh, siết lại vít kết nối hoặc thay thế trụ mới theo cam kết chính hãng.</li>
                        </ul>
                    </li>
                </ol>
            </section>

            <!-- SECTION 7 -->
            <section id="lien-he" class="policy-section">
                <h2><span>7.</span> Trung Tâm Bảo Hành & Chăm Sóc Khách Hàng</h2>
                <div class="contact-highlight-card">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: #0033a3; margin: 0 0 14px;">
                        <i class="fa-solid fa-headset"></i> TRUNG TÂM BẢO HÀNH NHA KHOA FLORA
                    </h4>
                    <p style="margin-bottom: 8px;"><strong>Địa chỉ:</strong> 326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, TP. Hồ Chí Minh</p>
                    <p style="margin-bottom: 8px;"><strong>Giờ làm việc:</strong> 8h30 - 18h30 (Tất cả các ngày trong tuần, kể cả Thứ Bảy & Chủ Nhật)</p>
                    <p style="margin-bottom: 8px;"><strong>Hotline hỗ trợ bảo hành 24/7:</strong> <a href="tel:02873058999" style="font-weight: 700; color: #0033a3;">028 7305 8999</a></p>
                    <p style="margin-bottom: 0;"><strong>Tra cứu trực tuyến:</strong> <a href="<?php echo esc_url(home_url('/warranty/')); ?>" style="font-weight: 700; color: #0493f1;">nhakhoaflora.com/warranty/</a></p>
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
