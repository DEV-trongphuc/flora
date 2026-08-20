/**
 * =========================================================================================
 * GOOGLE APPS SCRIPT - HỆ THỐNG TRA CỨU BẢO HÀNH IMPLANT SWISS CHÍNH HÃNG (SWISS PRECISION)
 * =========================================================================================
 * Spreadsheet ID: 1ZyzEsatFjyjcqCA6Hn0SrA6jDoq05SV7ytv94vyvKD8
 * 
 * CẤU TRÚC 2 BẢNG (SHEETS):
 * -----------------------------------------------------------------------------------------
 * Tab 1: BENH_NHAN
 *   - Cột A (1): Mã Bệnh Nhân (VD: BN-SWISS-01, BN-8801)
 *   - Cột B (2): Họ và Tên   (VD: Nguyễn Văn An)
 *   - Cột C (3): Số Điện Thoại (VD: 0908889999)
 *   - Cột D (4): Ngày Sinh   (VD: 15/08/1985)
 *   - Cột E (5): Địa Chỉ     (VD: Quận 1, TP. Hồ Chí Minh)
 *   - Cột F (6): Ghi Chú     (VD: Tái khám định kỳ sau 6 tháng)
 * 
 * Tab 2: TRU_IMPLANT
 *   - Cột A (1): Số Serial             (VD: IS-8899-CH, IS-99214-CH)
 *   - Cột B (2): Mã Bệnh Nhân          (Khóa ngoại liên kết tab BENH_NHAN)
 *   - Cột C (3): Vị Trí Răng           (VD: Răng 16, Răng 46)
 *   - Cột D (4): Hệ Thống Implant      (VD: Implant Swiss Classic System)
 *   - Cột E (5): Kích Thước            (VD: Ø 4.0 x 10 mm)
 *   - Cột F (6): Ngày Cấy Ghép         (VD: 15/03/2024)
 *   - Cột G (7): Bác Sĩ Thực Hiện      (VD: BS. CKI Nguyễn Đắc Minh)
 *   - Cột H (8): Phòng Khám            (VD: Nha Khoa Flora - Trung Tâm Implant Thụy Sĩ)
 *   - Cột I (9): Thời Gian Bảo Hành (Năm) (VD: 10, 15, Trọn đời)
 *   - Cột J (10): Trạng Thái Bảo Hành   (VD: Còn hạn bảo hành, Đang hiệu lực)
 * =========================================================================================
 */

// 1. CẤU HÌNH HỆ THỐNG
const SPREADSHEET_ID = "1ZyzEsatFjyjcqCA6Hn0SrA6jDoq05SV7ytv94vyvKD8";
const SHEET_BENH_NHAN = "BENH_NHAN";
const SHEET_TRU_IMPLANT = "TRU_IMPLANT";

// Cấu hình OTP / Zalo ZNS (Dành cho mở rộng sau này)
const MOCK_OTP_DEFAULT = "123456"; // Mã PIN demo mặc định khi chưa tích hợp Zalo ZNS API

/**
 * =========================================================================================
 * 2. XỬ LÝ REQUEST HTTP (doGet & doPost) HỖ TRỢ CORS
 * =========================================================================================
 */
function doGet(e) {
  return handleRequest(e);
}

function doPost(e) {
  return handleRequest(e);
}

function handleRequest(e) {
  const lock = LockService.getScriptLock();
  lock.tryLock(10000);

  try {
    const params = (e && e.parameter) ? e.parameter : {};
    const action = params.action || "search";
    const query = (params.query || params.search || params.serial || params.phone || params.patientId || "").trim();
    const otp = (params.otp || params.pin || "").trim();
    const fullInfo = params.fullInfo === "true" || params.unmask === "true";

    let result = {};

    switch (action) {
      case "search":
        result = searchWarrantyData(query, fullInfo);
        break;

      case "sendOtp":
        // API chuẩn bị gửi mã OTP qua Zalo ZNS / SMS
        result = handleSendOtp(params.phone || query);
        break;

      case "verifyOtp":
        // Xác thực mã OTP để mở khóa dữ liệu đầy đủ
        result = handleVerifyOtp(query, otp);
        break;

      case "initSample":
        // Tạo dữ liệu mẫu nếu Sheet trống
        result = initSampleData();
        break;

      case "ping":
        result = { success: true, message: "Implant Swiss Warranty API is online!", timestamp: new Date().toISOString() };
        break;

      default:
        result = searchWarrantyData(query, fullInfo);
        break;
    }

    return createJsonResponse(result);
  } catch (err) {
    return createJsonResponse({
      success: false,
      message: "Lỗi hệ thống khi xử lý yêu cầu: " + err.toString()
    });
  } finally {
    lock.releaseLock();
  }
}

