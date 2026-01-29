<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Lolos-Diposisi-".substr($_GET['awal'],0,10).".xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda
?>
<?php
ini_set("error_reporting", 1);
session_start();
//error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
include "../../koneksi.php";
include "../../tgl_indo.php";
//--
$idkk=$_REQUEST['idkk'];
$act=$_GET['g'];
//-
$Awal=$_GET['awal'];
$Akhir=$_GET['akhir'];
$TotalKirim=(float)$_GET['total'];
$TotalLot=(float)$_GET['total_lot'];
$qTgl=sqlsrv_query($con_db_qc_sqlsrv,"SELECT CONVERT(VARCHAR(10),CURRENT_TIMESTAMP,120) as tgl_skrg,CONVERT(VARCHAR(8),CURRENT_TIMESTAMP,108) as jam_skrg");
$rTgl=sqlsrv_fetch_array($qTgl,SQLSRV_FETCH_ASSOC);
if($Awal!=""){$tgl=substr($Awal,0,10); $jam=$Awal;}else{$tgl=$rTgl['tgl_skrg']; $jam=$rTgl['jam_skrg'];}
?>
<?php 
$nmBln=array(1 => "JANUARI","FEBUARI","MARET","APRIL","MEI","JUNI","JULI","AGUSTUS","SEPTEMBER","OKTOBER","NOVEMBER","DESEMBER");	
?>
<body>
<table width="100%" border='1'>
  <tr>
    <th colspan="16" align="center">LAPORAN LOLOS/DISPOSISI</th>
  </tr>
  <tr>
    <th colspan="16" align="center">PERIODE: <?php echo date("d/m/Y", strtotime($Awal));?> S/D <?php echo date("d/m/Y", strtotime($Akhir));?></th>
  </tr>
  <thead>
    <tr align="center">
      <td><font size="-2"><strong>NO</strong></font></td>
      <td><font size="-2"><strong>LANGGANAN</strong></font></td>
      <td><font size="-2"><strong>ORDER</strong></font></td>
      <td><font size="-2"><strong>JENIS KAIN</strong></font></td>
      <td><font size="-2"><strong>LEBAR &amp; GRAMASI</strong></font></td>
      <td><font size="-2"><strong>LOT</strong></font></td>
      <td><font size="-2"><strong>WARNA</strong></font></td>
      <td><font size="-2"><strong>QTY KIRIM</strong></font></td>
      <td><font size="-2"><strong>QTY KELUHAN</strong></font></td>
      <td><font size="-2"><strong>MASALAH DOMINAN</strong></font></td>
      <td><font size="-2"><strong>MASALAH</strong></font></td>
      <td><font size="-2"><strong>TGL PROSES</strong></font></td>
      <td><font size="-2"><strong>SOLUSI</strong></font></td>
      <td><font size="-2"><strong>PENYEBAB</strong></font></td>
      <td><font size="-2"><strong>PEJABAT</strong></font></td>
      <td><font size="-2"><strong>SHIFT</strong></font></td>
      <td><font size="-2"><strong>KETERANGAN</strong></font></td>
      <td><font size="-2"><strong>&nbsp;</strong></font></td>
    </tr>
  </thead>
	<tbody>  
		<?php
	$no=1;
    $qry1=sqlsrv_query($con_db_qc_sqlsrv,"SELECT * FROM db_qc.tbl_aftersales_now WHERE CONVERT(date, tgl_buat) BETWEEN ? AND ?
            AND (sts='1' OR sts_disposisiqc='1' OR kategori!='' OR (pejabat!='' AND sts_disposisipro='1')) ORDER BY pejabat ASC", array($Awal, $Akhir));
    $tkirim=0;
    $tclaim=0;
			while($row1=sqlsrv_fetch_array($qry1, SQLSRV_FETCH_ASSOC)){
        $qryQCF=sqlsrv_query($con_db_qc_sqlsrv,"SELECT a.nokk, a.masalah_dominan, b.tgl_fin, b.tgl_ins, b.tgl_masuk, b.ket from db_qc.tbl_aftersales_now a left join db_qc.tbl_qcf b on a.nokk=b.nokk WHERE a.nokk=?", array($row1['nokk']));
        $rQCF=sqlsrv_fetch_array($qryQCF, SQLSRV_FETCH_ASSOC);
				if($row1['t_jawab']!="" and $row1['t_jawab1']!="" and $row1['t_jawab2']!=""){ $tjawab=$row1['t_jawab'].",".$row1['t_jawab1'].", ".$row1['t_jawab2'];
				}else if($row1['t_jawab']!="" and $row1['t_jawab1']!="" and $row1['t_jawab2']==""){
				$tjawab=$row1['t_jawab'].",".$row1['t_jawab1'];	
				}else if($row1['t_jawab']!="" and $row1['t_jawab1']=="" and $row1['t_jawab2']!=""){
				$tjawab=$row1['t_jawab'].",".$row1['t_jawab2'];	
				}else if($row1['t_jawab']=="" and $row1['t_jawab1']!="" and $row1['t_jawab2']!=""){
				$tjawab=$row1['t_jawab1'].",".$row1['t_jawab2'];	
				}else if($row1['t_jawab']!="" and $row1['t_jawab1']=="" and $row1['t_jawab2']==""){
				$tjawab=$row1['t_jawab'];
				}else if($row1['t_jawab']=="" and $row1['t_jawab1']!="" and $row1['t_jawab2']==""){
				$tjawab=$row1['t_jawab1'];
				}else if($row1['t_jawab']=="" and $row1['t_jawab1']=="" and $row1['t_jawab2']!=""){
				$tjawab=$row1['t_jawab2'];	
				}else if($row1['t_jawab']=="" and $row1['t_jawab1']=="" and $row1['t_jawab2']==""){
				$tjawab="";	
				}
		 ?>
          <tr valign="top">
            <td align="center"><font size="-2"><?php echo $no; ?></font></td>
            <td><font size="-2"><?php echo strtoupper($row1['langganan']);?></font></td>
            <td align="center"><font size="-2"><?php echo strtoupper($row1['no_order']);?></font></td>
            <td><font size="-2"><?php echo strtoupper($row1['jenis_kain']);?></font></td>
            <td align="center"><font size="-2"><?php echo $row1['lebar']."x".$row1['gramasi'];?></font></td>
            <td align="center"><font size="-2"><?php echo strtoupper($row1['lot']);?></font></td>
            <td align="center"><font size="-2"><?php echo strtoupper($row1['warna']);?></font></td>
            <td align="right"><font size="-2"><?php echo strtoupper($row1['qty_kirim']);?></font></td>
            <td align="right"><font size="-2"><?php echo strtoupper($row1['qty_claim']);?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['masalah_dominan'];?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['masalah'];?></font></td>
            <td valign="top"><font size="-2"><?php if(strpos($rQCF['ket'],'AKJ') !== false){echo $rQCF['tgl_masuk'];}else if($rQCF['masalah_dominan']=="Beda Warna"){echo $rQCF['tgl_fin'];}else if($rQCF['masalah_dominan']!="Beda Warna"){echo $rQCF['tgl_ins'];}?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['solusi'];?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['penyebab'];?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['pejabat'];?></font></td>
            <td valign="top"><font size="-2"><?php if($row1['shift2']!=""){echo $row1['shift'].",".$row1['shift2'];}else{echo $row1['shift'];}?></font></td>
            <td valign="top"><font size="-2"><?php echo $row1['ket'];?></font></td>
            <td valign="top"><font size="-2"><?php if($row1['sts_check']=="Ceklis"){echo "&#10004";}else{echo "X";}?></font></td>
          </tr>
          <?php	$no++; 
				$tkirim=$tkirim+$row1['qty_kirim'];
				$tclaim=$tclaim+$row1['qty_claim'];
			} ?>	
          <tr valign="top">
            <td colspan="8" align="right"><strong>TOTAL</strong></td>
            <td align="right"><strong><?php echo $tkirim;?></strong></td>
            <td align="right"><strong><?php echo $tclaim;?></strong></td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
        </tbody>
    </tr>
</table>
<br>
<table width="100%" border="0">
  <tr>
      <td align="left" style="border-top:0px #000000 solid; 
                                            border-bottom:0px #000000 solid;
                                            border-left:0px #000000 solid; 
                                            border-right:0px #000000 solid;" width="45%"><strong>LOLOS QC</strong>
      </td>
    </tr>
</table>
<table width="100%" border="1">
<tr>
      <td align="left" style="border-top:0px #000000 solid; 
                                            border-bottom:0px #000000 solid;
                                            border-left:0px #000000 solid; 
                                            border-right:0px #000000 solid;"><table width="100%">
        <tr>
          <td style="border-top:0px #000000 solid; 
                                            border-bottom:0px #000000 solid;
                                            border-left:0px #000000 solid; 
                                            border-right:0px #000000 solid;"><table width="50%" border="1">
            <?php
            if($_GET['langganan']!=""){ $lgn =" AND langganan LIKE '%$_GET[langganan]%' "; }else{$lgn = " ";}
            //Shift A
            $qryjml1=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_a FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='A' OR shift2='A')", array($Awal, $Akhir));
            $rowjml1=sqlsrv_fetch_array($qryjml1, SQLSRV_FETCH_ASSOC);
            //Shift B
            $qryjml2=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_b FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='B' OR shift2='B')", array($Awal, $Akhir));
            $rowjml2=sqlsrv_fetch_array($qryjml2, SQLSRV_FETCH_ASSOC);
            //Shift C
            $qryjml3=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_c FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='C' OR shift2='C')", array($Awal, $Akhir));
            $rowjml3=sqlsrv_fetch_array($qryjml3, SQLSRV_FETCH_ASSOC);
            //Shift Non Shift
            $qryjml4=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_non FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Non-Shift' OR shift2='Non-Shift')", array($Awal, $Akhir));
            $rowjml4=sqlsrv_fetch_array($qryjml4, SQLSRV_FETCH_ASSOC);
            //QC2
            $qryjml5=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_qc2 FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='QC2' OR shift2='QC2')", array($Awal, $Akhir));
            $rowjml5=sqlsrv_fetch_array($qryjml5, SQLSRV_FETCH_ASSOC);
            //TQ
            $qryjml6=sqlsrv_query($con_db_qc_sqlsrv,"SELECT COUNT(*) as jml_tq FROM db_qc.tbl_aftersales_now WHERE
            CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Test Quality' OR shift2='Test Quality')", array($Awal, $Akhir));
            $rowjml6=sqlsrv_fetch_array($qryjml6, SQLSRV_FETCH_ASSOC);
            ?>
            <tr>
                <td align="center" rowspan ="3" width="25%"><strong>Shift</strong></td>
                <td align="center" rowspan ="3" width="25%"><strong>Qty Keluhan</strong></td>
                <td align="center" rowspan ="3" width="10%"><strong>% (Qty Keluhan)</strong></td>
                <td align="center" rowspan ="3" width="25%"><strong>Total Kasus</strong></td>
                <td align="center" rowspan ="3" width="10%"><strong>% (Total Kasus)</strong></td>
                <td align="center" colspan="4" width="25%"><strong>Lolos QC (Kasus)</strong></td>
            </tr>
            <tr>
                <td align="center" width="25%" colspan="2"><strong>Defect</strong></td>
                <td align="center" width="10%" colspan="2"><strong>BW</strong></td>
            </tr>
            <tr>
                <td align="center" width="25%"><strong>Kasus</strong></td>
                <td align="center" width="10%"><strong>Qty</strong></td>
                <td align="center" width="25%"><strong>Kasus</strong></td>
                <td align="center" width="10%"><strong>Qty</strong></td>
            </tr>
            <tr>
              <?php
              if($_GET['langganan']!=""){ $lgn =" AND langganan LIKE '%$_GET[langganan]%' "; }else{$lgn = " ";}
              $qry1=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_a FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='A' OR shift2='A')", array($Awal, $Akhir)); 
              //DEFECT LOLOS QC
              $qrylqcdefa=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='A' OR shift2='A')", array($Awal, $Akhir));
              $rlqcdefa=sqlsrv_fetch_array($qrylqcdefa, SQLSRV_FETCH_ASSOC);
              //BEDA WARNA LOLOS QC
              $qrylqcbwa=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='A' OR shift2='A')", array($Awal, $Akhir));
              $rlqcbwa=sqlsrv_fetch_array($qrylqcbwa, SQLSRV_FETCH_ASSOC);
              $tot_a=0;
              while($row1=sqlsrv_fetch_array($qry1, SQLSRV_FETCH_ASSOC)){
              $tot_a=$tot_a+$row1['qty_claim_a']; }?>
                <td align="left">A</td>
                <td align="right"><?php echo number_format($tot_a,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_a/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml1['jml_a']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml1['jml_a']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdefa['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdefa['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwa['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwa['qty_kg'],2)." Kg";?></td>
            </tr>
            <tr>
            <?php
              $qry2=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_b FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='B' OR shift2='B')", array($Awal, $Akhir)); 
              //DEFECT LOLOS QC
              $qrylqcdefb=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='B' OR shift2='B')", array($Awal, $Akhir));
              $rlqcdefb=sqlsrv_fetch_array($qrylqcdefb, SQLSRV_FETCH_ASSOC);
              //BEDA WARNA LOLOS QC
              $qrylqcbwb=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='B' OR shift2='B')", array($Awal, $Akhir));
              $rlqcbwb=sqlsrv_fetch_array($qrylqcbwb, SQLSRV_FETCH_ASSOC);
              $tot_b=0;
              while($row2=sqlsrv_fetch_array($qry2, SQLSRV_FETCH_ASSOC)){
              $tot_b=$tot_b+$row2['qty_claim_b']; }?>
                <td align="left">B</td>
                <td align="right"><?php echo number_format($tot_b,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_b/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml2['jml_b']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml2['jml_b']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdefb['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdefb['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwb['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwb['qty_kg'],2)." Kg";?></td>
            </tr>
            <tr>
            <?php
              $qry3=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_c FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='C' OR shift2='C')", array($Awal, $Akhir)); 
              //DEFECT LOLOS QC
              $qrylqcdefc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='C' OR shift2='C')", array($Awal, $Akhir));
              $rlqcdefc=sqlsrv_fetch_array($qrylqcdefc, SQLSRV_FETCH_ASSOC);
              //BEDA WARNA LOLOS QC
              $qrylqcbwc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='C' OR shift2='C')", array($Awal, $Akhir));
              $rlqcbwc=sqlsrv_fetch_array($qrylqcbwc, SQLSRV_FETCH_ASSOC);
              $tot_c=0;
              while($row3=sqlsrv_fetch_array($qry3, SQLSRV_FETCH_ASSOC)){
              $tot_c=$tot_c+$row3['qty_claim_c']; }?>
                <td align="left">C</td>
                <td align="right"><?php echo number_format($tot_c,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_c/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml3['jml_c']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml3['jml_c']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdefc['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdefc['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwc['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwc['qty_kg'],2)." Kg";?></td>
            </tr>
            <tr>
            <?php
              $qry4=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_non FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Non-Shift' OR shift2='Non-Shift')", array($Awal, $Akhir)); 
               //DEFECT LOLOS QC
               $qrylqcdefn=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
               CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Non-Shift' OR shift2='Non-Shift')", array($Awal, $Akhir));
               $rlqcdefn=sqlsrv_fetch_array($qrylqcdefn, SQLSRV_FETCH_ASSOC);
               //BEDA WARNA LOLOS QC
               $qrylqcbwn=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
               CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Non-Shift' OR shift2='Non-Shift')", array($Awal, $Akhir));
               $rlqcbwn=sqlsrv_fetch_array($qrylqcbwn, SQLSRV_FETCH_ASSOC);
              $tot_non=0;
              while($row4=sqlsrv_fetch_array($qry4, SQLSRV_FETCH_ASSOC)){
              $tot_non=$tot_non+$row4['qty_claim_non']; }?>
                <td align="left">Non-Shift</td>
                <td align="right"><?php echo number_format($tot_non,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_non/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml4['jml_non']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml4['jml_non']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdefn['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdefn['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwn['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwn['qty_kg'],2)." Kg";?></td>
            </tr>
            <tr>
              <?php
              $qry5=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_qc2 FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='QC2' OR shift2='QC2')", array($Awal, $Akhir)); 
              //DEFECT LOLOS QC
              $qrylqcdefqc2=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='QC2' OR shift2='QC2')", array($Awal, $Akhir));
              $rlqcdefqc2=sqlsrv_fetch_array($qrylqcdefqc2, SQLSRV_FETCH_ASSOC);
              //BEDA WARNA LOLOS QC
              $qrylqcbwqc2=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='QC2' OR shift2='QC2')", array($Awal, $Akhir));
              $rlqcbwqc2=sqlsrv_fetch_array($qrylqcbwqc2, SQLSRV_FETCH_ASSOC);
              $tot_qc2=0;
              while($row5=sqlsrv_fetch_array($qry5, SQLSRV_FETCH_ASSOC)){
              $tot_qc2=$tot_qc2+$row5['qty_claim_qc2']; }?>
                <td align="left">QC2</td>
                <td align="right"><?php echo number_format($tot_qc2,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_qc2/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml5['jml_qc2']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml5['jml_qc2']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdefqc2['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdefqc2['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwqc2['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwqc2['qty_kg'],2)." Kg";?></td>
              </tr>
              <tr>
              <?php
              $qry6=sqlsrv_query($con_db_qc_sqlsrv,"SELECT shift, shift2, if(shift2!='',qty_claim/2,qty_claim) as qty_claim_tq FROM db_qc.tbl_aftersales_now WHERE
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Test Quality' OR shift2='Test Quality')", array($Awal, $Akhir)); 
              //DEFECT LOLOS QC
              $qrylqcdeftq=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_def, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan != 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Test Quality' OR shift2='Test Quality')", array($Awal, $Akhir));
              $rlqcdeftq=sqlsrv_fetch_array($qrylqcdeftq, SQLSRV_FETCH_ASSOC);
              //BEDA WARNA LOLOS QC
              $qrylqcbwtq=sqlsrv_query($con_db_qc_sqlsrv,"SELECT count(*) as jml_bw, SUM(qty_claim) as qty_kg from db_qc.tbl_aftersales_now where sts_check ='Ceklis' and masalah_dominan = 'Beda Warna' and 
              CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts='1' AND sts_revdis='0' AND (shift='Test Quality' OR shift2='Test Quality')", array($Awal, $Akhir));
              $rlqcbwtq=sqlsrv_fetch_array($qrylqcbwtq, SQLSRV_FETCH_ASSOC);
              $tot_tq=0;
              while($row6=sqlsrv_fetch_array($qry6, SQLSRV_FETCH_ASSOC)){
              $tot_tq=$tot_tq+$row6['qty_claim_tq']; }?>
                <td align="left">Test Quality</td>
                <td align="right"><?php echo number_format($tot_tq,2)." Kg";?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($tot_tq/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rowjml6['jml_tq']." Kasus"; ?></td>
                <td align="center"><?php echo ($TotalLot ? number_format(($rowjml6['jml_tq']/$TotalLot)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlqcdeftq['jml_def'];?></td>
                <td align="center"><?php echo number_format($rlqcdeftq['qty_kg'],2)." Kg";?></td>
                <td align="center"><?php echo $rlqcbwtq['jml_bw'];?></td>
                <td align="center"><?php echo number_format($rlqcbwtq['qty_kg'],2)." Kg";?></td>
              </tr>
            <?php 
            $total=$tot_a+$tot_b+$tot_c+$tot_non+$tot_qc2+$tot_tq; 
            $totalkasus=$rowjml1['jml_a']+$rowjml2['jml_b']+$rowjml3['jml_c']+$rowjml4['jml_non']+$rowjml5['jml_qc2']+$rowjml6['jml_tq'];
            $totallqcdef=$rlqcdefa['jml_def']+$rlqcdefb['jml_def']+$rlqcdefc['jml_def']+$rlqcdefn['jml_def']+$rlqcdefqc2['jml_def']+$rlqcdeftq['jml_def'];
            $totalqtylqcdef=$rlqcdefa['qty_kg']+$rlqcdefb['qty_kg']+$rlqcdefc['qty_kg']+$rlqcdefn['qty_kg']+$rlqcdefqc2['qty_kg']+$rlqcdeftq['qty_kg'];
            $totallqcbw=$rlqcbwa['jml_bw']+$rlqcbwb['jml_bw']+$rlqcbwc['jml_bw']+$rlqcbwn['jml_bw']+$rlqcbwqc2['jml_bw']+$rlqcbwtq['jml_bw'];
            $totalqtylqcbw=$rlqcbwa['qty_kg']+$rlqcbwb['qty_kg']+$rlqcbwc['qty_kg']+$rlqcbwn['qty_kg']+$rlqcbwqc2['qty_kg']+$rlqcbwtq['qty_kg'];
            ?>
            <tr>
                <td align="left"><strong>Total</strong></td>
                <td align="right"><strong><?php echo number_format($total,2)." Kg"; ?></strong></td>
                <td align="center"><strong><?php echo ($TotalKirim ? number_format(($total/$TotalKirim)*100,2) : "0.00")." %";?></strong></td>
                <td align="center"><strong><?php echo $totalkasus." Kasus"; ?></strong></td>
                <td align="center"><strong><?php echo ($TotalLot ? number_format(($totalkasus/$TotalLot)*100,2) : "0.00")." %";?></strong></td>
                <td align="center"><strong><?php echo $totallqcdef." Kasus"; ?></strong></td>
                <td align="center"><strong><?php echo number_format($totalqtylqcdef,2)." Kg";?></strong></td>
                <td align="center"><strong><?php echo $totallqcbw." Kasus"; ?></strong></td>
                <td align="center"><strong><?php echo number_format($totalqtylqcbw,2)." Kg";?></strong></td>
            </tr>
            
            <tr>
                <td align="left"><strong>Total Kirim</strong></td>
                <td align="right"><strong><?php echo number_format($TotalKirim,2)." Kg"; ?></strong></td>
            </tr>
            <tr>
                <td align="left"><strong>Total Lot Kain</strong></td>
                <td align="right"><strong><?php echo $TotalLot; ?></strong></td>
            </tr>
          </table></td>
        </tr>
      </table></td>
    </tr>
</table>
<br>
<table width="100%" border="1">
    <tr>
      <td style="border-top:0px #000000 solid; 
                                            border-bottom:0px #000000 solid;
                                            border-left:0px #000000 solid; 
                                            border-right:0px #000000 solid;" valign="top">
          <table width="100%" border="1" class="table-list1">
            <tr align="center">
              <td rowspan="2" colspan="2"><font size="-2"><strong>PENYEBAB KELUHAN PELANGGAN</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>QTY KELUHAN (KG)</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>% DIBANDINGKAN TOTAL KIRIM</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>RETUR</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>% DIBANDINGKAN TOTAL KIRIM</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>GANTI KAIN EKSTERNAL</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>% DIBANDINGKAN TOTAL KIRIM</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>PERBAIKAN GARMENT</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>% DIBANDINGKAN TOTAL KIRIM</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>LETTER OF GUARANTEE (LG)</strong></font></td>
              <td rowspan="2"><font size="-2"><strong>% DIBANDINGKAN TOTAL KIRIM</strong></font></td>
            </tr>
            <tr>
            </tr>
              <?php 
                $qryjml=sqlsrv_query($con_db_qc_sqlsrv,"SELECT a.jml_dispro, b.jml_disqc FROM
                (SELECT COUNT(DISTINCT(pejabat)) AS jml_dispro FROM db_qc.tbl_aftersales_now WHERE CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts_disposisipro = '1' AND pejabat!='') AS A,
                (SELECT COUNT(DISTINCT(pejabat)) AS jml_disqc FROM db_qc.tbl_aftersales_now WHERE CONVERT(date, tgl_buat) BETWEEN ? AND ? AND sts_disposisiqc = '1' AND pejabat!='') AS B", array($Awal, $Akhir, $Awal, $Akhir));
                $rjml = sqlsrv_fetch_array($qryjml, SQLSRV_FETCH_ASSOC);
              ?>
            <tr>
                <td align="center" rowspan="<?php echo $rjml['jml_dispro']+1;?>" >DISPOSISI PRODUKSI</td>
            </tr>
            <?php
            $pjbpro=sqlsrv_query($con_db_qc_sqlsrv,"SELECT DISTINCT pejabat AS pejabat, SUM(qty_claim) AS qty_claim FROM db_qc.tbl_aftersales_now WHERE CONVERT(date, tgl_buat) BETWEEN ? AND ? 
            AND sts_disposisipro = '1' AND pejabat!='' 
            GROUP BY pejabat 
            ORDER BY qty_claim DESC", array($Awal, $Akhir));
            while($rpjbpro=sqlsrv_fetch_array($pjbpro, SQLSRV_FETCH_ASSOC)){
            $dpro=sqlsrv_query($con_db_qc_sqlsrv,"SELECT a.pejabat, SUM(a.qty_claim) AS qty_claim, 
            SUM(CASE WHEN a.solusi='RETUR' THEN a.qty_claim ELSE 0 END) AS qty_retur,
            SUM(CASE WHEN a.solusi='CUTTING LOSS (GANTI KAIN)' THEN a.qty_claim ELSE 0 END) AS qty_gantikain,
            SUM(CASE WHEN a.solusi='PERBAIKAN GARMENT' THEN a.qty_claim ELSE 0 END) AS qty_pg,
            SUM(CASE WHEN a.solusi='LETTER OF GUARANTEE (LG)' THEN a.qty_claim ELSE 0 END) AS qty_lg 
            FROM db_qc.tbl_aftersales_now a
            WHERE CONVERT(date, a.tgl_buat) BETWEEN ? AND ? AND a.sts_disposisipro='1' AND a.pejabat=?
            GROUP BY a.pejabat", array($Awal, $Akhir, $rpjbpro['pejabat']));
            $rpro=sqlsrv_fetch_array($dpro, SQLSRV_FETCH_ASSOC);
            ?>
            <tr>
                <td align="center"><?php echo $rpjbpro['pejabat'];?></td>
                <td align="center"><?php echo $rpro['qty_claim'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rpro['qty_claim']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rpro['qty_retur'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rpro['qty_retur']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rpro['qty_gantikain'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rpro['qty_gantikain']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rpro['qty_pg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rpro['qty_pg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rpro['qty_lg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rpro['qty_lg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
            </tr>
            <?php } ?>
            <tr>
                <td align="center" rowspan="<?php echo $rjml['jml_disqc']+1;?>" >DISPOSISI QC</td>
            </tr>
            <?php 
            $pjbqc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT DISTINCT pejabat AS pejabat, SUM(qty_claim) AS qty_claim FROM db_qc.tbl_aftersales_now WHERE CONVERT(date, tgl_buat) BETWEEN ? AND ? 
            AND sts_disposisiqc = '1' AND pejabat!='' 
            GROUP BY pejabat 
            ORDER BY qty_claim DESC", array($Awal, $Akhir));
            while($rpjbqc=sqlsrv_fetch_array($pjbqc, SQLSRV_FETCH_ASSOC)){
            $dqc=sqlsrv_query($con_db_qc_sqlsrv,"SELECT a.pejabat, SUM(a.qty_claim) AS qty_claim,
            SUM(CASE WHEN a.solusi='RETUR' THEN a.qty_claim ELSE 0 END) AS qty_retur,
            SUM(CASE WHEN a.solusi='CUTTING LOSS (GANTI KAIN)' THEN a.qty_claim ELSE 0 END) AS qty_gantikain,
            SUM(CASE WHEN a.solusi='PERBAIKAN GARMENT' THEN a.qty_claim ELSE 0 END) AS qty_pg,
            SUM(CASE WHEN a.solusi='LETTER OF GUARANTEE (LG)' THEN a.qty_claim ELSE 0 END) AS qty_lg 
            FROM db_qc.tbl_aftersales_now a
            WHERE CONVERT(date, a.tgl_buat) BETWEEN ? AND ? AND a.sts_disposisiqc='1' AND a.pejabat=?
            GROUP BY a.pejabat", array($Awal, $Akhir, $rpjbqc['pejabat']));
            $rqc=sqlsrv_fetch_array($dqc, SQLSRV_FETCH_ASSOC);
            ?>
            <tr>
                <td align="center"><?php echo $rpjbqc['pejabat'];?></td>
                <td align="center"><?php echo $rqc['qty_claim'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rqc['qty_claim']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rqc['qty_retur'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rqc['qty_retur']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rqc['qty_gantikain'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rqc['qty_gantikain']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rqc['qty_pg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rqc['qty_pg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rqc['qty_lg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rqc['qty_lg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
            </tr>
            <?php } ?>
            <?php
            $dlolos=sqlsrv_query($con_db_qc_sqlsrv,"SELECT SUM(a.qty_claim) AS qty_claim, 
            SUM(CASE WHEN a.solusi='RETUR' THEN a.qty_claim ELSE 0 END) AS qty_retur,
            SUM(CASE WHEN a.solusi='CUTTING LOSS (GANTI KAIN)' THEN a.qty_claim ELSE 0 END) AS qty_gantikain,
            SUM(CASE WHEN a.solusi='PERBAIKAN GARMENT' THEN a.qty_claim ELSE 0 END) AS qty_pg,
            SUM(CASE WHEN a.solusi='LETTER OF GUARANTEE (LG)' THEN a.qty_claim ELSE 0 END) AS qty_lg 
            FROM db_qc.tbl_aftersales_now a
            WHERE CONVERT(date, a.tgl_buat) BETWEEN ? AND ? AND a.sts='1'", array($Awal, $Akhir));
            $rlolos=sqlsrv_fetch_array($dlolos, SQLSRV_FETCH_ASSOC);
            ?>
            <tr>
                <td align="center" colspan="2">LOLOS QC</td>
                <td align="center"><?php echo $rlolos['qty_claim'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rlolos['qty_claim']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlolos['qty_retur'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rlolos['qty_retur']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlolos['qty_gantikain'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rlolos['qty_gantikain']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlolos['qty_pg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rlolos['qty_pg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rlolos['qty_lg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rlolos['qty_lg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
            </tr>
            <?php 
            $ktgr = sqlsrv_query($con_db_qc_sqlsrv,"SELECT kategori,keterangan FROM db_qc.tbl_kategori_aftersales ORDER BY id ASC");
            while($rktgr=sqlsrv_fetch_array($ktgr, SQLSRV_FETCH_ASSOC)){
                $dkt=sqlsrv_query($con_db_qc_sqlsrv,"SELECT SUM(a.qty_claim) AS qty_claim,
                SUM(CASE WHEN a.solusi='RETUR' THEN a.qty_claim ELSE 0 END) AS qty_retur,
                SUM(CASE WHEN a.solusi='CUTTING LOSS (GANTI KAIN)' THEN a.qty_claim ELSE 0 END) AS qty_gantikain,
                SUM(CASE WHEN a.solusi='PERBAIKAN GARMENT' THEN a.qty_claim ELSE 0 END) AS qty_pg,
                SUM(CASE WHEN a.solusi='LETTER OF GUARANTEE (LG)' THEN a.qty_claim ELSE 0 END) AS qty_lg 
                FROM db_qc.tbl_aftersales_now a
                WHERE CONVERT(date, a.tgl_buat) BETWEEN ? AND ? AND a.kategori=?", array($Awal, $Akhir, $rktgr['kategori']));
                $rkt=sqlsrv_fetch_array($dkt, SQLSRV_FETCH_ASSOC);
            ?>
            <tr>
                <td align="center" colspan="2"><?php echo $rktgr['keterangan'];?></td>
                <td align="center"><?php echo $rkt['qty_claim'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rkt['qty_claim']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rkt['qty_retur'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rkt['qty_retur']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rkt['qty_gantikain'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rkt['qty_gantikain']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rkt['qty_pg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rkt['qty_pg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
                <td align="center"><?php echo $rkt['qty_lg'];?></td>
                <td align="center"><?php echo ($TotalKirim ? number_format(($rkt['qty_lg']/$TotalKirim)*100,2) : "0.00")." %";?></td>
            </tr>
            <?php } ?>
          </table></td>
    </tr>
</table>

<script>
//alert('cetak');window.print();
</script> 
</body>
</html>