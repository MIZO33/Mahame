<?php
include 'db.php';

// Get all products
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->prepare("SELECT * FROM products");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
}

// Add a new product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $name = $data['name'];
    $quantity = $data['quantity'];
    $unit_price = $data['unit_price'];

    $stmt = $conn->prepare("INSERT INTO products (name, quantity, unit_price) VALUES (:name, :quantity, :unit_price)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':unit_price', $unit_price);
    $stmt->execute();
    
    echo json_encode(['message' => 'Product added successfully']);
}

// Update product stock
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $quantity = $data['quantity'];

    $stmt = $conn->prepare("UPDATE products SET quantity = :quantity WHERE id = :id");
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    echo json_encode(['message' => 'Product updated successfully']);
}

// Delete a product
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo json_encode(['message' => 'Product deleted successfully']);
}
?>
