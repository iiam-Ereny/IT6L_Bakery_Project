<?php
session_start();
include 'db.php'; // Database connection

if (isset($_GET['id'])) {
    $productID = intval($_GET['id']);

    // Delete related order items first
    $deleteOrders = "DELETE FROM order_item WHERE ProductID = $productID";
    mysqli_query($conn, $deleteOrders);

    // Now delete the product
    $query = "DELETE FROM Product WHERE ProductID = $productID";
    
    if (mysqli_query($conn, $query)) {
        header("Location: products.php?msg=Product deleted successfully");
        exit();
    } else {
        echo "Error deleting product: " . mysqli_error($conn);
    }
}
?>
