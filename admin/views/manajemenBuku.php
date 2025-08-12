<?php
require '../../session/sessionAdmin.php';
include 'database.php';
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manajemen Buku</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/manajemenBuku.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
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

    <main class="book-management">
      <section class="container">
        <button class="add-book-btn"><a href="tambahBukuBaru.php">Tambah Buku</a></button>

        <section class="content-wrapper">
          <div class="search-section">
            <input
              type="text"
              class="search-input"
              placeholder="Cari judul buku..."
              id="searchInput"
            />
          </div>

        <div class="table-container">
            <div class="table-header">
              <div class="table-cell">No</div>
              <div class="table-cell">Judul</div>
              <div class="table-cell">Penulis</div>
              <div class="table-cell">Penerbit</div>
              <div class="table-cell">Aksi</div>
            </div>

            <div class="table-body" id="tableBody">
              <?php
              $no = 1;
              $query = "SELECT * FROM buku";
              $result = mysqli_query($koneksi, $query);
              while ($row = mysqli_fetch_assoc($result)){
                ?>
              <div class="table-row">
                <div class="table-cell"><?php echo $no++; ?></div>
                <div class="table-cell"><a href="buku.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['judul']); ?></a></div>
                <div class="table-cell"><?php echo htmlspecialchars($row['penulis']); ?></div>
                <div class="table-cell"><?php echo htmlspecialchars($row['penerbit']); ?></div>
                <div class="action-cell">
                    <span class="action-link"><a href="editBuku.php?id=<?php echo $row['id']; ?>">EDIT</a></span>
                    <span class="separator">|</span>
                    <span class="action-link" onclick="deleteBook(<?php echo $row['id']; ?>)">HAPUS</span>
                </div>
              </div>
              <?php
              }
              ?>            
            </div>
        </div>
        </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/manajemenBuku.js"></script>
    <script src="../assets/js/manajemenBukuEdit_ajax.js"></script>
    <script src="../assets/js/manajemenBukuHapus_ajax.js"></script>
</body>
</html>