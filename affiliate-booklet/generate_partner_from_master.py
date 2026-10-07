# -*- coding: utf-8 -*-
"""
Generate partner-handbook.html directly from index.html:
- Same exact CSS stylesheet (booklet-portrait.css), same classes, same fonts, same visual perfection.
- Omits only the internal accounting page (page-10).
- Preserves full Customer Experience (page-6 booking + page-7 VietQR payment).
- Renumbers pages 1 to 14.
- Adjusts Cover title and Table of Contents specifically for Partners.
"""

import re
import os
import sys

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

base_dir = os.path.dirname(os.path.abspath(__file__))
index_path = os.path.join(base_dir, "index.html")
partner_path = os.path.join(base_dir, "partner-handbook.html")

with open(index_path, "r", encoding="utf-8") as f:
    master_html = f.read()

# Split master_html into head, pages, and footer/scripts
page_regex = re.compile(r'(<article class="booklet-page[^"]*" id="page-\d+"[^>]*>.*?</article>)', re.DOTALL)
pages = page_regex.findall(master_html)
print(f"Found {len(pages)} pages in index.html")

# The pages to include for the Partner Handbook (14 pages):
# index.html pages:
# page 0: page-1 (Bìa)
# page 1: page-2 (Mục lục)
# page 2: page-3 (Chính sách hoa hồng 10%)
# page 3: page-4 (Onboarding)
# page 4: page-5 (Welcome Email)
# page 5: page-6 (Trải nghiệm khách hàng: Đặt mua gói)
# page 6: page-7 (Cổng thanh toán VietQR ACB)
# page 7: page-8 (Cổng thông tin đối tác Portal)
# page 8: page-9 (Quy trình rút tiền)
# [SKIP page 9: page-10 (Nghiệp vụ kế toán nội bộ)]
# page 10: page-11 (Phiếu quyết toán & lịch sử chi trả) -> becomes page-10
# page 11: page-12 (Công cụ tra cứu) -> becomes page-11
# page 12: page-13 (Media Kit) -> becomes page-12
# page 13: page-14 (Hỗ trợ 24/7) -> becomes page-13
# page 14: page-15 (Bìa sau) -> becomes page-14

selected_indices = [0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14]
total_partner_pages = len(selected_indices) # 14

