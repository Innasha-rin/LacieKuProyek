<?php
// ajax_dashboard_data.php
header('Content-Type: application/json');
require '../../session/sessionUser.php';
include 'database.php';

// Check if user is logged in
if (!isset($_SESSION['nim'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Sesi tidak valid'
    ]);
    exit();
}

try {
    $nim = $_SESSION['nim'];
    $response = [
        'success' => true,
        'bookCount' => 0,
        'books' => [],
        'fine' => 'Rp 0'
    ];

    // Get total borrowed books count
    $sql_count = "SELECT COUNT(*) as total 
                  FROM peminjaman 
                  WHERE nim = :nim AND status_pengembalian = 'dipinjam' AND status_pengambilan = 'diambil'";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bindParam(':nim', $nim);
    $stmt_count->execute();
    $countResult = $stmt_count->fetch(PDO::FETCH_ASSOC);
    $response['bookCount'] = (int)$countResult['total'];

    // Get latest 2 borrowed books
    $sql_books = "SELECT buku.judul, peminjaman.tanggal_peminjaman, peminjaman.tanggal_jatuh_tempo
                  FROM peminjaman
                  JOIN buku ON peminjaman.id_buku = buku.id
                  WHERE peminjaman.nim = :nim AND peminjaman.status_pengembalian = 'dipinjam' AND peminjaman.status_pengambilan = 'diambil'
                  ORDER BY peminjaman.tanggal_peminjaman DESC
                  LIMIT 2";

    $stmt_books = $conn->prepare($sql_books);
    $stmt_books->bindParam(':nim', $nim);
    $stmt_books->execute();
    $response['books'] = $stmt_books->fetchAll(PDO::FETCH_ASSOC);

    // Get fine amount
    $sql_denda = "SELECT SUM(jumlah) as total_denda 
                  FROM denda 
                  WHERE nim = :nim AND status = 'belum bayar'";
    $stmt_denda = $conn->prepare($sql_denda);
    $stmt_denda->bindParam(':nim', $nim);
    $stmt_denda->execute();
    $rowDenda = $stmt_denda->fetch(PDO::FETCH_ASSOC);

    if ($rowDenda && $rowDenda['total_denda'] > 0) {
        $response['fine'] = 'Rp ' . number_format($rowDenda['total_denda'], 0, ',', '.');
    } else {
        $response['fine'] = 'Rp 0';
    }

    // Add timestamp for debugging
    $response['timestamp'] = date('Y-m-d H:i:s');

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan database: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>