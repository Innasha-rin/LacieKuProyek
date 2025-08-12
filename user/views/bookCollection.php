<?php
require '../../session/sessionUser.php';
include 'database.php';
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Koleksi Buku</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/bookCollection.css" />
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
    <body>
    <main class="page-container">
      <section class="search-section">
        <div class="search-container">
          <div class="search-bar">
            <img src="../assets/images/magnifier.png">
            <input
              type="text"
              id="searchInput"
              placeholder="Cari judul buku..."
              aria-label="Search for book titles"
              class="search-input"
            />
          </div>
        </div>
      </section>

      <section class="table-section">
        <div class="table-container">
          <div class="table-wrapper" id="tableWrapper">
            <div class="table-header">
              <div class="header-cell number-column">No</div>
              <div class="header-cell title-column">Judul</div>
              <div class="header-cell author-column">Penulis</div>
              <div class="header-cell publisher-column">Penerbit</div>
              <div class="header-cell action-column">Aksi</div>
            </div>

            <div class="table-body" id="tableBody">
              <?php
              $no = 1;
              $query = "SELECT * FROM buku";
              $result = mysqli_query($koneksi, $query);
              while ($row = mysqli_fetch_assoc($result)){
                ?>
              <div class="table-row">
                <div class="table-cell number-column"><?php echo $no++; ?></div>
                <div class="table-cell title-column"><a href="book information.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['judul']); ?></a></div>
                <div class="table-cell author-column"><?php echo htmlspecialchars($row['penulis']); ?></div>
                <div class="table-cell publisher-column"><?php echo htmlspecialchars($row['penerbit']); ?></div>
                <div class="table-cell action-column"><a href="peminjamanbuku.php?id=<?php echo $row['id']; ?>">PINJAM</a></div>
              </div>
              <?php
              }
              ?>            
            </div>
          </div>
        </div>
      </section>
    </main>
    <script src="../assets//js/hamburger.js"></script>
    <script src="../assets/js/bookCollection.js"></script>
  </body>
</html>
