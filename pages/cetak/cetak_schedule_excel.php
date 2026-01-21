<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=schedule_".substr($_GET['tgl'],0,10).".xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda
?>
<?php
//$lReg_username=$_SESSION['labReg_username'];

include "../../koneksi.php";
include "../../tgl_indo.php";
error_reporting(0);
//--
$idkk=$_REQUEST['idkk'];
$act=$_GET['g'];
//-
$Awal=$_GET['Awal'];
$Akhir=$_GET['Akhir'];
$qTgl = sqlsrv_query($con_db_qc_sqlsrv, "SELECT CONVERT(VARCHAR(10),CURRENT_TIMESTAMP,120) as tgl_skrg,CONVERT(VARCHAR(8),CURRENT_TIMESTAMP,108) as jam_skrg");
$rTgl = sqlsrv_fetch_array($qTgl,SQLSRV_FETCH_ASSOC);
if($Awal!=""){$tgl=substr($Awal,0,10); $jam=$Awal;}else{$tgl=$rTgl['tgl_skrg']; $jam=$rTgl['jam_skrg'];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- <link href="styles_cetak.css" rel="stylesheet" type="text/css"> -->
<title>Cetak Schedule</title>
<style>
.hurufvertical {
 writing-mode:tb-rl;
    -webkit-transform:rotate(-90deg);
    -moz-transform:rotate(-90deg);
    -o-transform: rotate(-90deg);
    -ms-transform:rotate(-90deg);
    transform: rotate(180deg);
    white-space:nowrap;
    float:left;
}	

input{
text-align:center;
border:hidden;
}
@media print {
  ::-webkit-input-placeholder { /* WebKit browsers */
      color: transparent;
  }
  :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
      color: transparent;
  }
  ::-moz-placeholder { /* Mozilla Firefox 19+ */
      color: transparent;
  }
  :-ms-input-placeholder { /* Internet Explorer 10+ */
      color: transparent;
  }
  .pagebreak { page-break-before:always; }
  .header {display:block}
  table thead 
   {
    display: table-header-group;
   }
}	
</style>	
</head>

