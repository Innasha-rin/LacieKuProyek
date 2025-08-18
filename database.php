<?php
$host = "sgp.domcloud.co";
$dbname = "ollie_project_db";
$username = "ollie-project";  // Sesuaikan dengan username database
$password = "4)_xZEF6+R7jhq9dU5";  // Sesuaikan dengan password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
