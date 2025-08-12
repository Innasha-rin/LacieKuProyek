<?php
session_start();

$timeout = 10000000000; // 10 menit
$current_time = time();
$nama = $_SESSION['namaAdmin'];

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
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
