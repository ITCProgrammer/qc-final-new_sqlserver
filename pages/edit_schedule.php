<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
function cekDesimal($angka){
	$bulat=round($angka);
	if($bulat>$angka){
		$jam=$bulat-1;
		$waktu=$jam.":30";
	}else{
		$jam=$bulat;
		$waktu=$jam.":00";
	}
	return $waktu;
}
if($_POST){ 
	extract($_POST);
	$id = $_POST['id'];
	$urut = $_POST['no_urut'];
	$ketkain = $_POST['ket_kain'];
	$ket = $_POST['ket'];
	$personil = $_POST['personil'];
	$mesin = $_POST['no_mesin'];
	$mcfrom = $_POST['mc_from'];
	$target = $_POST['target'];
	$catatan = $_POST['catatan'];
	$target1=cekDesimal($target);
	$status = $_POST['status'];
	if($status!=""){ $sts=", [status]='$status' ";}else{ $sts="";}
	$Qrycek=sqlsrv_query($con_db_qc_sqlsrv,"SELECT TOP 1 * FROM db_qc.tbl_mesin WHERE no_mesin=? ",[$mesin]);
	$rCek=sqlsrv_fetch_array($Qrycek,SQLSRV_FETCH_ASSOC);
	$kapasitas=$rCek['kapasitas'];
				$sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_schedule SET 
				[no_mesin]=?,
				[mc_from]=?,
				[target]=?,
				[no_urut]=?,
				[proses]=?,
				[no_sch]=?,
				[ket_kain]=?,
				[ket_status]=?,
				[catatan]=?,
				[personil]=?
				$sts
				WHERE [id]=? " , [$mesin,$mcfrom,$target,$urut,$_POST['proses'],$urut,$ketkain,$ket,$catatan,$personil,$id]);
				echo " <script>window.location='Schedule';</script>";
				
		}
		

?>
