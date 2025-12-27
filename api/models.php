<?php
header('Content-Type: application/json');
$models = glob('../assets/models/*.glb');
$uploads = glob('../uploads/*.glb');
echo json_encode(array_merge($models, $uploads));
?>
