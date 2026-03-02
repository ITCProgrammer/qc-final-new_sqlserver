<?PHP
ini_set("error_reporting", 1);
session_start();
include "koneksi.php";
set_time_limit(0);
?>
<?php
$Awal = isset($_POST['awal']) ? $_POST['awal'] : '';
$Akhir = isset($_POST['akhir']) ? $_POST['akhir'] : '';
$Customer = isset($_POST['customer']) ? $_POST['customer'] : '';
$Buyer = isset($_POST['buyer']) ? $_POST['buyer'] : '';
$PO = isset($_POST['po']) ? $_POST['po'] : '';
$Item = isset($_POST['item']) ? $_POST['item'] : '';

if (!function_exists('qcf_db2_quote')) {
  function qcf_db2_quote($value)
  {
    return str_replace("'", "''", (string) $value);
  }
}

if (!function_exists('qcf_decimal_integer_part')) {
  function qcf_decimal_integer_part($value)
  {
    if ($value === null || $value === '') {
      return '0';
    }
    $parts = explode('.', (string) $value, 2);
    return $parts[0];
  }
}

if (!function_exists('qcf_num_or_zero')) {
  function qcf_num_or_zero($value)
  {
    if ($value === null || $value === '') {
      return 0;
    }
    return $value;
  }
}

if (!function_exists('qcf_map_get')) {
  function qcf_map_get($map, $key, $field, $default = '')
  {
    if (isset($map[$key]) && isset($map[$key][$field])) {
      return $map[$key][$field];
    }
    return $default;
  }
}

if (!function_exists('qcf_db2_in_list')) {
  function qcf_db2_in_list($values)
  {
    $quoted = array();
    foreach ($values as $value) {
      $quoted[] = "'" . qcf_db2_quote($value) . "'";
    }
    return implode(',', $quoted);
  }
}

$db2Conn = function_exists('qcf_get_db2_conn') ? qcf_get_db2_conn() : $conn1;
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title></title>

</head>

