<?php
session_start();
include 'db.php'; // Database connection

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $deleteQuery = "DELETE FROM yields WHERE YieldID = $id";

    if (mysqli_query($conn, $deleteQuery)) {
        header("Location: yield.php");
        exit();
    } else {
        echo "Error deleting yield: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
