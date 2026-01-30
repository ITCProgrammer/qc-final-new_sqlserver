<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
	$id = $_POST['id'];
	$no_item = str_replace("'","''",$_POST['no_item']);
    $hangtag = str_replace("'","''",$_POST['hangtag']);
		$sqlupdate="UPDATE db_qc.tbl_master_hangtag SET 
			no_item=?,
			hangtag=?
		WHERE id=?";
		$params = array($no_item, $hangtag, $id);
		$result = sqlsrv_query($con_db_qc_sqlsrv,$sqlupdate,$params) or die (sqlsrv_errors());
		if($result){
			echo " <script>window.location='MasterHangtag';</script>";
		}else{
			echo "Update Data Gagal";
		}
?>
