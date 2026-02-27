<?PHP
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";

$value = isset($_POST['value']) ? $_POST['value'] : null;
$pk = isset($_POST['pk']) ? $_POST['pk'] : null;

$sql = "UPDATE db_qc.tbl_lap_inspeksi SET review_qcf = ? WHERE id = ?";
$params = array($value, $pk);
$stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);

if ($stmt === false) {
	http_response_code(500);
	echo json_encode('failed');
	exit;
}

echo json_encode('success');
