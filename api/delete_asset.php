<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) die(json_encode(['success' => false, 'error' => 'Unauthorized']));

$data = json_decode(file_get_contents('php://input'), true);
$path = $data['path'] ?? '';

// Basic security: prevent directory traversal
if (strpos($path, 'uploads/') === 0 && !strpos($path, '..')) {
    $fullPath = "../" . $path;
    if (file_exists($fullPath) && unlink($fullPath)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'File not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid path']);
}
?>
