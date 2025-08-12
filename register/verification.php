<?php
require '../database.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $no_hp = $_POST['no_hp'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));

    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $message= "Email sudah terdaftar.";
        exit;
    }

    $insert = $conn->prepare("INSERT INTO mahasiswa 
        (nim, nama, email, password, jenis_kelamin, tanggal_lahir, no_hp, token_verifikasi)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insert->execute([$nim, $nama, $email, $password, $jenis_kelamin, $tanggal_lahir, $no_hp, $token]);

    // Kirim email verifikasi
    $verifikasi_link = "http://localhost/tugas/proyek-perpustakaan/register/verification_success.php?token=$token";
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'notification.ollie@gmail.com'; // email kamu
        $mail->Password   = 'cunp fwrs ybot pkvy'; // app password Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('notification.ollie@gmail.com', 'OLLIE');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verifikasi Akun Mahasiswa';
        $mail->Body    = "Klik link berikut untuk verifikasi akun Anda:<br><a href='$verifikasi_link'>$verifikasi_link</a>";

        $mail->send();
        $message= "Registrasi berhasil! Silakan cek email untuk verifikasi.";
    } catch (Exception $e) {
        $message= "Email gagal dikirim: {$mail->ErrorInfo}";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrasi</title>
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/verification.css" />
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
      <section class="password-reset-card">
        <h1 class="verification-heading">Verifikasi email</h1>
        <p class="verification-message">
        <?php if (!empty($message)): ?>
        <p class="verification-message"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
        </p>
      </section>
    </main>
  </body>
</html>

