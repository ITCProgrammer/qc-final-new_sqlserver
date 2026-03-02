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

/**
 * 1. QUERY UTAMA
 * Kita tambahkan ORDER BY agar data yang Group & Hue-nya sama berkumpul jadi satu.
 */
$code = "SELECT a.*, b.berat_order_now, b.panjang_order_now 
         FROM db_qc.db_qc.tbl_qcf a
         LEFT JOIN db_qc.db_qc.tbl_qcf_qty_order b ON (a.id = b.id)
         WHERE a.hue IS NOT NULL 
         AND a.no_order LIKE '$Order%' AND a.no_po LIKE '$PO%' 
         AND a.no_hanger LIKE '$Item%' AND a.warna LIKE '$Warna%' 
         AND a.pelanggan LIKE '$Langganan%' AND a.nodemand LIKE '%$Demand%' 
         AND a.lot LIKE '%$Prodorder%' $Where $Dly
         ORDER BY a.[group] ASC, a.hue ASC, a.id ASC";

$sql = sqlsrv_query($con_db_qc_sqlsrv, $code);
$currentKey = ""; // Variable pembantu untuk grouping tabel
?>
<div align="center"> <h2>GROUPING FORM LANGGANAN</h2></div>
<br>
<?php
while ($row = sqlsrv_fetch_array($sql, SQLSRV_FETCH_ASSOC)) {
    $cleanGroup = strtoupper(trim($row['group']));
    $cleanHue   = strtoupper(trim($row['hue']));
    $groupHueKey = $cleanGroup . "|" . $cleanHue;
    // Jika Group/Hue berbeda dari data sebelumnya, tutup tabel lama (jika ada) dan buat header baru
    if ($currentKey != $groupHueKey) {
        if ($currentKey != "") { echo "</tbody></table><br>"; }
        $currentKey = $groupHueKey;
        ?>
        
        <table border="1" cellspacing="0" cellpadding="5" width="100%" style="margin-bottom: 10px; font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse;">
            <thead>
                <tr>
                    <td width="80" bgcolor="#f2f2f2"><strong>GROUP</strong></td>
                    <td width="150" align="center"><strong><?php echo $row['group']; ?></strong></td>
                    <td colspan="10" style="border:none;"></td>
                </tr>
                <tr>
                    <td bgcolor="#f2f2f2"><strong>HUE</strong></td>
                    <td align="center"><strong><?php echo $row['hue']; ?></strong></td>
                    <td colspan="10" style="border:none;"></td>
                </tr>
                <tr bgcolor="#f2f2f2" align="center" style="font-weight:bold;">
                    <td width="100">ITEM</td>
                    <td width="120">WARNA</td>
                    <td width="150">PO</td>
                    <td width="100">LOT</td>
                    <td width="50">GRADE</td>
                    <td width="40">ROLL</td>
                    <td width="80">QTY YARD</td>
                    <td width="100">ITEM</td>
                    <td width="100">LOT</td>
                    <td width="50">GRADE</td>
                    <td width="40">ROLL</td>
                    <td width="80">QTY YARD</td>
                </tr>
            </thead>
            <tbody>
        <?php
    }

    /**
     * 2. LOGIKA DATA KANAN (DETAIL)
     */
    $data_kanan = [];
    if (!empty($row['list_kanan'])) {
        $id_kanan_array = explode(',', $row['list_kanan']);
        $placeholders = str_repeat('?,', count($id_kanan_array) - 1) . '?';
        
        // Ambil detail berdasarkan list nodemand yang disimpan
        $sql_kanan = "SELECT no_item, lot, rol, panjang FROM db_qc.db_qc.tbl_qcf WHERE nodemand IN ($placeholders)";
        $stmt_kanan = sqlsrv_query($con_db_qc_sqlsrv, $sql_kanan, $id_kanan_array);
        
        if($stmt_kanan){
            while ($rk = sqlsrv_fetch_array($stmt_kanan, SQLSRV_FETCH_ASSOC)) {
                $data_kanan[] = $rk;
            }
        }
    } 
    // Tentukan jumlah baris yang harus dirender
    $max_row = max(1, count($data_kanan));
    
    for ($i = 0; $i < $max_row; $i++) { ?>
        <tr>
            <?php if ($i == 0) { ?>
                <td rowspan="<?php echo $max_row; ?>" align="center"><?php echo $row['no_item']; ?></td>
                <td rowspan="<?php echo $max_row; ?>" align="center"><?php echo $row['warna']; ?></td>
                <td rowspan="<?php echo $max_row; ?>" align="center"><?php echo $row['no_po']; ?></td>
            <?php } ?>

            <td align="center"><?php echo ($i == 0) ? $row['lot'] : ''; ?></td>
            <td align="center"></td> <td align="center"><?php echo ($i == 0) ? $row['rol'] : ''; ?></td>
            <td align="center"><?php echo ($i == 0) ? $row['panjang'] : ''; ?></td>

            <td align="center"><?php echo $data_kanan[$i]['no_item'] ?? ''; ?></td>
            <td align="center"><?php echo $data_kanan[$i]['lot'] ?? ''; ?></td>
            <td align="center"></td> <td align="center"><?php echo $data_kanan[$i]['rol'] ?? ''; ?></td>
            <td align="center"><?php echo $data_kanan[$i]['panjang'] ?? ''; ?></td>
        </tr>
    <?php } 
    // Garis pemisah antar record dalam satu Group/Hue agar tidak bingung
    echo "<tr><td colspan='12' style='background:#eeeeee; height:2px; padding:0;'></td></tr>";
} 

if ($currentKey != "") { echo "</tbody></table>"; }?>
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