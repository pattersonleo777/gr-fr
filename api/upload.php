<?php
session_start();
if (!isset($_SESSION['user_id'])) die(json_encode(['error' => 'Unauthorized']));

$type = $_POST['type']; // 'images' or 'models'
$targetDir = "../uploads/" . $type . "/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    echo json_encode(['success' => true, 'path' => 'uploads/' . $type . '/' . $fileName]);
} else {
    echo json_encode(['success' => false]);
}
?>
