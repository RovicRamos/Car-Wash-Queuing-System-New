<?php
// api/queue.php
include 'db_config.php'; // <--- UPDATED PATH

// Allow public access OR require auth depending on preference.
// For now, let's require auth so we know who is viewing.
require_auth();

$sql = "SELECT b.id, b.user_id, b.service_name, b.status, b.created_at, b.staff_assigned, b.notes, u.email as user_email
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        WHERE b.status IN ('pending', 'in_progress')
        ORDER BY 
            CASE WHEN b.status = 'in_progress' THEN 1 ELSE 2 END,
            b.created_at ASC";
        
$result = $conn->query($sql);

if ($result) {
    $queue = $result->fetch_all(MYSQLI_ASSOC);
    send_success($queue);
} else {
    send_error(500, 'Failed to fetch queue: ' . $conn->error);
}
?>