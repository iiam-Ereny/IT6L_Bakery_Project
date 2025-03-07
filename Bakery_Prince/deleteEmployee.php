<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: viewemployee.php");
    exit;
}

$id = $_GET['id'];

// Delete the employee record
$sql = "DELETE FROM Employee WHERE EmployeeID = $id";
if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Employee deleted successfully!'); window.location.href='viewemployee.php';</script>";
} else {
    echo "Error deleting employee: " . $conn->error;
}

$conn->close();
?>
