<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Top5-Buyer-Lap-Fin-".substr($_GET['awal'],0,10).".xls");//ganti nama sesuai keperluan
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
        <th bgcolor="#12C9F0"><div align="center">Buyer</div></th>
        <th bgcolor="#12C9F0"><div align="center">A</div></th>
        <th bgcolor="#12C9F0"><div align="center">B</div></th>
        <th bgcolor="#12C9F0"><div align="center">C</div></th>
        <th bgcolor="#12C9F0"><div align="center">D</div></th>
        <th bgcolor="#12C9F0"><div align="center">NULL</div></th>
        <th bgcolor="#12C9F0"><div align="center">%</div></th>
    </tr>
	<?php 
          $no=1;
          $sqlby=sqlsrv_query($con_db_qc_sqlsrv,"SELECT TOP 5 
            CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END AS buyer,
            count(a.nokk) as jml_kk
            from 
            db_qc.tbl_lap_inspeksi a
            where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
            AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
            group by 
            CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END
            order by jml_kk desc");
          while($rby=sqlsrv_fetch_array($sqlby)){
          //GROUP A
          $sqlga=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          a.grouping,
          count(a.nokk) as jml_kk_a
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'A' and CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END ='$rby[buyer]'
            GROUP BY a.grouping");
          $rga=sqlsrv_fetch_array($sqlga);
          //GROUP B
          $sqlgb=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          a.grouping,
          count(a.nokk) as jml_kk_b
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'B' and CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END ='$rby[buyer]'
            GROUP BY a.grouping");
          $rgb=sqlsrv_fetch_array($sqlgb);
          //GROUP C
          $sqlgc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          a.grouping,
          count(a.nokk) as jml_kk_c
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'C' and CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END ='$rby[buyer]'
            GROUP BY a.grouping");
          $rgc=sqlsrv_fetch_array($sqlgc);
          //GROUP D
          $sqlgd=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          a.grouping,
          count(a.nokk) as jml_kk_d
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and a.grouping = 'D' and CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END ='$rby[buyer]'
            GROUP BY a.grouping");
          $rgd=sqlsrv_fetch_array($sqlgd);
          //NULL
          $sqlgn=sqlsrv_query($con_db_qc_sqlsrv,"SELECT
          a.grouping,
          count(a.nokk) as jml_kk_null
          from 
          db_qc.tbl_lap_inspeksi a
          where (a.proses !='Oven' or a.proses !='Fin 1X') and a.dept ='QCF'
          AND TRY_CAST(tgl_update AS DATE) BETWEEN '$Awal' AND '$Akhir' 
          and (a.grouping = '' or a.grouping is null ) and CASE
                WHEN a.pelanggan LIKE '%/%'
                THEN RIGHT(a.pelanggan, CHARINDEX('/', REVERSE(a.pelanggan)) - 1)
                ELSE a.pelanggan
            END ='$rby[buyer]'
            GROUP BY a.grouping");
          $rgn=sqlsrv_fetch_array($sqlgn);
          ?>
        <tr valign="top">
            <td align="center"><?php echo $no;?></td>
            <td align="center"><?php echo $rby['buyer'];?></td>
            <td align="center"><?php echo $rga['jml_kk_a'];?></td>
            <td align="center"><?php echo $rgb['jml_kk_b'];?></td>
            <td align="center"><?php echo $rgc['jml_kk_c'];?></td>
            <td align="center"><?php echo $rgd['jml_kk_d'];?></td>
            <td align="center"><?php echo $rgn['jml_kk_null'];?></td>
            <td align="center"><?php echo number_format(($rby['jml_kk']/$rball['jml_kk_all'])*100,2)." %";?></td>
        </tr>
        <?php 
          $no++;}
        ?>
</table>
</body>