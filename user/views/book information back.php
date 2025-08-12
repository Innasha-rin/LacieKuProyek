<?php
include 'database.php'; // Pastikan file ini menghubungkan ke database

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM buku WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $buku = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($buku) {
            echo json_encode([
                "success" => true,
                "stok" => $buku["stok"],
                "cover" => $buku["cover"],
                "judul" => $buku["judul"],
                "sinopsis" => $buku["sinopsis"],
                "jumlah_halaman" => $buku["jumlah_halaman"],
                "penulis" => $buku["penulis"],
                "isbn" => $buku["isbn"],
                "tahun_terbit" => $buku["tahun_terbit"],
                "penerbit" => $buku["penerbit"] 
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Buku tidak ditemukan"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database error"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID buku tidak diberikan"]);
}
?>