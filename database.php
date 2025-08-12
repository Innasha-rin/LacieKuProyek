<?php
$host = "localhost";
$dbname = "ollieproject_db";
$username = "ollieproject";  // Sesuaikan dengan username database
$password = "_Sjdv83_91Q2QcOuC(";  // Sesuaikan dengan password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