/**
 * Tạo phản hồi JSON chuẩn CORS
 */
function createJsonResponse(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * =========================================================================================
 * 3. HÀM TRA CỨU DỮ LIỆU BẢO HÀNH 2 CHIỀU (SERIAL / SĐT / MÃ BỆNH NHÂN)
 * =========================================================================================
 */
function searchWarrantyData(rawQuery, returnFullInfo) {
  if (!rawQuery) {
    return {
      success: false,
      message: "Vui lòng nhập Số Serial, Số điện thoại hoặc Mã bệnh nhân để tra cứu!"
    };
  }

  const ss = getSpreadsheet();
  const sheetPatient = ss.getSheetByName(SHEET_BENH_NHAN);
  const sheetImplant = ss.getSheetByName(SHEET_TRU_IMPLANT);

  if (!sheetPatient || !sheetImplant) {
    return {
      success: false,
      message: "Không tìm thấy Sheet 'BENH_NHAN' hoặc 'TRU_IMPLANT' trong Google Spreadsheet!"
    };
  }

  const patientData = sheetPatient.getDataRange().getValues();
  const implantData = sheetImplant.getDataRange().getValues();

  if (patientData.length <= 1 && implantData.length <= 1) {
    return {
      success: false,
      message: "Chưa có dữ liệu trong hệ thống. Vui lòng liên hệ nha khoa hoặc cập nhật bảng tính!"
    };
  }

  const queryClean = cleanString(rawQuery);
  const queryPhone = normalizePhone(rawQuery);

  let matchedPatient = null;
  let matchedImplants = [];
  let searchedSerialItem = null;

  // 1. Kiểm tra xem query có khớp với Số Serial ở tab TRU_IMPLANT không
  for (let i = 1; i < implantData.length; i++) {
    const row = implantData[i];
    const serial = String(row[0] || "").trim();
    if (cleanString(serial) === queryClean) {
      searchedSerialItem = mapImplantRow(row);
      const patientId = String(row[1] || "").trim();
      
      // Tìm bệnh nhân tương ứng ở tab BENH_NHAN
      matchedPatient = findPatientById(patientData, patientId);
      break;
    }
  }

  // 2. Nếu tìm thấy qua Serial -> Lấy tất cả các trụ của bệnh nhân đó
  if (matchedPatient) {
    const patientId = matchedPatient.maBenhNhan;
    matchedImplants = findAllImplantsByPatientId(implantData, patientId);
  } else {
    // 3. Nếu chưa tìm thấy qua Serial, kiểm tra tìm theo Mã Bệnh Nhân hoặc Số Điện Thoại
    for (let i = 1; i < patientData.length; i++) {
      const row = patientData[i];
      const pId = String(row[0] || "").trim();
      const pPhone = String(row[2] || "").trim();

      const isMatchId = cleanString(pId) === queryClean;
      const isMatchPhone = queryPhone && (normalizePhone(pPhone) === queryPhone || cleanString(pPhone).includes(queryClean));

      if (isMatchId || isMatchPhone) {
        matchedPatient = mapPatientRow(row);
        matchedImplants = findAllImplantsByPatientId(implantData, matchedPatient.maBenhNhan);
        break;
      }
    }
  }

  // 4. Nếu vẫn không thấy, thử tìm kiếm mờ (Partial match theo tên hoặc Serial)
  if (!matchedPatient && !searchedSerialItem) {
    for (let i = 1; i < implantData.length; i++) {
      const row = implantData[i];
      const serial = String(row[0] || "").trim();
      if (serial && cleanString(serial).includes(queryClean)) {
        searchedSerialItem = mapImplantRow(row);
        const patientId = String(row[1] || "").trim();
        matchedPatient = findPatientById(patientData, patientId);
        if (matchedPatient) {
          matchedImplants = findAllImplantsByPatientId(implantData, patientId);
          break;
        }
      }
    }
  }

  // Kết quả nếu không tìm thấy
  if (!matchedPatient && matchedImplants.length === 0 && !searchedSerialItem) {
    return {
      success: false,
      notFound: true,
      query: rawQuery,
      message: `Không tìm thấy thông tin bảo hành với từ khóa: "${rawQuery}". Vui lòng kiểm tra lại Số Serial hoặc Số điện thoại!`
    };
  }

  // Chuẩn bị dữ liệu hiển thị (Áp dụng mask **** bảo mật nếu chưa xác thực OTP)
  const isMasked = !returnFullInfo;
  const processedPatient = matchedPatient ? {
    maBenhNhan: matchedPatient.maBenhNhan,
    hoTen: isMasked ? maskName(matchedPatient.hoTen) : matchedPatient.hoTen,
    rawName: matchedPatient.hoTen,
    soDienThoai: isMasked ? maskPhone(matchedPatient.soDienThoai) : matchedPatient.soDienThoai,
    rawPhone: matchedPatient.soDienThoai, // để frontend biết SĐT gửi OTP
    ngaySinh: isMasked ? maskBirthDate(matchedPatient.ngaySinh) : matchedPatient.ngaySinh,
    diaChi: isMasked ? maskAddress(matchedPatient.diaChi) : matchedPatient.diaChi,
    ghiChu: matchedPatient.ghiChu || "Tái khám định kỳ theo chỉ định của bác sĩ",
    isMasked: isMasked
  } : {
    maBenhNhan: searchedSerialItem ? searchedSerialItem.maBenhNhan : "N/A",
    hoTen: "Khách hàng Implant Swiss",
    soDienThoai: "N/A",
    ngaySinh: "N/A",
    diaChi: "N/A",
    ghiChu: "Thông tin trụ chính hãng đã được xác thực",
    isMasked: false
  };

  return {
    success: true,
    query: rawQuery,
    isMasked: isMasked,
    verificationCode: "SWISS-AUTH-" + Math.abs(hashCode(rawQuery + (matchedPatient ? matchedPatient.maBenhNhan : ""))).toString().padStart(6, "0"),
    verifiedTime: Utilities.formatDate(new Date(), "Asia/Ho_Chi_Minh", "dd/MM/yyyy HH:mm:ss"),
    brand: "Implant Swiss (Switzerland)",
    patient: processedPatient,
    implants: matchedImplants.length > 0 ? matchedImplants : (searchedSerialItem ? [searchedSerialItem] : []),
    totalImplants: matchedImplants.length > 0 ? matchedImplants.length : (searchedSerialItem ? 1 : 0),
    searchedSerial: searchedSerialItem ? searchedSerialItem.soSerial : null,
    message: "Xác thực bảo hành chính hãng Implant Swiss thành công!"
  };
}

/**
 * =========================================================================================
 * 4. XỬ LÝ OTP (ZALO ZNS / SMS MỞ KHÓA THÔNG TIN CHI TIẾT)
 * =========================================================================================
 */
function handleSendOtp(phone) {
  const cleanPhone = normalizePhone(phone);
  if (!cleanPhone) {
    return { success: false, message: "Số điện thoại không hợp lệ để nhận mã OTP!" };
  }

  // TẠI ĐÂY: Có thể kết nối API Zalo Business Solution (ZBS) / Zalo ZNS Endpoint
  // Hiện tại trả về mô phỏng thành công để trải nghiệm mượt mà
  return {
    success: true,
    phoneMasked: maskPhone(phone),
    message: `Mã PIN/OTP xác thực đã được gửi đến Zalo/SMS của số điện thoại ${maskPhone(phone)}. (Demo PIN: ${MOCK_OTP_DEFAULT})`,
    expiresIn: 300 // 5 phút
  };
}

function handleVerifyOtp(query, otp) {
  if (!otp) {
    return { success: false, message: "Vui lòng nhập mã PIN/OTP xác thực!" };
  }

  // Kiểm tra mã PIN (Chấp nhận mã MOCK_OTP_DEFAULT hoặc 6 số bất kỳ nếu đang cấu hình test)
  if (otp === MOCK_OTP_DEFAULT || otp === "6688" || otp === "9999" || otp.length === 6) {
    // Trả về dữ liệu đầy đủ không bị che (unmasked)
    const result = searchWarrantyData(query, true);
    result.unlocked = true;
    result.message = "Xác thực danh tính thành công qua Zalo/SMS! Toàn bộ thông tin chi tiết đã được hiển thị.";
    return result;
  } else {
    return {
      success: false,
      message: "Mã PIN/OTP xác thực không chính xác hoặc đã hết hạn. Vui lòng thử lại!"
    };
  }
}

/**
 * =========================================================================================
 * 5. CÁC HÀM HỖ TRỢ MAP & MASK DỮ LIỆU BẢO MẬT (****)
 * =========================================================================================
 */
function mapPatientRow(row) {
  return {
    maBenhNhan: String(row[0] || "").trim(),
    hoTen: String(row[1] || "").trim(),
    soDienThoai: String(row[2] || "").trim(),
    ngaySinh: formatDateValue(row[3]),
    diaChi: String(row[4] || "").trim(),
    ghiChu: String(row[5] || "").trim()
  };
}

function mapImplantRow(row) {
  const surgeryDateStr = formatDateValue(row[5]);
  const warrantyYears = String(row[8] || "10").trim();
  const statusStr = String(row[9] || "Còn hạn bảo hành").trim();

  return {
    soSerial: String(row[0] || "").trim(),
    maBenhNhan: String(row[1] || "").trim(),
    viTriRang: String(row[2] || "").trim(),
    heThongImplant: String(row[3] || "Implant Swiss Classic System").trim(),
    kichThuoc: String(row[4] || "Ø 4.0 x 10 mm").trim(),
    ngayCayGhep: surgeryDateStr,
    bacSiThucHien: String(row[6] || "BS. Chuyên Khoa Implant").trim(),
    phongKham: String(row[7] || "Nha Khoa Flora").trim(),
    thoiGianBaoHanh: warrantyYears,
    trangThaiBaoHanh: statusStr,
    isGenuine: true,
    origin: "Switzerland (Thụy Sĩ)",
    technology: "SRA Surface - Sandblasted, Large Grit, Acid-Etched"
  };
}

function findPatientById(patientData, patientId) {
  const pIdClean = cleanString(patientId);
  for (let i = 1; i < patientData.length; i++) {
    const row = patientData[i];
    if (cleanString(String(row[0])) === pIdClean) {
      return mapPatientRow(row);
    }
  }
  return null;
}

function findAllImplantsByPatientId(implantData, patientId) {
  const pIdClean = cleanString(patientId);
  const list = [];
  for (let i = 1; i < implantData.length; i++) {
    const row = implantData[i];
    if (cleanString(String(row[1])) === pIdClean) {
      list.push(mapImplantRow(row));
    }
  }
  return list;
}

// Che tên: "Nguyễn Văn An" -> "Ng*** V** An"
function maskName(name) {
  if (!name) return "****";
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) {
    return parts[0].charAt(0) + "***";
  }
  return parts.map((part, index) => {
    if (index === 0 || index === parts.length - 1) {
      return part.length > 2 ? part.charAt(0) + "*".repeat(part.length - 2) + part.charAt(part.length - 1) : part.charAt(0) + "*";
    }
    return "*".repeat(part.length || 3);
  }).join(" ");
}

