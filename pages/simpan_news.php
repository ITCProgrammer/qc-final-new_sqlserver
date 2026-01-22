<?php
ini_set("error_reporting", 1);
include("../koneksi.php");
if ($_POST) {
    extract($_POST);
    $pesan = strtoupper($_POST['line_news']);
        $sqlupdate=sqlsrv_query($con_db_qc_sqlsrv,"INSERT INTO db_qc.tbl_news_line (gedung,news_line,tgl_update) VALUES ('LT 1',?,CURRENT_TIMESTAMP)",[$pesan]);
        echo " <script>window.location='LineNews';</script>";
    }