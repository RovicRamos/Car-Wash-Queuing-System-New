<?php
// api/reports.php
include 'db_config.php'; // <--- UPDATED PATH
require_admin(); 

$method = $_SERVER['REQUEST_METHOD'];

// Handle "Reset Stats" Action
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action']) && $data['action'] === 'reset') {
        // Update the 'last_reset' timestamp to NOW
        $conn->query("UPDATE settings SET setting_value = CURRENT_TIMESTAMP WHERE setting_key = 'last_reset'");
        send_success(['message' => 'New day started. Stats reset.']);
    }
    exit;
}

// Handle GET Reports
$type = $_GET['type'] ?? 'dashboard';

// Fetch the last reset time from DB
$reset_result = $conn->query("SELECT setting_value FROM settings WHERE setting_key = 'last_reset'");
$last_reset = $reset_result->fetch_assoc()['setting_value'] ?? date('Y-m-d 00:00:00');

if ($type === 'dashboard') {
    handle_dashboard($last_reset);
} else {
    handle_full_report($last_reset);
}

function handle_dashboard($start_time) {
    global $conn;
    
    // Counts
    $pending = $conn->query("SELECT COUNT(id) as c FROM bookings WHERE status = 'pending'")->fetch_assoc()['c'] ?? 0;
    $in_progress = $conn->query("SELECT COUNT(id) as c FROM bookings WHERE status = 'in_progress'")->fetch_assoc()['c'] ?? 0;
    
    // Completed & Revenue TODAY (since last reset)
    $stmt = $conn->prepare("SELECT COUNT(id) as c, SUM(service_price) as t FROM bookings WHERE status = 'completed' AND completed_at >= ?");
    $stmt->bind_param("s", $start_time);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $completed = $res['c'] ?? 0;
    $revenue = $res['t'] ?? 0;
    
    // Recent Activity
    $stmt = $conn->prepare("SELECT service_name, service_price, staff_assigned, completed_at FROM bookings WHERE status = 'completed' AND completed_at >= ? ORDER BY completed_at DESC LIMIT 5");
    $stmt->bind_param("s", $start_time);
    $stmt->execute();
    $activity = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
    send_success([
        'pending' => $pending,
        'in_progress' => $in_progress,
        'completed_today' => $completed,
        'revenue_today' => $revenue,
        'recent_activity' => $activity
    ]);
}

function handle_full_report($start_time) {
    global $conn;
    
    try {
        // Totals
        $stmt = $conn->prepare("SELECT COUNT(id) as c, SUM(service_price) as t FROM bookings WHERE status = 'completed' AND completed_at >= ?");
        $stmt->bind_param("s", $start_time);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $revenue = $res['t'] ?? 0;
        $washes = $res['c'] ?? 0;
        
        // Staff Performance
        $stmt = $conn->prepare("SELECT staff_assigned, COUNT(id) as washes_completed FROM bookings WHERE status = 'completed' AND completed_at >= ? GROUP BY staff_assigned ORDER BY washes_completed DESC");
        $stmt->bind_param("s", $start_time);
        $stmt->execute();
        $staff = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Log
        $stmt = $conn->prepare("SELECT * FROM bookings WHERE status = 'completed' AND completed_at >= ? ORDER BY completed_at DESC");
        $stmt->bind_param("s", $start_time);
        $stmt->execute();
        $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        send_success([
            'total_revenue_today' => $revenue,
            'total_washes_today' => $washes,
            'staff_performance' => $staff,
            'completed_bookings_today' => $bookings
        ]);
    } catch (Exception $e) {
        send_error(500, $e->getMessage());
    }
}
?>