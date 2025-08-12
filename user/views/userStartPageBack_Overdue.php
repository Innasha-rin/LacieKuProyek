<?php
// ajax_check_overdue.php
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
    $currentDate = date('Y-m-d');
    
    // Check for overdue books
    $sql_overdue = "SELECT COUNT(*) as overdue_count,
                           GROUP_CONCAT(buku.judul SEPARATOR ', ') as overdue_titles
                    FROM peminjaman
                    JOIN buku ON peminjaman.id_buku = buku.id
                    WHERE peminjaman.nim = :nim 
                    AND peminjaman.status_pengembalian = 'dipinjam'
                    AND peminjaman.tanggal_jatuh_tempo < :current_date";
    
    $stmt_overdue = $conn->prepare($sql_overdue);
    $stmt_overdue->bindParam(':nim', $nim);
    $stmt_overdue->bindParam(':current_date', $currentDate);
    $stmt_overdue->execute();
    
    $overdueResult = $stmt_overdue->fetch(PDO::FETCH_ASSOC);
    $overdueCount = (int)$overdueResult['overdue_count'];
    
    // Check for books due in next 3 days (warning)
    $warningDate = date('Y-m-d', strtotime('+3 days'));
    $sql_warning = "SELECT COUNT(*) as warning_count,
                           GROUP_CONCAT(buku.judul SEPARATOR ', ') as warning_titles
                    FROM peminjaman
                    JOIN buku ON peminjaman.id_buku = buku.id
                    WHERE peminjaman.nim = :nim 
                    AND peminjaman.status_pengembalian = 'dipinjam'
                    AND peminjaman.tanggal_jatuh_tempo BETWEEN :current_date AND :warning_date";
    
    $stmt_warning = $conn->prepare($sql_warning);
    $stmt_warning->bindParam(':nim', $nim);
    $stmt_warning->bindParam(':current_date', $currentDate);
    $stmt_warning->bindParam(':warning_date', $warningDate);
    $stmt_warning->execute();
    
    $warningResult = $stmt_warning->fetch(PDO::FETCH_ASSOC);
    $warningCount = (int)$warningResult['warning_count'];
    
    $response = [
        'success' => true,
        'hasOverdue' => $overdueCount > 0,
        'count' => $overdueCount,
        'titles' => $overdueResult['overdue_titles'],
        'hasWarning' => $warningCount > 0,
        'warningCount' => $warningCount,
        'warningTitles' => $warningResult['warning_titles'],
        'currentDate' => $currentDate
    ];
    
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