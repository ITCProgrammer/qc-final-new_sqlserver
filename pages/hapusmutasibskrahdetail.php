<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
	$qcek=sqlsrv_query($con_db_qc_sqlsrv,"SELECT * FROM db_qc.mutasi_bs_krah_detail WHERE id='$modal_id'");
	$rcek=sqlsrv_fetch_array($qcek);
	$idm=$rcek['id_mutasi'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.mutasi_bs_krah_detail WHERE id='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='./MutasiBSDetail-$idm';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./MutasiBSDetail-$idm';</script>";
    }
