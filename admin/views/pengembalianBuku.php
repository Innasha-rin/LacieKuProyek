<?php
require '../../session/sessionAdmin.php';
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengembalian Buku</title>
    <link rel="stylesheet" href="../assets/css/pengembalianBuku.css">
    <link rel="stylesheet" href="../assets/css/hamburger.css"/>
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
    <main class="book-return-container">
      <section class="book-return-card">
        <div class="book-return-form">
          <div class="header-container">
            <h1 class="main-title">Pengembalian Buku</h1>
            <p class="subtitle">Masukkan ID Pengembalian Mahasiswa</p>
          </div>
          <!-- Form untuk mengirimkan ID Pengembalian -->
          <form action="pengembalianBuku2.php" method="get">
            <input 
              type="text" 
              name="id_pengembalian" 
              class="return-id-input" 
              placeholder="ID Pengembalian" 
              required
            >
            <div class="button-container">
              <button class="search-button" type="submit">Cari</button>
            </div>
          </form>
        </div>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
