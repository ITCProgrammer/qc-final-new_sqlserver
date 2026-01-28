<?php
    ini_set("error_reporting", 1);
    session_start();
    include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_conform_qc WHERE id='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='./Reportconfrom';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./Reportconfrom';</script>";
    }
