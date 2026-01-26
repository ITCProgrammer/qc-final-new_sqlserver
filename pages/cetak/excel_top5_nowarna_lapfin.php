<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Top5-NoWarna-Lap-Fin-".substr($_GET['awal'],0,10).".xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda
?>
<?php 
include "../../koneksi.php";
//--
$Awal=$_GET['awal'];
$Akhir=$_GET['akhir'];
?>
<body>
<?php 
      $sqlball=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
      count(a.nokk) as jml_kk_all 
      from 
      db_qc.tbl_lap_inspeksi a
      where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
      AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir'");
      $rball=sqlsrv_fetch_array($sqlball);
      ?>
<strong>Periode: <?php echo $Awal; ?> s/d <?php echo $Akhir; ?></strong><br>
<table width="100%" border="1">
    <tr>
        <th bgcolor="#12C9F0"><div align="center">No</div></th>
        <th bgcolor="#12C9F0"><div align="center">No Warna</div></th>
        <th bgcolor="#12C9F0"><div align="center">Warna</div></th>
        <th bgcolor="#12C9F0"><div align="center">A</div></th>
        <th bgcolor="#12C9F0"><div align="center">B</div></th>
        <th bgcolor="#12C9F0"><div align="center">C</div></th>
        <th bgcolor="#12C9F0"><div align="center">D</div></th>
        <th bgcolor="#12C9F0"><div align="center">NULL</div></th>
        <th bgcolor="#12C9F0"><div align="center">%</div></th>
    </tr>
    <?php 
          $no=1;
          $sqlw=sqlsrv_query($con_db_qc_sqlsrv,"SELECT TOP 5
          no_warna,
          warna,
          count(a.nokk) as jml_kk
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          group by 
          no_warna,
          warna
          order by jml_kk desc");
          while($rw=sqlsrv_fetch_array($sqlw)){
          //GROUP A
          $sqlwa=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          no_warna,
          warna,
          count(a.nokk) as jml_kk_a
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'A' and a.no_warna ='$rw[no_warna]' and a.warna ='$rw[warna]'
          group by 
          no_warna,
          warna");
          $rwa=sqlsrv_fetch_array($sqlwa);
          //GROUP B
          $sqlwb=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          no_warna,
          warna,
          count(a.nokk) as jml_kk_b
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'B' and a.no_warna ='$rw[no_warna]' and a.warna ='$rw[warna]'
          group by 
          no_warna,
          warna");
          $rwb=sqlsrv_fetch_array($sqlwb);
          //GROUP C
          $sqlwc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          no_warna,
          warna,
          count(a.nokk) as jml_kk_c
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'C' and a.no_warna ='$rw[no_warna]' and a.warna ='$rw[warna]'
          group by 
          no_warna,
          warna");
          $rwc=sqlsrv_fetch_array($sqlwc);
          //GROUP D
          $sqlwd=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          no_warna,
          warna,
          count(a.nokk) as jml_kk_d
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'D' and a.no_warna ='$rw[no_warna]' and a.warna ='$rw[warna]'
          group by 
          no_warna,
          warna");
          $rwd=sqlsrv_fetch_array($sqlwd);
          //NULL
          $sqlwn=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          no_warna,
          warna,
          count(a.nokk) as jml_kk_null
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and (a.grouping = '' or a.grouping is null ) and a.no_warna ='$rw[no_warna]' and a.warna ='$rw[warna]'
          group by 
          no_warna,
          warna");
          $rwn=sqlsrv_fetch_array($sqlwn);
          ?>
          <tr valign="top">
            <td align="center"><?php echo $no;?></td>
            <td align="center"><?php echo $rw['no_warna'];?></td>
            <td align="center"><?php echo $rw['warna'];?></td>
            <td align="center"><?php echo $rwa['jml_kk_a'];?></td>
            <td align="center"><?php echo $rwb['jml_kk_b'];?></td>
            <td align="center"><?php echo $rwc['jml_kk_c'];?></td>
            <td align="center"><?php echo $rwd['jml_kk_d'];?></td>
            <td align="center"><?php echo $rwn['jml_kk_null'];?></td>
            <td align="center"><?php echo number_format(($rw['jml_kk']/$rball['jml_kk_all'])*100,2)." %";?></td>
          </tr>
          <?php 
          $no++;}
          ?>
</table>
</body>