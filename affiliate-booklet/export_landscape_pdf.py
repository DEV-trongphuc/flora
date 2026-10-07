# -*- coding: utf-8 -*-
import asyncio
import os
import shutil
import sys
from playwright.async_api import async_playwright
import pymupdf as fitz

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

async def generate_landscape_pdf():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    html_path = os.path.join(base_dir, "showcase-landscape.html").replace("\\", "/")
    highres_img = os.path.join(base_dir, "TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png")
    
    # 2 output PDF destinations: in affiliate-booklet and root directory
    output_pdf_booklet = os.path.join(base_dir, "TONG_QUAN_TINH_NANG_HE_THONG_FLORA_LANDSCAPE.pdf")
    output_pdf_root = os.path.join(os.path.dirname(base_dir), "TONG_QUAN_TINH_NANG_HE_THONG_FLORA_LANDSCAPE.pdf")

    print("🚀 Đang xuất file PDF Nằm Ngang (Landscape) chuẩn Xanh Brand Flora...")
    print(f"📄 Nguồn ảnh: {highres_img}")
    print(f"📑 File đích: {output_pdf_booklet}")

    # Method 1: Using PyMuPDF with the ultra-high definition (4000x2901) raster
    # Exact proportional landscape dimensions in points
    # Let width = 1190.55 pt (A3 Landscape) or 841.89 pt (A4 Landscape)
    # Using A3 Landscape width = 1190.55 pt for ultra-luxurious, expansive presentation
    width_pts = 1190.55
    height_pts = width_pts * (2901.0 / 4000.0) # exact aspect ratio = ~863.66 pt

    pdf_doc = fitz.open()
    pdf_page = pdf_doc.new_page(width=width_pts, height=height_pts)
    page_rect = fitz.Rect(0, 0, width_pts, height_pts)
    pdf_page.insert_image(page_rect, filename=highres_img)

    for target in [output_pdf_booklet, output_pdf_root]:
        try:
            pdf_doc.save(target, deflate=True)
            size_mb = os.path.getsize(target) / (1024 * 1024)
            print(f"✔ Đã tạo thành công PDF: {target} ({size_mb:.2f} MB)")
        except Exception as e:
            print(f"⚠ Lỗi khi lưu {target}: {e}")
    pdf_doc.close()

    print("=================================================================")
    print("🎉 XUẤT FILE PDF TỔNG QUAN TÍNH NĂNG LANDSCAPE HOÀN TẤT!")
    print("=================================================================")

if __name__ == "__main__":
    asyncio.run(generate_landscape_pdf())
