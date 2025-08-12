<?php
require '../../session/sessionUser.php';
include 'database.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Makassar');

$nim = $_SESSION['nim'] ?? '';
$nama_mahasiswa = $_SESSION['nama'] ?? '';
$email_mahasiswa = $_SESSION['email'] ?? '';

$pesan = '';
$status = '';
$id_pengembalian = '';
$judul_buku = '';

// Proses form jika ada data POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $returning_id = $koneksi->real_escape_string($_POST['returning_id'] ?? '');
    $description = $koneksi->real_escape_string($_POST['description'] ?? '');
    
    // Validasi input
    if (empty($returning_id) || empty($description)) {
        $pesan = "Semua field harus diisi!";
        $status = "error";
    } else {
        // Validasi apakah ID pengembalian valid dan milik user ini
        $validasiQuery = "SELECT p.id, p.id_pengembalian, b.judul, p.status_pengembalian
                         FROM peminjaman p 
                         JOIN buku b ON p.id_buku = b.id
                         WHERE p.id_pengembalian = '$returning_id' 
                         AND p.nim = '$nim'
                         AND p.status_konfirmasi = 'sudah'";
        
        $validasiResult = $koneksi->query($validasiQuery);
        
        if ($validasiResult->num_rows > 0) {
            $dataPeminjaman = $validasiResult->fetch_assoc();
            $judul_buku = $dataPeminjaman['judul'];
            $id_peminjaman = $dataPeminjaman['id']; // Get the peminjaman ID
            
            // Cek apakah sudah pernah melaporkan ID ini sebelumnya
            $cekLaporan = $koneksi->query("SELECT id FROM pengembalian WHERE id = '$returning_id' AND deskripsi_kondisi IS NOT NULL");
            
            if ($cekLaporan->num_rows > 0) {
                $pesan = "ID Pengembalian ini sudah pernah dilaporkan sebelumnya!";
                $status = "error";
            } else {
                // Format deskripsi dengan jenis masalah
                $deskripsi_lengkap = $description;
                
                // Insert/Update laporan ke tabel pengembalian
                // Include id_peminjaman and tanggal_pengembalian in the INSERT statement
                $tanggal_sekarang = date('Y-m-d H:i:s');
                $insertLaporan = $koneksi->query("INSERT INTO pengembalian 
                    (id, id_peminjaman, tanggal_pengembalian, deskripsi_kondisi) VALUES ('$returning_id', '$id_peminjaman', '$tanggal_sekarang', '$deskripsi_lengkap')
                    ON DUPLICATE KEY UPDATE deskripsi_kondisi = '$deskripsi_lengkap', tanggal_pengembalian = '$tanggal_sekarang'");
                
                if ($insertLaporan) {
                    // Berhasil
                    $pesan = "Laporan berhasil dikirim!";
                    $status = "success";
                    $id_pengembalian = $returning_id;
                    
                    // TODO: Kirim email notifikasi ke admin atau user jika perlu
                    // kirimEmailLaporan($email_mahasiswa, $nama_mahasiswa, $judul_buku, $jenis_masalah, $description);
                    
                } else {
                    $pesan = "Gagal mengirim laporan: " . $koneksi->error;
                    $status = "error";
                }
            }
        } else {
            $pesan = "ID Pengembalian tidak valid atau tidak ditemukan!";
            $status = "error";
        }
    }
}

// Jika tidak ada data POST, redirect kembali
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: report book.php");
    exit();
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Lapor Buku</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/reportbook2.css" />
    
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
    <main class="notification-container">
      <section class="notification-card">
        
        <?php if ($status === 'success'): ?>
          <span class="success-icon">✅</span>
          <h1 class="notification-title">Laporan Berhasil Dikirim!</h1>
          
          <div class="alert success">
            <?php echo htmlspecialchars($pesan); ?>
          </div>
          
          <?php if (!empty($judul_buku)): ?>
          <div class="book-info">
            <h4>📚 Detail Buku:</h4>
            <p><strong>Judul:</strong> <?php echo htmlspecialchars($judul_buku); ?></p>
            <p><strong>ID Pengembalian:</strong> <?php echo htmlspecialchars($id_pengembalian); ?></p>
          </div>
          <?php endif; ?>
          
          <p class="notification-message">
            Laporan Anda telah tercatat dalam sistem. Segera hubungi pustakawan untuk mengembalikan buku dan menindaklanjuti laporan ini.
          </p>
          
        <?php else: ?>
          <span class="error-icon">❌</span>
          <h1 class="notification-title">Laporan Gagal Dikirim</h1>
          
          <div class="alert error">
            <?php echo htmlspecialchars($pesan); ?>
          </div>
          
          <p class="notification-message">
            Terjadi kesalahan saat mengirim laporan. Silakan coba lagi atau hubungi administrator jika masalah berlanjut.
          </p>
        <?php endif; ?>
        
        <div class="action-buttons">
          <?php if ($status === 'success'): ?>
            <a href="user start page.php" class="btn btn-primary">Kembali ke Dashboard</a>
          <?php else: ?>
            <a href="report book.php" class="btn btn-primary">Coba Lagi</a>
            <a href="user start page.php" class="btn btn-secondary">Dashboard</a>
          <?php endif; ?>
        </div>
      </section>
    </main>
    <script src="../assets//js/hamburger.js"></script>
    
    <script>
      // Auto redirect setelah 10 detik jika berhasil
      <?php if ($status === 'success'): ?>
      let countdown = 10;
      const countdownElement = document.createElement('p');
      countdownElement.style.cssText = 'color: #6c757d; font-size: 14px; margin-top: 15px;';
      document.querySelector('.notification-card').appendChild(countdownElement);
      
      const updateCountdown = () => {
        countdownElement.textContent = `Akan otomatis diarahkan ke beranda dalam ${countdown} detik...`;
        countdown--;
        
        if (countdown < 0) {
          window.location.href = 'user start page.php';
        }
      };
      
      updateCountdown();
      setInterval(updateCountdown, 1000);
      <?php endif; ?>
    </script>
  </body>
</html>