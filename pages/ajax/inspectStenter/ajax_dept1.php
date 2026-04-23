<?php
include "../../../koneksi.php";

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($searchTerm)) {
    $query = "SELECT id, nama 
              FROM db_qc.filter_dept 
              WHERE nama LIKE ? 
              ORDER BY nama ASC";
    $params = array("%$searchTerm%");
} else {
    $query = "SELECT TOP 20 id, nama 
              FROM db_qc.filter_dept 
              ORDER BY nama ASC";
    $params = array();
}

$result = sqlsrv_query($con_db_qc_sqlsrv, $query, $params);

if ($result === false) {
    die(json_encode(array("error" => sqlsrv_errors())));
}

$data = [];
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $data[] = [
        'id'   => $row['id'],
        'text' => $row['nama']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
?>