<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
	$qry=sqlsrv_query($con_db_qc_sqlsrv,"SELECT id_qcf FROM db_qc.tbl_qcf_detail WHERE id='$modal_id'");
	$r=sqlsrv_fetch_array($qry);
	$id=$r['id_qcf'];
    $modal=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_qcf_detail WHERE id='$modal_id' ");
    if ($modal) {
        echo "<script>window.location='./DetailDataKJ-$id';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='./DetailDataKJ-$id';</script>";
    }
