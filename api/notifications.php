<?php
include '../db_config.php';
require_auth();

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];

if ($method === 'GET') {
    // Fetch last 10 notifications
    $stmt = $conn->prepare("SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifs = $result->fetch_all(MYSQLI_ASSOC);
    
    // Count unread
    $stmt_count = $conn->prepare("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt_count->bind_param("i", $user_id);
    $stmt_count->execute();
    $unread = $stmt_count->get_result()->fetch_assoc()['c'];
    
    send_success(['notifications' => $notifs, 'unread_count' => $unread]);

} elseif ($method === 'POST') {
    // Mark all as read
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    send_success(['message' => 'Notifications marked read']);
} else {
    send_error(405, 'Method not allowed');
}
?>