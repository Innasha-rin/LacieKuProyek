<?php
require_once 'database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class DashboardAPI {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getUnverifiedPayments() {
        try {
            $query = "SELECT COUNT(*) as total FROM denda 
                     WHERE status_verifikasi = 'belum diverifikasi' 
                     AND status_pembayaran = 'menunggu'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'count' => (int)$result['total']
            ];
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getStatistics() {
        try {
            $query = "SELECT COUNT(*) as total FROM buku";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $totalBooks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $query = "SELECT COUNT(*) as total FROM mahasiswa";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $query = "SELECT COUNT(*) as total FROM peminjaman WHERE DATE(tanggal_peminjaman) = CURDATE() AND status_konfirmasi = 'sudah'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $dailyLoans = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            $query = "SELECT COUNT(*) as total FROM denda";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $lateReturns = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            return [
                'totalBooks' => $totalBooks,
                'totalUsers' => $totalUsers,
                'dailyLoans' => $dailyLoans,
                'lateReturns' => $lateReturns
            ];
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getBorrowingStats() {
        try {
            $data = [];
            $labels = [];
            
            $query = "SELECT DATE(tanggal_peminjaman) as date, COUNT(*) as count 
                      FROM peminjaman 
                      WHERE tanggal_peminjaman >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                      AND status_konfirmasi = 'sudah'
                      GROUP BY DATE(tanggal_peminjaman) 
                      ORDER BY DATE(tanggal_peminjaman)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $currentDate = new DateTime();
            $currentDate->modify('-29 days');
            
            for ($i = 0; $i < 30; $i++) {
                $formattedDate = $currentDate->format('Y-m-d');
                $labels[] = $currentDate->format('j');
                
                $found = false;
                foreach ($results as $row) {
                    if ($row['date'] == $formattedDate) {
                        $data[] = (int)$row['count'];
                        $found = true;
                        break;
                    }
                }
                
                if (!$found) {
                    $data[] = 0;
                }
                
                $currentDate->modify('+1 day');
            }
            
            return [
                'labels' => $labels,
                'data' => $data
            ];
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getPopularBooks() {
        try {
            $query = "SELECT b.id, b.judul, b.kategori, COUNT(p.id) as total_pinjam
                        FROM buku b
                        JOIN peminjaman p ON b.id = p.id_buku
                        WHERE p.status_konfirmasi = 'sudah'
                        GROUP BY b.id
                        ORDER BY total_pinjam DESC
                        LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $popularBooks = [];
            $i = 1;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $popularBooks[] = [
                    'id' => $i++,
                    'title' => $row['judul'],
                    'category' => $row['kategori']
                ];
            }
            
            return $popularBooks;
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getActiveBorrowers() {
        try {
            $query = "SELECT m.id, m.nim, m.nama, COUNT(p.id) as total_pinjam
                        FROM mahasiswa m
                        JOIN peminjaman p ON p.nim = m.nim
                        WHERE p.status_konfirmasi = 'sudah'
                        GROUP BY m.id
                        ORDER BY total_pinjam DESC
                        LIMIT 5";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $activeBorrowers = [];
            $i = 1;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $activeBorrowers[] = [
                    'id' => $i++,
                    'nim' => $row['nim'],
                    'name' => $row['nama']
                ];
            }
            
            return $activeBorrowers;
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    public function getLateReturns() {
        try {
            $query = "SELECT 
                        p.id, 
                        m.nim, 
                        m.nama, 
                        b.judul, 
                        DATEDIFF(CURDATE(), p.tanggal_jatuh_tempo) AS days_late,
                        d.jumlah AS total_denda
                        FROM 
                        peminjaman p
                        JOIN 
                        mahasiswa m ON p.nim = m.nim
                        JOIN 
                        buku b ON p.id_buku = b.id
                        JOIN 
                        denda d ON p.id = d.id_peminjaman
                        WHERE 
                        d.status = 'belum bayar' 
                        AND d.status_pembayaran = 'menunggu'
                        AND p.tanggal_jatuh_tempo < CURDATE()
                        ORDER BY 
                        days_late DESC
                        LIMIT 5";
    
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $lateReturns = [];
            $i = 1;
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $formattedDenda = 'Rp' . number_format($row['total_denda'], 0, ',', '.') . ',00';
                
                $lateReturns[] = [
                    'id' => $i++,
                    'nim' => $row['nim'],
                    'name' => $row['nama'],
                    'bookTitle' => $row['judul'],
                    'fine' => $formattedDenda
                ];
            }
            
            return $lateReturns;
        } catch(PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }    
}

function processRequest($conn) {
    $api = new DashboardAPI($conn);

    if (!isset($_GET['action'])) {
        return json_encode(['error' => 'No action specified']);
    }

    $action = $_GET['action'];

    switch ($action) {
        case 'getStatistics':
            return json_encode($api->getStatistics());
        case 'getBorrowingStats':
            return json_encode($api->getBorrowingStats());
        case 'getPopularBooks':
            return json_encode($api->getPopularBooks());
        case 'getActiveBorrowers':
            return json_encode($api->getActiveBorrowers());
        case 'getLateReturns':
            return json_encode($api->getLateReturns());
        case 'getUnverifiedPayments':
            return json_encode($api->getUnverifiedPayments());
        case 'getAllDashboardData':
            return json_encode([
                'statistics' => $api->getStatistics(),
                'borrowingStats' => $api->getBorrowingStats(),
                'popularBooks' => $api->getPopularBooks(),
                'activeBorrowers' => $api->getActiveBorrowers(),
                'lateReturns' => $api->getLateReturns(),
                'unverifiedPayments' => $api->getUnverifiedPayments()
            ]);
        default:
            return json_encode(['error' => 'Invalid action']);
    }
}

// Handle API requests if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    header('Content-Type: application/json');

    try {
        // Menggunakan koneksi PDO dari database.php
        echo processRequest($conn);
    } catch (PDOException $e) {
        echo json_encode(['error' => "Database connection failed: " . $e->getMessage()]);
    }
}
?>