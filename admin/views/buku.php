<?php
require '../../session/sessionAdmin.php';
include 'database.php';

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Detail Buku</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css"/>
    <link rel="stylesheet" href="../assets/css/buku.css" />
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
<article class="book-container">
    <div class="book-wrapper">
      <div class="book-content">
        <div class="availability-badge" id="stok">
          <?= $buku['stok'] ? 'TERSEDIA' : 'TIDAK TERSEDIA'; ?>
        </div>
        <section class="book-details-container" id="buku-detail">
        <div class="book-image-container">
          <img
            src="../assets/images/<?= htmlspecialchars($buku['cover']) ?>"
            alt="Cover Buku"
            class="book-image"
          />
        </div>
          <h2 class="book-title"><?= htmlspecialchars($buku['judul']) ?></h2>
          <p class="book-description">
            <?= nl2br(htmlspecialchars($buku['sinopsis'])) ?>
          </p>
          <div class="book-info-badge">Jumlah halaman: <?= $buku['jumlah_halaman'] ?></div>
          <div class="book-info-badge">Penulis: <?= htmlspecialchars($buku['penulis']) ?></div>
          <div class="book-info-badge">ISBN: ?= htmlspecialchars($buku['isbn']) ?></div>
          <div class="book-info-badge">Tahun terbit: <?= $buku['tahun_terbit'] ?></div>
          <div class="book-info-badge">Penerbit: <?= htmlspecialchars($buku['penerbit']) ?></div>
        </section>
      </div>
    </div>
  </article>
  <script src="../assets/js/buku_ajax.js"></script>
  <script src="../assets/js/hamburger.js"></script>
  </body>
  </html>
  