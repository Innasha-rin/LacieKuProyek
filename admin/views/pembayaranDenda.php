<?php
require '../../session/sessionAdmin.php';
include 'database.php';

$query = "
SELECT
    m.nim,
    m.nama,
    p.tanggal_peminjaman,
    DATEDIFF(pg.tanggal_pengembalian, p.tanggal_jatuh_tempo) AS keterlambatan,
    d.id_peminjaman,
    d.jumlah,
    d.status_verifikasi,
    d.status
FROM
    mahasiswa m
JOIN
    peminjaman p ON m.nim = p.nim
JOIN
    pengembalian pg ON p.id = pg.id_peminjaman
JOIN
    denda d ON p.id = d.id_peminjaman
WHERE
    pg.tanggal_pengembalian > p.tanggal_jatuh_tempo
AND d.status_verifikasi = 'belum diverifikasi'
AND d.status_pembayaran = 'menunggu'
ORDER BY
    pg.tanggal_pengembalian ASC
";

$result = mysqli_query($koneksi, $query);
$no = 1;
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pembayaran Denda</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css"/>
    <link rel="stylesheet" href="../assets/css/pembayaranDenda.css" />
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

    <main class="fine-payment-container">
      <section class="fine-payment-card">
        <div class="fine-payment-content">
          <div class="fine-payment-header">
            <h1 class="fine-payment-title">Pembayaran Denda</h1>
          </div>

          <div class="search-container">
            <input
              type="text"
              id="searchInput"
              class="search-input"
              placeholder="Cari pengguna..."
              autocomplete="off"
            />
          </div>

          <div class="table-wrapper">
            <!-- Fixed Header -->
            <div class="table-header-container">
              <table class="payment-table">
                <thead>
                  <tr class="table-header-row">
                    <th class="table-header-cell">No</th>
                    <th class="table-header-cell">NIM</th>
                    <th class="table-header-cell">Nama</th>
                    <th class="table-header-cell">Tanggal Peminjaman</th>
                    <th class="table-header-cell">Keterlambatan</th>
                    <th class="table-header-cell">Jumlah</th>
                    <th class="table-header-cell">Status Verifikasi</th>
                    <th class="table-header-cell">Status Pembayaran</th>
                    <th class="table-header-cell">Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>

            <!-- Scrollable Content -->
            <div class="table-container" id="tableContainer">
              <table class="payment-table" id="paymentTable">
                <tbody>
                  <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                  <tr class="table-row">
                    <td class="table-cell"><?= $no++ ?></td>
                    <td class="table-cell">
                      <p>
                        <?= htmlspecialchars($row['nim']) ?>
                      </p>
                    </td>
                    <td class="table-cell">
                      <p>
                        <?= htmlspecialchars($row['nama']) ?>
                      </p>
                    </td>
                    <td class="table-cell">
                      <?= date("d M Y", strtotime($row['tanggal_peminjaman'])) ?>
                    </td>
                    <td class="table-cell">
                      <?= $row['keterlambatan'] ?> hari
                    </td>
                    <td class="table-cell">
                      Rp. <?= number_format($row['jumlah'], 0, ',', '.') ?>
                    </td>
                    <td class="table-cell">
                      <p>
                        <?= htmlspecialchars($row['status_verifikasi']) ?>
                      </p>
                    </td>
                    <td class="table-cell">
                      <p>
                        <?= htmlspecialchars($row['status']) ?>
                      </p>
                    </td>
                    <td class="table-cell">
                      <div class="action-buttons">
                        <button class="action-button">
                          <a href="pembayaranDenda2.php?id_peminjaman=<?= $row['id_peminjaman'] ?>">
                            KONFIR
                          </a>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </main>
    
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/pembayaranDenda.js"></script>
  </body>
</html>