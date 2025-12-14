<?php
// api/services.php
include 'db_config.php'; // <--- UPDATED PATH

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET': handle_get(); break;
    case 'POST': require_admin(); handle_save(); break;
    case 'DELETE': require_admin(); handle_delete(); break;
    default: send_error(405, 'Method Not Allowed.');
}

function handle_get() {
    global $conn;
    $result = $conn->query("SELECT id, name, description, price, image_path FROM services");
    if ($result) send_success($result->fetch_all(MYSQLI_ASSOC));
    else send_error(500, 'Failed to fetch services.');
}

function handle_save() {
    global $conn;
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $desc = $_POST['description'] ?? '';
    
    if (empty($name) || !is_numeric($price) || $price < 0) send_error(400, 'Invalid input.');

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Save uploaded images to 'uploads/' folder inside 'api/' parent directory
        $dir = '../uploads/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file = uniqid('svc_') . '.' . $ext;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $file)) {
            $imagePath = 'uploads/' . $file;
        }
    }

    if ($id) {
        $sql = "UPDATE services SET name=?, description=?, price=?" . ($imagePath ? ", image_path=?" : "") . " WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($imagePath) $stmt->bind_param("ssdsi", $name, $desc, $price, $imagePath, $id);
        else $stmt->bind_param("ssdi", $name, $desc, $price, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO services (name, description, price, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $desc, $price, $imagePath);
    }
    
    if ($stmt->execute()) send_success(['message' => 'Service saved.']);
    else send_error(500, 'Database error: ' . $stmt->error);
}

function handle_delete() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? 0;
    
    $check_active = $conn->prepare("SELECT id FROM bookings WHERE service_id = ? AND status IN ('pending', 'in_progress')");
    $check_active->bind_param("i", $id);
    $check_active->execute();
    if ($check_active->get_result()->num_rows > 0) {
        send_error(409, 'Cannot delete: Service is in active queue.');
    }

    try {
        $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) send_success(['message' => 'Service deleted.']);
        else throw new Exception($stmt->error);
    } catch (Exception $e) {
        if ($conn->errno == 1451) send_error(409, 'Cannot delete: Service exists in sales history.');
        else send_error(500, 'Delete failed: ' . $e->getMessage());
    }
}
?>