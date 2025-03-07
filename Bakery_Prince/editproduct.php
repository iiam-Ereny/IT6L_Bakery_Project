<?php
session_start();
include 'db.php'; // Database connection

$product = null;
$categories = []; // Store category list

// Ensure 'id' is set in the URL and is a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productID = intval($_GET['id']);

    // Fetch product details
    $stmt = $conn->prepare("
        SELECT p.ProductID, p.Product_Name, p.CategoryID, p.Price, p.Stock_quantity 
        FROM Product p WHERE p.ProductID = ?
    ");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Error: Product not found.");
    }
    $stmt->close();
} else {
    die("Error: Invalid product ID.");
}

// Fetch all categories for the dropdown
$stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM categories ORDER BY CategoryName ASC");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['ProductID'], $_POST['Product_Name'], $_POST['CategoryID'], $_POST['Price'], $_POST['Stock_quantity'])) {
        die("Error: Missing required fields.");
    }

    $productID = intval($_POST['ProductID']);
    $productName = trim($_POST['Product_Name']);
    $categoryID = intval($_POST['CategoryID']);
    $price = floatval($_POST['Price']);
    $stockQuantity = intval($_POST['Stock_quantity']);

    // Validate inputs
    if ($price < 0 || $stockQuantity < 0) {
        die("Error: Price and Stock Quantity must be positive numbers.");
    }

    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE Product SET Product_Name = ?, CategoryID = ?, Price = ?, Stock_quantity = ? WHERE ProductID = ?");
    $stmt->bind_param("siddi", $productName, $categoryID, $price, $stockQuantity, $productID);

    if ($stmt->execute()) {
        header("Location: products.php?msg=Product updated successfully");
        exit();
    } else {
        die("Update failed: " . $stmt->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function validateForm() {
            var price = document.getElementById('Price').value;
            var stockQuantity = document.getElementById('Stock_quantity').value;
            if (isNaN(price) || isNaN(stockQuantity) || price < 0 || stockQuantity < 0) {
                alert('Price and Stock Quantity must be positive numbers.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="main-content">
    <h1>Edit Product</h1>
    <form action="editproduct.php?id=<?php echo htmlspecialchars($productID); ?>" method="post" onsubmit="return validateForm()">
        <input type="hidden" name="ProductID" value="<?php echo htmlspecialchars($product['ProductID'] ?? ''); ?>">
        
        <label for="Product_Name">Product Name:</label>
        <input type="text" id="Product_Name" name="Product_Name" value="<?php echo htmlspecialchars($product['Product_Name'] ?? ''); ?>" required>
        
        <label for="CategoryID">Category:</label>
        <select id="CategoryID" name="CategoryID" required>
            <?php foreach ($categories as $category) : ?>
                <option value="<?php echo $category['CategoryID']; ?>" 
                    <?php echo ($category['CategoryID'] == $product['CategoryID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['CategoryName']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="Price">Price:</label>
        <input type="number" id="Price" name="Price" step="0.01" value="<?php echo htmlspecialchars($product['Price'] ?? ''); ?>" required>
        
        <label for="Stock_quantity">Stock Quantity:</label>
        <input type="number" id="Stock_quantity" name="Stock_quantity" value="<?php echo htmlspecialchars($product['Stock_quantity'] ?? ''); ?>" required>
        
        <button type="submit">Update Product</button>
    </form>
</div>
</body>
</html>
