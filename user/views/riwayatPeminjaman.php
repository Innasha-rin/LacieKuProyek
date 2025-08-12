<?php
require '../../session/sessionUser.php';
include 'database.php';

if (!isset($_SESSION['nim'])) {
  header("Location: ../login.php");
  exit();
}

$nim = $_SESSION['nim'];

$sql = "SELECT 
          buku.judul,
          peminjaman.tanggal_peminjaman,
          peminjaman.tanggal_jatuh_tempo
      FROM peminjaman
      JOIN buku ON peminjaman.id_buku = buku.id
      WHERE peminjaman.nim = :nim AND peminjaman.status_pengambilan = 'diambil'
      ORDER BY peminjaman.tanggal_peminjaman DESC";

$stmt = $conn->prepare($sql);
$stmt->execute(['nim' => $nim]);
$riwayat = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Peminjaman</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/riwayatPeminjaman.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
    <style>
      /* Inline critical CSS untuk mencegah FOUC */
      body { 
        font-family: Inter, -apple-system, Roboto, Helvetica, sans-serif; 
        margin: 0; 
        padding: 0;
        -webkit-text-size-adjust: 100%;
        -webkit-tap-highlight-color: transparent;
      }
      
      /* Loading state */
      .loading-placeholder {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        color: #666;
        font-size: 16px;
      }
      
      /* Smooth transitions */
      * {
        -webkit-transition: all 0.2s ease;
        transition: all 0.2s ease;
      }
    </style>
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
        <a href="notificationPage.php"><div class="nav-item">Notifikasi</div></a>
        <a href="faq.php"><div class="nav-item">FAQ</div></a>
        <a href="../../logout.php"><div class="nav-item highlight">Keluar</div></a>
    </nav>
    <section class="borrowing-history">
      <article class="history-card">
        <div class="history-container">
          <div class="history-header">
            <h1 class="history-title">Riwayat Peminjaman</h1>
        </div>
          <div class="search-container">
            <input
              type="text"
              class="search-input"
              id="searchInput"
              placeholder="Cari Buku"
              aria-label="Cari pengguna"
            />
          </div>
          <div class="table-container" id="tableContainer">
            <table class="borrowing-table">
              <thead>
                <tr class="table-header-row">
                  <th class="table-header-cell">No</th>
                  <th class="table-header-cell">Judul Buku</th>
                  <th class="table-header-cell">Tanggal Pinjam</th>
                  <th class="table-header-cell">Jatuh Tempo</th>
                </tr>
              </thead>
              <tbody>
              <?php if (count($riwayat) > 0): ?>
                <?php $no = 1; ?>
                <?php foreach ($riwayat as $item): ?>
                <tr class="table-row">
                  <td class="table-cell"><?=$no++ ?></td>
                  <td class="table-cell"><?= htmlspecialchars($item['judul']) ?></td>
                  <td class="table-cell"><?= htmlspecialchars($item['tanggal_peminjaman']) ?></td>
                  <td class="table-cell"><?= htmlspecialchars($item['tanggal_jatuh_tempo']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3">Tidak ada riwayat peminjaman.</td>
                  </tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </article>
    </section>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/riwayatPeminjaman.js"></script>
    <!-- Performance optimization -->
    <script>
      // Lazy loading for non-critical resources
      if ('loading' in HTMLImageElement.prototype) {
        // Browser supports lazy loading
        const images = document.querySelectorAll('img[data-src]');
        images.forEach(img => {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
        });
      }
      
      // Add touch-friendly class for mobile
      if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
      }
    </script>
  </body>
</html>
