<?php
    session_start();
    include "koneksi.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_user']) && $_POST['simpan_user'] == 'Simpan') {
        $nama     = $_POST['nama'];

        $query = "INSERT INTO db_qc.tbl_jobdesc
            (nama) VALUES (?)";

        if (sqlsrv_query($con_db_qc_sqlsrv, $query, [$nama])) {
            $stmt_id = sqlsrv_query($con_db_qc_sqlsrv, "SELECT SCOPE_IDENTITY() AS last_id");
            $row_id  = sqlsrv_fetch_array($stmt_id, SQLSRV_FETCH_ASSOC);
            $last_id = isset($row_id['last_id']) ? (int)$row_id['last_id'] : 0;

            // Insert ke tbl_pembagian_testing_tq
            $query2 = "INSERT INTO db_qc.tbl_pembagian_testing_tq (id_jobdesc, jenis_testing) VALUES (?, '')";
            sqlsrv_query($con_db_qc_sqlsrv, $query2, [$last_id]);

        
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Data Berhasil Disimpan!',
                    text: 'Klik OK untuk kembali ke halaman user.',
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'pembagianTestingTQ';
                    }
                });
            </script>";
            exit;
        } else {
            $errs = sqlsrv_errors();
            $msg  = $errs ? $errs[0]['message'] : 'Unknown error';

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Gagal Menyimpan!',
                    text: '" . addslashes($msg) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.history.back();
                });
            </script>";
            exit;
        }
    }

    if (isset($_POST['update_pembagian_testing'])) {
        $id_jobdesc = intval($_POST['id_jobdesc']);
        $nama = isset($_POST['nama']) ? $_POST['nama'] : '';

        $id_users = isset($_POST['id_user']) && is_array($_POST['id_user']) ? $_POST['id_user'] : [];
        $jenis_testings = isset($_POST['jenis_testing']) && is_array($_POST['jenis_testing']) ? $_POST['jenis_testing'] : [];

        $jenis_testing_str = !empty($jenis_testings) ? implode(',', array_map(function($t) {
            return trim($t);
        }, $jenis_testings)) : null;

        sqlsrv_begin_transaction($con_db_qc_sqlsrv);

        try {
            // Update nama
            $update_nama_query = "UPDATE db_qc.tbl_jobdesc SET nama = ? WHERE id = ?";
            $stmt_update = sqlsrv_query($con_db_qc_sqlsrv, $update_nama_query, [$nama, $id_jobdesc]);
            if ($stmt_update === false) {
                throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Gagal update nama');
            }

            // Hapus data lama untuk id_jobdesc ini
            $stmt_delete = sqlsrv_query($con_db_qc_sqlsrv, "DELETE FROM db_qc.tbl_pembagian_testing_tq WHERE id_jobdesc = ?", [$id_jobdesc]);
            if ($stmt_delete === false) {
                throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Gagal delete data lama');
            }

            if (!empty($id_users)) {
                // Ada user → simpan semua user + jenis testing (kalau ada)
                foreach ($id_users as $user_id) {
                    $user_id_int = intval($user_id);

                    $stmt_insert = sqlsrv_query(
                        $con_db_qc_sqlsrv,
                        "INSERT INTO db_qc.tbl_pembagian_testing_tq (id_jobdesc, id_user, jenis_testing) VALUES (?, ?, ?)",
                        [$id_jobdesc, $user_id_int, $jenis_testing_str]
                    );

                    if ($stmt_insert === false) {
                        throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Gagal insert pembagian');
                    }
                }
            } else {
                // Tidak ada user → tetap simpan id_jobdesc dan jenis_testing (kalau ada)
                $stmt_insert = sqlsrv_query(
                    $con_db_qc_sqlsrv,
                    "INSERT INTO db_qc.tbl_pembagian_testing_tq (id_jobdesc, id_user, jenis_testing) VALUES (?, NULL, ?)",
                    [$id_jobdesc, $jenis_testing_str]
                );

                if ($stmt_insert === false) {
                    throw new Exception(sqlsrv_errors()[0]['message'] ?? 'Gagal insert pembagian (tanpa user)');
                }
            }

            sqlsrv_commit($con_db_qc_sqlsrv);

            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Data Berhasil Diperbarui!',
                    text: 'Klik OK untuk kembali ke halaman user.',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'pembagianTestingTQ';
                });
            </script>";
            exit;
        } catch (Exception $e) {
            sqlsrv_rollback($con_db_qc_sqlsrv);
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    title: 'Gagal Memperbarui!',
                    text: '" . addslashes($e->getMessage()) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
            exit;
        }
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title></title>
    </head>
    <body>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">PEMBAGIAN TESTING TQ</h3>
                        <br><br>
                        <!-- Tombol Add Description -->
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addDescModal">
                            + Add Description
                        </button>
                    </div>
                    <div class="box-body">
                        <table id="example3" class="table table-bordered table-hover table-striped display nowrap" width="100%">
                            <thead class="bg-blue">
                                <tr>
                                    <th><div align="center">NO</div></th>
                                    <th><div align="center">ACTION</div></th>
                                    <th><div align="center">JOB DESC</div></th>
                                    <th><div align="center">USER</div></th>
                                    <th><div align="center">JENIS TESTING</div></th>
                                    <th><div align="center">JUMLAH TESTING</div></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $users = sqlsrv_query($con_db_qc_sqlsrv, "SELECT * FROM db_qc.tbl_jobdesc");
                                    $no = 1;
                                    while($user = sqlsrv_fetch_array($users, SQLSRV_FETCH_ASSOC)) {
                                        // Ambil semua user dan jenis_testing untuk 1 id_jobdesc
                                        $pembagian_testing_q = sqlsrv_query($con_db_qc_sqlsrv, " SELECT
                                                users = STUFF((
                                                    SELECT DISTINCT ', ' + ul.[user]
                                                    FROM db_qc.tbl_pembagian_testing_tq p2
                                                    INNER JOIN db_qc.user_login ul ON ul.id = p2.id_user
                                                    WHERE p2.id_jobdesc = j.id
                                                    FOR XML PATH(''), TYPE
                                                ).value('.', 'nvarchar(max)'), 1, 2, ''),
                                                jenis_testing = MAX(p.jenis_testing)
                                            FROM db_qc.tbl_jobdesc j
                                            LEFT JOIN db_qc.tbl_pembagian_testing_tq p ON j.id = p.id_jobdesc
                                            WHERE j.id = ?
                                            GROUP BY j.id
                                        ", [$user['id']]);

                                        $pembagian_testing = sqlsrv_fetch_array($pembagian_testing_q, SQLSRV_FETCH_ASSOC);

                                        // Hitung jumlah jenis_testing
                                        $jumlah_testing = 0;
                                        if (!empty($pembagian_testing['jenis_testing'])) {
                                            $jenis_array = array_filter(array_map('trim', explode(',', $pembagian_testing['jenis_testing'])));
                                            $jumlah_testing = count($jenis_array);
                                        }
                                ?>
                                <tr>
                                    <td align="center"><?php echo $no; ?></td>
                                    <td align="center">
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editDescModal_<?php echo $user['id']; ?>">EDIT</button>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($pembagian_testing['users'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($pembagian_testing['jenis_testing'] ?? ''); ?></td>
                                    <td align="center"><?php echo $jumlah_testing; ?></td>
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

        <!-- Modal Tambah Desc -->
        <div class="modal fade" id="addDescModal" tabindex="-1" role="dialog" aria-labelledby="addDescLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form name="modal_popup" data-toggle="validator" method="post" action="" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <h5 class="modal-title" id="addDescLabel">Add New Description</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="color:white;">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Form Fields -->
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="simpan_user" value="Simpan" class="btn btn-success">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
            if (isset($_POST['update_pembagian_testing'])) {
                $id_jobdesc = intval($_POST['id_jobdesc']);

                $id_user = null;
                $jenis_testing = null;

                if (!empty($_POST['id_user']) && is_array($_POST['id_user'])) {
                    $id_user = implode(',', $_POST['id_user']);
                }

                if (!empty($_POST['jenis_testing']) && is_array($_POST['jenis_testing'])) {
                    $jenis_testing = implode(',', $_POST['jenis_testing']);
                }

                $stmt = sqlsrv_query(
                    $con_db_qc_sqlsrv, "UPDATE db_qc.tbl_pembagian_testing_tq
                     SET id_user = ?, jenis_testing = ?
                     WHERE id_jobdesc = ?",
                    [$id_user, $jenis_testing, $id_jobdesc]
                );

                if ($stmt !== false) {
                    echo "Data berhasil diupdate";
                } else {
                    $errs = sqlsrv_errors();
                    echo "Gagal update: " . ($errs ? $errs[0]['message'] : 'Unknown error');
                }
            }
        ?>

        <?php
            $jobDescriptions = sqlsrv_query($con_db_qc_sqlsrv, " SELECT
                    j.id AS id_jobdesc,
                    id_user = STUFF((
                        SELECT DISTINCT ',' + CONVERT(varchar(20), p2.id_user)
                        FROM db_qc.tbl_pembagian_testing_tq p2
                        WHERE p2.id_jobdesc = j.id
                        AND p2.id_user IS NOT NULL
                        FOR XML PATH(''), TYPE
                    ).value('.', 'nvarchar(max)'), 1, 1, ''),
                    jenis_testing = MAX(p.jenis_testing)
                FROM db_qc.tbl_jobdesc j
                LEFT JOIN db_qc.tbl_pembagian_testing_tq p ON p.id_jobdesc = j.id
                GROUP BY j.id
            ");

            while ($jobdesc = sqlsrv_fetch_array($jobDescriptions, SQLSRV_FETCH_ASSOC)):
                $selected_users_int = [];
                if (!empty($jobdesc['id_user'])) {
                    foreach (explode(',', $jobdesc['id_user']) as $p) {
                        $p = trim($p);
                        if ($p !== '' && ctype_digit($p)) {
                            $selected_users_int[] = (int)$p;
                        }
                    }
                }

                $whereSelected = '';
                if (!empty($selected_users_int)) {
                    $whereSelected = " OR id IN (" . implode(',', $selected_users_int) . ")";
                }

                $sqlUsers = " SELECT DISTINCT *
                            FROM db_qc.user_login
                            WHERE status = 'Aktif'
                            AND dept = 'QC'
                            AND (akses = 'admin' OR akses = 'biasa'){$whereSelected}
                            ORDER BY [user] ASC";
                $users = sqlsrv_query($con_db_qc_sqlsrv, $sqlUsers);

                $selected_testing = [];
                if (!empty($jobdesc['jenis_testing'])) {
                    $selected_testing = array_map('trim', explode(',', $jobdesc['jenis_testing']));
                }

                $jenisTesting = " SELECT DISTINCT
                        LTRIM(RTRIM(LEFT(physical, CHARINDEX(',', physical + ',') - 1))) AS physical
                    FROM db_qc.tbl_master_test
                    ORDER BY physical ASC
                ";
                $jenisTest = sqlsrv_query($con_db_qc_sqlsrv, $jenisTesting);

                $jenisTesting = "SELECT
                        users = STUFF((
                            SELECT DISTINCT ', ' + ul.[user]
                            FROM db_qc.tbl_pembagian_testing_tq p2
                            INNER JOIN db_qc.user_login ul ON ul.id = p2.id_user
                            WHERE p2.id_jobdesc = j.id
                            FOR XML PATH(''), TYPE
                        ).value('.', 'nvarchar(max)'), 1, 2, ''),
                        p.jenis_testing
                    FROM db_qc.tbl_jobdesc j
                    LEFT JOIN db_qc.tbl_pembagian_testing_tq p ON j.id = p.id_jobdesc
                    WHERE j.id = {$user['id']}
                    GROUP BY j.id, p.jenis_testing
                ";

                $jenis_testing_dipakai_lain = [];
                $res_dipakai_lain = sqlsrv_query($con_db_qc_sqlsrv, " SELECT jenis_testing
                    FROM db_qc.tbl_pembagian_testing_tq
                    WHERE id_jobdesc != " . intval($jobdesc['id_jobdesc']) . " AND jenis_testing IS NOT NULL
                ");

                while ($row = sqlsrv_fetch_array($res_dipakai_lain, SQLSRV_FETCH_ASSOC)) {
                    $arr = array_map('trim', explode(',', $row['jenis_testing']));
                    foreach ($arr as $j) {
                        if ($j !== '') {
                            $jenis_testing_dipakai_lain[] = $j;
                        }
                    }
                }
                $jenis_testing_dipakai_lain = array_unique($jenis_testing_dipakai_lain);

                $jenisTestingMaster = sqlsrv_query($con_db_qc_sqlsrv, " SELECT UPPER(value) AS physical
                    FROM db_qc.tbl_tq_mastertest t
                    WHERE is_active = 1
                ");
        ?>

        <!-- Modal Edit -->
        <div class="modal fade" id="editDescModal_<?php echo htmlspecialchars($jobdesc['id_jobdesc']); ?>" tabindex="-1" role="dialog" aria-labelledby="editUserLabel_<?php echo htmlspecialchars($jobdesc['id_jobdesc']); ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" action="">
                    <div class="modal-content">
                        <div class="modal-header bg-blue">
                            <h5 class="modal-title">Edit Pembagian Testing</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="color:white;">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="id_jobdesc" value="<?php echo htmlspecialchars($jobdesc['id_jobdesc']); ?>">

                            <!-- Input Nama Jobdesc -->
                            <div class="form-group">
                                <label>Nama Jobdesc</label>
                                <?php
                                    // Ambil nama jobdesc dari tbl_jobdesc untuk id ini
                                    $nama_jobdesc = '';
                                    $res_nama = sqlsrv_query($con_db_qc_sqlsrv, "SELECT nama FROM db_qc.tbl_jobdesc WHERE id = ?", [intval($jobdesc['id_jobdesc'])]);
                                    if ($row_nama = sqlsrv_fetch_array($res_nama, SQLSRV_FETCH_ASSOC)) {
                                        $nama_jobdesc = $row_nama['nama'];
                                    }
                                ?>
                                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($nama_jobdesc); ?>" required>
                            </div>

                            <!-- Select User -->
                            <div class="form-group">
                                <label>User</label>
                                <select name="id_user[]" class="form-select select2" multiple="multiple" data-placeholder="Pilih user QC" style="width:100%;">
                                    <?php
                                        while ($user = sqlsrv_fetch_array($users, SQLSRV_FETCH_ASSOC)):
                                    ?>
                                        <option value="<?php echo intval($user['id']); ?>"
                                            <?php if (in_array(intval($user['id']), $selected_users_int)) echo 'selected'; ?>>
                                            <?php echo strtoupper(htmlspecialchars($user['user'])) . ' - ' . strtoupper(htmlspecialchars($user['level'])); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Select Jenis Testing -->
                            <div class="form-group">
                                <label>Jenis Testing</label>
                                <select name="jenis_testing[]" class="form-select select2" multiple="multiple" data-placeholder="Pilih jenis testing" style="width:100%;">
                                    <?php while ($testing = sqlsrv_fetch_array($jenisTestingMaster, SQLSRV_FETCH_ASSOC)):
                                        $val = $testing['physical'];
                                        $disabled = '';
                                        if (in_array($val, $jenis_testing_dipakai_lain) && !in_array($val, $selected_testing)) {
                                            $disabled = 'disabled';
                                        }
                                    ?>
                                        <option value="<?php echo htmlspecialchars($val); ?>"
                                            <?php if (in_array($val, $selected_testing)) echo 'selected'; ?>
                                            <?php echo $disabled; ?>>
                                            <?php echo htmlspecialchars($val); ?>
                                            <?php if ($disabled) echo " (Sudah dipakai)"; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" name="update_pembagian_testing" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </body>
</html>

<?php if (isset($_SESSION['swal_success'])): ?>
<script>
    Swal.fire({
        title: 'Sukses!',
        text: 'Data berhasil disimpan.',
        icon: 'success',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
</script>
<?php unset($_SESSION['swal_success']); endif; ?>

<?php if (isset($_SESSION['swal_error'])): ?>
<script>
    Swal.fire({
        title: 'Gagal!',
        text: "<?php echo addslashes($_SESSION['swal_error']); ?>",
        icon: 'error',
        toast: true,
        position: 'top-end',
        showConfirmButton: true
    });
</script>
<?php unset($_SESSION['swal_error']); endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Inisialisasi Select2
        $('.select2').select2();
    });
</script>

