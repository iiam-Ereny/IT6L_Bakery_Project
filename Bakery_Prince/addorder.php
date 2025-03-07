
<?php
session_start();
include 'db.php'; // Database connection// Order_Itemc db

if (!isset($_SESSION['order_items'])) {
    $_SESSION['order_items'] = [];
}
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    unset($_SESSION['order_items'][$index]);
    $_SESSION['order_items'] = array_values($_SESSION['order_items']); // Reindex
    header("Location: addorder.php");
    exit();
}

if (isset($_POST['add_item'])) {
    $productID = $_POST['product'];
    $quantity = $_POST['quantity'];

    $query = "SELECT ProductID, Product_Name, Price, Stock_quantity, c.CategoryName 
              FROM Product p 
              JOIN Categories c ON p.CategoryID = c.CategoryID 
              WHERE ProductID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if (!$product) {
        $error = "Product not found.";
    } elseif ($quantity > $product['Stock_quantity']) {
        $error = "Quantity exceeds available stock.";
    } else {
        $_SESSION['order_items'][] = [
            'id' => $product['ProductID'],
            'name' => $product['Product_Name'],
            'category' => $product['CategoryName'],
            'price' => $product['Price'],
            'quantity' => $quantity
        ];
        header("Location: addorder.php");
        exit();
    }
}

// Handle placing order
if (isset($_POST['place_order']) && !empty($_SESSION['order_items'])) {
    $paymentMethod = $_POST['payment'];
    $orderDate = date('Y-m-d');
    $totalAmount = array_reduce($_SESSION['order_items'], function($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    // Insert into Order table
    $query = "INSERT INTO `Order` (Order_Date, Payment_Method, Amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssd", $orderDate, $paymentMethod, $totalAmount);
    $stmt->execute();
    $orderID = $stmt->insert_id;
    $stmt->close();

    // Insert into Order_Item table and update stock
    foreach ($_SESSION['order_items'] as $item) {
        $query = "INSERT INTO Order_Item (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiid", $orderID, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();

        // Update product stock
        $updateStockQuery = "UPDATE Product SET Stock_quantity = Stock_quantity - ? WHERE ProductID = ?";
        $stmt = $conn->prepare($updateStockQuery);
        $stmt->bind_param("ii", $item['quantity'], $item['id']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear session and redirect
    unset($_SESSION['order_items']);
    header("Location: showorder.php");
    exit();
}

// Fetch available products
$query = "SELECT ProductID, Product_Name, c.CategoryName FROM Product p 
          JOIN Categories c ON p.CategoryID = c.CategoryID 
          WHERE Stock_quantity > 0";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
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
    <h1>Create Order</h1>
    <div class="create-order">
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <form action="addorder.php" method="POST">
            <label for="product">Product:</label>
            <select name="product" id="product" required>
                <option value="">Select Product</option>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <option value="<?= $row['ProductID']; ?>">
                        <?= htmlspecialchars($row['Product_Name']); ?> (<?= htmlspecialchars($row['CategoryName']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
            
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" required>
            
            <button type="submit" name="add_item" class="add-item">Add Item</button>
        </form>
    </div>

    <h2>Products in Order</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Amount</th>
            <th>Action</th>
        </tr>
        <?php if (!empty($_SESSION['order_items'])): ?>
            <?php foreach ($_SESSION['order_items'] as $index => $item): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($item['name']); ?></td>
                    <td><?= htmlspecialchars($item['category']); ?></td>
                    <td>₱<?= number_format($item['price'], 2); ?></td>
                    <td><?= $item['quantity']; ?></td>
                    <td>₱<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td><a href="addorder.php?remove=<?= $index; ?>" class="remove-btn">Remove</a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center;">No items added yet.</td></tr>
        <?php endif; ?>
    </table>

    <div class="payment-section">
        <form action="addorder.php" method="POST">
            <label for="payment">Payment Method:</label>
            <select name="payment" id="payment" required>
                <option value="cash">Cash</option>
                <option value="gcash">Gcash</option>
            </select>
            
            <button type="submit" name="place_order" class="place-order">Place Order</button>
        </form>
    </div>
</div>
</body>
</html>
