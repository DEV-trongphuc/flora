/**
 * =========================================================================
 * GOOGLE APPS SCRIPT - FLORA SYSTEM (EMAIL AUTOMATION & CONFIRMATION)
 * =========================================================================
 * 
 * BỐ CỤC SHEET:
 * Cột A (1): name
 * Cột B (2): email
 * Cột C (3): mail2 (Timeline - gửi 20/07/2026)
 * Cột D (4): mail3 (Di chuyển - gửi 24/07/2026)
 * Cột E (5): confirm (Xác nhận tham gia)
 * 
 * Dòng 1: Tiêu đề gộp "EMAIL FLORA NGÀY HỘI"
 * Dòng 2: Lịch ngày gửi
 * Dòng 3: Tiêu đề cột (name, email, mail2, mail3, confirm)
 * Dòng 4 trở đi: Danh sách khách hàng
 */

// 1. CẤU HÌNH HỆ THỐNG
const SPREADSHEET_ID = "1Vbmb3Ql068y63bAYXrfpOmYwQfj6qgftqk_yUUwGItE";

// Điền Web App URL nhận được sau khi Triển khai (Deploy) vào đây để hoạt động
const WEB_APP_URL = "https://script.google.com/macros/s/AKfycbwfLrXPI8ERjD2UDMqKPFzMhYbv2er0UWqnBlxQOiIwtLzHqsmojQzSrNqTss7NlX8TEg/exec"; 

/**
 * =========================================================================
 * 2. ĐIỀU PHỐI GỬI EMAIL TỰ ĐỘNG HÀNG NGÀY (TRIGGER)
 * =========================================================================
 */
function dailyEventEmailDispatcher() {
  const vietnamTime = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd");
  console.log("Hàm Dispatcher hàng ngày đang kiểm tra lịch gửi vào lúc: " + vietnamTime);
  
  // Mail 2 gửi ngày 20/07/2026
  if (vietnamTime === "2026-07-20") {
    console.log("Hôm nay là 20/07/2026. Bắt đầu tự động gửi email mail2 (Timeline)...");
    scanAndSendMail2();
  }
  // Mail 3 gửi ngày 24/07/2026
  else if (vietnamTime === "2026-07-24") {
    console.log("Hôm nay là 24/07/2026. Bắt đầu tự động gửi email mail3 (Di chuyển)...");
    scanAndSendMail3();
  } else {
    console.log("Hôm nay là " + vietnamTime + ". Không có lịch gửi email tự động.");
  }
}

// Hàm gửi mail2 thủ công hoặc qua trigger
function scanAndSendMail2() {
  sendCampaignEmails(3, "mail2");
}

// Hàm gửi mail3 thủ công hoặc qua trigger
function scanAndSendMail3() {
  sendCampaignEmails(4, "mail3");
}

/**
 * LOGIC LÕI QUÉT VÀ GỬI EMAIL CHIẾN DỊCH
 */
function sendCampaignEmails(colIndex, templateName) {
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

  const sheet = ss.getSheets()[0]; // Làm việc trên Sheet đầu tiên
  const lastRow = sheet.getLastRow();
  if (lastRow < 4) {
    console.log("Không có dữ liệu khách hàng (dữ liệu bắt đầu từ dòng 4).");
    return;
  }

  // Đọc toàn bộ dữ liệu từ dòng 4
  const dataRange = sheet.getRange(4, 1, lastRow - 3, 5); // Đọc từ cột A đến E
  const data = dataRange.getValues();

  let count = 0;
  for (let i = 0; i < data.length; i++) {
    const row = data[i];
    const rowNum = i + 4; // Dòng thực tế trong Google Sheet

    const nameVal = row[0].toString().trim();      // Cột A (name)
    const emailVal = row[1].toString().trim();     // Cột B (email)
    const statusVal = row[colIndex - 1].toString().trim(); // Cột trạng thái hiện tại (mail2 hoặc mail3)

    // Chỉ gửi nếu có email hợp lệ và ô trạng thái chưa có chữ "Đã gửi" hoặc "Đã xem"
    if (emailVal && emailVal.indexOf("@") !== -1 && !statusVal.startsWith("Đã gửi") && !statusVal.startsWith("Đã xem")) {
      try {
        sendEmailHTML(emailVal, nameVal, rowNum, templateName);
        
        // Ghi nhận trạng thái gửi thành công
        sheet.getRange(rowNum, colIndex)
             .setValue("Đã gửi")
             .setBackground("#d4edda") // Xanh lá nhạt
             .setFontColor("#155724")
             .setFontWeight("bold");
        
        count++;
        Utilities.sleep(200); // Tránh spam quá nhanh làm lỗi quota gửi mail của Google
      } catch (err) {
        console.error("Lỗi gửi email dòng " + rowNum + ": " + err.toString());
        sheet.getRange(rowNum, colIndex)
             .setValue("Lỗi: " + err.toString())
             .setBackground("#f8d7da") // Đỏ nhạt
             .setFontColor("#721c24");
      }
    }
  }
  console.log("Chiến dịch " + templateName + " hoàn tất. Đã gửi: " + count + " email.");
}

