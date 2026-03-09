<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Grouping-Form".date($_GET['awal']).".xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
include "../../koneksi.php";

// Ambil Parameter Filter
$Awal      = isset($_GET['awal']) ? $_GET['awal'] : '';
$Akhir     = isset($_GET['akhir']) ? $_GET['akhir'] : '';
$Order     = isset($_GET['no_order']) ? $_GET['no_order'] : '';
$PO        = isset($_GET['no_po']) ? $_GET['no_po'] : '';
$Item      = isset($_GET['item']) ? $_GET['item'] : '';
$Warna     = isset($_GET['warna']) ? $_GET['warna'] : '';
$Langganan = isset($_GET['langganan']) ? $_GET['langganan'] : '';
$Delay     = isset($_GET['delay']) ? $_GET['delay'] : '';
$Demand    = isset($_GET['demand']) ? $_GET['demand'] : '';
$Prodorder = isset($_GET['prodorder']) ? $_GET['prodorder'] : '';

// Validasi Tanggal
$Where = "";
if($Awal != "") { 
    $Where = " AND CONVERT(varchar(10), a.tgl_masuk, 23) BETWEEN '$Awal' AND '$Akhir' "; 
}

// Filter Delay
$Dly = "";
if ($Delay == "1") {
    $Dly = " AND a.tgl_pack IS NOT NULL
             AND DATEDIFF(day, ISNULL(a.tglcwarna, a.tgl_pack), a.tgl_pack) >= 3
             AND a.sts_nodelay = '0'";
}

// QUERY UTAMA - Pastikan ORDER BY sangat krusial di sini agar grouping tidak pecah
$code = "SELECT a.*, b.berat_order_now, b.panjang_order_now 
         FROM db_qc.db_qc.tbl_qcf a
         LEFT JOIN db_qc.db_qc.tbl_qcf_qty_order b ON (a.id = b.id)
         WHERE a.hue IS NOT NULL 
         AND a.no_order LIKE '$Order%' AND a.no_po LIKE '$PO%' 
         AND a.no_hanger LIKE '$Item%' AND a.warna LIKE '$Warna%' 
         AND a.pelanggan LIKE '$Langganan%' AND a.nodemand LIKE '%$Demand%' 
         AND a.lot LIKE '%$Prodorder%' $Where $Dly
         ORDER BY a.[group] ASC, a.hue ASC, a.list_kanan ASC, a.id ASC";

$sql = sqlsrv_query($con_db_qc_sqlsrv, $code);

// 1. COLLECT DATA (Dikelompokkan per Group & Hue)
$all_data = [];
while ($row = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC)) {
    // Ambil data kanan untuk setiap baris
    $data_kanan = null;
    if (!empty($row['list_kanan'])) {
        $id_kanan = explode(',', $row['list_kanan'])[0]; // Ambil salah satu ID untuk identifikasi unique
        $sql_k = "SELECT no_item, lot, rol, panjang, grade_kanan FROM db_qc.db_qc.tbl_qcf WHERE nodemand = ?";
        $stmt_k = sqlsrv_query($con_db_qc_sqlsrv, $sql_k, array($id_kanan));
        $data_kanan = sqlsrv_fetch_array($stmt_k, SQLSRV_FETCH_ASSOC);
    }
    $row['detail_kanan'] = $data_kanan;
    $all_data[$row['group']][$row['hue']][] = $row;
}
?>

<div align="center"> <h2>GROUPING FORM LANGGANAN</h2></div>

<?php foreach ($all_data as $groupName => $hues): ?>
    <?php foreach ($hues as $hueName => $rows): ?>
        
        <table border="1" cellspacing="0" cellpadding="5" width="100%" style="margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse;">
            <thead>
                <tr>
                    <td bgcolor="#f2f2f2"><strong>GROUP</strong></td>
                    <td align="center"><strong><?php echo $groupName; ?></strong></td>
                    <td colspan="10" style="border:none;"></td>
                </tr>
                <tr>
                    <td bgcolor="#f2f2f2"><strong>HUE</strong></td>
                    <td align="center"><strong><?php echo $hueName; ?></strong></td>
                    <td colspan="10" style="border:none;"></td>
                </tr>
                <tr bgcolor="#f2f2f2" align="center" style="font-weight:bold;">
                    <td>ITEM (L)</td><td>WARNA</td><td>PO</td><td>LOT (L)</td><td>GRADE</td><td>ROLL</td><td>YARD</td>
                    <td>ITEM (R)</td><td>LOT (R)</td><td>GRADE</td><td>ROLL</td><td>YARD</td>
                </tr>
            </thead>
            <tbody>
            <?php
            // 2. PRE-PROCESSING ROWSPAN UNTUK KOLOM KANAN
            $rowspan_map = [];
            $prev_key = null;
            $start_index = 0;

            foreach ($rows as $idx => $r) {
                // Key untuk menentukan data kanan itu "sama" atau tidak
                $current_key = ($r['detail_kanan']) ? $r['detail_kanan']['no_item'].$r['detail_kanan']['lot'] : 'EMPTY-'.$idx;
                
                if ($current_key === $prev_key) {
                    $rowspan_map[$start_index]++;
                    $rowspan_map[$idx] = 0; // 0 berarti tidak perlu di-render (di-skip)
                } else {
                    $rowspan_map[$idx] = 1;
                    $start_index = $idx;
                    $prev_key = $current_key;
                }
            }

            // 3. RENDERING
            foreach ($rows as $idx => $row): ?>
                <tr>
                    <td align="center"><?php echo $row['no_item']; ?></td>
                    <td align="center"><?php echo $row['warna']; ?></td>
                    <td align="center"><?php echo $row['no_po']; ?></td>
                    <td align="center"><?php echo $row['lot']; ?></td>
                    <td align="center"><?php echo $row['grade_kiri']; ?></td>
                    <td align="center"><?php echo $row['rol']; ?></td>
                    <td align="center"><?php echo $row['panjang']; ?></td>

                    <?php if ($rowspan_map[$idx] > 0): ?>
                        <?php $dk = $row['detail_kanan']; ?>
                        <td rowspan="<?php echo $rowspan_map[$idx]; ?>" align="center">
                            <?php echo $dk['no_item'] ?? ''; ?>
                        </td>
                        <td rowspan="<?php echo $rowspan_map[$idx]; ?>" align="center">
                            <?php echo $dk['lot'] ?? ''; ?>
                        </td>
                        <td rowspan="<?php echo $rowspan_map[$idx]; ?>" align="center">
                            <?php echo $dk['grade_kanan'] ?? ''; ?>
                        </td>
                        <td rowspan="<?php echo $rowspan_map[$idx]; ?>" align="center">
                            <?php echo $dk['rol'] ?? ''; ?>
                        </td>
                        <td rowspan="<?php echo $rowspan_map[$idx]; ?>" align="center">
                            <?php echo $dk['panjang'] ?? ''; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <br>
    <?php endforeach; ?>
<?php endforeach; ?>

<br>
<table width="100%" style="font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <td width="50%" align="center"></td>
        <td width="50%" align="center"></td>
        <td width="50%" align="center">
            Colorist,<br><br><br><br><br><br><br>
            DEWI
        </td>
        <td width="50%" align="center"></td>
        <td width="50%" align="center"></td>
        <td width="50%" align="center"></td>
        <td width="50%" align="center">
            Mengetahui,<br><br><br><br><br><br><br>
            AGUNG C
        </td>
    </tr>
</table>