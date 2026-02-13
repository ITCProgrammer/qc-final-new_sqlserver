   <?PHP
ini_set("error_reporting", 1);
session_start();
include"koneksi.php";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Harian Produksi</title>

</head>
<body>
<?php
$Awal	= isset($_POST['awal']) ? $_POST['awal'] : '';
$Akhir	= isset($_POST['akhir']) ? $_POST['akhir'] : '';
$Order	= isset($_POST['order']) ? $_POST['order'] : '';
$Hanger	= isset($_POST['hanger']) ? $_POST['hanger'] : '';
$PO	= isset($_POST['po']) ? $_POST['po'] : '';	
$GShift	= isset($_POST['gshift']) ? $_POST['gshift'] : '';	
$Fs		= isset($_POST['fasilitas']) ? $_POST['fasilitas'] : '';
$sts_red = isset($_POST['sts_red']) ? $_POST['sts_red'] : '';
$sts_claim = isset($_POST['sts_claim']) ? $_POST['sts_claim'] : '';
$Langganan	= isset($_POST['langganan']) ? $_POST['langganan'] : '';
$Demand	= isset($_POST['demand']) ? $_POST['demand'] : '';
$Prodorder	= isset($_POST['prodorder']) ? $_POST['prodorder'] : '';
$Pejabat	= isset($_POST['pejabat']) ? $_POST['pejabat'] : '';
$Solusi	= isset($_POST['solusi']) ? $_POST['solusi'] : '';
$Kategori	= isset($_POST['kategori']) ? $_POST['kategori'] : '';
	
