<?php
ini_set("error_reporting", 1);
include "koneksi.php";
$nocounter = $_GET['nocount'];
?>
<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Kain Sudah Selesai Test</h3>
                <br>
                <div class="col-lg-12 overflow-auto table-responsive" style="overflow-x: auto;">
                    <table id="example1" class="table table-bordered table-hover table-striped" style="width: 100%;">
                        <thead>
                            <tr class="bg-green">
                                <th style="border: 1px solid #ddd;">#</th>
                                <th style="border: 1px solid #ddd;">Aksi</th>
                                <th style="border: 1px solid #ddd;">Suffix</th>
                                <th style="border: 1px solid #ddd;">No Counter</th>
                                <th style="border: 1px solid #ddd;">Jenis Testing</th>
                                <th style="border: 1px solid #ddd;">Treatment</th>
                                <th style="border: 1px solid #ddd;">Buyer</th>
                                <th style="border: 1px solid #ddd;">No Warna</th>
                                <th style="border: 1px solid #ddd;">Nama Warna</th>
                                <th style="border: 1px solid #ddd;">Item</th>
                                <th style="border: 1px solid #ddd;">Jenis Kain</th>
                                <!-- Update by ilham 21/06/2024 No Ticket BDIT240001517 -->
                                <th style="border: 1px solid #ddd;">Tgl Kain Diterima</th>
                                <th style="border: 1px solid #ddd;">Tgl Approve</th>
                                <th style="border: 1px solid #ddd;">Personil Testing</th>
                                <th style="border: 1px solid #ddd;">Permintaan Testing</th>
                                <th style="border: 1px solid #ddd;">Created By</th>
                                <!-- Update by ilham 21/06/2024 No Ticket BDIT240001517 -->
                                <th style="border: 1px solid #ddd;">Status Laborat</th>
                                <th style="border: 1px solid #ddd;">Status QC</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            
                            $sql = sqlsrv_query(
                                    $con_db_laborat_sqlsrv,
                                    "
                                    SELECT
                                        [id],
                                        [no_counter],
                                        [suffix],
                                        [jenis_testing],
                                        [treatment],
                                        [buyer],
                                        [no_warna],
                                        [warna],
                                        [no_item],
                                        [jenis_kain],
                                        [tgl_terimakain],
                                        [tgl_approve_qc],
                                        [nama_personil_test],
                                        [permintaan_testing],
                                        [created_by],
                                        [sts],
                                        [sts_laborat],
                                        [note_laborat],
                                        [sts_qc],
                                        [diterima_oleh],
                                        [nama_penerima],
                                        [note_qc],
                                        [tgl_buat]
                                    FROM db_laborat.tbl_test_qc
                                    WHERE
                                        deleted_at IS NULL
                                        AND sts_qc = 'Kain OK'
                                        AND sts_laborat != 'Cancel'
                                        AND (
                                            sts_laborat = 'Approved Full'
                                            OR sts_laborat = 'Approved Parsial'
                                        )
                                    ORDER BY tgl_buat DESC
                                    "
                                );
                            if(!$sql){
                                die(print_r(sqlsrv_errors(), true));
                            }
                                                    
                            while ($r = sqlsrv_fetch_array($sql)) {
                                $bgcolor = ($col++ & 1) ? 'gainsboro' : 'antiquewhite';
                                $detail2 = explode(",", $r['permintaan_testing']);
                            ?>
                                <tr>
                                    <td valign="center">
                                        <?php echo $no++; ?>
                                    </td>
                                    <td valign="center" align="center"><a href="pages/cetak/cetak_label_lab.php?nocounter=<?= $r['no_counter'] ?>" class="btn btn-danger" target="_blank"><i class="fa fa-print"></i></a></td>
                                    <td valign="center" align="center"><?php echo $r['suffix']; ?></td>
                                    <td valign="center"><?php echo $r['no_counter']; ?></td>
                                    <td valign="center"><?php echo $r['jenis_testing']; ?></td>
                                    <td valign="center"><?php echo $r['treatment']; ?></td>
                                    <td valign="center"><?php echo $r['buyer']; ?></td>
                                    <td valign="center" align="left"><?php echo $r['no_warna']; ?></td>
                                    <td valign="center"><?php echo $r['warna']; ?></td>
                                    <td valign="center"><?php echo $r['no_item']; ?></td>
                                    <td valign="center"><?php echo $r['jenis_kain']; ?></td>
                                    <!-- Update by ilham 21/06/2024 No Ticket BDIT240001517 -->
                                    <td valign="center"><?php echo $r['tgl_terimakain']->format('Y-m-d'); ?></td>
                                    <td valign="center"><?php echo $r['tgl_approve_qc']->format('Y-m-d'); ?></td>
                                    <td valign="center"><?php echo $r['nama_personil_test']; ?></td>
                                    <td valign="center" align="left"><?php if ($r['permintaan_testing'] != "") {
                                                                            echo $r['permintaan_testing'];
                                                                        } else {
                                                                            echo "<span class='label label-danger blink_me'>FULL TEST</span>";
                                                                        } ?></td>
                                    <td valign="top" class="13"><?php echo $r['created_by']; ?>
                                        <hr class="divider"><span class="label <?php if ($r['sts'] == "normal") {
                                                                                    echo "label-warning";
                                                                                } else {
                                                                                    echo "label-danger blink_me";
                                                                                } ?>"><?php echo $r['sts']; ?></span>
                                    </td>
                                    <td valign="top" class="13"><span class="label <?php if ($r['sts_laborat'] == "Open") {
                                                                                        echo "label-info";
                                                                                    } else if ($r['sts_laborat'] == "Waiting Approval") {
                                                                                        echo "label-info";
                                                                                    } else if ($r['sts_laborat'] == "In Progress") {
                                                                                        echo "label-success";
                                                                                    } else {
                                                                                        echo "label-primary";
                                                                                    } ?>"><?php echo $r['sts_laborat']; ?></span>
                                        <hr class="divider">
                                        <em><?php echo $r['note_laborat']; ?></em>
                                    </td>
                                    <td align="left" valign="top" class="13"><?php if ($r['sts_qc'] == "Belum Terima Kain" or $r['sts_qc'] == "Kain Sudah diTes") {
                                                                                    echo "<a href='#' class='kain_terima' id='" . $r['id'] . "'>";
                                                                                } ?>
                                        <span class="label 
                                        <?php

                                        //  Update by ilham 21/06/2024 No Ticket BDIT240001517 
                                        if ($r['sts_qc'] == "Hasil Test Full") {
                                            $r['sts_qc'] = "Testing Selesai";
                                        }

                                        if ($r['sts_qc'] == "Tunggu Kain") {
                                            echo "label-primary";
                                        } else if ($r['sts_qc'] == "Sudah Terima Kain") {
                                            echo "label-success blink_me";
                                        } else if ($r['sts_qc'] == "Kain Sudah diTes") {
                                            echo "label-info";
                                        } else if ($r['sts_qc'] == "Kain Bisa Diambil") {
                                            echo "label-danger blink_me";
                                        } else {
                                            echo "label-warning";
                                        }
                                        ?>">
                                            <?php echo $r['sts_qc']; ?>
                                        </span>
                                        <?php if ($r['sts_qc'] == "Belum Terima Kain") {
                                            echo "</a>";
                                        } ?>
                                        <!-- Update by ilham 02/07/2024 -->
                                        <hr class="divider"><em>Pengirim: <strong><?php echo $r['diterima_oleh']; ?></strong><br>
                                            Penerima: <strong><?php echo $r['nama_penerima']; ?></strong></em><br>
                                        <hr class="divider"><em><a data-pk="<?php echo $r['id'] ?>" data-value="<?php echo $r['note_qc'] ?>" class="note_qc" href="javascript:void(0)"><?php echo $r['note_qc']; ?></a></em><br>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <div id="TerimaKain" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"> -->
</div>