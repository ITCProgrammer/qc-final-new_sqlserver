<?php
ini_set("error_reporting", 1);
include("../koneksi.php");
if ($_POST) {
    extract($_POST);        
	   		$nama = strtoupper($_POST['nama']);
			$file = $_FILES['file']['name'];
		   // ambil data file
			$namaFile = $_FILES['file']['name'];
	        $namaSementara = $_FILES['file']['tmp_name'];

			// tentukan lokasi file akan dipindahkan
			$dirUpload = "dist/img/gambar/";

			// pindahkan file
			$terupload = move_uploaded_file($namaSementara, $dirUpload.$namaFile);
		   if ($terupload) { 
			$sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"INSERT INTO db_qc.tbl_gambar ([gambar],[desc],[tgl_update])
				VALUES(?,?,CURRENT_TIMESTAMP) ",[$file,$nama] );   
			echo " <script>window.location='GrafikQCF';</script>";   
		   } else {
    			echo "Upload Gagal!".$file;
		   }
		   
	   
        
    }