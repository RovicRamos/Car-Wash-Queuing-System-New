<?php
include 'db_config.php';
require_admin();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': handle_get(); break;
    case 'POST': handle_post(); break;
    case 'DELETE': handle_delete(); break;
    default: send_error(405, 'Method Not Allowed.');
}

function handle_get() {
    global $conn;
    $result = $conn->query("SELECT id, name, email, role, created_at, email_verified_at FROM users ORDER BY created_at DESC");
    if ($result) send_success($result->fetch_all(MYSQLI_ASSOC));
    else send_error(500, 'Failed to fetch users.');
}

function handle_post() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $pass = $data['password'] ?? '';
    $role = $data['role'] ?? 'customer';

    if (empty($name) || empty($email) || empty($pass)) send_error(400, 'All fields required.');
    if (!in_array($role, ['customer', 'admin', 'staff'])) send_error(400, 'Invalid role.');

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) send_error(409, 'Email already taken.');

    $hash = password_hash($pass, PASSWORD_DEFAULT);
    
    // ADMIN CREATED USERS ARE AUTO-VERIFIED
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, email_verified_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $hash, $role);

    if ($stmt->execute()) send_success(['id' => $stmt->insert_id, 'message' => 'User created and auto-verified.']);
    else send_error(500, 'Database error.');
}

function handle_delete() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    if ($id == $_SESSION['user_id']) send_error(403, 'Cannot delete yourself.');
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) send_success(['message' => 'User deleted.']);
    else send_error(500, 'Failed to delete user.');
}
?>