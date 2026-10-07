# -*- coding: utf-8 -*-
"""
NHA KHOA FLORA - EXPORT PARTNER HANDBOOK (CẨM NANG ĐỐI TÁC 10 TRANG) TO HIGH-RES PDF
Output: A4 Portrait PDF (210mm x 297mm = 595.28 pt x 841.89 pt)
"""

import asyncio
import os
import sys
from playwright.async_api import async_playwright
import pymupdf as fitz

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

async def generate_partner_pdf():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    html_path = os.path.join(base_dir, "partner-handbook.html").replace("\\", "/")
    output_pdf = os.path.join(base_dir, "NHA_KHOA_FLORA_CAM_NANG_CHO_DOI_TAC_PORTRAIT.pdf")
    temp_dir = os.path.join(base_dir, "scratch_pdf_pages_partner")
    os.makedirs(temp_dir, exist_ok=True)

    print("=================================================================")
    print("🚀 NHA KHOA FLORA - XUẤT CẨM NANG DÀNH CHO ĐỐI TÁC (14 TRANG A4 DỌC)")
    print(f"📄 Nguồn HTML: {html_path}")
    print(f"📑 File đích PDF: {output_pdf}")
    print("=================================================================")

    page_images = []
    total_pages = 14

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={"width": 1000, "height": 1414},
            device_scale_factor=2.0
        )
        page = await context.new_page()

        print("[*] Đang tải trang HTML Đối tác vào Playwright Chromium...")
        await page.goto(f"file:///{html_path}", wait_until="networkidle")
        await page.wait_for_timeout(2000)

        # Hide top nav header and controls dock
        await page.evaluate("""
            const header = document.querySelector('.booklet-nav-header');
            if (header) header.style.display = 'none';
            const viewport = document.querySelector('.booklet-viewport');
            if (viewport) {
                viewport.style.padding = '0';
                viewport.style.gap = '0';
            }
        """)

        for i in range(1, total_pages + 1):
            page_id = f"page-{i}"
            print(f"[*] Đang chụp Trang {i:02d} / {total_pages:02d} (A4 Dọc)...")

            await page.evaluate(f"""
                const targetId = '{page_id}';
                const pages = document.querySelectorAll('.booklet-page');
                pages.forEach(p => {{
                    if (p.id === targetId) {{
                        p.style.setProperty('display', 'flex', 'important');
                        p.style.setProperty('visibility', 'visible', 'important');
                        p.style.setProperty('opacity', '1', 'important');
                        p.style.setProperty('width', '1000px', 'important');
                        p.style.setProperty('height', '1414px', 'important');
                        p.style.setProperty('max-width', '1000px', 'important');
                        p.style.setProperty('max-height', '1414px', 'important');
                        p.style.setProperty('margin', '0', 'important');
                        p.style.setProperty('border', 'none', 'important');
                        p.style.setProperty('border-radius', '0', 'important');
                        p.style.setProperty('box-shadow', 'none', 'important');
                    }} else {{
                        p.style.setProperty('display', 'none', 'important');
                    }}
                }});
            """)
            await page.wait_for_timeout(400)

            page_el = await page.query_selector(f"#{page_id}")
            img_path = os.path.join(temp_dir, f"partner_page_{i:02d}.png")
            if page_el:
                await page_el.screenshot(path=img_path, timeout=15000)
            else:
                await page.screenshot(path=img_path)

            page_images.append(img_path)
            print(f"    ✔ Đã render ảnh: {img_path}")

        await browser.close()

    print("\n📦 Đang đóng gói 10 trang ảnh Đối tác thành PDF Dọc chuẩn A4...")
    width_pts = 595.28
    height_pts = 841.89
    page_rect = fitz.Rect(0, 0, width_pts, height_pts)

    pdf_doc = fitz.open()
    for idx, img_path in enumerate(page_images):
        pdf_page = pdf_doc.new_page(width=width_pts, height=height_pts)
        pdf_page.insert_image(page_rect, filename=img_path)
        print(f"    [+] Trang PDF {idx + 1:02d}: Chèn ảnh thành công")

    output_pdf_main = os.path.join(base_dir, "NHA_KHOA_FLORA_BO_DOI_TAC_10_TRANG.pdf")
    output_pdf_alt = os.path.join(base_dir, "NHA_KHOA_FLORA_CAM_NANG_CHO_DOI_TAC_PORTRAIT.pdf")

    for target in [output_pdf_main, output_pdf_alt]:
        try:
            pdf_doc.save(target, deflate=True)
            file_size_mb = os.path.getsize(target) / (1024 * 1024)
            print(f"✔ Đã lưu thành công: {target} ({file_size_mb:.2f} MB)")
        except Exception as e:
            print(f"⚠ Không thể lưu {target}: {e}")
    pdf_doc.close()

if __name__ == "__main__":
    asyncio.run(generate_partner_pdf())