if($_POST['gshift']=="ALL"){$shft=" ";}else{$shft=" AND b.g_shift = '$GShift' ";}	
?>
<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"> Filter Laporan KPE </h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <!-- form start -->
  <form method="post" enctype="multipart/form-data" name="form1" class="form-horizontal" id="form1">
    <div class="box-body">
      <div class="form-group">
        <div class="col-sm-2">
          <div class="input-group date">
            <div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
            <input name="awal" type="text" class="form-control pull-right" id="datepicker" placeholder="Tanggal Awal" value="<?php echo $Awal; ?>" autocomplete="off"/>
          </div>
        </div>
        <div class="col-sm-2">
          <div class="input-group date">
            <div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
            <input name="akhir" type="text" class="form-control pull-right" id="datepicker1" placeholder="Tanggal Akhir" value="<?php echo $Akhir;  ?>" autocomplete="off"/>
          </div>
        </div>
        <div class="col-sm-2">
            <input name="order" type="text" class="form-control pull-right" id="order" placeholder="No Order" value="<?php echo $Order;  ?>" />
          </div>
        <div class="col-sm-2">
            <input name="po" type="text" class="form-control pull-right" id="po" placeholder="No PO" value="<?php echo $PO;  ?>" />
          </div>
        <div class="col-sm-2">
            <input name="hanger" type="text" class="form-control pull-right" id="hanger" placeholder="No Hanger" value="<?php echo $Hanger;  ?>" />
          </div>
        <div class="col-sm-2">
            <input name="langganan" type="text" class="form-control pull-right" id="langganan" placeholder="Langganan/Buyer" value="<?php echo $Langganan;  ?>" />
          </div>
        <!-- /.input group -->
      </div>
      <div class="form-group">
        <div class="col-sm-2">
          <input name="demand" type="text" class="form-control pull-right" id="demand" placeholder="No Demand" value="<?php echo $Demand;  ?>" />
        </div>
        <div class="col-sm-2">
          <input name="prodorder" type="text" class="form-control pull-right" id="prodorder" placeholder="Prod. Order" value="<?php echo $Prodorder;  ?>" />
        </div>
        <div class="col-sm-2">
            <select class="form-control select2" name="pejabat" id="pejabat">
              <option value="">Pilih Pejabat</option>
                <?php 
                  $qryp=sqlsrv_query($con_db_qc_sqlsrv,"SELECT nama FROM db_qc.tbl_personil_aftersales WHERE jenis='pejabat' ORDER BY nama ASC");
                  while($rp=sqlsrv_fetch_array($qryp)){
                ?>
              <option value="<?php echo $rp['nama'];?>" <?php if($Pejabat==$rp['nama']){echo "SELECTED";}?>><?php echo $rp['nama'];?></option>	
                <?php }?>
            </select>
        </div>
        <div class="col-sm-2">
        <select class="form-control select2" name="solusi" id="solusi">
							<option value="">Solusi</option>
							<?php 
							$qryp=sqlsrv_query($con_db_qc_sqlsrv,"SELECT solusi FROM db_qc.tbl_solusi ORDER BY solusi ASC");
							while($rp=sqlsrv_fetch_array($qryp)){
							?>
							<option value="<?php echo $rp['solusi'];?>" <?php if($Solusi==$rp['solusi']){echo "SELECTED";}?>><?php echo $rp['solusi'];?></option>	
							<?php }?>
						</select>
        </div>
        <div class="col-sm-2">
        <select class="form-control select2" name="kategori" id="kategori">
							<option value="">Kategori</option>
							<?php 
							$categories = ["MAJOR", "SAMPLE", "REPEAT", "GENERAL"];
							foreach($categories as $category){
							?>
							<option value="<?=$category?>" <?=$Kategori==$category?'selected':''?>><?=$category?></option>	
							<?php }?>
						</select>
        </div>
      </div>
    <div class="form-group">
		  <label for="status_red" class="col-sm-0 control-label"></label>		  
        <div class="col-sm-3">
        <!-- <input type="checkbox" name="sts_red" id="sts_red" value="1" >   -->
        <input type="checkbox" name="sts_red" id="sts_red" value="1" <?php  if($sts_red=="1" or $sts_red=="0"){ echo "checked";} ?>>  
        <label> Laporan Leadtime Email</label>
          
        </div>		  	
		  </div>
      <div class="form-group">
		  <label for="status_claim" class="col-sm-0 control-label"></label>		  
        <div class="col-sm-3">
        <input type="checkbox" name="sts_claim" id="sts_claim" value="1" <?php  if($sts_claim=="1"){ echo "checked";} ?>>  
        <label> Claim</label>
          
        </div>		  	
		  </div>
    <!-- /.input group -->	
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
      <div class="col-sm-2">
        <button type="submit" class="btn btn-block btn-social btn-linkedin btn-sm" name="save" style="width: 60%">Search <i class="fa fa-search"></i></button>
      </div>
    </div>
    <!-- /.box-footer -->
  </form>
