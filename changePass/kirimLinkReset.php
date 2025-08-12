<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';
require '../database.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $role = $_POST["role"] ?? '';

    // Validasi email dan role
    if (empty($email) || empty($role)) {
        $message = "Mohon isi semua field.";
    } else {
        // Cek email di database
        if ($role == 'admin') {
            $stmt = $conn->prepare("SELECT * FROM admin_perpustakaan WHERE email = ?");
        } else {
            $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE email = ?");
        }

        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $message = "Email tidak ditemukan.";
        } else {
            // Buat token
            $token = bin2hex(random_bytes(32));

            // Simpan token
            $insert = $conn->prepare("INSERT INTO password_reset (email, token, role) VALUES (?, ?, ?)");
            $insert->execute([$email, $token, $role]);

            // Link reset
            $link = "http://localhost/tugas/proyek-perpustakaan/changePass/gantiPassword2.php?token=$token";

            // Kirim email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'notification.ollie@gmail.com';
                $mail->Password   = 'cunp fwrs ybot pkvy';
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;
                // $mail->SMTPDebug  = 2; // Debugging opsional

                $mail->setFrom('notification.ollie@gmail.com', 'OLLIE');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Link Reset Password';
                $mail->Body    = "Klik link berikut untuk reset password Anda: <br><a href='$link'>$link</a>";

                $mail->send();
                $message = "Link reset password telah dikirim ke email Anda (cek folder spam juga).";
            } catch (Exception $e) {
                $message = "Email gagal dikirim: {$mail->ErrorInfo}";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
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
      </section>
    </main>
  </body>
</html>

