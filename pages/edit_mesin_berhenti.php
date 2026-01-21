<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
if($_POST){ 
	extract($_POST);
	$id = $_POST['id'];
	$catatan = $_POST['catatan'];
	$lfin = $_POST['lembap_fin'];
	$lqcf = $_POST['lembap_qcf'];
	$qty = $_POST['qty'];
	$qtyloss = $_POST['qty_loss'];
	$noteloss = $_POST['note_loss'];
	$yard = $_POST['yard'];
	$jml = $_POST['jml_rol'];
	$shift = $_POST['shift'];
	$gshift = $_POST['g_shift'];
	$istirahat = $_POST['istirahat'];
	$shading = $_POST['shading'];
	$demand_lgcy = $_POST['demand_lgcy'];
	$t_jawab = $_POST['t_jawab'];
	$t_jawab_buyer = $_POST['t_jawab_buyer'];
	$operator = $_POST['operator'];
	
	$Qrycek=sqlsrv_query($con_db_qc_sqlsrv,"SELECT TOP 1 * FROM db_qc.tbl_schedule WHERE id=? ",[$id]);
	$rCek=sqlsrv_fetch_array($Qrycek,SQLSRV_FETCH_ASSOC);
	$sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_inspection SET 
				[catatan]=?,
				[qty]=?,
				[yard]=?,
				[jml_rol]=?,
				[status]='selesai'
				WHERE [id_schedule]=? ",[$catatan,$qty,$yard,$jml,$id]);
	if($sqlupdate){
				$sqlupdate1=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_schedule SET 
				[status]='selesai',
				[tgl_stop]=CURRENT_TIMESTAMP,
				[lembap_fin]=?,
				[lembap_qcf]=?,
				[istirahat]=?,
				[shading]=?,
				[shift]=?,
				[demand_lgcy]=?,
				[t_jawab]=?,
				[t_jawab_buyer]=?,
				[g_shift]=?,
				[qty_loss]=?,
				[note_loss]=?,
				[operator]=?
				WHERE [id]=? ",[$lfin,$lqcf,$istirahat,$shading,$shift,$demand_lgcy,$t_jawab,$t_jawab_buyer,$gshift,floatval($qtyloss),$noteloss,$operator,$id]);
				$sqlUrut=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE db_qc.tbl_schedule 
		  		SET no_urut = no_urut - 1 
				WHERE no_mesin = ?
		  		AND [status] = 'antri mesin' AND no_urut <> '1' ",[$rCek['no_mesin']]);	
				/*$sqlupdate1=sqlsrv_query("UPDATE tbl_montemp SET 
				tgl_target= ADDDATE(tgl_buat, INTERVAL '$target1' HOUR_MINUTE) 
				WHERE id_schedule='$id' LIMIT 1");*/
				echo " <script>window.location='Schedule';</script>";
			}			
		}
		

?>
