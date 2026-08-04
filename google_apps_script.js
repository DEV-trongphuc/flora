/**
 * =========================================================================
 * GOOGLE APPS SCRIPT - FLORA SYSTEM (WEBSITE SUBMIT & AUTOMATED EMAIL CAMPAIGNS)
 * =========================================================================
 * 
 * SƠ ĐỒ HOẠT ĐỘNG:
 * 1. Nhận Lead từ Website (flora.html): Ghi nhận trực tiếp vào sheet "DATA" (Giữ nguyên bản gốc 100%).
 * 2. Chiến dịch Email (Xác nhận, Remind 2 ngày, Countdown 1 ngày): Chạy trên sheet "Ngày hội implant (v2)".
 * 
 * Hướng dẫn thiết lập chi tiết:
 * 1. Mở Trang tính Google Sheets của bạn.
 * 2. Chọn "Tiện ích mở rộng" (Extensions) -> "Apps Script".
 * 3. Xóa hết code mặc định và dán toàn bộ mã nguồn bên dưới vào.
 * 4. Điền ID Bảng tính của bạn vào biến SPREADSHEET_ID ở dưới (hoặc để trống nếu script chạy trực tiếp bên trong Trang tính).
 * 
 * CÁCH THIẾT LẬP WEB APP:
 * 1. Ở góc trên bên phải màn hình Apps Script, nhấp vào nút "Triển khai" (Deploy) -> chọn "Triển khai mới" (New deployment).
 * 2. Nhấp vào biểu tượng bánh răng cài đặt -> Chọn "Ứng dụng web" (Web app).
 * 3. Cấu hình thông số:
 *    - Mô tả: "Flora System Web App"
 *    - Thực thi dưới danh nghĩa (Execute as): "Tôi" (Me - địa chỉ email của bạn)
 *    - Ai có quyền truy cập (Who has access): "Bất kỳ ai" (Anyone)
 * 4. Nhấp "Triển khai" (Deploy). Cấp quyền Gmail & Google Sheets nếu hệ thống yêu cầu.
 * 5. SAO CHÉP URL ỨNG DỤNG WEB (Web app URL) nhận được.
 * 6. DÁN URL ĐÓ vào biến WEB_APP_URL ở ngay dòng phía dưới.
 * 7. Nhấp nút Lưu (Save - biểu tượng ổ đĩa hoặc Ctrl + S).
 * 8. Triển khai lại phiên bản mới (Deploy -> Manage deployments -> Edit -> chọn Version là "New version" -> Deploy) để áp dụng link webapp mới.
 * 
 * CÁCH THIẾT LẬP TRIGGER TỰ ĐỘNG GỬI EMAIL (Cho sheet "Ngày hội implant (v2)"):
 * 
 * A. Tự động gửi Email XÁC NHẬN ngay khi có Lead mới (Zapier/Make/Facebook Ads đẩy vào):
 *    - Nhấp vào biểu tượng Đồng hồ (Triggers / Kích hoạt) ở thanh menu bên trái.
 *    - Chọn "+ Add Trigger" ở góc dưới bên phải.
 *    - Chọn hàm chạy: "onChangeTrigger".
 *    - Chọn nguồn sự kiện: "Từ trang tính" (From spreadsheet).
 *    - Chọn loại sự kiện: "Khi thay đổi" (On change).
 *    - Nhấp "Lưu" (Save).
 * 
 * B. Tự động quét gửi Email NHẮC NHỞ (Remind trước 2 ngày) và ĐẾM NGƯỢC (Countdown trước 1 ngày):
 *    - Chọn "+ Add Trigger" ở góc dưới bên phải.
 *    - Chọn hàm chạy: "dailyEventEmailDispatcher".
 *    - Chọn nguồn sự kiện: "Theo thời gian" (Time-driven).
 *    - Chọn loại bộ kích hoạt dựa trên thời gian: "Bộ kích hoạt theo ngày" (Day timer).
 *    - Chọn khoảng thời gian trong ngày: "8:00 AM đến 9:00 AM" (Hoặc khung giờ bạn muốn hệ thống tự động quét gửi).
 *    - Nhấp "Lưu" (Save).
 */

// ĐIỀN ID BẢNG TÍNH GOOGLE SHEETS CỦA BẠN VÀO ĐÂY (Để trống nếu script được tạo trực tiếp trong Trang tính đó)
const SPREADSHEET_ID = "1H8CVdQAlyuggCdt0YC1KUmJMQvUbIleGLcg_bXnwN-4"; 

// TÊN CÁC SHEET QUẢN LÝ DỮ LIỆU
const SHEET_NAME_WEBSITE = "DATA";                // Sheet nhận dữ liệu từ Form Website (flora.html)
const SHEET_NAME_LEADS = "Ngày hội implant (v2)"; // Sheet nhận dữ liệu Facebook Ads & Chạy chiến dịch email tự động

