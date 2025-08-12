<?php
require '../../session/sessionAdmin.php';
include 'database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$sql = "SELECT 
          denda.nim,
          mahasiswa.nama,
          denda.id_peminjaman,
          buku.judul,
          denda.jumlah,
          denda.status,
          denda.status_verifikasi,
          denda.status_pembayaran,
          denda.tanggal_upload_bukti,
          denda.tanggal_verifikasi,
          peminjaman.tanggal_peminjaman,
          peminjaman.tanggal_jatuh_tempo
      FROM denda
      JOIN peminjaman ON denda.id_peminjaman = peminjaman.id
      JOIN buku ON peminjaman.id_buku = buku.id
      JOIN mahasiswa ON denda.nim = mahasiswa.nim
      ORDER BY denda.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$riwayat = $stmt->fetchAll();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pembayaran</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/riwayatPembayaran.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
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
        <div class="nav-item highlight"><a href="../../logout.php">Keluar</a></div>
    </nav>
    <section class="payment-history">
      <article class="history-card">
        <div class="history-container">
          <div class="history-header">
            <h1 class="history-title">Riwayat Pembayaran Denda</h1>
        </div>
          <div class="search-container">
            <input
              type="text"
              class="search-input"
              id="searchInput"
              placeholder="Cari Judul Buku atau Status"
              aria-label="Cari riwayat pembayaran"
            />
          </div>
          <div class="table-container" id="tableContainer">
            <table class="payment-table">
              <thead>
              <tr class="table-header-row">
              <th class="table-header-cell">No</th>
              <th class="table-header-cell">NIM</th>
              <th class="table-header-cell">Nama Mahasiswa</th>
              <th class="table-header-cell">ID Peminjaman</th>
              <th class="table-header-cell">Judul Buku</th>
              <th class="table-header-cell">Jumlah Denda</th>
              <th class="table-header-cell">Status Pembayaran</th>
              <th class="table-header-cell">Status Verifikasi</th>
              <th class="table-header-cell">Tanggal Upload Bukti</th>
              <th class="table-header-cell">Tanggal Verifikasi</th>
              <th class="table-header-cell">Tanggal Pinjam</th>
            </tr>
              </thead>
              <tbody>
              <?php if (count($riwayat) > 0): ?>
              <?php $no = 1; ?>
              <?php foreach ($riwayat as $item): ?>
              <tr class="table-row">
                <td class="table-cell"><?=$no++ ?></td>
                <td class="table-cell"><?= htmlspecialchars($item['nim']) ?></td>
                <td class="table-cell"><?= htmlspecialchars($item['nama']) ?></td>
                <td class="table-cell"><?= htmlspecialchars($item['id_peminjaman']) ?></td>
                <td class="table-cell"><?= htmlspecialchars($item['judul']) ?></td>
                <td class="table-cell">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                <td class="table-cell">
                  <span class="status-badge status-<?= $item['status'] == 'lunas' ? 'lunas' : 'belum-bayar' ?>">
                    <?= $item['status'] == 'lunas' ? 'Lunas' : 'Belum Bayar' ?>
                  </span>
                </td>
                  <td class="table-cell">
                    <span class="verification-badge verification-<?= str_replace(' ', '-', $item['status_verifikasi']) ?>">
                      <?php 
                        switch($item['status_verifikasi']) {
                          case 'belum diverifikasi':
                            echo 'Pending';
                            break;
                          case 'valid':
                            echo 'Valid';
                            break;
                          case 'tidak valid':
                            echo 'Invalid';
                            break;
                          default:
                            echo ucfirst($item['status_verifikasi']);
                        }
                      ?>
                    </span>
                  </td>
                  <td class="table-cell">
                      <?php if ($item['tanggal_upload_bukti']): ?>
                        <span class="date-text"><?= date('d/m/Y H:i', strtotime($item['tanggal_upload_bukti'])) ?></span>
                      <?php else: ?>
                        <span class="no-date">Belum Upload</span>
                      <?php endif; ?>
                    </td>
                    <td class="table-cell">
                      <?php if ($item['tanggal_verifikasi']): ?>
                        <span class="date-text"><?= date('d/m/Y H:i', strtotime($item['tanggal_verifikasi'])) ?></span>
                      <?php else: ?>
                        <span class="no-date">Belum Diverifikasi</span>
                      <?php endif; ?>
                    </td>
                    <td class="table-cell"><?= date('d/m/Y', strtotime($item['tanggal_peminjaman'])) ?></td>
                  </tr>
                  <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="11" class="no-data">Tidak ada riwayat pembayaran denda.</td>
                    </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </article>
    </section>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/riwayatPembayaran.js"></script>
  </body>
</html>