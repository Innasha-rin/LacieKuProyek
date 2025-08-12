<?php
include 'database.php';
session_start();
date_default_timezone_set('Asia/Makassar');
if (!isset($_SESSION['nim'])) {
    die("Akses tidak sah");
}

$nim = $_SESSION['nim'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti'])) {
    // Debug: Cek error upload
    if ($_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
            UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP'
        ];
        
        $error_msg = $error_messages[$_FILES['bukti']['error']] ?? 'Error upload tidak dikenal';
        $_SESSION['message'] = 'Error upload: ' . $error_msg;
        $_SESSION['message_type'] = 'error';
        header("Location: paymentFine.php");
        exit();
    }

    $folder = "../receipt/";
    
    // Generate unique filename to prevent conflicts
    $original_filename = $_FILES['bukti']['name'];
    $filetype = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $filename = $nim . '_' . time() . '_' . uniqid() . '.' . $filetype;
    $target = $folder . $filename;

    // Validasi file
    $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($filetype, $allowed)) {
        $_SESSION['message'] = 'Tipe file tidak didukung. Hanya PDF, JPG, JPEG, PNG yang diizinkan.';
        $_SESSION['message_type'] = 'error';
        header("Location: paymentFine.php");
        exit();
    }
    
    // Validasi ukuran file (max 5MB)
    $max_size = 5 * 1024 * 1024; // 5MB
    if ($_FILES['bukti']['size'] > $max_size) {
        $_SESSION['message'] = 'Ukuran file terlalu besar. Maksimal 5MB.';
        $_SESSION['message_type'] = 'error';
        header("Location: paymentFine.php");
        exit();
    }

    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
        try {
            // Buat timestamp saat ini
            $tanggal_upload = date('Y-m-d H:i:s');
            
            // Update bukti_pembayaran, status, dan tanggal upload
            $stmt = $conn->prepare("UPDATE denda SET 
                bukti_pembayaran = :bukti, 
                status = 'lunas',
                tanggal_upload_bukti = :tanggal_upload,
                status_verifikasi = 'belum diverifikasi',
                status_pembayaran = 'menunggu'
                WHERE nim = :nim AND status = 'belum bayar'");
            
            $stmt->execute([
                'bukti' => $filename,
                'tanggal_upload' => $tanggal_upload,
                'nim' => $nim
            ]);

            // Set session message untuk feedback ke user
            $_SESSION['message'] = 'Bukti pembayaran berhasil dikirim pada ' . date('d/m/Y H:i:s', strtotime($tanggal_upload)) . '. Menunggu verifikasi admin.';
            $_SESSION['message_type'] = 'success';
            
            header("Location: paymentFine.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Gagal mengirim bukti pembayaran: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
            header("Location: paymentFine.php");
            exit();
        }
    } else {
        $_SESSION['message'] = 'Gagal mengupload file.';
        $_SESSION['message_type'] = 'error';
        header("Location: paymentFine.php");
        exit();
    }
} else {
    $_SESSION['message'] = 'Tidak ada file diupload.';
    $_SESSION['message_type'] = 'error';
    header("Location: paymentFine.php");
    exit();
}