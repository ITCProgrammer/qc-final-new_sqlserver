<?php
ini_set("error_reporting", 1);
session_start();
include "koneksi.php";

$qryCek = sqlsrv_query($con_db_qc_sqlsrv, "SELECT * FROM db_qc.tbl_aftersales_now WHERE id='$_GET[id]'");
$rCek = sqlsrv_fetch_array($qryCek, SQLSRV_FETCH_ASSOC);
?>
<?php
function no_urut($x)
{
	include "koneksi.php";
	date_default_timezone_set("Asia/Jakarta");
	if ($x == "Reject Buyer") {
		$fk = "RB";
	} else if ($x == "Kurang Qty") {
		$fk = "GK";
	}
	$format = $fk . date("y/m/");

	$sql = sqlsrv_query(
		$con_db_qc_sqlsrv,
		"SELECT TOP 1 no_bon
         FROM db_qc.tbl_ganti_kain_now
         WHERE SUBSTRING(no_bon,1,10) LIKE '" . $format . "%'
         ORDER BY no_bon DESC",
		[],
		["Scrollable" => SQLSRV_CURSOR_KEYSET]
	) or die(print_r(sqlsrv_errors(), true));

	$d = sqlsrv_num_rows($sql);
	if ($d > 0) {
		$r = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC);
		$d = $r['no_bon'];
		$str = substr($d, 8, 3);
		$Urut = (int) $str;
	} else {
		$Urut = 0;
	}
	$Urut = $Urut + 1;
	$Nol = "";
	$nilai = 3 - strlen($Urut);
	for ($i = 1; $i <= $nilai; $i++) {
		$Nol = $Nol . "0";
	}
	$nipbr = $format . $Nol . $Urut;
	return $nipbr;
}

date_default_timezone_set("Asia/Jakarta");
//Baca Tanggal Hari ini
$today = date("Y-m-d");

$sql = "SELECT max(kd_ganti) AS kd_ganti FROM db_qc.tbl_ganti_kain_now WHERE no_order='$rCek[no_order]'";
$query = sqlsrv_query($con_db_qc_sqlsrv, $sql, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]) or die(print_r(sqlsrv_errors(), true));

$cno = sqlsrv_num_rows($query);
if ($cno > 0) {
	$gno = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC);
	$text = $gno['kd_ganti'];
	if (strpos($text, 'GR') !== false) {
		$nilai = substr($text, 2, 2);
	} else {
		$nilai = substr($text, 1, 2);
	}
	$kode = (int) $nilai;
} else {
	$kode = 0;
}
//tambahkan sebanyak + 1
$kode = $kode + 1;
if ($_POST['alasan'] == "Reject Buyer") {
	$auto_kode = "GR" . $kode;
} else if ($_POST['alasan'] == "Kurang Qty") {
	$auto_kode = "G" . $kode;
} else {
	$auto_kode = "G1";
}
$alsn = isset($_POST['alasan']) ? $_POST['alasan'] : '';
$W1 = isset($_POST['warna1']) ? $_POST['warna1'] : '';
$W2 = isset($_POST['warna2']) ? $_POST['warna2'] : '';
$W3 = isset($_POST['warna3']) ? $_POST['warna3'] : '';
$posW1 = strpos($_POST['warna1'], ',');
$WRN1 = substr($_POST['warna1'], 0, $posW1);
$WN1 = str_replace("'", "''", $WRN1);
$posW2 = strpos($_POST['warna2'], ',');
$WRN2 = substr($_POST['warna2'], 0, $posW2);
$WN2 = str_replace("'", "''", $WRN2);
$posW3 = strpos($_POST['warna3'], ',');
$WRN3 = substr($_POST['warna3'], 0, $posW3);
$WN3 = str_replace("'", "''", $WRN3);

