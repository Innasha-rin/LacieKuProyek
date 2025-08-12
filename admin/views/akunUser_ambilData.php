<?php
require 'database.php'; // pastikan path benar
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    die("ID pengguna tidak ditemukan.");
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM mahasiswa WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Pengguna tidak ditemukan.");
}

// ambil nim dari data user
$nim = $user['nim'] ?? null;

if (!$nim) {
    die("NIM tidak ditemukan.");
}

// Ambil buku yang sedang dipinjam oleh NIM ini
$stmt = $conn->prepare("
    SELECT b.judul 
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.nim = ? AND p.status_pengembalian = 'dipinjam'
    LIMIT 2
");
$stmt->execute([$nim]);
$bukuDipinjam = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Ambil total denda dari tabel denda
$stmt = $conn->prepare("SELECT SUM(jumlah) FROM denda WHERE id = ?");
$stmt->execute([$id]);
$totalDenda = $stmt->fetchColumn() ?? 0;
?>
