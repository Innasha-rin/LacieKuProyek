<?php
include 'database.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Perhatikan bahwa di sini kita menggunakan 'id' sesuai nama kolom di database
    $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
    $stmt->execute([$id]);
    $buku = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($buku) {
        echo json_encode(["success" => true] + $buku);
    } else {
        echo json_encode(["success" => false, "message" => "Buku tidak ditemukan"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "ID tidak diberikan"]);
}
?>