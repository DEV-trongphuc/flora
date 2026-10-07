#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
=============================================================================
FLORA DENTAL CLINIC - AUTOMATED PRODUCTION DEPLOYMENT SCRIPT
=============================================================================
Tự động sao lưu, upload mã nguồn Theme Flora và làm mới Cache trên VPS.

Hướng dẫn sử dụng:
    python deploy.py
hoặc:
    ./deploy.ps1 (PowerShell)
    deploy.bat (Windows CMD)
=============================================================================
"""

import os
import sys
import time
import tarfile
import paramiko
from datetime import datetime

# Cấu hình UTF-8 cho console Windows
if sys.platform == 'win32':
    try:
        sys.stdout.reconfigure(encoding='utf-8')
        sys.stderr.reconfigure(encoding='utf-8')
    except Exception:
        pass

# CẤU HÌNH KẾT NỐI SERVER
SERVER_HOST = os.getenv('FLORA_SSH_HOST', '160.187.146.19')
SERVER_PORT = int(os.getenv('FLORA_SSH_PORT', 22))
SERVER_USER = os.getenv('FLORA_SSH_USER', 'root')
SERVER_PASS = os.getenv('FLORA_SSH_PASS', 'hN@011189')

# CÁC ĐƯỜNG DẪN TRÊN SERVER CẦN DEPLOY
TARGET_THEMES = [
    {
        'domain': 'nhakhoaflora.com',
        'theme_path': '/usr/local/lsws/nhakhoaflora.com/html/wp-content/themes/flora-theme',
        'wp_path': '/usr/local/lsws/nhakhoaflora.com/html',
        'owner': 'nhakhoaflorawpcom:nhakhoaflorawpcom'
    },
    {
        'domain': 'nhakhoaflora.site',
        'theme_path': '/usr/local/lsws/nhakhoaflora.site/html/wp-content/themes/flora-theme',
        'wp_path': '/usr/local/lsws/nhakhoaflora.site/html',
        'owner': 'nhakhoaflorawpsite:nhakhoaflorawpsite'
    }
]

LOCAL_THEME_DIR = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'flora-theme')
LOCAL_ARCHIVE = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'flora-theme-deploy.tar.gz')
REMOTE_TEMP_ARCHIVE = '/tmp/flora-theme-deploy.tar.gz'

def log(msg, level="INFO"):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    prefix = {
        "INFO": "\033[94m[*]\033[0m",
        "OK": "\033[92m[✓]\033[0m",
        "WARN": "\033[93m[!]\033[0m",
        "ERR": "\033[91m[✗]\033[0m"
    }.get(level, "[*]")
    print(f"{prefix} [{timestamp}] {msg}")

def run_remote_command(ssh, cmd):
    stdin, stdout, stderr = ssh.exec_command(cmd)
    out = stdout.read().decode('utf-8', errors='ignore').strip()
    err = stderr.read().decode('utf-8', errors='ignore').strip()
    return out, err

def create_local_tar_archive(source_dir, output_file):
    """Nén thư mục theme thành file tar.gz để upload siêu tốc"""
    log(f"Đang đóng gói Theme thành file nén: {os.path.basename(output_file)} ...")
    with tarfile.open(output_file, "w:gz") as tar:
        for root, dirs, files in os.walk(source_dir):
            # Bỏ qua các thư mục không cần thiết
            dirs[:] = [d for d in dirs if d not in ['.git', '.svn', 'node_modules', '__pycache__', '.idea', '.vscode']]
            for file in files:
                if file in ['.DS_Store', 'Thumbs.db']:
                    continue
                full_path = os.path.join(root, file)
                rel_path = os.path.relpath(full_path, source_dir)
                tar.add(full_path, arcname=rel_path)
    size_mb = os.path.getsize(output_file) / (1024 * 1024)
    log(f"Đã đóng gói xong! Dung lượng file nén: {size_mb:.2f} MB", "OK")

def deploy():
    print("\n" + "="*60)
    print("       FLORA DENTAL CLINIC - ULTRA-FAST DEPLOYMENT       ")
    print("="*60)

    if not os.path.exists(LOCAL_THEME_DIR):
        log(f"Không tìm thấy thư mục theme tại: {LOCAL_THEME_DIR}", "ERR")
        sys.exit(1)

    # 1. Đóng gói theme thành file .tar.gz
    create_local_tar_archive(LOCAL_THEME_DIR, LOCAL_ARCHIVE)

    log(f"Đang kết nối tới VPS: {SERVER_USER}@{SERVER_HOST}:{SERVER_PORT} ...")
    
    ssh = paramiko.SSHClient()
    ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    
    try:
        ssh.connect(SERVER_HOST, port=SERVER_PORT, username=SERVER_USER, password=SERVER_PASS, timeout=15)
        log("Kết nối SSH thành công!", "OK")
    except Exception as e:
        log(f"Lỗi kết nối SSH: {e}", "ERR")
        sys.exit(1)

    sftp = ssh.open_sftp()
    
    # 2. Upload file nén lên /tmp server
    log(f"Đang upload gói nén lên VPS -> {REMOTE_TEMP_ARCHIVE} ...")
    sftp.put(LOCAL_ARCHIVE, REMOTE_TEMP_ARCHIVE)
    log("Upload gói nén lên VPS hoàn tất!", "OK")

    # 3. Tạo thư mục sao lưu trên server
    backup_root = "/root/flora_backups"
    run_remote_command(ssh, f"mkdir -p {backup_root}")
    ts = datetime.now().strftime("%Y%m%d_%H%M%S")

    for target in TARGET_THEMES:
        domain = target['domain']
        theme_path = target['theme_path']
        wp_path = target['wp_path']
        owner = target['owner']

        print("\n" + "-"*50)
        log(f"Bắt đầu Deploy cho domain: {domain}")
        print("-"*50)

        # Kiểm tra xem đường dẫn WordPress có tồn tại không
        check_out, _ = run_remote_command(ssh, f"[ -d {wp_path} ] && echo 'EXISTS' || echo 'NOT_FOUND'")
        if 'EXISTS' not in check_out:
            log(f"Không tìm thấy thư mục WordPress tại {wp_path}, bỏ qua.", "WARN")
            continue

        # 4. Tạo bản sao lưu theme cũ
        backup_file = f"{backup_root}/flora_theme_{domain}_{ts}.tar.gz"
        log(f"Sao lưu theme hiện tại -> {backup_file} ...")
        run_remote_command(ssh, f"[ -d {theme_path} ] && tar -czf {backup_file} -C {theme_path}/.. flora-theme 2>/dev/null")
        log("Sao lưu hoàn tất.", "OK")

        # 5. Giải nén theme mới trực tiếp vào theme_path
        run_remote_command(ssh, f"mkdir -p {theme_path}")
        log(f"Giải nén mã nguồn mới vào {theme_path} ...")
        run_remote_command(ssh, f"tar -xzf {REMOTE_TEMP_ARCHIVE} -C {theme_path}")
        log("Giải nén mã nguồn thành công!", "OK")

        # 6. Phân quyền chính xác cho người dùng web server
        log(f"Cập nhật phân quyền user {owner} và chmod 755/644...")
        run_remote_command(ssh, f"chown -R {owner} {theme_path}")
        run_remote_command(ssh, f"find {theme_path} -type d -exec chmod 755 {{}} +")
        run_remote_command(ssh, f"find {theme_path} -type f -exec chmod 644 {{}} +")
        log("Phân quyền hoàn tất.", "OK")

        # 6b. Đảm bảo trang 'goi-dich-vu' được tạo trong WordPress với template Landing Page
        page_check, _ = run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post list --post_type=page --name=goi-dich-vu --format=ids 2>/dev/null")
        if not page_check.strip():
            log(f"Đang tạo trang WordPress '/goi-dich-vu/' với template Landing Page...")
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post create --post_type=page --post_title='Gói Chăm Sóc Răng Miệng' --post_name='goi-dich-vu' --post_status=publish --page_template='page-templates/template-goi-dich-vu.php' 2>/dev/null")
            log(f"Đã tạo trang '/goi-dich-vu/' thành công trên {domain} (không có trong menu)!", "OK")
        else:
            page_id = page_check.strip().split()[0]
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post meta update {page_id} _wp_page_template 'page-templates/template-goi-dich-vu.php' 2>/dev/null")
            log(f"Trang '/goi-dich-vu/' (ID: {page_id}) đã được gán template trên {domain}!", "OK")

        # 6c. Đảm bảo trang 'warranty' được tạo trong WordPress với template Tra cứu bảo hành
        warranty_check, _ = run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post list --post_type=page --name=warranty --format=ids 2>/dev/null")
        if not warranty_check.strip():
            log(f"Đang tạo trang WordPress '/warranty/' với template Tra Cứu Bảo Hành & Check Chính Hãng...")
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post create --post_type=page --post_title='Tra Cứu Bảo Hành & Kiểm Tra Chính Hãng' --post_name='warranty' --post_status=publish --page_template='page-templates/template-warranty.php' 2>/dev/null")
            log(f"Đã tạo trang '/warranty/' thành công trên {domain}!", "OK")
        else:
            w_page_id = warranty_check.strip().split()[0]
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post meta update {w_page_id} _wp_page_template 'page-templates/template-warranty.php' 2>/dev/null")
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post update {w_page_id} --post_status=publish 2>/dev/null")
            log(f"Trang '/warranty/' (ID: {w_page_id}) đã được gán template trên {domain}!", "OK")

        # 6d. Đảm bảo trang 'affiliate-portal' được tạo trong WordPress cho KOL Dashboard
        portal_check, _ = run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post list --post_type=page --name=affiliate-portal --format=ids 2>/dev/null")
        if not portal_check.strip():
            log(f"Đang tạo trang WordPress '/affiliate-portal/' với template Public Affiliate Portal...")
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post create --post_type=page --post_title='Cổng Thông Tin Đối Tác & KOL' --post_name='affiliate-portal' --post_status=publish --page_template='page-templates/template-affiliate-portal.php' 2>/dev/null")
            log(f"Đã tạo trang '/affiliate-portal/' thành công trên {domain}!", "OK")
        else:
            p_page_id = portal_check.strip().split()[0]
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post meta update {p_page_id} _wp_page_template 'page-templates/template-affiliate-portal.php' 2>/dev/null")
            run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} post update {p_page_id} --post_status=publish 2>/dev/null")
            log(f"Trang '/affiliate-portal/' (ID: {p_page_id}) đã được gán template trên {domain}!", "OK")

        # Flush rewrite rules
        run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} rewrite flush 2>/dev/null || true")

        # 7. Kích hoạt và Làm mới Cache
        log("Xóa LiteSpeed Cache & Object Cache...")
        run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} litespeed-purge all 2>/dev/null || true")
        run_remote_command(ssh, f"/usr/local/bin/wp --allow-root --path={wp_path} cache flush 2>/dev/null || true")
        log(f"Làm mới Cache cho {domain} hoàn tất.", "OK")

    # 8. Dọn dẹp file tạm
    run_remote_command(ssh, f"rm -f {REMOTE_TEMP_ARCHIVE}")
    if os.path.exists(LOCAL_ARCHIVE):
        try:
            os.remove(LOCAL_ARCHIVE)
        except Exception:
            pass

    # 9. Khởi động lại LiteSpeed Web Server an toàn
    log("Khởi động lại tiến trình LiteSpeed PHP...")
    run_remote_command(ssh, "touch /tmp/lshttpd/.rtreport 2>/dev/null; systemctl reload lsws 2>/dev/null || /usr/local/lsws/bin/lswsctrl restart")
    
    sftp.close()
    ssh.close()

    print("\n" + "="*60)
    log("🎉 TOÀN BỘ QUÁ TRÌNH DEPLOY LÊN SERVER ĐÃ HOÀN TẤT THÀNH CÔNG!", "OK")
    print("="*60 + "\n")

if __name__ == '__main__':
    deploy()
