<?php
include "../../../koneksi.php";

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';

if (!empty($searchTerm)) {
    $query = "SELECT
            *
        FROM
            db_qc.tbl_remarks_stenter
        WHERE
            status = '1'
            AND remarks LIKE ?
        ORDER BY
            [remarks] ASC";
    $params = array("%$searchTerm%");
} else {
    $query = "SELECT TOP 20 id, remarks 
              FROM db_qc.tbl_remarks_stenter 
              WHERE
              status = '1'
              ORDER BY remarks ASC";
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
        'text' => $row['remarks']
    ];
}
    
header('Content-Type: application/json');
echo json_encode($data);
?>