<?php
require '../../session/sessionAdmin.php';
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Daftar Pengguna</title>
    <link rel="stylesheet" href="../assets/css/hamburger.css"/>
    <link rel="stylesheet" href="../assets/css/daftarUsers.css" />
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
    <main class="user-list-page">
      <section class="user-list-container">
        <div class="user-list-content">
          <div class="user-list-header">
            <h1 class="page-title">Daftar Pengguna</h1>
            <input
              type="text"
              id="searchInput"
              class="search-input"
              placeholder="Cari pengguna"
            />
        </div>

          <div class="user-table-container">
            <div class="user-table" id="userTable">
              <div class="table-header">
                <div class="table-cell">No</div>
                <div class="table-cell">NIM</div>
                <div class="table-cell">Nama</div>
                <div class="table-cell">Jenis Kelamin</div>
                <div class="table-cell">Aksi</div>
              </div>

              <div class="table-row">
                <div class="table-cell">1</div>
                <div class="table-cell"><a href="akunUser.php">236661003</a></div>
                <div class="table-cell"><a href="akunUser.php">Rhaka Aditya Reswara</a></div>
                <div class="table-cell">Laki - laki</div>
                <div class="table-cell action-cell"><a href="hapusUser.php">HAPUS</a></div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>

              <div class="table-row">
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
                <div class="table-cell">data</div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
    <script src="../assets/js/daftarUsers.js"></script>
  </body>
</html>
