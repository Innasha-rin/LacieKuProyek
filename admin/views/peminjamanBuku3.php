<?php
require '../../session/sessionAdmin.php';
include 'database.php';

    // Pastikan ada id_peminjaman dari GET
    if (!isset($_GET['id_peminjaman'])) {
      die("ID Peminjaman tidak ditemukan.");
    }

    $id_peminjaman = $_GET['id_peminjaman'];

    // Update status_pengambilan dan status_konfirmasi
    $updatePeminjaman = $koneksi->query("UPDATE peminjaman 
      SET status_pengambilan = 'diambil', status_konfirmasi = 'sudah'
      WHERE id = '$id_peminjaman'");

    if (!$updatePeminjaman) {
      die("Gagal update peminjaman: " . $koneksi->error);
    }

    // Ambil id_buku dari peminjaman
    $ambilData = $koneksi->query("SELECT id_buku FROM peminjaman WHERE id = '$id_peminjaman'");
    if ($ambilData->num_rows == 0) {
      die("Data peminjaman tidak ditemukan.");
    }
    $data = $ambilData->fetch_assoc();
    $id_buku = $data['id_buku'];

?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Berhasil</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css">
    <link rel="stylesheet" href="../assets/css/peminjamanBuku3.css">
  </head>
  <body>
  <header>
        <div class="logo"><img src="../assets/images/ollie_teks.png" alt="logoOllie"></div>
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </header>

    <nav class="nav-menu">
        <a href="dashboard.php"><div class="nav-item">Home</div></a>
        <a href="manajemenBuku.php"><div class="nav-item">Manajemen Buku</div></a>
        <a href="peminjamanBuku.php"><div class="nav-item">Peminjaman Buku</div></a>
        <a href="pengembalianBuku.php"><div class="nav-item">Pengembalian Buku</div></a>
        <a href="pembayaranDenda.php"><div class="nav-item">Pembayaran Denda</div></a>
        <a href="daftarUsers.php"><div class="nav-item">Daftar Pengguna</div></a>
        <a href="riwayatPeminjaman.php"><div class="nav-item">Riwayat Peminjaman</div></a>
        <a href="riwayatPengembalian.php"><div class="nav-item">Riwayat Pengembalian</div></a>
        <a href="riwayatPembayaran.php"><div class="nav-item">Riwayat Pembayaran</div></a>
        <a href="akunAdmin.php"><div class="nav-item">Profil</div></a>
        <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
    </nav>
    <main class="success-container">
      <section class="success-card">
        <h1 class="success-title">Buku Berhasil Dipinjam</h1>
        <p class="success-message">
          Tekan tombol di bawah untuk kembali ke manajemen buku
        </p>
        <div class="button-container">
          <a href="peminjamanBuku.php"><button class="ok-button">Ok</button></a>
        </div>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
