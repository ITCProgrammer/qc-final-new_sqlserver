<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");

if ($_POST) {

    $id          = $_POST['id'];
    $masalah     = $_POST['masalah'];
    $sts_nodelay = (isset($_POST['sts_nodelay']) && $_POST['sts_nodelay'] == "1") ? "1" : "0";

    $sql = " UPDATE db_qc.tbl_qcf
        SET
            rol         = ?,
            netto       = ?,
            panjang     = ?,
            satuan      = ?,
            tgl_fin     = TRY_CONVERT(date, NULLIF(?, '')),
            tgl_ins     = TRY_CONVERT(date, NULLIF(?, '')),
            tgl_pack    = TRY_CONVERT(date, NULLIF(?, '')),
            tgl_masuk   = TRY_CONVERT(date, NULLIF(?, '')),
            ket         = ?,
            sts_nodelay = ?,
            masalah     = ?
        WHERE id = ?
    ";

    $params = [
        $_POST['rol'] ?? null,
        $_POST['netto'] ?? null,
        $_POST['panjang'] ?? null,
        $_POST['satuan'] ?? null,
        trim($_POST['tgl_fin'] ?? ''),
        trim($_POST['tgl_inspek'] ?? ''),
        trim($_POST['tgl_packing'] ?? ''),
        trim($_POST['tgl_masuk'] ?? ''),
        $_POST['ket'] ?? null,
        $sts_nodelay,
        $masalah,
        $id
    ];

    $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);

    if ($stmt === false) {
        echo "<pre>";
        print_r(sqlsrv_errors());
        echo "</pre>";
        exit;
    }

    echo "<script>swal({
  title: 'Data Telah diUbah',
  text: 'Klik Ok untuk melanjutkan',
  type: 'success',
  }).then((result) => {
  if (result.value) {
    window.location='./RekapData';
  }
});</script>";
}
?>
