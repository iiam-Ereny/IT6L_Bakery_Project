<?php
session_start();
include 'db.php'; // Database connection

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoryName = mysqli_real_escape_string($conn, $_POST['category_name']);

    // Insert only CategoryName (without Description)
    $query = "INSERT INTO categories (CategoryName) VALUES ('$categoryName')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Category added successfully!'); window.location.href='viewcategory.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
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
        <h1>Add Category</h1>
        <form action="" method="POST">
            <label>Category Name</label>
            <input type="text" name="category_name" required>
            <button type="submit">Save</button>
        </form>
    </div>
</body>
</html>
