<style>
input[type="number"]::-webkit-outer-spin-button, input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
 
input[type="number"] {
    -moz-appearance: textfield;
}
.btn-success {
margin :1px
}
</style>
<script>
	
function roundToTwo(num) {    
    return +(Math.round(num + "e+2")  + "e-2");
}
</script>
<?php
ini_set("error_reporting", 1);
session_start();
include"koneksi.php";
$nodemand=$_GET['nodemand'];
$no_test=$_GET['no_test'];

$modifiedData = str_replace('00000', '/',$_GET['no_test']);
$modifiedUrl = str_replace('77777', ' ', $modifiedData);


//$notest= isset($_POST['no_test']) ? $_POST['no_test'] : '';
//$sqlCek=mysqli_query("SELECT * FROM tbl_tq_nokk WHERE nokk='$nokk' and no_test='$notest' ORDER BY id DESC LIMIT 1");
//$cek=mysqli_num_rows($sqlCek);
//$rcek=mysqli_fetch_array($sqlCek);
$qryNoKK = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT * 
     FROM db_qc.tbl_tq_first_lot
     WHERE no_report_fl = '$modifiedUrl'"
);

$NoKKcek = ($qryNoKK && sqlsrv_has_rows($qryNoKK)) ? 1 : 0;
$rNoKK   = sqlsrv_fetch_array($qryNoKK, SQLSRV_FETCH_ASSOC);
// if ($rNoKK === false) {
//     echo "<pre>";
//     print_r(sqlsrv_errors());
//     echo "</pre>";
//     exit;
// }

$pos=strpos($rNoKK['pelanggan'], "/");
$posbuyer=substr($rNoKK['pelanggan'],$pos+1,50);
$buyer=str_replace("'","''",$posbuyer);
?>	
<?php 
$sqlCek1 = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT TOP 1 *,
        LTRIM(RTRIM(CONCAT(
            COALESCE(NULLIF(fc_note,''),'')        ,' ',
            COALESCE(NULLIF(ph_note,''),'')        ,' ',
            COALESCE(NULLIF(abr_note,''),'')       ,' ',
            COALESCE(NULLIF(bas_note,''),'')       ,' ',
            COALESCE(NULLIF(dry_note,''),'')       ,' ',
            COALESCE(NULLIF(fla_note,''),'')       ,' ',
            COALESCE(NULLIF(fwe_note,''),'')       ,' ',
            COALESCE(NULLIF(fwi_note,''),'')       ,' ',
            COALESCE(NULLIF(burs_note,''),'')      ,' ',
            COALESCE(NULLIF(repp_note,''),'')      ,' ',
            COALESCE(NULLIF(wick_note,''),'')      ,' ',
            COALESCE(NULLIF(absor_note,''),'')     ,' ',
            COALESCE(NULLIF(apper_note,''),'')     ,' ',
            COALESCE(NULLIF(fiber_note,''),'')     ,' ',
            COALESCE(NULLIF(pillb_note,''),'')     ,' ',
            COALESCE(NULLIF(pillm_note,''),'')     ,' ',
            COALESCE(NULLIF(pillr_note,''),'')     ,' ',
            COALESCE(NULLIF(thick_note,''),'')     ,' ',
            COALESCE(NULLIF(growth_note,''),'')    ,' ',
            COALESCE(NULLIF(recover_note,''),'')   ,' ',
            COALESCE(NULLIF(stretch_note,''),'')   ,' ',
            COALESCE(NULLIF(sns_note,''),'')       ,' ',
            COALESCE(NULLIF(snab_note,''),'')      ,' ',
            COALESCE(NULLIF(snam_note,''),'')      ,' ',
            COALESCE(NULLIF(snap_note,''),'')      ,' ',
            COALESCE(NULLIF(wash_note,''),'')      ,' ',
            COALESCE(NULLIF(water_note,''),'')     ,' ',
            COALESCE(NULLIF(acid_note,''),'')      ,' ',
            COALESCE(NULLIF(alkaline_note,''),'')  ,' ',
            COALESCE(NULLIF(crock_note,''),'')     ,' ',
            COALESCE(NULLIF(phenolic_note,''),'')  ,' ',
            COALESCE(NULLIF(cm_printing_note,''),''),' ',
            COALESCE(NULLIF(cm_dye_note,''),'')    ,' ',
            COALESCE(NULLIF(light_note,''),'')     ,' ',
            COALESCE(NULLIF(light_pers_note,''),''),' ',
            COALESCE(NULLIF(saliva_note,''),'')    ,' ',
            COALESCE(NULLIF(h_shrinkage_note,''),''),' ',
            COALESCE(NULLIF(fibre_note,''),'')
        ))) AS note_g
     FROM db_qc.tbl_tq_test_fl
     WHERE id_nokk = '".$rNoKK['id']."'
     ORDER BY id DESC"
);

