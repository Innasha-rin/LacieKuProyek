<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include __DIR__ . '/../database.php';

function kirimEmailPengembalian($nim, $email, $nama_mahasiswa, $judul_buku, $kondisi_buku, $status, $status_denda, $tanggal_kembali, $jumlah_denda = 0) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // Host SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notification.ollie@gmail.com';  // Ganti dengan email kamu
        $mail->Password   = 'cunp fwrs ybot pkvy';  // Gunakan App Password dari Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Pengirim dan Penerima
        $mail->setFrom('notification.ollie@gmail.com', 'OLLIE');
        $mail->addAddress($email, $nama_mahasiswa);

        // Subjek Email
        $mail->Subject = "Notifikasi Pengembalian Buku";
        $mail->isHTML(true);

        // Isi email HTML
        $body = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h3>Halo $nama_mahasiswa,</h3>
            <p>Terima kasih telah mengembalikan buku <strong>$judul_buku</strong> ke sistem <strong>OLLIE</strong>. Berikut adalah rincian pengembalian Anda:</p>
            <ul style='list-style: none; padding-left: 0;'>
                <li><strong>Judul Buku:</strong> $judul_buku</li>
                <li><strong>Status Buku:</strong> $kondisi_buku</li>
                <li><strong>Tanggal Pengembalian:</strong> $tanggal_kembali</li>
                <li><strong>Status Pengembalian:</strong> $status</li>
                <li><strong>Status Denda:</strong> $status_denda</li>";

        if ($status_denda === 'belum bayar' && $jumlah_denda > 0) {
            $body .= "<li><strong>Jumlah Denda:</strong> Rp " . number_format($jumlah_denda, 0, ',', '.') . "</li>";
        }

        $body .= "</ul><p>Terima kasih telah mengembalikan buku tepat waktu.</p><br><span>Sistem Perpustakaan OLLIE</span></div>";

        $mail->Body = $body;

        // Kirim email
        $mail->send();

        global $conn;
        $stmt = $conn->prepare("INSERT INTO notifikasi (nim, pesan, status, tanggal_kirim) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$nim, strip_tags($body), 'terkirim']);


        return true; // Email berhasil dikirim
    } catch (Exception $e) {
        // Jika terjadi error dalam pengiriman email
        error_log("Email gagal dikirim: {$mail->ErrorInfo}");
        return false; // Email gagal dikirim
    }
}
?>
