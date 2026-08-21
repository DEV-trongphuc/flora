/**
 * =========================================================================================
 * GOOGLE APPS SCRIPT - HỆ THỐNG TRA CỨU BẢO HÀNH & XÁC THỰC TRỤ IMPLANTSWISS CHÍNH HÃNG
 * =========================================================================================
 * Cấu trúc bảng Google Sheets (1 Sheet duy nhất):
 *   - Cột A (1): REF          (VD: S-BFHR4808, S-BFHR4308, S-BWFHR5508, S-BFHR4310)
 *   - Cột B (2): Product name (VD: Bone Level Fixture Hybrid, Bone Level Wide Fixture Hybrid)
 *   - Cột C (3): Size         (VD: 4.8X08 mm, 4.3X08 mm, 5.5X08 mm, 4.3X10 mm)
 *   - Cột D (4): CODE         (VD: 730080810250326033, 730060410250509018, 729061410240318007)
 *   - Cột E (5): LOT          (VD: 250326033000, 250509018000, 240318007000, 241023059000)
 * =========================================================================================
 */

// 1. CẤU HÌNH HỆ THỐNG
const SPREADSHEET_ID = "1ZyzEsatFjyjcqCA6Hn0SrA6jDoq05SV7ytv94vyvKD8";

/**
 * 2. XỬ LÝ REQUEST HTTP (doGet & doPost) HỖ TRỢ CORS
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
    const query = (params.query || params.search || params.code || params.lot || params.ref || "").trim();

    let result = {};

    switch (action) {
      case "search":
        result = searchImplantData(query);
        break;

      case "initSample":
        result = initSampleData();
        break;

      case "ping":
        result = { 
          success: true, 
          message: "Implantswiss Product Verification API is online & ready!", 
          timestamp: new Date().toISOString() 
        };
        break;

      default:
        result = searchImplantData(query);
        break;
    }

    return createJsonResponse(result);
  } catch (err) {
    return createJsonResponse({
      success: false,
      message: "Lỗi hệ thống: " + err.toString()
    });
  } finally {
    lock.releaseLock();
  }
}

function createJsonResponse(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * 3. HÀM TRA CỨU DỮ LIỆU TRỤ IMPLANT TỪ GOOGLE SHEET (1 SHEET DUY NHẤT)
 */
