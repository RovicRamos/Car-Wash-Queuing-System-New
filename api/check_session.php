<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    // User is logged in
    echo json_encode([
        'status' => 'success',
        'data' => [
            'loggedIn' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role']
            ]
        ]
    ]);
} else {
    // User is not logged in
    echo json_encode([
        'status' => 'success',
        'data' => [
            'loggedIn' => false
        ]
    ]);
}
exit;
?>
