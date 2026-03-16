<?php
ini_set("error_reporting", 1);
session_start();
include("../koneksi.php");
$no_po = isset($_GET['no_po']) ? $_GET['no_po'] : '';
$no_hanger = isset($_GET['no_hanger']) ? $_GET['no_hanger'] : '';
$no_warna = isset($_GET['no_warna']) ? $_GET['no_warna'] : '';

if (!function_exists('bp_value')) {
    function bp_value($value, $default = '')
    {
        return ($value === null || $value === '') ? $default : $value;
    }
}

if (!function_exists('bp_decimal')) {
    function bp_decimal($value)
    {
        if ($value === null || $value === '') {
            return '0.00';
        }

        return number_format((float)$value, 2, '.', '');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title >Detail Bon Penghubung</title>
    <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css"> -->
</head>
<body>

    
    <!-- <h5><strong>No Order : <?php echo htmlspecialchars($no_order); ?></strong></h5>
    <h5><strong>No Item : <?php echo htmlspecialchars($no_item); ?></strong></h5>
    <h5><strong>No Warna : <?php echo htmlspecialchars($no_warna); ?></strong></h5> -->
    <hr>
    <div align="center" style="border: 2px solid black; font-size: 25px;"><strong>Detail Bon Penghubung</strong></div>
    <br>
    <table style="width: 100%; table-layout: fixed;">
        <thead>
            <tr >
                <th style="border: 2px solid black; text-align: center;" rowspan="2">No</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Status</th>
                <th style="border: 2px solid black; text-align: center;" colspan="2">Qty FOC</th>
                <th style="border: 2px solid black; text-align: center;" colspan="2">Estimasi FOC</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Lot-Legacy</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Lot</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Demand</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Issue</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Notes</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Responsibility</th>
                <th style="border: 2px solid black; text-align: center;" rowspan="2">Inspection Report</th>
            </tr>
            <tr>
                <th style="border: 2px solid black; text-align: center;">Kg</th>
                <th style="border: 2px solid black; text-align: center;">Yard</th>
                <th style="border: 2px solid black; text-align: center;">Kg</th>
                <th style="border: 2px solid black; text-align: center;">Yard</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_berat_order = 0;
            $total_panjang_order = 0;
            $total_estimasi = 0;
            $total_panjang_estimasi = 0;
            $sqldtl = "SELECT
                tq.*,
                ncp.no_ncp,
                ncp.masalah_utama,
                ncp.akar_masalah,
                ncp.solusi_panjang,
                tli.qty_sisa,
                tli.satuan_sisa,
                c.masalah_dominan,
                c.ket
            FROM
                db_qc.tbl_qcf tq
                OUTER APPLY (
                    SELECT
                        STUFF((
                            SELECT DISTINCT ', ' + CAST(b1.no_ncp_gabungan AS VARCHAR(MAX))
                            FROM db_qc.tbl_ncp_qcf_now b1
                            WHERE b1.nodemand = tq.nodemand
                                AND ISNULL(CAST(b1.no_ncp_gabungan AS VARCHAR(MAX)), '') <> ''
                            FOR XML PATH(''), TYPE
                        ).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS no_ncp,
                        STUFF((
                            SELECT DISTINCT ', ' + CAST(b2.masalah_dominan AS VARCHAR(MAX))
                            FROM db_qc.tbl_ncp_qcf_now b2
                            WHERE b2.nodemand = tq.nodemand
                                AND ISNULL(CAST(b2.masalah_dominan AS VARCHAR(MAX)), '') <> ''
                            FOR XML PATH(''), TYPE
                        ).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS masalah_utama,
                        STUFF((
                            SELECT DISTINCT ', ' + CAST(b3.akar_masalah AS VARCHAR(MAX))
                            FROM db_qc.tbl_ncp_qcf_now b3
                            WHERE b3.nodemand = tq.nodemand
                                AND ISNULL(CAST(b3.akar_masalah AS VARCHAR(MAX)), '') <> ''
                            FOR XML PATH(''), TYPE
                        ).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS akar_masalah,
                        STUFF((
                            SELECT DISTINCT ', ' + CAST(b4.solusi_panjang AS VARCHAR(MAX))
                            FROM db_qc.tbl_ncp_qcf_now b4
                            WHERE b4.nodemand = tq.nodemand
                                AND ISNULL(CAST(b4.solusi_panjang AS VARCHAR(MAX)), '') <> ''
                            FOR XML PATH(''), TYPE
                        ).value('.', 'VARCHAR(MAX)'), 1, 2, '') AS solusi_panjang
                ) ncp
                OUTER APPLY (
                    SELECT TOP 1
                        qty_loss AS qty_sisa,
                        satuan AS satuan_sisa
                    FROM db_qc.tbl_lap_inspeksi
                    WHERE nodemand = tq.nodemand
                        AND no_order = tq.no_order
                ) tli
                OUTER APPLY (
                    SELECT TOP 1
                        masalah_dominan,
                        ket
                    FROM db_qc.tbl_aftersales_now
                    WHERE nodemand = tq.nodemand
                        AND nokk = tq.nokk
                ) c
            WHERE
                ISNULL(CAST(tq.sts_pbon AS VARCHAR(50)), '') != '10'
                AND (
                    ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_masalah AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_keterangan AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_roll1 AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_roll2 AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_roll3 AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_dep AS VARCHAR(MAX)))), '') != ''
                    OR ISNULL(LTRIM(RTRIM(CAST(tq.penghubung_dep_persen AS VARCHAR(MAX)))), '') != ''
                )
                AND ISNULL(CAST(tq.no_po AS VARCHAR(255)), '') = ?
                AND ISNULL(CAST(tq.no_hanger AS VARCHAR(255)), '') = ?
                AND ISNULL(CAST(tq.no_warna AS VARCHAR(255)), '') = ?
            ";
            $params = array($no_po, $no_hanger, $no_warna);
            $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sqldtl, $params);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            while($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
                $total_berat_order += (float)bp_value($r['berat_order'], 0);
                $total_panjang_order += (float)bp_value($r['panjang_order'], 0);
                $total_estimasi += (float)bp_value($r['estimasi'], 0);
                $total_panjang_estimasi += (float)bp_value($r['panjang_estimasi'], 0);
            ?>
            <tr>
                <td style="border: 2px solid black;" align="center"><?php echo $no;?></td>
                <td style="border: 2px solid black;" align="center"><?php $rsts= sqlsrv_query($con_db_qc_sqlsrv, "SELECT TOP 1 * FROM db_qc.tbl_bonpenghubung_mail WHERE nodemand = ?", array(bp_value($r['nodemand'])));
                    $dtsts = $rsts ? sqlsrv_fetch_array($rsts, SQLSRV_FETCH_ASSOC) : array();
                    if(isset($dtsts['status_approve']) && $dtsts['status_approve']==1){
                    echo 'APPROVE OLEH : '.bp_value($dtsts['approve_mkt']);
                    }else if(isset($dtsts['status_approve']) && $dtsts['status_approve']==99){
                    echo 'REJECT OLEH : '.bp_value($dtsts['approve_mkt']);
                    }else if(isset($dtsts['status_approve']) && $dtsts['status_approve']==2){
                    echo 'CLOSED OLEH : '.bp_value($dtsts['closed_ppc']);
                    } else {
                    echo '';
                    }?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_decimal($r['berat_order']);?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_decimal($r['panjang_order']);?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_decimal($r['estimasi']);?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_decimal($r['panjang_estimasi']);?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['lot_legacy']); // Lot-Legacy ?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['lot']); // Lot ?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['nodemand']); // Demand ?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['penghubung_masalah']); // Issue ?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['penghubung_keterangan']); // Notes ?></td>
                <td style="border: 2px solid black;" align="center"><?php echo bp_value($r['penghubung_dep']).bp_value($r['penghubung_dep_persen']); // Responsibility ?></td>
                <td style="border: 2px solid black;">
                     <a style="color: #E95D4E; font-size:10px; font-family: Microsoft Sans Serif;" href="cetak/cetak_inspectpackingreport.php?demand=<?= urlencode(bp_value($r['nodemand'])); ?>&ispacking=true" target="_blank">Inspect Report <i class="fa fa-link"></i></a>
                </td>
            </tr>
            <?php $no++;}?>
        </tbody>
        <tfoot>
            <tr style="background:#f5f5f5; font-weight:bold;">
                <td colspan="2" style="border:2px solid black; text-align:right;">Total</td>
                <td style="border:2px solid black; text-align:center;"><?php echo number_format($total_berat_order,2); ?></td>
                <td style="border:2px solid black; text-align:center;"><?php echo number_format($total_panjang_order,2); ?></td>
                <td style="border:2px solid black; text-align:center;"><?php echo number_format($total_estimasi,2); ?></td>
                <td style="border:2px solid black; text-align:center;"><?php echo number_format($total_panjang_estimasi,2); ?></td>
                <td colspan="7" style="border:2px solid black;"></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
</body>
</html>
