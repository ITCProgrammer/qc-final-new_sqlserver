<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";
$value = $_POST['value'];
$id = $_POST['pk'];


if ($value === '' || $value === null) {
    $query = "UPDATE db_qc.tbl_lap_beda_roll SET status = NULL WHERE id = '$id'";
} else {
    $query = "UPDATE db_qc.tbl_lap_beda_roll SET status = '$value' WHERE id = '$id'";
}

sqlsrv_query($con_db_qc_sqlsrv, $query);

echo json_encode('success');
