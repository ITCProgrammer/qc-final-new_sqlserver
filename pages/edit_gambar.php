<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
if ($_POST) {
    extract($_POST);
    $id = $_POST['id'];
    $desc = strtoupper($_POST['desc']);
	  $tampil = $_POST['tampil'];
    $sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"UPDATE TOP (1) db_qc.tbl_gambar SET
				[desc]=?,
				[tampil]=?,
				[tgl_update]=CURRENT_TIMESTAMP
				WHERE [id]=? ",[$desc,$tampil,$id]);
    //echo " <script>window.location='?p=Line-News';</script>";
    echo "<script>swal({
  title: 'Data Tersimpan',
  text: 'Klik Ok untuk melanjutkan',
  type: 'success',
  }).then((result) => {
  if (result.value) {
    window.location='GrafikQCF';
  }
});</script>";
}
