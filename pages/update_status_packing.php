<?php
include "../koneksi.php";

$id     = $_GET['id'] ?? null;
$demand = $_GET['demand'] ?? null;

if (!$id) {
    echo "<script>
            window.location.href = 'SchedulePacking';
        </script>";
    exit;
}

$sql = "UPDATE db_qc.tbl_schedule_packing
    SET
        [status]    = ?,
        tgl_update  = GETDATE(),
        ket_status  = ?
    WHERE id = ?
";

$params = [
    'selesai',
    'Input LapPackingNew',
    (int)$id
];

$stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);


if ($stmt === false) {
    // DEBUG ONLY
    // die(print_r(sqlsrv_errors(), true));
    echo "<script>
            window.location.href = 'SchedulePacking';
        </script>";
    exit;
}

// redirect
if ($demand) {
    echo "<script>
            window.location.href = 'LapPackingNew-" . $demand . "';
        </script>";
} else {
    // Jika query tidak berhasil dipersiapkan, tampilkan error dan redirect ke SchedulePacking
    echo "<script>
            window.location.href = 'SchedulePacking';
        </script>";
}
exit;
