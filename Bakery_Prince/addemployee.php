<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // Sanitize user input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $position = trim($_POST['position']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $date_hired = $_POST['date_hired'];

    // Prepare SQL statement to prevent SQL injection
    $sql = "INSERT INTO employee (First_name, Last_name, Position, Email, Phone_number, DateHired) 
            VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssss", $first_name, $last_name, $position, $email, $phone_number, $date_hired);
        if ($stmt->execute()) {
            header("Location: viewemployee.php?msg=Employee added successfully");
            exit();
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error preparing statement: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employee</title>
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
    <h1>Add Employee</h1>
    
    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="first_name" required>

        <label>Last Name:</label>
        <input type="text" name="last_name" required>

        <label>Position:</label>
        <input type="text" name="position" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Phone Number:</label>
        <input type="text" name="phone_number" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX" maxlength="11" required>

        <label>Date Hired:</label>
        <input type="date" name="date_hired" required>
        
        <button type="submit" name="add">Add Employee</button>
    </form>
</div>
</body>
</html>
