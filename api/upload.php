<?php
header('Content-Type: application/json');
$targetDir = "../uploads/";
$fileName = basename($_FILES["file"]["name"]);
$targetFilePath = $targetDir . $fileName;

if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
    echo json_encode(["success" => true, "filePath" => "uploads/$fileName"]);
} else {
    echo json_encode(["success" => false, "message" => "Server move failed"]);
}
?>
