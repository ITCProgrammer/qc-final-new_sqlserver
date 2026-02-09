<?php
include '../../koneksi.php';

if (isset($_GET['nodemand'])) {
    $nodemand = $_GET['nodemand'];

    $sql = "
    SELECT TOP 1 no_test
        FROM db_qc.tbl_tq_nokk
        WHERE nodemand = ?
        ORDER BY no_test DESC
    ";
    $query = sqlsrv_query($con_db_qc_sqlsrv, $sql, [$nodemand]);

    $row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);

    if ($row) {
        echo json_encode(['no_test' => $row['no_test']]);
    } else {
        echo json_encode(['no_test' => null]);
    }
}
?>
