<?php
// Konfigurasi database
$host = "sgp.domcloud.co";
$dbname = "radja_proyek_perpustakaan_db";
$username = "radja-proyek-perpustakaan";  // Sesuaikan dengan username database
$password = "r3u2b)w)XAi_P47AW1";  // Sesuaikan dengan password database

// Class Database untuk file yang membutuhkan OOP approach
class Database {
    private $host = "sgp.domcloud.co";
    private $dbname = "radja_proyek_perpustakaan_db";
    private $username = "radja-proyek-perpustakaan";  // Sesuaikan dengan username database
    private $password = "r3u2b)w)XAi_P47AW1";  // Sesuaikan dengan password database
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Connection error: " . $exception->getMessage());
        }

        return $this->conn;
    }
}

// Membuat koneksi global untuk file yang menggunakan $conn langsung
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Koneksi mysqli untuk kompatibilitas (jika ada file yang menggunakan mysqli)
$koneksi = mysqli_connect($host, $username, $password, $dbname);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
