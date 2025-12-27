<?php
$conn = mysqli_connect('localhost', 'admin', 'admin123', 'godsrods_db');
if (!$conn) { die(json_encode(['success' => false, 'error' => 'DB Connection Failed'])); }
?>