$yard1 = isset($_POST['pjg1']) ? $_POST['pjg1'] : '';
$yard2 = isset($_POST['pjg2']) ? $_POST['pjg2'] : '';
$yard3 = isset($_POST['pjg3']) ? $_POST['pjg3'] : '';
$qty_email = isset($_POST['qty_email']) ? $_POST['qty_email'] : '';
$pjg_email = isset($_POST['pjg_email']) ? $_POST['pjg_email'] : '';

$posLot1 = substr($_POST['warna1'], $posW1 + 1, 20);
$L1 = str_replace("'", "''", $posLot1);
$posLot2 = substr($_POST['warna2'], $posW2 + 1, 20);
$L2 = str_replace("'", "''", $posLot2);
$posLot3 = substr($_POST['warna3'], $posW3 + 1, 20);
$L3 = str_replace("'", "''", $posLot3);
$sat1 = isset($_POST['satuan1']) ? $_POST['satuan1'] : '';
$sat2 = isset($_POST['satuan2']) ? $_POST['satuan2'] : '';
$sat3 = isset($_POST['satuan3']) ? $_POST['satuan3'] : '';
$sat_email = isset($_POST['satuan_email']) ? $_POST['satuan_email'] : '';

// ganti LIKE '$today%' -> range 1 hari (aman untuk datetime/varchar ISO)
$todayStart = $today . " 00:00:00";

$qryg = sqlsrv_query(
	$con_db_qc_sqlsrv,
	"SELECT TOP 1 kd_ganti
     FROM db_qc.tbl_ganti_kain_now
     WHERE no_order='$rCek[no_order]' AND alasan='$_POST[alasan]'
       AND tgl_buat >= '$todayStart'
       AND tgl_buat <  DATEADD(day,1,'$todayStart')",
	[],
	["Scrollable" => SQLSRV_CURSOR_KEYSET]
);
$cekg = sqlsrv_num_rows($qryg);
$rg = sqlsrv_fetch_array($qryg, SQLSRV_FETCH_ASSOC);

$qryb = sqlsrv_query(
	$con_db_qc_sqlsrv,
	"SELECT TOP 1 no_bon
     FROM db_qc.tbl_ganti_kain_now
     WHERE id_nsp='$_GET[id]' AND alasan='$_POST[alasan]'
       AND tgl_buat >= '$todayStart'
       AND tgl_buat <  DATEADD(day,1,'$todayStart')",
	[],
	["Scrollable" => SQLSRV_CURSOR_KEYSET]
);
$cekb = sqlsrv_num_rows($qryb);
$rb = sqlsrv_fetch_array($qryb, SQLSRV_FETCH_ASSOC);

$qryd1 = sqlsrv_query(
	$con_db_qc_sqlsrv,
	"SELECT TOP 1 *
     FROM db_qc.tbl_aftersales_now
     WHERE warna='$WN1' and lot='$L1' and no_order='$rCek[no_order]'
       and no_hanger='$rCek[no_hanger]' AND po='$rCek[po]'
     ORDER BY id DESC"
);
$rowd1 = sqlsrv_fetch_array($qryd1, SQLSRV_FETCH_ASSOC);

$qryr1 = sqlsrv_query($con_db_qc_sqlsrv, "SELECT * FROM db_qc.tbl_aftersales_now WHERE id='$_GET[id]'");
$r1 = sqlsrv_fetch_array($qryr1, SQLSRV_FETCH_ASSOC);

