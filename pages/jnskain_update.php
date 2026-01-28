<?php
include("../koneksi.php");
	$id = $_POST['id'];
	$jns_kain = strtoupper($_POST['jns_kain']);
		$sqlupdate="UPDATE TOP (1) db_qc.tbl_tq_nokk SET 
		jenis_kain=?
		WHERE id=?";
		$result = sqlsrv_query($con_db_qc_sqlsrv,$sqlupdate,[$jns_kain,$id]) or die (p(sqlsrv_errors()));
		if($result){
			echo " <script>window.location='FinalStatusTQNew';</script>";
		}else{
			echo "Update Data Gagal";
		}
?>