function searchImplantData(rawQuery) {
  if (!rawQuery) {
    return {
      success: false,
      notFound: true,
      message: "Vui lòng nhập Mã CODE, Số LOT hoặc Mã REF để tra cứu thông số trụ Implantswiss!"
    };
  }

  const queryClean = cleanString(rawQuery);

  if (!queryClean || queryClean.length < 3) {
    return {
      success: false,
      notFound: true,
      query: rawQuery,
      message: "Vui lòng nhập tối thiểu 3 ký tự (Mã CODE, Số LOT hoặc Mã REF)!"
    };
  }

  const ss = getSpreadsheet();
  const sheets = ss.getSheets();
  if (!sheets || sheets.length === 0) {
    return {
      success: false,
      message: "Không tìm thấy Sheet nào trong Google Spreadsheet!"
    };
  }

  const sheet = sheets[0];
  const data = sheet.getDataRange().getValues();

  if (data.length <= 1) {
    return {
      success: false,
      message: "Bảng tính hiện chưa có dữ liệu trụ Implant. Vui lòng cập nhật dữ liệu!"
    };
  }

  const headerMap = buildHeaderMap(data[0], {
    ref: ["ref", "mã ref", "ma ref", "reference", "mã sản phẩm", "ma san pham"],
    productName: ["product name", "product_name", "tên sản phẩm", "ten san pham", "tên trụ", "ten tru", "product", "sản phẩm", "san pham"],
    size: ["size", "kích thước", "kich thuoc", "dimension", "quy cách", "quy cach"],
    code: ["code", "mã code", "ma code", "barcode", "mã vạch", "ma vach", "udi", "serial"],
    lot: ["lot", "số lot", "so lot", "mã lot", "ma lot", "batch", "lô"]
  });

  const refIdx = headerMap.ref !== -1 ? headerMap.ref : 0;
  const prodIdx = headerMap.productName !== -1 ? headerMap.productName : 1;
  const sizeIdx = headerMap.size !== -1 ? headerMap.size : 2;
  const codeIdx = headerMap.code !== -1 ? headerMap.code : 3;
  const lotIdx = headerMap.lot !== -1 ? headerMap.lot : 4;

  const matchedList = [];

  for (let i = 1; i < data.length; i++) {
    const row = data[i];
    const rowRef = String(row[refIdx] || "").trim();
    const rowProd = String(row[prodIdx] || "").trim();
    const rowSize = String(row[sizeIdx] || "").trim();
    const rowCode = String(row[codeIdx] || "").trim();
    const rowLot = String(row[lotIdx] || "").trim();

    if (!rowRef && !rowCode && !rowLot) continue;

    const cleanRef = cleanString(rowRef);
    const cleanCode = cleanString(rowCode);
    const cleanLot = cleanString(rowLot);

    const matchCode = cleanCode && (cleanCode === queryClean || cleanCode.includes(queryClean) || (queryClean.length >= 8 && queryClean.includes(cleanCode)));
    const matchLot = cleanLot && (cleanLot === queryClean || (queryClean.length >= 6 && cleanLot.includes(queryClean)));
    const matchRef = cleanRef && (cleanRef === queryClean || (queryClean.length >= 5 && cleanRef.includes(queryClean)));

    if (matchCode || matchLot || matchRef) {
      matchedList.push({
        ref: rowRef || "N/A",
        productName: rowProd || "Bone Level Fixture Hybrid",
        size: rowSize || "N/A",
        code: rowCode || "N/A",
        lot: rowLot || "N/A",
        material: "Medical Grade 4 Titanium (Ti-G4)",
        origin: "Made in Switzerland (Thụy Sĩ)",
        warranty: "Lifetime 1-to-1 replacement warranty (Bảo hành 1 đổi 1 trọn đời)",
        technology: "SRA Surface - Sandblasted, Large Grit, Acid-Etched",
        standards: "CE 1984 / FDA / ISO 13485 Medical Grade",
        isGenuine: true,
        status: "Chính hãng - Đang lưu hành & được bảo hành toàn cầu"
      });
    }
  }

  if (matchedList.length === 0) {
    return {
      success: false,
      notFound: true,
      query: rawQuery,
      message: `Không tìm thấy thông tin trụ Implantswiss với từ khóa: "${rawQuery}". Vui lòng kiểm tra lại Mã CODE, Số LOT hoặc Mã REF!`
    };
  }

  const primaryItem = matchedList[0];
  const now = new Date();
  const timeStr = Utilities.formatDate(now, "Asia/Ho_Chi_Minh", "dd/MM/yyyy HH:mm:ss");
  const authCode = "SWISS-AUTH-" + Math.abs(hashCode(primaryItem.code + primaryItem.lot + primaryItem.ref));

  return {
    success: true,
    query: rawQuery,
    verificationCode: authCode,
    verifiedTime: timeStr,
    brand: "Implantswiss (Switzerland)",
    totalItems: matchedList.length,
    implant: primaryItem,
    implants: matchedList,
    message: "Xác thực trụ Implantswiss chính hãng Thụy Sĩ thành công!"
  };
}

/**
 * 4. HELPER FUNCTIONS
 */
function buildHeaderMap(headerRow, schema) {
  const map = {};
  for (const key in schema) {
    map[key] = -1;
    const aliases = schema[key];
    for (let col = 0; col < headerRow.length; col++) {
      const colName = String(headerRow[col] || "").toLowerCase().trim();
      if (aliases.some(alias => colName === alias || colName.includes(alias))) {
        map[key] = col;
        break;
      }
    }
  }
  return map;
}

function cleanString(str) {
  if (!str) return "";
  return String(str).toLowerCase().replace(/[^a-z0-9]/g, "");
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
 * 5. HÀM TỰ ĐỘNG ĐIỀN DỮ LIỆU MẪU THEO ĐÚNG CẤU TRÚC 1 SHEET (REF, Product name, Size, CODE, LOT)
 */
function initSampleData() {
  const ss = getSpreadsheet();
  const sheets = ss.getSheets();
  const sheet = sheets[0];
  sheet.clear();

  const headers = [["REF", "Product name", "Size", "CODE", "LOT"]];
  const rows = [
    ["S-BFHR4808", "Bone Level Fixture Hybrid", "4.8X08 mm", "730080810250326033", "250326033000"],
    ["S-BFHR4308", "Bone Level Fixture Hybrid", "4.3X08 mm", "730060410250509018", "250509018000"],
    ["S-BWFHR5508", "Bone Level Wide Fixture Hybrid", "5.5X08 mm", "729061410240318007", "240318007000"],
    ["S-BFHR4310", "Bone Level Fixture Hybrid", "4.3X10 mm", "0107640168180102173006041024102305900021241023059037", "241023059000"]
  ];

  sheet.getRange(1, 1, 1, 5).setValues(headers).setBackground("#1e293b").setFontColor("#ffffff").setFontWeight("bold");
  sheet.getRange(2, 1, rows.length, 5).setValues(rows);
  sheet.autoResizeColumns(1, 5);

  return {
    success: true,
    message: "Đã khởi tạo bảng dữ liệu chuẩn 5 cột (REF, Product name, Size, CODE, LOT) thành công!"
  };
}

