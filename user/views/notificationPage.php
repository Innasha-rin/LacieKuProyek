<?php
require '../../session/sessionUser.php';
require_once 'notificationPage_class.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$notification = new Notification();
$nim = $_SESSION['nim'];
$unreadCount = $notification->getUnreadCount($nim);
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Notifikasi</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/notificationPage.css" />
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
        <a href="user start page.php"><div class="nav-item">Home</div></a>
        <a href="bookCollection.php"><div class="nav-item">Koleksi Buku</div></a>
        <a href="paymentFine.php"><div class="nav-item">Pembayaran Denda</div></a>
        <a href="report book.php"><div class="nav-item">Lapor Buku</div></a>
        <a href="riwayatPeminjaman.php"><div class="nav-item">Riwayat Peminjaman</div></a>
        <a href="riwayatPengembalian.php"><div class="nav-item">Riwayat Pengembalian</div></a>
        <a href="riwayatPembayaran.php"><div class="nav-item">Riwayat Pembayaran</div></a>
        <a href="user account.php"><div class="nav-item">Profil</div></a>
        <a href="notificationPage.php"><div class="nav-item">Notifikasi </div></a>
        <a href="faq.php"><div class="nav-item">FAQ</div></a>
        <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
    </nav>
    <main class="notification-page">
      <section class="notification-container">
        <h1 class="notification-title">Notifikasi</h1>

        <!-- Loading indicator -->
        <div id="loadingIndicator" class="loading-indicator" style="display: none;">
            <p>Memuat notifikasi...</p>
        </div>

        <!-- No notifications message -->
        <div id="noNotifications" class="no-notifications" style="display: none;">
            <p>Tidak ada notifikasi</p>
        </div>

        <div class="notification-table" id="notificationTable">
          <div class="table-header">
            <div class="table-cell">No</div>
            <div class="table-cell cell-centered">Waktu</div>
            <div class="table-cell cell-right">Tanggal</div>
            <div class="table-cell cell-right">Status</div>
          </div>

          <!-- Dynamic content will be loaded here -->
          <div id="notificationRows">
            <!-- Rows will be populated by JavaScript -->
          </div>
        </div>
      </section>
    </main>
    
    <script src="../assets/js/notificationPage_back.js"></script>
    <script src="../assets/js/notificationPage.js"></script>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>