partner_pages = []
for new_num, old_idx in enumerate(selected_indices, start=1):
    p_content = pages[old_idx]
    
    # Replace id="page-X" with id="page-new_num" and data-page="new_num"
    p_content = re.sub(r'id="page-\d+"', f'id="page-{new_num}"', p_content)
    p_content = re.sub(r'data-page="\d+"', f'data-page="{new_num}"', p_content)
    
    # Replace TRANG XX / 15 with TRANG new_num:02d / 14
    p_content = re.sub(r'TRANG\s+\d+\s*/\s*15', f'TRANG {new_num:02d} / {total_partner_pages:02d}', p_content)
    
    # Specific page customizations:
    if new_num == 1:
        # Cover page title adjustment
        p_content = p_content.replace(
            "CẨM NANG VẬN HÀNH<br>\n            <span style=\"color: #fde047;\">HỆ THỐNG ĐỐI TÁC & VOUCHER</span>",
            "CẨM NANG HỢP TÁC<br>\n            <span style=\"color: #fde047;\">DÀNH CHO ĐỐI TÁC & KOL</span>"
        )
        p_content = p_content.replace(
            "Tài liệu quy trình chuẩn (SOP) dành cho <strong>Đối Tác / KOL</strong>, <strong>Khách Hàng</strong>, <strong>Đội ngũ Lễ Tân - CSKH</strong> và <strong>Bộ phận Kế Toán</strong> Nha Khoa Flora. Hướng dẫn chi tiết từ lúc khởi tạo tài khoản, cơ chế tracking, trải nghiệm khách hàng đến quy trình đối soát và tự động hóa chi trả hoa hồng.",
            "Tài liệu hướng dẫn toàn diện dành riêng cho <strong>Đối Tác Tiếp Thị, Bác Sĩ Liên Kết & KOL</strong> của Nha Khoa Flora: Chính sách hoa hồng 10% thực thu, cách hướng dẫn khách đặt mua voucher, sử dụng Cổng Đối Tác và quy trình rút tiền Napas 24/7 tức thì."
        )
    
    if new_num == 2:
        # Update TOC to 14 pages
        # Update title & ranges
        p_content = p_content.replace(
            "TRANG 01 — 08",
            "TRANG 01 — 07"
        )
        p_content = p_content.replace(
            "TRANG 09 — 15",
            "TRANG 08 — 14"
        )
        # Fix numbers in TOC tiles
        # Tile 07 was VietQR, 08 was Portal, etc.
        # Let's rebuild the TOC grid cleanly in new_num == 2
        toc_replacement = """        <!-- MỤC LỤC: PHẦN 1: DÀNH CHO ĐỐI TÁC & KHÁCH HÀNG (TRANG 01 — 07) -->
        <div>
          <div class="toc-section-bar">
            <span class="title"><i class="fa-solid fa-layer-group" style="margin-right: 8px; color: #0033a3;"></i> PHẦN 1: CHÍNH SÁCH HOA HỒNG & TRẢI NGHIỆM KHÁCH HÀNG</span>
            <span class="range">TRANG 01 &mdash; 07</span>
          </div>
          <div class="toc-items-grid">
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">01</div>
                <div>
                  <div class="toc-page-name">Bìa Chính: Nhận Diện Đối Tác Chiến Lược Flora 2026</div>
                  <div class="toc-page-sub">Tiêu chuẩn nha khoa Thụy Sĩ</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">02</div>
                <div>
                  <div class="toc-page-name">Mục Lục & Bản Đồ Quy Trình Hợp Tác 360°</div>
                  <div class="toc-page-sub">3 bước liên thông đối tác - khách - phòng khám</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">03</div>
                <div>
                  <div class="toc-page-name">Chính Sách Hoa Hồng 10% Cho 2 Gói Dịch Vụ</div>
                  <div class="toc-page-sub">Gói Care Plus (250k) &amp; Flora White Up (180k)</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">04</div>
                <div>
                  <div class="toc-page-name">Khởi Tạo &amp; Truy Cập Cổng Đối Tác (Onboarding)</div>
                  <div class="toc-page-sub">nhakhoaflora.com/doi-tac/ kích hoạt 3s</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">05</div>
                <div>
                  <div class="toc-page-name">Giao Diện Email Kích Hoạt Tài Khoản (Welcome Email)</div>
                  <div class="toc-page-sub">Nhận mật khẩu và hướng dẫn đăng nhập</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">06</div>
                <div>
                  <div class="toc-page-name">Trải Nghiệm Khách Hàng: Đặt Mua Gói &amp; Điền Voucher</div>
                  <div class="toc-page-sub">Hướng dẫn khách nhập mã voucher để nhận ưu đãi</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile" style="grid-column: span 2;">
              <div class="toc-item-left">
                <div class="toc-page-num">07</div>
                <div>
                  <div class="toc-page-name">Cổng Thanh Toán VietQR ACB &amp; Tiếp Nhận Đơn Hàng Tự Động</div>
                  <div class="toc-page-sub">Quét mã VietQR tự động khớp lệnh &amp; Zalo Bot thông báo</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- MỤC LỤC: PHẦN 2: QUẢN TRỊ PORTAL & RÚT TIỀN HOA HỒNG (TRANG 08 — 14) -->
        <div>
          <div class="toc-section-bar">
            <span class="title"><i class="fa-solid fa-gears" style="margin-right: 8px; color: #0a1931;"></i> PHẦN 2: QUẢN TRỊ PORTAL, RÚT TIỀN &amp; MEDIA KIT</span>
            <span class="range">TRANG 08 &mdash; 14</span>
          </div>
          <div class="toc-items-grid" style="margin-bottom: 0;">
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">08</div>
                <div>
                  <div class="toc-page-name">Cổng Thông Tin Đối Tác (Portal) &amp; Bảng Kê Doanh Thu</div>
                  <div class="toc-page-sub">Dashboard thống kê realtime, link ref &amp; mã QR riêng</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">09</div>
                <div>
                  <div class="toc-page-name">Quy Trình Rút Tiền Hoa Hồng &amp; Đổi STK Ngân Hàng</div>
                  <div class="toc-page-sub">Tạo lệnh rút min 100.000 VNĐ, đổi STK tức thì</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">10</div>
                <div>
                  <div class="toc-page-name">Phiếu Quyết Toán &amp; Lịch Sử Chi Trả Hoa Hồng Đối Tác</div>
                  <div class="toc-page-sub">Bảng kê chi trả ngân hàng thật &amp; Zalo OA báo tiền về</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">11</div>
                <div>
                  <div class="toc-page-name">Công Cụ Tra Cứu Trạng Thái Đơn Hàng Trực Tiếp</div>
                  <div class="toc-page-sub">Kiểm tra tiến độ đơn hàng trên website 24/7</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">12</div>
                <div>
                  <div class="toc-page-name">Kho Tài Nguyên Tiếp Thị (Media Kit &amp; Content Mẫu)</div>
                  <div class="toc-page-sub">Banner, bài viết mẫu và tài liệu tư vấn khách hàng</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile">
              <div class="toc-item-left">
                <div class="toc-page-num">13</div>
                <div>
                  <div class="toc-page-name">Cẩm Nang Hỗ Trợ 24/7 &amp; Kênh Hotline Đối Tác Độc Quyền</div>
                  <div class="toc-page-sub">Chăm sóc 1:1, giải đáp thắc mắc và hỗ trợ kỹ thuật</div>
                </div>
              </div>
            </div>
            <div class="toc-item-tile" style="grid-column: span 2;">
              <div class="toc-item-left">
                <div class="toc-page-num">14</div>
                <div>
                  <div class="toc-page-name">Bìa Sau: Mạng Lưới Đối Tác Chiến Lược Flora 2026</div>
                  <div class="toc-page-sub">Cam kết đồng hành và phát triển bền vững cùng Đối tác</div>
                </div>
              </div>
            </div>
          </div>
        </div>"""
        
        # Replace the entire TOC section inside Page 2
        p_content = re.sub(
            r'<!-- MỤC LỤC: PHẦN 1.*?<!-- ==========================================================================',
            toc_replacement + "\n      </div>\n\n      <footer class=\"page-footer\">\n        <div class=\"footer-left\">NHA KHOA FLORA • MỤC LỤC &amp; KIẾN TRÚC HỆ SINH THÁI ĐỐI TÁC 360°</div>\n        <div class=\"footer-page-num\">TRANG 02 / 14</div>\n      </footer>\n    </article>\n\n    <!-- ==========================================================================",
            p_content,
            flags=re.DOTALL
        )

    partner_pages.append(p_content)

