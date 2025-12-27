<?php
header('Content-Type: application/json');
$targetDir = "../uploads/";
if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

if ($_FILES["file"]) {
    $fileName = time() . "_" . basename($_FILES["file"]["name"]);
    $targetPath = $targetDir . $fileName;
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetPath)) {
        echo json_encode(["success" => true, "filePath" => "uploads/" . $fileName]);
        exit;
    }
}
echo json_encode(["success" => false, "message" => "Upload failed"]);
