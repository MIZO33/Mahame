<?php
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Record a sale and update stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that the data is correctly retrieved
    if (isset($_POST['product_name']) && isset($_POST['quantity_sold'])) {
        $product_name = $_POST['product_name'];
        $quantity_sold = $_POST['quantity_sold'];

        try {
            // Check if product exists by name
            $stmt = $conn->prepare("SELECT id, quantity FROM products WHERE name = :product_name");
            $stmt->bindParam(':product_name', $product_name);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                // If the product doesn't exist, return an error
                echo json_encode(['error' => 'Invalid product name']);
            } else {
                $product_id = $product['id'];  // Get product ID from result

                // Insert into sales table
                $stmt = $conn->prepare("INSERT INTO sales (product_id, quantity_sold) VALUES (:product_id, :quantity_sold)");
                $stmt->bindParam(':product_id', $product_id);
                $stmt->bindParam(':quantity_sold', $quantity_sold);

                if ($stmt->execute()) {
                    // Update product stock
                    $stmt = $conn->prepare("UPDATE products SET quantity = quantity - :quantity_sold WHERE id = :product_id");
                    $stmt->bindParam(':quantity_sold', $quantity_sold);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->execute();

                    // Check the new stock level
                    $new_quantity = $product['quantity'] - $quantity_sold;

                    // Return success message with stock alert
                    if ($new_quantity < 10) {
                        echo json_encode(['message' => 'Sale recorded. Warning: Low stock level']);
                    } else {
                        echo json_encode(['message' => 'Sale recorded']);
                    }
                } else {
                    echo json_encode(['error' => 'Failed to insert sale']);
                }
            }
        } catch (PDOException $e) {
            // Output any SQL errors
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Missing required fields']);
    }
}
?>
