<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Makassar');

require '../../session/sessionUser.php';
include 'database.php';

// Masukkan file email_notification.php yang berisi fungsi kirimEmailPeminjaman
require '../../notification/send_peminjaman_email.php';

// Fungsi untuk generate ID unik
function generateRandomId($koneksi, $tabel, $kolom) {
    do {
        $randomId = mt_rand(100000000, 999999999); // 9 digit random
        $stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM $tabel WHERE $kolom = ?");
        $stmt->bind_param("s", $randomId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    } while ($data['total'] > 0);

    return $randomId;
}

// Variable untuk pesan error
$error_message = "";
$success = false;

// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    $error_message = "Anda harus login terlebih dahulu.";
} else {
    $nim = $_SESSION['nim'];

    // Validasi ID buku
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        $error_message = "ID Buku tidak valid.";
    } else {
        $id_buku = intval($_GET['id']);

        // Cek keberadaan NIM
        $stmt = $koneksi->prepare("SELECT nim FROM mahasiswa WHERE nim = ?");
        $stmt->bind_param("s", $nim);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error_message = "NIM tidak ditemukan.";
        } else {
            // Cek keberadaan buku
            $stmt = $koneksi->prepare("SELECT id, judul, penulis FROM buku WHERE id = ?");
            $stmt->bind_param("i", $id_buku);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $error_message = "Buku tidak ditemukan.";
            } else {
                $book = $result->fetch_assoc();
                $judul_buku = $book['judul'];
                $penulis = $book['penulis'];
                // Tambahan: Cek apakah stok habis
                $stmt_stok = $koneksi->prepare("SELECT stok FROM buku WHERE id = ?");
                $stmt_stok->bind_param("i", $id_buku);
                $stmt_stok->execute();
                $result_stok = $stmt_stok->get_result();
                $data_stok = $result_stok->fetch_assoc();

                if ($data_stok['stok'] <= 0) {
                    $error_message = "Stok buku '$judul_buku' sedang kosong. Tidak dapat melakukan peminjaman.";
                } else {
                    // Cek jumlah buku yang sedang dipinjam oleh user (status masih 'dipinjam')
                $stmt = $koneksi->prepare("SELECT COUNT(*) as total FROM peminjaman WHERE nim = ? AND status = 'dipinjam'");
                $stmt->bind_param("s", $nim);
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_assoc();

                if ($data['total'] >= 2) {
                    $error_message = "Maksimal meminjam 2 buku dalam satu waktu. Silakan kembalikan buku sebelum meminjam lagi.";
                } else {
                    // Ambil email mahasiswa
                    $stmt = $koneksi->prepare("SELECT email, nama FROM mahasiswa WHERE nim = ?");
                    $stmt->bind_param("s", $nim);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();
                    $email = $user['email'];
                    $nama_mahasiswa = $user['nama'];

                    // Generate data otomatis
                    $id_peminjaman     = generateRandomId($koneksi, 'peminjaman', 'id');
                    $id_pengembalian   = generateRandomId($koneksi, 'peminjaman', 'id_pengembalian');
                    $pin_peminjaman    = generateRandomId($koneksi, 'peminjaman', 'pin_peminjaman');
                    $pin_pengembalian  = generateRandomId($koneksi, 'peminjaman', 'pin_pengembalian');
                    $tanggal_peminjaman = date('Y-m-d');
                    $tanggal_jatuh_tempo = date('Y-m-d', strtotime('+7 days'));
                    $status = "dipinjam";
                    $status_pengambilan = "belum diambil";
                    $status_konfirmasi = "belum";
                    $status_pengembalian = "dipinjam";

                    // Masukkan data ke tabel peminjaman
                    $stmt = $koneksi->prepare("INSERT INTO peminjaman 
                    (id, nim, id_buku, tanggal_peminjaman, tanggal_jatuh_tempo, status, id_pengembalian, status_pengambilan, pin_peminjaman, pin_pengembalian, status_konfirmasi, status_pengembalian) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("isisssssssss", $id_peminjaman, $nim, $id_buku, $tanggal_peminjaman, $tanggal_jatuh_tempo, $status, $id_pengembalian, $status_pengambilan, $pin_peminjaman, $pin_pengembalian, $status_konfirmasi, $status_pengembalian);
                    // Kurangi stok buku sebanyak 1
                    $stmt_update_stok = $koneksi->prepare("UPDATE buku SET stok = stok - 1 WHERE id = ?");
                    $stmt_update_stok->bind_param("i", $id_buku);
                    $stmt_update_stok->execute();
                    if ($stmt->execute()) {
                        // Kirim email notifikasi peminjaman
                        kirimEmailPeminjaman($nim, $email, $nama_mahasiswa, $judul_buku, $penulis, $id_peminjaman, $id_pengembalian, $tanggal_peminjaman, $tanggal_jatuh_tempo);
                        $success = true;
                    } else {
                        $error_message = "Terjadi kesalahan saat memproses peminjaman.";
                        } 
                    }
                }
            }
        }
    }
}

if (isset($stmt)) {
    $stmt->close();
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Peminjaman Buku</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/peminjamanbuku.css" />
</head>
<body>
<header>
    <div class="logo"><img src="../assets/images/ollie_teks.png" alt="logoOllie"></div>
    <div class="hamburger">
        <span></span><span></span><span></span>
    </div>
</header>

<nav class="nav-menu">
    <a href="user start page.php"><div class="nav-item">Home</div></a>
    <a href="bookCollection.php"><div class="nav-item">Koleksi Buku</div></a>
    <a href="paymentFine.php"><div class="nav-item">Pembayaran Denda</div></a>
    <a href="report book.php"><div class="nav-item">Lapor Buku</div></a>
    <a href="riwayatPeminjaman.php"><div class="nav-item">Riwayat Peminjaman</div></a>
    <a href="riwayatPengembalian.php"><div class="nav-item">Riwayat Pengembalian</div></a>
    <a href="riwayatPembayaran.php"><div class="nav-item">Riwayat Pembayaran</div></a>
    <a href="user account.php"><div class="nav-item">Profil</div></a>
    <a href="notificationPage.php"><div class="nav-item">Notifikasi</div></a>
    <a href="faq.php"><div class="nav-item">FAQ</div></a>
    <a href="../../logout.php"><div class="nav-item">Logout</div></a>
</nav>

<main class="book-container">
    <section class="book-card">
        <?php if (!empty($error_message)): ?>
            <h1 class="book-title">Peminjaman Tidak Berhasil</h1>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php elseif ($success): ?>
            <h1 class="book-title">Peminjaman Buku</h1>
            <div class="input-wrapper">
                <label class="form-label">ID Peminjaman</label>
                <div class="book-input"><?php echo $id_peminjaman; ?></div>
                <label class="form-label">ID Pengembalian</label>
                <div class="book-input"><?php echo $id_pengembalian; ?></div>
            </div>
            <p class="notification-info">
                Kedua ID tersebut juga dikirim lewat<br />
                notifikasi sistem dan email pengguna.
            </p>
            <a href="user start page.php" class="home-button">Beranda</a>
        <?php else: ?>
            <h1 class="book-title">Terjadi Kesalahan</h1>
            <div class="alert alert-error">
                Terjadi kesalahan yang tidak diketahui.
            </div>
            <a href="user start page.php" class="back-button">Kembali ke Beranda</a>
        <?php endif; ?>
    </section>
</main>

<script src="../assets/js/hamburger.js"></script>
</body>
</html>