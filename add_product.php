<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve product data from the POST request
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    try {
        // Insert product into the database
        $stmt = $conn->prepare("INSERT INTO products (name, unit_price, quantity) VALUES (:name, :price, :quantity)");
        $stmt->bindParam(':name', $product_name);
        $stmt->bindParam(':price', $product_price);
        $stmt->bindParam(':quantity', $product_quantity);
        $stmt->execute();

        // Send a success response
        echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    } catch (PDOException $e) {
        // Handle any errors during the insert
        echo json_encode(['success' => false, 'message' => 'Error adding product: ' . $e->getMessage()]);
    }
}
?>