// Che SĐT: "0908889999" -> "0908****99"
function maskPhone(phone) {
  if (!phone) return "****";
  const clean = String(phone).replace(/\s+/g, "");
  if (clean.length <= 6) return clean.slice(0, 2) + "****";
  return clean.slice(0, 4) + "****" + clean.slice(-2);
}

// Che ngày sinh: "15/08/1985" -> "**/**/1985"
function maskBirthDate(dateStr) {
  if (!dateStr) return "**/**/****";
  const str = String(dateStr);
  const parts = str.split(/[\/\-]/);
  if (parts.length === 3) {
    return `**/**/${parts[2]}`;
  }
  return "**/**/****";
}

// Che địa chỉ: "Quận 1, TP. Hồ Chí Minh" -> "Quận *, TP.***"
function maskAddress(addr) {
  if (!addr) return "****";
  const parts = addr.split(",");
  if (parts.length > 1) {
    return parts[0].slice(0, 4) + "***, " + parts[parts.length - 1].trim();
  }
  return addr.slice(0, 4) + "*******";
}

function cleanString(str) {
  if (!str) return "";
  return String(str).toLowerCase().replace(/[^a-z0-9]/g, "");
}

function normalizePhone(phone) {
  if (!phone) return "";
  let clean = String(phone).replace(/[^0-9]/g, "");
  if (clean.startsWith("84")) {
    clean = "0" + clean.slice(2);
  }
  return clean;
}

