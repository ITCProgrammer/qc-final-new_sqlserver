<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE TOP (1) FROM db_qc.tbl_tq_randomtest WHERE id=? ",[$modal_id]);
    if ($modal) {
        echo "<script>window.location='./CetakRandom';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./CetakRandom';</script>";
    }
