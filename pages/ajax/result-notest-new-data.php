<?php
include '../../koneksi.php';

header('Content-Type: application/json');

$data = [];

$sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "
    SELECT
        a.no_order,
        a.no_test,
        a.nodemand,
        a.nokk,
        a.jenis_kain,
        a.lot,
        a.no_hanger,
        a.no_item,
        a.warna
    FROM db_qc.tbl_tq_nokk a
    INNER JOIN  db_qc.tbl_tq_test b
        ON a.id = b.id_nokk 
    WHERE YEAR(a.tgl_masuk) NOT IN (2019, 2020, 2021)
      AND a.nodemand <> ''
"
);

if ($sql) {
    while ($row = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC)) {
        $data[] = [
            'no_order'   => $row['no_order'],
            'no_test'    => $row['no_test'],
            'nodemand'   => $row['nodemand'],
            'nokk'       => $row['nokk'],
            'jenis_kain' => $row['jenis_kain'],
            'lot'        => $row['lot'],
            'no_hanger'  => $row['no_hanger'],
            'no_item'    => $row['no_item'],
            'warna'      => $row['warna'],
        ];
    }
}

echo json_encode([
    'success' => true,
    'data'    => $data,
]);
