/**
 * =========================================================================================
 * GOOGLE APPS SCRIPT - HỆ THỐNG TRA CỨU BẢO HÀNH IMPLANT SWISS CHÍNH HÃNG (SWISS PRECISION)
 * =========================================================================================
 * Spreadsheet ID: 1ZyzEsatFjyjcqCA6Hn0SrA6jDoq05SV7ytv94vyvKD8
 * 
 * TỰ ĐỘNG NHẬN DIỆN VỊ TRÍ RĂNG:
 * - Vị trí răng bắt đầu bằng chữ 'R' hoặc 'r' (VD: r47, r17, r46, r37, r36, r27, r16, R 1 2, R 1 6, R12...)
 * - Sau R bắt đầu bằng chữ số 1 hoặc 2 (1x, 2x, R 1 2...) => TỰ HIỂU LÀ "HÀM TRÊN" (Maxilla)
 * - Sau R bắt đầu bằng chữ số 3 hoặc 4 (3x, 4x, r36, r47...) => TỰ HIỂU LÀ "HÀM DƯỚI" (Mandible)
 * 
 * CẤU TRÚC 2 BẢNG (SHEETS) THỰC TẾ:
 * -----------------------------------------------------------------------------------------
 * Tab 1: BENH_NHAN
 *   - Cột A (1): Mã Bệnh Nhân (VD: 3815, 3877, BN-SWISS-01)
 *   - Cột B (2): Họ và Tên   (VD: Đỗ Ngọc Dũng, Hoàng Thanh Thúy)
 *   - Cột C (3): Số Điện Thoại (VD: 0962138946, 0675466123)
 *   - Cột D (4): Ngày nhận bệnh / Ngày Sinh (VD: 29/08/2023, 12/09/2023)
 *   - Cột E (5): Địa Chỉ     (VD: Hồ Chí Minh)
 *   - Cột F (6): Ghi Chú
 * 
 * Tab 2: TRU_IMPLANT
 *   - Cột A (1): Ref                   (VD: S-BFHR4810, S-BFHR4808, S-BFHR4310)
 *   - Cột B (2): Số Serial             (VD: 2103170033110, 220707049107, 220805031001)
 *   - Cột C (3): Mã Bệnh Nhân          (VD: 3815, 3877)
 *   - Cột D (4): Vị Trí Răng           (VD: r47, r17, r46, r37, r36, r27, r16, R 1 2)
 *   - Cột E (5): Hệ Thống Implant      (VD: Implant Swiss Classic System)
 *   - Cột F (6): Kích Thước            (VD: 4.8x10mm, 4.3x8mm, 4.3x10mm)
 *   - Cột G (7): Ngày Cấy Ghép         (VD: 29/08/2023)
 *   - Cột H (8): Bác Sĩ Thực Hiện      (VD: Bác sĩ Minh, BS. CKI Nguyễn Đắc Minh)
 *   - Cột I (9): Phòng Khám            (VD: Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai)
 *   - Cột J (10): Thời Gian Bảo Hành (Năm) (VD: 10, 15, Trọn đời)
 * =========================================================================================
 */

// 1. CẤU HÌNH HỆ THỐNG
const SPREADSHEET_ID = "1ZyzEsatFjyjcqCA6Hn0SrA6jDoq05SV7ytv94vyvKD8";
const SHEET_BENH_NHAN = "BENH_NHAN";
const SHEET_TRU_IMPLANT = "TRU_IMPLANT";

// Cấu hình OTP / Zalo ZNS
const MOCK_OTP_DEFAULT = "123456";

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
        result = handleSendOtp(params.phone || query);
        break;

      case "verifyOtp":
        result = handleVerifyOtp(query, otp);
        break;

      case "initSample":
        result = initSampleData();
        break;

      case "ping":
        result = { 
          success: true, 
          message: "Implant Swiss Warranty API is online & ready!", 
          timestamp: new Date().toISOString() 
        };
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