if (isset($_POST['save'])) {

	$bon = no_urut($_POST['alasan']);
	$order = $r1['no_order'];
	$noorder1 = str_replace("/", "&", $r1['no_order']);
	if ($cekg > 0) {
		$kdganti = $rg['kd_ganti'];
	} else {
		$kdganti = $auto_kode;
	}
	if ($cekb > 0) {
		$kdbon = $rb['no_bon'];
	} else {
		$kdbon = $bon;
	}
	$pos = strpos($r1['langganan'], "/");
	$posbuyer = substr($r1['langganan'], $pos + 1, 50);
	$buyer = str_replace("'", "''", $posbuyer);
	$analisa = str_replace("'", "''", $_POST['analisa']);
	$pencegahan = str_replace("'", "''", $_POST['pencegahan']);
	$alasan = str_replace("'", "''", $_POST['alasan']);
	$pwar1 = strpos($_POST['warna1'], ',');
	$pwar2 = strpos($_POST['warna2'], ',');
	$pwar3 = strpos($_POST['warna3'], ',');
	$potW1 = substr($_POST['warna1'], 0, $pwar1);
	$potW2 = substr($_POST['warna2'], 0, $pwar2);
	$potW3 = substr($_POST['warna3'], 0, $pwar3);

	$demand1 = str_replace("'", "''", $_POST['nodemand1']);
	$demand2 = str_replace("'", "''", $_POST['nodemand2']);
	$demand3 = str_replace("'", "''", $_POST['nodemand3']);
	$warna1 = str_replace("'", "''", $potW1);
	$warna2 = str_replace("'", "''", $potW2);
	$warna3 = str_replace("'", "''", $potW3);
	$kg1 = str_replace("'", "''", $_POST['kg1']);
	$kg2 = str_replace("'", "''", $_POST['kg2']);
	$kg3 = str_replace("'", "''", $_POST['kg3']);
	$pjg1 = str_replace("'", "''", $_POST['pjg1']);
	$pjg2 = str_replace("'", "''", $_POST['pjg2']);
	$pjg3 = str_replace("'", "''", $_POST['pjg3']);
	$satuan1 = str_replace("'", "''", $_POST['satuan1']);
	$satuan2 = str_replace("'", "''", $_POST['satuan2']);
	$satuan3 = str_replace("'", "''", $_POST['satuan3']);

	$potL1 = substr($_POST['warna1'], $pwar1 + 1, 20);
	$potL2 = substr($_POST['warna2'], $pwar2 + 1, 20);
	$potL3 = substr($_POST['warna3'], $pwar3 + 1, 20);
	$lot1 = str_replace("'", "''", $potL1);
	$lot2 = str_replace("'", "''", $potL2);
	$lot3 = str_replace("'", "''", $potL3);

	$langganan = str_replace("'", "''", $r1['langganan']);
	$masalah = str_replace("'", "''", $r1['masalah']);
	$no_po = str_replace("'", "''", $rowd1['po']);
	$no_item = str_replace("'", "''", $rowd1['no_item']);
	$no_hanger = str_replace("'", "''", $rowd1['no_hanger']);
	$jenis_kain = str_replace("'", "''", $rowd1['jenis_kain']);
	$lebar = str_replace("'", "''", $rowd1['lebar']);
	$gramasi = str_replace("'", "''", $rowd1['gramasi']);
	$warna = str_replace("'", "''", $rowd1['warna']);
	$no_warna = str_replace("'", "''", $rowd1['no_warna']);
	$qty_order = str_replace("'", "''", $rowd1['qty_order']);
	$qty_kirim = str_replace("'", "''", $rowd1['qty_kirim']);
	$qty_foc = str_replace("'", "''", $rowd1['qty_foc']);
	$qty_claim = str_replace("'", "''", $rowd1['qty_claim']);
	$t_jawab = str_replace("'", "''", $rowd1['t_jawab']);
	$t_jawab1 = str_replace("'", "''", $rowd1['t_jawab1']);
	$t_jawab2 = str_replace("'", "''", $rowd1['t_jawab2']);
	$persen = str_replace("'", "''", $rowd1['persen']);
	$persen1 = str_replace("'", "''", $rowd1['persen1']);
	$persen2 = str_replace("'", "''", $rowd1['persen2']);
	$styl = str_replace("'", "''", $r1['styl']);
	$satuan_o = str_replace("'", "''", $r1['satuan_o']);
	$satuan_k = str_replace("'", "''", $r1['satuan_k']);
	$satuan_f = str_replace("'", "''", $r1['satuan_f']);
	$satuan_c = str_replace("'", "''", $r1['satuan_c']);
	$qty_email = str_replace("'", "''", $_POST['qty_email']);
	$pjg_email = str_replace("'", "''", $_POST['pjg_email']);
	$satuan_email = str_replace("'", "''", $_POST['satuan_email']);
	$sub_defect = str_replace("'", "''", $_POST['sub_defect']);
	$solusi = str_replace("'", "''", $_POST['solusi']);

	
	$qry1 = sqlsrv_query($con_db_qc_sqlsrv, "
        INSERT INTO db_qc.tbl_ganti_kain_now (
            id_nsp,buyer,kd_ganti,no_bon,no_order,alasan,analisa,pencegahan,
            nodemand1,nodemand2,nodemand3,
            warna1,warna2,warna3,
            kg1,kg2,kg3,
            pjg1,pjg2,pjg3,
            satuan1,satuan2,satuan3,
            lot1,lot2,lot3,
            langganan,masalah,no_po,no_item,no_hanger,jenis_kain,lebar,gramasi,warna,no_warna,
            qty_order,qty_foc,qty_kirim,qty_claim,
            t_jawab,t_jawab1,t_jawab2,
            persen,persen1,persen2,
            styl,satuan_o,satuan_k,satuan_f,satuan_c,
            qty_email,pjg_email,satuan_email,
            sub_defect,solusi,
            tgl_buat,tgl_update
        ) VALUES (
            '$_GET[id]','$buyer','$kdganti','$kdbon','$order','$alasan','$analisa','$pencegahan',
            '$demand1','$demand2','$demand3',
            '$warna1','$warna2','$warna3',
            '$kg1','$kg2','$kg3',
            '$pjg1','$pjg2','$pjg3',
            '$satuan1','$satuan2','$satuan3',
            '$lot1','$lot2','$lot3',
            '$langganan','$masalah','$no_po','$no_item','$no_hanger','$jenis_kain','$lebar','$gramasi','$warna','$no_warna',
            '$qty_order','$qty_foc','$qty_kirim','$qty_claim',
            '$t_jawab','$t_jawab1','$t_jawab2',
            '$persen','$persen1','$persen2',
            '$styl','$satuan_o','$satuan_k','$satuan_f','$satuan_c',
            '$qty_email','$pjg_email','$satuan_email',
            '$sub_defect','$solusi',
            GETDATE(),GETDATE()
        )
    ");

	if ($qry1) {
		echo "<script>swal({
        title: 'Data Telah diSimpan',
        text: 'Klik Ok untuk input data kembali',
        type: 'success',
        }).then((result) => {
        if (result.value) {
            window.location.href='TambahBon-$_GET[id]-$noorder1';
        }
        });</script>";
	} else {
		die(print_r(sqlsrv_errors(), true));
	}
}
?>

