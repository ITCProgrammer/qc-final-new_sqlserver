<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";

$upd=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_tq_randomtest SET rsp_grdl1 = '$_POST[value]' where id = '$_POST[pk]'");

echo json_encode('success');
