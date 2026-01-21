<?php
ini_set("error_reporting", 1);
include("../koneksi.php");
if($_POST){ 
	extract($_POST);
	$id = $_POST['id'];
	$id_schedule = $_POST['id_schedule'];
	$no_order = $_POST['no_order'];
	$jeniskain = $_POST['jenis_kain'];
	$catatan = $_POST['catatan'];
	$warna = $_POST['warna'];
	$lot = $_POST['lot'];
	$sts = $_POST['sts_pro'];
	$kd_sts = $_POST['kd_sts'];
	$gerobak1 = $_POST['gerobak1'];
	$gerobak2 = $_POST['gerobak2'];
	$gerobak3 = $_POST['gerobak3'];
	$gerobak4 = $_POST['gerobak4'];
	$gerobak5 = $_POST['gerobak5'];
	$gerobak6 = $_POST['gerobak6'];
	$params=[];
	$update="";
	if($gerobak6!=""){ 
		$update.=" ,[no_gerobak1]=?,[no_gerobak2]=?,[no_gerobak3]=?,[no_gerobak4]=?,[no_gerobak5]=?,[no_gerobak6]=?,[tgl_out6]= CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[no_gerobak2],[no_gerobak3],[no_gerobak4],[no_gerobak5],[no_gerobak6],[tgl_out6] ";
		$insParam=" ,?,?,?,?,?,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
		$params[]=$gerobak2;
		$params[]=$gerobak3;
		$params[]=$gerobak4;
		$params[]=$gerobak5;
		$params[]=$gerobak6;
	}
	else if($gerobak5!=""){ 
		$update.=" ,[no_gerobak1]=?,[no_gerobak2]=?,[no_gerobak3]=?,[no_gerobak4]=?,[no_gerobak5]=?,[tgl_out5]=CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[no_gerobak2],[no_gerobak3],[no_gerobak4],[no_gerobak5],[tgl_out5] ";
		$insParam=" ,?,?,?,?,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
		$params[]=$gerobak2;
		$params[]=$gerobak3;
		$params[]=$gerobak4;
		$params[]=$gerobak5;
	}
	else if($gerobak4!=""){ 
		$update.=" ,[no_gerobak1]=?,[no_gerobak2]=?,[no_gerobak3]=?,[no_gerobak4]=?,[tgl_out4]=CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[no_gerobak2],[no_gerobak3],[no_gerobak4],[tgl_out4] ";
		$insParam=" ,?,?,?,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
		$params[]=$gerobak2;
		$params[]=$gerobak3;
		$params[]=$gerobak4;
	}
	else if($gerobak3!=""){ 
		$update.=" ,[no_gerobak1]=?,[no_gerobak2]=?,[no_gerobak3]=?,[tgl_out3]=CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[no_gerobak2],[no_gerobak3],[tgl_out3] ";
		$insParam=" ,?,?,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
		$params[]=$gerobak2;
		$params[]=$gerobak3;
	}
	else if($gerobak2!=""){ 
		$update.=" ,[no_gerobak1]=?,[no_gerobak2]=?,[tgl_out2]=CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[no_gerobak2],[tgl_out2] ";
		$insParam=" ,?,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
		$params[]=$gerobak2;
	}
	else if($gerobak1!=""){ 
		$update.=" ,[no_gerobak1]=?,[tgl_out1]=CURRENT_TIMESTAMP ";
		$insKolom=" ,[no_gerobak1],[tgl_out1] ";
		$insParam=" ,?,CURRENT_TIMESTAMP ";
		$params[]=$gerobak1;
	}	
	if($id_schedule==""){
		$sqlInsert="INSERT INTO db_qc.tbl_gerobak ([id_schedule],[no_order],[jenis_kain],[warna],[lot],[kd_status],[catatan],[status_produk] ".$insKolom.")
					VALUES (?,?,?,?,?,?,?,? ".$insParam.") ";
		$paramInsert=[$id,$no_order,$jeniskain,$warna,$lot,$kd_sts,$catatan,$sts];
		$ins=sqlsrv_query($con_db_qc_sqlsrv,$sqlInsert,array_merge($paramInsert,$params));
	}else{
		$sqlUpdate="UPDATE db_qc.tbl_gerobak SET		
				[catatan]=?,
				[status_produk]=?
				$update
				WHERE id=?
				";	
		$params[]=$id_schedule;	
		$paramUpdate=[$catatan,$sts];
		$upd=sqlsrv_query($con_db_qc_sqlsrv,$sqlUpdate,array_merge($paramUpdate,$params));	
	}
				echo " <script>window.location='Schedule';</script>";
				
		}
		

?>
