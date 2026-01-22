<?php
include '../../koneksi.php';

header('Content-Type: application/json');

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0; // Offset
$length = isset($_POST['length']) ? intval($_POST['length']) : 10; // Limit
// $searchValue = isset($_POST['search']['value']) ? mysqli_real_escape_string($con, $_POST['search']['value']) : '';
$searchValue = isset($_POST['search']['value'])
    ? str_replace("'", "''", $_POST['search']['value'])
    : '';


// Ordering
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDirection   = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$orderDirection   = strtolower($orderDirection) === 'desc' ? 'DESC' : 'ASC';


$columns = [
    1 => 'a.no_order',
    2 => 'a.no_test',
    3 => 'a.nodemand',
    4 => 'a.nokk',
    5 => 'a.jenis_kain',
    6 => 'a.lot',
    7 => 'a.no_hanger',
    8 => 'a.no_item',
    9 => 'a.warna'
];

$orderColumn = $columns[$orderColumnIndex] ?? $columns[1]; 
// $whereConditions = "DATE_FORMAT(a.tgl_masuk, '%Y') NOT IN ('2019', '2020', '2021') AND a.nodemand != ''";

$whereConditions = "
    YEAR(a.tgl_masuk) NOT IN (2019, 2020, 2021)
    AND a.nodemand <> ''
";


if (!empty($searchValue)) {
    $searchFields = [];
    foreach ($columns as $index => $colName) {
        if ($index > 0) {
            $searchFields[] = "$colName LIKE '%$searchValue%'";
        }
    }
    if (!empty($searchFields)) {
        $whereConditions .= " AND (" . implode(' OR ', $searchFields) . ")";
    }
}


// $sqlTotal = "SELECT COUNT(a.id) as total FROM tbl_tq_nokk a INNER JOIN tbl_tq_test b ON a.id = b.id_nokk WHERE DATE_FORMAT(a.tgl_masuk, '%Y') NOT IN ('2019', '2020', '2021') AND a.nodemand != ''";
$sqlTotal = "
    SELECT COUNT(a.id) AS total
    FROM db_qc.tbl_tq_nokk a
    INNER JOIN db_qc.tbl_tq_test b ON a.id = b.id_nokk
    WHERE YEAR(a.tgl_masuk) NOT IN (2019, 2020, 2021)
      AND a.nodemand <> ''
";

$stmtTotal = sqlsrv_query($con_db_qc_sqlsrv, $sqlTotal);
$rowTotal  = sqlsrv_fetch_array($stmtTotal, SQLSRV_FETCH_ASSOC);
$totalRecords = $rowTotal['total'] ?? 0;


if (!empty($searchValue)) {
    $sqlFiltered = "
        SELECT COUNT(a.id) AS total_filtered
        FROM db_qc.tbl_tq_nokk a
        INNER JOIN db_qc.tbl_tq_test b ON a.id = b.id_nokk
        WHERE $whereConditions
    ";
    $stmtFiltered = sqlsrv_query($con_db_qc_sqlsrv, $sqlFiltered);
    $rowFiltered  = sqlsrv_fetch_array($stmtFiltered, SQLSRV_FETCH_ASSOC);
    $totalFiltered = $rowFiltered['total_filtered'] ?? 0;
} else {
    $totalFiltered = $totalRecords;
}


$sql = "
    SELECT a.*
    FROM db_qc.tbl_tq_nokk a
    INNER JOIN db_qc.tbl_tq_test b ON a.id = b.id_nokk
    WHERE $whereConditions
    ORDER BY $orderColumn $orderDirection
    OFFSET $start ROWS FETCH NEXT $length ROWS ONLY
";

$stmtData = sqlsrv_query($con_db_qc_sqlsrv, $sql);
$data = [];


if ($stmtData) {
    while ($row = sqlsrv_fetch_array($stmtData, SQLSRV_FETCH_ASSOC)) {
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
    'draw'            => $draw,             
    'recordsTotal'    => $totalRecords,    
    'recordsFiltered' => $totalFiltered,   
    'data'            => $data,            
]);