<!-- <p>Ini adalah WARNA : <?php echo $rCek['warna'] ?></p>
					<p>Ini adalah LOT : <?php echo $rCek['lot'] ?></p>
					<p>Ini adalah NO ORDER : <?php echo $rCek['no_order'] ?></p>
					<p>Ini adalah NO HANGER : <?php echo $rCek['no_hanger'] ?></p>
					<p>Ini adalah NO PO : <?php echo $rCek['po'] ?></p> -->
<div class="box box-info">
	<form class="form-horizontal" action="" method="post" enctype="multipart/form-data" name="form1">
		<div class="box-header with-border">
			<h3 class="box-title">Formulir Ganti Kain</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">
			<div class="form-group">
				<label for="alasan" class="col-sm-2 control-label">Alasan</label>
				<div class="col-sm-3">
					<select class="form-control select2" name="alasan" required>
						<option value="">Pilih</option>
						<option <?php if ($_POST['alasan'] == "Reject Buyer") { ?> selected=selected <?php }
						; ?>value="Reject Buyer">Reject Buyer</option>
						<option <?php if ($_POST['alasan'] == "Kurang Qty") { ?> selected=selected <?php }
						; ?>value="Kurang Qty">Kurang Qty</option>
					</select>
				</div>
				<label for="qty_email" class="col-sm-2 control-label">Qty Permintaan Sesuai Email </label>
				<div class="col-sm-2">
					<div class="input-group">
						<?php
						$qryw1 = sqlsrv_query($con_db_qc_sqlsrv, "SELECT qty_claim,nodemand FROM db_qc.tbl_aftersales_now WHERE no_order='$rCek[no_order]' and no_hanger='$rCek[no_hanger]' and po='$rCek[po]'");
						$rowW1 = sqlsrv_fetch_array($qryw1, SQLSRV_FETCH_ASSOC);
						?>
						<input name="qty_email" type="text" class="form-control" id="qty_email" value="<?php echo $rowW1['qty_claim']; ?>" placeholder="0.00" style="text-align: right;">
						<span class="input-group-addon">Kg</span>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="input-group">
						<input name="pjg_email" type="text" class="form-control" id="pjg_email" value="<?php if ($pjg_email != "") {
							echo $pjg_email;
						} else {
							echo $rcek['pjg_email'];
						} ?>" placeholder="0.00" style="text-align: right;" required>
						<span class="input-group-addon">
							<select name="satuan_email" style="font-size: 12px;" id="satuan_email">
								<option value="Yard" <?php if ($rcek['satuan_email'] == "Yard") {
									echo "SELECTED";
								} ?>>Yard</option>
								<option value="Meter" <?php if ($rcek['satuan_email'] == "Meter") {
									echo "SELECTED";
								} ?>>Meter</option>
								<option value="PCS" <?php if ($rcek['satuan_email'] == "PCS") {
									echo "SELECTED";
								} ?>>PCS</option>
								<option value="<?php echo $sat_email; ?>" <?php if ($sat_email != "") {
									  echo "SELECTED";
								  } ?>><?php echo $sat_email; ?></option>
							</select>
						</span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="warna1" class="col-sm-2 control-label">Warna / Lot </label>
				<div class="col-sm-3">
					<select class="form-control select2" name="warna1" required>
						<option value="">Pilih</option>
						<?php
						// MySQL GROUP BY permisif -> SQL Server harus group semua kolom yang dipilih
						$sqlw1 = sqlsrv_query($con_db_qc_sqlsrv, "
                            SELECT warna,lot
                            FROM db_qc.tbl_aftersales_now
                            WHERE no_order='$rCek[no_order]' and no_hanger='$rCek[no_hanger]' and po='$rCek[po]'
                            GROUP BY po, no_order, no_hanger, warna, lot
                            ORDER BY warna
                        ");
						while ($rwarna = sqlsrv_fetch_array($sqlw1, SQLSRV_FETCH_ASSOC)) {
							?>
								<option value="<?php echo $rwarna['warna'] . "," . $rwarna['lot']; ?>"><?php echo $rwarna['warna'] . " / " . $rwarna['lot']; ?></option>
						<?php } ?>
						<option <?php if ($WN1 != "") { ?> selected=selected <?php }
						; ?>value="<?php echo $WN1 . "," . $L1; ?>"><?php echo $WN1 . " / " . $L1; ?></option>
					</select>
				</div>
				<label for="warna1" class="col-sm-2 control-label">Qty Setelah Negosiasi </label>
				<div class="col-sm-2">
					<div class="input-group">
						<input name="kg1" type="text" class="form-control" id="kg1" value="" placeholder="0.00" style="text-align: right;" required>
						<span class="input-group-addon">Kg</span>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="input-group">
						<input name="pjg1" type="text" class="form-control" id="pjg1" value="<?php if ($yard1 != "") {
							echo $yard1;
						} else {
							echo $rcek['pjg1'];
						} ?>" placeholder="0.00" style="text-align: right;" required>
						<span class="input-group-addon">
							<select name="satuan1" style="font-size: 12px;" id="satuan1">
								<option value="Yard" <?php if ($rcek['satuan1'] == "Yard") {
									echo "SELECTED";
								} ?>>Yard</option>
								<option value="Meter" <?php if ($rcek['satuan1'] == "Meter") {
									echo "SELECTED";
								} ?>>Meter</option>
								<option value="PCS" <?php if ($rcek['satuan1'] == "PCS") {
									echo "SELECTED";
								} ?>>PCS</option>
								<option value="<?php echo $sat1; ?>" <?php if ($sat1 != "") {
									  echo "SELECTED";
								  } ?>><?php echo $sat1; ?></option>
							</select>
						</span>
					</div>
				</div>
				<div class="col-sm-2">
					<input name="nodemand1" type="hidden" class="form-control" id="nodemand1" value="<?php echo $rowW1['nodemand']; ?>" placeholder="" readonly>
				</div>
			</div>
			<div class="form-group">
				<label for="sub_defect" class="col-sm-2 control-label">Sub Defect</label>
				<div class="col-sm-3">
					<select class="form-control select2" name="sub_defect" id="sub_defect">
						<option value="">Pilih</option>
						<?php
						$qrym = sqlsrv_query($con_db_qc_sqlsrv, "SELECT masalah FROM db_qc.tbl_masalah_aftersales ORDER BY masalah ASC");
						while ($rm = sqlsrv_fetch_array($qrym, SQLSRV_FETCH_ASSOC)) {
							?>
								<option value="<?php echo $rm['masalah']; ?>" <?php if ($rcek['sub_defect'] == $rm['masalah']) {
									  echo "SELECTED";
								  } ?>><?php echo $rm['masalah']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="sub_defect" class="col-sm-2 control-label">Solusi</label>
				<div class="col-sm-3">
					<select class="form-control select2" name="solusi" id="solusi">
						<option value="">Pilih</option>
						<?php
						$qrys = sqlsrv_query($con_db_qc_sqlsrv, "SELECT solusi FROM db_qc.tbl_solusi ORDER BY solusi ASC");
						while ($rs = sqlsrv_fetch_array($qrys, SQLSRV_FETCH_ASSOC)) {
							?>
								<option value="<?php echo $rs['solusi']; ?>" <?php if ($rCek['solusi'] == $rs['solusi']) {
									   echo "SELECTED";
								   } ?>>
									<?php echo $rs['solusi']; ?>
								</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
		<!-- /.box-footer -->
		<div class="box-footer">
			<input type="submit" value="Simpan" name="save" id="save" class="btn btn-primary pull-right">
			<button type="submit" value="cari" class="btn btn-danger">Cari Data</button>
		</div>
	</form>
</div>

<div class="row">
	<div class="col-xs-12">
		<div class="box">
		<form class="form-horizontal" action="" method="post" enctype="multipart/form-data" name="form2">
			<div class="box-header with-border">
					<div class="pull-right" >
						<input type="submit" value="Pilih Cetak" name="cari" id="cari" class="btn btn-primary pull-left"/>
						<a href="pages/cetak/cetak_bon_ganti.php?no_bon=<?php echo $r['no_bon'] ?>&id_nsp=<?php echo $_GET['id']; ?>&no_order=<?php echo $rCek['no_order']; ?>&po=<?php echo $rCek['po']; ?>&id_cek=<?php echo $_POST['cek'][0]; ?>&id_cek1=<?php echo $_POST['cek'][1]; ?>&id_cek2=<?php echo $_POST['cek'][2]; ?>" class="btn btn-info cetak <?php if ($_POST['cek'][0] == "") {
											echo "disabled";
										} ?>" target="_blank" >Cetak Bon </a>
					</div>
			</div>
			<div class="box-body">
				<table id="example3" class="table table-bordered table-hover table-striped nowrap" width="100%">
					<thead class="bg-green">
					<tr>
						<th width="48"><div align="center">No</div></th>
						<th width="60"><div align="center">Tgl Buat</div></th>
						<th width="301"><div align="center">Alasan</div></th>
						<th width="343"><div align="center">Analisa</div></th>
						<th width="331"><div align="center">Pencegahan</div></th>
						<th width="331"><div align="center">Sub Defect</div></th>
						<th width="331"><div align="center">Warna</div></th>
						<th width="331"><div align="center">Qty Email</div></th>
						<th width="331"><div align="center">Qty Nego</div></th>
						<th width="331"><div align="center">Aksi</div></th>
					</tr>
					</thead>
				<tbody>
					<?php
					$sql = sqlsrv_query($con_db_qc_sqlsrv, "SELECT * FROM db_qc.tbl_ganti_kain_now WHERE id_nsp='$_GET[id]' ORDER BY tgl_buat ASC");
					while ($r = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC)) {

						$no++;
						$bgcolor = ($col++ & 1) ? 'gainsboro' : 'antiquewhite';
						?>
							<tr bgcolor="<?php echo $bgcolor; ?>">
								<td align="center"><a href="#" class="edit_bon" id="<?php echo $r['id'] ?>"><?php echo $no; ?></a><br><input type="checkbox" name="cek[]" value="<?php echo $r['id']; ?>"
									<?php if (isset($_POST['cek']) && in_array($r['id'], $_POST['cek'])): ?>
										checked="checked"
									<?php endif; ?>/></td>
								<td align="center">
									<?php
										$tgl = $r['tgl_buat'] ?? null;

										if ($tgl instanceof DateTime) {
											echo $tgl->format('Y-m-d');      // atau 'd-m-Y H:i:s'
										} else {
											echo $tgl; // kalau sudah string
										}
									?>
								</td>
								<td align="center"><?php echo $r['alasan']; ?></td>
								<td align="left"><?php echo $r['analisa']; ?></td>
								<td align="left"><?php echo $r['pencegahan']; ?></td>
								<td align="left"><?php echo $r['sub_defect']; ?></td>
								<td align="left" valign="top"><?php if ($r['warna1'] != "") {
									echo $r['warna1'];
								} ?></td>
								<td align="right"><?php if ($r['qty_email'] > 0) {
									echo $r['qty_email'] . " Kg " . $r['pjg_email'] . " " . $r['satuan_email'];
								} ?></td>
								<td align="right"><?php if ($r['kg1'] > 0) {
									echo $r['kg1'] . " Kg " . $r['pjg1'] . " " . $r['satuan1'];
								} ?></td>
								<td align="center"><div class="btn-group">
								<a href="#" class="btn btn-danger btn-xs <?php if ($_SESSION['akses'] == 'biasa') {
									echo "disabled";
								} ?>" onclick="confirm_delete('./HapusDataBON-<?php echo $r['id'] ?>');"><i class="fa fa-trash"></i> </a></div></td>
							</tr>
						<?php
						$tpersen += $r['persen'];
					}
					?>
				</tbody>
				</table>
					<div id="KodeEdit" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					</div>
					<div id="PersenEdit" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					</div>
					<div id="EditBon" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					</div>
			</div>
			</form>
		</div>
	</div>

<div class="modal fade" id="modal_del" tabindex="-1" >
	<div class="modal-dialog modal-sm" >
		<div class="modal-content" style="margin-top:100px;">
			<div class="modal-header">
				<button type="button" class="close"  data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" style="text-align:center;">Are you sure to delete all data ?</h4>
			</div>

			<div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
				<a href="#" class="btn btn-danger" id="delete_link">Delete</a>
				<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	function confirm_delete(delete_url)
	{
	  $('#modal_del').modal('show', {backdrop: 'static'});
	  document.getElementById('delete_link').setAttribute('href' , delete_url);
	}
</script>
