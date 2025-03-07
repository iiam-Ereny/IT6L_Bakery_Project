<?php
session_start();
include 'db.php'; // Database connection

// Handle order deletion
if (isset($_GET['delete'])) {
    $orderID = $_GET['delete'];

    // Delete order items first to maintain foreign key integrity
    $deleteOrderItemsQuery = "DELETE FROM Order_Item WHERE OrderID = ?";
    $stmt = $conn->prepare($deleteOrderItemsQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    // Delete the order itself
    $deleteOrderQuery = "DELETE FROM `Order` WHERE OrderID = ?";
    $stmt = $conn->prepare($deleteOrderQuery);
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->close();

    header("Location: showorder.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Summary</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<div class="sidebar">
    <h2>POINT OF SALE SYSTEM</h2>
    <ul>
        <li><a href="main.php">Dashboard</a></li>
        <li><a href="#">Orders</a>
            <ul>
                <li><a href="addorder.php">Add Order</a></li>
                <li><a href="showorder.php">Show Order</a></li>
            </ul>
        </li>
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="#">Categories</a>
            <ul>
                <li><a href="addcategory.php">Add Category</a></li>
                <li><a href="viewcategory.php">View Categories</a></li>
            </ul>
        </li>
        <li><a href="#">Employees</a>
            <ul>
                <li><a href="addemployee.php">Add Employee</a></li>
                <li><a href="viewemployee.php">View Employees</a></li>
            </ul>
        </li>
        <li><a href="yield.php">Yield</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Order Summary</h1>

    <?php
    // Fetch all orders with their products
    $query = "
        SELECT o.OrderID, o.Order_Date, o.Payment_Method, o.Amount,
               GROUP_CONCAT(CONCAT(p.Product_Name, ' (', oi.Quantity, ')') SEPARATOR ', ') AS Products
        FROM `Order` o
        JOIN Order_Item oi ON o.OrderID = oi.OrderID
        JOIN Product p ON oi.ProductID = p.ProductID
        GROUP BY o.OrderID
        ORDER BY o.Order_Date DESC";
    
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error in query: " . mysqli_error($conn));  // Show SQL error for debugging
    }
    ?>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Products</th>
            <th>Order Date</th>
            <th>Payment Method</th>
            <th>Total Amount</th>
            <th>Action</th> <!-- New Column for Delete Button -->
        </tr>

        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['OrderID']}</td>
                    <td>{$row['Products']}</td>
                    <td>{$row['Order_Date']}</td>
                    <td>{$row['Payment_Method']}</td>
                    <td>₱" . number_format($row['Amount'], 2) . "</td>
                    <td>
                        <a href='showorder.php?delete={$row['OrderID']}' class='remove-btn' onclick='return confirm(\"Are you sure you want to delete this order?\");'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
