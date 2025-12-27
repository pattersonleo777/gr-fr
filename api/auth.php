<?php
session_start();
include '../db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (($data['action'] ?? '') === 'login') {
    // Captcha Validation
    $responseKeys = json_decode($response, true);
    
    if (!$responseKeys["success"]) {
        die(json_encode(['success' => false, 'error' => 'Captcha failed']));
    }

    $user = $data['username'];
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if ($res && password_verify($data['password'], $res['password'])) {
        $_SESSION['user_id'] = $res['id'];
        $_SESSION['username'] = $res['username'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
    }
}
?>