</div>
<div class="row">
  <div class="col-xs-12">
    <div class="box">
      <div class="box-header with-border">
        <h3 class="box-title">Data KPE</h3><br>
        <?php if($_POST['awal']!="") { ?><b>Periode: <?php echo $_POST['awal']." to ".$_POST['akhir']; ?></b>
          <?php } ?>
          <div class="pull-right">

          <?php if($_POST['solusi'] == 'PERBAIKAN GARMENT'){ ?>
            <a href="pages/cetak/cetak_perbaikan_garment.php?awal=<?=$Awal?>&akhir=<?=$Akhir?>" class="btn btn-primary" target="_blank">Cetak Perbaikan Garment</a>
          <?php }elseif($_POST['solusi'] == 'DEBIT NOTE') {?>
            <a href="pages/cetak/cetak_debit_note.php?awal=<?=$Awal?>&akhir=<?=$Akhir?>" class="btn btn-success" target="_blank">Cetak Debit Note</a>
            <?php }?>
            
            <a href="pages/cetak/cetak_kpe.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&order=<?php echo $_POST['order']; ?>&po=<?php echo $_POST['po']; ?>&hanger=<?php echo $_POST['hanger']; ?>&langganan=<?php echo $_POST['langganan']; ?>&demand=<?php echo $_POST['demand']; ?>&prodorder=<?php echo $_POST['prodorder']; ?>&pejabat=<?php echo $_POST['pejabat']; ?>" class="btn btn-danger <?php if($_POST['awal']=="") { echo "disabled"; }?>" target="_blank">Cetak KPE</a>
			 
			 
			<a href="pages/cetak/cetak_kpe_disposisi.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&order=<?php echo $_POST['order']; ?>&po=<?php echo $_POST['po']; ?>&hanger=<?php echo $_POST['hanger']; ?>&langganan=<?php echo $_POST['langganan']; ?>&demand=<?php echo $_POST['demand']; ?>&prodorder=<?php echo $_POST['prodorder']; ?>&pejabat=<?php echo $_POST['pejabat']; ?>" class="btn btn-danger <?php if($_POST['awal']=="") { echo "disabled"; }?>" target="_blank">Cetak KPE Disposisi</a> 
            <a href="pages/cetak/excel_kpe_disposisi.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&order=<?php echo $_POST['order']; ?>&po=<?php echo $_POST['po']; ?>&hanger=<?php echo $_POST['hanger']; ?>&langganan=<?php echo $_POST['langganan']; ?>&demand=<?php echo $_POST['demand']; ?>&prodorder=<?php echo $_POST['prodorder']; ?>&pejabat=<?php echo $_POST['pejabat']; ?>" class="btn btn-success <?php if($_POST['awal']=="") { echo "disabled"; }?>" target="_blank">Excel KPE Disposisi</a> 
          </div>
        <?php if($sts_red=='1' or $sts_red=='0'){ ?>
              <div class="pull-right">
                <a href="pages/cetak/cetak_redemail.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>" class="btn btn-primary <?php if($_POST['awal']=="") { echo "disabled"; }?>" target="_blank">Cetak Leadtime Email</a>
                </div>
            <?php } ?>
        <?php if($sts_claim=='1'){?>
              <div class="pull-right">
              <a href="pages/cetak/cetak_claim.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>" class="btn btn-primary <?php if($_POST['awal']=="") { echo "disabled"; }?>" target="_blank">Cetak Claim</a>
                </div>
            <?php } ?>
	    </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped nowrap" id="example3" style="width:100%">
          <thead class="bg-blue">
            <tr>
              <th rowspan='2' rowspan='2'><div align="center">No</div></th>
              <th rowspan='2'><div align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Aksi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></th>
              <th rowspan='2'><div align="center">Tgl</div></th>
              <th rowspan='2'><div align="center">Pelanggan</div></th>
              <th rowspan='2'><div align="center">Buyer</div></th>
              <th rowspan='2'><div align="center">No Demand</div></th>
              <th rowspan='2'><div align="center">No Prod Order</div></th>
              <th rowspan='2'><div align="center">PO</div></th>
              <!-- <th rowspan='2'><div align="center">NO ITEM</div></th> -->
              <th rowspan='2'><div align="center">Order</div></th>
              <th rowspan='2'><div align="center">Hanger</div></th>
              <th rowspan='2'><div align="center">Jenis Kain</div></th>
              <th rowspan='2'><div align="center">Lebar</div></th>
              <th rowspan='2'><div align="center">Gramasi</div></th>
              <th rowspan='2'><div align="center">Lot</div></th>
              <th rowspan='2'><div align="center">Warna</div></th>
              <th rowspan='2'><div align="center">Qty Order</div></th>
              <th rowspan='2'><div align="center">Qty Order (yd)</div></th>
              <th rowspan='2'><div align="center">Qty Kirim</div></th>
              <th rowspan='2'><div align="center">Qty Kirim (yd)</div></th>
              <th rowspan='2'><div align="center">Qty Claim</div></th>
              <th rowspan='2'><div align="center">Qty Claim (yd)</div></th>
              <th rowspan='2'><div align="center">Qty Lolos QC (kg)</div></th>
              <th rowspan='2'><div>
                <div align="center">T Jawab</div>
              </div></th>
              <th rowspan='2'><div align="center">Masalah Dominan</div></th>
              <th rowspan='2'><div align="center">Masalah</div></th>
              <th rowspan='2'><div align="center">Penyebab</div></th>
              <th rowspan='2'><div align="center">Route Cause</div></th>
              <th rowspan='2'><div align="center">Solusi</div></th>
              <th rowspan='2'><div align="center">Personil</div></th>
              <th rowspan='2'><div align="center">Pejabat</div></th>
              <th rowspan='2'><div align="center">Lolos/Disposisi</div></th>
              <th rowspan='2'><div align="center">BPP</div></th>
              <th colspan= '4'class="text-center">NCP</th>
              <!-- <th><div align="center">Analisa Kerusakan</div></th> -->
              <th rowspan='2'><div align="center">Ket</div></th>
            </tr>
            <tr>
            <th class="text-center">No NCP</th>
              <th class="text-center">Masalah Utama</th>
              <th class="text-center">Akar Masalah</th>
              <th class="text-center">Solusi Jangka Panjang</th>
            </tr>
          </thead>
          <tbody>
          <?php
            $no=1;
            // if($sts_red=="1"){ $stsred =" AND a.sts_red='1' "; }else{$stsred = " ";}
            if($sts_claim=="1"){ $stsclaim =" AND a.sts_claim='1' "; }else{$stsclaim =" ";}
            if($Awal!=""){ $Where =" AND CAST( a.tgl_buat AS DATE ) BETWEEN '$Awal' AND '$Akhir' "; }

            if($Kategori != "") {
              $query4Kategori = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
                                                      a.*,
                                                      b.pjg1
                                                      FROM
                                                      db_qc.tbl_aftersales_now a
                                                      LEFT JOIN db_qc.tbl_ganti_kain_now b
                                                      ON
                                                      b.id_nsp = a.id
                                                      WHERE
                                                      CAST(a.tgl_buat AS DATE ) BETWEEN '$Awal' AND '$Akhir'
                                                      ORDER BY
                                                      a.tgl_buat ASC");
              $majorTemp = [];
              $sampleTemp = [];
              $repeatTemp = [];
              $generalTemp = [];

              while($row = sqlsrv_fetch_array($query4Kategori, SQLSRV_FETCH_ASSOC)) {
                  if($row['pjg1'] >= 500) {
                      $majorTemp[] = $row;
                  } elseif(in_array(substr($row['no_order'], 0, 3), ['SAM', 'SME'])) {
                      $sampleTemp[] = $row;
                  } else {
                      $generalTemp[] = $row;
                  }
              }

              $hanger_masalah_dominan = array_map(function($value) {
                  return $value['no_hanger'].''.$value['masalah_dominan'];
              }, $generalTemp);

              $count_hanger_masalah_dominan = array_count_values($hanger_masalah_dominan);
              $group_hanger_masalah_dominan = array_keys(array_filter($count_hanger_masalah_dominan, fn($value) => $value > 1 ));

              foreach ($generalTemp as $key => $value) {
                  $hmd = $value['no_hanger'].''.$value['masalah_dominan'];
                  if(in_array($hmd, $group_hanger_masalah_dominan)){
                      $repeatTemp[] = $value;
                      unset($generalTemp[$key]);
                  }
              }

              $majorTemp = array_column($majorTemp, 'id');
              $sampleTemp = array_column($sampleTemp, 'id');
              $repeatTemp = array_column($repeatTemp, 'id');
              $generalTemp = array_column($generalTemp, 'id');

              switch ($Kategori) {
                case "MAJOR":
                    $WhereKategori = "AND a.id IN (" . implode(",", $majorTemp) . ") ";
                    break;
                case "SAMPLE":
                    $WhereKategori = "AND a.id IN (" . implode(",", $sampleTemp) . ") ";
                    break;
                case "REPEAT":
                    $WhereKategori = "AND a.id IN (" . implode(",", $repeatTemp) . ") ";
                    break;
                case "GENERAL":
                    $WhereKategori = "AND a.id IN (" . implode(",", $generalTemp) . ") ";
                    break;
                default:
                    // handle default case if necessary
              }
            }

            // if($Awal!="" or $sts_red=="1" or $sts_claim=="1" or $Order!="" or $Hanger!="" or $PO!="" or $Langganan!="" or $Demand!="" or $Prodorder!="" or $Pejabat!="" or $Solusi!=""){
            if($Awal!=""  or $sts_claim=="1" or $Order!="" or $Hanger!="" or $PO!="" or $Langganan!="" or $Demand!="" or $Prodorder!="" or $Pejabat!="" or $Solusi!=""){
              $query_sqlserver = "SELECT
                                        a.*,
                                        ncp.no_ncp,
                                        ncp.masalah_utama,
                                        ncp.akar_masalah,
                                        ncp.solusi_panjang
                                    FROM db_qc.tbl_aftersales_now a
                                    LEFT JOIN (
                                        SELECT
                                            b.nodemand,
                                            STRING_AGG(b.no_ncp_gabungan, ', ') AS no_ncp,
                                            STRING_AGG(b.masalah_dominan, ', ') AS masalah_utama,
                                            STRING_AGG(b.akar_masalah, ', ') AS akar_masalah,
                                            STRING_AGG(b.solusi_panjang, ', ') AS solusi_panjang
                                        FROM (
                                            SELECT DISTINCT
                                                nodemand,
                                                no_ncp_gabungan,
                                                masalah_dominan,
                                                akar_masalah,
                                                solusi_panjang
                                            FROM db_qc.tbl_ncp_qcf_now
                                        ) b
                                        GROUP BY b.nodemand
                                    ) ncp
                                        ON ncp.nodemand = a.nodemand
                                    WHERE
                                        a.no_order   LIKE '%' + ? + '%'
                                        AND a.po     LIKE '%' + ? + '%'
                                        AND a.no_hanger LIKE '%' + ? + '%'
                                        AND a.langganan LIKE '%' + ? + '%'
                                        AND a.nodemand LIKE '%' + ? + '%'
                                        AND a.nokk   LIKE '%' + ? + '%'
                                        AND a.pejabat LIKE '%' + ? + '%'
                                        AND a.solusi LIKE '%' + ? + '%'
                                        $Where $stsclaim 
                                    ORDER BY a.id ASC;";
              $qry1=sqlsrv_query($con_db_qc_sqlsrv, $query_sqlserver, [$Order, $PO, $Hanger, $Langganan, $Demand, $Prodorder, $Pejabat, $Solusi]);
              while($row1=sqlsrv_fetch_array($qry1)){
                  $noorder=str_replace("/","&",$row1['no_order']);
                  if($row1['t_jawab']!="" and $row1['t_jawab1']!="" and $row1['t_jawab2']!=""){ $tjawab=$row1['t_jawab']."+".$row1['t_jawab1']."+".$row1['t_jawab2'];
                  }else if($row1['t_jawab']!="" and $row1['t_jawab1']!="" and $row1['t_jawab2']==""){
                  $tjawab=$row1['t_jawab']."+".$row1['t_jawab1'];	
                  }else if($row1['t_jawab']!="" and $row1['t_jawab1']=="" and $row1['t_jawab2']!=""){
                  $tjawab=$row1['t_jawab']."+".$row1['t_jawab2'];	
                  }else if($row1['t_jawab']=="" and $row1['t_jawab1']!="" and $row1['t_jawab2']!=""){
                  $tjawab=$row1['t_jawab1']."+".$row1['t_jawab2'];	
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
          <tr bgcolor="<?php echo $bgcolor; ?>">
            <td align="center"><?php echo $no; ?>
            </td>
            <td align="center"><div class="btn-group">
            <a href="TambahBon-<?php echo $row1['id']; ?>-<?php echo $noorder; ?>" class="btn btn-warning btn-xs <?php if($_SESSION['akses']=='biasa' OR $_SESSION['lvl_id']!='AFTERSALES'){ echo "disabled"; } ?>" target="_blank"><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Ganti Kain"></i> </a>
            <a href="TambahDetailRetur-<?php echo $row1['id']; ?>" class="btn btn-success btn-xs <?php if($_SESSION['akses']=='biasa' OR $_SESSION['lvl_id']!='AFTERSALES'){ echo "disabled"; } ?>" target="_blank"><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="Retur"></i> </a>
            <a href="TambahTPUKPE-<?php echo $row1['id']; ?>" class="btn btn-primary btn-xs <?php if($_SESSION['akses']=='biasa' OR $_SESSION['lvl_id']!='AFTERSALES'){ echo "disabled"; } ?>" target="_blank"><i class="fa fa-plus" data-toggle="tooltip" data-placement="top" title="TPUKPE"></i> </a>
            <a href="EditKPENew-<?php echo $row1['id']; ?>" class="btn btn-info btn-xs <?php if($_SESSION['akses']=='biasa' OR $_SESSION['lvl_id']!='AFTERSALES'){ echo "disabled"; } ?>" target="_blank"><i class="fa fa-edit" data-toggle="tooltip" data-placement="top" title="Edit"></i> </a>
            <a href="#" class="btn btn-danger btn-xs <?php if($_SESSION['akses']=='biasa' OR $_SESSION['lvl_id']!='AFTERSALES'){ echo "disabled"; } ?>" onclick="confirm_delete('./HapusDataKPE-<?php echo $row1['id'] ?>');"><i class="fa fa-trash" data-toggle="tooltip" data-placement="top" title="Hapus"></i> </a>
            </div></td>
            <td align="center">
              <?php 
              if (!empty($row1['tgl_buat'])) {
                  if ($row1['tgl_buat'] instanceof DateTime) {
                      echo $row1['tgl_buat']->format('Y-m-d');
                  } else {
                      echo date('d-m-Y H:i:s', strtotime($row1['tgl_buat']));
                  }
              }
              ?>
            </td>
            <?php 
              $pelanggan = explode('/', $row1['langganan'])[0]; 
              $buyer = explode('/', $row1['langganan'])[1];
            ?>

            <td><?php echo $pelanggan; ?></td>
            <td><?php echo $buyer; ?></td>

            <td align="center"><?php echo $row1['nodemand'];?></td>
            <td align="center"><?php echo $row1['nokk'];?></td>
            <td align="center"><?php echo $row1['po'];?></td>
            <!-- <td align="center"><?php echo $row1['no_item'];?></td> -->
            <td align="center"><?php echo $row1['no_order'];?></td>
            <td align="center" valign="top"><?php echo $row1['no_hanger'];?></td>
            <td><?php echo $row1['jenis_kain'];?></td>
            <td align="center"><?php echo $row1['lebar'];?></td>
            <td align="center"><?php echo $row1['gramasi'];?></td>
            <td align="center"><?php echo $row1['lot'];?></td>
            <td align="center"><?php echo $row1['warna'];?></td>
            <td align="right"><?php echo $row1['qty_order'];?></td>
            <td align="right"><?php echo $row1['qty_order2'];?></td>
            <td align="right"><?php echo $row1['qty_kirim'];?></td>
            <td align="right"><?php echo $row1['qty_kirim2'];?></td>
            <td align="right"><?php echo $row1['qty_claim'];?></td>
            <td align="right"><?php echo $row1['qty_claim2'];?></td>
            <td align="right"><?php echo $row1['qty_lolos'];?></td>
            <td align="center"><?php echo $tjawab;?></td>
            <td><?php echo $row1['masalah_dominan'];?></td>
            <td><?php echo $row1['masalah'];?></td>
            <td><?php echo $row1['penyebab'];?></td>
            <td><?php echo $row1['kategori'];?></td> <!-- route cause -->
            <td>
                <?php if($row1['solusi'] == "PERBAIKAN GARMENT") { ?>

                  <a href="#" id='' nsp-id="<?=$row1['id'];?>" class="detail_solusi_perbaikan_garment"><?php echo $row1['solusi'];?></a>
                  <a href="#" id='' nsp-id="<?=$row1['id'];?>" class="edit_detail_solusi_perbaikan_garment btn btn-info btn-xs">Edit</a>
                  <!-- <a href="#" id='' class="detail_solusi_perbaikan_garment" data-toggle="modal" data-target="#DataSolusi" data-no_item="<?php echo $row1['no_item']; ?>"><?php echo $row1['solusi'];?></a> -->
                
                <?php }elseif($row1['solusi'] == "DEBIT NOTE"){?>
                  <a href="#" id='' nsp-id="<?=$row1['id'];?>" class="detail_solusi_debit_note"><?php echo $row1['solusi'];?></a>
                  <a href="#" id='' nsp-id="<?=$row1['id'];?>" class="edit_detail_solusi_debit_note btn btn-info btn-xs">Edit</a>
                  
                <?php }else{?>
                  <?php echo $row1['solusi'];?>
                <?php }?>
            </td>
            
                  <!-- <td><?php //echo $row1['solusi'];?></td> -->
            <td><?php if($row1['personil2']!=""){echo $row1['personil'].",".$row1['personil2'];}else{echo $row1['personil'];}?></td>
            <td><?php echo $row1['pejabat'];?></td>
            <td><?php if($row1['sts']=="1"){echo "Lolos QC";}else if($row1['sts_disposisiqc']=="1"){echo "Disposisi QC";}else if($row1['sts_disposisipro']=="1"){echo "Disposisi Produksi";}?><?php if($row1['sts_nego']=="1"){echo ", Negosiasi Aftersales";}?></td>
            <td><?php if($row1['status_penghubung']=='terima'){
              echo '&#10004';
            }else if($row1['status_penghubung']=='tolak'){
              echo 'X';
            }else{
              echo '';
            }?></td>
            <td><?php echo $row1['no_ncp'];?></td>
            <td><?php echo $row1['masalah_utama'];?></td>
            <td><?php echo $row1['akar_masalah'];?></td>
            <td><?php echo $row1['solusi_panjang'];?></td>
            <td><?php echo $row1['ket'];?></td>
            </tr>
          <?php	$no++;  }} ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
  <!-- end of data kpe -->

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

<!-- Create -->
<div id="DataSolusiPerbaikanGarment" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>	
<div id="DataSolusiDebitNote" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>	

<!-- Edit -->
<div id="EditDataSolusiPerbaikanGarment" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>	
<div id="EditDataSolusiDebitNote" class="modal fade modal-3d-slit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>	




<script type="text/javascript">
    function confirm_delete(delete_url)
    {
      $('#modal_del').modal('show', {backdrop: 'static'});
      document.getElementById('delete_link').setAttribute('href' , delete_url);
    }
</script>	
<script>
		$(document).ready(function() {
			$('[data-toggle="tooltip"]').tooltip();
		});

	</script>

<script>
function formatRupiah(input) {
  if(input.value != "") {
    // Menghilangkan tanda titik dan koma
    var value = input.value.replace(/[^\d]/g, '');

    // Mengonversi nilai menjadi angka
    var amount = parseInt(value);

    // Mengonversi angka menjadi format rupiah
    var formattedValue = amount.toLocaleString('id-ID');

    // Memasukkan nilai yang sudah diformat kembali ke dalam input field
    input.value = formattedValue;
  }
}
</script>
</body>
</html>