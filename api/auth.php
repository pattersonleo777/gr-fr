<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'));

// Accept any login for now to test functionality
if (!empty($data->email) && !empty($data->password)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
?>