// DÁN WEB APP URL CỦA BẠN SAU KHI TRIỂN KHAI VÀO ĐÂY ĐỂ THEO DÕI LƯỢT MỞ THƯ
const WEB_APP_URL = "https://script.google.com/macros/s/AKfycbwynAqJc_hyqWmMvFz1QEf6c-8ZKyIlexvg_NCOM2fZ8oGLa579QV3hTMJ0BTySuR6YiA/exec";

// Tên các cột quản lý trạng thái gửi email (Cho Sheet Leads Facebook)
const CONFIRM_STATUS_HEADER = "Email Status";      // Thư xác nhận đăng ký thành công
const REMIND_STATUS_HEADER = "Remind Status";      // Thư nhắc nhở trước 2 ngày (23/07/2026)
const COUNTDOWN_STATUS_HEADER = "Countdown Status";   // Thư đếm ngược trước 1 ngày (24/07/2026)

// Tên các cột quản lý trạng thái mở email (Cho Sheet Leads Facebook)
const CONFIRM_OPENED_HEADER = "Email Opened";      // Khách hàng mở thư xác nhận
const REMIND_OPENED_HEADER = "Remind Opened";      // Khách hàng mở thư nhắc hẹn
const COUNTDOWN_OPENED_HEADER = "Countdown Opened";   // Khách hàng mở thư đếm ngược

/**
 * =========================================================================
 * 1. PHẦN TIẾP NHẬN LEAD TỪ WEBSITE (GỬI LÊN SHEET "DATA") - BẢN GỐC 100%
 * =========================================================================
 */
