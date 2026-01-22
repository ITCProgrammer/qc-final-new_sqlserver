<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $modal_id=$_GET['id'];
	$qry=sqlsrv_query($con_db_qc_sqlsrv,"SELECT gambar FROM db_qc.tbl_gambar WHERE id=? ",[$modal_id]);
	$r=sqlsrv_fetch_array($qry,SQLSRV_FETCH_ASSOC);
	$gambar=$r['gambar'];
	$modal1=sqlsrv_query($con_db_qc_sqlsrv,"DELETE FROM db_qc.tbl_gambar WHERE id=? ",[$modal_id]);
    if ($modal1) {
		$file="dist/img/gambar/".$gambar;
		if(file_exists($file)){
			unlink($file);
		}
		echo "<script>window.location='GrafikQCF';</script>";
    } else {
        echo "<script>alert('Gagal Hapus');window.location='GrafikQCF';</script>";
    }
