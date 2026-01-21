<?php
ini_set("error_reporting", 1);
include("../koneksi.php");
if($_POST){ 
	extract($_POST);
	//tangkap data array dari form
    $urut = $_POST['no_urut'];
	$personil = $_POST['personil'];
    //foreach
    foreach ($urut as $urut_key => $urut_value) {
    $query = "UPDATE TOP (1) db_qc.tbl_schedule SET 
	[no_urut] =  ?,
	[personil]=  ?
    WHERE [id] = ? ;";
    $result = sqlsrv_query($con_db_qc_sqlsrv,$query,[$urut_value,$personil,$urut_key]);
    }
    if (!$result) {
        die ('cant update:' .sqlsrv_error());
    }else{
		echo " <script>window.location='Schedule';</script>";
	}
				
						
		}		

?>