$cek1  = ($sqlCek1 && sqlsrv_has_rows($sqlCek1)) ? 1 : 0;
$rcek1 = sqlsrv_fetch_array($sqlCek1, SQLSRV_FETCH_ASSOC);
if ($rcek1 === false) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    exit;
}
$sqlCekR = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT *,
        LTRIM(RTRIM(CONCAT(
            COALESCE(NULLIF(rfc_note,''),'')        ,' ',
            COALESCE(NULLIF(rph_note,''),'')        ,' ',
            COALESCE(NULLIF(rabr_note,''),'')       ,' ',
            COALESCE(NULLIF(rbas_note,''),'')       ,' ',
            COALESCE(NULLIF(rdry_note,''),'')       ,' ',
            COALESCE(NULLIF(rfla_note,''),'')       ,' ',
            COALESCE(NULLIF(rfwe_note,''),'')       ,' ',
            COALESCE(NULLIF(rfwi_note,''),'')       ,' ',
            COALESCE(NULLIF(rburs_note,''),'')      ,' ',
            COALESCE(NULLIF(rrepp_note,''),'')      ,' ',
            COALESCE(NULLIF(rwick_note,''),'')      ,' ',
            COALESCE(NULLIF(rabsor_note,''),'')     ,' ',
            COALESCE(NULLIF(rapper_note,''),'')     ,' ',
            COALESCE(NULLIF(rfiber_note,''),'')     ,' ',
            COALESCE(NULLIF(rpillb_note,''),'')     ,' ',
            COALESCE(NULLIF(rpillm_note,''),'')     ,' ',
            COALESCE(NULLIF(rpillr_note,''),'')     ,' ',
            COALESCE(NULLIF(rthick_note,''),'')     ,' ',
            COALESCE(NULLIF(rgrowth_note,''),'')    ,' ',
            COALESCE(NULLIF(rrecover_note,''),'')   ,' ',
            COALESCE(NULLIF(rstretch_note,''),'')   ,' ',
            COALESCE(NULLIF(rsns_note,''),'')       ,' ',
            COALESCE(NULLIF(rsnab_note,''),'')      ,' ',
            COALESCE(NULLIF(rsnam_note,''),'')      ,' ',
            COALESCE(NULLIF(rsnap_note,''),'')      ,' ',
            COALESCE(NULLIF(rwash_note,''),'')      ,' ',
            COALESCE(NULLIF(rwater_note,''),'')     ,' ',
            COALESCE(NULLIF(racid_note,''),'')      ,' ',
            COALESCE(NULLIF(ralkaline_note,''),'')  ,' ',
            COALESCE(NULLIF(rcrock_note,''),'')     ,' ',
            COALESCE(NULLIF(rphenolic_note,''),'')  ,' ',
            COALESCE(NULLIF(rcm_printing_note,''),''),' ',
            COALESCE(NULLIF(rcm_dye_note,''),'')    ,' ',
            COALESCE(NULLIF(rlight_note,''),'')     ,' ',
            COALESCE(NULLIF(rlight_pers_note,''),''),' ',
            COALESCE(NULLIF(rsaliva_note,''),'')    ,' ',
            COALESCE(NULLIF(rh_shrinkage_note,''),''),' ',
            COALESCE(NULLIF(rfibre_note,''),'')
        ))) AS rnote_g
     FROM db_qc.tbl_tq_randomtest
     WHERE no_item = '".$rNoKK['no_item']."'
        OR no_hanger = '".$rNoKK['no_hanger']."'"
);

