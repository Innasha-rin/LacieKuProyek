<?php
include 'database.php'; // Pastikan ada koneksi ke database

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Ambil data dari form
        $judul = $_POST['judul'] ?? '';
        $penulis = $_POST['penulis'] ?? '';
        $penerbit = $_POST['penerbit'] ?? '';
        $tahun_terbit = $_POST['tahun_terbit'] ?? '';
        $jumlah_halaman = $_POST['jumlah_halaman'] ?? '';
        $kategori = $_POST['kategori'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $sinopsis = $_POST['sinopsis'] ?? '';
        $stok = $_POST['stok'] ?? 0;
        $cover = $_POST['cover'] ?? 0;

        // Proses upload cover buku
        $cover = "";
        if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../CoverBook, OLLIE/';
            $cover = time() . '_' . basename($_FILES['cover']['name']); // Tambahkan timestamp untuk menghindari duplikasi nama file
            $uploadFile = $uploadDir . $cover;
        
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $uploadFile)) {
                error_log("File berhasil diunggah: " . $uploadFile);
            } else {
                error_log("Gagal mengunggah file: " . $uploadFile);
            }
        }

        // Validasi data sederhana
        if (empty($judul) || empty($penulis) || empty($penerbit) || empty($jumlah_halaman) || empty($kategori) || empty($isbn) || empty($sinopsis)  || empty($tahun_terbit) || empty($isbn)) {
            echo json_encode(["success" => false, "message" => "Harap isi semua field wajib."]);
            exit;
        }

        // Insert ke database
        $stmt = $conn->prepare("INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, jumlah_halaman, kategori, isbn, sinopsis, cover, stok) 
                                VALUES (:judul, :penulis, :penerbit, :tahun_terbit, :jumlah_halaman, :kategori, :isbn, :sinopsis, :cover, :stok)");
        $stmt->bindParam(':judul', $judul);
        $stmt->bindParam(':penulis', $penulis);
        $stmt->bindParam(':penerbit', $penerbit);
        $stmt->bindParam(':tahun_terbit', $tahun_terbit);
        $stmt->bindParam(':jumlah_halaman', $jumlah_halaman);
        $stmt->bindParam(':kategori', $kategori);
        $stmt->bindParam(':isbn', $isbn);
        $stmt->bindParam(':sinopsis', $sinopsis);
        $stmt->bindParam(':cover', $cover);
        $stmt->bindParam(':stok', $stok, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Buku berhasil ditambahkan"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menambahkan buku"]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Terjadi kesalahan: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Metode tidak diizinkan"]);
}
?>
