<?php
require '../database.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE token_verifikasi = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $update = $conn->prepare("UPDATE mahasiswa SET is_verified = 1, token_verifikasi = NULL WHERE token_verifikasi = ?");
        $update->execute([$token]);
        $message= "Akun berhasil diverifikasi! Anda bisa login sekarang.";
        // Redirect ke halaman login (opsional)
        // header("Location: login.php");
    } else {
        $message= "Token tidak valid atau akun sudah diverifikasi.";
    }
} else {
    $message= "Token tidak ditemukan.";
}
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Berhasil</title>
    <link rel="stylesheet" href="../assets/css/verification_success.css" />
  </head>
  <body>
    <div class="logo-wrapper">
      <img
        src="../assets/images/login_ollie_logo.png"
        class="logo-image"
        alt="Logo"
      />
    </div>
    <main class="verification-container">
      <h1 class="verification-heading">
        <?php if (!empty($message)): ?>
        <?= htmlspecialchars($message) ?>
        <?php endif; ?>
      </h1>
      <p class="verification-message">
        Tekan tombol di bawah untuk pergi ke halaman login
      </p>
      <a href="../login.php" class="login-button">Login</a>
    </main>
  </body>
</html>