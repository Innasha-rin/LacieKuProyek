<?php
require '../../session/sessionAdmin.php';
include 'database.php';

$query = "
  SELECT 
    m.nama AS nama_mahasiswa,
    m.nim,
    b.judul,
    p.tanggal_peminjaman,
    pg.kondisi_buku,
    pg.tanggal_pengembalian,
    p.tanggal_jatuh_tempo,
    d.jumlah AS jumlah_denda
  FROM pengembalian pg
  JOIN peminjaman p ON pg.id_peminjaman = p.id
  JOIN mahasiswa m ON p.nim = m.nim
  JOIN buku b ON p.id_buku = b.id
  LEFT JOIN denda d ON d.id_peminjaman = p.id
  ORDER BY pg.tanggal_pengembalian DESC
";

$result = $koneksi->query($query);

$no = 1; // Nomor urut
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pengembalian</title>
    <link rel="stylesheet" href="../assets/css/riwayatPengembalian.css" />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
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
    <section class="return-history">
      <div class="card">
        <div class="card-header">
          <h1 class="page-title">Riwayat Pengembalian</h1>
          <div class="search-container">
            <input
              type="search"
              id="searchInput"
              class="search-input"
              placeholder="Cari pengguna"
              aria-label="Cari pengguna"
            />
          </div>
        </div>
        <div id="tableContainer" class="table-container">
          <table class="return-table">
            <thead>
              <tr class="table-header">
                <th class="column-no">No</th>
                <th class="column-name">Nama</th>
                <th class="column-nim">NIM</th>
                <th class="column-book">Judul Buku</th>
                <th class="column-borrow-date">Tanggal Pinjam</th>
                <th class="column-return-date">Tanggal Kembali</th>
                <th class="column-late-days">Status Buku</th>
                <th class="column-late-days">Keterlambatan (Hari)</th>
                <th class="column-fine">Denda</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
              <?php
                // Hitung keterlambatan
                $tanggal_jatuh_tempo = new DateTime($row['tanggal_jatuh_tempo']);
                $tanggal_pengembalian = new DateTime($row['tanggal_pengembalian']);
                $selisih = $tanggal_jatuh_tempo->diff($tanggal_pengembalian)->days;

                if ($tanggal_pengembalian > $tanggal_jatuh_tempo) {
                    $keterlambatan = $selisih . " hari";
                } else {
                    $keterlambatan = "Tidak terlambat";
                }

                // Format denda ke format rupiah
                $denda = $row['jumlah_denda'] ? "Rp" . number_format($row['jumlah_denda'], 2, ',', '.') : "Rp0,00";
                
                // Format status buku dengan warna
                $kondisi_buku = htmlspecialchars($row['kondisi_buku']);
                $status_class = '';
                switch(strtolower($kondisi_buku)) {
                    case 'baik':
                        $status_class = 'status-baik';
                        break;
                    case 'bermasalah':
                        $status_class = 'status-bermasalah';
                        break;
                    default:
                        $status_class = '';
                }
              ?>
              <tr class="table-row">
                <td class="column-no"><?= $no++; ?></td>
                <td class="column-name"><?= htmlspecialchars($row['nama_mahasiswa']); ?></td>
                <td class="column-nim"><?= htmlspecialchars($row['nim']); ?></td>
                <td class="column-book"><?= htmlspecialchars($row['judul']); ?></td>
                <td class="column-borrow-date"><?= date('d F Y', strtotime($row['tanggal_peminjaman'])); ?></td>
                <td class="column-return-date"><?= date('d F Y', strtotime($row['tanggal_pengembalian'])); ?></td>
                <td class="column-status">
                  <span class="<?= $status_class; ?>"><?= ucfirst($kondisi_buku); ?></span>
                </td>
                <td class="column-late-days"><?= $keterlambatan; ?></td>
                <td class="column-fine"><?= $denda; ?></td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/riwayatPengembalian.js"></script>
  </body>
</html>