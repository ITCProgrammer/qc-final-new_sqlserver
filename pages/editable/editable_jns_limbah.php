<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";

sqlsrv_query($con_db_qc_sqlsrv,"UPDATE db_qc.mutasi_bs_krah SET jns_limbah = '$_POST[value]' where id = '$_POST[pk]'");

echo json_encode('success');
