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
	$personil = $_POST['personil'];
	$Qrycek=sqlsrv_query($con_db_qc_sqlsrv,"SELECT TOP (1) * FROM db_qc.tbl_schedule WHERE id=? ",[$id]);
	$rCek=sqlsrv_fetch_array($Qrycek,SQLSRV_FETCH_ASSOC);

	$target1=floatval($rCek['target']);
	$target_extract=explode('.',strval($target1));
	if(count($target_extract)==2){
		$addDate="DATEADD(hour,".$target_extract[0].",DATEADD(Minute,".$target_extract[1].",CURRENT_TIMESTAMP)),";
	}else{
		$addDate="DATEADD(hour,".$target_extract[0].",CURRENT_TIMESTAMP),";
	}

	$sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"INSERT db_qc.tbl_inspection ([id_schedule],[nodemand],[nokk],[status],[tgl_target],[personil],[tgl_buat],[tgl_update]) 
				VALUES (?,?,?,'sedang jalan', $addDate ?,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP) ",
				[$rCek['id'],$rCek['nodemand'],$rCek['nokk'],$personil]);
	$sqlupdate1=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_schedule SET 
				[status]='sedang jalan',
				[tgl_mulai]=CURRENT_TIMESTAMP
				WHERE [id]=? ",[$id]);
				/*$sqlupdate1=sqlsrv_query("UPDATE tbl_montemp SET 
				tgl_target= ADDDATE(tgl_buat, INTERVAL '$target1' HOUR_MINUTE) 
				WHERE id_schedule='$id' LIMIT 1");*/
	echo " <script>window.location='Schedule';</script>";
}
		

?>
