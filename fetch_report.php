<?php
include 'db.php'; // Ensure database connection is included correctly.

// Fetch daily sales
$dailySalesQuery = $conn->prepare("
    SELECT SUM(total_sale_amount) AS daily_sales 
    FROM sales 
    WHERE DATE(sale_date) = CURDATE()
");
$dailySalesQuery->execute();
$dailySalesResult = $dailySalesQuery->fetch(PDO::FETCH_ASSOC);
$dailySales = $dailySalesResult['daily_sales'] ?? 0; // Default to 0 if NULL

// Fetch monthly sales
$monthlySalesQuery = $conn->prepare("
    SELECT SUM(total_sale_amount) AS monthly_sales 
    FROM sales 
    WHERE MONTH(sale_date) = MONTH(CURDATE()) 
      AND YEAR(sale_date) = YEAR(CURDATE())
");
$monthlySalesQuery->execute();
$monthlySalesResult = $monthlySalesQuery->fetch(PDO::FETCH_ASSOC);
$monthlySales = $monthlySalesResult['monthly_sales'] ?? 0; // Default to 0 if NULL

// Return response as JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'daily_sales' => number_format($dailySales, 2), // Format for clarity
    'monthly_sales' => number_format($monthlySales, 2)
]);
exit;
?>
