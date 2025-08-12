<?php
// Ambil token dari URL
$token = $_GET['token'] ?? '';
if (!$token) {
    echo "Token tidak valid.";
    exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/gantiPassword2.css" />
  </head>
  <body>
    <main class="password-page">
      <section class="password-card">
        <h1 class="password-title">Ganti Password</h1>
        <p class="password-subtitle">Masukkan password baru anda</p>
        <form class="password-form" action="gantiPassword_proses.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
          <input
            type="password"
            name="password"
            placeholder="password baru"
            class="password-input"
            aria-label="New password"
            required
          />
          <input
            type="password"
            name="konfirmasi"
            placeholder="konfirmasi password baru"
            class="password-input"
            aria-label="Confirm new password"
            required
          />
          <button type="submit" class="password-submit">Kirim</button>
        </form>
      </section>
    </main>
  </body>
</html>