<body>
<table width="100%">
  <tr>
    <td><table width="100%" border="1" class="table-list1">
        <tr>
          <td colspan="12" align="center" scope="col"><h2>SCHEDULE QC FINAL</h2></td>
        </tr>
        <tr>
          <td colspan="2" scope="col"><font size="-1">Hari/Tanggal : <?php echo tanggal_indo ($tgl, true);?></font></td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td scope="col">&nbsp;</td>
          <td width="4%" scope="col">&nbsp;</td>
          <td width="6%" scope="col">&nbsp;</td>
          <td scope="col"><font size="-1">Jam: <?php echo date('H:i:s', strtotime($jam));?></font></td>
        </tr>
        <tr>
          <td rowspan="2" scope="col"><div align="center">Nomor<br />
            Mesin</div></td>
          <td rowspan="2" scope="col"><div align="center">No. Urut</div></td>
          <td rowspan="2" scope="col"><div align="center">Pelanggan</div></td>
          <td rowspan="2" scope="col"><div align="center">No. Order</div></td>
          <td rowspan="2" scope="col"><div align="center">Jenis Kain</div></td>
          <td rowspan="2" scope="col"><div align="center">Warna</div></td>
          <td rowspan="2" scope="col"><div align="center">No. Warna</div></td>
          <td rowspan="2" scope="col"><div align="center">Lot</div></td>
          <td rowspan="2" scope="col"><div align="center">Tanggal Delivery</div></td>
          <td colspan="2" scope="col"><div align="center">Quantity</div></td>
          <td rowspan="2" scope="col"><div align="center">Keterangan</div></td>
        </tr>
        <tr>
          <td><div align="center">Roll</div></td>
          <td><div align="center">Kg</div></td>
        </tr>
	<?php	
  $data = sqlsrv_query($con_db_qc_sqlsrv, "SELECT b.* from db_qc.tbl_mesin b WHERE ket='Inspection' ORDER BY b.no_mesin ASC");
	$no=1;
	$n=1;
	$c=0;
	 ?>
	<?php
	  $col=0;
  while($rowd = sqlsrv_fetch_array($data,SQLSRV_FETCH_ASSOC)) {
			$bgcolor = ($col++ & 1) ? 'gainsboro' : 'antiquewhite';
  $qryMC = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
   	COUNT(*) as jml_mc
FROM
	db_qc.tbl_schedule 
WHERE
	NOT STATUS = 'selesai'
	AND no_mesin='".$rowd['no_mesin']."'
GROUP BY
	no_mesin");
  $rMC = sqlsrv_fetch_array($qryMC,SQLSRV_FETCH_ASSOC);
  $qryUrt = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
  max(id),
	max(lot) lot,
	no_mesin,
	no_urut,
	max(buyer) buyer,
	max(langganan) langganan,
	max(no_order) no_order,
	max(nokk) nokk,
	max(jenis_kain) jenis_kain,
	max(warna) warna,
	max(no_warna) no_warna,
	max(rol) rol,
	max(bruto) bruto,
	max(proses) proses,
	max(ket_status) ket_status,
	max(CONVERT(VARCHAR(10),tgl_delivery)) tgl_delivery,
	max(ket_kain) ket_kain,
	max(mc_from) mc_from,
	max(catatan) catatan,
	max(personil) personil
FROM
	db_qc.tbl_schedule 
WHERE
	NOT STATUS = 'selesai'
	AND no_mesin='".$rowd['no_mesin']."'
	AND no_urut='1'
GROUP BY
	no_mesin,
	no_urut 
ORDER BY
	no_mesin,no_urut ASC");
       $rU = sqlsrv_fetch_array($qryUrt,SQLSRV_FETCH_ASSOC);
		 ?>
    <tr>
      <td rowspan="<?php echo $rMC['jml_mc'];?>"><div align="center" style="font-size: 18px;"><strong><?php echo $rowd['no_mesin'];?></strong>
        </div><div align="center" style="font-size: 12px;">(<?php echo $rowd['ket'];?>)</div>
      </td>
      <td align="center" valign="top" style="height: 0.27in;"><?php echo $rU['no_urut'];?></td>
      <td align="center" valign="top"><?php echo $rU['langganan']; ?></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rU['no_order']; ?></div></td>
      <td valign="top"><div style="font-size: 8px;">
        <?php if(strlen($rU['jenis_kain'])>25){echo substr($rU['jenis_kain'],0,25)."...";}else{echo $rU['jenis_kain'];} ?>
      </div></td>
      <td align="center" valign="top"><div style="font-size: 8px;">
        <?php if(strlen($rU['warna'])>25){echo substr($rU['warna'],0,25)."...";}else{echo $rU['warna'];} ?>
      </div></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rU['no_warna']; ?></div></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rU['lot']; ?></div></td>
      <td align="center" valign="top"><?php if($rU['tgl_delivery']!="0000-00-00"){echo $rU['tgl_delivery'];} ?></td>
      <td align="center" valign="top"><?php if($rU['rol']!="0"){echo $rU['rol'];} ?></td>
      <td align="right" valign="top"><?php if($rU['bruto']!="0"){echo $rU['bruto'];} ?></td>
      <td valign="top"><?php echo $rU['ket_status']; ?><br />
        <?php echo $rU['personil']; ?><br />
        <?php echo $rU['ket_kain']; ?>
        <?php if($rU['mc_from']!=""){ echo " MC".$rU['mc_from'];} ?>
        <br />
        <?php echo $rU['catatan']; ?></td>	  	
      </tr>
	<?php if($rMC['jml_mc']>1) {?>
		  <?php for($x=1;$x<=$rMC['jml_mc']-1;$x++){ 
			 $noU=$x+1;

       $qryUrt1 = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
  id,
	max(lot) lot,
	max(no_mesin) no_mesin,
	max(no_urut) no_urut,
	max(buyer) buyer,
	max(langganan) langganan,
	max(no_order) no_order,
	max(nokk) nokk,
	max(jenis_kain) jenis_kain,
	max(warna) warna,
	max(no_warna) no_warna,
	max(rol) rol,
	max(bruto) bruto,
	max(proses) proses,
	max(ket_status) ket_status,
	max(CONVERT(VARCHAR(10),tgl_delivery)) tgl_delivery,
	max(ket_kain) ket_kain,
	max(mc_from) mc_from,
	max(catatan) catatan,
	max(personil) personil
FROM
	db_qc.tbl_schedule 
WHERE
	NOT STATUS = 'selesai'
	AND no_mesin='".$rowd['no_mesin']."'
	AND no_urut='$noU'
GROUP BY
	id
ORDER BY
	max(no_mesin),max(no_urut) ASC");
      $rUt = sqlsrv_fetch_array($qryUrt1,SQLSRV_FETCH_ASSOC);		  
		  ?>
    <tr>
      <td align="center" valign="top" style="height: 0.27in;"><?php echo $rUt['no_urut'];?></td>
      <td align="center" valign="top"><?php echo $rUt['langganan']; ?></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rUt['no_order']; ?></div></td>
      <td valign="top"><div style="font-size: 8px;">
        <?php if(strlen($rUt['jenis_kain'])>25){echo substr($rUt['jenis_kain'],0,25)."...";}else{echo $rUt['jenis_kain'];} ?>
      </div></td>
      <td align="center" valign="top"><div style="font-size: 8px;">
        <?php if(strlen($rUt['warna'])>25){echo substr($rUt['warna'],0,25)."...";}else{echo $rUt['warna'];} ?>
      </div></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rUt['no_warna']; ?></div></td>
      <td align="center" valign="top"><div style="font-size: 8px;"><?php echo $rUt['lot']; ?></div></td>
      <td align="center" valign="top"><?php if($rUt['tgl_delivery']!="0000-00-00"){echo $rUt['tgl_delivery'];} ?></td>
      <td align="center" valign="top"><?php if($rUt['rol']!="0"){echo $rUt['rol'];} ?></td>
      <td align="right" valign="top"><?php if($rUt['bruto']!="0"){echo $rUt['bruto'];} ?></td>
      <td valign="top"><?php echo $rUt['ket_status']; ?><br />
        <?php echo $rUt['personil']; ?><br />
        <?php echo $rUt['ket_kain']; ?>
        <?php if($rUt['mc_from']!=""){ echo " MC".$rUt['mc_from'];} ?>
        <br />
        <?php echo $rUt['catatan']; ?></td>	
      </tr>
    
		 <?php 
		 $totRol=$totRol+$rUt['rol'];
		 $totBruto=$totBruto+$rUt['bruto'];
		 	 
		 } ?>
	<?php } ?>	
		  
  <?php
	$totRol1=$totRol1+$rU['rol'];
	$totBruto1=$totBruto1+$rU['bruto'];  
	  $no++; } 
  ?>
    <?php $qryKk = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
   	COUNT(*) as jml_kk
