<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";
// echo 'test2';
$upd= sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_schedule SET bruto = ? where id = ? ",[$_POST['value'],$_POST['pk']]);

echo json_encode('success');
