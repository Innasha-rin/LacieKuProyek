<?php
// Pastikan database.php hanya di-include sekali
require_once 'database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan session sudah dimulai dan user_id ada
if (!isset($_SESSION['user_id'])) {
    die("Session tidak valid. Silakan login kembali.");
}

$user_id = $_SESSION['user_id'];

try {
    $sql = "SELECT nim, nama, email, jenis_kelamin, tanggal_lahir, no_hp, password FROM mahasiswa WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>