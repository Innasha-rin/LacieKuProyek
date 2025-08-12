<?php
require '../../session/sessionAdmin.php';
require 'akunUser_ambilData.php'; // ambil data user, buku, dan denda
ini_set('display_errors', 1);
error_reporting(0);

if (!isset($user)) {
    // Jika user tidak ditemukan atau ID tidak valid, redirect
    header("Location: daftarUsers.php");
    exit();
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akun Pengguna</title>
  <link rel="stylesheet" href="../assets/css/akunUser.css"/>
  <link rel="stylesheet" href="../assets/css/hamburger.css"/>
</head>
<body>
<header>
  <div class="logo"><img src="../assets/images/ollie_teks.png" alt="logoOllie"></div>
  <div class="hamburger"><span></span><span></span><span></span></div>
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

<main class="profile-container">
  <section class="profile-card">
    <img src="../assets/images/ProfilAdmin.png">

    <h1 class="username"><?= htmlspecialchars($user['nama']) ?></h1>

    <svg class="verification-badge" width="114" height="30" viewBox="0 0 114 30" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M0 0H114V30H0V0Z" fill="#3797A4"></path>
      <text fill="white" font-family="Inter" font-size="14"><tspan x="7" y="20.6">TERVERIFIKASI</tspan></text>
    </svg>

    <form class="form-container">
      <div class="form-group"><label class="form-label">NIM</label><input type="text" value="<?= htmlspecialchars($user['nim']) ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Nama</label><input type="text" value="<?= htmlspecialchars($user['nama']) ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Email</label><input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Jenis Kelamin</label><input type="text" value="<?= htmlspecialchars($user['jenis_kelamin']) ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Tanggal Lahir</label><input type="date" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Nomor HP</label><input type="text" value="<?= htmlspecialchars($user['no_hp']) ?>" class="form-input" disabled></div>

      <div class="form-group"><label class="form-label">Buku 1 yang Dipinjam</label><input type="text" value="<?= htmlspecialchars($bukuDipinjam[0] ?? '-') ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Buku 2 yang Dipinjam</label><input type="text" value="<?= htmlspecialchars($bukuDipinjam[1] ?? '-') ?>" class="form-input" disabled></div>
      <div class="form-group"><label class="form-label">Total Denda</label><input type="text" value="Rp <?= number_format($totalDenda, 0, ',', '.') ?>" class="form-input" disabled></div>
    </form>
  </section>
</main>

<script src="../assets/js/hamburger.js"></script>
</body>
</html>
