<?php
/**
 * NHA KHOA FLORA - ALL-IN-ONE MASTER ZALO BOT WEBHOOK ENDPOINT
 * URL: https://[domain]/wp-content/themes/flora-theme/api/zalo-webhook.php
 * 
 * Hỗ trợ đồng thời:
 * 1. Zalo Bot Platform Webhook 2 chiều (Lệnh /chatid, /pending, /duyet, /tuchoi, /report, /test)
 * 2. Tự động nhận diện & lưu Chat ID của nhóm Zalo CSKH / Tư Vấn Viên
 * 3. Duyệt hoặc từ chối đơn hàng gói khám trực tiếp ngay trong nhóm Zalo
 * 4. Google Sheets / Facebook Lead Ads Webhook (đồng bộ data tiếp thị nếu có)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Bot-Api-Secret-Token, X-Secret-Token, X-Zalo-Secret');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 1. Load WordPress Environment
$wp_load_path = '';
$dirs = [
    __DIR__ . '/../../../../wp-load.php',
    __DIR__ . '/../../../wp-load.php',
    $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php'
];

foreach ($dirs as $dir) {
    if (file_exists($dir)) {
        $wp_load_path = $dir;
        break;
    }
}

if ($wp_load_path) {
    require_once $wp_load_path;
} else {
    echo json_encode(['success' => false, 'message' => 'WordPress environment not found']);
    exit;
}

// 2. Secret Token kiểm tra bảo mật
$EXPECTED_SECRET = get_option('flora_zalo_secret_token', 'flora2026');
$incomingSecret = $_SERVER['HTTP_X_BOT_API_SECRET_TOKEN'] 
    ?? $_SERVER['HTTP_X_ZALO_SECRET'] 
    ?? $_SERVER['HTTP_X_SECRET_TOKEN'] 
    ?? $_GET['token'] 
    ?? $_GET['secret'] 
    ?? '';

// Thiết lập nhanh cấu hình từ URL (nếu có secret hợp lệ)
if ($incomingSecret === $EXPECTED_SECRET) {
    if (!empty($_GET['set_chat_id'])) {
        update_option('flora_zalo_group_chat_id', sanitize_text_field($_GET['set_chat_id']));
    }
    if (!empty($_GET['set_bot_token'])) {
        update_option('flora_zalo_bot_token', sanitize_text_field($_GET['set_bot_token']));
    }
}

// 3. Handle GET Request (Status Diagnostics & Webhook Verification)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $challenge = $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? '';
    if (!empty($challenge)) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $challenge;
        exit;
    }

    global $wpdb;
    $table_name = function_exists('flora_get_orders_table_name') ? flora_get_orders_table_name() : $wpdb->prefix . 'flora_orders';
    $pending_count = 0;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
        $pending_count = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status = 'pending_confirmation'");
    }

    $botToken = get_option('flora_zalo_bot_token', '');
    $tokenMasked = !empty($botToken) ? (substr($botToken, 0, 8) . '...' . substr($botToken, -6)) : 'Chưa cấu hình Bot Token';
    $groupId = get_option('flora_zalo_group_chat_id', 'Chưa kết nối Group ID (Thêm bot vào nhóm và gõ /chatid)');

    echo json_encode([
        'status'                 => 'active',
        'system'                 => 'Nha Khoa Flora - Master Zalo Bot Webhook Engine',
        'bank_account'           => '77779268 - CONG TY CO PHAN FLORA DENTAL CARE (ACB - CN HOA HUNG)',
        'zalo_bot_token'         => $tokenMasked,
        'zalo_group_chat_id'     => $groupId,
        'pending_orders_count'   => $pending_count,
        'supported_bot_commands' => [
            '/chatid'  => 'Tự động lưu ID nhóm Zalo vào website Flora',
            '/pending' => 'Xem danh sách các đơn hàng đang chờ duyệt và link bill',
            '/duyet <mã>' => 'Duyệt đơn hàng và gửi email kích hoạt cho khách ngay từ Zalo',
            '/tuchoi <mã>' => 'Từ chối đơn hàng và gửi email khiếu nại',
            '/report'  => 'Báo cáo doanh thu & đơn hàng hôm nay',
            '/test'    => 'Kiểm tra đường truyền bot'
        ],
        'current_time'           => current_time('d/m/Y H:i:s')
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 4. Parse Incoming Payload
$rawInput = file_get_contents('php://input');

// Log phục vụ đối soát (tối đa 2MB)
$logFile = __DIR__ . '/zalo_webhook_log.txt';
if (file_exists($logFile) && @filesize($logFile) > 2 * 1024 * 1024) {
    @rename($logFile, __DIR__ . '/zalo_webhook_log.bak.txt');
}
@file_put_contents($logFile, date('[Y-m-d H:i:s]') . " " . $rawInput . "\n", FILE_APPEND | LOCK_EX);

$data = json_decode($rawInput, true);
if (empty($data)) {
    $data = $_POST;
}

if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'No payload received']);
    exit;
}

// =========================================================================
// PHÂN NHÁNH 1: SỰ KIỆN TỪ ZALO BOT PLATFORM (Tin nhắn / Lệnh từ Nhóm Zalo)
// =========================================================================
$isZaloBotEvent = (
    isset($data['message']['text']) || 
    isset($data['result']['message']['text']) ||
    isset($data['event_name'])
);

if ($isZaloBotEvent) {
    $chatId = $data['message']['chat']['id'] ?? $data['result']['message']['chat']['id'] ?? $data['sender']['id'] ?? $data['message']['from']['id'] ?? '';
    $text   = trim($data['message']['text'] ?? $data['result']['message']['text'] ?? '');

    if (!empty($chatId)) {
        // Tự động lưu ID nhóm nếu tin nhắn đến từ Group
        $chatType = $data['message']['chat']['type'] ?? $data['result']['message']['chat']['type'] ?? '';
        if ($chatType === 'group' || $chatType === 'supergroup' || strpos($chatId, 'zgr-') === 0 || strpos($chatId, '-') === 0) {
            update_option('flora_zalo_group_chat_id', $chatId);
        }

        // Trích xuất thông tin người gửi tin nhắn (Tư vấn viên Zalo)
        $senderName = $data['message']['from']['name'] 
            ?? $data['message']['from']['display_name'] 
            ?? $data['sender']['name'] 
            ?? 'Tư vấn viên';
        $senderId = $data['message']['from']['id'] ?? $data['sender']['id'] ?? '';
        $senderLabel = !empty($senderId) ? "{$senderName} (ID: {$senderId})" : $senderName;

        // Tự động bóc tách lệnh (bỏ qua tag @Bot)
        $slashPos = strpos($text, '/');
        if ($slashPos !== false) {
            $cleanText = trim(substr($text, $slashPos));
        } else {
            $cleanText = trim(preg_replace('/^@.+?\s+/u', '', $text));
        }
        $cleanText = preg_replace('/@[a-zA-Z0-9_\.]+/u', '', $cleanText); // loại bỏ @botname nếu gắn sau lệnh
        $textLower = mb_strtolower($cleanText, 'UTF-8');
        $parts = preg_split('/\s+/', $cleanText);
        $command = mb_strtolower($parts[0] ?? '', 'UTF-8');
        $arg1 = $parts[1] ?? '';
        $arg2 = isset($parts[2]) ? trim(implode(' ', array_slice($parts, 2))) : '';

        // Nhận diện cụm 2 từ tiếng Việt thông dụng (VD: "từ chối", "thống kê", "hướng dẫn", "duyệt lại", "duyệt kol", "từ chối kol")
        $firstTwo = mb_strtolower(($parts[0] ?? '') . ' ' . ($parts[1] ?? ''), 'UTF-8');
        if ($firstTwo === 'từ chối' || $firstTwo === '/từ chối') {
            $command = 'từ chối';
            $arg1 = $parts[2] ?? '';
            $arg2 = isset($parts[3]) ? trim(implode(' ', array_slice($parts, 3))) : '';
        } elseif ($firstTwo === 'duyệt lại' || $firstTwo === '/duyệt lại') {
            $command = 'duyệt lại';
            $arg1 = $parts[2] ?? '';
            $arg2 = isset($parts[3]) ? trim(implode(' ', array_slice($parts, 3))) : '';
        } elseif ($firstTwo === 'duyệt kol' || $firstTwo === '/duyệt kol') {
            $command = 'duyệt kol';
            $arg1 = $parts[2] ?? '';
            $arg2 = isset($parts[3]) ? trim(implode(' ', array_slice($parts, 3))) : '';
        } elseif ($firstTwo === 'từ chối kol' || $firstTwo === '/từ chối kol') {
            $command = 'từ chối kol';
            $arg1 = $parts[2] ?? '';
            $arg2 = isset($parts[3]) ? trim(implode(' ', array_slice($parts, 3))) : '';
        } elseif ($firstTwo === 'thống kê' || $firstTwo === '/thống kê') {
            $command = 'thống kê';
        } elseif ($firstTwo === 'hướng dẫn' || $firstTwo === '/hướng dẫn') {
            $command = 'hướng dẫn';
        }

        // 1. LỆNH /hello, /hi
        if (in_array($command, ['/hello', '/hi', 'hello', 'hi']) || in_array($textLower, ['/hello', '/hi', 'hello', 'hi', 'xin chào', 'chào'])) {
            $helloMsg = "👋 [ XIN CHÀO TỪ BOT NHA KHOA FLORA! ]\n"
                      . "━━━━━━━\n"
                      . "🤖 Tôi là Trợ Lý Zalo Bot tự động đối soát đơn hàng gói khám của Nha Khoa Flora.\n\n"
                      . "🟢 Trạng Thái Hoạt Động:\n"
                      . "  • Hệ thống: Trực tuyến 24/7\n"
                      . "  • Ngân hàng đối soát: ACB - 77779268\n"
                      . "  • Group Chat ID: " . $chatId . "\n"
                      . "  • Người tương tác: " . $senderLabel . "\n\n"
                      . "💡 Gõ /help để xem danh sách các lệnh hỗ trợ.";
            flora_send_zalo_bot_direct_message($helloMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'hello_replied']);
            exit;
        }

        // 2. LỆNH /help, /tools, /huongdan
        if (in_array($command, ['/help', '/tools', 'help', 'tools', '/huongdan', 'hướng dẫn']) || in_array($textLower, ['/help', '/tools', 'help', 'tools', '/huongdan', 'hướng dẫn'])) {
            $helpMsg = "🛠 [ DANH SÁCH LỆNH ZALO BOT FLORA ]\n"
                     . "━━━━━━━\n"
                     . "1. Dành Cho Đối Tác Tiếp Thị:\n"
                     . "  ▸ LINK <mã_ref> : Liên kết Zalo này với Cổng Đối Tác\n"
                     . "  ▸ SODU : Xem số dư hoa hồng khả dụng và đang chờ\n"
                     . "  ▸ LINK : Lấy lại link giới thiệu & mã voucher cá nhân\n"
                     . "  ▸ HOTRO : Kết nối chuyên viên hỗ trợ đối tác\n"
                     . "  ▸ HUY : Hủy liên kết Zalo bot\n\n"
                     . "2. Quản Lý Đơn Hàng Gói Khám (Nội Bộ):\n"
                     . "  ▸ /pending : Danh sách đơn hàng đang chờ duyệt & link bill\n"
                     . "  ▸ /duyet <mã_đơn> : Duyệt đơn ngay trên Zalo\n"
                     . "  ▸ /duyetlai <mã_đơn> : Phục hồi & duyệt lại đơn đã từ chối\n"
                     . "  ▸ /tuchoi <mã_đơn> [lý do] : Từ chối đơn\n\n"
                     . "3. Quản Lý Đối Tác / KOL (Admin):\n"
                     . "  ▸ /pendingkol : Danh sách hồ sơ đối tác đang chờ duyệt\n"
                     . "  ▸ /duyetkol <sđt_hoặc_id> : Duyệt đối tác & cấp mã REF\n"
                     . "  ▸ /tuchoikol <sđt_hoặc_id> [lý do] : Từ chối hồ sơ đối tác\n\n"
                     . "4. Báo Cáo & Cấu Hình:\n"
                     . "  ▸ /report : Báo cáo doanh thu & tổng đơn hôm nay\n"
                     . "  ▸ /chatid : Đăng ký & lưu ID nhóm nhận thông báo\n"
                     . "  ▸ /test : Gửi tin nhắn thử nghiệm kiểm tra kết nối";
            flora_send_zalo_bot_direct_message($helpMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'help_replied']);
            exit;
        }

        // 2b. LỆNH DÀNH RIÊNG CHO ĐỐI TÁC TIẾP THỊ (PARTNER BOT COMMANDS)

        // 2b. LỆNH DÀNH RIÊNG CHO ĐỐI TÁC TIẾP THỊ (PARTNER BOT COMMANDS)

        // Nhận diện mã đối tác linh hoạt (Hỗ trợ 1-chạm: /link <mã>, / <mã>, /<mã>, hoặc chỉ gõ trần <mã>)
        $potential_ref = '';
        if (in_array($command, ['/link', 'link', '/lienket', 'lienket', 'liên kết', '/liên kết', '/connect', 'connect', '/ketnoi', 'ketnoi', '/ref', 'ref', '/ma', 'ma', 'mã', '/mã', '/']) && !empty($arg1)) {
            $potential_ref = strtoupper(trim(trim($arg1), " \"#':;.,<>[]()"));
        } elseif (strpos($cleanText, '/') === 0 && strlen($cleanText) >= 2 && strlen($cleanText) <= 35) {
            $candidate = strtoupper(trim(ltrim($cleanText, '/'), " \"#':;.,<>[]()"));
            if (!in_array(strtolower($candidate), ['pending', 'help', 'report', 'test', 'chatid', 'sodu', 'link', 'hotro', 'huy', 'duyet', 'tuchoi', 'start', 'menu'])) {
                $potential_ref = $candidate;
            }
        } elseif (count($parts) === 1 && strlen($cleanText) >= 3 && strlen($cleanText) <= 25) {
            if (!in_array($textLower, ['/pending', 'pending', '/help', 'help', '/report', 'report', '/test', 'test', '/chatid', 'chatid', 'sodu', '/sodu', 'link', '/link', 'hotro', '/hotro', 'huy', '/huy', 'hello', 'hi', '/hello', '/hi', 'start', '/start'])) {
                $potential_ref = strtoupper(trim($cleanText, " \"#':;.,<>[]()"));
            }
        }

        // Nếu có mã đối tác cần ghép nối
        if (!empty($potential_ref)) {
            global $wpdb;
            $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE ref_code = %s OR phone = %s LIMIT 1", $potential_ref, $potential_ref), ARRAY_A);
            
            if ($kol) {
                // Cập nhật ngay Chat ID cho đối tác
                $wpdb->update($aff_table, array('zalo_chat_id' => $chatId), array('id' => $kol['id']), array('%s'), array('%d'));
                
                $portal_link = home_url('/doi-tac/?token=' . $kol['secret_token']);
                $linkSuccessMsg = "🎉 [ LIÊN KẾT ZALO BOT THÀNH CÔNG ] 🎉\n"
                                . "━━━━━━━\n"
                                . "Chào Đối tác {$kol['name']}!\n"
                                . "Tài khoản Zalo của bạn đã được kết nối thành công với Cổng Đối Tác Flora:\n\n"
                                . "• Mã REF: {$kol['ref_code']}\n"
                                . "• Số ĐT: {$kol['phone']}\n"
                                . "• Chat ID: {$chatId}\n\n"
                                . "Từ bây giờ, bạn sẽ tự động nhận được:\n"
                                . "1. Thông báo khi đơn hàng giới thiệu ĐƯỢC DUYỆT THÀNH CÔNG (+hoa hồng).\n"
                                . "2. Thông báo kết quả chuyển khoản khi bạn làm phiếu rút tiền.\n"
                                . "━━━━━━━\n"
                                . "💡 Cú pháp tra cứu nhanh mọi lúc:\n"
                                . "• Soạn: SODU -> Xem số dư khả dụng có thể rút\n"
                                . "• Soạn: LINK -> Lấy lại link giới thiệu cá nhân\n"
                                . "• Soạn: HOTRO -> Kết nối chuyên viên CSKH\n"
                                . "👉 Cổng Đối Tác: {$portal_link}";
                flora_send_zalo_bot_direct_message($linkSuccessMsg, $chatId);
                echo json_encode(['success' => true, 'action' => 'partner_linked', 'ref_code' => $kol['ref_code']]);
                exit;
            } elseif (strpos($cleanText, '/') === 0 || in_array($command, ['/link', 'link', '/lienket', 'lienket', 'liên kết', '/liên kết', '/connect', 'connect', '/ketnoi', 'ketnoi', '/ref', 'ref'])) {
                // Nếu người dùng gõ lệnh / hoặc /link mà không tìm thấy mã ref
                $notFoundMsg = "⚠️ [ KHÔNG TÌM THẤY HỒ SƠ ĐỐI TÁC ] ⚠️\n"
                             . "━━━━━━━\n"
                             . "Không tìm thấy hồ sơ Đối Tác với mã REF hoặc SĐT: {$potential_ref}\n\n"
                             . "💡 Quý đối tác vui lòng kiểm tra lại Mã giới thiệu (REF) trên Cổng Đối Tác (nhakhoaflora.com/doi-tac/) hoặc liên hệ Hotline: 028 7305 8999.";
                flora_send_zalo_bot_direct_message($notFoundMsg, $chatId);
                exit;
            }
        }

        // Cú pháp: SODU / SỐ DƯ (Đối tác tra cứu số dư hoa hồng)
        if (in_array($command, ['/sodu', 'sodu', '/sốdư', 'sốdư', 'số dư', '/số dư', 'so du', '/so du'])) {
            global $wpdb;
            $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE zalo_chat_id = %s LIMIT 1", $chatId), ARRAY_A);
            
            if (!$kol) {
                $msg = "⚠️ Tài khoản Zalo này chưa được liên kết với hồ sơ Đối Tác nào trên Flora.\n\n"
                     . "👉 Quý đối tác vui lòng soạn:\n"
                     . "   LINK <MÃ_REF>\n"
                     . "Ví dụ: LINK DOITACTEST để liên kết nhận báo cáo số dư tức thì!";
                flora_send_zalo_bot_direct_message($msg, $chatId);
                exit;
            }
            
            $stats = function_exists('flora_affiliate_get_financial_stats') ? flora_affiliate_get_financial_stats($kol['id']) : array('available_balance' => 0, 'pending_commission' => 0, 'paid_commission' => 0, 'confirmed_orders' => 0);
            $availFmt   = number_format($stats['available_balance'], 0, ',', '.') . ' VNĐ';
            $pendingFmt = number_format($stats['pending_commission'], 0, ',', '.') . ' VNĐ';
            $paidFmt    = number_format($stats['paid_commission'], 0, ',', '.') . ' VNĐ';
            $portalUrl  = home_url('/doi-tac/?token=' . $kol['secret_token']);
            
            $soduMsg = "💰 [ TRA CỨU SỐ DƯ HOA HỒNG FLORA ] 💰\n"
                     . "━━━━━━━\n"
                     . "Chào Đối tác {$kol['name']}! (REF: {$kol['ref_code']})\n\n"
                     . "💳 Số dư khả dụng (Có thể rút ngay): {$availFmt}\n"
                     . "⏳ Hoa hồng chờ đối soát: {$pendingFmt}\n"
                     . "✅ Tổng hoa hồng đã nhận: {$paidFmt}\n"
                     . "📦 Số đơn hoàn tất: {$stats['confirmed_orders']} đơn\n"
                     . "━━━━━━━\n"
                     . "👉 Bấm vào đây để vào Cổng Rút Tiền:\n"
                     . "   {$portalUrl}";
            flora_send_zalo_bot_direct_message($soduMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'partner_balance_checked']);
            exit;
        }

        // Cú pháp: LINK (Không kèm tham số - Đối tác lấy lại link giới thiệu & voucher)
        if ((in_array($command, ['/link', 'link', '/voucher', 'voucher']) && empty($arg1)) || in_array($textLower, ['link', '/link', 'voucher', '/voucher'])) {
            global $wpdb;
            $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE zalo_chat_id = %s LIMIT 1", $chatId), ARRAY_A);
            
            if (!$kol) {
                $msg = "⚠️ Tài khoản Zalo này chưa liên kết với hồ sơ Đối Tác Flora.\n\n"
                     . "👉 Soạn: LINK <MÃ_REF> để liên kết (Ví dụ: LINK DOITACTEST).";
                flora_send_zalo_bot_direct_message($msg, $chatId);
                exit;
            }
            
            $refUrl = home_url('/goi-dich-vu/?ref=' . $kol['ref_code']);
            $portalUrl = home_url('/doi-tac/?token=' . $kol['secret_token']);
            $commRate = $kol['commission_rate'] . ($kol['commission_type'] === 'fixed' ? 'đ' : '%');
            
            $linkMsg = "🔗 [ THÔNG TIN TIẾP THỊ ĐỘC QUYỀN ] 🔗\n"
                     . "━━━━━━━\n"
                     . "Chào Đối tác {$kol['name']}!\n\n"
                     . "• Mã REF của bạn: {$kol['ref_code']}\n"
                     . "• Mức hoa hồng: {$commRate}\n"
                     . "• Đường link giới thiệu độc quyền:\n"
                     . "  {$refUrl}\n"
                     . "• Mã voucher ưu đãi cho khách: {$kol['ref_code']}\n"
                     . "━━━━━━━\n"
                     . "👉 Cổng quản trị cá nhân: {$portalUrl}";
            flora_send_zalo_bot_direct_message($linkMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'partner_link_retrieved']);
            exit;
        }

        // Cú pháp: HOTRO / HỖ TRỢ
        if (in_array($command, ['/hotro', 'hotro', '/hỗtrợ', 'hỗtrợ', 'hỗ trợ', '/hỗ trợ', 'support', '/support'])) {
            $hotroMsg = "📞 [ TRỢ GIÚP ĐỐI TÁC NHA KHOA FLORA ] 📞\n"
                      . "━━━━━━━\n"
                      . "Mọi thắc mắc về đối soát doanh thu, duyệt đơn hoặc quyết toán hoa hồng, Quý đối tác vui lòng liên hệ:\n\n"
                      . "• Hotline Đối Tác 24/7: 028 7305 8999\n"
                      . "• Zalo Hỗ Trợ Kỹ Thuật & Kế Toán: 028 7305 8999\n"
                      . "• Phòng khám: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, TP.HCM\n"
                      . "• Thời gian hỗ trợ: 08:00 - 19:00 (Tất cả các ngày trong tuần)";
            flora_send_zalo_bot_direct_message($hotroMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'hotro_replied']);
            exit;
        }

        // Cú pháp: HUY / UNLINK (Hủy liên kết Zalo bot)
        if (in_array($command, ['/huy', 'huy', '/unlink', 'unlink', 'hủy', '/hủy'])) {
            global $wpdb;
            $aff_table = function_exists('flora_get_affiliates_table_name') ? flora_get_affiliates_table_name() : $wpdb->prefix . 'flora_affiliates';
            $kol = $wpdb->get_row($wpdb->prepare("SELECT * FROM $aff_table WHERE zalo_chat_id = %s LIMIT 1", $chatId), ARRAY_A);
            if ($kol) {
                $wpdb->update($aff_table, array('zalo_chat_id' => ''), array('id' => $kol['id']), array('%s'), array('%d'));
                $unMsg = "✅ Đã hủy liên kết Zalo Bot cho hồ sơ Đối Tác {$kol['name']} (REF: {$kol['ref_code']}).\n"
                       . "Khi cần kết nối lại, bạn chỉ cần soạn: LINK {$kol['ref_code']}";
                flora_send_zalo_bot_direct_message($unMsg, $chatId);
            } else {
                flora_send_zalo_bot_direct_message("Tài khoản Zalo này hiện chưa liên kết với đối tác nào.", $chatId);
            }
            exit;
        }

        // 3. LỆNH /chatid, /id
        if (in_array($command, ['/chatid', 'chatid', '/id', 'id', '/info', 'info']) || in_array($textLower, ['/chatid', 'chatid', '/id', 'id', '/info', 'info'])) {
            update_option('flora_zalo_group_chat_id', $chatId);
            $reply = "✅ [ NHA KHOA FLORA - KẾT NỐI NHÓM THÀNH CÔNG ]\n"
                   . "━━━━━━━\n"
                   . "📌 Group Chat ID: " . $chatId . "\n"
                   . "👤 Người kích hoạt: " . $senderLabel . "\n\n"
                   . "🎉 Hệ thống đã tự động lưu nhóm Zalo này vào Website Flora!\n"
                   . "Mọi thông báo khi khách chuyển khoản ACB, đăng ký đối tác hoặc yêu cầu rút tiền hoa hồng sẽ được rung chuông ngay tại đây.\n\n"
                   . "💡 Gõ /pending để kiểm tra các đơn hàng đang chờ duyệt.";
            flora_send_zalo_bot_direct_message($reply, $chatId);
            echo json_encode(['success' => true, 'action' => 'chatid_registered', 'chat_id' => $chatId]);
            exit;
        }

        // 4. LỆNH /test
        if ($textLower === '/test') {
            $testMsg = "🔔 [ TIN NHẮN KIỂM TRA ĐƯỜNG TRUYỀN FLORA ]\n"
                     . "━━━━━━━\n"
                     . "🏷️ Mã đơn test: FLORA-TEST-" . rand(1000, 9999) . "\n"
                     . "👤 Khách hàng: Nguyễn Khách Mẫu\n"
                     . "📞 SĐT: 0912345678\n"
                     . "📦 Gói: Care Plus (999.000 VNĐ)\n"
                     . "🏦 Ngân hàng: ACB - 77779268 (CN HOA HUNG)\n"
                     . "⏰ Lúc: " . current_time('d/m/Y H:i:s') . "\n"
                     . "━━━━━━━\n"
                     . "⚡ Zalo Bot Platform hoạt động hoàn hảo và sẵn sàng nhận đơn!";
            flora_send_zalo_bot_direct_message($testMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'test_sent']);
            exit;
        }

        // 5. LỆNH /pending, /cho (Xem danh sách đơn chờ duyệt)
        if (in_array($textLower, ['/pending', '/cho', 'pending', 'chờ', 'cho'])) {
            global $wpdb;
            $table_name = function_exists('flora_get_orders_table_name') ? flora_get_orders_table_name() : $wpdb->prefix . 'flora_orders';
            $orders = $wpdb->get_results("SELECT * FROM $table_name WHERE payment_status = 'pending_confirmation' ORDER BY created_at DESC LIMIT 5", ARRAY_A);

            if (empty($orders)) {
                $msg = "🎉 Hiện không có đơn hàng nào đang chờ xác nhận! Tất cả đơn đã được xử lý xong.";
            } else {
                $msg = "⏳ [ DANH SÁCH ĐƠN HÀNG ĐANG CHỜ XÁC NHẬN ] (" . count($orders) . " đơn mới nhất)\n"
                     . "━━━━━━━\n";
                foreach ($orders as $idx => $o) {
                    $num = $idx + 1;
                    $amt = number_format($o['final_amount'], 0, ',', '.') . 'đ';
                    $hasProof = !empty($o['proof_image_url']) ? "📸 ĐÃ CÓ ẢNH BILL" : "Chưa gửi ảnh";
                    $timeAgo = human_time_diff(strtotime($o['created_at']), current_time('timestamp')) . ' trước';

                    $msg .= "{$num}. #{$o['order_code']} | {$amt}\n"
                          . "   👤 {$o['customer_name']} - {$o['customer_phone']}\n"
                          . "   📦 {$o['package_name']}\n"
                          . "   📝 Cú pháp: {$o['transfer_syntax']}\n"
                          . "   🖼️ {$hasProof}\n";
                    if (!empty($o['proof_image_url'])) {
                        $msg .= "   🔗 Link bill: {$o['proof_image_url']}\n";
                    }
                    $msg .= "   ⏰ Tạo: {$timeAgo}\n"
                          . "   👉 Duyệt: /duyet {$o['order_code']}\n"
                          . "   👉 Từ chối: /tuchoi {$o['order_code']}\n\n";
                }
                $msg .= "━━━━━━━\n"
                      . "💡 Gõ /duyet <mã_đơn> để duyệt trực tiếp ngay tại đây!";
            }
            flora_send_zalo_bot_direct_message($msg, $chatId);
            echo json_encode(['success' => true, 'action' => 'pending_listed']);
            exit;
        }

        // Đảm bảo module order-manager & affiliate-manager luôn sẵn sàng
        if (!function_exists('flora_approve_order')) {
            $orderMgrPath = get_template_directory() . '/inc/order-manager.php';
            if (file_exists($orderMgrPath)) {
                require_once $orderMgrPath;
            }
        }
        if (!function_exists('flora_approve_affiliate')) {
            $affMgrPath = get_template_directory() . '/inc/affiliate-manager.php';
            if (file_exists($affMgrPath)) {
                require_once $affMgrPath;
            }
        }

        // 6. LỆNH /duyet <mã_đơn> (Duyệt đơn trực tiếp từ Zalo)
        $isDuyetCmd = in_array($command, ['/duyet', 'duyet', '/duyệt', 'duyệt', '/approve', 'approve']);
        if ($isDuyetCmd) {
            $orderCode = strtoupper(trim(trim($arg1), ' "#\':;.,<>[]()'));
            if (empty($orderCode)) {
                $hintMsg = "⚠️ Vui lòng cung cấp mã đơn hàng cần duyệt.\n"
                         . "Cú pháp: /duyet <mã_đơn>\n"
                         . "Ví dụ: /duyet FLORA-89AB12CD";
                flora_send_zalo_bot_direct_message($hintMsg, $chatId);
                exit;
            }

            if (function_exists('flora_approve_order')) {
                $operator = "TVV {$senderLabel} (Zalo)";
                $res = flora_approve_order($orderCode, $operator, false);

                if ($res['success']) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';

                    $successMsg = "🔔 [ ĐÃ DUYỆT THÀNH CÔNG ĐƠN HÀNG #{$order['order_code']} ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n\n"
                                . "👤 Thông Tin Khách Hàng:\n"
                                . "  ▸ Họ tên: {$order['customer_name']}\n"
                                . "  ▸ Số ĐT: {$order['customer_phone']}\n"
                                . "  └─ Gói kích hoạt: {$order['package_name']}\n"
                                . "  └─ Thực thu ACB: {$amtFmt}\n"
                                . "  └─ Cú pháp: {$order['transfer_syntax']}\n"
                                . (!empty($order['affiliate_code']) ? "  └─ Mã giới thiệu: {$order['affiliate_code']}\n" : "")
                                . "  └─ Trạng thái: ĐÃ THANH TOÁN (PAID)\n"
                                . "  └─ Người duyệt: {$senderName}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Nguồn: Hệ Thống Flora\n"
                                . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($successMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'order_approved', 'order_code' => $order['order_code']]);
                    exit;
                } elseif (!empty($res['already_paid'])) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Hệ thống';
                    $paidTime = !empty($res['paid_time']) ? date('d/m/Y H:i:s', strtotime($res['paid_time'])) : (!empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : 'Trước đó');

                    $alreadyMsg = "🔔 [ ĐƠN HÀNG ĐÃ ĐƯỢC DUYỆT TRƯỚC ĐÓ ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n"
                                . "Mã đơn: #{$order['order_code']}\n\n"
                                . "👤 Thông Tin Đơn Hàng:\n"
                                . "  ▸ Khách hàng: {$order['customer_name']} - {$order['customer_phone']}\n"
                                . "  ▸ Gói dịch vụ: {$order['package_name']}\n"
                                . "  └─ Số tiền: {$amtFmt}\n"
                                . "  └─ Trạng thái: ĐÃ THANH TOÁN (PAID)\n"
                                . "  └─ Người duyệt trước: {$prevOp}\n"
                                . "  └─ Thời điểm duyệt: {$paidTime}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Đơn hàng này đã được duyệt hoàn tất, không cần duyệt lại!\n"
                                . "  └─ Thời gian báo: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($alreadyMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'already_paid', 'order_code' => $order['order_code']]);
                    exit;
                } elseif (!empty($res['already_rejected'])) {
                    $order = $res['order'];
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Tư vấn viên / Admin';
                    $rejTime = !empty($res['rejected_time']) ? date('d/m/Y H:i:s', strtotime($res['rejected_time'])) : (!empty($order['rejected_at']) ? date('d/m/Y H:i:s', strtotime($order['rejected_at'])) : 'Trước đó');
                    $rejReason = !empty($res['reason']) ? $res['reason'] : (!empty($order['reject_reason']) ? $order['reject_reason'] : 'Chưa nhận được chuyển khoản sau đối soát sao kê ACB');

                    $warnMsg = "⚠️ [ ĐƠN HÀNG ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ ] ⚠️\n"
                             . "━━━━━━\n"
                             . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n"
                             . "Mã đơn: #{$order['order_code']}\n\n"
                             . "👤 Thông Tin Khách Hàng:\n"
                             . "  ▸ Họ tên: {$order['customer_name']}\n"
                             . "  ▸ Số ĐT: {$order['customer_phone']}\n"
                             . "  └─ Gói đăng ký: {$order['package_name']}\n"
                             . "  └─ Trạng thái: ĐÃ TỪ CHỐI (REJECTED)\n"
                             . "  └─ Người từ chối: {$prevOp}\n"
                             . "  └─ Thời điểm từ chối: {$rejTime}\n"
                             . "  └─ Lý do từ chối: {$rejReason}\n\n"
                             . "━━━━━━\n"
                             . "  └─ Hướng dẫn: Nếu khách hàng đã chuyển khoản đúng và cần duyệt phục hồi:\n"
                             . "  └─ Cú pháp: /duyetlai {$order['order_code']}\n"
                             . "  └─ Hoặc xử lý trực tiếp tại trang quản trị WP-Admin.";
                    flora_send_zalo_bot_direct_message($warnMsg, $chatId);
                    echo json_encode(['success' => false, 'action' => 'already_rejected', 'order_code' => $order['order_code']]);
                    exit;
                } else {
                    $failMsg = "❌ Thao tác duyệt không thành công: " . $res['message'];
                    flora_send_zalo_bot_direct_message($failMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => $res['message']]);
                    exit;
                }
            } else {
                flora_send_zalo_bot_direct_message("❌ Lỗi hệ thống: Hàm flora_approve_order chưa được khởi tạo.", $chatId);
                exit;
            }
        }

        // 6b. LỆNH /duyetlai <mã_đơn> (Phục hồi và duyệt lại đơn đã từng bị từ chối)
        $isDuyetLaiCmd = in_array($command, ['/duyetlai', 'duyetlai', '/duyệt lại', 'duyệt lại', '/duyệtlại', 'duyệtlại', '/reapprove', 'reapprove']);
        if ($isDuyetLaiCmd) {
            $orderCode = strtoupper(trim(trim($arg1), ' "#\':;.,<>[]()'));
            if (empty($orderCode)) {
                $hintMsg = "⚠️ Vui lòng cung cấp mã đơn hàng cần duyệt phục hồi lại.\n"
                         . "Cú pháp: /duyetlai <mã_đơn>\n"
                         . "Ví dụ: /duyetlai FLORA-89AB12CD";
                flora_send_zalo_bot_direct_message($hintMsg, $chatId);
                exit;
            }

            if (function_exists('flora_approve_order')) {
                $operator = "TVV {$senderLabel} (Zalo)";
                $res = flora_approve_order($orderCode, $operator, true);

                if ($res['success']) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';

                    $successMsg = "🔔 [ ĐÃ PHỤC HỒI & DUYỆT THÀNH CÔNG ĐƠN HÀNG #{$order['order_code']} ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n\n"
                                . "👤 Thông Tin Khách Hàng:\n"
                                . "  ▸ Họ tên: {$order['customer_name']}\n"
                                . "  ▸ Số ĐT: {$order['customer_phone']}\n"
                                . "  └─ Gói kích hoạt: {$order['package_name']}\n"
                                . "  └─ Thực thu ACB: {$amtFmt}\n"
                                . "  └─ Cú pháp: {$order['transfer_syntax']}\n"
                                . (!empty($order['affiliate_code']) ? "  └─ Mã giới thiệu: {$order['affiliate_code']}\n" : "")
                                . "  └─ Trạng thái: ĐÃ PHỤC HỒI & THANH TOÁN (PAID)\n"
                                . "  └─ Người duyệt phục hồi: {$senderName}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Đơn từ chối trước đó đã được mở lại và gửi mail xác nhận.\n"
                                . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($successMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'order_reapproved', 'order_code' => $order['order_code']]);
                    exit;
                } elseif (!empty($res['already_paid'])) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Hệ thống';
                    $paidTime = !empty($res['paid_time']) ? date('d/m/Y H:i:s', strtotime($res['paid_time'])) : (!empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : 'Trước đó');

                    $alreadyMsg = "🔔 [ ĐƠN HÀNG ĐÃ ĐƯỢC DUYỆT TRƯỚC ĐÓ ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n"
                                . "Mã đơn: #{$order['order_code']}\n\n"
                                . "👤 Thông Tin Đơn Hàng:\n"
                                . "  ▸ Khách hàng: {$order['customer_name']} - {$order['customer_phone']}\n"
                                . "  ▸ Gói dịch vụ: {$order['package_name']}\n"
                                . "  └─ Số tiền: {$amtFmt}\n"
                                . "  └─ Trạng thái: ĐÃ THANH TOÁN (PAID)\n"
                                . "  └─ Người duyệt trước: {$prevOp}\n"
                                . "  └─ Thời điểm duyệt: {$paidTime}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Đơn hàng này đang ở trạng thái ĐÃ DUYỆT, không cần duyệt lại!\n"
                                . "  └─ Thời gian báo: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($alreadyMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'already_paid', 'order_code' => $order['order_code']]);
                    exit;
                } else {
                    $failMsg = "❌ Thao tác phục hồi không thành công: " . $res['message'];
                    flora_send_zalo_bot_direct_message($failMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => $res['message']]);
                    exit;
                }
            } else {
                flora_send_zalo_bot_direct_message("❌ Lỗi hệ thống: Hàm flora_approve_order chưa được khởi tạo.", $chatId);
                exit;
            }
        }

        // 7. LỆNH /tuchoi <mã_đơn> [lý do] (Từ chối đơn trực tiếp từ Zalo)
        $isTuChoiCmd = in_array($command, ['/tuchoi', 'tuchoi', '/từ chối', 'từ chối', '/từchối', 'từchối', '/reject', 'reject']);
        if ($isTuChoiCmd) {
            $orderCode = strtoupper(trim(trim($arg1), ' "#\':;.,<>[]()'));
            $reason = !empty($arg2) ? trim($arg2) : 'Chưa nhận được chuyển khoản sau khi đối soát sao kê ngân hàng ACB.';

            if (empty($orderCode)) {
                $hintMsg = "⚠️ Vui lòng cung cấp mã đơn hàng cần từ chối.\n"
                         . "Cú pháp: /tuchoi <mã_đơn> [lý do]\n"
                         . "Ví dụ: /tuchoi FLORA-89AB12CD Chưa nhận được tiền vào ACB";
                flora_send_zalo_bot_direct_message($hintMsg, $chatId);
                exit;
            }

            if (function_exists('flora_reject_order')) {
                $operator = "TVV {$senderLabel} (Zalo)";
                $res = flora_reject_order($orderCode, $reason, $operator);

                if ($res['success']) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
                    $rejectMsg = "🔔 [ ĐÃ TỪ CHỐI ĐƠN HÀNG #{$order['order_code']} ] 🔔\n"
                               . "━━━━━━\n"
                               . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n\n"
                               . "👤 Thông Tin Khách Hàng:\n"
                               . "  ▸ Họ tên: {$order['customer_name']}\n"
                               . "  ▸ Số ĐT: {$order['customer_phone']}\n"
                               . "  └─ Gói đăng ký: {$order['package_name']}\n"
                               . "  └─ Số tiền: {$amtFmt}\n"
                               . "  └─ Trạng thái: ĐÃ TỪ CHỐI (REJECTED)\n"
                               . "  └─ Lý do: {$reason}\n"
                               . "  └─ Người từ chối: {$senderName}\n\n"
                               . "━━━━━━\n"
                               . "  └─ Nguồn: Hệ Thống Flora\n"
                               . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($rejectMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'order_rejected', 'order_code' => $order['order_code']]);
                    exit;
                } elseif (!empty($res['is_paid'])) {
                    $order = $res['order'];
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Hệ thống / Kế toán';
                    $paidTime = !empty($res['paid_time']) ? date('d/m/Y H:i:s', strtotime($res['paid_time'])) : (!empty($order['paid_at']) ? date('d/m/Y H:i:s', strtotime($order['paid_at'])) : 'Trước đó');

                    $warnMsg = "⛔ [ CẢNH BÁO AN TOÀN TÀI CHÍNH ] ⛔\n"
                             . "━━━━━━\n"
                             . "Đơn hàng #{$order['order_code']} ĐÃ ĐƯỢC DUYỆT THANH TOÁN THÀNH CÔNG trước đó!\n\n"
                             . "👤 Chi Tiết Đơn Hàng:\n"
                             . "  ▸ Khách hàng: {$order['customer_name']} ({$order['customer_phone']})\n"
                             . "  ▸ Gói dịch vụ: {$order['package_name']}\n"
                             . "  └─ Trạng thái: ĐÃ THANH TOÁN (PAID)\n"
                             . "  └─ Người duyệt: {$prevOp}\n"
                             . "  └─ Thời điểm duyệt: {$paidTime}\n\n"
                             . "━━━━━━\n"
                             . "  └─ Để bảo vệ sổ sách kế toán, bot Zalo không cho phép từ chối đơn đã thanh toán.\n"
                             . "  └─ Vui lòng liên hệ Kế toán hoặc xử lý trực tiếp tại WP-Admin nếu cần hoàn tiền.";
                    flora_send_zalo_bot_direct_message($warnMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => 'Cannot cancel paid order via Zalo Bot']);
                    exit;
                } elseif (!empty($res['already_rejected'])) {
                    $order = $res['order'];
                    $amtFmt = number_format($order['final_amount'], 0, ',', '.') . ' VNĐ';
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Tư vấn viên / Admin';
                    $rejTime = !empty($res['rejected_time']) ? date('d/m/Y H:i:s', strtotime($res['rejected_time'])) : (!empty($order['rejected_at']) ? date('d/m/Y H:i:s', strtotime($order['rejected_at'])) : 'Trước đó');
                    $rejReason = !empty($res['reason']) ? $res['reason'] : (!empty($order['reject_reason']) ? $order['reject_reason'] : 'Chưa nhận được chuyển khoản sau khi đối soát sao kê ACB');

                    $alreadyMsg = "🔔 [ ĐƠN HÀNG ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dịch vụ: Nha Khoa Flora - {$order['package_name']}\n"
                                . "Mã đơn: #{$order['order_code']}\n\n"
                                . "👤 Thông Tin Khách Hàng:\n"
                                . "  ▸ Họ tên: {$order['customer_name']}\n"
                                . "  ▸ Số ĐT: {$order['customer_phone']}\n"
                                . "  └─ Gói đăng ký: {$order['package_name']}\n"
                                . "  └─ Số tiền: {$amtFmt}\n"
                                . "  └─ Trạng thái: ĐÃ TỪ CHỐI (REJECTED)\n"
                                . "  └─ Người từ chối trước đó: {$prevOp}\n"
                                . "  └─ Thời điểm từ chối: {$rejTime}\n"
                                . "  └─ Lý do ghi nhận: {$rejReason}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Đơn hàng này đã được từ chối trước đó, không cần từ chối lại!\n"
                                . "  └─ Hướng dẫn: Nếu muốn phục hồi và duyệt lại, gõ: /duyetlai {$order['order_code']}\n"
                                . "  └─ Thời gian báo: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($alreadyMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'already_rejected', 'order_code' => $order['order_code']]);
                    exit;
                } else {
                    $failMsg = "❌ Thao tác từ chối không thành công: " . $res['message'];
                    flora_send_zalo_bot_direct_message($failMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => $res['message']]);
                    exit;
                }
            } else {
                flora_send_zalo_bot_direct_message("❌ Lỗi hệ thống: Hàm flora_reject_order chưa được khởi tạo.", $chatId);
                exit;
            }
        }

        // 7b. LỆNH /pendingkol (Danh sách đối tác / KOL đang chờ duyệt)
        $isPendingKolCmd = in_array($command, ['/pendingkol', 'pendingkol', '/chokol', 'chokol']);
        if ($isPendingKolCmd) {
            global $wpdb;
            $aff_table = $wpdb->prefix . 'flora_affiliates';
            $affiliates = [];
            if ($wpdb->get_var("SHOW TABLES LIKE '$aff_table'") == $aff_table) {
                $affiliates = $wpdb->get_results("SELECT * FROM $aff_table WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5", ARRAY_A);
            }

            if (empty($affiliates)) {
                $msg = "🎉 Hiện không có hồ sơ đối tác/KOL nào đang chờ duyệt! Tất cả đã được xử lý.";
            } else {
                $msg = "⏳ [ DANH SÁCH ĐỐI TÁC / KOL CHỜ DUYỆT ] (" . count($affiliates) . " đối tác mới nhất)\n"
                     . "━━━━━━\n";
                foreach ($affiliates as $idx => $a) {
                    $num = $idx + 1;
                    $timeAgo = human_time_diff(strtotime($a['created_at']), current_time('timestamp')) . ' trước';
                    $bankInfo = trim(($a['bank_name'] ?? '') . ' ' . ($a['bank_account_number'] ?? ''));
                    $msg .= "{$num}. {$a['name']} | SĐT: {$a['phone']}\n"
                          . "   📧 {$a['email']}\n"
                          . (!empty($bankInfo) ? "   🏦 Ngân hàng: {$bankInfo}\n" : "")
                          . "   ⏰ Đăng ký: {$timeAgo}\n"
                          . "   👉 Duyệt: /duyetkol {$a['phone']}\n"
                          . "   👉 Từ chối: /tuchoikol {$a['phone']}\n\n";
                }
                $msg .= "━━━━━━\n"
                      . "💡 Gõ /duyetkol <SĐT> để duyệt và cấp mã giới thiệu ngay tại đây!";
            }
            flora_send_zalo_bot_direct_message($msg, $chatId);
            echo json_encode(['success' => true, 'action' => 'pendingkol_listed']);
            exit;
        }

        // 7c. LỆNH /duyetkol <sđt/id/ref> (Duyệt đối tác tiếp thị)
        $isDuyetKolCmd = in_array($command, ['/duyetkol', 'duyetkol', '/duyệt kol', 'duyệt kol', '/duyệtkol', 'duyệtkol']);
        if ($isDuyetKolCmd) {
            $target = trim(trim($arg1), ' "#\':;.,<>[]()');
            if (empty($target)) {
                $hintMsg = "⚠️ Vui lòng cung cấp SĐT hoặc ID đối tác cần duyệt.\n"
                         . "Cú pháp: /duyetkol <SĐT_hoặc_ID>\n"
                         . "Ví dụ: /duyetkol 0912345678";
                flora_send_zalo_bot_direct_message($hintMsg, $chatId);
                exit;
            }

            if (function_exists('flora_approve_affiliate')) {
                $operator = "TVV {$senderLabel} (Zalo)";
                $res = flora_approve_affiliate($target, 0, $operator);

                if ($res['success']) {
                    $aff = $res['affiliate'];
                    $refCode = $res['ref_code'];
                    $refLink = home_url('/goi-dich-vu/?ref=' . $refCode);

                    $msg = "🔔 [ ĐÃ DUYỆT THÀNH CÔNG ĐỐI TÁC / KOL ] 🔔\n"
                         . "━━━━━━\n"
                         . "Dự án: Cổng Đối Tác Nha Khoa Flora\n\n"
                         . "👤 Thông Tin Đối Tác:\n"
                         . "  ▸ Họ tên: {$aff['name']}\n"
                         . "  ▸ Số ĐT: {$aff['phone']}\n"
                         . "  ▸ Email: {$aff['email']}\n"
                         . "  └─ Mã giới thiệu: {$refCode}\n"
                         . "  └─ Tỉ lệ hoa hồng: {$aff['commission_rate']}%\n"
                         . "  └─ Link giới thiệu: {$refLink}\n"
                         . "  └─ Trạng thái: ĐÃ KÍCH HOẠT (ACTIVE)\n"
                         . "  └─ Người duyệt: {$senderName}\n\n"
                         . "━━━━━━\n"
                         . "  └─ Email: Đã gửi thông tin tài khoản và link portal cho đối tác.\n"
                         . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($msg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'affiliate_approved', 'phone' => $aff['phone']]);
                    exit;
                } elseif (!empty($res['already_approved'])) {
                    $aff = $res['affiliate'];
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Hệ thống / Admin';
                    $approvedTime = !empty($aff['approved_at']) ? date('d/m/Y H:i:s', strtotime($aff['approved_at'])) : 'Trước đó';

                    $alreadyMsg = "🔔 [ HỒ SƠ ĐỐI TÁC ĐÃ ĐƯỢC DUYỆT TRƯỚC ĐÓ ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dự án: Cổng Đối Tác Nha Khoa Flora\n"
                                . "Đối tác: {$aff['name']} ({$aff['phone']})\n\n"
                                . "👤 Chi Tiết Kích Hoạt:\n"
                                . "  ▸ Mã Ref: {$aff['ref_code']}\n"
                                . "  ▸ Email: {$aff['email']}\n"
                                . "  └─ Hoa hồng: {$aff['commission_rate']}%\n"
                                . "  └─ Trạng thái: ĐANG HOẠT ĐỘNG (ACTIVE)\n"
                                . "  └─ Người duyệt trước: {$prevOp}\n"
                                . "  └─ Thời điểm duyệt: {$approvedTime}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Đối tác đã được cấp mã hoạt động, không cần duyệt lại!\n"
                                . "  └─ Thời gian báo: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($alreadyMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'affiliate_already_approved', 'phone' => $aff['phone']]);
                    exit;
                } else {
                    $failMsg = "❌ Thao tác duyệt đối tác không thành công: " . $res['message'];
                    flora_send_zalo_bot_direct_message($failMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => $res['message']]);
                    exit;
                }
            } else {
                flora_send_zalo_bot_direct_message("❌ Lỗi hệ thống: Hàm flora_approve_affiliate chưa được khởi tạo.", $chatId);
                exit;
            }
        }

        // 7d. LỆNH /tuchoikol <sđt/id/ref> [lý do] (Từ chối đối tác tiếp thị)
        $isTuChoiKolCmd = in_array($command, ['/tuchoikol', 'tuchoikol', '/từ chối kol', 'từ chối kol', '/từchốikol', 'từchốikol']);
        if ($isTuChoiKolCmd) {
            $target = trim(trim($arg1), ' "#\':;.,<>[]()');
            $reason = !empty($arg2) ? trim($arg2) : 'Hồ sơ đối tác chưa đáp ứng đủ tiêu chuẩn hợp tác.';

            if (empty($target)) {
                $hintMsg = "⚠️ Vui lòng cung cấp SĐT hoặc ID đối tác cần từ chối.\n"
                         . "Cú pháp: /tuchoikol <SĐT_hoặc_ID> [lý do]\n"
                         . "Ví dụ: /tuchoikol 0912345678 Chưa đủ điều kiện";
                flora_send_zalo_bot_direct_message($hintMsg, $chatId);
                exit;
            }

            if (function_exists('flora_reject_affiliate')) {
                $operator = "TVV {$senderLabel} (Zalo)";
                $res = flora_reject_affiliate($target, $reason, $operator);

                if ($res['success']) {
                    $aff = $res['affiliate'];
                    $msg = "🔔 [ ĐÃ TỪ CHỐI HỒ SƠ ĐỐI TÁC / KOL ] 🔔\n"
                         . "━━━━━━\n"
                         . "Dự án: Cổng Đối Tác Nha Khoa Flora\n\n"
                         . "👤 Thông Tin Đối Tác:\n"
                         . "  ▸ Họ tên: {$aff['name']}\n"
                         . "  ▸ Số ĐT: {$aff['phone']}\n"
                         . "  ▸ Email: {$aff['email']}\n"
                         . "  └─ Trạng thái: ĐÃ TỪ CHỐI (INACTIVE)\n"
                         . "  └─ Lý do: {$reason}\n"
                         . "  └─ Người từ chối: {$senderName}\n\n"
                         . "━━━━━━\n"
                         . "  └─ Nguồn: Hệ Thống Flora\n"
                         . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($msg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'affiliate_rejected', 'phone' => $aff['phone']]);
                    exit;
                } elseif (!empty($res['already_rejected'])) {
                    $aff = $res['affiliate'];
                    $prevOp = !empty($res['operator']) ? $res['operator'] : 'Admin';
                    $rejReason = !empty($res['reason']) ? $res['reason'] : (!empty($aff['reject_reason']) ? $aff['reject_reason'] : 'Chưa phù hợp tiêu chuẩn');

                    $alreadyMsg = "🔔 [ HỒ SƠ ĐỐI TÁC ĐÃ BỊ TỪ CHỐI TRƯỚC ĐÓ ] 🔔\n"
                                . "━━━━━━\n"
                                . "Dự án: Cổng Đối Tác Nha Khoa Flora\n"
                                . "Đối tác: {$aff['name']} ({$aff['phone']})\n\n"
                                . "👤 Thông Tin Ghi Nhận:\n"
                                . "  ▸ Email: {$aff['email']}\n"
                                . "  └─ Trạng thái: ĐÃ TỪ CHỐI (INACTIVE)\n"
                                . "  └─ Người từ chối trước đó: {$prevOp}\n"
                                . "  └─ Lý do: {$rejReason}\n\n"
                                . "━━━━━━\n"
                                . "  └─ Lưu ý: Hồ sơ này đã bị từ chối trước đó, không cần từ chối lại!\n"
                                . "  └─ Thời gian báo: " . current_time('d/m/Y H:i:s');
                    flora_send_zalo_bot_direct_message($alreadyMsg, $chatId);
                    echo json_encode(['success' => true, 'action' => 'affiliate_already_rejected', 'phone' => $aff['phone']]);
                    exit;
                } else {
                    $failMsg = "❌ Thao tác từ chối đối tác không thành công: " . $res['message'];
                    flora_send_zalo_bot_direct_message($failMsg, $chatId);
                    echo json_encode(['success' => false, 'message' => $res['message']]);
                    exit;
                }
            } else {
                flora_send_zalo_bot_direct_message("❌ Lỗi hệ thống: Hàm flora_reject_affiliate chưa được khởi tạo.", $chatId);
                exit;
            }
        }

        // 8. LỆNH /report hoặc /thongke
        if (in_array($textLower, ['/report', '/thongke', 'report', 'thống kê'])) {
            global $wpdb;
            $table_name = function_exists('flora_get_orders_table_name') ? flora_get_orders_table_name() : $wpdb->prefix . 'flora_orders';
            $today = current_time('Y-m-d');

            $total_today = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s", $today));
            $paid_today  = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE DATE(created_at) = %s AND payment_status = 'paid'", $today));
            $revenue     = (int)$wpdb->get_var($wpdb->prepare("SELECT COALESCE(SUM(final_amount), 0) FROM $table_name WHERE DATE(created_at) = %s AND payment_status = 'paid'", $today));
            $pending     = (int)$wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE payment_status = 'pending_confirmation'");

            $repMsg = "🔔 [ BÁO CÁO ĐƠN HÀNG GÓI KHÁM HÔM NAY ] 🔔\n"
                    . "━━━━━━\n"
                    . "Dự án: Nha Khoa Flora\n\n"
                    . "👤 Thống Kê Hoạt Động:\n"
                    . "  ▸ Ngày: " . current_time('d/m/Y') . "\n"
                    . "  └─ Tổng đơn hôm nay: {$total_today} đơn\n"
                    . "  └─ Đã duyệt thành công: {$paid_today} đơn\n"
                    . "  └─ Thực thu ACB: " . number_format($revenue, 0, ',', '.') . " VNĐ\n"
                    . "  └─ Đơn đang chờ xác nhận: {$pending} đơn\n\n"
                    . "━━━━━━\n"
                    . "  └─ Hướng dẫn: Gõ /pending để xem danh sách chờ duyệt\n"
                    . "  └─ Thời gian: " . current_time('d/m/Y H:i:s');
            flora_send_zalo_bot_direct_message($repMsg, $chatId);
            echo json_encode(['success' => true, 'action' => 'report_sent']);
            exit;
        }
    }

    echo json_encode(['status' => 'ok', 'chat_id' => $chatId]);
    exit;
}

// Phản hồi mặc định
echo json_encode([
    'success' => true,
    'message' => 'Nha Khoa Flora Webhook endpoint received payload successfully'
], JSON_UNESCAPED_UNICODE);

/**
 * Helper gửi tin nhắn trực tiếp qua Zalo Bot Platform
 */
function flora_send_zalo_bot_direct_message($text, $chatId = '') {
    $token = trim(get_option('flora_zalo_bot_token', ''));
    $targetChat = !empty($chatId) ? trim($chatId) : trim(get_option('flora_zalo_group_chat_id', ''));

    if (empty($token) || empty($targetChat)) {
        return false;
    }

    $url = "https://bot-api.zaloplatforms.com/bot" . $token . "/sendMessage";
    $payload = wp_json_encode([
        "chat_id" => $targetChat,
        "text"    => $text
    ], JSON_UNESCAPED_UNICODE);

    wp_remote_post($url, [
        'timeout'     => 10,
        'headers'     => ['Content-Type' => 'application/json; charset=utf-8'],
        'body'        => $payload,
        'data_format' => 'body',
        'sslverify'   => false
    ]);

    return true;
}
