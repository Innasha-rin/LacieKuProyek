<?php
require '../../session/sessionUser.php';
include 'database.php';

if (!isset($_SESSION['nim'])) {
  header("Location: ../login.php");
  exit();
}

$nim = $_SESSION['nim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pengguna</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/hamburger.css" />
  <link rel="stylesheet" href="../assets/css/user start page.css" />
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
  <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
</nav>

<div class="spacer"></div>
<div class="welcome-text">
  <span>Selamat datang,</span>
  <h1 class="user-name"><?= htmlspecialchars($nama) ?></h1>
</div>

<section class="main-container">

  <a href="bookCollection.php" class="collection-button">Koleksi Buku</a>

  <div class="borrowed-books" id="borrowedBooksContainer">
    <p class="count" id="bookCount">-</p>
    <p class="label">Buku yang dipinjam</p>
    <button class="refresh-btn" onclick="refreshDashboard()">Refresh</button>
  </div>

  <div class="book-grid" id="bookGrid">
    <!-- Books will be loaded here via AJAX -->
  </div>

  <div id="fineContainer">
    <a href="paymentFine.php"><p class="fine-status" id="fineStatus">Denda: -</p></a>
  </div>
  
  <a href="report book.php" class="report-button">Lapor Buku</a>
</section>

<!-- Notification container -->
<div id="notification" class="notification"></div>

<script src="../assets/js/hamburger.js"></script>
<script src="../assets/js/userStartPageBack.js"></script>
</body>
</html>