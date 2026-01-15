<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";

sqlsrv_query($con_db_qc_sqlsrv,"UPDATE db_qc.tbl_lap_beda_roll SET lot_legacy = '$_POST[value]' where id = '$_POST[pk]'");

echo json_encode('success');
