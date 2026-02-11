<?php
session_start();
include("../koneksi.php");


function get_client_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_X_FORWARDED'])) return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR'])) return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED'])) return $_SERVER['HTTP_FORWARDED'];
    if (!empty($_SERVER['REMOTE_ADDR'])) return $_SERVER['REMOTE_ADDR'];
    return 'UNKNOWN';
}

$ip_num = get_client_ip();

$id         = $_POST['id'] ?? null;
$sts        = $_POST['sts'] ?? null;
$no_counter = $_POST['no_counter'] ?? null;
$penerima   = $_POST['nama_penerima'] ?? null;
$diterima   = $_POST['diterima_oleh'] ?? null;

$usrid = $_SESSION['usrid'] ?? null;

$success = true;
$errMsg = '';

if (!$con_db_laborat_sqlsrv) {
    $success = false;
    $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
    $errMsg = $errors ? $errors[0]['message'] : 'Koneksi DB laborat gagal.';
}

if ($success && (!$id || !$sts || !$no_counter)) {
    $success = false;
    $errMsg = 'Data tidak lengkap. Mohon ulangi input.';
}

if ($success && $sts === 'Sudah Terima Kain') {
    if (!$penerima || !$diterima) {
        $success = false;
        $errMsg = 'Pengirim dan Penerima wajib diisi.';
    }
}

// mulai transaksi
if ($success && !sqlsrv_begin_transaction($con_db_laborat_sqlsrv)) {
    $success = false;
    $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
    $errMsg = $errors ? $errors[0]['message'] : 'Gagal memulai transaksi.';
}

$sqlupdate = "
        UPDATE TOP (1) db_laborat.tbl_test_qc
        SET
            sts_qc = ?,
            sts_laborat = 'In Progress',
            nama_penerima = ?,
            diterima_oleh = ?,
            tgl_terimakain = GETDATE(),
            terimakain_by = ?
        WHERE id = ?;
    ";

    $params_update = [$sts, $penerima, $diterima, $usrid, $id];
    $stmt_update = sqlsrv_query($con_db_laborat_sqlsrv, $sqlupdate, $params_update);

if (!$stmt_update) {
    $success = false;
    $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
    $errMsg = $errors ? $errors[0]['message'] : 'Gagal update data.';
}

if ($success) {
    $sql_log = "
        INSERT INTO db_laborat.log_qc_test
            (no_counter, status, info, do_by, do_at, ip_address)
        VALUES
            (?, 'In Progress', 'Kain sudah diterima QC', ?, GETDATE(), ?);
    ";

    $params_log = [$no_counter, $usrid, $ip_num];
    $stmt_log = sqlsrv_query($con_db_laborat_sqlsrv, $sql_log, $params_log);

    if ($stmt_log === false) {
        $success = false;
        $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
        $errMsg = $errors ? $errors[0]['message'] : 'Gagal simpan log.';
    }
}

if ($success) {
    sqlsrv_commit($con_db_laborat_sqlsrv);
    echo "<script>window.location='KainInLab';</script>";
} else {
    sqlsrv_rollback($con_db_laborat_sqlsrv);

    echo "<script>swal({
        title: 'Gagal terima kain!!',
        text: " . json_encode($errMsg) . ",
        type: 'error',
    }).then((result) => {
        if (result.value) {
            window.history.back();
        }
    });</script>";
}
