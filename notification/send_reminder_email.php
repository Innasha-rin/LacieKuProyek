<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Makassar');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
include __DIR__ . '/../database.php';

function kirimNotifikasi($conn, $nim, $email, $nama, $judul, $jenis, $tanggal_jatuh_tempo, $hari_terlambat = 0) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'notification.ollie@gmail.com'; // Ganti dengan email kamu
        $mail->Password = 'cunp fwrs ybot pkvy';       // Ganti dengan password app
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('notification.ollie@gmail.com', 'OLLIE');
        $mail->addAddress($email, $nama);
        $mail->isHTML(true);

        if ($jenis === "pengingat") {
            $mail->Subject = "Pengingat Jatuh Tempo Peminjaman Buku";
            $body = "
                <h3>Halo $nama,</h3>
                <p>Buku <strong>$judul</strong> akan jatuh tempo pada <strong>$tanggal_jatuh_tempo</strong>.</p>
                <p>Segera kembalikan untuk menghindari denda keterlambatan.</p>
                <p><em>Terima kasih telah menggunakan OLLIE!</em></p>";
            $status = "pengingat";
        } elseif ($jenis === "tenggat") {
            $mail->Subject = "Hari Ini Tenggat Pengembalian Buku";
            $body = "
                <h3>Halo $nama,</h3>
                <p>Hari ini adalah tenggat waktu pengembalian buku <strong>$judul</strong>.</p>
                <p>Segera kembalikan agar tidak terkena denda keterlambatan.</p>
                <p><em>Terima kasih telah menggunakan OLLIE!</em></p>";
            $status = "pengingat-tenggat";
        } else {
            $denda = $hari_terlambat * 2000;
            $mail->Subject = "Peringatan Denda Keterlambatan Buku";
            $body = "
                <h3>Halo $nama,</h3>
                <p>Buku <strong>$judul</strong> sudah melewati tenggat pengembalian pada <strong>$tanggal_jatuh_tempo</strong>.</p>
                <p>Anda terlambat selama <strong>$hari_terlambat hari</strong> dan terkena denda sebesar <strong>Rp$denda</strong>.</p>
                <p>Segera kembalikan buku untuk menghentikan akumulasi denda.</p>
                <p><em>Terima kasih telah menggunakan OLLIE!</em></p>";
            $status = "peringatan-denda";
        }

        $mail->Body = $body;
        $mail->send();

        // Simpan notifikasi ke database
        $stmt = $conn->prepare("INSERT INTO notifikasi (nim, pesan, status, tanggal_kirim) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$nim, strip_tags($body), 'terkirim']);

    } catch (Exception $e) {
        error_log("Gagal kirim notifikasi ke $email: {$mail->ErrorInfo}");
    }
}

// Ambil data peminjaman yang masih aktif
$sql = "SELECT p.*, m.nama AS nama_mahasiswa, m.email, b.judul 
        FROM peminjaman p
        JOIN mahasiswa m ON p.nim = m.nim
        JOIN buku b ON p.id_buku = b.id
        WHERE p.status_pengembalian = 'dipinjam'
        AND p.status_pengambilan = 'diambil'";

$result = $conn->query($sql);
$today = date('Y-m-d');

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $tanggal_jatuh_tempo = $row['tanggal_jatuh_tempo'];
    $diff = (strtotime($tanggal_jatuh_tempo) - strtotime($today)) / (60 * 60 * 24);

    if ($diff == 2 || $diff == 1) {
        // H-2 dan H-1 → pengingat biasa
        kirimNotifikasi($conn, $row['nim'], $row['email'], $row['nama_mahasiswa'], $row['judul'], "pengingat", $tanggal_jatuh_tempo);
    } elseif ($diff == 0) {
        // H-0 → hari tenggat pengembalian
        kirimNotifikasi($conn, $row['nim'], $row['email'], $row['nama_mahasiswa'], $row['judul'], "tenggat", $tanggal_jatuh_tempo);
    } elseif ($diff < 0) {
        // Sudah lewat tenggat → peringatan denda
        $hari_terlambat = abs($diff);
        kirimNotifikasi($conn, $row['nim'], $row['email'], $row['nama_mahasiswa'], $row['judul'], "peringatan", $tanggal_jatuh_tempo, $hari_terlambat);
    }
}
?>
