/**
 * =========================================================================================
 * GOOGLE APPS SCRIPT - HỆ THỐNG TRA CỨU BẢO HÀNH & XÁC THỰC TRỤ IMPLANTSWISS CHÍNH HÃNG
 * =========================================================================================
 * Cấu trúc bảng Google Sheets (1 Sheet duy nhất):
 *   - Cột A (1): REF          (VD: S-BFHR4808, S-BFHR4308, S-BWFHR5508, S-BFHR4310) -> Mã quy cách/catalog
 *   - Cột B (2): Product name (VD: Bone Level Fixture Hybrid, Bone Level Wide Fixture Hybrid)
 *   - Cột C (3): Size         (VD: 4.8X08 mm, 4.3X08 mm, 5.5X08 mm, 4.3X10 mm)
 *   - Cột D (4): SN / CODE    (VD: 730080810250326033, 730060410250509018, 729061410240318007) -> MÃ ĐỊNH DANH DUY NHẤT
 *   - Cột E (5): LOT          (VD: 250326033000, 250509018000, 240318007000, 241023059000)
 * 
 * LƯU Ý QUAN TRỌNG:
 * Hệ thống tra cứu ĐƯỢC THIẾT LẬP CHỈ TRA CỨU BẰNG MÃ SN (Serial Number / CODE duy nhất).
 * KHÔNG tra cứu bằng mã REF vì mã REF có thể giống nhau giữa nhiều trụ cùng model/kích thước.
 * =========================================================================================
 */

