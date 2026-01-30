<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_master_hangtag WHERE id='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='./MasterHangtag';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./MasterHangtag';</script>";
    }
