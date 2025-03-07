<?php
$servername = "localhost"; // Change this if your database is hosted elsewhere
$username = "root"; // Your MySQL username (default in XAMPP is "root")
$password = ""; // Your MySQL password (default in XAMPP is empty)
$dbname = "bakery_db"; // Your database name

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
