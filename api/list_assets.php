<?php
header('Content-Type: application/json');
$dir = "../uploads/images/";
$result = [];

if (is_dir($dir)) {
    $files = array_diff(scandir($dir), array('..', '.'));
    foreach ($files as $file) {
        // Only include actual image files
        if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $result[] = ['path' => 'uploads/images/' . $file];
        }
    }
}
echo json_encode($result);
?>