$cekR  = ($sqlCekR && sqlsrv_has_rows($sqlCekR)) ? 1 : 0;
$rcekR = sqlsrv_fetch_array($sqlCekR, SQLSRV_FETCH_ASSOC);
if ($rcekR === false) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    exit;
}
$sqlCekD = sqlsrv_query(
    $con_db_qc_sqlsrv,
    "SELECT TOP 1 *,
        LTRIM(RTRIM(CONCAT(
            COALESCE(NULLIF(dfc_note,''),'')        ,' ',
            COALESCE(NULLIF(dph_note,''),'')        ,' ',
            COALESCE(NULLIF(dabr_note,''),'')       ,' ',
            COALESCE(NULLIF(dbas_note,''),'')       ,' ',
            COALESCE(NULLIF(ddry_note,''),'')       ,' ',
            COALESCE(NULLIF(dfla_note,''),'')       ,' ',
            COALESCE(NULLIF(dfwe_note,''),'')       ,' ',
            COALESCE(NULLIF(dfwi_note,''),'')       ,' ',
            COALESCE(NULLIF(dburs_note,''),'')      ,' ',
            COALESCE(NULLIF(drepp_note,''),'')      ,' ',
            COALESCE(NULLIF(dwick_note,''),'')      ,' ',
            COALESCE(NULLIF(dabsor_note,''),'')     ,' ',
            COALESCE(NULLIF(dapper_note,''),'')     ,' ',
            COALESCE(NULLIF(dfiber_note,''),'')     ,' ',
            COALESCE(NULLIF(dpillb_note,''),'')     ,' ',
            COALESCE(NULLIF(dpillm_note,''),'')     ,' ',
            COALESCE(NULLIF(dpillr_note,''),'')     ,' ',
            COALESCE(NULLIF(dthick_note,''),'')     ,' ',
            COALESCE(NULLIF(dgrowth_note,''),'')    ,' ',
            COALESCE(NULLIF(drecover_note,''),'')   ,' ',
            COALESCE(NULLIF(dstretch_note,''),'')   ,' ',
            COALESCE(NULLIF(dsns_note,''),'')       ,' ',
            COALESCE(NULLIF(dsnab_note,''),'')      ,' ',
            COALESCE(NULLIF(dsnam_note,''),'')      ,' ',
            COALESCE(NULLIF(dsnap_note,''),'')      ,' ',
            COALESCE(NULLIF(dwash_note,''),'')      ,' ',
            COALESCE(NULLIF(dwater_note,''),'')     ,' ',
            COALESCE(NULLIF(dacid_note,''),'')      ,' ',
            COALESCE(NULLIF(dalkaline_note,''),'')  ,' ',
            COALESCE(NULLIF(dcrock_note,''),'')     ,' ',
            COALESCE(NULLIF(dphenolic_note,''),'')  ,' ',
            COALESCE(NULLIF(dcm_printing_note,''),''),' ',
            COALESCE(NULLIF(dcm_dye_note,''),'')    ,' ',
            COALESCE(NULLIF(dlight_note,''),'')     ,' ',
            COALESCE(NULLIF(dlight_pers_note,''),''),' ',
            COALESCE(NULLIF(dsaliva_note,''),'')    ,' ',
            COALESCE(NULLIF(dh_shrinkage_note,''),''),' ',
            COALESCE(NULLIF(dfibre_note,''),'')
        ))) AS dnote_g
     FROM db_qc.tbl_tq_disptest_fl
     WHERE id_nokk = '".$rNoKK['id']."'
     ORDER BY id DESC"
);

