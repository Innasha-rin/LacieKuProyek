<?php
session_start();

$timeout = 10000000000; // 10 menit
$current_time = time();
$nama = $_SESSION['namaUser'];

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Cek timeout
if (isset($_SESSION['last_activity'])) {
    if ($current_time - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        header("Location: ../../login.php?timeout=1");
        exit;
    }
}

$_SESSION['last_activity'] = $current_time;
