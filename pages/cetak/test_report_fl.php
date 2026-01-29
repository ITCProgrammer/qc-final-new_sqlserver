<style>
body, table {
font-family: arial;
font-size: 10px;
}

.ttd {
border-collapse: collapse;
width: 100%;
}
.ttd, .ttd th, .ttd td {
border:	1px solid #ddd;
text-align: center
}

table {
  border-collapse: collapse;
    width: 100%;
}
table, th, td {
  border: 0px solid #ddd;
  text-align: left
}
.min_height_header {
	min-height: 100px;
}
</style>

<?php
$now = date("d-m-Y"); 
$ttd = '
<br><br>
<table  class="ttd">
	<tr>
		<td width="25%"></td>
		<td width="25%">Dibuat oleh :</td>
		<td width="25%">Diperiksa oleh :</td>
		<td width="25%">Disetujui oleh :</td>
	</tr>
	<tr>
		<td width="25%" style="text-align:left">Nama</td>
		<td width="25%">Kireyna :</td>
		<td width="25%">Vivik Kurniawati :</td>
		<td width="25%">Ferry Wibowo :</td>
	</tr>
	<tr>
		<td width="25%" style="text-align:left">Jabatan</td>
		<td width="25%">Operator :</td>
		<td width="25%">Asst. Supervisor :</td>
		<td width="25%">Asst. Manager :</td>
	</tr>
	<tr>
		<td width="25%" style="text-align:left">Tanggal</td>
		<td width="25%">'.$now.' :</td>
		<td width="25%">'.$now.' :</td>
		<td width="25%">'.$now.' :</td>
	</tr>
	<tr>
		<td width="25%" style="text-align:left">Tanda Tangan
		<br><br><br><br><br>
		</td>
		<td width="25%"></td>
		<td width="25%"></td>
		<td width="25%"></td>
	</tr>
</table>
<br><br>
'; ?>


<?php 
include"../../koneksi.php";
ini_set("error_reporting", 1);
session_start();

$status = $_GET['tabel'];
if ($status=='nokk') {
	$table_tq = 'db_qc.tbl_tq_nokk';
	$field  = 'nodemand';
	$tbl_tq_test = 'db_qc.tbl_tq_test';
	$tbl_tq_disptest = 'db_qc.tbl_tq_disptest';
} else {
	$table_tq = 'db_qc.tbl_tq_first_lot';
	$field  = 'no_report_fl';
	$tbl_tq_test = 'db_qc.tbl_tq_test_fl';
	$tbl_tq_disptest = 'db_qc.tbl_tq_disptest_fl';
}

$tbl_tq_randomtest =  'db_qc.tbl_tq_randomtest';

$id =  $_GET['id'];

// tq_first_lot
$tq_fl_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT TOP 1 *
     FROM $table_tq
     WHERE $field = '$id'
     ORDER BY id DESC"
);
$rcek = sqlsrv_fetch_array($tq_fl_sql, SQLSRV_FETCH_ASSOC);

$id =  $rcek['id'];

// tbl_tq_test_fl 
$tq_test_fl_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT TOP 1 *
     FROM $tbl_tq_test
     WHERE id_nokk = '$id'
     ORDER BY id DESC"
);
$rcek1 = sqlsrv_fetch_array($tq_test_fl_sql, SQLSRV_FETCH_ASSOC);

if ($rcek1==null) {
	echo '<div style="color:red">PHYSICAL, FUNCTIONAL & PH, COLORFASTNESS (tq_test/fl) NOT FOUND </div> ';
	exit;
}

// random test
$tq_randomtes_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT *
     FROM $tbl_tq_randomtest
     WHERE no_item = '".$rcek['no_item']."'
        OR no_hanger = '".$rcek['no_hanger']."'"
);
$rcekR = sqlsrv_fetch_array($tq_randomtes_sql, SQLSRV_FETCH_ASSOC);

$tq_diptest_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT TOP 1 *
     FROM $tbl_tq_disptest
     WHERE id_nokk = '".$rcek['id']."'
     ORDER BY id DESC"
);
$rcekD = sqlsrv_fetch_array($tq_diptest_sql, SQLSRV_FETCH_ASSOC);

$setup_report_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT *
     FROM db_qc.tbl_reportsetup_fl
     ORDER BY sort_id"
);
//$no_item 	  = 'PLF-11106';
$no_item 	  = $rcek['no_item'];
$standard_sql = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT *
     FROM db_qc.tbl_std_tq_fl
     WHERE no_item = '$no_item'"
);


$standard_data = sqlsrv_fetch_array($standard_sql, SQLSRV_FETCH_ASSOC);
if ($standard_data === null) {
    $standard_data = [];
}
?>


<div style="width: 100%; border: 0px solid black;">
	<div style="display: flex; align-items: center;">
		<div style="width: 5%;">
			<img width="50px" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRaMe3VM-CTQ_9WngsirowPGd1RRa5v6syvmfpBFBRiUg&s">
		</div>
		<div style="flex: 1; text-align: center;font-size:18">
			<div style="font-weight: bold;">
				INDO-TAICHEN TEXTILE INDUSTRY
			</div>
			<div style="font-weight: bold;">
				IN-HOUSE LABTEST
			</div>
			<div style="font-size: 10px;">
				FW-12-QCF-01A/02
			</div>
		</div>
	</div>
</div>

<br><br>



