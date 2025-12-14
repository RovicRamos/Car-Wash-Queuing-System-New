<?php
// api/db_config.php

// 1. DISABLE HTML ERRORS (Prevents JSON crashes)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// 2. CHECK PHPMAILER PATH
// Assumes: C:\xampp\htdocs\carwash\api\ (this file)
// Target:  C:\xampp\htdocs\carwash\libs\PHPMailer\src\
$base_path = __DIR__ . '/../libs/PHPMailer/src/';

// Check if files exist before loading to prevent crash
if (!file_exists($base_path . 'PHPMailer.php')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'PHPMailer not found. Check libs folder.', 'path' => $base_path]);
    exit;
}

require_once $base_path . 'Exception.php';
require_once $base_path . 'PHPMailer.php';
require_once $base_path . 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 3. DATABASE CONNECTION (LOCAL XAMPP)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');      // Standard XAMPP user
define('DB_PASSWORD', '');          // Standard XAMPP password (empty)
define('DB_NAME', 'rovics_car_wash'); 

try {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// 4. HELPER FUNCTIONS
function send_error($code, $message, $details = []) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $message, 'details' => $details]);
    exit;
}

function send_success($data) {
    echo json_encode(['status' => 'success', 'data' => $data]);
    exit;
}

function require_auth() {
    if (!isset($_SESSION['user_id'])) send_error(401, 'Unauthorized. Please log in.');
}

function require_admin() {
    require_auth();
    if ($_SESSION['role'] !== 'admin') send_error(403, 'Forbidden. Admin access required.');
}

function require_staff() {
    require_auth();
    if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff') send_error(403, 'Forbidden. Staff access required.');
}

function create_notification($conn, $user_id, $message) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    $stmt->close();
}

// 5. EMAIL FUNCTION
function send_verification_email($email, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        
        // YOUR CREDENTIALS
        $mail->Username   = 'rovicramosfermin@gmail.com'; 
        $mail->Password   = 'qzwd ujqg auro itqm';    
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('rovicramosfermin@gmail.com', "Rovic's Car Wash");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Verify your account";
        $mail->Body    = "<h2>Verification Code: $code</h2>";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        // error_log($mail->ErrorInfo);
        return false;
    }
}
?>