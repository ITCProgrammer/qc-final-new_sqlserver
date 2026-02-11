<?php
session_start();
include "../../koneksi.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

function writeLog($message) {
    $logFile = 'update_log_lapkpeqcf.txt';
    $timestamp = date('Y-m-d H:i:s');
    $formattedMessage = "[$timestamp] " . $message . "\n";
    file_put_contents($logFile, $formattedMessage, FILE_APPEND);
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    $msg = "Sesi tidak ditemukan. Silakan login ulang.";
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("Sesi tidak ditemukan");
    exit();
}

$user          = $_SESSION['user_id'];
$today         = date('Y-m-d H:i:s');

$nodemand      = $_POST['no_demand']      ?? '';
$nokk          = $_POST['no_kk']          ?? '';
$personil1     = $_POST['personil1']     ?? '';
$personil2     = $_POST['personil2']     ?? '';
$personil3     = $_POST['personil3']     ?? '';
$personil4     = $_POST['personil4']     ?? '';
$personil5     = $_POST['personil5']     ?? '';
$personil6     = $_POST['personil6']     ?? '';
$shift1        = $_POST['shift1']        ?? '';
$shift2        = $_POST['shift2']        ?? '';
$pejabat       = $_POST['pejabat']       ?? '';
$hitung        = $_POST['hitung']        ?? '';
$status        = $_POST['status']        ?? '';
$analisa       = $_POST['analisis']      ?? '';
$hasil_analisa = $_POST['hasil_analisa'] ?? '';

if ($nodemand === '') {
    $msg = "'nodemand' kosong. Tidak bisa lanjut.";
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("Gagal: nodemand kosong");
    exit();
}

if (!$con_db_qc_sqlsrv) {
    $errors = sqlsrv_errors();
    $msg = "Gagal koneksi DB.";
    if ($errors) {
        $msg .= " " . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("Gagal koneksi DB: " . $msg);
    exit();
}

$sqlCheck = "SELECT COUNT(*) AS cnt FROM db_qc.tbl_add_kpe_qcf WHERE no_demand = ?";
$stmtCheck = sqlsrv_prepare($con_db_qc_sqlsrv, $sqlCheck, array($nodemand));
if (!$stmtCheck || !sqlsrv_execute($stmtCheck)) {
    $errors = sqlsrv_errors();
    $msg = "Prepare/execute cek gagal.";
    if ($errors) {
        $msg .= " " . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("Prepare/execute cek gagal: " . $msg);
    exit();
}
$rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);
$exists = ($rowCheck && (int)$rowCheck['cnt'] > 0);
sqlsrv_free_stmt($stmtCheck);
writeLog("Cek data untuk '$nodemand': " . ($exists ? "ADA, akan di-UPDATE" : "TIDAK ADA, akan di-INSERT"));

if ($exists) {
    $sql = "UPDATE db_qc.tbl_add_kpe_qcf SET
            personil1=?, personil2=?, personil3=?, personil4=?, personil5=?, personil6=?,
            shift1=?, shift2=?, pejabat=?, hitung=?, status=?, analisis=?, hasil_analisa=?,
            update_user=?, update_date=?, nokk=?
            WHERE no_demand=?";
    $params = array(
        $personil1, $personil2, $personil3, $personil4, $personil5, $personil6,
        $shift1, $shift2, $pejabat, $hitung, $status, $analisa, $hasil_analisa,
        $user, $today, $nokk, $nodemand
    );
    $action = "UPDATE";
} else {
    $sql = "INSERT INTO db_qc.tbl_add_kpe_qcf (
            no_demand, personil1, personil2, personil3, personil4, personil5, personil6,
            shift1, shift2, pejabat, hitung, status, analisis, hasil_analisa, insert_user, insert_date, nokk
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = array(
        $nodemand, $personil1, $personil2, $personil3, $personil4, $personil5, $personil6,
        $shift1, $shift2, $pejabat, $hitung, $status, $analisa, $hasil_analisa, $user, $today, $nokk
    );
    $action = "INSERT";
}

$stmt = sqlsrv_prepare($con_db_qc_sqlsrv, $sql, $params);
if (!$stmt) {
    $errors = sqlsrv_errors();
    $msg = "Prepare $action gagal.";
    if ($errors) {
        $msg .= " " . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("Prepare $action gagal: " . $msg);
    exit();
}

if (sqlsrv_execute($stmt)) {
    $msg = "Data berhasil di-$action untuk No Demand: $nodemand";
    echo json_encode(['status' => 'ok', 'message' => $msg]);
    writeLog("$action berhasil untuk $nodemand");
} else {
    $errors = sqlsrv_errors();
    $msg = "$action gagal.";
    if ($errors) {
        $msg .= " " . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    writeLog("$action gagal: " . $msg);
}

sqlsrv_free_stmt($stmt);
writeLog("=== SELESAI ===\n");
?>
