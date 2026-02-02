<?php
session_start();

$id = $_POST['id'];
$no_counter = $_POST['no_counter'];

function get_client_ip()
{
	$ipaddress = '';
	if (isset($_SERVER['HTTP_CLIENT_IP']))
		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_X_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	else if (isset($_SERVER['HTTP_FORWARDED']))
		$ipaddress = $_SERVER['HTTP_FORWARDED'];
	else if (isset($_SERVER['REMOTE_ADDR']))
		$ipaddress = $_SERVER['REMOTE_ADDR'];
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}

$ip_num = get_client_ip();

$approved1 = str_replace("'", "''", $_POST['approved1']);

try {
	if (sqlsrv_begin_transaction($con_db_laborat_sqlsrv) === false) {
		throw new Exception("Begin transaction failed.");
	}

	$sql_update = "UPDATE db_laborat.tbl_test_qc SET
						sts_qc = ?,
						sts_laborat = ?,
						tgl_approve_qc = GETDATE(),
						approved_qc1 = ?
					WHERE id = ?";

	$params_update = array(
		'Hasil Test Parsial',
		'Waiting Approval Parsial',
		$approved1,
		$id
	);

	$result_update = sqlsrv_query($con_db_laborat_sqlsrv, $sql_update, $params_update);
	if ($result_update === false) {
		throw new Exception("Query UPDATE failed: " . print_r(sqlsrv_errors(), true));
	}

	$sql_insert_log = "INSERT INTO db_laborat.log_qc_test
						(no_counter, status, info, do_by, do_at, ip_address)
					   VALUES
						(?, ?, ?, ?, GETDATE(), ?)";

	$params_log = array(
		$no_counter,
		'Waiting Approval Parsial',
		'QC sudah approve parsial',
		$_SESSION['usrid'],
		$ip_num
	);

	$result_insert_log = sqlsrv_query($con_db_laborat_sqlsrv, $sql_insert_log, $params_log);
	if ($result_insert_log === false) {
		throw new Exception("Query INSERT log failed: " . print_r(sqlsrv_errors(), true));
	}

	sqlsrv_commit($con_db_laborat_sqlsrv);

	echo "<script>window.location='KainInLab';</script>";
} catch (Exception $e) {
	sqlsrv_rollback($con_db_laborat_sqlsrv);

	echo "<script>swal({
	title: 'Gagal approve!!',
	text: 'Terjadi kesalahan,coba lagi nanti.',
	type: 'error',
	}).then((result) => {
	if (result.value) {
		window.history.back();
	}
	});</script>";
}
