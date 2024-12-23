<?php
header('Content-Type: application/json');
$mysqli = new mysqli('localhost', 'root', '', 'inventory_system');

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$username = $mysqli->real_escape_string($data['username']);
$password = password_hash($data['password'], PASSWORD_BCRYPT);
$role = $mysqli->real_escape_string($data['role']);

if ($mysqli->query("INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')")) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add user']);
}

$mysqli->close();
?>
