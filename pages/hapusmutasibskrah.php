<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.mutasi_bs_krah WHERE id='$modal_id' ");
	$modal1=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.mutasi_bs_krah_detail WHERE id_mutasi='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='./MutasiBS';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./MutasiBS';</script>";
    }
