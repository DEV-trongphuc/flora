/**
 * =========================================================================
 * GOOGLE APPS SCRIPT - LEAD COLLECTION BACKEND (GOOGLE SHEETS ONLY)
 * =========================================================================
 * 
 * Hướng dẫn sử dụng:
 * 1. Mở Bảng tính Google Sheets (ví dụ: bảng tính LEAD_WEBSITE của bạn).
 * 2. Vào mục "Tiện ích mở rộng" (Extensions) -> chọn "Apps Script".
 * 3. Xóa hết code mặc định và dán toàn bộ mã nguồn bên dưới vào.
 * 4. Thay thế giá trị SPREADSHEET_ID bên dưới bằng ID bảng tính của bạn.
 * 5. Chọn nút "Triển khai" (Deploy) -> chọn "Triển khai mới" (New deployment).
 *    - Chọn loại hình triển khai là "Ứng dụng web" (Web app).
 *    - Cấu hình quyền truy cập (Who has access) là "Bất kỳ ai" (Anyone).
 *    - Nhấp "Triển khai" (Deploy), cấp quyền truy cập nếu được yêu cầu.
 * 6. Sao chép URL ứng dụng Web (Web app URL) nhận được và dán vào biến GOOGLE_SCRIPT_URL
 *    ở đầu khối mã Javascript trong file HTML.
 */

// ĐIỀN ID BẢNG TÍNH GOOGLE SHEETS CỦA BẠN VÀO ĐÂY (Hoặc để trống nếu Script được tạo trực tiếp bên trong Trang tính)
const SPREADSHEET_ID = "1H8CVdQAlyuggCdt0YC1KUmJMQvUbIleGLcg_bXnwN-4"; 
const SHEET_NAME = "DATA";

function doPost(e) {
  try {
    // 1. Mở Bảng tính
    let ss;
    if (SPREADSHEET_ID) {
      ss = SpreadsheetApp.openById(SPREADSHEET_ID);
    } else {
      ss = SpreadsheetApp.getActiveSpreadsheet();
    }
    
    // 2. Lấy hoặc tạo Sheet có tên DATA
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
    
    // 5. Trả về kết quả JSON thành công (Hỗ trợ CORS)
    return ContentService.createTextOutput(JSON.stringify({ 
      "result": "success", 
      "message": "Lead appended successfully"
    }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    console.error("Lỗi Google Apps Script: " + error.toString());
    return ContentService.createTextOutput(JSON.stringify({ "result": "error", "error": error.toString() }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// Hàm Test thử (bấm Chạy trong Apps Script để kiểm tra quyền truy cập và tạo sheet)
function testAddRow() {
  const mockEvent = {
    postData: {
      contents: JSON.stringify({
        formType: "Test Form",
        fullname: "Nguyễn Văn Test",
        phone: "0987654321",
        email: "test@gmail.com",
        clinic: "Nha Khoa Test",
        city: "Hồ Chí Minh",
        interest: "Cần tư vấn thêm",
        date: "12/06/2026",
        timeSlot: "14:00 - 15:00",
        note: "Ghi chú test"
      })
    }
  };
  doPost(mockEvent);
}