<b><u>TEST REPORT</u></b>
<br><br>
<div class="min_height_header">
<table  width=100%>
<tr>
<td width=60%  valign="top">
			<table>
			<tr>
			<td width=30%>GARMENT FACTORY</td>
			<td width=2%>:</td>
			<td><?=$rcek['pelanggan']?></td>
			</tr>
			<tr>
			<td valign="top">FABRIC TYPE</td>
			<td valign="top">:</td>
			<td valign="top"><?=$rcek['no_hanger']?>/<?=$rcek['jenis_kain']?>
			</td>
			</tr>
			<tr>
			<td>COLOUR </td>
			<td>:</td>	
			<td><?=$rcek['warna']?></td>
			</tr>
			<tr>
			<td>COLOR NO</td>
			<td>:</td>
			<td><?=$rcek['no_warna']?></td>
			</tr>
		</table>
</td>
<td width=5%> &nbsp;</td>
<td valign="top">
		<table>
			<tr>
			<td width=35%>DATE</td>
			<td width=4%>:</td>
			<td><?php if ($rcek['tgl_update']) { echo date('d-m-y', strtotime($rcek['tgl_update']));  }?></td>
			</tr>
			<tr>
			<td>P.O. NO.</td>
			<td>:</td>
			<td><?=$rcek['no_po']?></td>
			</tr>
			<tr>
			<td>ORDER NO.</td>
			<td>:</td>
			<td><?=$rcek['no_order']?></td>
			</tr>
			<tr>
			<td>WIDTH</td>
			<td>:</td>
			<td><?=$rcek['lebar']?></td>
			</tr>
			<tr>
			<td>WEIGHT</td>
			<td>:</td>
			<td><?=$rcek['gramasi']?></td>
			</tr>
			<tr>
			<td valign="top">LOT/NO PROD. ORDER</td>
			<td valign="top">:</td>
			<td valign="top"><?=$rcek['lot']?></td>
			</tr>
		</table>
	
</td>
</tr>
</table>
</div>

<br><br>
<?php $width_column = '12%';

?>

<table width=100%>
	<tr>
		
		<td><b>FABRIC PROPERTIES : </b>	</td>
		<td style="text-align:center" width="<?=$width_column?>"></td>
		<td style="text-align:center" width="<?=$width_column?>"><u><i>STANDARD</i></u></td>
		<td style="text-align:center" width="<?=$width_column?>"></td>
		<td style="text-align:center" width="<?=$width_column?>"><u>RESULT</u></td>
		<td style="text-align:center"  width="<?=$width_column?>"><u>REMARKS</u></td>
	</tr>
	<?php 
		while($row=sqlsrv_fetch_array($setup_report_sql)){
		if ($row['style_header']=='1') { ?>
			<tr>				
					<td> </td>
					<td style="text-align:center"><?=$row['standard1']?></td>
					<td style="text-align:center"><?=$row['standard2']?> </td>
					<td style="text-align:center"><?=$row['result1']?></td>
					<td style="text-align:center"><?=$row['result2']?></td>		
			</tr>
		<?php } 
		else if ($row['style_formula']=='1') { echo FORMULA($row['properties']); ?>
			

		<?php }
			else {
	?>
		<tr>
			<td><?=$row['properties']?></td>
			<td style="text-align:center"><?php if (array_key_exists($row['standard1'],$standard_data)) { echo $standard_data[ $row['standard1'] ];} ?> </td>
			<td style="text-align:center" ><?php if (array_key_exists($row['standard2'],$standard_data)) { echo $standard_data[ $row['standard2'] ];} ?></td>
			<td style="text-align:center"><?php if (array_key_exists($row['result1'],$rcek1)) { echo $rcek1[ $row['result1'] ];} ?></td>
			<td style="text-align:center"><?php if (array_key_exists($row['result2'],$rcek1)) { echo $rcek1[ $row['result2'] ];} ?></td>
			<td style="text-align:center"><?php if (array_key_exists($row['remarks'],$rcek1)) { echo $rcek1[ $row['remarks'] ];} ?></td>
		</tr>
	<?php
			if ($row['style_line_height']=='1') {	
				echo '<tr><td colspan=6>&nbsp;</td></tr>';
			} 

			if ($row['style_signature']=='1') {	 ?>
				<tr>
						<td colspan=6><?php  echo $ttd?>
						</td>
				</tr>
			<?php } 
		}
	} 
	 ?>
	
</table>

<?php 

function FORMULA($properties) { ?>
<?php 
	global $rcek1;
	global $standard_data;
	if ($properties == 'FORMULA1') {
		$row  = '<tr>				
					<td >'.$rcek1['fc_cott1'].'</td>
					<td></td>
					<td style="text-align:center">'.$standard_data['fibercontent1'].'</td>
					<td></td>
					<td style="text-align:center">'.$rcek1['fc_cott'].'</td>
					<td style="text-align:center">'.$rcek1['stat_fib'].'</td>
				</tr>
				<tr>				
					<td >'.$rcek1['fc_poly1'].'</td>
					<td></td>
					<td style="text-align:center">'.$standard_data['fibercontent2'].'</td>
					<td></td>
					<td style="text-align:center">'.$rcek1['fc_poly'].'</td>
					<td style="text-align:center">'.$rcek1['stat_fib'].'</td>
				</tr>
				';
		return $row ; 
	} else {
		//return 2 ; 
	}
}

/*
function abc() {
	global $x ; 
	return $x ; 
}

echo abc();
$x = '9999999999';
*/
?>





































