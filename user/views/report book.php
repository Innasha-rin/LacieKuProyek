<?php
require '../../session/sessionUser.php';
include 'database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ambil NIM dari session
$nim = $_SESSION['nim'] ?? '';

// Ambil daftar peminjaman yang bisa dilaporkan (belum dikembalikan)
$queryPeminjaman = "SELECT p.id_pengembalian, b.judul, p.tanggal_peminjaman, p.tanggal_jatuh_tempo
                   FROM peminjaman p 
                   JOIN buku b ON p.id_buku = b.id
                   WHERE p.nim = '$nim' 
                   AND p.status_pengembalian = 'dipinjam' 
                   AND p.status_konfirmasi = 'sudah'
                   ORDER BY p.tanggal_peminjaman DESC";
$resultPeminjaman = $koneksi->query($queryPeminjaman);
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lapor Buku</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/report book.css" />
    
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
    
    <main class="page-container">

      <section class="report-box">
        <h1 class="report-title">Lapor Buku</h1>
        <p class="report-subtitle">Laporkan buku yang rusak/hilang</p>

        <div class="info-box">
          <h4>ℹ️ Informasi Penting</h4>
          <p>Pilih ID Pengembalian dari buku yang ingin dilaporkan. Laporan ini akan diproses saat Anda mengembalikan buku ke perpustakaan.</p>
        </div>

        <form class="report-form" action="reportbook2.php" method="POST">
          <div class="form-group">
            <select name="returning_id" id="returning-id" class="form-select" required>
              <option value="">Pilih ID Pengembalian</option>
              <?php if ($resultPeminjaman->num_rows > 0): ?>
                <?php while ($row = $resultPeminjaman->fetch_assoc()): ?>
                  <option value="<?php echo htmlspecialchars($row['id_pengembalian']); ?>" 
                          data-judul="<?php echo htmlspecialchars($row['judul']); ?>"
                          data-tanggal="<?php echo htmlspecialchars($row['tanggal_peminjaman']); ?>"
                          data-tempo="<?php echo htmlspecialchars($row['tanggal_jatuh_tempo']); ?>">
                    <?php echo htmlspecialchars($row['id_pengembalian']); ?> - <?php echo htmlspecialchars($row['judul']); ?>
                  </option>
                <?php endwhile; ?>
              <?php else: ?>
                <option value="">Tidak ada buku yang dapat dilaporkan</option>
              <?php endif; ?>
            </select>
            <div id="book-info" class="book-info" style="display: none;"></div>
          </div>

          <div class="form-group">
            <textarea
              name="description"
              id="description"
              placeholder="Deskripsi masalah secara detail..."
              class="form-textarea"
              required
            ></textarea>
          </div>

          <?php if ($resultPeminjaman->num_rows > 0): ?>
          <button type="submit" class="report-button">Kirim Laporan</button>
          <?php else: ?>
          <button type="button" class="report-button" disabled style="opacity: 0.5; cursor: not-allowed;">
            Tidak ada buku untuk dilaporkan
          </button>
          <?php endif; ?>
        </form>
      </section>
    </main>
    
    <script src="../assets//js/hamburger.js"></script>
    <script>
      // Update info buku saat ID Pengembalian dipilih
      document.getElementById('returning-id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const infoDiv = document.getElementById('book-info');
        
        if (selectedOption.value) {
          const judul = selectedOption.getAttribute('data-judul');
          const tanggal = selectedOption.getAttribute('data-tanggal');
          const tempo = selectedOption.getAttribute('data-tempo');
          
          infoDiv.innerHTML = `
            <strong>📚 ${judul}</strong><br>
            📅 Dipinjam: ${tanggal}<br>
            ⏰ Jatuh Tempo: ${tempo}
          `;
          infoDiv.style.display = 'block';
        } else {
          infoDiv.style.display = 'none';
        }
      });
      
      
      // Validasi form sebelum submit
      document.querySelector('.report-form').addEventListener('submit', function(e) {
        const returningId = document.getElementById('returning-id').value;
        const description = document.getElementById('description').value;
        
        if (!returningId || !description.trim()) {
          e.preventDefault();
          alert('Mohon lengkapi semua field yang diperlukan!');
          return false;
        }
        
        if (description.trim().length < 10) {
          e.preventDefault();
          alert('Deskripsi harus minimal 10 karakter!');
          return false;
        }
        
        // Konfirmasi sebelum submit
        const confirmMessage = `Apakah Anda yakin ingin melapor untuk ID Pengembalian: ${returningId}?`;
        if (!confirm(confirmMessage)) {
          e.preventDefault();
          return false;
        }
      });
    </script>
  </body>
</html>