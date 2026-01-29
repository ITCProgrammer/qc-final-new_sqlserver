<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
    $no_item = $_POST['no_item'];
    $material_name = $_POST['material_name'];
    $fiber_content = $_POST['fiber_content'];
    $user = $_SESSION['usrid'];
	$sqlinsert="INSERT INTO db_qc.master_matrialname (item, matrial_name, fiber_content, creation, last_update, creation_user, last_update_user)
	VALUES (?, ?, ?, GETDATE(), GETDATE(), ?, ?)";
	$result = sqlsrv_query($con_db_qc_sqlsrv, $sqlinsert, array($no_item, $material_name, $fiber_content, $user, $user));
	if($result){
		echo " <script>window.location='MasterLLL';</script>";
	}else{
		$errors = sqlsrv_errors();
		echo "Insert Data Gagal : ".(is_array($errors) ? $errors[0]['message'] : "Unknown error");
	}
?>
