<?php
ini_set("error_reporting", 1);
session_start();

ob_start();
header('Content-Type: application/json; charset=utf-8');

require_once "../../koneksi.php";

if (!$con_db_qc_sqlsrv) {
    ob_end_clean();
    $errors = sqlsrv_errors();
    $msg = 'Koneksi SQL Server (db_qc) gagal.';
    if ($errors) {
        $msg .= ' ' . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

// INPUT
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$field = isset($_POST['field']) ? trim($_POST['field']) : '';
$value = isset($_POST['value']) ? (int) $_POST['value'] : null;

// VALIDASI
$allowedValues = [5, 10, 15, 20, 25, 30];

// WAJIB whitelist kolom yang boleh diupdate
$allowedFields = ['pengurangan_poin']; 

if ($id <= 0) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
    exit;
}
if (!in_array($value, $allowedValues, true)) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Value tidak valid']);
    exit;
}
if (!in_array($field, $allowedFields, true)) {
    ob_end_clean();
    echo json_encode(['status' => 'error', 'message' => 'Field tidak diizinkan']);
    exit;
}

// UPDATE
$sql = "UPDATE db_qc.tbl_aftersales_now SET $field=? WHERE id=?";
$stmt = sqlsrv_prepare($con_db_qc_sqlsrv, $sql, array($value, $id));
if (!$stmt) {
    ob_end_clean();
    $errors = sqlsrv_errors();
    $msg = 'Prepare gagal.';
    if ($errors) {
        $msg .= ' ' . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

if (!sqlsrv_execute($stmt)) {
    ob_end_clean();
    $errors = sqlsrv_errors();
    $msg = 'Execute gagal.';
    if ($errors) {
        $msg .= ' ' . print_r($errors, true);
    }
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}

$affected = sqlsrv_rows_affected($stmt);
sqlsrv_free_stmt($stmt);

ob_end_clean();
echo json_encode(['status' => 'ok', 'affected' => $affected]);
