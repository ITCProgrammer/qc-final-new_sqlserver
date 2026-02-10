<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";

$up=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_detail_retur_now SET status3 = ? where id = ? ",[$_POST['value'],$_POST['pk']]);


echo json_encode('success');
