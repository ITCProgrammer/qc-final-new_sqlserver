<?php
session_start();
include '../koneksi.php'; 

function sqlsrv_fail($title = "SQLSRV Error") {
    $errs = sqlsrv_errors();
    echo $title . ": " . ($errs ? print_r($errs, true) : "Unknown error");
    exit;
}

if (isset($_POST['nodemand']) && isset($_POST['action'])) {
    $nodemand = trim((string)$_POST['nodemand']);
    $action   = trim((string)$_POST['action']);

    if ($nodemand === '') {
        echo "nodemand tidak valid.";
        exit;
    }

    $checkQuery = "SELECT TOP 1 * FROM db_qc.tbl_bonpenghubung_mail WHERE nodemand = ?";
    $checkStmt  = sqlsrv_prepare($con_db_qc_sqlsrv, $checkQuery, [$nodemand]);
    if (!$checkStmt) sqlsrv_fail("Gagal prepare query pengecekan");

    if (!sqlsrv_execute($checkStmt)) sqlsrv_fail("Gagal execute query pengecekan");

    $beforeRow = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC);
    if (!$beforeRow) {
        echo "Data dengan nodemand $nodemand tidak ditemukan.";
        exit;
    }

    echo "Data sebelum update: " . print_r($beforeRow, true);

    $approve = ($_SESSION['nama1'] ?? '') . '-(' . ($_SESSION['dept'] ?? '') . ')';

    if ($action === "approve") {
        $status = '1';
        $sql = "UPDATE db_qc.tbl_bonpenghubung_mail
                SET status_approve = ?, approve_mkt = ?
                WHERE nodemand = ?";
        $params = [$status, $approve, $nodemand];

    } elseif ($action === "reject") {
        $status = '99';
        $sql = "UPDATE db_qc.tbl_bonpenghubung_mail
                SET status_approve = ?, approve_mkt = ?
                WHERE nodemand = ?";
        $params = [$status, $approve, $nodemand];

    } elseif ($action === "closeApprove") {
        $status = '2';
        $sql = "UPDATE db_qc.tbl_bonpenghubung_mail
                SET status_approve = ?, closed_ppc = ?
                WHERE nodemand = ?";
        $params = [$status, $approve, $nodemand];

    } else {
        echo "Action tidak valid.";
        exit;
    }

    $stmt = sqlsrv_prepare($con_db_qc_sqlsrv, $sql, $params);
    if (!$stmt) sqlsrv_fail("Gagal prepare query update");

    if (!sqlsrv_execute($stmt)) sqlsrv_fail("Gagal execute query update");

    $affected = sqlsrv_rows_affected($stmt);
    if ($affected === false) sqlsrv_fail("Gagal membaca rows affected");

    if ($affected <= 0) {
        echo "Query dieksekusi tapi 0 rows affected. Cek nodemand match / tipe kolom / data.";
        exit;
    }

    echo "Update sukses. Rows affected: $affected\n";

    if ($action === "approve") {
        require '../vendor/autoload.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host       = 'mail.indotaichen.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dept.it@indotaichen.com';
        $mail->Password   = 'Xr7PzUWoyPA';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('dept.it@indotaichen.com', 'DEPT IT');
        $mail->addAddress('qcf.adm@indotaichen.com', 'ADM QCF');
        $mail->addAddress('tobias.sulistiyo@indotaichen.com', 'TOBIAS');
        $mail->addAddress('septian.saputra@indotaichen.com', 'Septian Saputra');

        // ambil pelanggan sekali (biar gak query berulang di loop)
        $pelanggan = '';
        $qcfStmt = sqlsrv_prepare($con_db_qc_sqlsrv, "SELECT TOP 1 pelanggan FROM db_qc.tbl_qcf WHERE nodemand = ?", [$nodemand]);
        if ($qcfStmt && sqlsrv_execute($qcfStmt)) {
            $qcfRow = sqlsrv_fetch_array($qcfStmt, SQLSRV_FETCH_ASSOC);
            $pelanggan = $qcfRow['pelanggan'] ?? '';
        }

        $user_email = sqlsrv_query($con_db_qc_sqlsrv, "SELECT * FROM db_qc.email_user_penghubung WHERE dept='PPC'");
        while ($data_email = sqlsrv_fetch_array($user_email, SQLSRV_FETCH_ASSOC)) {
            if ($pelanggan !== '' && stripos($pelanggan, $data_email['sales_detail']) !== false) {
                $mail->addAddress($data_email['email'], $data_email['user']);
            }
        }

        $mail->Subject = 'Closed Bon Penghubung QCF-' . htmlspecialchars($nodemand);
        $mail->isHTML(true);
        $mail->Body = "<p>Dear PPC Teams,</p>
                       <p>Mohon ditindaklanjuti terkait Approval Bon Penghubung.</p>
                       <p>Bon Penghubung sebelumnya sudah di Approve oleh {$_SESSION['nama1']}-({$_SESSION['dept']})</p>
                       <p>Mohon untuk dapat CLOSED Bon Penghubung ini</p>
                       <p>&nbsp;</p>
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sebelum melakukan CLOSE, Mohon untuk login terlebih dahulu <a href='online.indotaichen.com/Qc-Final-New'>Login</a></p>
                       <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Anda dapat melakukan CLOSED pada link berikut: <a href='online.indotaichen.com/Qc-Final-New/ApproveBonPenghubung-" . htmlspecialchars($nodemand) . "'>Closed Bon Penghubung</a></p>
                       <p>&nbsp;</p>";

        if ($mail->send()) {
            echo "Email berhasil dikirim!\n";
        } else {
            echo "Update sukses tapi email gagal dikirim: " . $mail->ErrorInfo . "\n";
        }
    } else {
        echo "Tidak kirim email karena action = $action\n";
    }

    $afterStmt = sqlsrv_prepare($con_db_qc_sqlsrv, $checkQuery, [$nodemand]);
    if (!$afterStmt) sqlsrv_fail("Gagal prepare query cek setelah update");
    if (!sqlsrv_execute($afterStmt)) sqlsrv_fail("Gagal execute query cek setelah update");

    $afterRow = sqlsrv_fetch_array($afterStmt, SQLSRV_FETCH_ASSOC);
    echo "Data setelah update: " . print_r($afterRow, true);

    sqlsrv_close($con_db_qc_sqlsrv);

} else {
    echo "nodemand atau action tidak ditemukan.";
}
