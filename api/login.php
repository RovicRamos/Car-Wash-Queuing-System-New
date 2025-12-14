<?php
include 'db_config.php';
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) send_error(400, 'Invalid request body.');

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) send_error(400, 'Email and password required.');

$stmt = $conn->prepare("SELECT id, email, password_hash, role, email_verified_at FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password_hash'])) {
        
        // CHECK VERIFICATION
        if ($user['email_verified_at'] === null) {
            send_error(403, 'Email not verified. Please verify your account first.');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        send_success([
            'message' => 'Login successful.',
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);
    } else {
        send_error(401, 'Invalid email or password.');
    }
} else {
    send_error(401, 'Invalid email or password.');
}
?>