$cekD  = ($sqlCekD && sqlsrv_has_rows($sqlCekD)) ? 1 : 0;
$rcekD = sqlsrv_fetch_array($sqlCekD, SQLSRV_FETCH_ASSOC);
if ($rcekD === false) {
    echo "<pre>";
    print_r(sqlsrv_errors());
    echo "</pre>";
    exit;
}
?>
<form class="form-horizontal" action="" method="post" enctype="multipart/form-data" name="form0" id="form0">
 <div class="box box-success" style="width: 98%;">
   <div class="box-header with-border">
    <h3 class="box-title">Report Adidas</h3>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse" ><i class="fa fa-minus"></i></button>
    </div>
  </div>
  <?php if($rcek1['status']=="" and $no_test!=""){?>
  <div class="alert alert-warning alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
         <h4><i class="icon fa fa-info"></i> Informasi</h4>

        <p>Tidak dapat cetak laporan karena masih terdapat test yang belum selesai. <br>
        Periksa kembali test tersebut.</p>
      </div>
  <?php } ?>
  
  <div class="box-body"> 
	 <div class="col-md-6">
	  <!--	 
	  <div class="form-group">
		  <label for="no_order" class="col-sm-3 control-label">No Order</label>
		  <div class="col-sm-4">
			<input name="no_order" type="text" class="form-control" id="no_order" 
			value="" placeholder="No Order">
		  </div>				   
		</div>	 
      -->
      <script>
				function handleChange() {
					var input = document.getElementById("no_test");
					var modifiedValue = input.value.replaceAll("/", "00000").replaceAll(" ", "77777");
					window.location = "ReportNewFL-" + modifiedValue;
				}

                function handleBlur() {
					var input = document.getElementById("no_test");
					var modifiedValue = input.value.replaceAll("/", "00000").replaceAll(" ", "77777");
					window.location = "ReportNewFL-" + modifiedValue;
				}
			</script>
                    <div class="form-group">
                        <label for="no_test" class="col-sm-3 control-label">Search</label>
                        <div class="col-sm-5">
                            <div class="input-group">

                            <input name="no_test" type="text" class="form-control" id="no_test" placeholder="No Report FL"  
                onchange="handleChange()" value="<?php echo $notest;?>"  onBlur="handleBlur()" >
                                <!--
                                <input name="no_test" type="text" class="form-control" id="no_test" 
                                    onchange="window.location='ReportNewFL-'+this.value" onBlur="window.location='ReportNewFL-'+this.value" value="<?php if($_GET['no_test']!=""){echo $_GET['no_test'];}?>" placeholder="" required >
                                -->

                                <span class="input-group-addon"><a href="#" data-toggle="modal" data-target="#myModal1"><i class="fa fa-arrow-circle-right"></i> </a></span>
				            </div>	  
		                </div>
                    </div> 
                    <?php if($no_test!=""){ ?> 
                    <div class="form-group">
                    <label for="nodemand" class="col-sm-3 control-label">No Demand</label>
                    <div class="col-sm-5">
                        <?php 
                        ?>
                        <input name="nodemand" type="text" class="form-control" id="nodemand" placeholder="No Demand" 
                            value="<?php if($rNoKK['nodemand_new']!=''){echo $rNoKK['nodemand_new'];}else if($rNoKK['nodemand_new']==''){echo $rNoKK['nodemand'];}?>" readonly="readonly">
                    </div>				   
                    </div>

                    <div class="form-group">
                    <label for="nodemand" class="col-sm-3 control-label">No Report FL</label>
                    <div class="col-sm-9">
                        <?php 
                        ?>
                        <input type="text" value="<?=$rNoKK['no_report_fl']?>"  disabled>  
        
                    </div>				   
                    </div>


                    <?php if($rNoKK['nodemand_new']!=''){?>
                    <div class="form-group">
                        <label for="nodemand_old" class="col-sm-3 control-label">No Demand Old</label>
                            <div class="col-sm-5">
                                <input name="nodemand_old" type="text" class="form-control" id="nodemand_old" placeholder="No Demand Old"
                                value="<?php if($rNoKK['nodemand_new']!=''){echo $rNoKK['nodemand'];} ?>" readonly="readonly" >
                            </div>
                    </div>
                    <?php } ?>
                    <div class="form-group">
                            <label for="nokk" class="col-sm-3 control-label">No Prod. Order</label>
                            <div class="col-sm-3">
                                <input name="nokk" type="text" class="form-control" id="nokk" placeholder="No Prod. Order" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['nokk'];}?>" readonly="readonly" >
                            </div>				   
                            </div>
                    <div class="form-group">
                            <label for="no_order" class="col-sm-3 control-label">No Order</label>
                            <div class="col-sm-4">
                                <input name="no_order" type="text" class="form-control" id="no_order" placeholder="No Order" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['no_order'];}?>" readonly="readonly">
                            </div>				   
                            </div>
                        <div class="form-group">
                            <label for="no_po" class="col-sm-3 control-label">Pelanggan</label>
                            <div class="col-sm-8">
                                <input name="pelanggan" type="text" class="form-control" id="no_po" placeholder="Pelanggan" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['pelanggan'];}?>" readonly="readonly" >
                            </div>				   
                            </div>	           
                            <div class="form-group">
                            <label for="no_hanger" class="col-sm-3 control-label">No Hanger / No Item</label>
                            <div class="col-sm-3">
                                <input name="no_hanger" type="text" class="form-control" id="no_hanger" placeholder="No Hanger" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['no_hanger'];}?>" readonly="readonly">  
                            </div>
                            <div class="col-sm-3">
                            <input name="no_item" type="text" class="form-control" id="no_item" placeholder="No Item" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['no_item'];}?>" readonly="readonly">
                            </div>	
                            </div>
                            <div class="form-group">
                            <label for="jns_kain" class="col-sm-3 control-label">Jenis Kain</label>
                            <div class="col-sm-8">
                                <textarea name="jns_kain" readonly="readonly" class="form-control" id="jns_kain" placeholder="Jenis Kain"><?php if($NoKKcek>0){echo $rNoKK['jenis_kain'];}?></textarea>
                                </div>
                            </div>
                    <div class="form-group">
                            <label for="warna" class="col-sm-3 control-label">Warna</label>
                            <div class="col-sm-8">
                                <textarea name="warna" readonly="readonly" class="form-control" id="warna" placeholder="Warna"><?php if($NoKKcek>0){echo $rNoKK['warna'];}?></textarea>
                            </div>				   
                            </div>
                    <div class="form-group">
                            <label for="lot" class="col-sm-3 control-label">Prod. Order / Lot</label>
                            <div class="col-sm-3">
                                <input name="lot" type="text" class="form-control" id="lot" placeholder="Lot" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['lot'];}?>" readonly="readonly" >
                            </div>				   
                            </div>
                    
                    <div class="form-group">
                            <label for="buyer" class="col-sm-3 control-label">Buyer</label>
                            <div class="col-sm-8">
                                <input name="buyer" type="text" class="form-control" id="buyer" placeholder="Buyer" 
                                value="<?php if($NoKKcek>0){echo $rNoKK['buyer'];}?>" readonly="readonly" >
                            </div>				   
                            </div>
                    <?php } ?>
	 </div>
	