<body>
  <div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">Silahkan masukkan data yang ingin dicari</h3>
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
              <input name="awal" type="text" class="form-control pull-right" id="datepicker" placeholder="Tanggal Awal"
                value="<?php echo $Awal; ?>" autocomplete="off" />
            </div>
          </div>
          <div class="col-sm-2">
            <!-- <input name="customer" type="text" class="form-control" id="customer" placeholder="Customer" value="<?php echo $Customer; ?>" /> -->
            <select class="form-control select2" name="customer" id="customer">
              <option value="">Pilih Langganan</option>
              <?php
              $qryc = "SELECT ORDERPARTNER.CUSTOMERSUPPLIERCODE,
                  BUSINESSPARTNER.LEGALNAME1 
                  FROM ORDERPARTNER ORDERPARTNER
                  LEFT JOIN BUSINESSPARTNER BUSINESSPARTNER
                  ON ORDERPARTNER.ORDERBUSINESSPARTNERNUMBERID = BUSINESSPARTNER.NUMBERID 
                  WHERE ORDERPARTNER.CUSTOMERSUPPLIERTYPE ='1'
                  ORDER BY BUSINESSPARTNER.LEGALNAME1 ASC";
              $stmt = db2_exec($conn1, $qryc, array('cursor' => DB2_SCROLLABLE));
              while ($rc = db2_fetch_assoc($stmt)) {
                ?>
                <option value="<?php echo $rc['LEGALNAME1']; ?>" <?php if ($Customer == $rc['LEGALNAME1']) {
                     echo "SELECTED";
                   } ?>>
                  <?php echo $rc['LEGALNAME1']; ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="col-sm-2">
            <select class="form-control select2" name="buyer" id="buyer">
              <option value="">Pilih Buyer</option>
              <?php
              $qryb = "SELECT 
                DISTINCT(ORDERPARTNERBRAND.LONGDESCRIPTION) AS BUYER
                FROM ORDERPARTNER ORDERPARTNER
                LEFT JOIN ORDERPARTNERBRAND ORDERPARTNERBRAND
                ON ORDERPARTNER.CUSTOMERSUPPLIERCODE = ORDERPARTNERBRAND.ORDPRNCUSTOMERSUPPLIERCODE
                WHERE ORDERPARTNER.CUSTOMERSUPPLIERTYPE ='1' AND ORDERPARTNERBRAND.LONGDESCRIPTION IS NOT NULL
                ORDER BY ORDERPARTNERBRAND.LONGDESCRIPTION ASC";
              $stmt1 = db2_exec($conn1, $qryb, array('cursor' => DB2_SCROLLABLE));
              while ($rb = db2_fetch_assoc($stmt1)) {
                ?>
                <option value="<?php echo $rb['BUYER']; ?>" <?php if ($Buyer == $rb['BUYER']) {
                     echo "SELECTED";
                   } ?>>
                  <?php echo $rb['BUYER']; ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <!-- /.input group -->
        </div>
        <div class="form-group">
          <div class="col-sm-2">
            <div class="input-group date">
              <div class="input-group-addon"> <i class="fa fa-calendar"></i> </div>
              <input name="akhir" type="text" class="form-control pull-right" id="datepicker1"
                placeholder="Tanggal Akhir" value="<?php echo $Akhir; ?>" autocomplete="off" />
            </div>
          </div>
          <div class="col-sm-2">
            <input name="po" type="text" class="form-control pull-right" placeholder="PO Number"
              value="<?php echo $PO; ?>" />
          </div>
          <div class="col-sm-2">
            <input name="item" type="text" class="form-control pull-right" placeholder="No Item"
              value="<?php echo $Item; ?>" />
          </div>
          <!-- /.input group -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <div class="pull-right">
            <button type="submit" class="btn btn-success" name="cari"><i class="fa fa-search"></i> Cari Data</button>
          </div>
        </div>
        <!-- /.box-footer -->
      </div>
    </form>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Summary Inspection Packing Report</h3><br>
          <b>Tanggal Inspeksi :
            <?php echo $_POST['awal']; ?> s.d.
            <?php echo $_POST['akhir']; ?>
          </b> <br><br>
          <?php if ($_POST['customer'] != '') { ?><b>Customer :
              <?php echo $_POST['customer']; ?>
            </b><br>
          <?php } ?>
          <?php if ($_POST['buyer'] != '') { ?><b>Buyer :
              <?php echo $_POST['buyer']; ?>
            </b><br>
          <?php } ?>
          <?php if ($_POST['item'] != '') { ?><b>No Item :
              <?php echo $_POST['item']; ?>
            </b>
          <?php } ?>
          <div class="pull-right">
            <a href="pages/cetak/excel_summary_inspekpacking_langganan.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&buyer=<?php echo $_POST['buyer']; ?>"
              class="btn btn-success <?php if ($_POST['awal'] == "" or $_POST['buyer'] == "") {
                echo "disabled";
              } ?>" target="_blank">Excel Summary Langganan</a>
            <a href="pages/cetak/excel_summary_inspekpacking_item.php?awal=<?php echo $_POST['awal']; ?>&akhir=<?php echo $_POST['akhir']; ?>&buyer=<?php echo $_POST['buyer']; ?>&item=<?php echo $_POST['item']; ?>"
              class="btn btn-success <?php if ($_POST['awal'] == "" or $_POST['buyer'] == "") {
                echo "disabled";
              } ?>" target="_blank">Excel Summary Item</a>
          </div>
        </div>
        <div class="box-body">
          <table id="example3" class="table table-bordered table-hover table-striped display nowrap" width="100%">
            <thead class="bg-blue">
              <tr>
                <th width="24">
                  <div align="center">No</div>
                </th>
                <th width="24">
                  <div align="center">UOM</div>
                </th>
                <th width="78">
                  <div align="center">No Demand</div>
                </th>
                <th width="78">
                  <div align="center">Buyer</div>
                </th>
                <th width="78">
                  <div align="center">Langganan</div>
                </th>
                <th width="78">
                  <div align="center">Hanger</div>
                </th>
                <th width="78">
                  <div align="center">Item</div>
                </th>
                <th width="100">
                  <div align="center">Description</div>
                </th>
                <th width="100">
                  <div align="center">Style/Season</div>
                </th>
                <th width="88">
                  <div align="center">Color</div>
                </th>
                <th width="90">
                  <div align="center">PO Number</div>
                </th>
                <th width="80">
                  <div align="center">Bon Order</div>
                </th>
                <th width="80">
                  <div align="center">LOT</div>
                </th>
                <th width="80">
                  <div align="center">Tgl Inspek</div>
                </th>
                <th width="80">
                  <div align="center">Roll</div>
                </th>
                <th width="80">
                  <div align="center">QTY</div>
                </th>
                <th width="80">
                  <div align="center">Yard</div>
                </th>
                <th width="80">
                  <div align="center">Lebar</div>
                </th>
                <th width="80">
                  <div align="center">Gramasi</div>
                </th>
                <th width="80">
                  <div align="center">Lebar Inspek</div>
                </th>
                <th width="80">
                  <div align="center">Gramasi Inspek</div>
                </th>
                <th width="25">
                  <div align="center">A Slub</div>
                </th>
                <th width="25">
                  <div align="center">A Barre</div>
                </th>
                <th width="25">
                  <div align="center">A Uneven</div>
                </th>
                <th width="25">
                  <div align="center">A YarnContam</div>
                </th>
                <th width="25">
                  <div align="center">A Neps</div>
                </th>
                <th width="25">
                  <div align="center">B Missing</div>
                </th>
                <th width="25">
                  <div align="center">B Holes</div>
                </th>
                <th width="25">
                  <div align="center">B Streak</div>
                </th>
                <th width="25">
                  <div align="center">B MissKnit</div>
                </th>
                <th width="25">
                  <div align="center">B Knot</div>
                </th>
                <th width="25">
                  <div align="center">B Oil</div>
                </th>
                <th width="25">
                  <div align="center">B Fly</div>
                </th>
                <th width="25">
                  <div align="center">C Hair</div>
                </th>
                <th width="25">
                  <div align="center">C Holes</div>
                </th>
                <th width="25">
                  <div align="center">C Color</div>
                </th>
                <th width="25">
                  <div align="center">C Abra</div>
                </th>
                <th width="25">
                  <div align="center">C Dye</div>
                </th>
                <th width="25">
                  <div align="center">C Wrink</div>
                </th>
                <th width="25">
                  <div align="center">C Bowing</div>
                </th>
                <th width="25">
                  <div align="center">C Pin</div>
                </th>
                <th width="25">
                  <div align="center">C Pick</div>
                </th>
                <th width="25">
                  <div align="center">C Knot</div>
                </th>
                <th width="25">
                  <div align="center">D Uneven</div>
                </th>
                <th width="25">
                  <div align="center">D Stains</div>
                </th>
                <th width="25">
                  <div align="center">D Oil</div>
                </th>
                <th width="25">
                  <div align="center">D Dirt</div>
                </th>
                <th width="25">
                  <div align="center">D Water</div>
                </th>
                <th width="25">
                  <div align="center">E Print</div>
                </th>
                <th width="25">
                  <div align="center">Total Point</div>
                </th>
                <th width="25">
                  <div align="center">Jml A</div>
                </th>
                <th width="25">
                  <div align="center">Kg A</div>
                </th>
                <th width="25">
                  <div align="center">Yd A</div>
                </th>
                <th width="25">
                  <div align="center">Jml B</div>
                </th>
                <th width="25">
                  <div align="center">Kg B</div>
                </th>
                <th width="25">
                  <div align="center">Yd B</div>
                </th>
                <th width="25">
                  <div align="center">Jml C/X</div>
                </th>
                <th width="25">
                  <div align="center">Kg C/X</div>
                </th>
                <th width="25">
                  <div align="center">Yd C/X</div>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $summaryRows = array();
              $rollMap = array();
              $gradeMap = array();
              $defectMap = array();
              $requestAttrMap = array();
              $inspectAttrMap = array();
              $lotMap = array();

              $safeAwal = qcf_db2_quote($Awal);
              $safeAkhir = qcf_db2_quote($Akhir);
              $safeCustomer = qcf_db2_quote($Customer);
              $safeBuyer = qcf_db2_quote($Buyer);
              $safePO = qcf_db2_quote($PO);
              $safeItem = qcf_db2_quote($Item);

              $filterApplied = ($Awal != '' || $Customer != '' || $Buyer != '' || $PO != '' || $Item != '');
              if ($filterApplied) {
                $whereParts = array("LENGTH(TRIM(B.ELEMENTCODE))=13");
                if ($Awal != '' && $Akhir != '') {
                  $whereParts[] = "VARCHAR_FORMAT(B.INSPECTIONSTARTDATETIME,'YYYY-MM-DD') BETWEEN '$safeAwal' AND '$safeAkhir'";
                }
                if ($PO != '') {
                  $whereParts[] = "(D.PO_HEADER = '$safePO' OR D.PO_LINE = '$safePO')";
                }
                if ($Customer != '') {
                  $whereParts[] = "D.LEGALNAME1 = '$safeCustomer'";
                }
                if ($Buyer != '') {
                  $whereParts[] = "D.LONGDESCRIPTION = '$safeBuyer'";
                }
                if ($Item != '') {
                  $whereParts[] = "D.SHORTDESCRIPTION = '$safeItem'";
                }
                $whereSql = implode(' AND ', $whereParts);

                $sql = "SELECT 
                  A.CODE,
                  LEFT(B.INSPECTIONSTARTDATETIME,10) AS TGL_INSPEK,
                  SUM(C.POINTS) AS TOTAL_POIN,
                  SUM(B.LENGTHGROSS) AS TOTAL_YARD,
                  SUM(B.WEIGHTGROSS) AS TOTAL_QTY,
                  D.CODE AS NO_ORDER,
                  D.ORDERPARTNERBRANDCODE,
                  D.LONGDESCRIPTION AS BUYER, 
                  D.LEGALNAME1 AS LANGGANAN, 
                  D.ITEMDESCRIPTION AS JENIS_KAIN,
                  D.PO_HEADER AS PO_HEADER,
                  D.PO_LINE AS PO_LINE,
                  D.INTERNALREFERENCE AS STYLE_SEASON,
                  D.ORDERLINE,
                  D.SUBCODE02,
                  D.SUBCODE03,
                  D.SHORTDESCRIPTION AS NO_ITEM,
                  TRIM(E.LONGDESCRIPTION) AS WARNA
                FROM PRODUCTIONDEMAND A 
                LEFT JOIN ELEMENTSINSPECTION B
                  ON A.CODE = B.DEMANDCODE 
                LEFT JOIN ELEMENTSINSPECTIONEVENT C 
                  ON B.ELEMENTCODE = C.ELEMENTSINSPECTIONELEMENTCODE 
                LEFT JOIN
                  (SELECT SALESORDER.CODE,SALESORDER.ORDERPARTNERBRANDCODE,ORDERPARTNERBRAND.LONGDESCRIPTION,BUSINESSPARTNER.LEGALNAME1,
                  SALESORDER.EXTERNALREFERENCE AS PO_HEADER,SALESORDERLINE.EXTERNALREFERENCE AS PO_LINE,SALESORDERLINE.INTERNALREFERENCE, 
                  SALESORDERLINE.ITEMDESCRIPTION, SALESORDERLINE.ORDERLINE,SALESORDERLINE.SUBCODE02,SALESORDERLINE.SUBCODE03, ORDERITEMORDERPARTNERLINK.SHORTDESCRIPTION 
                  FROM SALESORDER SALESORDER
                  LEFT JOIN SALESORDERLINE SALESORDERLINE ON SALESORDER.CODE = SALESORDERLINE.SALESORDERCODE
                  LEFT JOIN ORDERPARTNERBRAND ORDERPARTNERBRAND ON SALESORDER.ORDPRNCUSTOMERSUPPLIERCODE = ORDERPARTNERBRAND.ORDPRNCUSTOMERSUPPLIERCODE AND SALESORDER.ORDERPARTNERBRANDCODE = ORDERPARTNERBRAND.CODE
                  LEFT JOIN ORDERPARTNER ORDERPARTNER ON SALESORDER.ORDPRNCUSTOMERSUPPLIERCODE = ORDERPARTNER.CUSTOMERSUPPLIERCODE
                  LEFT JOIN BUSINESSPARTNER BUSINESSPARTNER ON ORDERPARTNER.ORDERBUSINESSPARTNERNUMBERID = BUSINESSPARTNER.NUMBERID
                  LEFT JOIN ORDERITEMORDERPARTNERLINK ORDERITEMORDERPARTNERLINK ON SALESORDER.ORDPRNCUSTOMERSUPPLIERCODE = ORDERITEMORDERPARTNERLINK.ORDPRNCUSTOMERSUPPLIERCODE AND SALESORDERLINE.ITEMTYPEAFICODE= ORDERITEMORDERPARTNERLINK.ITEMTYPEAFICODE AND 
                  SALESORDERLINE.SUBCODE01 = ORDERITEMORDERPARTNERLINK.SUBCODE01 AND SALESORDERLINE.SUBCODE02 = ORDERITEMORDERPARTNERLINK.SUBCODE02 AND SALESORDERLINE.SUBCODE03 = ORDERITEMORDERPARTNERLINK.SUBCODE03 AND
                  SALESORDERLINE.SUBCODE04 = ORDERITEMORDERPARTNERLINK.SUBCODE04 AND SALESORDERLINE.SUBCODE05 = ORDERITEMORDERPARTNERLINK.SUBCODE05 AND SALESORDERLINE.SUBCODE06 = ORDERITEMORDERPARTNERLINK.SUBCODE06 AND 
                  SALESORDERLINE.SUBCODE07 = ORDERITEMORDERPARTNERLINK.SUBCODE07 AND SALESORDERLINE.SUBCODE08 = ORDERITEMORDERPARTNERLINK.SUBCODE08 AND SALESORDERLINE.SUBCODE09 = ORDERITEMORDERPARTNERLINK.SUBCODE09 AND 
                  SALESORDERLINE.SUBCODE10 = ORDERITEMORDERPARTNERLINK.SUBCODE10
                  GROUP BY SALESORDER.CODE,SALESORDER.ORDERPARTNERBRANDCODE,ORDERPARTNERBRAND.LONGDESCRIPTION,
                  SALESORDER.EXTERNALREFERENCE,SALESORDERLINE.EXTERNALREFERENCE,SALESORDERLINE.ITEMDESCRIPTION,BUSINESSPARTNER.LEGALNAME1,SALESORDERLINE.INTERNALREFERENCE, SALESORDERLINE.ORDERLINE,SALESORDERLINE.SUBCODE02,SALESORDERLINE.SUBCODE03,ORDERITEMORDERPARTNERLINK.SHORTDESCRIPTION) D 
                  ON A.ORIGDLVSALORDLINESALORDERCODE = D.CODE AND A.ORIGDLVSALORDERLINEORDERLINE = D.ORDERLINE
                LEFT JOIN 
                  (SELECT USERGENERICGROUP.CODE,USERGENERICGROUP.LONGDESCRIPTION FROM USERGENERICGROUP USERGENERICGROUP) E
                  ON A.SUBCODE05 = E.CODE
                WHERE $whereSql
                GROUP BY 
                  A.CODE,
                  LEFT(B.INSPECTIONSTARTDATETIME,10),
                  D.CODE,
                  D.ORDERPARTNERBRANDCODE,
                  D.LONGDESCRIPTION, 
                  D.LEGALNAME1, 
                  D.ITEMDESCRIPTION,
                  D.PO_HEADER,
                  D.PO_LINE,
                  D.SHORTDESCRIPTION,
                  D.INTERNALREFERENCE,
                  D.ORDERLINE,
                  D.SUBCODE02,
                  D.SUBCODE03,
                  E.LONGDESCRIPTION";

                $stmt = db2_exec($db2Conn, $sql, array('cursor' => DB2_SCROLLABLE));
                $demandCodeSet = array();
                if ($stmt) {
                  while ($row = db2_fetch_assoc($stmt)) {
                    $summaryRows[] = $row;
                    $demandCodeSet[$row['CODE']] = $row['CODE'];
                  }
                }

                if (!empty($demandCodeSet)) {
                  $demandCodes = array_values($demandCodeSet);
                  $demandChunks = array_chunk($demandCodes, 300);
                  $inspectionDateFilter = "";
                  if ($Awal != '' && $Akhir != '') {
                    $inspectionDateFilter = " AND VARCHAR_FORMAT(EI.INSPECTIONSTARTDATETIME,'YYYY-MM-DD') BETWEEN '$safeAwal' AND '$safeAkhir' ";
                  }

                  foreach ($demandChunks as $chunkCodes) {
                    $inList = qcf_db2_in_list($chunkCodes);

                    $sqlRoll = "SELECT
                      EI.DEMANDCODE,
                      COUNT(EI.ELEMENTCODE) AS TOTAL_ROLL,
                      MAX(EI.WIDTHNET) AS WIDTHNET
                      FROM ELEMENTSINSPECTION EI
                      WHERE LENGTH(TRIM(EI.ELEMENTCODE))=13
                      AND EI.DEMANDCODE IN ($inList)
                      GROUP BY EI.DEMANDCODE";
                    $stmtRoll = db2_exec($db2Conn, $sqlRoll, array('cursor' => DB2_SCROLLABLE));
                    while ($rowRoll = db2_fetch_assoc($stmtRoll)) {
                      $rollMap[$rowRoll['DEMANDCODE']] = $rowRoll;
                    }

                    $sqlGrade = "SELECT
                      EI.DEMANDCODE,
                      SUM(CASE WHEN EI.QUALITYCODE = '1' THEN 1 ELSE 0 END) AS JML_A,
                      SUM(CASE WHEN EI.QUALITYCODE = '1' THEN EI.WEIGHTNET ELSE 0 END) AS JML_KG_A,
                      SUM(CASE WHEN EI.QUALITYCODE = '1' THEN EI.LENGTHGROSS ELSE 0 END) AS JML_YARD_A,
                      SUM(CASE WHEN EI.QUALITYCODE = '2' THEN 1 ELSE 0 END) AS JML_B,
                      SUM(CASE WHEN EI.QUALITYCODE = '2' THEN EI.WEIGHTNET ELSE 0 END) AS JML_KG_B,
                      SUM(CASE WHEN EI.QUALITYCODE = '2' THEN EI.LENGTHGROSS ELSE 0 END) AS JML_YARD_B,
                      SUM(CASE WHEN EI.QUALITYCODE = '3' THEN 1 ELSE 0 END) AS JML_C,
                      SUM(CASE WHEN EI.QUALITYCODE = '3' THEN EI.WEIGHTNET ELSE 0 END) AS JML_KG_C,
                      SUM(CASE WHEN EI.QUALITYCODE = '3' THEN EI.LENGTHGROSS ELSE 0 END) AS JML_YARD_C
                      FROM ELEMENTSINSPECTION EI
                      WHERE LENGTH(TRIM(EI.ELEMENTCODE))=13
                      AND EI.DEMANDCODE IN ($inList)
                      $inspectionDateFilter
                      GROUP BY EI.DEMANDCODE";
                    $stmtGrade = db2_exec($db2Conn, $sqlGrade, array('cursor' => DB2_SCROLLABLE));
                    while ($rowGrade = db2_fetch_assoc($stmtGrade)) {
                      $gradeMap[$rowGrade['DEMANDCODE']] = $rowGrade;
                    }

                    $sqlDefect = "SELECT
                      EI.DEMANDCODE,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'Y01' THEN EIE.POINTS ELSE 0 END) AS POINTS_Y01,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'Y02' THEN EIE.POINTS ELSE 0 END) AS POINTS_Y02,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'Y03' THEN EIE.POINTS ELSE 0 END) AS POINTS_Y03,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'Y04' THEN EIE.POINTS ELSE 0 END) AS POINTS_Y04,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'Y05' THEN EIE.POINTS ELSE 0 END) AS POINTS_Y05,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T01' THEN EIE.POINTS ELSE 0 END) AS POINTS_T01,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T02' THEN EIE.POINTS ELSE 0 END) AS POINTS_T02,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T03' THEN EIE.POINTS ELSE 0 END) AS POINTS_T03,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T04' THEN EIE.POINTS ELSE 0 END) AS POINTS_T04,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T05' THEN EIE.POINTS ELSE 0 END) AS POINTS_T05,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T06' THEN EIE.POINTS ELSE 0 END) AS POINTS_T06,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'T07' THEN EIE.POINTS ELSE 0 END) AS POINTS_T07,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D01' THEN EIE.POINTS ELSE 0 END) AS POINTS_D01,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D02' THEN EIE.POINTS ELSE 0 END) AS POINTS_D02,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D03' THEN EIE.POINTS ELSE 0 END) AS POINTS_D03,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D04' THEN EIE.POINTS ELSE 0 END) AS POINTS_D04,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D05' THEN EIE.POINTS ELSE 0 END) AS POINTS_D05,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D06' THEN EIE.POINTS ELSE 0 END) AS POINTS_D06,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D07' THEN EIE.POINTS ELSE 0 END) AS POINTS_D07,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D08' THEN EIE.POINTS ELSE 0 END) AS POINTS_D08,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D09' THEN EIE.POINTS ELSE 0 END) AS POINTS_D09,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'D10' THEN EIE.POINTS ELSE 0 END) AS POINTS_D10,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'C01' THEN EIE.POINTS ELSE 0 END) AS POINTS_C01,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'C02' THEN EIE.POINTS ELSE 0 END) AS POINTS_C02,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'C03' THEN EIE.POINTS ELSE 0 END) AS POINTS_C03,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'C04' THEN EIE.POINTS ELSE 0 END) AS POINTS_C04,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'C05' THEN EIE.POINTS ELSE 0 END) AS POINTS_C05,
                      SUM(CASE WHEN EIE.CODEEVENTCODE = 'P01' THEN EIE.POINTS ELSE 0 END) AS POINTS_P01
                      FROM ELEMENTSINSPECTION EI
                      LEFT JOIN ELEMENTSINSPECTIONEVENT EIE
                        ON EI.ELEMENTCODE = EIE.ELEMENTSINSPECTIONELEMENTCODE
                      WHERE LENGTH(TRIM(EI.ELEMENTCODE))=13
                      AND EI.DEMANDCODE IN ($inList)
                      $inspectionDateFilter
                      GROUP BY EI.DEMANDCODE";
                    $stmtDefect = db2_exec($db2Conn, $sqlDefect, array('cursor' => DB2_SCROLLABLE));
                    while ($rowDefect = db2_fetch_assoc($stmtDefect)) {
                      $defectMap[$rowDefect['DEMANDCODE']] = $rowDefect;
                    }

                    $sqlRequestAttr = "SELECT
                      PD.CODE AS DEMANDCODE,
                      MAX(CASE WHEN ADSTORAGE.NAMENAME = 'GSM' THEN ADSTORAGE.VALUEDECIMAL END) AS GSM_REQ,
                      MAX(CASE WHEN ADSTORAGE.NAMENAME = 'Width' THEN ADSTORAGE.VALUEDECIMAL END) AS WIDTH_REQ
                      FROM PRODUCTIONDEMAND PD
                      LEFT JOIN PRODUCT PRODUCT
                        ON PD.ITEMTYPEAFICODE = PRODUCT.ITEMTYPECODE
                        AND PD.SUBCODE01 = PRODUCT.SUBCODE01
                        AND PD.SUBCODE02 = PRODUCT.SUBCODE02
                        AND PD.SUBCODE03 = PRODUCT.SUBCODE03
                        AND PD.SUBCODE04 = PRODUCT.SUBCODE04
                        AND PD.SUBCODE05 = PRODUCT.SUBCODE05
                        AND PD.SUBCODE06 = PRODUCT.SUBCODE06
                        AND PD.SUBCODE07 = PRODUCT.SUBCODE07
                        AND PD.SUBCODE08 = PRODUCT.SUBCODE08
                        AND PD.SUBCODE09 = PRODUCT.SUBCODE09
                        AND PD.SUBCODE10 = PRODUCT.SUBCODE10
                      LEFT JOIN ADSTORAGE ADSTORAGE
                        ON PRODUCT.ABSUNIQUEID = ADSTORAGE.UNIQUEID
                      WHERE PD.CODE IN ($inList)
                      AND ADSTORAGE.NAMENAME IN ('GSM', 'Width')
                      GROUP BY PD.CODE";
                    $stmtReqAttr = db2_exec($db2Conn, $sqlRequestAttr, array('cursor' => DB2_SCROLLABLE));
                    while ($rowReqAttr = db2_fetch_assoc($stmtReqAttr)) {
                      $requestAttrMap[$rowReqAttr['DEMANDCODE']] = $rowReqAttr;
                    }

                    $sqlInspectAttr = "SELECT
                      A.ENTRYDOCUMENTNUMBER AS DEMANDCODE,
                      MAX(ADSTORAGE.VALUEDECIMAL) AS GSM_INSPEK
                      FROM ELEMENTS A
                      LEFT JOIN ADSTORAGE ADSTORAGE
                        ON A.ABSUNIQUEID = ADSTORAGE.UNIQUEID
                      WHERE A.ENTRYDOCUMENTNUMBER IN ($inList)
                      AND TRIM(ADSTORAGE.NAMENAME) = 'GSM'
                      GROUP BY A.ENTRYDOCUMENTNUMBER";
                    $stmtInspectAttr = db2_exec($db2Conn, $sqlInspectAttr, array('cursor' => DB2_SCROLLABLE));
                    while ($rowInspectAttr = db2_fetch_assoc($stmtInspectAttr)) {
                      $inspectAttrMap[$rowInspectAttr['DEMANDCODE']] = $rowInspectAttr;
                    }

                    $sqlLot = "SELECT
                      PRODUCTIONDEMANDSTEP.PRODUCTIONDEMANDCODE AS DEMANDCODE,
                      MAX(PRODUCTIONDEMANDSTEP.PRODUCTIONORDERCODE) AS PRODUCTIONORDERCODE
                      FROM PRODUCTIONDEMANDSTEP PRODUCTIONDEMANDSTEP
                      WHERE PRODUCTIONDEMANDSTEP.PRODUCTIONDEMANDCODE IN ($inList)
                      GROUP BY PRODUCTIONDEMANDSTEP.PRODUCTIONDEMANDCODE";
                    $stmtLot = db2_exec($db2Conn, $sqlLot, array('cursor' => DB2_SCROLLABLE));
                    while ($rowLot = db2_fetch_assoc($stmtLot)) {
                      $lotMap[$rowLot['DEMANDCODE']] = $rowLot;
                    }
                  }
                }
              }
              $col = 0;
              foreach ($summaryRows as $r) {
                $bgcolor = ($col++ & 1) ? 'gainsboro' : 'antiquewhite';
                $totalpoin = explode('.', (string) $r['TOTAL_POIN']);
                $demandCode = $r['CODE'];

                $rr = array(
                  'TOTAL_ROLL' => qcf_map_get($rollMap, $demandCode, 'TOTAL_ROLL', 0),
                  'WIDTHNET' => qcf_map_get($rollMap, $demandCode, 'WIDTHNET', '')
                );

                $WidthNet = qcf_decimal_integer_part($rr['WIDTHNET']);
                $Gramasi = qcf_decimal_integer_part(qcf_map_get($inspectAttrMap, $demandCode, 'GSM_INSPEK', ''));
                $GramasiPermintaan = qcf_decimal_integer_part(qcf_map_get($requestAttrMap, $demandCode, 'GSM_REQ', ''));
                $LebarPermintaan = qcf_decimal_integer_part(qcf_map_get($requestAttrMap, $demandCode, 'WIDTH_REQ', ''));

                $rY = array(
                  'POINTS_Y01' => qcf_map_get($defectMap, $demandCode, 'POINTS_Y01', ''),
                  'POINTS_Y02' => qcf_map_get($defectMap, $demandCode, 'POINTS_Y02', ''),
                  'POINTS_Y03' => qcf_map_get($defectMap, $demandCode, 'POINTS_Y03', ''),
                  'POINTS_Y04' => qcf_map_get($defectMap, $demandCode, 'POINTS_Y04', ''),
                  'POINTS_Y05' => qcf_map_get($defectMap, $demandCode, 'POINTS_Y05', '')
                );
                $ASLUB = explode('.', (string) qcf_num_or_zero($rY['POINTS_Y01']));
                $ABARRE = explode('.', (string) qcf_num_or_zero($rY['POINTS_Y02']));
                $AUNEVEN = explode('.', (string) qcf_num_or_zero($rY['POINTS_Y03']));
                $AYARN = explode('.', (string) qcf_num_or_zero($rY['POINTS_Y04']));
                $ANEPS = explode('.', (string) qcf_num_or_zero($rY['POINTS_Y05']));

                $rT = array(
                  'POINTS_T01' => qcf_map_get($defectMap, $demandCode, 'POINTS_T01', ''),
                  'POINTS_T02' => qcf_map_get($defectMap, $demandCode, 'POINTS_T02', ''),
                  'POINTS_T03' => qcf_map_get($defectMap, $demandCode, 'POINTS_T03', ''),
                  'POINTS_T04' => qcf_map_get($defectMap, $demandCode, 'POINTS_T04', ''),
                  'POINTS_T05' => qcf_map_get($defectMap, $demandCode, 'POINTS_T05', ''),
                  'POINTS_T06' => qcf_map_get($defectMap, $demandCode, 'POINTS_T06', ''),
                  'POINTS_T07' => qcf_map_get($defectMap, $demandCode, 'POINTS_T07', '')
                );
                $BMISSING = explode('.', (string) qcf_num_or_zero($rT['POINTS_T01']));
                $BHOLES = explode('.', (string) qcf_num_or_zero($rT['POINTS_T02']));
                $BSTREAK = explode('.', (string) qcf_num_or_zero($rT['POINTS_T03']));
                $BMISSKNIT = explode('.', (string) qcf_num_or_zero($rT['POINTS_T04']));
                $BKNOT = explode('.', (string) qcf_num_or_zero($rT['POINTS_T05']));
                $BOIL = explode('.', (string) qcf_num_or_zero($rT['POINTS_T06']));
                $BFLY = explode('.', (string) qcf_num_or_zero($rT['POINTS_T07']));

                $rD = array(
                  'POINTS_D01' => qcf_map_get($defectMap, $demandCode, 'POINTS_D01', ''),
                  'POINTS_D02' => qcf_map_get($defectMap, $demandCode, 'POINTS_D02', ''),
                  'POINTS_D03' => qcf_map_get($defectMap, $demandCode, 'POINTS_D03', ''),
                  'POINTS_D04' => qcf_map_get($defectMap, $demandCode, 'POINTS_D04', ''),
                  'POINTS_D05' => qcf_map_get($defectMap, $demandCode, 'POINTS_D05', ''),
                  'POINTS_D06' => qcf_map_get($defectMap, $demandCode, 'POINTS_D06', ''),
                  'POINTS_D07' => qcf_map_get($defectMap, $demandCode, 'POINTS_D07', ''),
                  'POINTS_D08' => qcf_map_get($defectMap, $demandCode, 'POINTS_D08', ''),
                  'POINTS_D09' => qcf_map_get($defectMap, $demandCode, 'POINTS_D09', ''),
                  'POINTS_D10' => qcf_map_get($defectMap, $demandCode, 'POINTS_D10', '')
                );
                $CHAIR = explode('.', (string) qcf_num_or_zero($rD['POINTS_D01']));
                $CHOLES = explode('.', (string) qcf_num_or_zero($rD['POINTS_D02']));
                $CCOLOR = explode('.', (string) qcf_num_or_zero($rD['POINTS_D03']));
                $CABRA = explode('.', (string) qcf_num_or_zero($rD['POINTS_D04']));
                $CDYE = explode('.', (string) qcf_num_or_zero($rD['POINTS_D05']));
                $CWRINK = explode('.', (string) qcf_num_or_zero($rD['POINTS_D06']));
                $CBOWING = explode('.', (string) qcf_num_or_zero($rD['POINTS_D07']));
                $CPIN = explode('.', (string) qcf_num_or_zero($rD['POINTS_D08']));
                $CPICK = explode('.', (string) qcf_num_or_zero($rD['POINTS_D09']));
                $CKNOT = explode('.', (string) qcf_num_or_zero($rD['POINTS_D10']));

                $rC = array(
                  'POINTS_C01' => qcf_map_get($defectMap, $demandCode, 'POINTS_C01', ''),
                  'POINTS_C02' => qcf_map_get($defectMap, $demandCode, 'POINTS_C02', ''),
                  'POINTS_C03' => qcf_map_get($defectMap, $demandCode, 'POINTS_C03', ''),
                  'POINTS_C04' => qcf_map_get($defectMap, $demandCode, 'POINTS_C04', ''),
                  'POINTS_C05' => qcf_map_get($defectMap, $demandCode, 'POINTS_C05', '')
                );
                $DUNEVEN = explode('.', (string) qcf_num_or_zero($rC['POINTS_C01']));
                $DSTAINS = explode('.', (string) qcf_num_or_zero($rC['POINTS_C02']));
                $DOIL = explode('.', (string) qcf_num_or_zero($rC['POINTS_C03']));
                $DDIRT = explode('.', (string) qcf_num_or_zero($rC['POINTS_C04']));
                $DWATER = explode('.', (string) qcf_num_or_zero($rC['POINTS_C05']));

                $rP01 = array(
                  'POINTS' => qcf_map_get($defectMap, $demandCode, 'POINTS_P01', '')
                );
                $EPRINT = explode('.', (string) qcf_num_or_zero($rP01['POINTS']));

                $rGA = array(
                  'JML_A' => qcf_map_get($gradeMap, $demandCode, 'JML_A', 0),
                  'JML_KG_A' => qcf_map_get($gradeMap, $demandCode, 'JML_KG_A', 0),
                  'JML_YARD_A' => qcf_map_get($gradeMap, $demandCode, 'JML_YARD_A', 0)
                );
                $rGB = array(
                  'JML_B' => qcf_map_get($gradeMap, $demandCode, 'JML_B', 0),
                  'JML_KG_B' => qcf_map_get($gradeMap, $demandCode, 'JML_KG_B', 0),
                  'JML_YARD_B' => qcf_map_get($gradeMap, $demandCode, 'JML_YARD_B', 0)
                );
                $rGC = array(
                  'JML_C' => qcf_map_get($gradeMap, $demandCode, 'JML_C', 0),
                  'JML_KG_C' => qcf_map_get($gradeMap, $demandCode, 'JML_KG_C', 0),
                  'JML_YARD_C' => qcf_map_get($gradeMap, $demandCode, 'JML_YARD_C', 0)
                );
                $rLot = array(
                  'PRODUCTIONORDERCODE' => qcf_map_get($lotMap, $demandCode, 'PRODUCTIONORDERCODE', '')
                );
                $uom = 0;
                if ((float) $r['TOTAL_YARD'] > 0) {
                  $uom = ((float) $r['TOTAL_POIN'] * 100) / (float) $r['TOTAL_YARD'];
                }

                ?>
                <tr bgcolor="<?php echo $bgcolor; ?>">
                  <td align="center">
                    <?php echo $no; ?>
                  </td>
                  <td>
                    <?php echo number_format($uom, 2); ?>
                  </td>
                  <td>
                    <?php echo $r['CODE']; ?>
                  </td>
                  <td>
                    <?php echo $r['BUYER']; ?>
                    <!-- disini -->
                  </td>
                  <td>
                    <?php echo $r['LANGGANAN']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['SUBCODE02'] . $r['SUBCODE03']; ?>
                  </td>
                  <td align="center">
                    <?php if ($r['NO_ITEM'] != '') {
                      echo $r['NO_ITEM'];
                    } else {
                      echo $r['SUBCODE02'] . $r['SUBCODE03'];
                    } ?>
                  </td>
                  <td align="center">
                    <?php echo $r['JENIS_KAIN']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['STYLE_SEASON']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['WARNA']; ?>
                  </td>
                  <td align="center">
                    <?php if ($r['PO_HEADER'] == "") {
                      echo $r['PO_LINE'];
                    } else {
                      echo $r['PO_HEADER'];
                    } ?>
                  </td>
                  <td align="center">
                    <?php echo $r['NO_ORDER']; ?>
                  </td>
                  <td align="center">
                    <?php echo $rLot['PRODUCTIONORDERCODE']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['TGL_INSPEK']; ?>
                  </td>
                  <td align="center">
                    <?php echo $rr['TOTAL_ROLL']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['TOTAL_QTY']; ?>
                  </td>
                  <td align="center">
                    <?php echo $r['TOTAL_YARD']; ?>
                  </td>
                  <td align="center">
                    <?php echo $LebarPermintaan; ?>
                  </td>
                  <td align="center">
                    <?php echo $GramasiPermintaan; ?>
                  </td>
                  <td align="center">
                    <?php echo $WidthNet; ?>
                  </td>
                  <td align="center">
                    <?php echo $Gramasi; ?>
                  </td>
                  <td align="center">
                    <?php if ($rY['POINTS_Y01'] != '') {
                      echo $ASLUB[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rY['POINTS_Y02'] != '') {
                      echo $ABARRE[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rY['POINTS_Y03'] != '') {
                      echo $AUNEVEN[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rY['POINTS_Y04'] != '') {
                      echo $AYARN[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rY['POINTS_Y05'] != '') {
                      echo $ANEPS[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T01'] != '') {
                      echo $BMISSING[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T02'] != '') {
                      echo $BHOLES[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T03'] != '') {
                      echo $BSTREAK[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T04'] != '') {
                      echo $BMISSKNIT[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T05'] != '') {
                      echo $BKNOT[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T06'] != '') {
                      echo $BOIL[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rT['POINTS_T07'] != '') {
                      echo $BFLY[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D01'] != '') {
                      echo $CHAIR[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D02'] != '') {
                      echo $CHOLES[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D03'] != '') {
                      echo $CCOLOR[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D04'] != '') {
                      echo $CABRA[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D05'] != '') {
                      echo $CDYE[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D06'] != '') {
                      echo $CWRINK[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D07'] != '') {
                      echo $CBOWING[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D08'] != '') {
                      echo $CPIN[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D09'] != '') {
                      echo $CPICK[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rD['POINTS_D10'] != '') {
                      echo $CKNOT[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rC['POINTS_C01'] != '') {
                      echo $DUNEVEN[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rC['POINTS_C02'] != '') {
                      echo $DSTAINS[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rC['POINTS_C03'] != '') {
                      echo $DOIL[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rC['POINTS_C04'] != '') {
                      echo $DDIRT[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rC['POINTS_C05'] != '') {
                      echo $DWATER[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rP01['POINTS'] != '') {
                      echo $EPRINT[0];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php echo $totalpoin[0]; ?>
                  </td>
                  <td align="center">
                    <?php if ($rGA['JML_A'] != '') {
                      echo $rGA['JML_A'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGA['JML_KG_A'] != '') {
                      echo $rGA['JML_KG_A'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGA['JML_YARD_A'] != '') {
                      echo $rGA['JML_YARD_A'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGB['JML_B'] != '') {
                      echo $rGB['JML_B'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGB['JML_KG_B'] != '') {
                      echo $rGB['JML_KG_B'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGB['JML_YARD_B'] != '') {
                      echo $rGB['JML_YARD_B'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGC['JML_C'] != '') {
                      echo $rGC['JML_C'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGC['JML_KG_C'] != '') {
                      echo $rGC['JML_KG_C'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                  <td align="center">
                    <?php if ($rGC['JML_YARD_C'] != '') {
                      echo $rGC['JML_YARD_C'];
                    } else {
                      echo "0";
                    } ?>
                  </td>
                </tr>
                <?php $no++;
              } ?>
            </tbody>
            <tfoot class="bg-blue">
              <tr>
                <td align="center">
                  <div align="center">No</div>
                </td>
                <td align="center">
                  <div align="center">UOM</div>
                </td>
                <td align="center">
                  <div align="center">No Demand</div>
                </td>
                <td align="center">
                  <div align="center">Buyer</div>
                </td>
                <td align="center">
                  <div align="center">Langganan</div>
                </td>
                <td align="center">
                  <div align="center">Hanger</div>
                </td>
                <td align="center">
                  <div align="center">Item</div>
                </td>
                <td align="center">
                  <div align="center">Description</div>
                </td>
                <td align="center">
                  <div align="center">Style/Season</div>
                </td>
                <td align="center">
                  <div align="center">Color</div>
                </td>
                <td align="center">
                  <div align="center">PO Number</div>
                </td>
                <td align="center">
                  <div align="center">Bon Order</div>
                </td>
                <td align="center">
                  <div align="center">LOT</div>
                </td>
                <td align="center">
                  <div align="center">Tgl Inspek</div>
                </td>
                <td align="center">
                  <div align="center">Roll</div>
                </td>
                <td align="center">
                  <div align="center">Qty</div>
                </td>
                <td align="center">
                  <div align="center">Yard</div>
                </td>
                <td align="center">
                  <div align="center">Lebar</div>
                </td>
                <td align="center">
                  <div align="center">Gramasi</div>
                </td>
                <td align="center">
                  <div align="center">Lebar Inspek</div>
                </td>
                <td align="center">
                  <div align="center">Gramasi Inspek</div>
                </td>
                <td align="center">
                  <div align="center">A Slub</div>
                </td>
                <td align="center">
                  <div align="center">A Barre</div>
                </td>
                <td align="center">
                  <div align="center">A Uneven</div>
                </td>
                <td align="center">
                  <div align="center">A YarnContam</div>
                </td>
                <td align="center">
                  <div align="center">A Neps</div>
                </td>
                <td align="center">
                  <div align="center">B Missing</div>
                </td>
                <td align="center">
                  <div align="center">B Holes</div>
                </td>
                <td align="center">
                  <div align="center">B Streak</div>
                </td>
                <td align="center">
                  <div align="center">B MissKnit</div>
                </td>
                <td align="center">
                  <div align="center">B Knot</div>
                </td>
                <td align="center">
                  <div align="center">B Oil</div>
                </td>
                <td align="center">
                  <div align="center">B Fly</div>
                </td>
                <td align="center">
                  <div align="center">C Hair</div>
                </td>
                <td align="center">
                  <div align="center">C Holes</div>
                </td>
                <td align="center">
                  <div align="center">C Color</div>
                </td>
                <td align="center">
                  <div align="center">C Abra</div>
                </td>
                <td align="center">
                  <div align="center">C Dye</div>
                </td>
                <td align="center">
                  <div align="center">C Wrink</div>
                </td>
                <td align="center">
                  <div align="center">C Bowing</div>
                </td>
                <td align="center">
                  <div align="center">C Pin</div>
                </td>
                <td align="center">
                  <div align="center">C Pick</div>
                </td>
                <td align="center">
                  <div align="center">C Knot</div>
                </td>
                <td align="center">
                  <div align="center">D Uneven</div>
                </td>
                <td align="center">
                  <div align="center">D Stains</div>
                </td>
                <td align="center">
                  <div align="center">D Oil</div>
                </td>
                <td align="center">
                  <div align="center">D Dirt</div>
                </td>
                <td align="center">
                  <div align="center">D Water</div>
                </td>
                <td align="center">
                  <div align="center">E Print</div>
                </td>
                <td align="center">
                  <div align="center">Total Point</div>
                </td>
                <td align="center">
                  <div align="center">Jml A</div>
                </td>
                <td align="center">
                  <div align="center">Kg A</div>
                </td>
                <td align="center">
                  <div align="center">Yd A</div>
                </td>
                <td align="center">
                  <div align="center">Jml B</div>
                </td>
                <td align="center">
                  <div align="center">Kg B</div>
                </td>
                <td align="center">
                  <div align="center">Yd B</div>
                </td>
                <td align="center">
                  <div align="center">Jml C/X</div>
                </td>
                <td align="center">
                  <div align="center">Kg C/X</div>
                </td>
                <td align="center">
                  <div align="center">Yd C/X</div>
                </td>
              </tr>
            </tfoot>
          </table>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div id="Detail" class="modal fade modal-rotate-from-bottom" tabindex="-1" role="dialog"
    aria-labelledby="myModalLabel" aria-hidden="true">
  </div>
  <!-- Modal Popup untuk delete-->
  <div class="modal fade" id="modal_delete" tabindex="-1">
    <div class="modal-dialog modal-sm">
      <div class="modal-content" style="margin-top:100px;">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" style="text-align:center;">Are you sure to delete this information ?</h4>
        </div>

        <div class="modal-footer" style="margin:0px; border-top:0px; text-align:center;">
          <a href="#" class="btn btn-danger" id="delete_link">Delete</a>
          <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal Popup untuk Edit-->
  <div id="DataEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

  </div>
  </div>
</body>
<script type="text/javascript">
  function confirm_delete(delete_url) {
    $('#modal_delete').modal('show', { backdrop: 'static' });
    document.getElementById('delete_link').setAttribute('href', delete_url);
  }

</script>

</html>
