<?php
require '../../session/sessionAdmin.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css" />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
  </head>
  <body>
  <!-- Loading indicator -->
  <div id="loadingIndicator" class="loading-overlay">
    <div class="spinner"></div>
  </div>
  
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
    <main class="dashboard">
      <div class="welcome-header">
        <p class="welcome-text">Selamat datang,</p>
        <h1 class="user-name"><?= htmlspecialchars($nama) ?></h1>
        <button id="refreshData" class="refresh-btn">
          <i class="fas fa-sync-alt"></i> Refresh Data
        </button>
      </div>
      <section class="dashboard-content">
        <section class="stats-cards">
          <article class="stat-card">
            <h2 class="stat-title">Total Buku</h2>
            <p class="stat-value">memuat...</p>
          </article>
          <article class="stat-card">
            <h2 class="stat-title">Total Pengguna</h2>
            <p class="stat-value">memuat...</p>
          </article>
          <article class="stat-card">
            <h2 class="stat-title">Total Peminjaman Hari Ini</h2>
            <p class="stat-value">memuat...</p>
          </article>
          <article class="stat-card">
            <h2 class="stat-title">Total Keterlambatan</h2>
            <p class="stat-value">memuat...</p>
          </article>
        </section>
        <div id="unverifiedPaymentsNotification" class="notification-container">
          <div class="notification-content">
            <i class="fas fa-bell notification-icon"></i>
            <span class="notification-text">Pembayaran belum diverifikasi: <span id="unverifiedPaymentsCount">Memuat...</span></span>
            <a href="pembayaranDenda.php" class="notification-link">Lihat Detail</a>
          </div>
        </div>
        <section class="statistics-section">
          <h2 class="section-title">Statistik Peminjaman</h2>
          <figure class="chart-container">
            <canvas id="borrowingChart"></canvas>
          </figure>
        </section>
        <section class="popular-books-section">
          <h2 class="section-title">Buku Terpopuler</h2>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr class="table-header">
                  <th class="table-cell">No</th>
                  <th class="table-cell">Judul Buku</th>
                  <th class="table-cell">Kategori</th>
                </tr>
              </thead>
              <tbody>
                <tr class="table-row">
                  <td class="table-cell" colspan="3">Memuat data...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
        <section class="active-borrowers-section">
          <h2 class="section-title">Peminjam Aktif</h2>
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr class="table-header">
                  <th class="table-cell">No</th>
                  <th class="table-cell">NIM</th>
                  <th class="table-cell">Nama</th>
                </tr>
              </thead>
              <tbody>
                <tr class="table-row">
                  <td class="table-cell" colspan="3">Memuat data...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
        <section class="late-returns-section">
          <h2 class="section-title">Tagihan Denda</h2>
          <div class="table-container">
            <table class="data-table late-table">
              <thead>
                <tr class="table-header">
                  <th class="table-cell">No</th>
                  <th class="table-cell">NIM</th>
                  <th class="table-cell">Nama</th>
                  <th class="table-cell">Judul Buku</th>
                  <th class="table-cell">Total Denda</th>
                </tr>
              </thead>
              <tbody>
                <tr class="table-row">
                  <td class="table-cell" colspan="5">Memuat data...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/dashboard.js"></script>
  </body>
</html>