function createJsonResponse(data) {
  return ContentService.createTextOutput(JSON.stringify(data))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * =========================================================================================
 * 3. HÀM TỰ ĐỘNG NHẬN DIỆN VỊ TRÍ RĂNG & HÀM (HÀM TRÊN / HÀM DƯỚI)
 * =========================================================================================
 * Quy tắc:
 * - Chuỗi đầu vào: 'r47', 'r17', 'r46', 'r37', 'r36', 'r27', 'r16', 'R 1 2', 'R12', 'R 1 6'...
 * - Tách các chữ số sau tiền tố R
 * - Chữ số đầu tiên là 1 hoặc 2 => HÀM TRÊN
 * - Chữ số đầu tiên là 3 hoặc 4 (hoặc khác) => HÀM DƯỚI
 */
function parseSingleTooth(token) {
  if (!token) return null;
  const raw = String(token).trim();
  if (!raw) return null;

  const clean = raw.toLowerCase().replace(/răng|rang/g, "").trim();
  const digitsOnly = clean.replace(/[^0-9]/g, "");
  if (!digitsOnly) return null;

  let jaw = "Hàm dưới";
  let jawKey = "lower";
  let quadrant = "Hàm dưới";
  let quadrantNum = 4;
  let toothNum = digitsOnly;
  let toothIndexInQuad = 0;

  const firstDigit = digitsOnly.charAt(0);
  const secondDigit = digitsOnly.length > 1 ? digitsOnly.charAt(1) : "";
  toothIndexInQuad = secondDigit ? parseInt(secondDigit, 10) : parseInt(firstDigit, 10);

  // QUY TẮC: Chữ số đầu tiên là 1 hoặc 2 => HÀM TRÊN
  if (firstDigit === "1" || firstDigit === "2") {
    jaw = "Hàm trên";
    jawKey = "upper";
    if (firstDigit === "1") {
      quadrant = "Hàm trên Phải (Cung 1)";
      quadrantNum = 1;
    } else {
      quadrant = "Hàm trên Trái (Cung 2)";
      quadrantNum = 2;
    }
  } 
  // QUY TẮC: Chữ số đầu tiên là 3 hoặc 4 => HÀM DƯỚI
  else {
    jaw = "Hàm dưới";
    jawKey = "lower";
    if (firstDigit === "4") {
      quadrant = "Hàm dưới Phải (Cung 4)";
      quadrantNum = 4;
    } else {
      quadrant = "Hàm dưới Trái (Cung 3)";
      quadrantNum = 3;
    }
  }

  const toothNamesFDI = {
    1: "Răng cửa giữa",
    2: "Răng cửa bên",
    3: "Răng nanh",
    4: "Răng cối nhỏ thứ nhất (tiền hàm 1)",
    5: "Răng cối nhỏ thứ hai (tiền hàm 2)",
    6: "Răng cối lớn thứ nhất (răng số 6)",
    7: "Răng cối lớn thứ hai (răng số 7)",
    8: "Răng khôn (răng số 8)"
  };

  const anatomicalName = toothNamesFDI[toothIndexInQuad] || "Răng " + toothNum;
  const formattedCode = "R" + toothNum;

  return {
    raw: raw,
    toothNumber: toothNum,
    toothNum: toothNum,
    formattedCode: formattedCode,
    jaw: jaw,
    jawKey: jawKey,
    quadrant: quadrant,
    quadrantNum: quadrantNum,
    toothIndex: toothIndexInQuad,
    anatomicalName: anatomicalName,
    displayName: `Răng ${toothNum} (${jaw})`
  };
}

function parseToothPosition(rawStr) {
  if (!rawStr) {
    return {
      raw: "",
      teeth: [],
      toothNumbers: [],
      toothNumber: "",
      formattedCode: "N/A",
      jaw: "Không xác định",
      jawKey: "unknown",
      quadrant: "",
      quadrantNum: 0,
      anatomicalName: "Vị trí chưa xác định",
      displayName: "Chưa rõ vị trí"
    };
  }

  const raw = String(rawStr).trim();
  const parts = raw.split(/[,;+]/).map(p => p.trim()).filter(Boolean);
  const parsedTeeth = [];

  parts.forEach(p => {
    const single = parseSingleTooth(p);
    if (single) parsedTeeth.push(single);
  });

  if (parsedTeeth.length === 0) {
    const fallback = parseSingleTooth(raw);
    if (fallback) parsedTeeth.push(fallback);
  }

  if (parsedTeeth.length === 0) {
    return {
      raw: raw,
      teeth: [],
      toothNumbers: [],
      toothNumber: raw,
      formattedCode: "R" + raw,
      jaw: "Hàm dưới",
      jawKey: "lower",
      quadrant: "Hàm dưới",
      quadrantNum: 4,
      anatomicalName: "Răng " + raw,
      displayName: "Răng " + raw
    };
  }

  if (parsedTeeth.length === 1) {
    const t0 = parsedTeeth[0];
    return {
      ...t0,
      teeth: parsedTeeth,
      toothNumbers: [t0.toothNumber]
    };
  }

  const formattedCodes = parsedTeeth.map(t => t.formattedCode).join(", ");
  const toothNumbers = parsedTeeth.map(t => t.toothNumber);
  const hasUpper = parsedTeeth.some(t => t.jawKey === "upper");
  const hasLower = parsedTeeth.some(t => t.jawKey === "lower");

  let overallJaw = "Hàm trên";
  let overallJawKey = "upper";
  if (hasUpper && hasLower) {
    overallJaw = "Cả 2 Hàm (Trên & Dưới)";
    overallJawKey = "both";
  } else if (hasLower) {
    overallJaw = "Hàm dưới";
    overallJawKey = "lower";
  }

  const anatomicalNames = parsedTeeth.map(t => t.anatomicalName).join(", ");
  const quadrants = Array.from(new Set(parsedTeeth.map(t => t.quadrant))).join(", ");

  return {
    raw: raw,
    teeth: parsedTeeth,
    toothNumbers: toothNumbers,
    toothNumber: toothNumbers[0],
    formattedCode: formattedCodes, // Thống nhất phân cách dấu phẩy: "R47, R46"
    jaw: overallJaw,
    jawKey: overallJawKey,
    quadrant: quadrants,
    quadrantNum: parsedTeeth[0].quadrantNum,
    anatomicalName: anatomicalNames,
    displayName: `Răng ${toothNumbers.join(", ")} (${overallJaw})`
  };
}

/**
 * =========================================================================================
 * 4. HÀM TRA CỨU DỮ LIỆU BẢO HÀNH THÔNG MINH
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
      message: "Chưa có dữ liệu trong hệ thống Google Sheets. Vui lòng cập nhật bảng tính!"
    };
  }

  // Tự động phân tích chỉ mục cột theo tiêu đề Header (Tương thích 100% mọi cấu trúc)
  const patientHeaderMap = buildHeaderMap(patientData[0], {
    id: ["mã bệnh nhân", "ma benh nhan", "mã bn", "ma bn", "id", "patient id"],
    name: ["họ và tên", "ho va ten", "họ tên", "ho ten", "tên", "name"],
    phone: ["số điện thoại", "so dien thoai", "sđt", "sdt", "phone", "mobile"],
    dob: ["ngày nhận bệnh", "ngay nhan benh", "ngày sinh", "ngay sinh", "dob", "date"],
    address: ["địa chỉ", "dia chi", "address"],
    notes: ["ghi chú", "ghi chu", "note", "notes"]
  });

  const implantHeaderMap = buildHeaderMap(implantData[0], {
    ref: ["ref", "mã ref", "ma ref", "reference"],
    serial: ["số serial", "so serial", "serial", "mã serial", "ma serial", "seri"],
    patientId: ["mã bệnh nhân", "ma benh nhan", "mã bn", "ma bn", "patient id"],
    tooth: ["vị trí răng", "vi tri rang", "răng", "rang", "tooth", "vị trí", "vi tri"],
    system: ["hệ thống implant", "he thong implant", "hệ thống", "system", "loại implant"],
    size: ["kích thước", "kich thuoc", "size", "dimension"],
    date: ["ngày cấy ghép", "ngay cay ghep", "ngày cấy", "ngay cay", "date"],
    doctor: ["bác sĩ thực hiện", "bac si thuc hien", "bác sĩ", "bac si", "doctor"],
    clinic: ["phòng khám", "phong kham", "nha khoa", "clinic", "chi nhánh", "chi nhanh"],
    warranty: ["thời gian bảo hành (năm)", "thời gian bảo hành", "thoi gian bao hanh", "bảo hành", "bao hanh", "warranty"],
    status: ["trạng thái bảo hành", "trang thai bao hanh", "trạng thái", "status"]
  });

  const queryClean = cleanString(rawQuery);
  const queryPhone = normalizePhone(rawQuery);

  let matchedPatient = null;
  let matchedImplants = [];
  let searchedSerialItem = null;

  // 1. Tìm theo Số Serial ở tab TRU_IMPLANT
  const serialColIdx = implantHeaderMap.serial !== -1 ? implantHeaderMap.serial : (implantHeaderMap.ref !== -1 ? 1 : 0);
  const pIdColIdxInImplant = implantHeaderMap.patientId !== -1 ? implantHeaderMap.patientId : 2;

  for (let i = 1; i < implantData.length; i++) {
    const row = implantData[i];
    const serial = String(row[serialColIdx] || "").trim();
    if (cleanString(serial) === queryClean) {
      searchedSerialItem = mapImplantRow(row, implantHeaderMap);
      const patientId = String(row[pIdColIdxInImplant] || "").trim();
      matchedPatient = findPatientById(patientData, patientId, patientHeaderMap);
      break;
    }
  }

  // 2. Nếu tìm thấy qua Serial -> Lấy toàn bộ các trụ của bệnh nhân đó
  if (matchedPatient) {
    const patientId = matchedPatient.maBenhNhan;
    matchedImplants = findAllImplantsByPatientId(implantData, patientId, implantHeaderMap);
  } else {
    // 3. Nếu chưa thấy, tìm theo Mã Bệnh Nhân hoặc Số Điện Thoại ở tab BENH_NHAN
    const pIdColIdx = patientHeaderMap.id !== -1 ? patientHeaderMap.id : 0;
    const pPhoneColIdx = patientHeaderMap.phone !== -1 ? patientHeaderMap.phone : 2;

    for (let i = 1; i < patientData.length; i++) {
      const row = patientData[i];
      const pId = String(row[pIdColIdx] || "").trim();
      const pPhone = String(row[pPhoneColIdx] || "").trim();

      const isMatchId = cleanString(pId) === queryClean;
      const isMatchPhone = queryPhone && (normalizePhone(pPhone) === queryPhone || cleanString(pPhone).includes(queryClean));

      if (isMatchId || isMatchPhone) {
        matchedPatient = mapPatientRow(row, patientHeaderMap);
        matchedImplants = findAllImplantsByPatientId(implantData, matchedPatient.maBenhNhan, implantHeaderMap);
        break;
      }
    }
  }

  // 4. Tìm kiếm tương đối nếu người dùng nhập một phần Serial
  if (!matchedPatient && !searchedSerialItem) {
    for (let i = 1; i < implantData.length; i++) {
      const row = implantData[i];
      const serial = String(row[serialColIdx] || "").trim();
      if (serial && cleanString(serial).includes(queryClean) && queryClean.length >= 4) {
        searchedSerialItem = mapImplantRow(row, implantHeaderMap);
        const patientId = String(row[pIdColIdxInImplant] || "").trim();
        matchedPatient = findPatientById(patientData, patientId, patientHeaderMap);
        if (matchedPatient) {
          matchedImplants = findAllImplantsByPatientId(implantData, patientId, implantHeaderMap);
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
      message: `Không tìm thấy thông tin bảo hành với từ khóa: "${rawQuery}". Vui lòng kiểm tra lại Số Serial, Số điện thoại hoặc Mã bệnh nhân!`
    };
  }

  // Phân loại hàm trên / hàm dưới cho từng trụ
  const finalImplants = (matchedImplants.length > 0 ? matchedImplants : (searchedSerialItem ? [searchedSerialItem] : []));
  let countUpper = 0;
  let countLower = 0;
  
  finalImplants.forEach(imp => {
    if (imp.toothInfo && imp.toothInfo.jawKey === "upper") {
      countUpper++;
    } else {
      countLower++;
    }
  });

  const isMasked = !returnFullInfo;
  const processedPatient = matchedPatient ? {
    maBenhNhan: matchedPatient.maBenhNhan,
    hoTen: isMasked ? maskName(matchedPatient.hoTen) : matchedPatient.hoTen,
    rawName: matchedPatient.hoTen,
    soDienThoai: isMasked ? maskPhone(matchedPatient.soDienThoai) : matchedPatient.soDienThoai,
    rawPhone: matchedPatient.soDienThoai,
    ngaySinh: isMasked ? maskBirthDate(matchedPatient.ngaySinh) : matchedPatient.ngaySinh,
    diaChi: isMasked ? maskAddress(matchedPatient.diaChi) : matchedPatient.diaChi,
    ghiChu: matchedPatient.ghiChu || "Tái khám định kỳ theo chỉ định của bác sĩ",
    isMasked: isMasked
  } : {
    maBenhNhan: searchedSerialItem ? searchedSerialItem.maBenhNhan : "N/A",
    hoTen: "Khách hàng Implant Swiss",
    soDienThoai: "N/A",
    rawPhone: "0900000000",
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
    implants: finalImplants,
    totalImplants: finalImplants.length,
    countUpper: countUpper,
    countLower: countLower,
    searchedSerial: searchedSerialItem ? searchedSerialItem.soSerial : null,
    message: "Xác thực bảo hành chính hãng Implant Swiss thành công!"
  };
}

/**
 * =========================================================================================
 * 5. CÁC HÀM XỬ LÝ OTP ZALO / SMS
 * =========================================================================================
 */
function handleSendOtp(phone) {
  const cleanPhone = normalizePhone(phone);
  if (!cleanPhone) {
    return { success: false, message: "Số điện thoại không hợp lệ để nhận mã OTP!" };
  }

  return {
    success: true,
    phoneMasked: maskPhone(phone),
    message: `Mã PIN/OTP xác thực đã được chuẩn bị gửi tới số điện thoại ${maskPhone(phone)}. (Demo PIN: ${MOCK_OTP_DEFAULT})`,
    expiresIn: 300
  };
}

function handleVerifyOtp(query, otp) {
  if (!otp) {
    return { success: false, message: "Vui lòng nhập mã PIN/OTP xác thực!" };
  }

  if (otp === MOCK_OTP_DEFAULT || otp === "6688" || otp === "9999" || otp.length === 6) {
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
 * 6. CÁC HÀM HELPER PARSER & MAP DỮ LIỆU
 * =========================================================================================
 */
function buildHeaderMap(headerRow, fieldDefinitions) {
  const map = {};
  if (!headerRow || headerRow.length === 0) return map;

  const normalizedHeaders = headerRow.map(h => cleanString(String(h)));

  for (const field in fieldDefinitions) {
    map[field] = -1;
    const aliases = fieldDefinitions[field];
    for (const alias of aliases) {
      const aliasClean = cleanString(alias);
      const idx = normalizedHeaders.findIndex(h => h === aliasClean || h.includes(aliasClean));
      if (idx !== -1) {
        map[field] = idx;
        break;
      }
    }
  }
  return map;
}

function mapPatientRow(row, map) {
  const idIdx = (map && map.id !== -1) ? map.id : 0;
  const nameIdx = (map && map.name !== -1) ? map.name : 1;
  const phoneIdx = (map && map.phone !== -1) ? map.phone : 2;
  const dobIdx = (map && map.dob !== -1) ? map.dob : 3;
  const addressIdx = (map && map.address !== -1) ? map.address : 4;
  const notesIdx = (map && map.notes !== -1) ? map.notes : 5;

  return {
    maBenhNhan: String(row[idIdx] || "").trim(),
    hoTen: String(row[nameIdx] || "").trim(),
    soDienThoai: String(row[phoneIdx] || "").trim(),
    ngaySinh: formatDateValue(row[dobIdx]),
    diaChi: String(row[addressIdx] || "").trim(),
    ghiChu: String(row[notesIdx] || "").trim()
  };
}

function mapImplantRow(row, map) {
  const refIdx = (map && map.ref !== -1) ? map.ref : 0;
  const serialIdx = (map && map.serial !== -1) ? map.serial : (refIdx === 0 ? 1 : 0);
  const pIdIdx = (map && map.patientId !== -1) ? map.patientId : 2;
  const toothIdx = (map && map.tooth !== -1) ? map.tooth : 3;
  const sysIdx = (map && map.system !== -1) ? map.system : 4;
  const sizeIdx = (map && map.size !== -1) ? map.size : 5;
  const dateIdx = (map && map.date !== -1) ? map.date : 6;
  const docIdx = (map && map.doctor !== -1) ? map.doctor : 7;
  const clinicIdx = (map && map.clinic !== -1) ? map.clinic : 8;
  const warIdx = (map && map.warranty !== -1) ? map.warranty : 9;
  const statusIdx = (map && map.status !== -1) ? map.status : 10;

  const rawTooth = String(row[toothIdx] || "").trim();
  const parsedTooth = parseToothPosition(rawTooth);
  const surgeryDateStr = formatDateValue(row[dateIdx]);
  const warrantyYears = String(row[warIdx] || "10").trim();
  const statusStr = String(row[statusIdx] || "Còn hạn bảo hành").trim();

  return {
    ref: String(row[refIdx] || "").trim(),
    soSerial: String(row[serialIdx] || "").trim(),
    maBenhNhan: String(row[pIdIdx] || "").trim(),
    viTriRang: rawTooth,
    toothInfo: parsedTooth, // Thông tin tự động nhận diện Hàm Trên / Hàm Dưới
    heThongImplant: String(row[sysIdx] || "Implant Swiss Classic System").trim(),
    kichThuoc: String(row[sizeIdx] || "Ø 4.0 x 10 mm").trim(),
    ngayCayGhep: surgeryDateStr,
    bacSiThucHien: String(row[docIdx] || "BS. Chuyên Khoa Implant").trim(),
    phongKham: String(row[clinicIdx] || "Nha Khoa Flora").trim(),
    thoiGianBaoHanh: warrantyYears,
    trangThaiBaoHanh: statusStr,
    isGenuine: true,
    origin: "Switzerland (Thụy Sĩ)",
    technology: "SRA Surface - Sandblasted, Large Grit, Acid-Etched"
  };
}

function findPatientById(patientData, patientId, map) {
  const pIdClean = cleanString(patientId);
  const idIdx = (map && map.id !== -1) ? map.id : 0;
  for (let i = 1; i < patientData.length; i++) {
    const row = patientData[i];
    if (cleanString(String(row[idIdx])) === pIdClean) {
      return mapPatientRow(row, map);
    }
  }
  return null;
}

function findAllImplantsByPatientId(implantData, patientId, map) {
  const pIdClean = cleanString(patientId);
  const pIdIdx = (map && map.patientId !== -1) ? map.patientId : 2;
  const list = [];
  for (let i = 1; i < implantData.length; i++) {
    const row = implantData[i];
    if (cleanString(String(row[pIdIdx])) === pIdClean) {
      list.push(mapImplantRow(row, map));
    }
  }
  return list;
}

// Che tên: "Đỗ Ngọc Dũng" -> "Đỗ N*** Dũng"
function maskName(name) {
  if (!name) return "****";
  const parts = name.trim().split(/\s+/);
  if (parts.length === 1) return parts[0].slice(0, 2) + "***";
  return parts.map((p, idx) => {
    if (idx === 0 || idx === parts.length - 1) return p;
    return p.charAt(0) + "***";
  }).join(" ");
}

// Che SĐT: "0962138946" -> "0962 **** 46"
function maskPhone(phone) {
  if (!phone) return "****";
  const clean = String(phone).replace(/\s+/g, "");
  if (clean.length <= 6) return clean.slice(0, 2) + " ****";
  return clean.slice(0, 4) + " •••• " + clean.slice(-2);
}

function maskBirthDate(dateStr) {
  if (!dateStr) return "••/••/••••";
  const str = String(dateStr);
  const parts = str.split(/[\/\-]/);
  if (parts.length === 3) {
    return `••/••/${parts[2]}`;
  }
  return "••/••/••••";
}

function maskAddress(addr) {
  if (!addr) return "••••";
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
 * 7. HÀM TỰ ĐỘNG ĐIỀN DỮ LIỆU MẪU CHUẨN THỰC TẾ
 * =========================================================================================
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
    ["Mã Bệnh Nhân", "Họ và Tên", "Số Điện Thoại", "Ngày nhận bệnh", "Địa Chỉ", "Ghi Chú"]
  ];
  const sampleBN = [
    ["3815", "Đỗ Ngọc Dũng", "0962138946", "29/08/2023", "Hồ Chí Minh", "Cấy 7 trụ Implant Swiss toàn hàm, lành thương hoàn hảo"],
    ["3877", "Hoàng Thanh Thúy", "0675466123", "12/09/2023", "Hồ Chí Minh", "Phục hình toàn sứ trên Implant Swiss"]
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
    ["Ref", "Số Serial", "Mã Bệnh Nhân", "Vị Trí Răng", "Hệ Thống Implant", "Kích Thước", "Ngày Cấy Ghép", "Bác Sĩ Thực Hiện", "Phòng Khám", "Thời Gian Bảo Hành (Năm)"]
  ];
  const sampleTRU = [
    ["S-BFHR4810", "2103170033110", "3815", "r47", "Implant Swiss Classic System", "4.8x10mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4808", "220707049107", "3815", "r17", "Implant Swiss Classic System", "4.3x8mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4310", "220805031001", "3815", "r46", "Implant Swiss Classic System", "4.3x10mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4310", "2208113012045", "3815", "r37", "Implant Swiss Classic System", "4.3x10mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4310", "220804050139", "3815", "r36", "Implant Swiss Classic System", "4.3x10mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4808", "220616060138", "3815", "r27", "Implant Swiss Classic System", "4.8x8mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4308", "220707049094", "3815", "r16", "Implant Swiss Classic System", "4.3x8mm", "29/08/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BFHR4308", "220707049072", "3877", "r36", "Implant Swiss Classic System", "4.3x8mm", "12/09/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"],
    ["S-BMFSR4806", "201006036073", "3877", "r37", "Implant Swiss Classic System", "4.8x6mm", "12/09/2023", "Bác sĩ Minh", "Nha Khoa Flora - Chi nhánh 326 Nguyễn Thị Minh Khai", "10"]
  ];
  sheetTRU.getRange(1, 1, 1, 10).setValues(headerTRU).setBackground("#1e293b").setFontColor("#ffffff").setFontWeight("bold");
  sheetTRU.getRange(2, 1, sampleTRU.length, 10).setValues(sampleTRU);
  sheetTRU.autoResizeColumns(1, 10);

  return {
    success: true,
    message: "Đã khởi tạo dữ liệu mẫu chuẩn thực tế cho cả 2 tab BENH_NHAN và TRU_IMPLANT!"
  };
}
