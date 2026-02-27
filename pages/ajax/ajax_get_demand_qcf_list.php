<?php
include "../../koneksi.php";
header('Content-Type: application/json');

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];

if (!empty($searchTerm)) {
    $sql = "SELECT DISTINCT TOP 20 nodemand FROM db_qc.tbl_qcf 
            WHERE nodemand LIKE ? AND nodemand IS NOT NULL 
            ORDER BY nodemand";
    
    $params = array("%$searchTerm%");
    $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);

    if ($stmt === false) {
        error_log("SQL Error in ajax_get_demand_qcf_list: " . print_r(sqlsrv_errors(), true));
        echo json_encode([]);
        exit;
    }

    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $results[] = [
            'id' => trim($row['nodemand']), 
            'text' => trim($row['nodemand'])
        ];
    }
}

echo json_encode($results);
exit;
?>