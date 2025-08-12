<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include __DIR__ . '/../database.php';


// Fungsi untuk mengirimkan email peminjaman
function kirimEmailPeminjaman($nim, $email, $nama_mahasiswa, $judul_buku, $penulis, $id_peminjaman, $id_pengembalian, $tanggal_pinjam, $tanggal_jatuh_tempo) {
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notification.ollie@gmail.com'; // Ganti dengan email kamu
        $mail->Password   = 'cunp fwrs ybot pkvy';   // Gunakan App Password dari Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Pengirim dan Penerima
        $mail->setFrom('notification.ollie@gmail.com', 'OLLIE');
        $mail->addAddress($email, $nama_mahasiswa);

        // Subjek Email
        $mail->Subject = "Notifikasi Peminjaman Buku";
        $mail->isHTML(true);

        // Isi email HTML
        $body = "
        <div style='font-family: Arial, sans-serif; line-height: 1.6;'>
            <h3>Halo $nama_mahasiswa,</h3>
            <p>Anda telah berhasil meminjam buku dari sistem <strong>OLLIE</strong> dengan rincian berikut:</p>
            <ul style='list-style: none; padding-left: 0;'>
                <li><strong>Judul Buku:</strong> $judul_buku</li>
                <li><strong>Penulis:</strong> $penulis</li>
                <li><strong>ID Peminjaman:</strong> $id_peminjaman</li>
                <li><strong>ID Pengembalian:</strong> $id_pengembalian</li>
                <li><strong>Tanggal Peminjaman:</strong> $tanggal_pinjam</li>
                <li><strong>Tanggal Jatuh Tempo:</strong> $tanggal_jatuh_tempo</li>
            </ul>
            <p>Harap pastikan buku dikembalikan tepat waktu untuk menghindari <strong>denda keterlambatan sebesar Rp2.000,00 per hari</strong>.</p>
            <p>Terima kasih telah menggunakan <strong>OLLIE!</strong></p>
            <br/>
            <span>Sistem Perpustakaan OLLIE</span>
        </div>";

        $mail->Body = $body;
        $mail->send();

        // ✅ Simpan ke tabel notifikasi jika email berhasil dikirim
        global $conn;
        $stmt = $conn->prepare("INSERT INTO notifikasi (nim, pesan, status, tanggal_kirim) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$nim, strip_tags($body), 'terkirim']);


        return true;
    } catch (Exception $e) {
        error_log("Email gagal dikirim: {$mail->ErrorInfo}");
        return false;
    }
}
?>
