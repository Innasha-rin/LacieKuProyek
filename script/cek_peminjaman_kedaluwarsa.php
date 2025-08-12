<?php
require 'database.php'; // koneksi DB Anda
date_default_timezone_set('Asia/Makassar');

$sekarang = date('Y-m-d H:i:s');

// Ambil semua peminjaman yang belum diambil dan sudah lewat 24 jam
$sql = "SELECT id, id_buku, tanggal_peminjaman 
        FROM peminjaman 
        WHERE status_pengambilan = 'belum diambil' 
          AND TIMESTAMPDIFF(HOUR, tanggal_peminjaman, NOW()) >= 24";
$result = $koneksi->query($sql);

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $id_buku = $row['id_buku'];

    // Kembalikan stok buku
    $stmt = $koneksi->prepare("UPDATE buku SET stok = stok + 1 WHERE id = ?");
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();

    // Tandai status peminjaman sebagai kadaluwarsa atau dibatalkan
    $stmt = $koneksi->prepare("UPDATE peminjaman SET status = 'dibatalkan', status_pengambilan = 'expired' WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
?>
