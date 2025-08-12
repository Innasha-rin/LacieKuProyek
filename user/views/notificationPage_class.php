<?php
// classes/Notification.php
require_once 'database.php';

class Notification {
    private $conn;
    private $table_name = "notifikasi";

    public $id;
    public $nim;
    public $pesan;
    public $status;
    public $tanggal_kirim;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Mengambil semua notifikasi berdasarkan NIM
    public function getNotificationsByNIM($nim) {
        $query = "SELECT id, nim, pesan, status, tanggal_kirim 
                  FROM " . $this->table_name . " 
                  WHERE nim = :nim 
                  ORDER BY tanggal_kirim DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        return $stmt;
    }

    // Mengambil notifikasi berdasarkan ID
    public function getNotificationById($id, $nim) {
        $query = "SELECT id, nim, pesan, status, tanggal_kirim 
                  FROM " . $this->table_name . " 
                  WHERE id = :id AND nim = :nim";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update status notifikasi menjadi 'dibaca'
    public function markAsRead($id, $nim) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'dibaca' 
                  WHERE id = :id AND nim = :nim";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nim', $nim);
        
        return $stmt->execute();
    }

    // Menghitung jumlah notifikasi yang belum dibaca
    public function getUnreadCount($nim) {
        $query = "SELECT COUNT(*) as unread_count 
                  FROM " . $this->table_name . " 
                  WHERE nim = :nim AND status = 'terkirim'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['unread_count'];
    }

    // Membuat notifikasi baru (untuk keperluan admin)
    public function createNotification($nim, $pesan) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (nim, pesan, status, tanggal_kirim) 
                  VALUES (:nim, :pesan, 'terkirim', NOW())";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->bindParam(':pesan', $pesan);
        
        return $stmt->execute();
    }

    // Format tanggal Indonesia
    public function formatDateIndonesian($datetime) {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $date = new DateTime($datetime);
        $day = $date->format('j');
        $month = $months[(int)$date->format('n')];
        $year = $date->format('Y');
        
        return $day . ' ' . $month . ' ' . $year;
    }

    // Format waktu
    public function formatTime($datetime) {
        $date = new DateTime($datetime);
        return $date->format('H:i:s');
    }
}
?>