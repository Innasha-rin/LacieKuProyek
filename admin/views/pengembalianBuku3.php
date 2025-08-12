<?php
require '../../session/sessionAdmin.php';
include 'database.php';
require '../../notification/send_pengembalian_email.php';

date_default_timezone_set('Asia/Makassar');

// Pastikan ada id_pengembalian dari GET
if (!isset($_GET['id_pengembalian'])) {
  die("ID Pengembalian tidak ditemukan.");
}

$id_pengembalian = $_GET['id_pengembalian'];
$id_pengembalian = $koneksi->real_escape_string($id_pengembalian);

// CEK APAKAH ADA LAPORAN DARI USER (NEW FEATURE)
$cekLaporan = $koneksi->query("SELECT deskripsi_kondisi FROM pengembalian WHERE id = '$id_pengembalian'");
$dataLaporan = $cekLaporan->fetch_assoc();
$deskripsi_kondisi = $dataLaporan['deskripsi_kondisi'] ?? '';

// Tentukan kondisi buku berdasarkan ada/tidaknya deskripsi
if (!empty($deskripsi_kondisi)) {
    $kondisi_buku = 'bermasalah';
    $status_kondisi = 'Laporan:'.$deskripsi_kondisi;
} else {
    $kondisi_buku = 'baik';
    $status_kondisi = 'Tidak ada laporan masalah';
}

// Ambil data peminjaman berdasarkan id_pengembalian
$query = "
  SELECT 
    p.id AS id_peminjaman,
    p.nim,
    m.nama AS nama_mahasiswa,
    m.email AS email_mahasiswa, 
    b.id AS id_buku,
    b.judul,
    b.stok,
    b.cover,
    p.status,
    p.tanggal_peminjaman,
    p.tanggal_jatuh_tempo,
    p.status_pengambilan,
    p.status_konfirmasi
  FROM 
    peminjaman p
  JOIN 
    mahasiswa m ON p.nim = m.nim
  JOIN 
    buku b ON p.id_buku = b.id
  WHERE 
    p.id_pengembalian = '$id_pengembalian'
";

$result = $koneksi->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Assign data ke variabel
    $id_peminjaman = $data['id_peminjaman'];
    $nim = $data['nim'];
    $nama_mahasiswa = $data['nama_mahasiswa'];
    $email_mahasiswa = $data['email_mahasiswa'];
    $id_buku = $data['id_buku'];
    $judul = $data['judul'];
    $stok = $data['stok'];
    $cover = $data['cover'];
    $status = $data['status'];
    $tanggal_peminjaman = $data['tanggal_peminjaman'];
    $tanggal_jatuh_tempo = $data['tanggal_jatuh_tempo'];
    $status_pengambilan = $data['status_pengambilan'];
    $status_konfirmasi = $data['status_konfirmasi'];

    $currentDate = date('Y-m-d');
    $dueDate = $tanggal_jatuh_tempo;

    // Tentukan status baru berdasarkan tanggal jatuh tempo
    if ($currentDate > $dueDate) {
        $new_status = 'terlambat';
        $status_denda = 'belum bayar';
    } else {
        $new_status = 'dikembalikan';
        $status_denda = 'tidak ada';
    }

    $new_status_pengembalian = 'dikembalikan';

    // Update status peminjaman
    $updateStatus = $koneksi->query("UPDATE peminjaman 
      SET status = '$new_status', 
          status_pengembalian = '$new_status_pengembalian' 
      WHERE id_pengembalian = '$id_pengembalian'");

    if (!$updateStatus) {
        die("Gagal update status peminjaman: " . $koneksi->error);
    }

    // Update stok buku (hanya jika kondisi baik)
    if ($kondisi_buku === 'baik') {
        $updateStok = $koneksi->query("UPDATE buku 
          SET stok = stok + 1
          WHERE id = '$id_buku'");
    } else {
        // Jika bermasalah, buku tidak dikembalikan ke stok
        echo "<!-- Buku bermasalah, stok tidak ditambahkan -->";
    }

    if (!$updateStok && $kondisi_buku === 'baik') {
        die("Gagal update stok buku: " . $koneksi->error);
    }

    // Update/Insert tabel pengembalian dengan kondisi buku yang sudah ditentukan
    $insertPengembalian = $koneksi->query("INSERT INTO pengembalian
      (id, id_peminjaman, tanggal_pengembalian, kondisi_buku, status_denda, status_konfirmasi, deskripsi_kondisi)
      VALUES
      ('$id_pengembalian', '$id_peminjaman', '$currentDate', '$kondisi_buku', '$status_denda', 'sudah', '$deskripsi_kondisi')
      ON DUPLICATE KEY UPDATE
      kondisi_buku = '$kondisi_buku',
      deskripsi_kondisi = '$deskripsi_kondisi'");
    
    if (!$insertPengembalian) {
        die("Gagal insert/update pengembalian: " . $koneksi->error);
    }

    // Perhitungan denda (sama seperti sebelumnya)
    $tanggal_pengembalian = new DateTime($currentDate);
    $tanggal_jatuh_tempo = new DateTime($data['tanggal_jatuh_tempo']);

    if ($tanggal_pengembalian > $tanggal_jatuh_tempo) {
        $selisih_hari = $tanggal_pengembalian->diff($tanggal_jatuh_tempo)->days;
        $jumlah_denda = $selisih_hari * 2000;

        $resultLastDenda = $koneksi->query("SELECT MAX(id) AS max_id FROM denda");
        $rowLastDenda = $resultLastDenda->fetch_assoc();
        $id_denda_baru = ($rowLastDenda['max_id'] ?? 0) + 1;

        $insertDenda = $koneksi->query("INSERT INTO denda
          (id, nim, id_peminjaman, jumlah, status, bukti_pembayaran, status_verifikasi, status_pembayaran)
          VALUES
          ('$id_denda_baru', '$nim', '$id_peminjaman', '$jumlah_denda', 'belum bayar', NULL, 'belum diverifikasi', 'menunggu')
        ");

        if (!$insertDenda) {
            die("Gagal menambahkan denda: " . $koneksi->error);
        }
    }

    // Kirim email notifikasi (dengan info kondisi buku)
    kirimEmailPengembalian($nim, $email_mahasiswa, $nama_mahasiswa, $judul, $kondisi_buku, $new_status, $status_denda, $currentDate, ($status_denda === 'belum bayar') ? $jumlah_denda : 0, $kondisi_buku, $deskripsi_kondisi);

} else {
    echo "Data pengembalian tidak ditemukan.";
}

$koneksi->close();
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Buku berhasil dikembalikan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/pengembalianBuku3.css" />
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
    
    <main class="success-container">
      <section class="success-modal">
        <h1 class="success-title">Buku Berhasil Dikembalikan</h1>
        
        <?php if (!empty($deskripsi_kondisi)): ?>
        <div class="warning-section">
          <h3 style="color: #ff6b35;">Ada Laporan Masalah!</h3>
          <p style="background: #fff3cd; padding: 10px; border-radius: 5px; border-left: 4px solid #ff6b35;">
            <strong>Kondisi:</strong> <?php echo htmlspecialchars($kondisi_buku); ?><br>
            <strong>Deskripsi:</strong> <?php echo htmlspecialchars($deskripsi_kondisi); ?>
          </p>
        </div>
        <?php else: ?>
        <p style="color: #28a745;">✅ Buku dalam kondisi baik</p>
        <?php endif; ?>
        <br>
        <p class="success-message">
          Tekan tombol di bawah untuk kembali ke pengembalian buku
        </p>
        <a href="pengembalianBuku.php"><button class="success-button">Ok</button></a>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>