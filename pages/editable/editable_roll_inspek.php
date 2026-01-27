<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";
// echo '$_POST[value]';
// echo '$_POST[pk]';
$upd= sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_inspection SET jml_rol = ? where id = ? ",[$_POST['value'],$_POST['pk']]);

echo json_encode('success');
