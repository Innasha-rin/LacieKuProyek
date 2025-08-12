<?php
// api/notifications.php
session_start();
require_once 'notificationPage_class.php';

// Set header untuk JSON response
header('Content-Type: application/json');

// Cek apakah user sudah login
if (!isset($_SESSION['nim'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$notification = new Notification();
$nim = $_SESSION['nim'];

// Handle berbagai request method
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        handleGetRequest($notification, $nim);
        break;
    
    case 'POST':
        handlePostRequest($notification, $nim);
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function handleGetRequest($notification, $nim) {
    if (isset($_GET['action'])) {
        switch($_GET['action']) {
            case 'list':
                getNotificationsList($notification, $nim);
                break;
            
            case 'unread_count':
                getUnreadCount($notification, $nim);
                break;
            
            case 'detail':
                if (isset($_GET['id'])) {
                    getNotificationDetail($notification, $nim, $_GET['id']);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID parameter required']);
                }
                break;
            
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    } else {
        getNotificationsList($notification, $nim);
    }
}

function handlePostRequest($notification, $nim) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action'])) {
        switch($input['action']) {
            case 'mark_read':
                if (isset($input['id'])) {
                    markNotificationAsRead($notification, $nim, $input['id']);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID parameter required']);
                }
                break;
            
            default:
                http_response_code(400);
                echo json_encode(['error' => 'Invalid action']);
                break;
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Action parameter required']);
    }
}

function getNotificationsList($notification, $nim) {
    try {
        $stmt = $notification->getNotificationsByNIM($nim);
        $notifications = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $notifications[] = [
                'id' => $row['id'],
                'pesan' => $row['pesan'],
                'status' => $row['status'],
                'tanggal_kirim' => $row['tanggal_kirim'],
                'tanggal_formatted' => $notification->formatDateIndonesian($row['tanggal_kirim']),
                'waktu_formatted' => $notification->formatTime($row['tanggal_kirim'])
            ];
        }
        
        echo json_encode([
            'success' => true,
            'data' => $notifications
        ]);
        
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch notifications']);
    }
}

function getUnreadCount($notification, $nim) {
    try {
        $count = $notification->getUnreadCount($nim);
        echo json_encode([
            'success' => true,
            'unread_count' => $count
        ]);
        
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to get unread count']);
    }
}

function getNotificationDetail($notification, $nim, $id) {
    try {
        $detail = $notification->getNotificationById($id, $nim);
        
        if ($detail) {
            // Mark as read when viewed
            $notification->markAsRead($id, $nim);
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $detail['id'],
                    'pesan' => $detail['pesan'],
                    'status' => 'dibaca', // Update status since we just marked it as read
                    'tanggal_kirim' => $detail['tanggal_kirim'],
                    'tanggal_formatted' => $notification->formatDateIndonesian($detail['tanggal_kirim']),
                    'waktu_formatted' => $notification->formatTime($detail['tanggal_kirim'])
                ]
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Notification not found']);
        }
        
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch notification detail']);
    }
}

function markNotificationAsRead($notification, $nim, $id) {
    try {
        $result = $notification->markAsRead($id, $nim);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Failed to mark notification as read']);
        }
        
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
    }
}
?>