/**
 * HÀM GỬI EMAIL HTML SỬ DỤNG TEMPLATE
 */
function sendEmailHTML(email, name, rowNum, templateName) {
  let subject = "";
  if (templateName === "mail2") {
    subject = "Thư mời & Lịch trình chi tiết: Ngày hội Implant 2026";
  } else if (templateName === "mail3") {
    subject = "Thông tin tham dự Ngày hội Implant 2026 | Thời gian & Hướng dẫn di chuyển";
  }

  // Khởi tạo HTML template từ file html tương ứng
  const template = HtmlService.createTemplateFromFile(templateName);
  template.name = name || "Quý khách";
  template.email = email;
  template.rowNum = rowNum;
  template.webAppUrl = WEB_APP_URL;

  const htmlBody = template.evaluate().getContent();

  MailApp.sendEmail({
    to: email,
    subject: subject,
    htmlBody: htmlBody,
    name: "Nha Khoa Flora",
    replyTo: "support@floraclinic.vn"
  });
}

/**
 * =========================================================================
 * 3. TIẾP NHẬN REQUEST WEB APP (OPEN TRACKING & CONFIRMATION BUTTON)
 * =========================================================================
 */
function doGet(e) {
  try {
    const action = e.parameter.action;
    const email = e.parameter.email;
    const rowStr = e.parameter.row;
    const campaign = e.parameter.campaign;
    const nameStr = e.parameter.name ? decodeURIComponent(e.parameter.name) : "Quý khách";

    let ss;
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
    const sheet = ss.getSheets()[0];

    // Xác định dòng cần cập nhật dựa trên email (ưu tiên tìm kiếm để tránh lệch dòng)
    let targetRow = parseInt(rowStr);
    if (email) {
      const foundRow = findRowByEmail(sheet, email);
      if (foundRow !== -1) {
        targetRow = foundRow;
      }
    }

    const timestamp = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd HH:mm:ss");

    // A. XỬ LÝ THEO DÕI MỞ EMAIL (TRACK OPEN)
    if (action === "trackOpen") {
      if (targetRow >= 4 && targetRow <= sheet.getLastRow() && campaign) {
        let colIndex = (campaign === "mail2") ? 3 : (campaign === "mail3" ? 4 : -1);
        if (colIndex !== -1) {
          const cell = sheet.getRange(targetRow, colIndex);
          const currentVal = cell.getValue().toString().trim();
          
          // Chỉ đổi trạng thái nếu ô đó chưa được đánh dấu là "Đã xem"
          if (!currentVal.startsWith("Đã xem")) {
            cell.setValue("Đã xem lúc: " + timestamp)
                .setBackground("#fff3cd") // Màu vàng nhạt
                .setFontColor("#856404")
                .setFontWeight("bold");
            console.log("Ghi nhận mở thư thành công cho: " + email + " (" + campaign + ")");
          }
        }
      }
      
      // Trả về ảnh GIF 1x1 trong suốt cho Tracking Pixel
      const gifBase64 = "R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
      return ContentService.createTextOutput()
                           .setMimeType(ContentService.MimeType.TEXT)
                           .setContent(Utilities.newBlob(Utilities.base64Decode(gifBase64), "image/gif").toString());
    }
    
    // B. XỬ LÝ KHÁCH HÀNG BẤM NÚT XÁC NHẬN THAM GIA
    else if (action === "confirmAttendance") {
      if (targetRow >= 4 && targetRow <= sheet.getLastRow()) {
        const confirmCell = sheet.getRange(targetRow, 5); // Cột E (confirm)
        confirmCell.setValue("Xác nhận lúc: " + timestamp)
                   .setBackground("#d4edda") // Màu xanh lá nhạt
                   .setFontColor("#155724")
                   .setFontWeight("bold");
        console.log("Khách hàng đã xác nhận tham gia: " + email);
      }

      // Trả về trang thông báo xác nhận thành công cao cấp
      const successTemplate = HtmlService.createTemplateFromFile("confirm_success");
      successTemplate.name = nameStr;
      
      return successTemplate.evaluate()
                            .setTitle("Xác nhận tham gia thành công | Nha Khoa Flora")
                            .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL);
    }
  } catch (err) {
    console.error("Lỗi doGet Web App: " + err.toString());
    return HtmlService.createHtmlOutput("<h3>Đã xảy ra lỗi: " + err.toString() + "</h3>");
  }
}

