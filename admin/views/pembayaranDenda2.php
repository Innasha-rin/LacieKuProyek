<?php
require '../../session/sessionAdmin.php';
include 'database.php';

$id_peminjaman = $_GET['id_peminjaman'];

// Query: ambil data mahasiswa, buku, dan denda berdasarkan id_peminjaman
$query = "
SELECT 
    m.nama,
    m.nim,
    b.judul,
    d.jumlah AS denda,
    d.bukti_pembayaran,
    d.id_peminjaman
FROM 
    peminjaman p
JOIN 
    mahasiswa m ON p.nim = m.nim
JOIN 
    buku b ON p.id_buku = b.id
LEFT JOIN 
    denda d ON p.id = d.id_peminjaman
WHERE 
    p.id = '$id_peminjaman'
";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);
$filename = $data['bukti_pembayaran'];
$file_path = "../../user/receipt/$filename";
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pembayaran Denda</title>
    <link rel="stylesheet" href="../assets/css/pembayaranDenda2.css">
    <link rel="stylesheet" href="../assets/css/hamburger.css">
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
    <main class="payment-fine-container">
      <section class="payment-card">
      <form class="payment-form" method="post" action="pembayaranDenda3.php">
        <h1 class="payment-title">Pembayaran Denda</h1>

        <input
          type="text"
          class="input-field name-field"
          name="nama"
          value="<?= $data['nama'] ?>"
          readonly
        />

        <input
          type="text"
          class="input-field nim-field"
          name="nim"
          value="<?= $data['nim'] ?>"
          readonly
        />

        <input
          type="text"
          class="input-field book-title-field"
          name="judul"
          value="<?= $data['judul'] ?>"
          readonly
        />

        <input
          type="text"
          class="input-field total-fine-field"
          name="total_denda"
          value="Rp. <?= number_format($data['denda'], 0, ',', '.') ?>,00"
          readonly
        />

        <a href="<?= $file_path ?>" download>
          <button type="button" class="download-button">
            Unduh bukti pembayaran
          </button>
        </a>

        <button type="submit" class="accept-button">
          <a href="pembayaranDenda3.php?id_peminjaman=<?= $data['id_peminjaman'] ?>">Terima</a>
        </button>
  
      </form>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
