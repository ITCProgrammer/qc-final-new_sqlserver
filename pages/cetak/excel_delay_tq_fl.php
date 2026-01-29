<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=delay-TQ_FL.xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda
?>
<?php 
  include "../../koneksi.php";
?>
<body>
  <table width="100%" border="1">
    <tr>
      <th bgcolor="#12C9F0">NO</th>
      <th bgcolor="#12C9F0">NO. REPORT FL</th>
      <th bgcolor="#12C9F0">NO. KK</th>
      <th bgcolor="#12C9F0">TANGGAL TARGET</th>
      <th bgcolor="#12C9F0">LANGGANAN</th>
    </tr>
    <?php
      $no = 1;
        $sql = " SELECT a.*, a.id AS idkk, b.*
          FROM db_qc.tbl_tq_first_lot a
          LEFT JOIN db_qc.tbl_tq_test_fl b ON a.id = b.id_nokk
          WHERE (b.[status] = '' OR b.[status] IS NULL)
            AND a.tgl_masuk >= DATEADD(DAY, -30, CAST(GETDATE() AS date))
            AND a.tgl_masuk <  DATEADD(DAY, 1, CAST(GETDATE() AS date))
          ORDER BY a.tgl_target ASC
        ";

        $stmt = sqlsrv_query($con_db_qc_sqlsrv, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
          $tglTargetObj = $r['tgl_target'] ?? null;

          $delayDays = 0;
          if ($tglTargetObj instanceof DateTime) {
              $now = new DateTime();
              if ($tglTargetObj < $now) {
                  $delayDays = (int)$tglTargetObj->diff($now)->days;
              }
              $tglTargetText = $tglTargetObj->format('Y-m-d');
          } else {
              $tglTargetText = '';
          }
          ?>
          <tr>
            <td align="center"><?php echo $no; ?></td>
            <td align="center">'<?php echo $r['no_report_fl']; ?></td>
            <td align="center">'<?php echo $r['nokk']; ?></td>
            <td align="center">
              <?php echo $tglTargetText; ?><br>
              <?php if ($delayDays > 0) { ?>
                <span style="color:#F44336;text-align:center;">
                  <?php echo "Delay " . $delayDays . " Hari"; ?>
                </span>
              <?php } ?>
            </td>
            <td align="center"><?php echo $r['pelanggan']; ?></td>
          </tr>
          <?php
            $no++;
        }
    ?>
  </table>
</body>