/**
 * HÀM TÌM DÒNG DỮ LIỆU QUA EMAIL KHÁCH HÀNG
 */
function findRowByEmail(sheet, email) {
  const lastRow = sheet.getLastRow();
  if (lastRow < 4) return -1;
  
  // Đọc danh sách email từ cột B (cột số 2), từ dòng 4 đến cuối
  const emails = sheet.getRange(4, 2, lastRow - 3, 1).getValues();
  const searchEmail = email.trim().toLowerCase();
  
  for (let i = 0; i < emails.length; i++) {
    if (emails[i][0].toString().trim().toLowerCase() === searchEmail) {
      return i + 4; // Trả về số dòng thực tế
    }
  }
  return -1;
}

/**
 * =========================================================================
 * 4. CÁC HÀM TIỆN ÍCH KIỂM THỬ VÀ RESET TRẠNG THÁI
 * =========================================================================
 */

// DANH SÁCH EMAIL NHẬN THỬ NGHIỆM (Thay đổi các email này bằng email của bạn để test)
const LIST_TEST_EMAILS = [
  { name: "Người Test 1", email: "test1@gmail.com", rowNum: 4 },
  { name: "Người Test 2", email: "test2@gmail.com", rowNum: 5 }
];

function testSendMail2Now() {
  console.log("Đang chạy gửi thử nghiệm mail2 (Timeline) tới danh sách cố định...");
  let ss;
  try {
    ss = SpreadsheetApp.openById(SPREADSHEET_ID);
  } catch(e) {
    ss = SpreadsheetApp.getActiveSpreadsheet();
  }
  const sheet = ss.getSheets()[0];
  
  LIST_TEST_EMAILS.forEach(function(user) {
    if (user.email && user.email.indexOf("@") !== -1) {
      console.log("Gửi thử nghiệm mail2 tới: " + user.email);
      sendEmailHTML(user.email, user.name, user.rowNum, "mail2");
      
      // Ghi nhận trạng thái gửi thành công lên dòng test tương ứng
      if (user.rowNum >= 4) {
        sheet.getRange(user.rowNum, 3) // Cột C
             .setValue("Đã gửi (Test)")
             .setBackground("#d4edda")
             .setFontColor("#155724")
             .setFontWeight("bold");
      }
    }
  });
}

function testSendMail3Now() {
  console.log("Đang chạy gửi thử nghiệm mail3 (Di chuyển) tới danh sách cố định...");
  let ss;
  try {
    ss = SpreadsheetApp.openById(SPREADSHEET_ID);
  } catch(e) {
    ss = SpreadsheetApp.getActiveSpreadsheet();
  }
  const sheet = ss.getSheets()[0];
  
  LIST_TEST_EMAILS.forEach(function(user) {
    if (user.email && user.email.indexOf("@") !== -1) {
      console.log("Gửi thử nghiệm mail3 tới: " + user.email);
      sendEmailHTML(user.email, user.name, user.rowNum, "mail3");
      
      // Ghi nhận trạng thái gửi thành công lên dòng test tương ứng
      if (user.rowNum >= 4) {
        sheet.getRange(user.rowNum, 4) // Cột D
             .setValue("Đã gửi (Test)")
             .setBackground("#d4edda")
             .setFontColor("#155724")
             .setFontWeight("bold");
      }
    }
  });
}

function resetMail2Status() {
  resetColumnStatus(3);
  console.log("Đã reset trạng thái cột mail2 (cột C).");
}

function resetMail3Status() {
  resetColumnStatus(4);
  console.log("Đã reset trạng thái cột mail3 (cột D).");
}

function resetConfirmStatus() {
  resetColumnStatus(5);
  console.log("Đã reset trạng thái cột confirm (cột E).");
}

function resetColumnStatus(colIndex) {
  let ss = SpreadsheetApp.openById(SPREADSHEET_ID);
  const sheet = ss.getSheets()[0];
  const lastRow = sheet.getLastRow();
  if (lastRow >= 4) {
    sheet.getRange(4, colIndex, lastRow - 3, 1)
         .clearContent()
         .setBackground(null)
         .setFontColor(null)
         .setFontWeight("normal");
  }
}
