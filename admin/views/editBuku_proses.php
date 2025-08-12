<?php
include 'database.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(["success" => false, "message" => "ID buku tidak valid."]);
            exit;
        }

        // Debug untuk melihat data POST dan FILES
        error_log("POST data: " . print_r($_POST, true));
        error_log("FILES data: " . print_r($_FILES, true));

        // Ambil cover lama sebelum update
        $stmt = $conn->prepare("SELECT cover FROM buku WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $oldCoverData = $stmt->fetch(PDO::FETCH_ASSOC);
        $oldCover = $oldCoverData ? ($oldCoverData['cover'] ?? null) : null;

        error_log("Old cover: " . ($oldCover ?? 'none'));

        // Siapkan query update
        $updates = [];
        $params = [];

        // Field yang bisa diperbarui
        $fields = ['judul', 'penulis', 'penerbit', 'tahun_terbit', 'jumlah_halaman', 'kategori', 'isbn', 'sinopsis', 'stok'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $_POST[$field];
            }
        }

        // Proses upload cover baru jika ada
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK && $_FILES['cover']['size'] > 0) {
            $uploadDir = '../../CoverBook, OLLIE/';
            
            // Validasi file yang diupload
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            $fileType = $_FILES['cover']['type'];
            
            if (!in_array($fileType, $allowedTypes)) {
                echo json_encode(["success" => false, "message" => "Format file tidak didukung. Gunakan JPG, PNG, atau GIF."]);
                exit;
            }
            
            // Generate nama file unik untuk menghindari konflik
            $fileName = basename($_FILES['cover']['name']);
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'cover_' . time() . '_' . $id . '.' . $fileExt;
            
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $targetPath)) {
                $updates[] = "cover = :cover";
                $params[':cover'] = $newFileName;
                
                // Hapus cover lama jika ada dan berbeda dari cover default
                if ($oldCover && $oldCover !== "default.jpg" && file_exists($uploadDir . $oldCover)) {
                    if (unlink($uploadDir . $oldCover)) {
                        error_log("Old cover deleted: $oldCover");
                    } else {
                        error_log("Failed to delete old cover: $oldCover");
                    }
                }
            } else {
                error_log("Failed to move uploaded file to $targetPath");
                echo json_encode(["success" => false, "message" => "Gagal mengupload file cover."]);
                exit;
            }
        }

        if (empty($updates)) {
            echo json_encode(["success" => false, "message" => "Tidak ada perubahan yang dilakukan."]);
            exit;
        }

        // Buat query update
        $sql = "UPDATE buku SET " . implode(", ", $updates) . " WHERE id = :id";
        $params[':id'] = $id;

        error_log("SQL Query: $sql");
        error_log("Params: " . print_r($params, true));

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            echo json_encode(["success" => true, "message" => "Buku berhasil diperbarui"]);
        } else {
            error_log("SQL Error: " . print_r($stmt->errorInfo(), true));
            echo json_encode(["success" => false, "message" => "Gagal memperbarui buku: " . implode(", ", $stmt->errorInfo())]);
        }
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan"]);
}
?>