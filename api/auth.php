<?php
session_start();
include '../db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if ($action === 'login') {
    $user = $data['username'];
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $user, $user);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($data['password'], $result['password'])) {
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['username'] = $result['username'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid Login']);
    }
}

if ($action === 'register') {
    $pass = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, display_name, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['username'], $data['email'], $data['display_name'], $pass);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'User already exists']);
    }
}
?>
