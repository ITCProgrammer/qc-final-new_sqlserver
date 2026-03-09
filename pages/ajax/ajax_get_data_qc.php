<?php
include "../../koneksi.php";
header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "SELECT id, nodemand, no_po, no_item, warna, 
            [group] as group_report, 
            hue as hue_report, 
            list_kanan as demand_kanan,
            grade_kiri,
            grade_kanan
            FROM db_qc.tbl_qcf WHERE id = ?";
            
    $params = array($id);
    $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);
    if ($stmt === false) {
        echo json_encode(["error" => "Query gagal", "details" => sqlsrv_errors()]);
        exit;
    }

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        echo json_encode($row);
        exit;
    }

    echo json_encode(["error" => "Data tidak ditemukan"]);
    exit;
}
?>