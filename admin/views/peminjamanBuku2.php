<?php
require '../../session/sessionAdmin.php';
include 'database.php';
error_reporting(0);
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Buku</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css">
    <link rel="stylesheet" href="../assets/css/peminjamanBuku2.css">
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
    <?php
    // Cek apakah 'id_peminjaman' ada di URL (menggunakan GET)
    if (isset($_GET['id_peminjaman'])) {
        $id_peminjaman = $_GET['id_peminjaman'];

        // Sanitasi data (untuk menghindari SQL Injection)
        $id_peminjaman = $koneksi->real_escape_string($id_peminjaman);

        // Query untuk mengambil data dari tabel peminjaman, mahasiswa, dan buku
        // Mengecualikan status 'terlambat' dan 'dikembalikan'
        $sql = "
            SELECT p.id, p.nim, p.id_buku, p.status,
                  m.nama AS nama_mahasiswa, b.judul, b.stok, b.cover
            FROM peminjaman p
            JOIN mahasiswa m ON p.nim = m.nim
            JOIN buku b ON p.id_buku = b.id
            WHERE p.id = '$id_peminjaman'
            AND p.status NOT IN ('terlambat', 'dikembalikan')
        ";

        // Eksekusi query
        $result = $koneksi->query($sql);

        // Cek apakah data ditemukan
        if ($result->num_rows > 0) {
          // Ambil data
          $data = $result->fetch_assoc();
          
          // Variabel untuk data mahasiswa dan buku
          $nim = $data['nim'];
          $nama_mahasiswa = $data['nama_mahasiswa'];
          $id_buku = $data['id_buku'];
          $judul_buku = $data['judul'];
          $stok = $data['stok'];
          $cover = $data['cover']; // Ambil nama file cover
        } else {
          $pesan= "Buku sudah dikembalikan!";
        }
      }

        // Tutup koneksi
        $koneksi->close();
    ?>
    <main class="book-borrowing-container">
      <section class="card-container">
        <h3><?php echo $pesan; ?></h3>
        <form class="form-container" action="peminjamanBuku3.php" method="post">
          <h1 class="form-title">Informasi Peminjaman Buku</h1>
          <img
            src="../../CoverBook, OLLIE/<?php echo $cover; ?>"
            alt="Cover"
            class="form-image"
          />
          <div class="form-row">NIM</div>
          <input type="text" class="form-field field-nim" placeholder="NIM" value="<?php echo $nim; ?>" readonly />
          <div class="form-row">Nama Mahasiswa</div>
          <input type="text" class="form-field field-nama" placeholder="Nama" value="<?php echo $nama_mahasiswa; ?>" readonly />
          <div class="form-row">Judul Buku</div>
          <input
            type="text"
            class="form-field field-judul"
            placeholder="Judul Buku"
            value="<?php echo $judul_buku; ?>" 
            readonly 
          />
          <div class="form-row">ID Buku</div>
          <input
            type="text"
            class="form-field field-id"
            placeholder="ID Buku"
            value="<?php echo $id_buku; ?>" 
            readonly 
          />
          <div class="form-row">Stok</div>
          <input type="text" class="form-field field-stok" placeholder="Stok" value="<?php echo $stok; ?>" readonly />

          <a href="peminjamanBuku3.php?id_peminjaman=<?=$id_peminjaman ?>" class="submit-button">Terima</a>
        </form>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>