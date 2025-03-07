<?php
// Database connection
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$database = "bakery_db";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employees
$sql = "SELECT * FROM Employee";
$result = $conn->query($sql);

// Display table
echo "<h2>Employee List</h2>";
echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Position</th>
            <th>Salary</th>
            <th>Date Hired</th>
            <th>Action</th>
        </tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["EmployeeID"] . "</td>
                <td>" . $row["FirstName"] . " " . $row["LastName"] . "</td>
                <td>" . $row["Position"] . "</td>
                <td>₱" . number_format($row["Salary"], 2) . "</td>
                <td>" . $row["DateHired"] . "</td>
                <td>
                    <button style='background-color: green; color: white;'>Edit</button>
                    <button style='background-color: red; color: white;'>Delete</button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No employees found.</td></tr>";
}

echo "</table>";

$conn->close();
?>