# Get the HTML wrapper from index.html (before page-1 and after last page)
pre_pages = master_html[:master_html.find('<article class="booklet-page')]
post_pages = master_html[master_html.rfind('</article>') + len('</article>'):]

# In pre_pages, update title and navigation bar
pre_pages = pre_pages.replace(
    "<title>Cẩm Nang Vận Hành Affiliate & Voucher | Nha Khoa Flora</title>",
    "<title>Cẩm Nang Dành Cho Đối Tác & KOL | Nha Khoa Flora</title>"
)
pre_pages = pre_pages.replace(
    "CẨM NANG VẬN HÀNH AFFILIATE &amp; VOUCHER 2026",
    "CẨM NANG DÀNH CHO ĐỐI TÁC &amp; KOL 2026"
)
pre_pages = pre_pages.replace(
    "Trang <span id=\"currentPageNum\">01</span> / 15",
    "Trang <span id=\"currentPageNum\">01</span> / 14"
)

# Update javascript in post_pages to have 14 pages
post_pages = post_pages.replace("totalPages = 15", "totalPages = 14")
post_pages = post_pages.replace("total_pages = 15", "total_pages = 14")

# Combine everything
new_partner_html = pre_pages + "\n\n".join(partner_pages) + post_pages

with open(partner_path, "w", encoding="utf-8") as f:
    f.write(new_partner_html)

print(f"✔ Đã tạo thành công partner-handbook.html ({len(partner_pages)} trang) với 100% style gốc từ index.html!")
