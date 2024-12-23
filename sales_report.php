<?php
include 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json'); // Set JSON content type

try {
    // Get today's date
    $today = date('Y-m-d');

    // Calculate today's total sales
    $stmt = $conn->prepare("
        SELECT SUM(quantity_sold * p.unit_price) AS daily_sales
        FROM sales s
        JOIN products p ON s.product_id = p.id
        WHERE DATE(s.sale_date) = :today
    ");
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
    $daily_sales = $report['daily_sales'] ?? 0;

    // Check if today's sales have already been recorded
    $checkStmt = $conn->prepare("SELECT * FROM daily_sales_report WHERE report_date = :today");
    $checkStmt->bindParam(':today', $today);
    $checkStmt->execute();

    if ($checkStmt->rowCount() == 0) {
        // Insert today's sales into the daily_sales_report table
        $insertStmt = $conn->prepare("INSERT INTO daily_sales_report (report_date, total_sales) VALUES (:today, :total_sales)");
        $insertStmt->bindParam(':today', $today);
        $insertStmt->bindParam(':total_sales', $daily_sales);
        $insertStmt->execute();
    }

    // Calculate the total sales for the current month
    $current_month = date('Y-m');
    $monthStmt = $conn->prepare("
        SELECT SUM(total_sales) AS monthly_sales
        FROM daily_sales_report
        WHERE DATE_FORMAT(report_date, '%Y-%m') = :current_month
    ");
    $monthStmt->bindParam(':current_month', $current_month);
    $monthStmt->execute();
    $monthlyReport = $monthStmt->fetch(PDO::FETCH_ASSOC);
    $monthly_sales = $monthlyReport['monthly_sales'] ?? 0;

    // Send the data as JSON
    echo json_encode([
        'success' => true,
        'daily_sales' => $daily_sales,
        'monthly_sales' => $monthly_sales
    ]);
} catch (PDOException $e) {
    // Handle errors
    echo json_encode([
        'success' => false,
        'message' => 'Error generating report: ' . $e->getMessage()
    ]);
}
?>
