<?php

$host = "sgp.domcloud.co";
$dbname = "radja_proyek_perpustakaan_db";
$username = "radja-proyek-perpustakaan";  // Sesuaikan dengan username database
$password = "r3u2b)w)XAi_P47AW1";  // Sesuaikan dengan password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