</div>	 
   <div class="box-footer"> 
	    <?php if($rcek1['status']!=""){ ?>

 	        <a href="pages/cetak/cetak_report_adidas_new_fl.php?idkk=<?php echo $rNoKK['id'];?>&noitem=<?php echo $rNoKK['no_item'];?>&nohanger=<?php echo $rNoKK['no_hanger'];?>" target="_blank" class="btn btn-success" <?php if($rcek1['status']=="Reject"):?> onclick="return confirm('Summary Test Quality berstatus Reject, apakah ingin tetap mencetak laporan?')" <?php endif; ?>> <i class="fa fa-print"></i> Print Report Adidas</a>
           
            <a target="_blank" href="pages/cetak/test_report_fl.php?id=<?php echo $rNoKK['no_report_fl'];?>">
            <i class="fa fa-print" class="btn btn-success"></i> Report </a>
           
            <!--
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Converse</a>
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Lululemon</a>
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Mizano</a>
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Aloyoga</a>
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Beyond Yoga</a>
            <a href="pages/cetak/cetak_report_fl.php" class="btn btn-success"><i class="fa fa-print"></i> Report Athleta</a>
            
            <a href="" class="btn btn-warning"></i> Upload Report Eksternal PDF</a>
            --> 

	        <!-- <a href="pages/cetak/cetak_reportfunc_adidas.php?idkk=<?php echo $rNoKK['id'];?>&noitem=<?php echo $rNoKK['no_item'];?>&nohanger=<?php echo $rNoKK['no_hanger'];?>" target="_blank" class="btn btn-success" <?php if($rcek1['status']=="Reject"):?> onclick="return confirm('Summary Test Quality berstatus Reject, apakah ingin tetap mencetak laporan?')"<?php endif; ?>><i class="fa fa-print"></i> Print Report Functional</a> -->
        <?php }?>
        <!--<?php if(($buyer=="LULULEMON ATHLETICA" OR $buyer=="LULULEMON") AND $rcek1['status']!=""){ ?>
            <a href="pages/cetak/cetak_report_lululemon.php?idkk=<?php echo $rNoKK['id'];?>&noitem=<?php echo $rNoKK['no_item'];?>&nohanger=<?php echo $rNoKK['no_hanger'];?>" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Print Report Lululemon (On Progress)</a>
	        <a href="#"><i class="fa fa-print"></i> Print Report Functional</a>
        <?php }?>-->
        <!--<?php if($rNoKK['buyer']=="UNDER ARMOUR" and $rcek1['status']!=""){ ?>
            <a href="#"><i class="fa fa-print"></i> Print Report Physical</a>
	        <a href="#"><i class="fa fa-print"></i> Print Report Functional</a>
        <?php }?>-->
   </div>
    <!-- /.box-footer -->
