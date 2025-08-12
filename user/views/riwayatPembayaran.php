<?php
require '../../session/sessionUser.php';
include 'database.php';

if (!isset($_SESSION['nim'])) {
  header("Location: ../login.php");
  exit();
}

$nim = $_SESSION['nim'];

$sql = "SELECT 
          peminjaman.id as id_peminjaman,
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
      WHERE denda.nim = :nim
      ORDER BY denda.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute(['nim' => $nim]);
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
              placeholder="Cari Judul Buku, ID Peminjaman, atau Status"
              aria-label="Cari riwayat pembayaran"
            />
          </div>
          
          <!-- Desktop Table View -->
          <div class="table-container" id="tableContainer">
            <table class="payment-table">
              <thead>
                <tr class="table-header-row">
                  <th class="table-header-cell">No</th>
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
                  <td class="table-cell">
                    <span class="id-peminjaman"><?= htmlspecialchars($item['id_peminjaman']) ?></span>
                  </td>
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
                    <td colspan="9" class="no-data">Tidak ada riwayat pembayaran denda.</td>
                  </tr>
              <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards View -->
          <div class="mobile-cards-container" id="mobileCardsContainer">
            <?php if (count($riwayat) > 0): ?>
              <?php $no = 1; ?>
              <?php foreach ($riwayat as $item): ?>
              <div class="payment-card" data-search-content="<?= htmlspecialchars(strtolower($item['judul'] . ' ' . $item['id_peminjaman'] . ' ' . $item['status'] . ' ' . $item['status_verifikasi'])) ?>">
                <div class="card-header">
                  <div class="card-id">
                    Peminjaman #<?= htmlspecialchars($item['id_peminjaman']) ?>
                  </div>
                  <div class="card-number">#<?= $no++ ?></div>
                </div>
                
                <div class="card-title"><?= htmlspecialchars($item['judul']) ?></div>
                
                <div class="card-details">
                  <div class="card-detail">
                    <div class="card-detail-label">Jumlah Denda</div>
                    <div class="card-detail-value card-amount">Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></div>
                  </div>
                  <div class="card-detail">
                    <div class="card-detail-label">Tanggal Pinjam</div>
                    <div class="card-detail-value"><?= date('d/m/Y', strtotime($item['tanggal_peminjaman'])) ?></div>
                  </div>
                </div>

                <div class="card-status-row">
                  <span class="status-badge status-<?= $item['status'] == 'lunas' ? 'lunas' : 'belum-bayar' ?>">
                    <?= $item['status'] == 'lunas' ? 'Lunas' : 'Belum Bayar' ?>
                  </span>
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
                </div>

                <div class="card-dates">
                  <div class="date-row">
                    <span class="date-label">Upload Bukti:</span>
                    <span class="date-value">
                      <?php if ($item['tanggal_upload_bukti']): ?>
                        <?= date('d/m/Y H:i', strtotime($item['tanggal_upload_bukti'])) ?>
                      <?php else: ?>
                        <em>Belum Upload</em>
                      <?php endif; ?>
                    </span>
                  </div>
                  <div class="date-row">
                    <span class="date-label">Verifikasi:</span>
                    <span class="date-value">
                      <?php if ($item['tanggal_verifikasi']): ?>
                        <?= date('d/m/Y H:i', strtotime($item['tanggal_verifikasi'])) ?>
                      <?php else: ?>
                        <em>Belum Diverifikasi</em>
                      <?php endif; ?>
                    </span>
                  </div>
                  <div class="date-row">
                    <span class="date-label">Jatuh Tempo:</span>
                    <span class="date-value"><?= date('d/m/Y', strtotime($item['tanggal_jatuh_tempo'])) ?></span>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="mobile-no-data">Tidak ada riwayat pembayaran denda.</div>
            <?php endif; ?>
          </div>
        </div>
      </article>
    </section>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/riwayatPembayaran.js"></script>
  </body>
</html>