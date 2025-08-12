<?php
require '../database.php'; // Pastikan file ini mengembalikan $conn sebagai PDO instance

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"] ?? '';
    $password = $_POST["password"] ?? '';
    $konfirmasi = $_POST["konfirmasi"] ?? '';

    // 1. Cek apakah token valid
    $query = $conn->prepare("SELECT * FROM password_reset WHERE token = ? LIMIT 1");
    $query->execute([$token]);
    $row = $query->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo "Token tidak valid atau sudah kadaluarsa.";
        exit;
    }

    $email = $row['email'];
    $role = $row['role']; // 'admin' atau 'user'

    // 2. Cek kecocokan password
    if ($password !== $konfirmasi) {
        echo "Password tidak sama!";
        exit;
    }

    // 3. Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 4. Update password berdasarkan role
    if ($role == 'admin') {
        $update = $conn->prepare("UPDATE admin_perpustakaan SET password = ? WHERE email = ?");
    } else {
        $update = $conn->prepare("UPDATE mahasiswa SET password = ? WHERE email = ?");
    }

    $success = $update->execute([$hashedPassword, $email]);

    if ($success) {
        // 5. Hapus token dari tabel
        $delete = $conn->prepare("DELETE FROM password_reset WHERE token = ?");
        $delete->execute([$token]);

        $message="Password berhasil diubah. Silakan login kembali.";
    } else {
        $message="Gagal mengubah password.";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Ganti Password</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/gantiPassword.css" />
  </head>
  <body>
    <main class="password-reset-container">
      <section class="password-reset-card">
        <h1 class="password-reset-title">Ganti Password</h1>
        <p class="password-reset-subtitle">
        <?php if (!empty($message)): ?>
        <p class="password-reset-subtitle"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
        </p>
        <a href="../login.php"><button type="submit" class="submit-button">Login</button></a>
      </section>
    </main>
  </body>
</html>