</div>
</form>         
				  </div>
                </div>
            </div>
        </div>
<!-- Modal -->
<div class="modal fade modal-3d-slit" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width:90%">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Data Kain Masuk</h4>
			</div>
			<div class="modal-body">
				<table id="lookup" class="table table-bordered table-hover table-striped" width="100%">
					<thead>
						<tr>
							<th width="5%">No</th>
							<th width="10%">No Order</th>
                            <th width="8%">No Report FL</th>
                            <!--
                            <th width="8%">No Test</th>-->
                            <th width="14%">No Demand</th>
							<th width="14%">No KK</th>
							<th width="31%">Jenis Kain</th>
							<th width="22%">Lot</th>
							<th width="8%">No Hanger</th>
							<th width="8%">No Item</th>
                            <th width="8%">Warna</th>
						</tr>
					</thead>
					<tbody>
						<?php
                                //Data ditampilkan ke tabel
                                $sql = sqlsrv_query($con_db_qc_sqlsrv," SELECT a.*
                                    FROM db_qc.tbl_tq_first_lot a
                                    INNER JOIN db_qc.tbl_tq_test_fl b ON a.id=b.id_nokk
                                    WHERE a.nodemand <> ''
                                ");

                                $no = 1;
                                while ($r = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC)) {

                                    ?>
						<tr class="pilih-no_test" data-no_test="<?php echo $r['no_report_fl']; ?>">
							<td align="center">
								<?php echo $no; ?>
							</td>
							<td align="center">
								<?php echo $r['no_order']; ?>
							</td>
                            <td align="center">
								<b><?php echo $r['no_report_fl']; ?></b>
							</td>
                            <!--
                            <td align="center">
								<?php echo $r['no_test']; ?>
							</td>-->
                            <td align="center">
								<?php echo $r['nodemand']; ?>
							</td>
							<td align="center">
								<?php echo $r['nokk']; ?>
							</td>
							<td>
								<?php echo $r['jenis_kain']; ?>
							</td>
							<td align="center">
								<?php echo $r['lot']; ?>
							</td>
							<td align="right">
								<?php echo $r['no_hanger']; ?>
							</td>
							<td align="center">
								<?php echo $r['no_item']; ?>
							</td>
                            <td align="center">
								<?php echo $r['warna']; ?>
							</td>
						</tr>
						<?php
                                    $no++;
                                }
                                ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>