FROM
	db_qc.tbl_schedule 
WHERE
	NOT STATUS = 'selesai'");
    $rKK = sqlsrv_fetch_array($qryKk,SQLSRV_FETCH_ASSOC); ?>
	<tr>
      <td valign="top" style="height: 0.27in;">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
	  <td valign="top">&nbsp;</td>	
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">&nbsp;</td>
      <td align="center" valign="top">Total</td>
      <td align="center" valign="top"><?php echo $totRol1+$totRol; ?></td>
      <td align="right" valign="top"><?php echo $totBruto1+$totBruto; ?></td>
      <td valign="top">Sisa Kartu Kerja <strong><?php echo $rKK['jml_kk']; ?></strong></td>
    </tr>	  
</table></td>
    </tr>
  
</table>
<!--<table width="99%" border="0">
  <tbody>
    <tr>
      <td width="73%" rowspan="4"><div style="font-size: 11px; font-family:sans-serif, Roman, serif;">
    <?Php $dtKet = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
	sum( CASE WHEN ket_status = 'Tolak Basah' THEN 1 ELSE 0 END  ) AS tolak_basah,
	sum( CASE WHEN ket_status = 'Gagal Proses' THEN 1 ELSE 0 END  ) AS gagal_proses,
	sum( CASE WHEN ket_status = 'Perbaikan' THEN 1 ELSE 0 END ) AS perbaikan,
	sum( CASE WHEN ket_status = 'Greige' OR ket_status = 'Salesmen Sample' OR ket_status = 'Development Sample' OR ket_status = 'Cuci Misty' OR ket_status = 'Cuci YD' THEN 1 ELSE 0 END ) AS greige,
	sum( CASE WHEN ket_status = 'Tolak Basah' THEN bruto ELSE 0 END ) AS tolak_basah_kg,
	sum( CASE WHEN ket_status = 'Gagal Proses' THEN bruto ELSE 0 END ) AS gagal_proses_kg,
	sum( CASE WHEN ket_status = 'Perbaikan' THEN bruto ELSE 0 END ) AS perbaikan_kg,
	sum( CASE WHEN ket_status = 'Greige' OR ket_status = 'Salesmen Sample' OR ket_status = 'Development Sample' OR ket_status = 'Cuci Misty' OR ket_status = 'Cuci YD' THEN bruto ELSE 0 END  ) AS greige_kg
FROM
	db_qc.tbl_schedule 
WHERE
	NOT STATUS = 'selesai'");
        $rKet = sqlsrv_fetch_array($dtKet,SQLSRV_FETCH_ASSOC); ?>
        Perbaikan: <?php echo $rKet['perbaikan']; ?> Lot &nbsp; <?php echo $rKet['perbaikan_kg']; ?> Kg<br />
        Gagal Proses : <?php echo $rKet['gagal_proses']; ?> Lot &nbsp; <?php echo $rKet['gagal_proses_kg']; ?> Kg<br />
		Greige : <?php echo $rKet['greige']; ?> Lot &nbsp; <?php echo $rKet['greige_kg']; ?> Kg<br />  
      Tolak Basah : <?php echo $rKet['tolak_basah']; ?> Lot &nbsp; <?php echo $rKet['tolak_basah_kg']; ?> Kg </div></td>
      <td width="20%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><pre></pre></td>
    </tr>
  </tbody>
</table>-->
<script>
//alert('cetak');window.print();
</script> 
</body>
</html>