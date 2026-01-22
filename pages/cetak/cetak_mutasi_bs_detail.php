<?php
//$lReg_username=$_SESSION['labReg_username'];
ini_set("error_reporting", 1);
session_start();
include "../../koneksi.php";
include "../../tgl_indo.php";
//--
$idkk = $_REQUEST['idkk'];
$act = $_GET['g'];
//-
$Awal = $_GET['Awal'];
$Akhir = $_GET['Akhir'];
$qTgl   = sqlsrv_query($con_db_qc_sqlsrv,"SELECT
            CONVERT(varchar(10), GETDATE(), 23) AS tgl_skrg,
            CONVERT(varchar(8),  GETDATE(), 108) AS jam_skrg;");
$rTgl   = sqlsrv_fetch_array($qTgl);
if ($Awal != "") {
  $tgl = substr($Awal, 0, 10);
  $jam = $Awal;
} else {
  $tgl = $rTgl['tgl_skrg'];
  $jam = $rTgl['jam_skrg'];
}
?>
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link href="styles_cetak.css" rel="stylesheet" type="text/css">
  <title>Cetak Mutasi BS</title>
  <style>
    body, table, td, th, input, select, textarea {
      font-family: 'Times New Roman', Times, serif !important;
    }
    .hurufvertical {
      writing-mode: tb-rl;
      -webkit-transform: rotate(-90deg);
      -moz-transform: rotate(-90deg);
      -o-transform: rotate(-90deg);
      -ms-transform: rotate(-90deg);
      transform: rotate(180deg);
      white-space: nowrap;
      float: left;
    }

    input {
      text-align: center;
      border: hidden;
    }

    @media print {
      ::-webkit-input-placeholder {
        /* WebKit browsers */
        color: transparent;
      }

      :-moz-placeholder {
        /* Mozilla Firefox 4 to 18 */
        color: transparent;
      }

      ::-moz-placeholder {
        /* Mozilla Firefox 19+ */
        color: transparent;
      }

      :-ms-input-placeholder {
        /* Internet Explorer 10+ */
        color: transparent;
      }

      .pagebreak {
        page-break-before: always;
      }

      .header {
        display: block
      }

      table thead {
        display: table-header-group;
      }
    }
  </style>
</head>

<body>
  <table width="100%" style="font-family: 'Times New Roman', Times, serif;">
    <thead>
      <tr>
        <td>
          <table width="100%" border="1" class="table-list1">
            <tr>
              <td width="9%" align="center"><img src="indo.jpg" width="40" height="40" /></td>
              <td align="center" valign="middle"><strong>
                  <font size="+1">BUKTI MUTASI BARANG LIMBAH</font>
                </strong></td>
              <td align="center" valign="middle">
                <table width="100%" border="0">
                    <tbody>
                    <tr style="border: none;">
                      <td style="border: none;">No. Form</td>
                      <td style="border: none;">:13-01</td>
                    </tr>
                    <tr style="border: none;">
                      <td style="border: none;">No. Revisi</td>
                      <td style="border: none;">: 00</td>
                    </tr>
                    <tr style="border: none;">
                      <td style="border: none;">Tgl. Terbit</td>
                      <td style="border: none;">: 23-jan-18</td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </table>
          <?php
          $dt = sqlsrv_query($con_db_qc_sqlsrv, " SELECT * FROM db_qc.mutasi_bs_krah WHERE id='$_GET[idm]'");
          $r = sqlsrv_fetch_array($dt);
          ?>
          <table width="100%" border="0">
            <tbody>
              <tr>
                <td width="23%">
                  <font size="-1">No.</font>
                </td>
                <td width="32%">
                  <font size="-1">:
                    <?php echo $r['no_mutasi']; ?>
                  </font>
                </td>
                <td align="left">
                  <font size="-1">Tanggal</font>
                </td>
                <td align="left">
                  <font size="-1">:
                    <?php echo $r['tgl_buat']->format('d F Y'); ?>
                  </font>
                </td>
              </tr>
              <tr>
                <td>
                  <font size="-1">Departemen</font>
                </td>
                <td>
                  <font size="-1">:
                    <?php echo $r['dept']; ?>
                  </font>
                </td>
                <td align="left">
                  <font size="-1">Jam Penyerahan:</font>
                </td>
                <td align="left">
                  <font size="-1">:
                    <?php echo date_format($r['jam_penyerahan'], 'H:i:s'); ?> wib
                  </font>
                </td>
              </tr>
              <tr>
                <td colspan="2">
                  <font size="-1">Barang Limbah Yang Dimutasi : </font>
                </td>
                <td width="18%" align="left">&nbsp;</td>
                <td width="27%" align="left">&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
    </thead>
    <tr>
      <td>
        <table width="100%" border="1" class="table-list1">
          <tr>
            <td width="5%" align="center" valign="top">
              <font size="+2"><strong>NO.</strong></font>
            </td>
            <td width="73%" align="center" valign="top">
              <font size="+2"><strong>Jenis Limbah</strong></font>
            </td>
            <td width="22%" align="center" valign="top">
              <font size="+2"><strong>Quantity (Kg/Pcs)</strong></font>
            </td>
          </tr>
          <?php
          $data = sqlsrv_query($con_db_qc_sqlsrv, "SELECT md.*,m.no_mutasi,m.jns_limbah FROM db_qc.mutasi_bs_krah_detail md
   INNER JOIN db_qc.mutasi_bs_krah m ON md.id_mutasi=m.id 
   WHERE m.id='$_GET[idm]' 
   ORDER BY md.id ASC");
          $no = 1;
          while ($rowd = sqlsrv_fetch_array($data)) {
            ?>
            <tr>
              <td align="center" valign="top">
                <?php echo $no; ?>
              </td>
              <td align="center" valign="top"><strong>
                  <?php 
                  if ($no == 1) {
                    echo $rowd['jns_limbah']; 
                  } 
                  ?>
                </strong></td>
              <td align="left" valign="top">
                <table width="100%" style="border: none;">
                  <tr>
                    <td style="border: none;" width="30%" align="right"><?php echo $rowd['qty']; ?></td>
                    <td style="border: none;" width="20%" align="right"><?php echo $rowd['satuan']; ?></td>
                    <td style="border: none;" width="50%" align="left"><?php echo $rowd['catatan']; ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php $no++;
            $Tqty += $rowd['qty'];
          } ?>
          <?php for ($i = $no; $i <= 46; $i++) { ?>
            <tr>
              <td align="center" valign="top">
                <?php echo $i; ?>
              </td>
              <td align="center" valign="top">&nbsp;</td>
              <td align="center" valign="top">&nbsp;</td>
            </tr>
      </tr>
    <?php } ?>
  </table>
  </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="0">
        <tbody>
          <tr>
            <td width="39%">
              <font size="+2">TOTAL</font>
            </td>
            <td width="61%" align="center">
              <font size="+4"> <strong>
                  <?php echo number_format($Tqty, 2, '.', ''); ?> &nbsp;&nbsp;&nbsp;KG
                </strong></font>
            </td>
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="1" class="table-list1">

        <tr>
          <td width="16%" scope="col">&nbsp;</td>
          <td width="29%" scope="col">
            <div align="center">Diserah Oleh;</div>
          </td>
          <td width="29%" scope="col">
            <div align="center">Diterima Oleh;</div>
          </td>
        </tr>
        <tr>
          <td>Nama</td>
          <td align="center">
            <font size="-1"><strong>
                <?php echo $r['serah']; ?>
              </strong></font>
          </td>
          <td align="center">
            <font size="-1"><strong>
                <?php echo $r['terima']; ?>
              </strong></font>
          </td>
        </tr>
        <tr>
          <td>Jabatan</td>
          <td align="center"> <?php echo $r['jabatan1']; ?></td>
          <td align="center"> <?php echo $r['jabatan2']; ?></td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td align="center">
            <?php echo tanggal_indo($tgl, false); ?>
          </td>
          <td align="center">
            <?php echo tanggal_indo($tgl, false); ?>
          </td>
        </tr>
        <tr>
          <td valign="top" style="height: 0.5in;">Tanda Tangan</td>
          <td align="center"><!--<img src="ttd/bayu.png" width="50" height="50" alt=""/>--></td>
          <td align="center"><!--<img src="ttd/putri.png" width="50" height="50" alt=""/>--></td>
        </tr>

      </table>
    </td>
  </tr>

  </table>
  <!--<table width="99%" border="0">
  <tbody>
    <tr>
      <td width="73%" rowspan="4"><div style="font-size: 11px; font-family:sans-serif, Roman, serif;">
        <?php $dtKet = sqlsrv_query($con_db_qc_sqlsrv, "SELECT
            SUM( IIF ( ket_status = 'Tolak Basah', 1, 0 ) ) AS tolak_basah,
            SUM( IIF ( ket_status = 'Gagal Proses', 1, 0 ) ) AS gagal_proses,
            SUM( IIF ( ket_status = 'Perbaikan', 1, 0 ) ) AS perbaikan,
            SUM( IIF ( ket_status = 'Greige' OR ket_status = 'Salesmen Sample' OR ket_status = 'Development Sample' OR ket_status = 'Cuci Misty' OR ket_status = 'Cuci YD', 1, 0 ) ) AS greige,
            SUM( IIF ( ket_status = 'Tolak Basah',bruto, 0 ) ) AS tolak_basah_kg,
            SUM( IIF ( ket_status = 'Gagal Proses', bruto, 0 ) ) AS gagal_proses_kg,
            SUM( IIF ( ket_status = 'Perbaikan', bruto, 0 ) ) AS perbaikan_kg,
            SUM( IIF ( ket_status = 'Greige' OR ket_status = 'Salesmen Sample' OR ket_status = 'Development Sample' OR ket_status = 'Cuci Misty' OR ket_status = 'Cuci YD', bruto, 0 ) ) AS greige_kg
          FROM
            db_qc.tbl_schedule 
          WHERE
            NOT STATUS = 'selesai'");
        $rKet = sqlsrv_fetch_array($dtKet); ?>
        Perbaikan: <?php echo $rKet['perbaikan']; ?> Lot &nbsp; <?php echo $rKet['perbaikan_kg']; ?> Kg<br />
        Gagal Proses : <?php echo $rKet['gagal_proses']; ?> Lot &nbsp; <?php echo $rKet['gagal_proses_kg']; ?> Kg<br />
    Greige : <?php echo $rKet['greige']; ?> Lot &nbsp; <?php echo $rKet['greige_kg']; ?> Kg<br />  
      Tolak Basah : <?php echo $rKet['tolak_basah']; ?> Lot &nbsp; <?php echo $rKet['tolak_basah_kg']; ?> Kg </div></td>
      <td width="20%">&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><pre></pre></td>
    </tr>
  </tbody>
</table>-->
  <script>
    //alert('cetak');window.print();
  </script>
</body>

</html>