// 1. CẤU HÌNH HỆ THỐNG
const SPREADSHEET_ID = "1Pvd5AxuTdAesPsHOl4dkWPsYdh83gUuqvPV3Htrz0x4";

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
    const query = (params.query || params.sn || params.serial || params.code || params.search || "").trim();

    let result = {};

    switch (action) {
      case "search":
        result = searchImplantData(query);
        break;

      case "getTabs":
        const allSheets = getSpreadsheet().getSheets();
        const tabList = [];
        for (let i = 0; i < allSheets.length; i++) {
          const sName = allSheets[i].getName();
          if (sName.trim().toUpperCase() !== "TEST") {
            tabList.push({
              gid: String(allSheets[i].getSheetId()),
              name: sName
            });
          }
        }
        result = { success: true, tabs: tabList };
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
 * BẢNG ÁNH XẠ GTIN CHUẨN QUỐC TẾ CỦA HÃNG NOVODENT IMPLANTSWISS
 */
const GTIN_MAP = {
  "07640168180058": { ref: "S-BFHR3708", prod: "Bone Level Fixture Hybrid", size: "3.7X08 mm" },
  "07640168180065": { ref: "S-BFHR3710", prod: "Bone Level Fixture Hybrid", size: "3.7X10 mm" },
  "07640168180072": { ref: "S-BFHR3712", prod: "Bone Level Fixture Hybrid", size: "3.7X12 mm" },
  "07640168180089": { ref: "S-BFHR3714", prod: "Bone Level Fixture Hybrid", size: "3.7X14 mm" },
  "07640168180096": { ref: "S-BFHR4308", prod: "Bone Level Fixture Hybrid", size: "4.3X08 mm" },
  "07640168180102": { ref: "S-BFHR4310", prod: "Bone Level Fixture Hybrid", size: "4.3X10 mm" },
  "07640168180119": { ref: "S-BFHR4312", prod: "Bone Level Fixture Hybrid", size: "4.3X12 mm" },
  "07640168180126": { ref: "S-BFHR4314", prod: "Bone Level Fixture Hybrid", size: "4.3X14 mm" },
  "07640168180133": { ref: "S-BFHR4808", prod: "Bone Level Fixture Hybrid", size: "4.8X08 mm" },
  "07640168180140": { ref: "S-BFHR4810", prod: "Bone Level Fixture Hybrid", size: "4.8X10 mm" },
  "07640168180157": { ref: "S-BFHR4812", prod: "Bone Level Fixture Hybrid", size: "4.8X12 mm" },
  "07640168180164": { ref: "S-BFHR4814", prod: "Bone Level Fixture Hybrid", size: "4.8X14 mm" },
  "07640168180171": { ref: "S-BWFHR5508", prod: "Bone Level Wide Fixture Hybrid", size: "5.5X08 mm" },
  "07640168180188": { ref: "S-BWFHR5510", prod: "Bone Level Wide Fixture Hybrid", size: "5.5X10 mm" },
  "07640168180195": { ref: "S-BWFHR5512", prod: "Bone Level Wide Fixture Hybrid", size: "5.5X12 mm" },
  "07640168180010": { ref: "S-BMFSR3308", prod: "Bone Level Mini Fixture Straight", size: "3.3X08 mm" },
  "07640168180027": { ref: "S-BMFSR3310", prod: "Bone Level Mini Fixture Straight", size: "3.3X10 mm" },
  "07640168180034": { ref: "S-BMFSR3312", prod: "Bone Level Mini Fixture Straight", size: "3.3X12 mm" },
  "07640168180041": { ref: "S-BMFSR3314", prod: "Bone Level Mini Fixture Straight", size: "3.3X14 mm" },
  "07640168184384": { ref: "S-BMFSR4305", prod: "Bone Level Mini Fixture Straight", size: "4.3X05 mm" },
  "07640168184391": { ref: "S-BMFSR4306", prod: "Bone Level Mini Fixture Straight", size: "4.3X06 mm" },
  "07640168184407": { ref: "S-BMFSR4805", prod: "Bone Level Mini Fixture Straight", size: "4.8X05 mm" },
  "07640168184414": { ref: "S-BMFSR4806", prod: "Bone Level Mini Fixture Straight", size: "4.8X06 mm" },
  "07640168184421": { ref: "S-BMFSR5505", prod: "Bone Level Mini Fixture Straight", size: "5.5X05 mm" },
  "07640168184438": { ref: "S-BMFSR5506", prod: "Bone Level Mini Fixture Straight", size: "5.5X06 mm" },
  "07630188202670": { ref: "S-BNFHR2910", prod: "Bone Level Narrow Hybrid Fixture", size: "2.9X10 mm" },
  "07630188202687": { ref: "S-BNFHR2912", prod: "Bone Level Narrow Hybrid Fixture", size: "2.9X12 mm" },
  "07630188202694": { ref: "S-BNFHR2914", prod: "Bone Level Narrow Hybrid Fixture", size: "2.9X14 mm" }
};

/**
 * 3. HÀM TRA CỨU DỮ LIỆU TRỤ IMPLANT TỪ GOOGLE SHEET (TÌM KIẾM TRÊN TẤT CẢ CÁC TAB NHA KHOA)
 */
function searchImplantData(rawQuery) {
  if (!rawQuery) {
    return {
      success: false,
      notFound: true,
      message: "Vui lòng nhập Mã SN (12 chữ số cuối của mã vạch) để tra cứu thông số trụ Implantswiss chính hãng!"
    };
  }

  const queryClean = cleanString(rawQuery);

  if (!queryClean || queryClean.length < 12) {
    return {
      success: false,
      notFound: true,
      query: rawQuery,
      message: "Vui lòng nhập đủ 12 chữ số của mã SN hoặc quét toàn bộ mã vạch để tra cứu!"
    };
  }

  // Chuẩn hóa SN cần tìm: Nếu người dùng nhập mã barcode dài (>= 12 ký tự), trích xuất 12 số cuối
  const querySN = queryClean.length >= 12 ? queryClean.slice(-12) : queryClean;

  const ss = getSpreadsheet();
  const sheets = ss.getSheets();
  if (!sheets || sheets.length === 0) {
    return {
      success: false,
      message: "Không tìm thấy Sheet nào trong Google Spreadsheet!"
    };
  }

  const matchedList = [];

  // LẶP QUA TẤT CẢ CÁC TAB TRONG GOOGLE SPREADSHEET (BỎ QUA TAB DEMO 'TEST')
  for (let s = 0; s < sheets.length; s++) {
    const sheet = sheets[s];
    const sheetName = sheet.getName();
    if (sheetName.trim().toUpperCase() === "TEST") continue; // Loại bỏ tab mẫu/demo

    const data = sheet.getDataRange().getValues();
    if (!data || data.length === 0) continue;

    const firstRowStr = (data[0] || []).join(" ").toUpperCase();
    const hasHeader = /REF|CODE|SẢN PHẨM|PRODUCT|MÃ|SERIAL/i.test(firstRowStr);
    const startIdx = hasHeader ? 1 : 0;

    const headerMap = hasHeader ? buildHeaderMap(data[0], {
      ref: ["mã sản phẩm (ref)", "mã ref", "ma ref", "reference", "ref"],
      productName: ["tên sản phẩm (product name)", "tên sản phẩm", "ten san pham", "product name", "product_name", "tên trụ", "ten tru"],
      size: ["size", "kích thước", "kich thuoc", "dimension", "quy cách", "quy cach"],
      code: ["code", "sn / code", "sn", "mã sn", "ma sn", "serial", "số serial", "so serial", "serial number", "mã code", "ma code", "barcode", "mã vạch", "ma vach", "udi"],
      lot: ["lot", "số lot", "so lot", "mã lot", "ma lot", "batch", "lô"],
      mfg: ["date of manufacture", "ngày sản xuất", "ngay san xuat", "mfg", "mfg date", "nsx"],
      exp: ["expiration date", "expiiration date", "hạn sử dụng", "han su dung", "hsd", "exp", "exp date"]
    }) : { ref: 0, productName: 1, size: 2, code: 3, lot: 4, mfg: -1, exp: -1 };

    const refIdx = headerMap.ref !== -1 ? headerMap.ref : 0;
    const prodIdx = headerMap.productName !== -1 ? headerMap.productName : 1;
    const sizeIdx = headerMap.size !== -1 ? headerMap.size : 2;
    const codeIdx = headerMap.code !== -1 ? headerMap.code : 3;
    const lotIdx = headerMap.lot !== -1 ? headerMap.lot : 4;
    const mfgIdx = headerMap.mfg;
    const expIdx = headerMap.exp;

    let lastRef = "";
    let lastProd = "";
    let lastSize = "";

    for (let i = startIdx; i < data.length; i++) {
      const row = data[i];
      let rowRef = String(row[refIdx] || "").trim();
      let rowProd = String(row[prodIdx] || "").trim();
      let rowSize = String(row[sizeIdx] || "").trim();
      const rowCode = String(row[codeIdx] || "").trim();
      const rowLot = String(row[lotIdx] || "").trim().replace(/\.0+$/, "");
      const rowMfg = mfgIdx !== -1 ? String(row[mfgIdx] || "").trim() : "";
      const rowExp = expIdx !== -1 ? String(row[expIdx] || "").trim() : "";

      if (!rowCode && !rowRef && !rowLot) continue;

      // Xử lý ô bị gộp (Merged cells fill-down)
      if (rowRef) lastRef = rowRef;
      else rowRef = lastRef;

      if (rowProd) lastProd = rowProd;
      else rowProd = lastProd;

      if (rowSize) lastSize = rowSize;
      else rowSize = lastSize;

      // Tự động phân giải qua GTIN nếu ref/size còn trống
      const gtinMatch = rowCode.match(/^01(\d{14})/);
      if (gtinMatch && GTIN_MAP[gtinMatch[1]]) {
        const cat = GTIN_MAP[gtinMatch[1]];
        if (!rowRef || rowRef === "N/A") rowRef = cat.ref;
        if (!rowProd || rowProd === "N/A" || rowProd === rowRef) rowProd = cat.prod;
        if (!rowSize || rowSize === "N/A") rowSize = cat.size;
      }

      if (!rowProd || rowProd === rowRef) {
        rowProd = "Bone Level Fixture Hybrid";
      }

      const cleanCode = cleanString(rowCode);
      const sn12 = cleanCode.length >= 12 ? cleanCode.slice(-12) : cleanCode;

      // QUY TẮC: CHỈ TRA CỨU CHÍNH XÁC BẰNG MÃ SN (12 SỐ CUỐI) HOẶC TOÀN BỘ MÃ BARCODE
      let isMatch = false;
      if (queryClean.length >= 12) {
        isMatch = (sn12 === querySN) || (cleanCode === queryClean);
      }

      if (isMatch) {
        const dates = parseDatesFromGS1(rowCode, rowLot, rowMfg, rowExp);

        matchedList.push({
          ref: rowRef || "N/A",
          productName: rowProd,
          size: rowSize || "N/A",
          code: rowCode || "N/A",
          sn: sn12 || rowCode || "N/A",
          sn_12: sn12,
          lot: rowLot || dates.lot || "N/A",
          clinic: sheetName,
          dateOfManufacture: dates.mfgDate,
          expirationDate: dates.expDate,
          material: "Medical Grade 4 Titanium (Ti-G4)",
          origin: "Made in Switzerland (Novodent SA)",
          warranty: "Lifetime 1-to-1 replacement warranty (Bảo hành 1 đổi 1 trọn đời)",
          technology: "SRA Surface - Sandblasted, Large Grit, Acid-Etched",
          standards: "CE 1984 / FDA / ISO 13485 Medical Grade",
          isGenuine: true,
          status: "Chính hãng - Đang lưu hành & được bảo hành toàn cầu"
        });
      }
    }
  }

  if (matchedList.length === 0) {
    return {
      success: false,
      notFound: true,
      query: rawQuery,
      message: `Không tìm thấy thông tin trụ Implantswiss với mã: "${rawQuery}". Lưu ý: Hệ thống tra cứu chỉ định danh chuẩn xác bằng Mã SN (12 chữ số cuối của mã vạch). Mã REF hoặc Số LOT không được dùng để định danh duy nhất từng trụ!`
    };
  }

  const primaryItem = matchedList[0];
  const now = new Date();
  const timeStr = Utilities.formatDate(now, "Asia/Ho_Chi_Minh", "dd/MM/yyyy HH:mm:ss");
  const snVal = primaryItem.sn || (primaryItem.code && primaryItem.code.length >= 12 ? primaryItem.code.slice(-12) : primaryItem.code) || rawQuery;
  const authCode = snVal;

  return {
    success: true,
    query: rawQuery,
    verificationCode: authCode,
    verifiedTime: timeStr,
    manufacturer: "Novodent SA (Switzerland)",
    brand: "Implantswiss",
    totalItems: matchedList.length,
    implant: primaryItem,
    implants: matchedList,
    message: "Xác nhận trụ Implantswiss chính hãng từ Novodent SA (Thụy Sĩ) thành công!"
  };
}

/**
 * Trích xuất ngày sản xuất và hạn sử dụng từ GS1-128 / LOT
 */
function parseDatesFromGS1(codeStr, lotStr, explicitMfg, explicitExp) {
  let mfg = explicitMfg || "";
  let exp = explicitExp || "";
  const code = String(codeStr || "").trim();
  const lot = String(lotStr || "").trim();

  if (!exp && code) {
    const matchExp = code.match(/^01\d{14}17(\d{2})(\d{2})(\d{2})/) || code.match(/(?:^|\D|01\d{14})17(\d{2})(\d{2})(\d{2})(?:10|21|\D|$)/);
    if (matchExp) {
      const yy = matchExp[1];
      const mm = matchExp[2];
      const dd = matchExp[3];
      const fullYear = parseInt(yy, 10) > 50 ? "19" + yy : "20" + yy;
      exp = `${dd}/${mm}/${fullYear}`;
    }
  }

  if (!mfg && lot && lot.length >= 6) {
    const yy = lot.substring(0, 2);
    const mm = lot.substring(2, 4);
    const dd = lot.substring(4, 6);
    const mNum = parseInt(mm, 10);
    const dNum = parseInt(dd, 10);
    if (mNum >= 1 && mNum <= 12 && dNum >= 1 && dNum <= 31) {
      const fullYear = parseInt(yy, 10) > 50 ? "19" + yy : "20" + yy;
      mfg = `${dd}/${mm}/${fullYear}`;
    }
  }

  if (mfg && !exp) {
    const p = mfg.split(/[\/\-\.]/);
    if (p.length === 3) exp = `${p[0]}/${p[1]}/${parseInt(p[2], 10) + 5}`;
  }
  if (exp && !mfg) {
    const p = exp.split(/[\/\-\.]/);
    if (p.length === 3) mfg = `${p[0]}/${p[1]}/${parseInt(p[2], 10) - 5}`;
  }

  return {
    mfgDate: mfg || "Theo lô sản xuất",
    expDate: exp || "5 năm kể từ NSX"
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

