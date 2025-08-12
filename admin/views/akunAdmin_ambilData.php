<?php
include 'database.php';

$admin_id = $_SESSION['admin_id'];

// Ambil data admin dari DB
$sql = "SELECT nama, email, password, no_hp FROM admin_perpustakaan WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $admin_id, PDO::PARAM_INT);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle jika admin tidak ditemukan
if (!$admin) {
    $message= "Admin tidak ditemukan.";
    exit;
}
?>