<?php
require '../../session/sessionAdmin.php';
include 'database.php';

$sql = "
  SELECT 
    p.id AS id_peminjaman,
    m.nama AS nama_mahasiswa,
    m.nim,
    b.judul AS judul_buku,
    p.tanggal_peminjaman,
    p.tanggal_jatuh_tempo
  FROM 
    peminjaman p
  JOIN 
    mahasiswa m ON p.nim = m.nim
  JOIN 
    buku b ON p.id_buku = b.id
  WHERE
    p.status_pengambilan = 'diambil'
  ORDER BY p.tanggal_peminjaman DESC"; // Menampilkan data terbaru terlebih dahulu

// Eksekusi query
$result = $koneksi->query($sql);

// Mengecek apakah query berhasil dijalankan
if (!$result) {
    die("Error: " . $koneksi->error);
}
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
              placeholder="Cari pengguna"
              aria-label="Cari pengguna"
            />
          </div>
          <div class="table-container" id="tableContainer">
            <table class="borrowing-table">
            <thead>
              <tr class="table-header-row">
                  <th class="table-header-cell">No</th>
                  <th class="table-header-cell">Nama</th>
                  <th class="table-header-cell">NIM</th>
                  <th class="table-header-cell">Judul Buku</th>
                  <th class="table-header-cell">Tanggal Pinjam</th>
                  <th class="table-header-cell">Jatuh Tempo</th>
              </tr>
            </thead>
            <tbody>
                <?php
                // Inisialisasi nomor urut (No)
                $no = 1;
                
                // Periksa apakah ada hasil query
                if ($result->num_rows > 0) {
                      // Looping melalui hasil query
                      while ($data = $result->fetch_assoc()) {
                          echo "<tr class='table-row'>";
                          echo "<td class='table-cell'>" . $no . "</td>"; // Menampilkan nomor urut
                          echo "<td class='table-cell'>" . $data['nama_mahasiswa'] . "</td>"; // Nama mahasiswa dengan link ke akunUser.php
                          echo "<td class='table-cell'>" . $data['nim'] . "</td>"; // NIM mahasiswa dengan link ke akunUser.php
                          echo "<td class='table-cell'>" . $data['judul_buku'] . "</td>"; // Judul buku
                          echo "<td class='table-cell'>" . $data['tanggal_peminjaman'] . "</td>"; // Tanggal pinjam
                          echo "<td class='table-cell'>" . $data['tanggal_jatuh_tempo'] . "</td>"; // Tanggal jatuh tempo
                          echo "</tr>";
                          $no++; // Menambah nomor urut
                      }
                  } else {
                      echo "<tr><td colspan='6'>Tidak ada data peminjaman</td></tr>";
                  }
                  ?>
              </tbody>
              </table>
          </div>
        </div>
      </article>
    </section>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/riwayatPeminjaman.js"></script>
  </body>
</html>
