<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $no_item = str_replace("'","''",$_POST['no_item']);
    $hangtag = str_replace("'","''",$_POST['hangtag']);
		$sqlinsert="INSERT INTO db_qc.tbl_master_hangtag (
			no_item,
			hangtag,
			tgl_buat
		) 
		VALUES (
			?, ?, GETDATE())";
		$params = array($no_item, $hangtag);
		$result = sqlsrv_query($con_db_qc_sqlsrv,$sqlinsert,$params) or die (sqlsrv_errors());
		if($result){
			echo " <script>window.location='MasterHangtag';</script>";
		}else{
			echo "Insert Data Gagal";
		}
?>
