<?php
require '../../session/sessionUser.php';
require_once 'useraccount_back.php'; // Hanya include useraccount_back.php, jangan include database.php lagi
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Akun Pengguna</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css"
    />
    <link rel="stylesheet" href="../assets/css/hamburger.css" />
    <link rel="stylesheet" href="../assets/css/user account.css" />
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
<main class="profile-container">
  <section class="profile-card">
    <div class="avatar-wrapper">
    <img class="profile-img" src="../assets/images/ProfilUser.png">
    </div>

    <h1 class="profile-name"><?= htmlspecialchars($user['nama']) ?></h1>

    <div class="verification-status">TERVERIFIKASI</div>

    <form class="profile-form">
      <div class="form-field">
        <label for="nim" class="field-label">NIM</label>
        <input type="text" id="nim" class="field-input" placeholder="NIM" value="<?= htmlspecialchars($user['nim']) ?>" readonly/>
      </div>

      <div class="form-field">
        <label for="nama" class="field-label">Nama</label>
        <input type="text" id="nama" class="field-input" placeholder="Nama" value="<?= htmlspecialchars($user['nama']) ?>" readonly/>
      </div>

      <div class="form-field">
        <label for="email" class="field-label">Email</label>
        <input
          type="email"
          id="email"
          class="field-input"
          placeholder="Email"
          value="<?= htmlspecialchars($user['email']) ?>"
          readonly
        />
      </div>

      <div class="form-field">
        <label for="gender" class="field-label">Jenis Kelamin</label>
        <input
          type="text"
          id="gender"
          class="field-input"
          placeholder="Jenis Kelamin"
          value="<?= htmlspecialchars($user['jenis_kelamin']) ?>"
          readonly
        />
      </div>

      <div class="form-field">
        <label for="birthdate" class="field-label">Tanggal Lahir</label>
        <input
          type="text"
          id="birthdate"
          class="field-input"
          placeholder="Tanggal Lahir"
          value="<?= htmlspecialchars($user['tanggal_lahir']) ?>"
          readonly
        />
      </div>

      <div class="form-field">
        <label for="phone" class="field-label">Nomor HP</label>
        <input
          type="tel"
          id="phone"
          class="field-input"
          placeholder="Nomor HP"
          value="<?= htmlspecialchars($user['no_hp']) ?>"
          readonly
        />
      </div>

      <div class="form-field">
        <label for="password" class="field-label">Password</label>
        <input
          type="password"
          id="password"
          class="field-input"
          placeholder="Password"
          value="*********************************"
          readonly
        />
      </div>
      <a href="../../changePass/gantiPassword.php" class="logout-button">Ganti Password</a>
    </form>
  </section>
</main>
<script src="../assets//js/hamburger.js"></script>
</body>
</html>