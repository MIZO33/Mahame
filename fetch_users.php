<?php
header('Content-Type: application/json');
$mysqli = new mysqli('localhost', 'root', '', 'inventory_system');

if ($mysqli->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$result = $mysqli->query("SELECT id, username, role FROM users");
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch users']);
}

$mysqli->close();
?>
