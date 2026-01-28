<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=delay-TQ.xls");//ganti nama sesuai keperluan
header("Pragma: no-cache");
header("Expires: 0");
//disini script laporan anda
?>
<?php 
include "../../koneksi.php";
//--
?>
<body>
	
<table width="100%" border="1">
    <tr>
      <th bgcolor="#12C9F0">NO</th>
      <th bgcolor="#12C9F0">NO. TEST</th>
      <th bgcolor="#12C9F0">NO. KK</th>
      <th bgcolor="#12C9F0">TANGGAL TARGET</th>
      <th bgcolor="#12C9F0">LANGGANAN</th>
    </tr>
	<?php 
    $no=1;
    $query=sqlsrv_query($con_db_qc_sqlsrv,"SELECT a.*, a.id AS idkk, b.* , CONVERT(VARCHAR(19), tgl_target) AS tgl_target2, CONVERT(VARCHAR(19), tgl_masuk) AS tgl_masuk FROM db_qc.tbl_tq_nokk a
    LEFT JOIN db_qc.tbl_tq_test b ON a.id=b.id_nokk
    WHERE ([status]='' or [status] IS NULL) AND CONVERT(DATE, tgl_masuk) BETWEEN DATEADD(day,-30,CURRENT_TIMESTAMP) and CURRENT_TIMESTAMP
    AND tgl_target < CURRENT_TIMESTAMP
    ORDER BY tgl_target ASC");
	while($r=sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC)){
        $tgltarget = new DateTime($r['tgl_target2']);
        $now=new DateTime();
        $target = $now->diff($tgltarget);
        $delay = $tgltarget->diff($now);
	?>
    <tr>
      <td align="center"><?php echo $no;?></td>
      <td align="center">'<?php echo $r['no_test'];?></td>
      <td align="center">'<?php echo $r['nokk'];?></td>
      <td align="center"><?php echo $r['tgl_target2'];?><br>
      <?php if($delay->d>0){ ?>
        <span style="color:#F44336;text-align:center;"><?php echo "Delay "; echo $delay->d; echo " Hari";?></span>
      <?php } ?>
      </td>
      <td align="center"><?php echo $r['pelanggan'];?></td>
  </tr>
    <?php $no++;} ?>
</table>
</body>