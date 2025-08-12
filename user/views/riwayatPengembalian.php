<?php
require '../../session/sessionUser.php';
include 'database.php';

$nim = $_SESSION['nim']; // pastikan 'nim' sudah terset saat login

// Query gabungan 3 tabel dengan menambahkan kondisi_buku
$sql = "SELECT 
    buku.judul AS judul_buku,
    peminjaman.tanggal_peminjaman,
    pengembalian.tanggal_pengembalian,
    pengembalian.kondisi_buku,
    DATEDIFF(pengembalian.tanggal_pengembalian, peminjaman.tanggal_jatuh_tempo) AS keterlambatan,
    denda.jumlah AS denda
FROM 
    peminjaman
LEFT JOIN 
    buku ON peminjaman.id_buku = buku.id
LEFT JOIN 
    pengembalian ON peminjaman.id = pengembalian.id_peminjaman
LEFT JOIN 
    denda ON peminjaman.id = denda.id_peminjaman
WHERE 
    peminjaman.nim = '$_SESSION[nim]' AND peminjaman.status_pengambilan = 'diambil'
ORDER BY 
    peminjaman.tanggal_peminjaman DESC";

$result = $koneksi->query($sql);
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="description" content="Riwayat Pengembalian Buku - Sistem Perpustakaan" />
    <meta name="theme-color" content="#CEE397" />
    
    <!-- PWA Support -->
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    
    <title>Riwayat Pengembalian</title>
    <link rel="stylesheet" href="../assets/css/riwayatPengembalian.css" />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    
    <!-- Preload important resources -->
    <link rel="preload" href="../assets/images/ollie_teks.png" as="image" />
    
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
    
    <section class="return-history">
      <div class="card">
        <div class="card-header">
          <h1 class="page-title">Riwayat Pengembalian</h1>
          <div class="search-container">
            <input
              type="search"
              id="searchInput"
              class="search-input"
              placeholder="Cari Buku"
              aria-label="Cari pengguna"
              autocomplete="off"
              autocorrect="off"
              autocapitalize="off"
              spellcheck="false"
            />
          </div>
        </div>
        
        <div id="tableContainer" class="table-container">
          <!-- Desktop Table View -->
          <table class="return-table">
            <thead>
              <tr class="table-header">
                <th class="column-no">No</th>
                <th class="column-book">Judul Buku</th>
                <th class="column-borrow-date">Tanggal Pinjam</th>
                <th class="column-return-date">Tanggal Kembali</th>
                <th class="column-book-status">Status Buku</th>
                <th class="column-late-days">Keterlambatan (Hari)</th>
                <th class="column-fine">Denda</th>
              </tr>
            </thead>
            <tbody>
            <?php
              $no = 1;
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $terlambat = $row['keterlambatan'] > 0 ? $row['keterlambatan'] : 0;
                      $denda = $row['denda'] ? $row['denda'] : 0;

                      // Tangani tanggal pengembalian
                      if (empty($row['tanggal_pengembalian']) || $row['tanggal_pengembalian'] == '0000-00-00') {
                          $tanggal_pengembalian = "Belum Dikembalikan";
                          $kondisi_buku = "-";
                      } else {
                          $tanggal_pengembalian = date("d M Y", strtotime($row['tanggal_pengembalian']));
                          $kondisi_buku = !empty($row['kondisi_buku']) ? htmlspecialchars($row['kondisi_buku']) : "Tidak ada keterangan";
                      }

                      // Escape HTML untuk keamanan
                      $judul_buku = htmlspecialchars($row['judul_buku']);
                      $tanggal_peminjaman = date("d M Y", strtotime($row['tanggal_peminjaman']));

                      echo "<tr class='table-row' data-book-title='" . strtolower($judul_buku) . "'>
                              <td class='column-no'>{$no}</td>
                              <td class='column-book'>{$judul_buku}</td>
                              <td class='column-borrow-date'>{$tanggal_peminjaman}</td>
                              <td class='column-return-date'>{$tanggal_pengembalian}</td>
                              <td class='column-book-status'>{$kondisi_buku}</td>
                              <td class='column-late-days'>{$terlambat}</td>
                              <td class='column-fine'>Rp" . number_format($denda, 0, ',', '.') . "</td>
                            </tr>";
                      $no++;
                  }
              } else {
                  echo "<tr><td colspan='7' style='text-align:center; padding: 40px;'>Tidak ada data riwayat pengembalian.</td></tr>";
              }
              ?>
            </tbody>
          </table>
          
          <!-- Mobile cards will be generated by JavaScript -->
        </div>
      </div>
    </section>
    
    <!-- Scripts -->
    <script src="../assets/js/hamburger.js" defer></script>
    <script src="../assets/js/riwayatPengembalian.js" defer></script>
    
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