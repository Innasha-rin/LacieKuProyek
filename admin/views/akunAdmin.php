<?php
require '../../session/sessionAdmin.php';
include 'akunAdmin_ambilData.php';
include 'database.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Akun Admin</title>
    <link rel="stylesheet" href="../assets/css/akunAdmin.css" />
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

    <main class="profile-container">
      <section class="profile-card">
        <div class="profile-content">
          <div class="profile-avatar">
            <img src="../assets/images/ProfilAdmin.png">
          </div>
          <h1 class="profile-name"><?= $admin['nama'] ?></h1>
          <span class="profile-badge">TERVERIFIKASI</span>

            <div class="form-item">
              <label for="nama" class="form-label">Nama</label>
              <input type="text" id="nama" class="form-input" value="<?= htmlspecialchars($admin['nama']) ?>" readonly />
            </div>

            <div class="form-item">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" class="form-input" value="<?= htmlspecialchars($admin['email']) ?>" readonly />
            </div>

            <div class="form-item">
              <label for="phone" class="form-label">Nomor HP</label>
              <input type="tel" id="phone" class="form-input" value="<?= htmlspecialchars($admin['no_hp']) ?>" readonly />
            </div>

            <div class="form-item">
              <label for="password" class="form-label">Password</label>
              <input type="password" id="password" class="form-input" value="<?= htmlspecialchars($admin['password']) ?>" readonly />
            </div>
            
            <a href="../../changePass/gantiPassword.php"><button type="button" class="logout-button">Ganti Password</button></a>
          </form>
        </div>
      </section>
    </main>
    <script src="../assets/js/hamburger.js"></script>
  </body>
</html>
