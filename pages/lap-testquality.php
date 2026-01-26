<?PHP
ini_set("error_reporting", 1);
session_start();
include "koneksi.php";

?>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Laporan Test Quality</title>

</head>

<body>
<?php
$Awal  = isset($_POST['awal']) ? $_POST['awal'] : '';
$Akhir = isset($_POST['akhir']) ? $_POST['akhir'] : '';
$Dept  = isset($_POST['dept']) ? $_POST['dept'] : '';
$Shift = isset($_POST['shift']) ? $_POST['shift'] : '';
$GShift= isset($_POST['gshift']) ? $_POST['gshift'] : '';
$Proses= isset($_POST['proses']) ? $_POST['proses'] : '';
$jamA  = isset($_POST['jam_awal']) ? $_POST['jam_awal'] : '';
$jamAr = isset($_POST['jam_akhir']) ? $_POST['jam_akhir'] : '';
$Buyer = isset($_POST['buyer']) ? $_POST['buyer'] : '';
$Item  = isset($_POST['no_item']) ? $_POST['no_item'] : '';

// ==== hanya tangani tanggal saja ====
// kalau tanggal kosong (menu pertama dibuka), set aman biar SQL Server gak error
if ($Awal == '' || $Akhir == '') {
    $start_date = '1900-01-01 00:00:00';
    $stop_date  = '1900-01-01 00:00:00';
} else {
    // rapikan jam: kalau kosong -> default, kalau HH:MM -> tambah :00
    $jamA  = trim($jamA);
    $jamAr = trim($jamAr);

    if ($jamA == '')  $jamA  = '00:00:00';
    if ($jamAr == '') $jamAr = '23:59:59';

    if (strlen($jamA) == 5)  $jamA  .= ':00';   // HH:MM -> HH:MM:00
    if (strlen($jamAr) == 5) $jamAr .= ':00';   // HH:MM -> HH:MM:00

    // pastikan format jam valid, kalau tidak valid fallback default
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jamA))  $jamA  = '00:00:00';
    if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $jamAr)) $jamAr = '23:59:59';

    $start_date = trim($Awal)  . ' ' . $jamA;
    $stop_date  = trim($Akhir) . ' ' . $jamAr;
}
?>

	<div class="row">
		<div class="col-xs-4">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"> Filter Tanggal Test Quality</h3>

				</div>
				<!-- /.box-header -->
				<!-- form start -->
				<form method="post" enctype="multipart/form-data" name="form1" class="form-horizontal" id="form1">
					<div class="box-body">
						<div class="form-group">
							<div class="col-sm-5">
								<div class="input-group date">
									<div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
									<input name="awal" type="text" class="form-control pull-right" id="datepicker"
										placeholder="Tanggal Awal" value="<?php echo $Awal; ?>" autocomplete="off" />
								</div>
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control timepicker" name="jam_awal"
										placeholder="00:00" value="<?php echo $jamA; ?>" autocomplete="off">
									<div class="input-group-addon">
										<i class="fa fa-clock-o"></i>
									</div>
								</div>
								<div>
								</div>
							</div>
							<!-- /.input group -->
						</div>

						<div class="form-group">
							<div class="col-sm-5">
								<div class="input-group date">
									<div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
									<input name="akhir" type="text" class="form-control pull-right" id="datepicker1"
										placeholder="Tanggal Akhir" value="<?php echo $Akhir; ?>" autocomplete="off" />
								</div>
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<input type="text" class="form-control timepicker" name="jam_akhir"
										placeholder="00:00" value="<?php echo $jamAr; ?>" autocomplete="off">
									<div class="input-group-addon">
										<i class="fa fa-clock-o"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-3">
								<select class="form-control select2" name="shift" id="shift">
									<option value="">Pilih</option>
									<option value="ALL" <?php if ($Shift == "ALL") {
										echo "SELECTED";
									} ?>>ALL</option>
									<option value="1" <?php if ($Shift == "1") {
										echo "SELECTED";
									} ?>>1</option>
									<option value="2" <?php if ($Shift == "2") {
										echo "SELECTED";
									} ?>>2</option>
									<option value="3" <?php if ($Shift == "3") {
										echo "SELECTED";
									} ?>>3</option>
									<option value="NON" <?php if ($Shift == "NON") {
										echo "SELECTED";
									} ?>>
										Non-Shift</option>
								</select>
							</div>
							<div class="col-sm-3">
								<select class="form-control select2" name="gshift" id="gshift">
									<option value="">Pilih</option>
									<option value="ALL" <?php if ($GShift == "ALL") {
										echo "SELECTED";
									} ?>>ALL</option>
									<option value="A" <?php if ($GShift == "A") {
										echo "SELECTED";
									} ?>>A</option>
									<option value="B" <?php if ($GShift == "B") {
										echo "SELECTED";
									} ?>>B</option>
									<option value="C" <?php if ($GShift == "C") {
										echo "SELECTED";
									} ?>>C</option>
									<option value="NON" <?php if ($GShift == "NON") {
										echo "SELECTED";
									} ?>>
										Non-Shift</option>
								</select>
							</div>
						</div>
						<!-- <div class="form-group">
							<div class="col-sm-6">
								<select name="buyer" class="form-control select2" id="buyer" style="width: 100%">
									<option value="">Pilih</option>
									<?php
									$sqlBuyer = sqlsrv_query($con_db_qc_sqlsrv, "SELECT buyer FROM db_qc.tbl_schedule  GROUP BY buyer");
									while ($rBy = sqlsrv_fetch_array($sqlBuyer)) {
										?>
										<option value="<?php echo $rBy['buyer']; ?>" <?php if ($Buyer == $rBy['buyer']) {
											   echo "SELECTED";
										   } ?>>
											<?php echo $rBy['buyer']; ?>
										</option>
									<?php } ?>
								</select>
							</div>
						</div> -->
					</div>
					<!-- /.box-body -->
					<div class="box-footer">
						<div class="col-sm-2">
							<button type="submit" class="btn btn-social btn-linkedin btn-sm" name="save">Search <i
									class="fa fa-search"></i></button>
						</div>
					</div>
					<!-- /.box-footer -->
				</form>
			</div>
		</div>
		<div class="col-xs-8">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"> Rangkuman Test Quality</h3>
					<?php if ($Awal != "") { ?>
						<!-- <div class="pull-right">
							<a href="pages/cetak/excel-rangkuman-inspeksi.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&dept=<?php echo $_POST['dept']; ?>&shift=<?php echo $_POST['shift']; ?>&gshift=<?php echo $_POST['gshift']; ?>&proses=<?php echo $_POST['proses']; ?>&buyer=<?php echo $_POST['buyer']; ?>&jam_awal=<?php echo $_POST['jam_awal']; ?>&jam_akhir=<?php echo $_POST['jam_akhir']; ?>"
								class="btn btn-primary <?php if ($_POST['awal'] == "") {
									echo "disabled";
								} ?>" target="_blank">Rangkuman Excel</a>
						</div> -->
					<?php } ?>
				</div>
				<div class="box-body">
					<?php 
						function buildQCWhere(&$params, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$where = [];

							$where[] = "t.tgl_masuk BETWEEN ? AND ?";
							$params[] = $start_date;
							$params[] = $stop_date;

							if ($Shift !== "ALL" && $Shift !== "") {
								$where[] = "t.shift = ?";
								$params[] = $Shift;
							}

							if ($GShift !== "ALL" && $GShift !== "") {
								$where[] = "t.gshift = ?";
								$params[] = $GShift;
							}

							if (!empty($Proses)) {
								$where[] = "t.development = ?";
								$params[] = $Proses;
							}

							return implode(" AND ", $where);
						}

						function execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params)
						{
							$stmt = sqlsrv_prepare($con_db_qc_sqlsrv, $sql, $params);
							if (!$stmt) {
								echo "<pre>Prepare failed: " . print_r(sqlsrv_errors(), true) . "</pre>";
								return ['A'=>0,'B'=>0,'C'=>0,'NON'=>0];
							}

							if (!sqlsrv_execute($stmt)) {
								echo "<pre>Execute failed: " . print_r(sqlsrv_errors(), true) . "</pre>";
								sqlsrv_free_stmt($stmt);
								return ['A'=>0,'B'=>0,'C'=>0,'NON'=>0];
							}

							$data = ['A'=>0,'B'=>0,'C'=>0,'NON'=>0];
							while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
								$gshift = $r['gshift'] ?? '';
								$total  = (int)($r['total'] ?? 0);

								if ($gshift !== '' && array_key_exists($gshift, $data)) {
									$data[$gshift] = $total;
								}
							}

							sqlsrv_free_stmt($stmt);
							return $data;
						}

						function getQCCountByShift($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$params = [];
							$whereSQL = buildQCWhere($params, $start_date, $stop_date, $Shift, $GShift, $Proses);

							$sql = " SELECT t.gshift, COUNT(*) AS total
								FROM db_qc.tbl_tq_nokk t
								WHERE $whereSQL
								GROUP BY t.gshift
							";

							return execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params);
						}

						function getCountTest($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$params = [];
							$whereSQL = buildQCWhere($params, $start_date, $stop_date, $Shift, $GShift, $Proses);

							$sql = " SELECT t.gshift, COUNT(*) AS total
								FROM (
									SELECT
										t.operator,
										t.shift,
										t.gshift,
										t.tgl_update AS tanggal_update_headerkk,
										t.tgl_masuk  AS tanggal_masuk_kk,
										t1.tgl_approve AS tanggal_approve,
										CASE
											WHEN t1.tgl_update > t1.tgl_buat THEN t1.tgl_update
											ELSE t1.tgl_buat
										END AS tgl_masuk,
										t1.tgl_buat   AS tanggal_buat_data,
										t1.tgl_update AS tanggal_update_data
									FROM db_qc.tbl_tq_nokk t
									LEFT JOIN db_qc.tbl_tq_test t1 ON t1.id_nokk = t.id
									WHERE
										(t.operator IS NOT NULL AND t.shift IS NOT NULL AND t.gshift IS NOT NULL)
								) AS t
								WHERE $whereSQL
								GROUP BY t.gshift
							";

							return execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params);
						}

						function getCountLot($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$params = [];
							$whereSQL = buildQCWhere($params, $start_date, $stop_date, $Shift, $GShift, $Proses);

							$sql = " SELECT t.gshift, COUNT(*) AS total
								FROM (
									SELECT
										t.operator,
										t.shift,
										t.gshift,
										t.tgl_update AS tanggal_update_headerkk,
										t.tgl_masuk  AS tanggal_masuk_kk,
										t1.tgl_approve AS tgl_masuk,
										CASE
											WHEN t1.tgl_update > t1.tgl_buat THEN t1.tgl_update
											ELSE t1.tgl_buat
										END AS tgl_masuk_data_kk,
										t1.tgl_buat   AS tanggal_buat_data,
										t1.tgl_update AS tanggal_update_data
									FROM db_qc.tbl_tq_nokk t
									LEFT JOIN db_qc.tbl_tq_test t1 ON t1.id_nokk = t.id
									WHERE
										(t.operator IS NOT NULL AND t.shift IS NOT NULL AND t.gshift IS NOT NULL)
								) AS t
								WHERE $whereSQL
								GROUP BY t.gshift
							";

							return execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params);
						}

						function getCountLotNa($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$params = [];
							$whereSQL = buildQCWhere($params, $start_date, $stop_date, $Shift, $GShift, $Proses);

							$sql = " SELECT t.gshift, COUNT(*) AS total
								FROM (
									SELECT
										t.operator,
										t.shift,
										t.gshift,
										t.tgl_update AS tanggal_update_headerkk,
										t.tgl_masuk  AS tgl_masuk,
										t1.tgl_approve AS tgl_approve,
										CASE
											WHEN t1.tgl_update > t1.tgl_buat THEN t1.tgl_update
											ELSE t1.tgl_buat
										END AS tgl_masuk_data_kk,
										t1.tgl_buat   AS tanggal_buat_data,
										t1.tgl_update AS tanggal_update_data
									FROM db_qc.tbl_tq_nokk t
									LEFT JOIN db_qc.tbl_tq_test t1 ON t1.id_nokk = t.id
									WHERE
										(t.operator IS NOT NULL AND t.shift IS NOT NULL AND t.gshift IS NOT NULL)
										AND t1.tgl_approve IS NULL
								) AS t
								WHERE $whereSQL
								GROUP BY t.gshift
							";
							return execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params);
						}

						function getCountTesting($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							$params = [];
							$whereSQL = buildQCWhere($params, $start_date, $stop_date, $Shift, $GShift, $Proses);

							$sql = " SELECT t.gshift, COUNT(*) AS total
								FROM (
									SELECT
										t.operator,
										t.shift,
										t.gshift,
										t.tgl_update AS tanggal_update_headerkk,
										t.tgl_masuk  AS tgl_masuk,
										t1.tgl_approve AS tgl_approve,
										CASE
											WHEN t1.tgl_update > t1.tgl_buat THEN t1.tgl_update
											ELSE t1.tgl_buat
										END AS tgl_masuk_data_kk,
										t1.tgl_buat   AS tanggal_buat_data,
										t1.tgl_update AS tanggal_update_data
									FROM db_qc.tbl_tq_nokk t
									LEFT JOIN db_qc.tbl_tq_test t1 ON t1.id_nokk = t.id
									WHERE
										(t.operator IS NOT NULL AND t.shift IS NOT NULL AND t.gshift IS NOT NULL)
										AND (t1.tgl_buat IS NULL OR t1.tgl_update IS NULL)
								) AS t
								WHERE $whereSQL
								GROUP BY t.gshift
							";
							return execCountByShiftSqlsrv($con_db_qc_sqlsrv, $sql, $params);
						}

						function getRangkumanQC($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses)
						{
							return [
								'kain_masuk' => getQCCountByShift($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses),
								'testing_selesai' => getCountTest($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses),
								'lot_approved' => getCountLot($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses),
								'lot_not_approved' => getCountLotNa($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses),
								'testing_not_start' => getCountTesting($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses),
							];
						}
					?>
				<table class="table table-bordered table-striped" style="width: 100%;">
					<thead class="bg-red">
						<tr>
							<th width="5%" rowspan="2">
								<div align="center">Shift</div>
							</th>
							<th width="14%" rowspan="2">
								<div align="center">Kain Masuk</div>
							</th>
							<th width="14%" rowspan="2">
								<div align="center">Jumlah Testing Masuk</div>
							</th>
							<th width="10%" rowspan="2">
								<div align="center">Testing Selesai</div>
							</th>
							<th width="18%" rowspan="2">
								<div align="center">Lot Approved</div>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php $rangkuman = getRangkumanQC($con_db_qc_sqlsrv, $start_date, $stop_date, $Shift, $GShift, $Proses);
							foreach (['A','B','C','NON'] as $s): ?>
						<tr>
							<td align="center"><b><?= $s ?></b></td>
							<td align="right"><?= $rangkuman['kain_masuk'][$s] ?></td>
							<td align="right"><?= $rangkuman['kain_masuk'][$s] ?></td>
							<td align="right"><?= $rangkuman['testing_selesai'][$s] ?></td>
							<td align="right"><?= $rangkuman['lot_approved'][$s] ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td align="center"><b>TOTAL</b></td>
							<td align="right"><?= array_sum($rangkuman['kain_masuk']) ?></td>
							<td align="right"><?= array_sum($rangkuman['kain_masuk']) ?></td>
							<td align="right"><?= array_sum($rangkuman['testing_selesai']) ?></td>
							<td align="right"><?= array_sum($rangkuman['lot_approved']) ?></td>
						</tr>
						<tr>
							<td align="center" colspan='2'><b>SISA KK TUNGGU HASIL</b></td>
							<td align="right"><?= array_sum($rangkuman['lot_not_approved']) ?></td>
							<td align="right"  colspan='2'></td>
						</tr>
						<tr>
							<td align="center" colspan='2'><b>SISA TESTING TUNGGU HASIL</b></td>
							<td align="right"><?= array_sum($rangkuman['testing_not_start']) ?></td>
							<td align="right" colspan='2'></td>
						</tr>
					</tfoot>
				</table>
				</div>
				<!-- /.box-header -->
				<!-- form start -->
			</div>
		</div>

	</div>
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title">Data Kain Masuk</h3><br>
					<?php if ($_POST['awal'] != "") { ?><b>Periode:
							<?php echo $start_date . " to " . $stop_date ?>
						</b>
					<?php } ?>
					<?php if ($_POST['awal'] != "") { ?>
						<!-- <div class="pull-right">
							<a href="pages/cetak/cetak_lap_inspeksi.php?&awal=<?php echo $Awal; ?>&akhir=<?php echo $Akhir; ?>&jam_awal=<?php echo $jamA; ?>&jam_akhir=<?php echo $jamAr; ?>&shift=<?php echo $Shift; ?>&gshift=<?php echo $GShift; ?>&proses=<?php echo $Proses; ?>&buyer=<?php echo $Buyer; ?>"
								class="btn btn-danger " target="_blank" data-toggle="tooltip" data-html="true"
								title="Laporan Inspeksi"><i class="fa fa-print"></i> Cetak</a>
							<a href="pages/cetak/excel_lap_inspectnew.php?&awal=<?php echo $Awal; ?>&akhir=<?php echo $Akhir; ?>&jam_awal=<?php echo $jamA; ?>&jam_akhir=<?php echo $jamAr; ?>&shift=<?php echo $Shift; ?>&gshift=<?php echo $GShift; ?>&proses=<?php echo $Proses; ?>&buyer=<?php echo $Buyer; ?>"
								class="btn btn-success " target="_blank" data-toggle="tooltip" data-html="true"
								title="Laporan Inspeksi Harian New"><i class="fa fa-print"></i> Laporan Inspeksi Harian
								New</a>
						</div> -->
					<?php } ?>
				</div>
				<div class="box-body">
					<table class="table table-bordered table-hover table-striped nowrap" id="example3"
						style="width:100%">
						<thead class="bg-blue">
							<tr>
								<th>
									<div align="center">No</div>
								</th>
								<th>
									<div align="center">Pelanggan</div>
								</th>
								<th>
									<div align="center">No Order</div>
								</th>
								<th>
									<div align="center">Jenis Kain</div>
								</th>
								<th>
									<div align="center">Warna</div>
								</th>
								<th>
									<div align="center">Tgl Pengiriman</div>
								</th>
								<th>
									<div align="center">Lot</div>
								</th>
								<th>
									<div align="center">No Item</div>
								</th>
								<th>
									<div align="center">Inspektor</div>
								</th>
								<th>
									<div align="center">No MC</div>
								</th>
								<th>
									<div align="center">Roll</div>
								</th>
								<th>
									<div align="center">Qty Bruto</div>
								</th>
								<th>
									<div align="center">Yard</div>
								</th>
								<th>
									<div align="center">Total Waktu</div>
								</th>
								<th>
									<div align="center">Tanggal Mulai</div>
								</th>
								<th>
									<div align="center">Tanggal Stop</div>
								</th>
								<th>
									<div align="center">Yard/Menit</div>
								</th>
								<th>
									<div align="center">No Test</div>
								</th>
								<th>Nokk</th>
								<th>No Demand</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$no = 1;
								$query2 = " SELECT t.*
									FROM db_qc.tbl_tq_nokk t
									WHERE t.tgl_masuk BETWEEN ? AND ?
									$Wshift $WGshift $WProses
									ORDER BY t.tgl_masuk ASC
								";

								$params2 = [$start_date, $stop_date];

								// eksekusi sqlsrv
								$qry1 = sqlsrv_query($con_db_qc_sqlsrv, $query2, $params2);
								if ($qry1 === false) {
									echo "<tr><td colspan='21'><pre>" . print_r(sqlsrv_errors(), true) . "</pre></td></tr>";
								} else {
									$totOk = 0; $totTk = 0; $totPr = 0;
									$totOkQ = 0; $totTkQ = 0; $totPrQ = 0;
									$totOkY = 0; $totTkY = 0; $totPrY = 0;
									$totF = 0; $totO = 0; $totPS = 0;
									$totFQ = 0; $totOQ = 0; $totPSQ = 0;
									$totFY = 0; $totOY = 0; $totPSY = 0;

									$okRol = 0; $okQty = 0; $okYrd = 0;
									$TkRol = 0; $TkQty = 0; $TkYrd = 0;
									$PrRol = 0; $PrQty = 0; $PrYrd = 0;
									$FRol = 0; $FQty = 0; $FYrd = 0;
									$ORol = 0; $OQty = 0; $OYrd = 0;
									$PSRol = 0; $PSQty = 0; $PSYrd = 0;

									while ($row1 = sqlsrv_fetch_array($qry1, SQLSRV_FETCH_ASSOC)) {
										$waktu = (int)($row1['waktu'] ?? 0);
										$istirahat = (int)($row1['istirahat'] ?? 0);
										$hourdiff = $waktu - $istirahat;
										$tgl_delivery = $row1['tgl_delivery'];
										if ($tgl_delivery instanceof DateTime) $tgl_delivery = $tgl_delivery->format('Y-m-d H:i:s');
										$tgl_mulai = $row1['tgl_mulai'];
										if ($tgl_mulai instanceof DateTime) $tgl_mulai = $tgl_mulai->format('Y-m-d H:i:s');
										$tgl_stop = $row1['tgl_stop'];
										if ($tgl_stop instanceof DateTime) $tgl_stop = $tgl_stop->format('Y-m-d H:i:s');
							?>
										<tr valign="top" bgcolor="<?php echo $bgcolor; ?>">
											<td align="center"><?php echo $no; ?></td>
											<td align="left"><?php echo $row1['pelanggan']; ?></td>
											<td><?php echo $row1['no_order']; ?></td>
											<td><?php echo $row1['jenis_kain']; ?></td>
											<td align="left"><?php echo $row1['warna']; ?></td>
											<td align="center"><?php echo $tgl_delivery; ?></td>
											<td align="center"><?php echo $row1['lot']; ?></td>
											<td align="center"><?php echo $row1['no_item']; ?></td>
											<td align="center"><?php echo $row1['personil']; ?></td>
											<td align="center"><?php echo $row1['no_mesin']; ?></td>

											<td align="center">
												<?php if (($row1['jml_rol'] ?? 0) > 0) { ?>
													<a data-pk="<?php echo $row1['idins']; ?>"
													data-value="<?php echo $row1['jml_rol']; ?>"
													class="jml_roll_inspeksi"
													href="javascript:void(0)">
														<?php echo $row1['jml_rol']; ?>
													</a>
												<?php } else { ?>
													<a data-pk="<?php echo $row1['id_schedule']; ?>"
													data-value="<?php echo $row1['rol']; ?>"
													class="jml_roll_inspeksi2"
													href="javascript:void(0)">
														<?php echo $row1['rol']; ?>
													</a>
												<?php } ?>
											</td>

											<td align="center">
												<?php if (($row1['jml_rol'] ?? 0) > 0) { ?>
													<a data-pk="<?php echo $row1['idins']; ?>"
													data-value="<?php echo $row1['qty']; ?>"
													class="qty_inspeksi"
													href="javascript:void(0)">
														<?php echo $row1['qty']; ?>
													</a>
												<?php } else { ?>
													<a data-pk="<?php echo $row1['id_schedule']; ?>"
													data-value="<?php echo $row1['bruto']; ?>"
													class="qty_inspeksi2"
													href="javascript:void(0)">
														<?php echo $row1['bruto']; ?>
													</a>
												<?php } ?>
											</td>

											<td align="center">
												<?php if (($row1['yard'] ?? 0) > 0) { ?>
													<a data-pk="<?php echo $row1['idins']; ?>"
													data-value="<?php echo $row1['yard']; ?>"
													class="jml_yard_inspeksi"
													href="javascript:void(0)">
														<?php echo $row1['yard']; ?>
													</a>
												<?php } else { ?>
													<a data-pk="<?php echo $row1['id_schedule']; ?>"
													data-value="<?php echo $row1['pjng_order']; ?>"
													class="jml_yard_inspeksi2"
													href="javascript:void(0)">
														<?php echo $row1['pjng_order']; ?>
													</a>
												<?php } ?>
											</td>

											<td align="center"><?php echo $hourdiff; ?></td>
											<td align="center"><?php echo $tgl_mulai; ?></td>
											<td align="center"><?php echo $tgl_stop; ?></td>

											<td align="center">
												<?php
												$yard = (float)($row1['yard'] ?? 0);
												echo ($hourdiff > 0) ? round($yard / $hourdiff, 2) : "0";
												?>
											</td>

											<td><?php echo $row1['no_test']; ?></td>
											<td><?php echo $row1['nokk']; ?></td>
											<td>
												<a href="javascript:void(0)"
												class="nodemand-link"
												data-nodemand="<?php echo htmlspecialchars(trim((string)$row1['nodemand'])); ?>">
													<?php echo htmlspecialchars((string)$row1['nodemand']); ?>
												</a>
											</td>

											<td>
												<a href="#" onclick="confirm_del('HapusIns-<?php echo $row1['idins'] ?>');"
												class="btn btn-xs btn-danger <?php
													if ($_SESSION['akses'] == "biasa" || $_SESSION['lvl_id'] != "INSPEKSI") echo "disabled";
												?>">
													<i class="fa fa-trash"></i>
												</a>
											</td>
										</tr>
								<?php
										if ($row1['proses'] == "Inspect Finish" && $row1['status_produk'] == "1") {
											$okRol = (int)($row1['jml_rol'] ?? 0);
											$okQty = (int)($row1['qty'] ?? 0);
											$okYrd = (int)($row1['yard'] ?? 0);
										} else { $okRol=0; $okQty=0; $okYrd=0; }

										if ($row1['proses'] == "Inspect Finish" && $row1['status_produk'] == "2") {
											$TkRol = (int)($row1['jml_rol'] ?? 0);
											$TkQty = (int)($row1['qty'] ?? 0);
											$TkYrd = (int)($row1['yard'] ?? 0);
										} else { $TkRol=0; $TkQty=0; $TkYrd=0; }

										if ($row1['proses'] == "Inspect Finish" && $row1['status_produk'] == "3") {
											$PrRol = (int)($row1['jml_rol'] ?? 0);
											$PrQty = (int)($row1['qty'] ?? 0);
											$PrYrd = (int)($row1['yard'] ?? 0);
										} else { $PrRol=0; $PrQty=0; $PrYrd=0; }

										if ($row1['proses'] == "Inspect Finish" && ($row1['status_produk'] == "1" || $row1['status_produk'] == "2")) {
											$FRol = (int)($row1['jml_rol'] ?? 0);
											$FQty = (int)($row1['qty'] ?? 0);
											$FYrd = (int)($row1['yard'] ?? 0);
										} else { $FRol=0; $FQty=0; $FYrd=0; }

										if ($row1['proses'] == "Inspect Oven") {
											$ORol = (int)($row1['jml_rol'] ?? 0);
											$OQty = (int)($row1['qty'] ?? 0);
											$OYrd = (int)($row1['yard'] ?? 0);
										} else { $ORol=0; $OQty=0; $OYrd=0; }

										if ($row1['proses'] == "Pisah") {
											$PSRol = (int)($row1['jml_rol'] ?? 0);
											$PSQty = (int)($row1['qty'] ?? 0);
											$PSYrd = (int)($row1['yard'] ?? 0);
										} else { $PSRol=0; $PSQty=0; $PSYrd=0; }

										$totOk += $okRol; $totTk += $TkRol; $totPr += $PrRol;
										$totOkQ += $okQty; $totTkQ += $TkQty; $totPrQ += $PrQty;
										$totOkY += $okYrd; $totTkY += $TkYrd; $totPrY += $PrYrd;

										$totF += $FRol; $totO += $ORol; $totPS += $PSRol;
										$totFQ += $FQty; $totOQ += $OQty; $totPSQ += $PSQty;
										$totFY += $FYrd; $totOY += $OYrd; $totPSY += $PSYrd;

										$no++;
									}

									sqlsrv_free_stmt($qry1);
								}
							?>
						</tbody>
						<tfoot>
							<tr valign="top" bgcolor="<?php echo $bgcolor; ?>">
								<td align="center">&nbsp;</td>
								<td align="left">&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="left">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
							</tr>
							<tr valign="top" bgcolor="<?php echo $bgcolor; ?>">
								<td align="center">&nbsp;</td>
								<td align="left"><strong>F</strong>=
									<?php echo $totF . "x" . $totFQ . " KGs"; ?>
								</td>
								<td>&nbsp;</td>
								<td align="left"><strong>OK</strong>=
									<?php echo $totOk . "x" . $totOkQ . " KGs"; ?>
								</td>
								<td align="left"><strong>PR</strong>=
									<?php echo $totPr . "x" . $totPrQ . " KGs"; ?>
								</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="left"><strong>F</strong>=
									<?php echo $totF . "x" . $totFY . " Yrds"; ?>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="left"><strong>OK</strong>=
									<?php echo $totOk . "x" . $totOkY . " Yrds"; ?>
								</td>
								<td align="left"><strong>PR</strong>=
									<?php echo $totPr . "x" . $totPrY . " Yrds"; ?>
								</td>
								<td align="left">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr valign="top" bgcolor="<?php echo $bgcolor; ?>">
								<td align="center">&nbsp;</td>
								<td align="left"><strong>O</strong>=
									<?php echo $totO . "x" . $totOQ . " KGs"; ?>
								</td>
								<td>&nbsp;</td>
								<td align="left"><strong>TK</strong>=
									<?php echo $totTk . "x" . $totTkQ . " KGs"; ?>
								</td>
								<td align="left"><strong>PS</strong>=
									<?php echo $totPS . "x" . $totPSQ . " KGs"; ?>
								</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="left"><strong>O</strong>=
									<?php echo $totO . "x" . $totOY . " Yrds"; ?>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="left"><strong>TK</strong>=
									<?php echo $totTk . "x" . $totTkY . " Yrds"; ?>
								</td>
								<td align="left"><strong>PS</strong>=
									<?php echo $totPS . "x" . $totPSY . " Yrds"; ?>
								</td>
								<td align="left">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td align="center">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	<script>
