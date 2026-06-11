/**
 * =========================================================================
 * GOOGLE APPS SCRIPT - LEAD COLLECTION BACKEND WITH META CONVERSIONS API
 * =========================================================================
 * 
 * Hướng dẫn sử dụng:
 * 1. Mở Bảng tính Google Sheets (ví dụ: bảng tính LEAD_WEBSITE của bạn).
 * 2. Vào mục "Tiện ích mở rộng" (Extensions) -> chọn "Apps Script".
 * 3. Xóa hết code mặc định và dán toàn bộ mã nguồn bên dưới vào.
 * 4. Thay thế giá trị SPREADSHEET_ID bên dưới bằng ID bảng tính của bạn.
 * 5. Thay thế giá trị META_PIXEL_ID bên dưới bằng ID Pixel Facebook của bạn.
 * 6. Chọn nút "Triển khai" (Deploy) -> chọn "Triển khai mới" (New deployment).
 *    - Chọn loại hình triển khai là "Ứng dụng web" (Web app).
 *    - Cấu hình quyền truy cập (Who has access) là "Bất kỳ ai" (Anyone).
 *    - Nhấp "Triển khai" (Deploy), cấp quyền truy cập nếu được yêu cầu.
 * 7. Sao chép URL ứng dụng Web (Web app URL) nhận được và dán vào biến GOOGLE_SCRIPT_URL
 *    ở đầu khối mã Javascript trong file index.html.
 */

// ĐIỀN ID BẢNG TÍNH GOOGLE SHEETS CỦA BẠN VÀO ĐÂY (Hoặc để trống nếu Script được tạo trực tiếp bên trong Trang tính)
const SPREADSHEET_ID = "1DjptVktrbp__2MZ-rS8_XAipBwNUqkID4XqzTDg0V4o"; 
const SHEET_NAME = "LEAD_WEBSITE";

// CẤU HÌNH META CONVERSIONS API (CAPI)
const META_PIXEL_ID = "1560663312285833"; // Thay thế bằng Pixel ID thực tế của bạn
const META_ACCESS_TOKEN = "EAATBtgVJGAMBRuy504D0Gd1rZBvT2dQrDxvFTiin1u1pcs6VYwCswANPMYWiPHQSkZBQ8N2cSvrZCEiZCoZAjjvQnCIjqYQrydu64C60wnjx2DSsVrq1ZAoOCC4b9ZBPhWVZCLnvLlY0fO0bqFYRr3IZAs6pxEvFoiVYQ7US54jJRaDlXot9cDvvF1Y95ZAT9z66Ut7QZDZD";
const TEST_EVENT_CODE = "TEST15646"; // Điền mã Test Event Code từ Trình quản lý sự kiện nếu muốn thử nghiệm (Ví dụ: TEST12345). Để trống khi chạy thật.

