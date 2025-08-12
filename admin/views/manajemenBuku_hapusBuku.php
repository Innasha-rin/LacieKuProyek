<?php
header("Content-Type: application/json");
include 'database.php'; // Sesuaikan path jika perlu

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["id"])) {
        $id = intval($_POST["id"]); // Pastikan ID adalah angka

        try {
            // Ambil nama file gambar sebelum menghapus buku
            $stmt = $conn->prepare("SELECT cover FROM buku WHERE id = :id");
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $coverPath = "../../CoverBook, OLLIE/" . $result["cover"]; // Sesuaikan path folder gambar

                // Hapus buku dari database
                $stmt = $conn->prepare("DELETE FROM buku WHERE id = :id");
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    // Hapus file gambar jika ada
                    if (file_exists($coverPath)) {
                        unlink($coverPath); // Hapus file gambar
                    }

                    echo json_encode(["success" => true]);
                } else {
                    echo json_encode(["success" => false, "message" => "Gagal menghapus buku dari database."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Buku tidak ditemukan."]);
            }
        } catch (PDOException $e) {
            echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "ID buku tidak ditemukan."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode request tidak valid."]);
}

$conn = null;
?>
