import asyncio
import os
from playwright.async_api import async_playwright

async def render_page_7():
    base_dir = os.path.abspath('affiliate-booklet')
    html_path = os.path.join(base_dir, 'index.html').replace('\\', '/')
    temp_dir = os.path.join(base_dir, 'scratch_pdf_pages_portrait')

    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        context = await browser.new_context(
            viewport={'width': 1000, 'height': 1414},
            device_scale_factor=2.0
        )
        page = await context.new_page()
        await page.goto(f'file:///{html_path}', wait_until='networkidle')
        await page.wait_for_timeout(1000)

        # Hide top nav header
        await page.evaluate("""
            const header = document.querySelector('.booklet-nav-header');
            if (header) header.style.display = 'none';
            const viewport = document.querySelector('.booklet-viewport');
            if (viewport) {
                viewport.style.padding = '0';
                viewport.style.gap = '0';
            }
        """)

        page_id = 'page-7'
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
        await page.wait_for_timeout(300)

        page_el = await page.query_selector(f'#{page_id}')
        img_path = os.path.join(temp_dir, 'page_07.png')
        await page_el.screenshot(path=img_path)
        print('Rendered:', img_path)
        await browser.close()

if __name__ == '__main__':
    asyncio.run(render_page_7())
