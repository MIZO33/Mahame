<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Set JSON content type

try {
    // Fetch products from the database
    $stmt = $conn->prepare("SELECT name, quantity, unit_price FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode(['success' => true, 'data' => $products]);
} catch (PDOException $e) {
    // Handle errors and send a failure response
    echo json_encode(['success' => false, 'message' => 'Error fetching products: ' . $e->getMessage()]);
}
?>
