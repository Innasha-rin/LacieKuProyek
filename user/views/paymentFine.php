<?php
require '../../session/sessionUser.php';
include 'database.php';

try {
  $nim = $_SESSION['nim'];
  
  // Query untuk mendapatkan total denda yang belum bayar
  $stmt = $conn->prepare("SELECT SUM(jumlah) AS total_denda FROM denda WHERE nim = :nim AND status = 'belum bayar'");
  $stmt->execute(['nim' => $nim]);
  $denda = $stmt->fetch(PDO::FETCH_ASSOC);
  $jumlah_denda = $denda['total_denda'] ?? 0;
  
  // Query untuk mendapatkan status pembayaran yang sudah diupload
  $stmt_status = $conn->prepare("SELECT 
    bukti_pembayaran, 
    tanggal_upload_bukti, 
    status_verifikasi, 
    tanggal_verifikasi,
    status_pembayaran 
    FROM denda 
    WHERE nim = :nim AND bukti_pembayaran IS NOT NULL 
    ORDER BY tanggal_upload_bukti DESC 
    LIMIT 1");
  $stmt_status->execute(['nim' => $nim]);
  $status_pembayaran = $stmt_status->fetch(PDO::FETCH_ASSOC);
  
} catch (PDOException $e) {
    $jumlah_denda = 0;
    $status_pembayaran = null;
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Pembayaran denda</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
  href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
  rel="stylesheet"
/>
<link rel="stylesheet" href="../assets/css/hamburger.css" />
<link rel="stylesheet" href="../assets/css/paymentFine.css" />
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
<div class="spacer"></div>
<main class="payment-container">
  <section class="payment-card">
    <h1 class="payment-title">Bayar Denda</h1>
    
    <?php
    // Tampilkan pesan feedback
    if (isset($_SESSION['message'])) {
        $message_class = $_SESSION['message_type'] == 'success' ? 'status-success' : 'status-error';
        echo '<div class="status-container ' . $message_class . '">';
        echo '<p>' . $_SESSION['message'] . '</p>';
        echo '</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    
    <p class="fine-description">Anda memiliki denda sebesar:</p>
    <p class="fine-amount">Rp <?= number_format($jumlah_denda, 0, ',', '.') ?></p>
    
    <?php if ($status_pembayaran): ?>
        <div class="status-container status-info">
            <h3>Status Pembayaran Terakhir:</h3>
            <p><strong>File:</strong> <?= htmlspecialchars($status_pembayaran['bukti_pembayaran']) ?></p>
            <p class="timestamp"><strong>Diupload:</strong> <?= date('d/m/Y H:i:s', strtotime($status_pembayaran['tanggal_upload_bukti'])) ?></p>
            
            <?php if ($status_pembayaran['status_verifikasi'] == 'belum diverifikasi'): ?>
                <p><strong>Status:</strong> <span style="color: #ffc107;">Menunggu Verifikasi Admin</span></p>
            <?php elseif ($status_pembayaran['status_verifikasi'] == 'valid'): ?>
                <p><strong>Status:</strong> <span style="color: #28a745;">Pembayaran Diterima</span></p>
                <?php if ($status_pembayaran['tanggal_verifikasi']): ?>
                    <p class="timestamp"><strong>Diverifikasi:</strong> <?= date('d/m/Y H:i:s', strtotime($status_pembayaran['tanggal_verifikasi'])) ?></p>
                <?php endif; ?>
            <?php elseif ($status_pembayaran['status_verifikasi'] == 'tidak valid'): ?>
                <p><strong>Status:</strong> <span style="color: #dc3545;">Pembayaran Ditolak</span></p>
                <?php if ($status_pembayaran['tanggal_verifikasi']): ?>
                    <p class="timestamp"><strong>Diverifikasi:</strong> <?= date('d/m/Y H:i:s', strtotime($status_pembayaran['tanggal_verifikasi'])) ?></p>
                    <br>
                <?php endif; ?>
                <p><em>Silakan upload ulang bukti pembayaran yang valid.</em></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($jumlah_denda > 0): ?>
    <h2 class="qris-title">QR CODE PERPUSTAKAAN</h2>
    <a href="https://checkout-staging.xendit.co/od/ollie-fine-payment" target="_blank" rel="noopener noreferrer">
    <img
    src="../assets/images/ollie_fine_payment.png"
    alt="qr Code"
    class="qris-code"
    />
    </a>
    <form action="upload_bukti.php" method="POST" enctype="multipart/form-data">
      <div class="payment-actions">
        <label for="file-upload" class="file-upload-label">
          <input type="file" name="bukti" id="file-upload" class="file-input" hidden required />
          <span class="upload-text">Upload file</span>
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg" class="upload-icon">
            <path d="M14.435 0L8 6.5H12.333V17.3333H16.667V6.5H21L14.435 0Z"
                  fill="black" fill-opacity="0.5"/>
          </svg>
        </label>
        <button type="submit" class="payment-submit">Kirim Bukti Pembayaran</button>
      </div>
    </form>
    <?php else: ?>
        <div class="status-container status-success">
            <p>Tidak ada denda yang perlu dibayar.</p>
        </div>
    <?php endif; ?>
  </section>
</main>
<script src="../assets//js/hamburger.js"></script>
</body>
</html>