<?php
include 'db_config.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$code = $data['code'] ?? '';

if (empty($email) || empty($code)) send_error(400, 'Email and code required.');

// Check code
$stmt = $conn->prepare("SELECT id, name, role FROM users WHERE email = ? AND verification_code = ?");
$stmt->bind_param("ss", $email, $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Mark as verified
    $update = $conn->prepare("UPDATE users SET email_verified_at = NOW(), verification_code = NULL WHERE id = ?");
    $update->bind_param("i", $user['id']);
    $update->execute();
    
    // Log user in immediately
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['email'] = $email;
    
    send_success([
        'message' => 'Email verified successfully!',
        'user' => [
            'id' => $user['id'],
            'email' => $email,
            'role' => $user['role']
        ]
    ]);
} else {
    send_error(400, 'Invalid verification code.');
}
?>