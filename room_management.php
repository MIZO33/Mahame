<?php
include 'db.php';

// Fetch all rooms
$stmt = $conn->prepare("SELECT * FROM rooms");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add room if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_number'])) {
    $roomNumber = $_POST['room_number'];
    $insertRoom = $conn->prepare("INSERT INTO rooms (room_number, status) VALUES (:room_number, 'vacant')");
    $insertRoom->bindParam(':room_number', $roomNumber);
    $insertRoom->execute();
    header("Location: room_management.php"); // Refresh page
    exit;
}

// Update room status and price
if (isset($_POST['room_id'], $_POST['status'])) {
    $roomId = $_POST['room_id'];
    $status = $_POST['status'];
    $price = ($status === 'booked' && isset($_POST['price']) && $_POST['price'] > 0) ? $_POST['price'] : null;

    // Update room status and price in the database
    $updateRoom = $conn->prepare("UPDATE rooms SET status = :status, price = :price WHERE id = :id");
    $updateRoom->bindParam(':status', $status);
    $updateRoom->bindParam(':price', $price, PDO::PARAM_INT);
    $updateRoom->bindParam(':id', $roomId);
    $updateRoom->execute();

    // If room is booked and price is entered, update the daily sales report
    if ($status === 'booked' && $price > 0) {
        $today = date('Y-m-d');
        
        // Check if today's sales entry exists
        $checkStmt = $conn->prepare("SELECT * FROM daily_sales_report WHERE report_date = :today");
        $checkStmt->bindParam(':today', $today);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() == 0) {
            // Insert new entry if it doesn't exist
            $insertSale = $conn->prepare("INSERT INTO daily_sales_report (report_date, total_sales) VALUES (:today, :price)");
            $insertSale->bindParam(':today', $today);
            $insertSale->bindParam(':price', $price);
            $insertSale->execute();
        } else {
            // Update existing entry by adding the price
            $updateSale = $conn->prepare("UPDATE daily_sales_report SET total_sales = total_sales + :price WHERE report_date = :today");
            $updateSale->bindParam(':price', $price);
            $updateSale->bindParam(':today', $today);
            $updateSale->execute();
        }
    }
    header("Location: room_management.php");
    exit;
}

// Delete room if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_room_id'])) {
    $deleteRoomId = $_POST['delete_room_id'];
    $deleteRoom = $conn->prepare("DELETE FROM rooms WHERE id = :id");
    $deleteRoom->bindParam(':id', $deleteRoomId);
    $deleteRoom->execute();
    header("Location: room_management.php"); // Refresh page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Room Management</title>
    <link rel="stylesheet" href="style/room.css">
    <script>
        // Enable or disable the price field based on room status
        function togglePriceInput(selectElement, priceInput) {
            if (selectElement.value === 'booked') {
                priceInput.disabled = false;
            } else {
                priceInput.disabled = true;
                priceInput.value = ''; // Clear price when status is vacant
            }
        }
    </script>
</head>
<body>

    <!-- Form to Add New Room -->
    <form method="POST">
        <label for="room_number">Room Number:</label>
        <input type="text" id="room_number" name="room_number" required>
        <button type="submit">Add Room</button>
    </form>

    <!-- List of Rooms -->
    <h2>Available Rooms</h2>
    <table>
        <thead>
            <tr>
                <th>Room Number</th>
                <th>Status</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_number']) ?></td>
                    <td><?= htmlspecialchars($room['status']) ?></td>
                    <td><?= htmlspecialchars($room['status'] === 'booked' ? $room['price'] : '') ?></td>
                    <td>
                        <!-- Form to Update Room Status and Price -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                            <select name="status" onchange="togglePriceInput(this, this.nextElementSibling)">
                                <option value="vacant" <?= $room['status'] === 'vacant' ? 'selected' : '' ?>>Vacant</option>
                                <option value="booked" <?= $room['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                            </select>
                            <input type="number" name="price" placeholder="Price" value="<?= htmlspecialchars($room['status'] === 'booked' ? $room['price'] : '') ?>" min="0" <?= $room['status'] === 'vacant' ? 'disabled' : '' ?>>
                            <button type="submit">Save</button>
                        </form>

                        <!-- Form to Delete Room -->
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this room?');">
                            <input type="hidden" name="delete_room_id" value="<?= $room['id'] ?>">
                            <button type="submit" style="color:red;">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
