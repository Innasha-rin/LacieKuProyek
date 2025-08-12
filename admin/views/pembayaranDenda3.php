<?php
require '../../session/sessionAdmin.php';
include 'database.php';
date_default_timezone_set('Asia/Makassar');

$verification_success = false;
$verification_time = null;
$error_message = '';
$file_deleted = false;
$file_delete_error = '';

if (isset($_GET['id_peminjaman'])) {
  $id = $_GET['id_peminjaman'];
  $tanggal_verifikasi = date('Y-m-d H:i:s');

  // Ambil data bukti pembayaran sebelum update
  try {
    $stmt_file = $conn->prepare("SELECT bukti_pembayaran FROM denda WHERE id_peminjaman = :id_peminjaman");
    $stmt_file->execute(['id_peminjaman' => $id]);
    $file_data = $stmt_file->fetch(PDO::FETCH_ASSOC);
    
    if ($file_data && $file_data['bukti_pembayaran']) {
      $filename = $file_data['bukti_pembayaran'];
      $file_path = "../../user/receipt/" . $filename;
      
      // Hapus file bukti pembayaran jika ada
      if (file_exists($file_path)) {
        if (unlink($file_path)) {
          $file_deleted = true;
        } else {
          $file_delete_error = 'Gagal menghapus file bukti pembayaran dari server';
        }
      } else {
        $file_delete_error = 'File bukti pembayaran tidak ditemukan di server';
      }
    }
  } catch (PDOException $e) {
    $file_delete_error = 'Error saat mengambil data file: ' . $e->getMessage();
  }

  // Update tabel denda dengan prepared statement untuk keamanan
  try {
    $stmt = $conn->prepare("UPDATE denda 
        SET 
            status_verifikasi = 'valid', 
            status_pembayaran = 'diterima',
            tanggal_verifikasi = :tanggal_verifikasi
        WHERE id_peminjaman = :id_peminjaman");
    
    $stmt->execute([
        'tanggal_verifikasi' => $tanggal_verifikasi,
        'id_peminjaman' => $id
    ]);

    // Update tabel pengembalian
    $stmt2 = $conn->prepare("UPDATE pengembalian 
        SET status_denda = 'lunas' 
        WHERE id_peminjaman = :id_peminjaman");
    
    $stmt2->execute(['id_peminjaman' => $id]);
    
    $verification_success = true;
    $verification_time = $tanggal_verifikasi;
    
  } catch (PDOException $e) {
    $verification_success = false;
    $error_message = $e->getMessage();
  }
}

// Ambil data denda untuk ditampilkan
$denda_info = null;
if (isset($_GET['id_peminjaman'])) {
    try {
        $stmt = $conn->prepare("SELECT d.*, m.nama, p.judul_buku 
            FROM denda d 
            JOIN mahasiswa m ON d.nim = m.nim 
            LEFT JOIN peminjaman pm ON d.id_peminjaman = pm.id
            LEFT JOIN buku p ON pm.id_buku = p.id
            WHERE d.id_peminjaman = :id_peminjaman");
        
        $stmt->execute(['id_peminjaman' => $_GET['id_peminjaman']]);
        $denda_info = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error
    }
}
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Konfirmasi Pembayaran Denda</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/pembayaranDenda3.css">
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
    
    <main class="payment-container">
      <section class="payment-card">
        <h1 class="payment-title">Pembayaran Denda</h1>
        
        <?php if (isset($verification_success) && $verification_success): ?>
            <div class="success-container">
                <h3>✅ Verifikasi Berhasil!</h3>
                <p>Pembayaran denda telah diverifikasi dan diterima.</p>
                <p class="timestamp"><strong>Waktu Verifikasi:</strong> <?= date('d/m/Y H:i:s', strtotime($verification_time)) ?></p>
            </div>
            
            <!-- Status Penghapusan File -->
            <div class="info-section">
                <h4>📁 Status Penghapusan File Bukti Pembayaran:</h4>
                <?php if ($file_deleted): ?>
                    <div class="file-status file-success">
                        ✅ File bukti pembayaran berhasil dihapus dari server
                    </div>
                <?php elseif ($file_delete_error): ?>
                    <div class="file-status file-error">
                        ⚠️ <?= htmlspecialchars($file_delete_error) ?>
                    </div>
                <?php else: ?>
                    <div class="file-status">
                        ℹ️ Tidak ada file yang perlu dihapus
                    </div>
                <?php endif; ?>
                
                <p><small><strong>Catatan:</strong> Data bukti pembayaran di database tetap tersimpan untuk keperluan audit dan riwayat.</small></p>
            </div>
            
        <?php elseif (isset($verification_success) && !$verification_success): ?>
            <div class="error-container">
                <h3>❌ Verifikasi Gagal!</h3>
                <p>Terjadi kesalahan saat memverifikasi pembayaran: <?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($denda_info): ?>
            <div class="info-container">
                <h3>Informasi Denda:</h3>
                <div class="info-row"><strong>Nama Mahasiswa:</strong> <?= htmlspecialchars($denda_info['nama']) ?></div>
                <div class="info-row"><strong>NIM:</strong> <?= htmlspecialchars($denda_info['nim']) ?></div>
                <div class="info-row"><strong>Jumlah Denda:</strong> Rp <?= number_format($denda_info['jumlah'], 0, ',', '.') ?></div>
                <div class="info-row"><strong>Nama File Bukti:</strong> <?= htmlspecialchars($denda_info['bukti_pembayaran']) ?></div>
                
                <?php if ($denda_info['tanggal_upload_bukti']): ?>
                    <div class="info-row timestamp">
                        <strong>Diupload pada:</strong> <?= date('d/m/Y H:i:s', strtotime($denda_info['tanggal_upload_bukti'])) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($denda_info['tanggal_verifikasi']): ?>
                    <div class="info-row timestamp">
                        <strong>Diverifikasi pada:</strong> <?= date('d/m/Y H:i:s', strtotime($denda_info['tanggal_verifikasi'])) ?>
                    </div>
                <?php endif; ?>
                
                <div class="info-row">
                    <strong>Status Verifikasi:</strong> 
                    <span style="color: <?= $denda_info['status_verifikasi'] == 'valid' ? 'green' : 'orange' ?>;">
                        <?= htmlspecialchars($denda_info['status_verifikasi']) ?>
                    </span>
                </div>
                
                <div class="info-row">
                    <strong>Status Pembayaran:</strong> 
                    <span style="color: <?= $denda_info['status_pembayaran'] == 'diterima' ? 'green' : 'orange' ?>;">
                        <?= htmlspecialchars($denda_info['status_pembayaran']) ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (!isset($verification_success) || !$verification_success): ?>
            <p class="payment-confirmation">Konfirmasi pembayaran denda?</p>
            <p><small><strong>Perhatian:</strong> Setelah dikonfirmasi, file bukti pembayaran akan dihapus dari server namun data tetap tersimpan di database.</small></p>
            <a href="pembayaranDenda3.php?id_peminjaman=<?= $_GET['id_peminjaman'] ?>">
                <button class="payment-button">Terima</button>
            </a>
        <?php else: ?>
            <a href="pembayaranDenda.php">
                <button class="payment-button">Kembali ke Daftar</button>
            </a>
        <?php endif; ?>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>