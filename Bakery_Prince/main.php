<?php
session_start();
include 'db.php'; // Database connection
// Fetch total sales
$query = "SELECT SUM(Amount) AS total_sales FROM `Order`";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalSales = $row['total_sales'] ?? 0; // Default to 0 if no sales yet

// Fetch total customers (each order represents a customer)
$query = "SELECT COUNT(*) AS total_customers FROM `Order`";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalCustomers = $row['total_customers'] ?? 0; // Each order = one customer
// Fetch total products sold (sum of all quantities in `Order_Item`)
$query = "SELECT SUM(Quantity) AS total_orders FROM `Order_Item`";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalOrders = $row['total_orders'] ?? 0; // Total products sold


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <title>Dashboard</title>
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
  <h1>Dashboard</h1>
  <div class="dashboard-cards">
    <div class="card">
      <h3>Total Sales</h3>
      <p style="color: black; font-weight: bold;">₱<?= number_format($totalSales, 2); ?></p>
    </div>
    <div class="card">
      <h3>Total Customers</h3>
      <p style="color: black; font-weight: bold;"><?= number_format($totalCustomers); ?></p>
    </div>
    <div class="card">
      <h3>Total Orders</h3>
      <p style="color: black; font-weight: bold;"><?= number_format($totalOrders); ?></p> <!-- Now shows total products sold -->
    </div>
  </div>
</div>

<div class="logout-container">
    <button class="logout-btn" onclick="logout()">Log-Out <i class="fa fa-sign-out"></i></button>
</div>

<script>
    function logout() {
        window.location.href = "login.php";
    }
</script>



</body>
</html>

