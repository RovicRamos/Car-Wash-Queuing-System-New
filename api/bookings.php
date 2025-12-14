<?php
// api/bookings.php
include 'db_config.php'; // <--- FIXED PATH

$method = $_SERVER['REQUEST_METHOD'];
require_auth();

switch ($method) {
    case 'GET': handle_get(); break;
    case 'POST': handle_post(); break;
    case 'PUT': require_staff(); handle_put(); break;
    default: send_error(405, 'Method Not Allowed.');
}

function handle_get() {
    global $conn;
    
    $target_user_id = $_SESSION['user_id']; // Default: fetch my own history

    // Allow Admin/Staff to view a specific user's history
    // Check if 'user_id' is passed in the URL (e.g., ?user_id=5)
    if (isset($_GET['user_id'])) {
        require_staff(); // Security check: Only staff/admin can see other people's history
        $target_user_id = $_GET['user_id'];
    }

    // Fetch bookings
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $target_user_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        send_success($result);
    } else {
        send_error(500, 'Failed to fetch history.');
    }
}

function handle_post() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = $_SESSION['user_id'];
    $service_id = $data['service_id'] ?? 0;
    $notes = $data['notes'] ?? '';

    if (empty($service_id)) send_error(400, 'Invalid service ID.');
    
    // Get Service Details
    $svc = $conn->prepare("SELECT name, price FROM services WHERE id = ?");
    $svc->bind_param("i", $service_id);
    $svc->execute();
    $res = $svc->get_result();
    if ($res->num_rows === 0) send_error(404, 'Service not found.');
    $s = $res->fetch_assoc();

    // Create Booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, service_name, service_price, notes, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisds", $user_id, $service_id, $s['name'], $s['price'], $notes);
    
    if ($stmt->execute()) {
        // Notify Staff
        $staff_query = $conn->query("SELECT id FROM users WHERE role IN ('admin', 'staff')");
        while ($staff = $staff_query->fetch_assoc()) {
            create_notification($conn, $staff['id'], "New Booking: " . $s['name']);
        }
        send_success(['id' => $stmt->insert_id, 'message' => 'Booking created.']);
    } else send_error(500, 'Failed to create booking.');
}

function handle_put() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = $data['booking_id'] ?? 0;
    $status = $data['status'] ?? '';
    
    if (empty($booking_id) || empty($status)) send_error(400, 'ID and status required.');
    
    $sql = "UPDATE bookings SET status = ?";
    $params = [$status]; $types = "s";

    if ($status === 'in_progress') {
        $staff = $data['staff_assigned'] ?? 'Unknown';
        $sql .= ", staff_assigned = ?, started_at = CURRENT_TIMESTAMP";
        $params[] = $staff; $types .= "s";
    } elseif ($status === 'completed') {
        $sql .= ", completed_at = CURRENT_TIMESTAMP";
    } elseif ($status === 'cancelled') {
        $sql .= ", cancelled_at = CURRENT_TIMESTAMP";
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $booking_id; $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Notify Customer
        $b_query = $conn->query("SELECT user_id, service_name FROM bookings WHERE id = $booking_id");
        if ($b = $b_query->fetch_assoc()) {
            $msg = "Your wash ($b[service_name]) is now " . str_replace('_', ' ', $status) . ".";
            create_notification($conn, $b['user_id'], $msg);
        }
        send_success(['id' => $booking_id, 'status' => $status]);
    } else send_error(500, 'Failed to update booking.');
}
?>