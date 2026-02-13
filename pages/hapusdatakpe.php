<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");

$modal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "DELETE FROM db_qc.tbl_aftersales_now WHERE id = ?";
$params = [$modal_id];

$modal = sqlsrv_query($con_db_qc_sqlsrv, $sql, $params);

if ($modal === false) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    echo "<script>alert('Gagal Hapus');window.location='./LapKPE';</script>";
    exit;
}

echo "<script>window.location='./LapKPE';</script>";
?>
