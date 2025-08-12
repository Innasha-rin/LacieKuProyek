<?php
// hapusUser.php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set ke 0 agar error tidak dikirim dalam respons JSON

include 'database.php';

// Log permintaan untuk debugging
file_put_contents('delete_log.txt', date('Y-m-d H:i:s') . ' - ID: ' . $_POST['id'] . "\n", FILE_APPEND);

// Validasi apakah ID diberikan dan merupakan angka
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        // Siapkan dan eksekusi query untuk menghapus pengguna
        $query = "DELETE FROM mahasiswa WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $success = $stmt->execute();
        
        if ($success) {
            // Jika berhasil, kirim respons sukses
            echo json_encode(['status' => 'success', 'message' => 'Pengguna berhasil dihapus']);
        } else {
            // Jika gagal, kirim respons gagal
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus pengguna']);
        }
    } catch (PDOException $e) {
        // Tangani kesalahan database
        file_put_contents('error_log.txt', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . "\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    // Jika ID tidak valid
    echo json_encode(['status' => 'error', 'message' => 'ID pengguna tidak valid']);
}
?>