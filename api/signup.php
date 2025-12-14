<?php
include 'db_config.php'; // Uses the config in same folder

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) send_error(400, 'Invalid request body.');

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($name) || empty($email) || empty($password)) send_error(400, 'All fields are required.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) send_error(400, 'Invalid email format.');
if (strlen($password) < 6) send_error(400, 'Password must be at least 6 characters.');

// Check existing
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) send_error(409, 'Email already in use.');

// Hash & Generate Code
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$verification_code = rand(100000, 999999);

// Insert user (Unverified)
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role, verification_code, email_verified_at) VALUES (?, ?, ?, 'customer', ?, NULL)");
$stmt->bind_param("ssss", $name, $email, $password_hash, $verification_code);

if ($stmt->execute()) {
    // Send Email
    send_verification_email($email, $verification_code);
    
    send_success([
        'message' => 'Account created. Please verify your email.',
        'require_verification' => true,
        'email' => $email
        // 'debug_code' => $verification_code // Uncomment for testing if email fails
    ]);
} else {
    send_error(500, 'Failed to register user.');
}
?>