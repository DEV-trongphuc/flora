import asyncio
import os
import shutil
import sys
from playwright.async_api import async_playwright

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

async def render_showcase_png():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    html_path = os.path.join(base_dir, "showcase-features.html").replace("\\", "/")
    output_png = os.path.join(base_dir, "TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png")
    root_png = os.path.join(os.path.dirname(base_dir), "TONG_QUAN_TINH_NANG_HE_THONG_FLORA.png")

    print("🚀 Đang render 1 ảnh PNG tổng quan tính năng hệ thống Flora...")
    print(f"📄 Nguồn: {html_path}")
    print(f"🖼️ Đích: {output_png}")

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        # 2500px width with device scale factor 1.5 or 2.0
        context = await browser.new_context(
            viewport={"width": 2500, "height": 3200},
            device_scale_factor=1.5
        )
        page = await context.new_page()
        await page.goto(f"file:///{html_path}", wait_until="networkidle")
        await page.wait_for_timeout(2500)

        poster_elem = await page.query_selector("#showcase-poster")
        if poster_elem:
            await poster_elem.screenshot(path=output_png, type="png")
            print(f"✅ Đã chụp thành công: {output_png} ({os.path.getsize(output_png):,} bytes)")
        else:
            await page.screenshot(path=output_png, full_page=True, type="png")
            print(f"✅ Đã chụp full page: {output_png}")

        await browser.close()

    # Sao chép ra thư mục gốc
    shutil.copy2(output_png, root_png)
    print(f"✅ Đã copy ra thư mục gốc: {root_png}")

if __name__ == "__main__":
    asyncio.run(render_showcase_png())