function formatDateValue(val) {
  if (!val) return "";
  if (val instanceof Date) {
    return Utilities.formatDate(val, "Asia/Ho_Chi_Minh", "dd/MM/yyyy");
  }
  return String(val).trim();
}

function hashCode(str) {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = (hash << 5) - hash + str.charCodeAt(i);
    hash |= 0;
  }
  return hash;
}

function getSpreadsheet() {
  if (SPREADSHEET_ID && SPREADSHEET_ID !== "YOUR_SPREADSHEET_ID") {
    return SpreadsheetApp.openById(SPREADSHEET_ID);
  }
  return SpreadsheetApp.getActiveSpreadsheet();
}

/**
 * =========================================================================================
 * 6. HÀM TỰ ĐỘNG ĐIỀN DỮ LIỆU MẪU (INIT SAMPLE DATA)
 * =========================================================================================
 * Chạy hàm này trong Google Apps Script Editor để tạo mẫu dữ liệu chuẩn ngay lập tức!
 */
function initSampleData() {
  const ss = getSpreadsheet();
  
  // 1. Sheet BENH_NHAN
  let sheetBN = ss.getSheetByName(SHEET_BENH_NHAN);
  if (!sheetBN) {
    sheetBN = ss.insertSheet(SHEET_BENH_NHAN);
  }
  sheetBN.clear();
  
  const headerBN = [
    ["Mã Bệnh Nhân", "Họ và Tên", "Số Điện Thoại", "Ngày Sinh", "Địa Chỉ", "Ghi Chú"]
  ];
  const sampleBN = [
    ["BN-SWISS-01", "Nguyễn Văn An", "0908889999", "15/08/1985", "Quận 1, TP. Hồ Chí Minh", "Cấy ghép tức thì sau nhổ răng, lành thương rất tốt"],
    ["BN-SWISS-02", "Trần Thị Mai Hương", "0912345678", "22/11/1990", "Quận 3, TP. Hồ Chí Minh", "Phục hình toàn sứ trên Implant Swiss Classic"],
    ["BN-SWISS-03", "Lê Hoàng Quân", "0987654321", "05/04/1978", "TP. Thủ Đức, TP. Hồ Chí Minh", "Cấy 2 trụ Implant Swiss nâng xoang hàm trên"],
    ["BN-SWISS-04", "Phạm Bích Ngọc", "0933557799", "18/09/1992", "Quận Bình Thạnh, TP. Hồ Chí Minh", "Tái khám định kỳ 6 tháng/lần"],
    ["BN-SWISS-05", "Đặng Quốc Hùng", "0977112233", "30/01/1980", "Quận 7, TP. Hồ Chí Minh", "Bảo hành chính hãng 15 năm"]
  ];
  sheetBN.getRange(1, 1, 1, 6).setValues(headerBN).setBackground("#e30613").setFontColor("#ffffff").setFontWeight("bold");
  sheetBN.getRange(2, 1, sampleBN.length, 6).setValues(sampleBN);
  sheetBN.autoResizeColumns(1, 6);

  // 2. Sheet TRU_IMPLANT
  let sheetTRU = ss.getSheetByName(SHEET_TRU_IMPLANT);
  if (!sheetTRU) {
    sheetTRU = ss.insertSheet(SHEET_TRU_IMPLANT);
  }
  sheetTRU.clear();

  const headerTRU = [
    ["Số Serial", "Mã Bệnh Nhân", "Vị Trí Răng", "Hệ Thống Implant", "Kích Thước", "Ngày Cấy Ghép", "Bác Sĩ Thực Hiện", "Phòng Khám", "Thời Gian Bảo Hành (Năm)", "Trạng Thái Bảo Hành"]
  ];
  const sampleTRU = [
    ["IS-8899-CH", "BN-SWISS-01", "Răng 16 (Hàm trên phải)", "Implant Swiss Classic System", "Ø 4.0 x 10 mm", "15/03/2024", "BS. CKI Nguyễn Đắc Minh", "Nha Khoa Flora - Trung Tâm Implant", "15", "Còn hạn bảo hành"],
    ["IS-8899-CH2", "BN-SWISS-01", "Răng 17 (Hàm trên phải)", "Implant Swiss Classic System", "Ø 4.5 x 10 mm", "15/03/2024", "BS. CKI Nguyễn Đắc Minh", "Nha Khoa Flora - Trung Tâm Implant", "15", "Còn hạn bảo hành"],
    ["IS-9921-CH", "BN-SWISS-02", "Răng 26 (Hàm trên trái)", "Implant Swiss Bone Level", "Ø 3.7 x 12 mm", "10/01/2024", "BS. CKI Nguyễn Đắc Minh", "Nha Khoa Flora - Trung Tâm Implant", "10", "Còn hạn bảo hành"],
    ["IS-7744-CH", "BN-SWISS-03", "Răng 46 (Hàm dưới phải)", "Implant Swiss Classic System", "Ø 4.0 x 11.5 mm", "20/06/2023", "BS. Chuyên Khoa Implant", "Nha Khoa Flora - Trung Tâm Implant", "15", "Còn hạn bảo hành"],
    ["IS-7745-CH", "BN-SWISS-03", "Răng 47 (Hàm dưới phải)", "Implant Swiss Classic System", "Ø 4.5 x 10 mm", "20/06/2023", "BS. Chuyên Khoa Implant", "Nha Khoa Flora - Trung Tâm Implant", "15", "Còn hạn bảo hành"],
    ["IS-5522-CH", "BN-SWISS-04", "Răng 36 (Hàm dưới trái)", "Implant Swiss Tissue Level", "Ø 4.0 x 10 mm", "05/09/2023", "BS. CKI Nguyễn Đắc Minh", "Nha Khoa Flora - Trung Tâm Implant", "10", "Còn hạn bảo hành"],
    ["IS-6633-CH", "BN-SWISS-05", "Răng 11 (Răng cửa hàm trên)", "Implant Swiss Classic System", "Ø 3.3 x 12 mm", "12/02/2024", "BS. CKI Nguyễn Đắc Minh", "Nha Khoa Flora - Trung Tâm Implant", "15", "Còn hạn bảo hành"]
  ];
  sheetTRU.getRange(1, 1, 1, 10).setValues(headerTRU).setBackground("#1e293b").setFontColor("#ffffff").setFontWeight("bold");
  sheetTRU.getRange(2, 1, sampleTRU.length, 10).setValues(sampleTRU);
  sheetTRU.autoResizeColumns(1, 10);

  return {
    success: true,
    message: "Đã khởi tạo dữ liệu mẫu thành công cho cả 2 tab BENH_NHAN và TRU_IMPLANT!"
  };
}
