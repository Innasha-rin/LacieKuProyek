<?php
session_start();
require 'database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Cek di tabel admin
    $stmt = $conn->prepare("SELECT * FROM admin_perpustakaan WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['role'] = 'admin';
        $_SESSION['namaAdmin'] = $admin['nama'];
        $_SESSION['last_activity'] = time();
        header("Location: admin/views/dashboard.php");
        exit();
    }

    // Cek di tabel user
    $stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE email = :email AND is_verified = 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nim']= $user['nim'];
        $_SESSION['role'] = 'user';
        $_SESSION['namaUser'] = $user['nama'];
        $_SESSION['last_activity'] = time();
        header("Location: user/views/user start page.php");
        exit();
    }

    $message = "Login gagal! Email atau password salah.";
}
?>


