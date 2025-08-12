<?php
$host = "localhost";
$dbname = "ollie1";
$username = "root";  // Sesuaikan dengan username database
$password = "";  // Sesuaikan dengan password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$koneksi = mysqli_connect($host, $username, $password, $dbname);
if (!$koneksi) {
    die("Koneksi gagal" . mysqli_connect_error());
}
?>
