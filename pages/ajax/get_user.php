<?php
include '../../koneksi.php';

if (isset($_GET['nama_bow'])) {
    $nama_bow = $_GET['nama_bow'];
										
    $query = "SELECT u.nama 
              FROM db_qc.user_login u 
              WHERE u.id = ?";
    
    $stmt = sqlsrv_query($con_db_qc_sqlsrv, $query, [$nama_bow]);

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        echo json_encode(['success' => true, 'nama_user' => $row['nama']]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
}
?>
