<?php
ini_set("error_reporting", 1);
include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal1=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_schedule_krah WHERE id='$modal_id' ");
    if ($modal1) {
        echo "<script>window.location='ScheduleKrah';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='ScheduleKrah';</script>";
    }
