<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_lap_jahit_shading WHERE id='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='LihatDataJahitShading';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='LihatDataJahitShading';</script>";
    }
