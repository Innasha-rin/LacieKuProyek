<?php
require '../../session/sessionUser.php';
require_once 'notificationPage_class.php';

$notification = new Notification();
$nim = $_SESSION['nim'];

// Get notification ID from URL parameter
$notificationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($notificationId === 0) {
    // Redirect back to notification list if no ID provided
    header('Location: notificationPage.php');
    exit;
}

// Get notification details
$notificationDetail = $notification->getNotificationById($notificationId, $nim);

if (!$notificationDetail) {
    // Redirect back if notification not found
    header('Location: notificationPage.php');
    exit;
}

// Mark notification as read
$notification->markAsRead($notificationId, $nim);

// Format the message for display
$formattedMessage = nl2br(htmlspecialchars($notificationDetail['pesan']));

// Extract title from message (first line) or use default
$messageLines = explode("\n", $notificationDetail['pesan']);
$notificationTitle = !empty($messageLines[0]) ? trim($messageLines[0]) : 'Notifikasi Perpustakaan';

// Format date and time
$formattedDate = $notification->formatDateIndonesian($notificationDetail['tanggal_kirim']);
$formattedTime = $notification->formatTime($notificationDetail['tanggal_kirim']);
$fullDateTime = $formattedTime . ', ' . $formattedDate;

// Get unread count for badge
$unreadCount = $notification->getUnreadCount($nim);
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($notificationTitle); ?></title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/notificationPage2.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
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
    
    <main class="notification-page">
      <section class="notification-container">
        <div class="notification-header">
            <span>Detail Notifikasi</span>
        </div>
        
        <article class="notification-card">
            <div class="notification-content">
              <h2 class="notification-title"><?php echo htmlspecialchars($notificationTitle); ?></h2>
              <div class="notification-status">
                  <span class="status-badge read">Dibaca</span>
              </div>
              <div class="notification-message">
                  <?php echo $formattedMessage; ?>
              </div>
              <time class="notification-timestamp"><?php echo $fullDateTime; ?></time>
            </div>
        </article>
        
        <div class="notification-actions">
            <button onclick="goBack()" class="btn-primary">Kembali ke Daftar Notifikasi</button>
        </div>
      </section>
    </main>
    
    <script src="../assets/js/hamburger.js"></script>
    <script>
        function goBack() {
            window.location.href = 'notificationPage.php';
        }
        
        // Optional: Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                goBack();
            }
        });
        
        // Mark this notification as read (if needed for real-time updates)
        document.addEventListener('DOMContentLoaded', function() {
            // This ensures the notification is marked as read
            markAsRead(<?php echo $notificationId; ?>);
        });
        
        async function markAsRead(notificationId) {
            try {
                const response = await fetch('notificationPage_back.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'mark_read',
                        id: notificationId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    console.log('Notification marked as read');
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }
    </script>