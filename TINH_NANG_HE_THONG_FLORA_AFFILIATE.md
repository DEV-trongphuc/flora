# HỆ THỐNG TOÀN DIỆN VOUCHER & AFFILIATE NHA KHOA FLORA 2026
## TÀI LIỆU TỔNG QUAN TÍNH NĂNG ĐÃ TRIỂN KHAI & BẢN ĐỒ GIAO DIỆN HỆ THỐNG (LANDSCAPE)

> **Ấn bản tài liệu chính thức**  
> **Đơn vị phát hành:** Hệ Thống Nha Khoa Flora Dental Clinic  
> **Phiên bản hệ thống:** Flora Affiliate & Voucher Hub v2.3.7  
> **Tệp ảnh tổng quan duy nhất (Master Landscape PNG):** [`TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png`](file:///d:/GITHUB_SPACE/FLORA/TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png)  
> **Tệp tài liệu PDF Nằm Ngang (Master Landscape PDF):** [`TONG_QUAN_TINH_NANG_HE_THONG_FLORA_LANDSCAPE.pdf`](file:///d:/GITHUB_SPACE/FLORA/TONG_QUAN_TINH_NANG_HE_THONG_FLORA_LANDSCAPE.pdf)  
> **Phong cách thiết kế:** Định dạng Nằm Ngang (Landscape), chuẩn nhận diện Xanh Brand Flora (`#0033a3`, `#071a3d`, `#002270`), loại bỏ hoàn toàn các viền màu sắc lòe loẹt, tinh tế và tối giản như ấn phẩm PDF chuẩn mực y khoa Thụy Sĩ.

---

## MỤC LỤC
1. [Tổng Quan Kiến Trúc Hệ Thống (3 Trụ Cột Nằm Ngang)](#1-tổng-quan-kiến-trúc-hệ-thống-3-trụ-cột-nằm-ngang)
2. [Phân Hệ Khách Hàng (Customer Journey — 4 Bước)](#2-phân-hệ-khách-hàng-customer-journey--4-bước)
3. [Phân Hệ Đối Tác Tiếp Thị (Affiliate Partner Portal — 4 Module)](#3-phân-hệ-đối-tác-tiếp-thị-affiliate-partner-portal--4-module)
4. [Phân Hệ Quản Trị Trung Tâm Flora (Admin Backoffice & 3 Màn Hình Thật)](#4-phân-hệ-quản-trị-trung-tâm-flora-admin-backoffice--3-màn-hình-thật)
5. [Hạ Tầng Vận Hành & Tự Động Hóa Kỹ Thuật Số](#5-hạ-tầng-vận-hành--tự-động-hóa-kỹ-thuật-số)
6. [Danh Mục Màn Hình & Tệp Ảnh Master Landscape PNG](#6-danh-mục-màn-hình--tệp-ảnh-master-landscape-png)

---

## 1. TỔNG QUAN KIẾN TRÚC HỆ THỐNG (3 TRỤ CỘT NẰM NGANG)

Hệ thống được tổ chức thành 3 luồng giao diện trực quan trải dài theo chiều ngang:

```
========================================================================================================================
                                     NHA KHOA FLORA • DENTAL CLINIC
                BẢN ĐỒ KIẾN TRÚC TÍNH NĂNG & GIAO DIỆN HỆ THỐNG (ĐỊNH DẠNG NẰM NGANG)
========================================================================================================================
[ HÀNG 01: PHÂN HỆ KHÁCH HÀNG ]
  (1) Đặt Mua Voucher Online  ──►  (2) VietQR Thanh Toán ACB  ──►  (3) E-Voucher & Email QR  ──►  (4) Tra Cứu Đơn Hàng
      Gói Care Plus / White Up          Webhook 3s tự động             Mã định danh check-in           Self-Service Online

[ HÀNG 02: PHÂN HỆ ĐỐI TÁC TIẾP THỊ (PORTAL) ]
  (5) Đăng Nhập Cổng Đối Tác   ──►  (6) Dashboard 4 Chỉ Số    ──►  (7) Bảng Kê Doanh Thu     ──►  (8) Tạo Lệnh Rút Tiền
      Xác thực tài khoản riêng          Link Ref & QR tiếp thị         Bảo mật Y khoa HIPAA            Min 100k, Flora duyệt

[ HÀNG 03: TRUNG TÂM QUẢN TRỊ FLORA ADMIN & VẬN HÀNH ]
  (9) Quản Lý KOL & Affiliate ──► (10) Quản Lý Đơn Gói Khám ──► (11) Chi Tiết KOL & Quyết Toán ──► (12) 4 Khối Vận Hành
      Giám sát 6 chỉ số toàn hệ        Xác thực VietQR ACB, Duyệt      Ca điều trị thực tế, STK         ACB • Zalo • Napas • HIPAA
========================================================================================================================
```

---

## 2. PHÂN HỆ KHÁCH HÀNG (CUSTOMER JOURNEY — 4 BƯỚC)

### 2.1. Bước 1: Đặt Mua Voucher Nha Khoa (Checkout Page)
- **Màn hình thực tế:** [`checkout_flora_screen.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/checkout_flora_screen.png)
- **Tính năng:**
  - Chọn gói dịch vụ: **GÓI CARE PLUS** (Chăm sóc răng miệng định kỳ) hoặc **GÓI FLORA WHITE UP** (Tẩy trắng răng công nghệ cao).
  - Tự động nhận diện mã giới thiệu Đối tác (Ref Code) qua URL.
  - Thu thập thông tin khách hàng nhận voucher: Họ tên, Số điện thoại, Email.
  - Giao diện thiết kế tối ưu chuyển đổi cao trên Mobile & Desktop.

### 2.2. Bước 2: Cổng VietQR Thanh Toán ACB Tự Động (Webhook 3s)
- **Màn hình thực tế:** [`vietqr_flora_modal.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/vietqr_flora_modal.png)
- **Tính năng:**
  - Tự động sinh mã VietQR động chuẩn NAPAS khớp chính xác số tiền và mã đơn hàng.
  - Tương thích 100% ứng dụng ngân hàng và ví điện tử (VCB, ACB, Techcombank, MB, MoMo,...).
  - Đồng hồ đếm ngược 15 phút bảo mật giao dịch.
  - **ACB Webhook khớp lệnh 3 giây**: Tự động hoàn tất thanh toán, **không bắt buộc khách gửi bill thủ công**.

### 2.3. Bước 3: E-Voucher & Email Xác Nhận Tức Thì
- **Màn hình thực tế:** [`email_success_flora_real.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/email_success_flora_real.png)
- **Tính năng:**
  - Header chuẩn nhận diện thương hiệu Nha Khoa Flora kèm logo.
  - Cấp **mã Voucher điện tử định danh** kèm **mã QR check-in** tại quầy lễ tân phòng khám.
  - Gửi kèm cẩm nang chăm sóc răng miệng, bản đồ cơ sở và hotline đặt hẹn ưu tiên.

### 2.4. Bước 4: Cổng Tra Cứu Đơn Hàng Online (Order Tracking)
- **Màn hình thực tế:** [`order_tracking_screen.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/order_tracking_screen.png)
- **Tính năng:**
  - Cổng tra cứu trực tuyến dành riêng cho khách hàng (Self-Service).
  - Khách chỉ cần nhập Mã đơn hàng hoặc Số điện thoại để kiểm tra trạng thái kích hoạt.
  - Giảm tải 80% áp lực hỗ trợ khách hàng cho đội ngũ lễ tân phòng khám.

---

## 3. PHÂN HỆ ĐỐI TÁC TIẾP THỊ (AFFILIATE PARTNER PORTAL — 4 MODULE)

### 3.1. Module 1: Đăng Nhập Cổng Đối Tác (Portal Auth)
- **Màn hình thực tế:** [`portal_auth_screen.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/portal_auth_screen.png)
- **Tính năng:**
  - Cổng xác thực độc lập, an toàn dành cho cộng tác viên và đối tác.
  - Đăng ký và kích hoạt nhanh chóng, phân quyền tài khoản đối tác tự động.
  - Ghi nhớ đăng nhập an toàn, tương thích mọi thiết bị di động.

### 3.2. Module 2: Dashboard Thống Kê 4 Chỉ Số & Bộ Công Cụ Ref
- **Màn hình thực tế:** [`real_portal_dashboard.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_portal_dashboard.png)
- **Tính năng:**
  - Bảng điều khiển 4 chỉ số realtime: **Doanh số phát sinh, Hoa hồng tích lũy 10%, Số đơn thành công, Lượt clicks**.
  - Nút sao chép **Đường link Ref độc quyền** để gắn vào bio TikTok, Facebook, YouTube.
  - Nút tải ngay **Mã QR tiếp thị riêng** dạng vector sắc nét để in ấn standee hoặc quầy tiếp tân.

### 3.3. Module 3: Bảng Kê Doanh Thu & Bảo Mật Chuẩn Y Khoa HIPAA
- **Màn hình thực tế:** [`real_ledger_table.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_ledger_table.png)
- **Tính năng:**
  - Bảng kê minh bạch từng giao dịch (Mã đơn FT..., ngày giờ, số tiền).
  - Tự động cộng chuẩn xác hoa hồng 10%: **+125.000đ** (hoặc gói 2.500.000đ) / **+265.000đ** (White Up).
  - **Tuân thủ chuẩn bảo mật Y khoa HIPAA quốc tế**: Ẩn danh toàn bộ bệnh án và số điện thoại bệnh nhân để bảo vệ quyền riêng tư y đức.

### 3.4. Module 4: Quản Lý Rút Tiền & Cập Nhật STK Ngân Hàng
- **Màn hình thực tế:**
  - Modal Rút Tiền: [`real_withdrawal_modal.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_withdrawal_modal.png)
  - Modal Cập Nhật STK: [`real_bank_update_modal.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_bank_update_modal.png)
- **Tính năng:**
  - Đối tác chủ động gửi lệnh rút tiền 24/7 trực tiếp trên Portal (Hạn mức tối thiểu: **100.000đ**).
  - Cập nhật số tài khoản ngân hàng chính chủ linh hoạt (ACB, Vietcombank, Techcombank,...).
  - **Flora đối soát & phê duyệt**: Kế toán Flora kiểm tra số dư và duyệt chi an toàn trước khi chuyển khoản qua cổng Napas 24/7 liên ngân hàng.

---

## 4. PHÂN HỆ QUẢN TRỊ TRUNG TÂM FLORA (ADMIN BACKOFFICE & 3 MÀN HÌNH THẬT)

### 4.1. Màn Hình 1: Quản Lý Mạng Lưới KOL & Affiliate (Admin Overview)
- **Màn hình thực tế:** [`real_flora_admin_dashboard.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_flora_admin_dashboard.png)
- **Tính năng:**
  - Giám sát toàn bộ đối tác KOL (Bà Nhi Vlog, Đối tác mẫu,...), cấp mã REF, trạng thái hoạt động.
  - Báo cáo 6 chỉ số trọng yếu: KOL hoạt động, Lượt truy cập REF, Đơn KOL thành công, Doanh thu từ KOL, Hoa hồng cần chi trả, Đơn trực tiếp Flora.
  - Nút "Quản Lý Đơn Rút Tiền" và nút "Xuất Đối Soát Toàn Bộ" ra file Excel.

### 4.2. Màn Hình 2: Quản Lý Đơn Hàng Gói Dịch Vụ (Order Management)
- **Màn hình thực tế:** [`real_admin_order_management.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_admin_order_management.png)
- **Tính năng:**
  - Xác thực chuyển khoản VietQR Ngân hàng ACB (77779268), đối soát cú pháp SĐT và kích hoạt gói khám tự động.
  - 4 thẻ thống kê: Tổng đơn hàng, Doanh thu đã thu, Chờ xác nhận, Từ chối/Quá 24h.
  - Bảng danh sách đơn hàng thực tế: Cú pháp CK, Minh chứng bill, Trạng thái (Đã duyệt / Chờ duyệt), thao tác Duyệt/Từ chối.
  - Tích hợp nút cấu hình **Cài Đặt ACB & Zalo Bot** và **Xuất Excel (CSV)**.

### 4.3. Màn Hình 3: Chi Tiết Đối Tác & Quyết Toán KOL (KOL Detail Modal)
- **Màn hình thực tế:** [`real_admin_kol_detail_modal.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/real_admin_kol_detail_modal.png)
- **Tính năng:**
  - Xem chi tiết từng ca điều trị thực tế gắn với đối tác:
    - Niềng răng trong suốt Invisalign: 45.000.000đ (Hoa hồng: +4.500.000đ).
    - Cấy ghép Implant Straumann SLA Active: 28.000.000đ (Hoa hồng: +2.800.000đ).
    - Răng sứ toàn sứ Thụy Sĩ (16 răng): 15.000.000đ (Hoa hồng: +1.500.000đ).
    - Gói Care Plus gia đình: 2.500.000đ (Hoa hồng: +250.000đ).
  - Nguyên tắc tài chính chuẩn xác: "Chỉ tính hoa hồng thực nhận khi đơn đã thanh toán".
  - Hiển thị chuẩn STK thụ hưởng ACB: 77779268 (NGUYEN THU TRANG).
  - Nút chức năng **Quyết Toán** và **Xuất Excel KOL**.

---

## 5. HẠ TẦNG VẬN HÀNH & TỰ ĐỘNG HÓA KỸ THUẬT SỐ

1. **ACB Open Banking Webhook (< 3 giây):** Tự động bắt tín hiệu biến động số dư và kích hoạt đơn hàng không cần soát bill thủ công.
2. **Zalo Bot Realtime Alert:** Bắn thông báo tức thời về Zalo ngay khi có đơn mới hoặc yêu cầu rút tiền mới từ đối tác.
3. **Chi Trả Napas 24/7 (Flora Duyệt):** Kế toán Flora kiểm soát và phê duyệt an toàn trước khi chuyển khoản liên ngân hàng. Min rút 100.000đ.
4. **Bảo Mật Y Khoa Chuẩn HIPAA:** Mã hóa dữ liệu bệnh án, ẩn danh SĐT người bệnh trên Portal để bảo vệ y đức và quyền riêng tư.

---

## 6. DANH MỤC MÀN HÌNH & TỆP ẢNH MASTER LANDSCAPE PNG

### 🖼️ Tệp Ảnh Master Duy Nhất (Định Dạng Nằm Ngang - Chuẩn Xanh Brand)
- **Đường dẫn tệp gốc:** [`d:\GITHUB_SPACE\FLORA\TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png`](file:///d:/GITHUB_SPACE/FLORA/TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png)
- **Đường dẫn booklet:** [`d:\GITHUB_SPACE\FLORA\affiliate-booklet\TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png)
- **Kích thước ảnh:** **`4000 x 2901 px`** (Định dạng Nằm Ngang Landscape, độ phân giải 4K siêu nét).
- **Quy chuẩn đồ họa:** Chuẩn màu Xanh Brand Flora (`#0033a3`, `#071a3d`), **bỏ hoàn toàn tất cả border màu**, nền sáng sạch sẽ như ấn bản PDF.
- **Mã nguồn HTML:** [`affiliate-booklet/showcase-landscape.html`](file:///d:/GITHUB_SPACE/FLORA/affiliate-booklet/showcase-landscape.html).
