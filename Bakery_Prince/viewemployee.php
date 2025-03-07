<?php
session_start();
include 'db.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Employees</title>
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
    <h2>Employee List</h2>
    
    <?php
    // Fetch employees from the database
    $sql = "SELECT * FROM employee ORDER BY EmployeeID ASC";
    $result = $conn->query($sql);
    ?>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Date Hired</th>
            <th>Action</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["EmployeeID"]); ?></td>
                    <td><?= htmlspecialchars($row["First_name"]) . " " . htmlspecialchars($row["Last_name"]); ?></td>
                    <td><?= htmlspecialchars($row["Position"]); ?></td>
                    <td><?= htmlspecialchars($row["Email"]); ?></td>
                    <td><?= htmlspecialchars($row["Phone_number"]); ?></td>
                    <td><?= htmlspecialchars($row["DateHired"]); ?></td>
                    <td>
                        <a href="editemployee.php?id=<?= $row["EmployeeID"]; ?>" class="edit-btn">Edit</a>
                        <a href="deleteemployee.php?id=<?= $row["EmployeeID"]; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan='7' style="text-align:center;">No employees found.</td></tr>
        <?php endif; ?>
    </table>
</div>

</body>
</html>
