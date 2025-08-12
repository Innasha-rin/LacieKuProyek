<?php
require '../../session/sessionAdmin.php';
include 'database.php';
date_default_timezone_set('Asia/Makassar');
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Pengembalian Buku</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/pengembalianBuku2.css" />
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
    if (isset($_GET['id_pengembalian'])) {
        $id_pengembalian = $_GET['id_pengembalian'];

        // Sanitasi data (untuk menghindari SQL Injection)
        $id_pengembalian = $koneksi->real_escape_string($id_pengembalian);

        // Query untuk mencari data berdasarkan id_pengembalian
        // Mengecualikan status 'terlambat' dan 'dikembalikan'
        // Menambahkan JOIN dengan tabel pengembalian untuk mengambil deskripsi_kondisi
        $query = "
            SELECT
                p.id,
                p.nim,
                m.nama AS nama_mahasiswa,
                b.id AS id_buku,
                b.judul,
                b.stok,
                b.cover,
                p.status,
                p.tanggal_peminjaman,
                p.tanggal_jatuh_tempo,
                p.status_pengambilan,
                p.status_konfirmasi,
                pg.deskripsi_kondisi
            FROM
                peminjaman p
            JOIN
                mahasiswa m ON p.nim = m.nim
            JOIN
                buku b ON p.id_buku = b.id
            LEFT JOIN
                pengembalian pg ON p.id = pg.id_peminjaman
            WHERE
                p.id_pengembalian = '$id_pengembalian'
                AND p.status NOT IN ('terlambat', 'dikembalikan')
        ";

        // Eksekusi query
        $result = $koneksi->query($query);

        // Periksa apakah query menghasilkan hasil
        if ($result->num_rows > 0) {
            // Ambil data dari query
            $data = $result->fetch_assoc();

            // Assign data ke variabel
            $nim = $data['nim'];
            $nama_mahasiswa = $data['nama_mahasiswa'];
            $id_buku = $data['id_buku'];
            $judul = $data['judul'];
            $stok = $data['stok'];
            $cover = $data['cover'];
            $status = $data['status'];
            $tanggal_jatuh_tempo = $data['tanggal_jatuh_tempo'];
            $deskripsi_kondisi = $data['deskripsi_kondisi'] ?? ''; // Menambahkan deskripsi kondisi dengan fallback empty string

            // Bandingkan tanggal jatuh tempo dengan tanggal saat ini
            $currentDate = date('Y-m-d'); // Mendapatkan tanggal sekarang
            $dueDate = $tanggal_jatuh_tempo; // Tanggal jatuh tempo dari database

            // Variabel untuk denda
            $denda = 0;
            $status_jatuh_tempo = $dueDate;

            // Jika tanggal jatuh tempo sudah lewat
            if (strtotime($currentDate) > strtotime($dueDate)) {
              $tanggal1 = new DateTime($dueDate);
              $tanggal2 = new DateTime($currentDate);
              $selisih = $tanggal1->diff($tanggal2)->days;

              $denda = $selisih * 2000;
              $status_jatuh_tempo = "Terlambat $selisih hari";
          } else {
              $status_jatuh_tempo = "Belum Jatuh Tempo";
          }
        } else {
            $pesan = "Buku sudah dikembalikan!";
            // Inisialisasi variabel untuk menghindari error jika data tidak ditemukan
            $nim = '';
            $nama_mahasiswa = '';
            $id_buku = '';
            $judul = '';
            $stok = '';
            $cover = '';
            $status = '';
            $deskripsi_kondisi = '';
            $status_jatuh_tempo = '';
            $denda = 0;
        }
      }
        // Tutup koneksi
        $koneksi->close();
    ?>
    <main class="container">
      <section class="content-wrapper">
        <div class="header">
        <?php if (isset($pesan)): ?>
          <h3><?php echo $pesan; ?></h3>
        <?php endif; ?>
          <h1 class="title">Informasi Pengembalian Buku</h1>
          <?php if (!empty($cover)): ?>
          <img
            src="../../CoverBook, OLLIE/<?php echo $cover; ?>"
            alt="Book Return Image"
            class="book-icon"
          />
          <?php endif; ?>
        </div>

        <form class="form-container">
          <div class="form-row">
            <div class="form-group">Nim
              <input
                type="text"
                placeholder="NIM"
                class="form-input"
                aria-label="NIM"
                value="<?php echo htmlspecialchars($nim); ?>"
                readonly
              />
            </div>
            <div class="form-group">Status
              <input
                type="text"
                placeholder="Status Buku"
                class="form-input"
                aria-label="Status Buku"
                value="<?php echo htmlspecialchars($status); ?>"
                readonly
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">Nama
              <input
                type="text"
                placeholder="Nama"
                class="form-input"
                aria-label="Nama"
                value="<?php echo htmlspecialchars($nama_mahasiswa); ?>"
                readonly
              />
            </div>
            <div class="form-group">Jatuh Tempo
              <input
                type="text"
                placeholder="Status Jatuh Tempo"
                class="form-input"
                aria-label="Status Jatuh Tempo"
                value="<?php echo htmlspecialchars($status_jatuh_tempo); ?>"
                readonly
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">Judul Buku
              <input
                type="text"
                placeholder="Judul Buku"
                class="form-input"
                aria-label="Judul Buku"
                value="<?php echo htmlspecialchars($judul); ?>"
                readonly
              />
            </div>
            <div class="form-group">Denda
              <input
                type="text"
                placeholder="Total Denda"
                class="form-input"
                aria-label="Total Denda"
                value="Rp. <?php echo number_format($denda, 0, ',', '.'); ?>"
                readonly
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">Id Buku
              <input
                type="text"
                placeholder="ID Buku"
                class="form-input"
                aria-label="ID Buku"
                value="<?php echo htmlspecialchars($id_buku); ?>"
                readonly
              />
            </div>
            <div class="form-group">Deskripsi
              <textarea
                placeholder="Deskripsi kondisi buku"
                class="form-textarea"
                aria-label="Deskripsi"
                readonly
              ><?php echo htmlspecialchars($deskripsi_kondisi); ?></textarea>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">Stok
              <input
                type="text"
                placeholder="Stok"
                class="form-input"
                aria-label="Stok"
                value="<?php echo htmlspecialchars($stok); ?>"
                readonly
              />
            </div>
          </div>

          <div class="button-container">
            <button type="button" class="accept-button" onclick="window.location.href='pengembalianBuku3.php?id_pengembalian=<?=$id_pengembalian ?>'">Terima</button>
          </div>
        </form>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