function doPost(e) {
  try {
    let ss;
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
    
    let sheet = ss.getSheetByName(SHEET_NAME_WEBSITE);
    const headers = [
      "Thời gian",
      "Loại form",
      "Họ và tên",
      "Số điện thoại",
      "Email",
      "Phòng khám",
      "Thành phố",
      "Nhu cầu quan tâm",
      "Ngày hẹn",
      "Giờ hẹn",
      "Ghi chú"
    ];
    
    if (!sheet) {
      sheet = ss.insertSheet(SHEET_NAME_WEBSITE);
      sheet.getRange(1, 1, 1, headers.length)
           .setValues([headers])
           .setFontWeight("bold")
           .setBackground("#c59e6c")
           .setFontColor("#ffffff")
           .setHorizontalAlignment("center");
      sheet.setFrozenRows(1);
    }
    
    const data = JSON.parse(e.postData.contents);
    const timestamp = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd HH:mm:ss");
    
    // Thêm dòng mới vào sheet DATA theo đúng định dạng cũ
    sheet.appendRow([
      timestamp,
      data.formType || "",
      data.fullname || "",
      data.phone || "",
      data.email || "",
      data.clinic || "",
      data.city || "",
      data.interest || "",
      data.date || "",
      data.timeSlot || "",
      data.note || ""
    ]);
    
    sheet.autoResizeColumns(1, headers.length);
    
    return ContentService.createTextOutput(JSON.stringify({ 
      "result": "success", 
      "message": "Lead appended successfully to DATA sheet"
    }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error("Lỗi doPost Google Apps Script: " + error.toString());
    return ContentService.createTextOutput(JSON.stringify({ "result": "error", "error": error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * =========================================================================
 * 2. PHẦN CHIẾN DỊCH EMAIL TỰ ĐỘNG (CHẠY TRÊN SHEET "Ngày hội implant (v2)")
 * =========================================================================
 */

/**
 * Lấy sơ đồ vị trí các cột dựa trên hàng tiêu đề đầu tiên (1-based index)
 */
function getHeaderMapping(sheet) {
  const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  const mapping = {};
  for (let i = 0; i < headers.length; i++) {
    const headerName = headers[i].toString().trim().toLowerCase();
    if (headerName) {
      mapping[headerName] = i + 1;
    }
  }
  return mapping;
}

/**
 * Lấy hoặc tạo cột quản lý trạng thái nếu chưa có
 */
function getOrAddStatusColumn(sheet, columnName, mapping, headerBgColor) {
  const lowerName = columnName.toLowerCase();
  let colIndex = mapping[lowerName];
  if (!colIndex) {
    const nextCol = sheet.getLastColumn() + 1;
    sheet.getRange(1, nextCol).setValue(columnName)
         .setFontWeight("bold")
         .setBackground(headerBgColor || "#0033a3") // Royal Blue mặc định
         .setFontColor("#ffffff")
         .setHorizontalAlignment("center");
    colIndex = nextCol;
    mapping[lowerName] = nextCol;
  }
  return colIndex;
}

/**
 * Hàm kiểm tra sự kiện theo ngày để tự động gửi Remind hoặc Countdown
 */
function dailyEventEmailDispatcher() {
  const vietnamTime = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd");
  console.log("Hàm Dispatcher hàng ngày đang kiểm tra lịch gửi vào lúc: " + vietnamTime);
  
  // Ngày hội chính thức diễn ra ngày 25/07/2026
  // Gửi email REMIND trước 2 ngày -> Ngày 23/07/2026
  if (vietnamTime === "2026-07-23") {
    console.log("Hôm nay là 23/07/2026. Bắt đầu tự động gửi email REMIND...");
    scanAndSendRemindEmails();
  }
  // Gửi email COUNTDOWN trước 1 ngày -> Ngày 24/07/2026
  else if (vietnamTime === "2026-07-24") {
    console.log("Hôm nay là 24/07/2026. Bắt đầu tự động gửi email COUNTDOWN...");
    scanAndSendCountdownEmails();
  } else {
    console.log("Hôm nay là " + vietnamTime + ". Không phải ngày gửi email Remind (23/07) hay Countdown (24/07).");
  }
}

// -------------------------------------------------------------------------
// A. QUÉT & GỬI EMAIL THÔNG THƯỜNG (CHƯA ĐƯỢC GỬI)
// -------------------------------------------------------------------------

function scanAndSendEmails() {
  sendCampaignEmails(SHEET_NAME_LEADS, CONFIRM_STATUS_HEADER, sendEmailHTML, "#0033a3");
}

function scanAndSendRemindEmails() {
  sendCampaignEmails(SHEET_NAME_LEADS, REMIND_STATUS_HEADER, sendRemindEmailHTML, "#0493f1");
}

function scanAndSendCountdownEmails() {
  sendCampaignEmails(SHEET_NAME_LEADS, COUNTDOWN_STATUS_HEADER, sendCountdownEmailHTML, "#0135a6");
}

// -------------------------------------------------------------------------
// B. RESET CỘT TRẠNG THÁI ĐỂ GỬI LẠI HÀNG LOẠT (GỬI LẠI NGƯỜI CŨ)
// -------------------------------------------------------------------------

function forceResendConfirmationToAll() {
  resetAndResend(SHEET_NAME_LEADS, CONFIRM_STATUS_HEADER, scanAndSendEmails);
}

function forceResendRemindToAll() {
  resetAndResend(SHEET_NAME_LEADS, REMIND_STATUS_HEADER, scanAndSendRemindEmails);
}

function forceResendCountdownToAll() {
  resetAndResend(SHEET_NAME_LEADS, COUNTDOWN_STATUS_HEADER, scanAndSendCountdownEmails);
}

// -------------------------------------------------------------------------
// C. LOGIC LÕI GỬI THƯ CHIẾN DỊCH
// -------------------------------------------------------------------------

function sendCampaignEmails(sheetTargetName, statusHeaderName, emailSendingFunc, headerColor) {
  let ss;
  try {
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
  } catch (e) {
    console.error("Lỗi khi mở bảng tính: " + e.toString());
    return;
  }

  const sheet = ss.getSheetByName(sheetTargetName);
  if (!sheet) {
    console.error("Không tìm thấy Sheet: " + sheetTargetName);
    return;
  }

  const lastRow = sheet.getLastRow();
  if (lastRow < 2) return;

  const mapping = getHeaderMapping(sheet);
  const statusCol = getOrAddStatusColumn(sheet, statusHeaderName, mapping, headerColor);
  
  const emailCol = mapping["email"];
  const nameCol = mapping["full_name"];
  const phoneCol = mapping["phone_number"];
  const conditionCol = mapping["tình_trạng_mất_răng_của_bạn"];
  const detailCol = mapping["mô_tả_chi_tiết_về_tình_trạng_răng_của_bạn"];
  const cityCol = mapping["city"];

  if (!emailCol) {
    console.error("Không tìm thấy cột 'email' trong bảng tính của sheet: " + sheetTargetName);
    return;
  }

  const dataRange = sheet.getRange(2, 1, lastRow - 1, sheet.getLastColumn());
  const data = dataRange.getValues();

  let count = 0;
  for (let i = 0; i < data.length; i++) {
    const row = data[i];
    const rowNum = i + 2;

    const emailVal = emailCol ? row[emailCol - 1].toString().trim() : "";
    const nameVal = nameCol ? row[nameCol - 1].toString().trim() : "";
    const phoneVal = phoneCol ? row[phoneCol - 1].toString().trim() : "";
    const conditionVal = conditionCol ? row[conditionCol - 1].toString().trim() : "";
    const detailVal = detailCol ? row[detailCol - 1].toString().trim() : "";
    const cityVal = cityCol ? row[cityCol - 1].toString().trim() : "";
    const statusVal = statusCol ? row[statusCol - 1].toString().trim() : "";

    if (emailVal && emailVal.indexOf("@") !== -1 && statusVal !== "Sent") {
      try {
        emailSendingFunc(emailVal, nameVal, phoneVal, conditionVal, detailVal, cityVal, rowNum);
        
        sheet.getRange(rowNum, statusCol)
             .setValue("Sent")
             .setBackground("#d4edda")
             .setFontColor("#155724")
             .setFontWeight("bold");
        
        count++;
        Utilities.sleep(150);
      } catch (err) {
        console.error("Lỗi dòng " + rowNum + " khi gửi chiến dịch " + statusHeaderName + ": " + err.toString());
        sheet.getRange(rowNum, statusCol)
             .setValue("Error: " + err.toString())
             .setBackground("#f8d7da")
             .setFontColor("#721c24");
      }
    }
  }
  console.log("Đã quét xong chiến dịch " + statusHeaderName + " trên sheet " + sheetTargetName + ". Gửi thành công: " + count + " email.");
}

function resetAndResend(sheetTargetName, statusHeaderName, scanFunc) {
  let ss;
  try {
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
  } catch (e) {
    console.error("Lỗi khi mở bảng tính: " + e.toString());
    return;
  }

  const sheet = ss.getSheetByName(sheetTargetName);
  if (!sheet) return;

  const lastRow = sheet.getLastRow();
  if (lastRow < 2) return;

  const mapping = getHeaderMapping(sheet);
  const statusCol = getOrAddStatusColumn(sheet, statusHeaderName, mapping);

  console.log("Đang reset cột trạng thái gửi: " + statusHeaderName);
  
  sheet.getRange(2, statusCol, lastRow - 1, 1)
       .clearContent()
       .setBackground(null)
       .setFontColor(null)
       .setFontWeight("normal");
  
  console.log("Reset thành công. Đang tiến hành quét gửi lại...");
  scanFunc();
}

// -------------------------------------------------------------------------
// D. THEO DÕI MỞ THƯ (EMAIL OPEN TRACKING) - CHỈ ÁP DỤNG TRÊN SHEET LEADS
// -------------------------------------------------------------------------

/**
 * Xử lý GET request (Dùng làm Tracking Pixel chạy khi người nhận mở email)
 */
function doGet(e) {
  try {
    const action = e.parameter.action;
    if (action === "trackOpen") {
      const email = e.parameter.email;
      const row = e.parameter.row;
      const campaign = e.parameter.campaign;
      
      if (email && campaign) {
        markEmailAsOpened(row, email, campaign);
      }
    }
  } catch (err) {
    console.error("Lỗi khi xử lý doGet: " + err.toString());
  }
  
  return ContentService.createTextOutput("")
                       .setMimeType(ContentService.MimeType.TEXT);
}

/**
 * Ghi nhận mở thư vào trang tính Google Sheets (Sheet Leads)
 */
function markEmailAsOpened(rowNum, email, campaign) {
  let ss;
  try {
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
  } catch (e) {
    console.error("Lỗi trackOpen không mở được Sheets: " + e.toString());
    return;
  }

  const sheet = ss.getSheetByName(SHEET_NAME_LEADS);
  if (!sheet) return;

  const mapping = getHeaderMapping(sheet);
  
  let openedHeader = "";
  let headerColor = "";
  if (campaign === "confirm") {
    openedHeader = CONFIRM_OPENED_HEADER;
    headerColor = "#0033a3";
  } else if (campaign === "remind") {
    openedHeader = REMIND_OPENED_HEADER;
    headerColor = "#0493f1";
  } else if (campaign === "countdown") {
    openedHeader = COUNTDOWN_OPENED_HEADER;
    headerColor = "#0135a6";
  } else {
    return;
  }
  
  const openedCol = getOrAddStatusColumn(sheet, openedHeader, mapping, headerColor);
  
  let targetRow = parseInt(rowNum);
  const emailCol = mapping["email"];
  
  if (emailCol && targetRow > 1 && targetRow <= sheet.getLastRow()) {
    const currentEmail = sheet.getRange(targetRow, emailCol).getValue().toString().trim();
    if (currentEmail.toLowerCase() !== email.toLowerCase()) {
      const emails = sheet.getRange(2, emailCol, sheet.getLastRow() - 1, 1).getValues();
      for (let j = 0; j < emails.length; j++) {
        if (emails[j][0].toString().trim().toLowerCase() === email.toLowerCase()) {
          targetRow = j + 2;
          break;
        }
      }
    }
  }
  
  if (targetRow > 1 && targetRow <= sheet.getLastRow()) {
    const cell = sheet.getRange(targetRow, openedCol);
    if (!cell.getValue()) {
      const timestamp = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd HH:mm:ss");
      cell.setValue("Opened: " + timestamp)
          .setBackground("#fff3cd")
          .setFontColor("#856404")
          .setFontWeight("bold");
      console.log("Ghi nhận mở thư thành công cho: " + email + " (" + campaign + ")");
    }
  }
}

// -------------------------------------------------------------------------
// E. CÁC MẪU EMAIL HTML CHI TIẾT
// -------------------------------------------------------------------------

/**
 * 1. EMAIL XÁC NHẬN ĐĂNG KÝ
 */
function sendEmailHTML(email, name, phone, condition, details, city, rowNum) {
  const subject = "Xác nhận đăng ký tham dự Ngày hội Implant 2026";
  const logoUrl = "https://nhakhoaflora.com/wp-content/uploads/2022/05/cropped-LOGO-FLORA1-3-192x192.png";
  
  const cleanName = name || "Quý khách hàng";
  const cleanPhone = phone || "Chưa cung cấp";
  const cleanCondition = condition || "Chưa cập nhật";
  const cleanCity = city || "Chưa cập nhật";

  let trackingPixel = "";
  if (WEB_APP_URL && WEB_APP_URL !== "ĐIỀN_WEB_APP_URL_CỦA_BẠN_VÀO_ĐÂY") {
    const trackUrl = WEB_APP_URL + "?action=trackOpen&email=" + encodeURIComponent(email) + "&row=" + rowNum + "&campaign=confirm";
    trackingPixel = `<img src="${trackUrl}" width="1" height="1" style="display:none; width:1px; height:1px; border:none;" alt="" />`;
  }

  const htmlBody = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>${subject}</title>
    </head>
    <body style="font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7fa; color: #0f172a;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
          <td align="center">
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 51, 163, 0.08); border: 1px solid #e2e8f0;">
              <tr>
                <td style="background: linear-gradient(135deg, #0493f1 0%, #0135a6 100%); padding: 35px 30px; text-align: center;">
                  <img src="${logoUrl}" alt="Nha Khoa Flora Logo" style="height: 55px; width: auto; margin-bottom: 12px; display: inline-block;">
                  <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; text-transform: uppercase;">Xác nhận đăng ký tham dự</h1>
                  <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Ngày hội Implant 2026</p>
                </td>
              </tr>
              <tr>
                <td style="padding: 35px 30px; background-color: #ffffff;">
                  <p style="font-size: 15px; font-weight: bold; color: #0a1931; margin-top: 0; margin-bottom: 15px;">Kính gửi Quý Khách hàng ${name ? name : ""},</p>
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 15px;">
                    Nha khoa Flora xin chân thành cảm ơn Quý Khách đã quan tâm và đăng ký tham dự Ngày hội Implant 2026 với chủ đề: <strong>Vững vàng ăn nhai, tự tin sống khỏe</strong>.
                  </p>
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px;">
                    Chúng tôi xin xác nhận thông tin đăng ký của Quý Khách đã được ghi nhận thành công.
                  </p>
                  
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(4, 147, 241, 0.03); border: 1px solid rgba(4, 147, 241, 0.15); border-radius: 10px; padding: 18px 20px; margin-bottom: 22px;">
                    <tr style="font-size: 14px;">
                      <td style="padding: 6px 0; color: #0033a3; font-weight: bold; width: 90px; vertical-align: top;">📅 Thời gian:</td>
                      <td style="padding: 6px 0; color: #0f172a; font-weight: 600;">25/07/2026 | 8:30 - 13:00</td>
                    </tr>
                    <tr style="font-size: 14px;">
                      <td style="padding: 6px 0; color: #0033a3; font-weight: bold; vertical-align: top;">📍 Địa điểm:</td>
                      <td style="padding: 6px 0; color: #0f172a; font-weight: 500; line-height: 1.4;">326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, Thành phố Hồ Chí Minh</td>
                    </tr>
                  </table>
                  
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-left: 4px solid #64748b; border-radius: 6px; padding: 15px 18px; margin-bottom: 22px;">
                    <tr>
                      <td style="font-size: 13px; font-weight: bold; color: #475569; padding-bottom: 6px; text-transform: uppercase;">Thông tin ghi nhận:</td>
                    </tr>
                    <tr style="font-size: 13px; color: #334155;">
                      <td style="padding: 2px 0;">• <strong>Họ tên:</strong> ${cleanName}</td>
                    </tr>
                    <tr style="font-size: 13px; color: #334155;">
                      <td style="padding: 2px 0;">• <strong>SĐT:</strong> ${cleanPhone}</td>
                    </tr>
                    <tr style="font-size: 13px; color: #334155;">
                      <td style="padding: 2px 0;">• <strong>Tình trạng:</strong> ${cleanCondition}</td>
                    </tr>
                  </table>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #b45309; margin-bottom: 22px; background-color: #fffbeb; padding: 12px; border-radius: 8px; border: 1px solid #fef3c7;">
                    💡 <strong>Lưu ý:</strong> Để thuận tiện cho việc đón tiếp, Quý Khách vui lòng có mặt trước giờ bắt đầu khoảng 15 phút.
                  </p>
                  
                  <p style="font-size: 14px; color: #334155; margin-bottom: 15px;">
                    Nếu cần hỗ trợ thêm thông tin, Quý Khách vui lòng liên hệ trực tiếp:
                  </p>
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 25px; font-size: 14px;">
                    <tr>
                      <td style="padding: 4px 0;">🔗 <strong>Fanpage:</strong> <a href="https://www.facebook.com/nhakhoaflora" target="_blank" style="color: #0493f1; text-decoration: none;">fb.com/nhakhoaflora</a></td>
                    </tr>
                    <tr>
                      <td style="padding: 4px 0;">📞 <strong>Hotline:</strong> 028 7305 8999 - 0902535068 (Zalo)</td>
                    </tr>
                  </table>
                  
                  <p style="font-size: 15px; font-weight: bold; color: #0a1931; text-align: center; margin-bottom: 0;">
                    Hẹn gặp Quý Khách tại Ngày hội Implant 2026!
                  </p>
                </td>
              </tr>
              <tr>
                <td style="background-color: #0a1931; color: #cbd5e1; padding: 30px 25px; text-align: center; font-size: 12px;">
                  <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 8px;">NHA KHOA FLORA</strong>
                  <div>📍 Địa chỉ: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, Quận 3, TP.HCM</div>
                  <div>🌐 Website: nhakhoaflora.com</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      ${trackingPixel}
    </body>
    </html>
  `;
  
  MailApp.sendEmail({
    to: email,
    subject: subject,
    htmlBody: htmlBody,
    name: "Nha Khoa Flora",
    replyTo: "support@floraclinic.vn"
  });
}

/**
 * 2. EMAIL REMIND (Timeline HTML)
 */
function sendRemindEmailHTML(email, name, phone, condition, details, city, rowNum) {
  const subject = "Ngày hội Implant 2026 | Những điều Quý Khách cần biết trước khi tham dự";
  const logoUrl = "https://nhakhoaflora.com/wp-content/uploads/2022/05/cropped-LOGO-FLORA1-3-192x192.png";
  
  let trackingPixel = "";
  if (WEB_APP_URL && WEB_APP_URL !== "ĐIỀN_WEB_APP_URL_CỦA_BẠN_VÀO_ĐÂY") {
    const trackUrl = WEB_APP_URL + "?action=trackOpen&email=" + encodeURIComponent(email) + "&row=" + rowNum + "&campaign=remind";
    trackingPixel = `<img src="${trackUrl}" width="1" height="1" style="display:none; width:1px; height:1px; border:none;" alt="" />`;
  }

  const htmlBody = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>${subject}</title>
    </head>
    <body style="font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7fa; color: #0f172a;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
          <td align="center">
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 51, 163, 0.08); border: 1px solid #e2e8f0;">
              <tr>
                <td style="background: linear-gradient(135deg, #0493f1 0%, #0135a6 100%); padding: 35px 30px; text-align: center;">
                  <img src="${logoUrl}" alt="Nha Khoa Flora Logo" style="height: 55px; width: auto; margin-bottom: 12px; display: inline-block;">
                  <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; text-transform: uppercase;">Thông báo nhắc hẹn</h1>
                  <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Ngày hội Implant 2026</p>
                </td>
              </tr>
              <tr>
                <td style="padding: 35px 30px; background-color: #ffffff;">
                  <p style="font-size: 15px; font-weight: bold; color: #0a1931; margin-top: 0; margin-bottom: 15px;">Kính gửi Quý Khách hàng ${name ? name : ""},</p>
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 15px;">
                    Nha khoa Flora xin trân trọng thông báo, Ngày hội Implant 2026 với chủ đề <strong>"Vững vàng ăn nhai, tự tin sống khỏe"</strong> sẽ chính thức diễn ra vào:
                  </p>
                  
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: rgba(0, 51, 163, 0.02); border-left: 4px solid #0033a3; padding: 15px; margin-bottom: 22px; font-size: 14px; line-height: 1.5;">
                    <tr><td style="padding: 3px 0;">📅 <strong>Thời gian:</strong> 08:30 – 13:00 | Ngày 25/07/2026</td></tr>
                    <tr><td style="padding: 3px 0;">📍 <strong>Địa điểm:</strong> 326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, Thành phố Hồ Chí Minh</td></tr>
                    <tr><td style="padding: 3px 0;">⏰ <strong>Thời gian check-in:</strong> 08:30 - 09:00</td></tr>
                  </table>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 15px;">
                    Để Quý Khách thuận tiện theo dõi, Nha khoa Flora xin gửi kèm timeline chương trình với những nội dung nổi bật xuyên suốt sự kiện:
                  </p>
                  
                  <!-- BEAUTIFUL HTML TIMELINE INFO GRAPHIC -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-radius: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 22px; margin-bottom: 25px;">
                    <tr>
                      <td align="center" style="padding-bottom: 18px; border-bottom: 2px solid #0493f1;">
                        <span style="font-size: 14px; font-weight: bold; color: #0033a3; text-transform: uppercase; letter-spacing: 0.5px;">📅 TIMELINE CHƯƠNG TRÌNH SỰ KIỆN</span>
                      </td>
                    </tr>
                    <tr>
                      <td style="padding-top: 15px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size: 13px; line-height: 1.5; color: #334155;">
                          <tr>
                            <td width="100" style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #0493f1; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">08:30 - 09:15</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #0a1931; font-weight: 600;">Đón khách - Hướng dẫn thăm khám/chụp phim đợt 01</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #0033a3; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">09:15 - 09:30</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #0a1931; font-weight: 600;">Khai mạc chương trình</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #0033a3; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">09:30 - 10:00</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155;">Gặp gỡ, giao lưu và giải đáp thắc mắc cùng BS CKI Nguyễn Đắc Minh - Chuyên gia cấy ghép Implant và phục hình thẩm mỹ</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #64748b; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">10:00 - 10:10</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #64748b; font-style: italic;">Nghỉ giải lao & Teabreak</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #0033a3; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">10:10 - 10:40</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155;">Gặp gỡ, giao lưu và demo mô hình Implant cùng đại diện hãng ImplantSwiss</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #64748b; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">10:40 - 10:50</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155;">Teabreak & Hướng dẫn thăm khám/chụp phim đợt 02</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #0033a3; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">10:50 - 11:20</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #334155; font-weight: 600;">Phân tích phim - Q&A cùng Bác sĩ Đắc Minh</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; vertical-align: top;"><span style="background-color: #ef4444; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">11:20 - 11:30</span></td>
                            <td style="padding: 8px 0 8px 10px; border-bottom: 1px solid #f1f5f9; color: #0a1931; font-weight: bold;">🎁 Công bố kết quả giải thưởng</td>
                          </tr>
                          <tr>
                            <td style="padding: 8px 0; vertical-align: top;"><span style="background-color: #ef4444; color: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: bold; font-size: 11px;">Từ 11:30</span></td>
                            <td style="padding: 8px 0 0 10px; color: #0033a3; font-weight: bold;">Kết thúc chương trình, hẹn lịch thăm khám/điều trị và tư vấn chuyên sâu sau chương trình</td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 25px;">
                    Nếu cần thêm thông tin hoặc hỗ trợ, Quý Khách vui lòng liên hệ ngay với Nha khoa Flora.
                  </p>
                  
                  <p style="font-size: 15px; font-weight: bold; color: #0033a3; text-align: center; margin-bottom: 0;">
                    Hẹn gặp Quý Khách tại Ngày hội Implant 2026!
                  </p>
                </td>
              </tr>
              <tr>
                <td style="background-color: #0a1931; color: #cbd5e1; padding: 30px 25px; text-align: center; font-size: 12px; line-height: 1.6;">
                  <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 8px;">NHA KHOA FLORA</strong>
                  <div>📍 Địa chỉ: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, Quận 3, TP.HCM</div>
                  <div>📞 Hotline: 028 7305 8999 - 0902535068 (Zalo)</div>
                  <div>🌐 Website: nhakhoaflora.com</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      ${trackingPixel}
    </body>
    </html>
  `;
  
  MailApp.sendEmail({
    to: email,
    subject: subject,
    htmlBody: htmlBody,
    name: "Nha Khoa Flora",
    replyTo: "support@floraclinic.vn"
  });
}

/**
 * 3. EMAIL COUNTDOWN (Bản đồ chỉ đường HTML)
 */
function sendCountdownEmailHTML(email, name, phone, condition, details, city, rowNum) {
  const subject = "Thông tin tham dự Ngày hội Implant 2026 | Thời gian & Hướng dẫn di chuyển";
  const logoUrl = "https://nhakhoaflora.com/wp-content/uploads/2022/05/cropped-LOGO-FLORA1-3-192x192.png";
  
  let trackingPixel = "";
  if (WEB_APP_URL && WEB_APP_URL !== "ĐIỀN_WEB_APP_URL_CỦA_BẠN_VÀO_ĐÂY") {
    const trackUrl = WEB_APP_URL + "?action=trackOpen&email=" + encodeURIComponent(email) + "&row=" + rowNum + "&campaign=countdown";
    trackingPixel = `<img src="${trackUrl}" width="1" height="1" style="display:none; width:1px; height:1px; border:none;" alt="" />`;
  }

  const htmlBody = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>${subject}</title>
    </head>
    <body style="font-family: 'Segoe UI', Tahoma, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7fa; color: #0f172a;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
          <td align="center">
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 51, 163, 0.08); border: 1px solid #e2e8f0;">
              <tr>
                <td style="background: linear-gradient(135deg, #0493f1 0%, #0135a6 100%); padding: 35px 30px; text-align: center;">
                  <img src="${logoUrl}" alt="Nha Khoa Flora Logo" style="height: 55px; width: auto; margin-bottom: 12px; display: inline-block;">
                  <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; text-transform: uppercase;">Hướng dẫn di chuyển</h1>
                  <p style="color: #e2e8f0; margin: 5px 0 0 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Sự kiện diễn ra vào ngày mai</p>
                </td>
              </tr>
              <tr>
                <td style="padding: 35px 30px; background-color: #ffffff;">
                  <p style="font-size: 15px; font-weight: bold; color: #0a1931; margin-top: 0; margin-bottom: 15px;">Kính gửi Quý Khách hàng ${name ? name : ""},</p>
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 15px;">
                    Ngày hội Implant Flora 2026 sẽ chính thức diễn ra trong 2 ngày nữa. Nha khoa Flora rất hân hạnh được chào đón và đồng hành cùng Quý Khách trong sự kiện đặc biệt này.
                  </p>
                  <p style="font-size: 14px; line-height: 1.6; color: #b45309; margin-bottom: 22px; background-color: #fffbeb; padding: 12px; border-radius: 8px; border: 1px solid #fef3c7;">
                    ⚠️ <strong>Lưu ý quan trọng:</strong> Sự kiện sẽ bắt đầu làm thủ tục check-in đón tiếp từ <strong>8:30 - 9:00</strong>.
                  </p>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 15px;">
                    Quý Khách vui lòng tham khảo bản đồ và hướng dẫn di chuyển dưới đây để thuận tiện tìm đường đến địa điểm tổ chức:
                  </p>
                  
                  <!-- BEAUTIFUL HTML MAP INFO CARD -->
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-radius: 12px; background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 22px; margin-bottom: 25px; text-align: center;">
                    <tr>
                      <td>
                        <div style="font-size: 12px; font-weight: bold; color: #0033a3; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">📍 ĐỊA ĐIỂM TỔ CHỨC</div>
                        <div style="font-size: 15px; font-weight: bold; color: #0a1931; margin-bottom: 5px;">NHA KHOA FLORA</div>
                        <div style="font-size: 13px; color: #334155; margin-bottom: 15px; line-height: 1.4;">
                          326 Nguyễn Thị Minh Khai, Phường Bàn Cờ, Quận 3, TP.HCM<br>
                          <span style="font-size: 11px; color: #64748b; font-style: italic;">(Gần ngã tư Nguyễn Thị Minh Khai - Cao Thắng, có bãi giữ xe máy & xe hơi miễn phí)</span>
                        </div>
                        
                        <!-- Stylized Map Placeholder using pure HTML/CSS -->
                        <a href="https://maps.app.goo.gl/Wvxnwksz5Px4weCs5" target="_blank" style="display: block; text-decoration: none; border-radius: 8px; overflow: hidden; border: 1px solid #cbd5e1; margin-bottom: 15px;">
                          <div style="height: 140px; background-color: #e5e7eb; background-image: radial-gradient(#d1d5db 2px, transparent 2px), linear-gradient(90deg, transparent 40px, #9ca3af 41px, transparent 42px), linear-gradient(0deg, transparent 70px, #9ca3af 71px, transparent 72px); background-size: 12px 12px, 100% 100%, 100% 100%; position: relative; display: flex; align-items: center; justify-content: center;">
                            <div style="background-color: #ffffff; padding: 8px 14px; border-radius: 20px; box-shadow: 0 4px 10px rgba(0,51,163,0.1); display: inline-flex; align-items: center; border: 1.5px solid #0493f1;">
                              <span style="font-size: 14px; margin-right: 4px;">📍</span>
                              <span style="font-size: 11px; font-weight: 700; color: #0033a3;">Nha Khoa Flora (Quận 3)</span>
                            </div>
                            <div style="position: absolute; bottom: 5px; right: 5px; background-color: rgba(10, 25, 49, 0.7); color: #ffffff; padding: 2px 5px; border-radius: 3px; font-size: 9px;">Bấm để xem bản đồ</div>
                          </div>
                        </a>
                        
                        <a href="https://maps.app.goo.gl/Wvxnwksz5Px4weCs5" target="_blank" style="display: inline-block; background-color: #0493f1; color: #ffffff !important; text-decoration: none; padding: 10px 22px; border-radius: 30px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                          🗺️ Chỉ Đường Google Maps
                        </a>
                      </td>
                    </tr>
                  </table>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 25px;">
                    Để việc đón tiếp được chu đáo, Quý Khách vui lòng đến trước giờ khai mạc khoảng 15 phút để được hỗ trợ check-in và hoàn tất thủ tục ghi danh.
                  </p>
                  
                  <p style="font-size: 14px; line-height: 1.6; color: #334155; margin-bottom: 20px;">
                    Nha khoa Flora rất mong được gặp Quý Khách tại Ngày hội Implant 2026 và kính chúc Quý Khách có những trải nghiệm ý nghĩa tại chương trình.
                  </p>
                  
                  <p style="font-size: 14px; color: #334155; margin-bottom: 0; font-weight: bold; text-align: center;">
                    Trân trọng kính mời,<br>
                    <span style="color: #0033a3; font-size: 15px;">NHA KHOA FLORA</span>
                  </p>
                </td>
              </tr>
              <tr>
                <td style="background-color: #0a1931; color: #cbd5e1; padding: 30px 25px; text-align: center; font-size: 12px; line-height: 1.6;">
                  <strong style="color: #ffffff; font-size: 14px; display: block; margin-bottom: 8px;">NHA KHOA FLORA</strong>
                  <div>📍 Địa chỉ: 326 Nguyễn Thị Minh Khai, P. Bàn Cờ, Quận 3, TP.HCM</div>
                  <div>📞 Hotline: 028 7305 8999 - 0902535068 (Zalo)</div>
                  <div>🌐 Website: nhakhoaflora.com</div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      ${trackingPixel}
    </body>
    </html>
  `;
  
  MailApp.sendEmail({
    to: email,
    subject: subject,
    htmlBody: htmlBody,
    name: "Nha Khoa Flora",
    replyTo: "support@floraclinic.vn"
  });
}

/**
 * Trigger tự động kích hoạt mỗi khi trang tính có sự thay đổi cấu trúc/dòng (như Zapier/Make đẩy dòng mới vào)
 */
function onChangeTrigger(e) {
  console.log("Kích hoạt onChangeTrigger bởi sự kiện thay đổi: " + (e ? e.changeType : "Thủ công"));
  if (!e || e.changeType === "INSERT_ROW" || e.changeType === "EDIT" || e.changeType === "OTHER") {
    scanAndSendEmails();
  }
}