function doPost(e) {
  try {
    // 1. Mở Bảng tính
    let ss;
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
    
    // 2. Lấy hoặc tạo Sheet có tên LEAD_WEBSITE
    let sheet = ss.getSheetByName(SHEET_NAME);
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
      sheet = ss.insertSheet(SHEET_NAME);
      sheet.getRange(1, 1, 1, headers.length)
           .setValues([headers])
           .setFontWeight("bold")
           .setBackground("#c59e6c")
           .setFontColor("#ffffff")
           .setHorizontalAlignment("center");
      sheet.setFrozenRows(1);
    }
    
    // 3. Phân tích dữ liệu nhận được từ Request
    const data = JSON.parse(e.postData.contents);
    const timestamp = Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "yyyy-MM-dd HH:mm:ss");
    
    // 4. Thêm dòng dữ liệu mới vào Sheets
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
    
    // Tự động căn chỉnh độ rộng cột
    sheet.autoResizeColumns(1, headers.length);
    
    // 5. Gửi dữ liệu tới Meta Conversions API
    const capiResult = sendToMetaConversionsAPI(data);
    
    // 6. Trả về kết quả JSON thành công (Hỗ trợ CORS)
    return ContentService.createTextOutput(JSON.stringify({ 
      "result": "success", 
      "message": "Lead appended successfully",
      "capi": capiResult
    }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error("Lỗi Google Apps Script: " + error.toString());
    return ContentService.createTextOutput(JSON.stringify({ "result": "error", "error": error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Gửi thông tin lead tới Meta Conversions API
 */
function sendToMetaConversionsAPI(leadData) {
  if (!META_PIXEL_ID || META_PIXEL_ID === "" || META_PIXEL_ID === "YOUR_META_PIXEL_ID_HERE") {
    console.warn("Meta Pixel ID chưa được cấu hình. Bỏ qua gửi CAPI.");
    return { "status": "skipped", "reason": "Pixel ID not configured" };
  }
  if (!META_ACCESS_TOKEN || META_ACCESS_TOKEN === "") {
    console.warn("Meta Access Token chưa được cấu hình. Bỏ qua gửi CAPI.");
    return { "status": "skipped", "reason": "Access Token not configured" };
  }
  
  try {
    const emailRaw = leadData.email || "";
    const phoneRaw = leadData.phone || "";
    const fullnameRaw = leadData.fullname || "";
    const cityRaw = leadData.city || "";
    
    // Chuẩn hóa và băm SHA-256 các thông tin định danh người dùng
    const emailHashed = sha256Hash(emailRaw.trim().toLowerCase());
    const normalizedPhone = normalizePhone(phoneRaw);
    const phoneHashed = sha256Hash(normalizedPhone);
    const cityHashed = sha256Hash(cityRaw.trim().toLowerCase());
    
    const nameParts = getFirstAndLastName(fullnameRaw);
    const firstNameHashed = sha256Hash(nameParts.first);
    const lastNameHashed = sha256Hash(nameParts.last);
    
    // Tạo cấu trúc user_data
    const userData = {};
    if (emailHashed) userData.em = [emailHashed];
    if (phoneHashed) userData.ph = [phoneHashed];
    if (firstNameHashed) userData.fn = [firstNameHashed];
    if (lastNameHashed) userData.ln = [lastNameHashed];
    if (cityHashed) userData.ct = [cityHashed];
    
    // Gắn thông tin Client/Browser (giúp tăng Event Match Quality)
    if (leadData.clientUserAgent) userData.client_user_agent = leadData.clientUserAgent;
    if (leadData.fbp) userData.fbp = leadData.fbp;
    if (leadData.fbc) userData.fbc = leadData.fbc;
    
    const eventData = {
      "event_name": "Lead",
      "event_time": Math.floor(Date.now() / 1000),
      "action_source": "website",
      "event_source_url": leadData.eventSourceUrl || "https://brandson.vn/cay-ghep-implant.html",
      "event_id": leadData.eventId || "",
      "user_data": userData,
      "custom_data": {
        "lead_type": leadData.formType || "",
        "clinic": leadData.clinic || "",
        "interest": leadData.interest || "",
        "appointment_date": leadData.date || "",
        "appointment_time": leadData.timeSlot || "",
        "note": leadData.note || ""
      }
    };
    
    const payload = {
      "data": [eventData]
    };
    
    // Nếu có truyền mã test_event_code từ frontend hoặc cấu hình sẵn ở trên
    const activeTestCode = leadData.testEventCode || TEST_EVENT_CODE;
    if (activeTestCode) {
      payload.test_event_code = activeTestCode;
    }
    
    const url = "https://graph.facebook.com/v19.0/" + META_PIXEL_ID + "/events?access_token=" + META_ACCESS_TOKEN;
    const options = {
      "method": "post",
      "contentType": "application/json",
      "payload": JSON.stringify(payload),
      "muteHttpExceptions": true
    };
    
    const response = UrlFetchApp.fetch(url, options);
    const responseText = response.getContentText();
    const responseCode = response.getResponseCode();
    
    console.log("Meta CAPI Response Code: " + responseCode);
    console.log("Meta CAPI Response: " + responseText);
    
    return {
      "status": responseCode === 200 ? "success" : "error",
      "code": responseCode,
      "response": JSON.parse(responseText)
    };
  } catch (error) {
    console.error("Lỗi khi gửi Meta Conversions API: " + error.toString());
    return { "status": "exception", "error": error.toString() };
  }
}

/**
 * Hàm băm SHA-256 sử dụng Utilities.computeDigest của Google Apps Script
 */
function sha256Hash(input) {
  if (!input) return null;
  const cleaned = input.toString().trim().toLowerCase();
  if (cleaned === "") return null;
  
  const digest = Utilities.computeDigest(Utilities.DigestAlgorithm.SHA_256, cleaned, Utilities.Charset.UTF_8);
  let hash = "";
  for (let i = 0; i < digest.length; i++) {
    let byteVal = digest[i];
    if (byteVal < 0) byteVal += 256;
    let byteString = byteVal.toString(16);
    if (byteString.length == 1) byteString = "0" + byteString;
    hash += byteString;
  }
  return hash;
}

/**
 * Chuẩn hóa số điện thoại: thêm mã quốc gia 84 và bỏ số 0 ở đầu
 */
function normalizePhone(phone) {
  if (!phone) return null;
  let digits = phone.toString().replace(/\D/g, ""); // Giữ lại chữ số
  if (digits.startsWith("0")) {
    digits = "84" + digits.substring(1);
  } else if (digits.length > 0 && !digits.startsWith("84")) {
    digits = "84" + digits;
  }
  return digits;
}

/**
 * Phân tách họ tên theo quy tắc tiếng Việt (tên ở cuối, họ & tên đệm ở đầu)
 */
function getFirstAndLastName(fullname) {
  if (!fullname) return { first: "", last: "" };
  const name = fullname.trim();
  const parts = name.split(/\s+/);
  if (parts.length === 1) {
    return { first: name, last: "" };
  }
  const first = parts.pop();
  const last = parts.join(" ");
  return { first: first, last: last };
}

// Hàm Test thử (bấm Chạy trong Apps Script để kiểm tra quyền truy cập và tạo sheet)
function testAddRow() {
  const mockEvent = {
    postData: {
      contents: JSON.stringify({
        formType: "Test Form CAPI",
        fullname: "Nguyễn Văn Capi",
        phone: "0987654321",
        email: "test.capi@gmail.com",
        clinic: "Nha Khoa CAPI",
        city: "Hồ Chí Minh",
        interest: "Cần tư vấn thêm",
        date: "12/06/2026",
        timeSlot: "14:00 - 15:00",
        note: "Ghi chú test CAPI",
        clientUserAgent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64)",
        eventSourceUrl: "https://brandson.vn/cay-ghep-implant.html",
        fbp: "fb.1.1681140000.123456789",
        fbc: "fb.1.1681140000.fbclid123456"
      })
    }
  };
  doPost(mockEvent);
}