document.addEventListener('click', function (e) {

    const link = e.target.closest('.nodemand-link');
    if (!link) return;

    const nodemand = link.dataset.nodemand;
    if (!nodemand) return;

    fetch('pages/ajax/get_no_test.php?nodemand=' + encodeURIComponent(nodemand))
        .then(response => response.json())
        .then(data => {

            if (data.no_test) {
                // ✅ buka tab baru ke halaman final
                window.open(
                    'TestingNewNoTes-' + data.no_test,
                    '_blank'
                );
            } else {
                Swal.fire({
                    title: 'No Kartu Tidak Ditemukan',
                    text: 'Klik OK untuk input data kembali',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error(error);
            Swal.fire({
                title: 'Terjadi Kesalahan',
                text: 'Gagal mengambil data dari server.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
});
</script>

	<!-- Modal Popup untuk delete-->
	<div class="modal fade" id="delLapIns" tabindex="-1">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" style="margin-top:100px;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="text-align:center;">Are you sure to delete this information ?</h4>
				</div>

				<div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
					<a href="#" class="btn btn-danger" id="del_link">Delete</a>
					<button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
	<!-- Modal -->
	<div id="StsEdit" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
		aria-hidden="true"></div>
	<script>
		$(document).ready(function () {
			$('[data-toggle="tooltip"]').tooltip();
		});
		function confirm_del(delete_url) {
			$('#delLapIns').modal('show', {
				backdrop: 'static'
			});
			document.getElementById('del_link').setAttribute('href', delete_url);
		}
	</script>
</body>

</html>