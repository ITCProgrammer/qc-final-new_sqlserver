<?php
header('Content-Type: application/json');
error_reporting(1);
include "../../koneksi.php";

if(isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $group = isset($_POST['group_report']) ? trim($_POST['group_report']) : '';
    $hue = isset($_POST['hue_report']) ? trim($_POST['hue_report']) : '';
    
    // demand_kanan sudah dikirim sebagai string (comma-separated), bukan array
    $demand_kanan = isset($_POST['demand_kanan']) ? trim($_POST['demand_kanan']) : '';

    // UPDATE dengan escape column names (group adalah reserved keyword)
    $sql = "UPDATE db_qc.tbl_qcf SET 
            [group] = ?, 
            hue = ?, 
            list_kanan = ? 
            WHERE id = ?";
            
    $params = array($group, $hue, $demand_kanan, $id);
    $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);

    if($stmt === false) {
        $errors = sqlsrv_errors();
        echo json_encode(array(
            'status' => 'error', 
            'message' => 'SQL Error: ' . json_encode($errors)
        ));
    } else {
        $rows_affected = sqlsrv_rows_affected($stmt);
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Data berhasil diperbarui!',
            'rows_affected' => $rows_affected
        ));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'ID tidak dikirim'));
}
?>