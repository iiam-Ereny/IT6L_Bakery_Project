<?php
session_start();
include 'db.php'; // Database connection

error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Handle form submission to add a new inventory item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_inventory'])) {
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);

    $query = "INSERT INTO inventory (Item_Name, Category, Price, Stock_Quantity) 
              VALUES ('$item_name', '$category', '$price', '$quantity')";

    if (mysqli_query($conn, $query)) {
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle form submission to update inventory item
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_inventory'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);

    $query = "UPDATE inventory SET 
              Item_Name = '$item_name', 
              Category = '$category', 
              Price = '$price', 
              Stock_Quantity = '$quantity' 
              WHERE ID = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Handle deletion of an inventory item
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    
    $delete_query = "DELETE FROM inventory WHERE ID = $delete_id";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: inventory.php");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Inventory</title>
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
    <h1>Stock Inventory</h1>
    <button onclick="openModal()" class="add-btn">Add Inventory</button>
    <table>
        <tr>
            <th>ID</th>
            <th>Item Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock Quantity</th>
            <th>Stock Status</th>
            <th>Actions</th>
        </tr>

        <?php
        $query = "SELECT * FROM inventory";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['ID']}</td>
                    <td>{$row['Item_Name']}</td>
                    <td>{$row['Category']}</td>
                    <td>₱{$row['Price']}</td>
                    <td>{$row['Stock_Quantity']}</td>
                    <td>" . ($row['Stock_Quantity'] > 0 ? "<span class='stock-status in-stock'>IN STOCK</span>" : "<span class='stock-status out-of-stock'>OUT OF STOCK</span>") . "</td>
                    <td>
                        <a href='#' class='edit-btn' onclick='openEditModal({$row['ID']}, \"{$row['Item_Name']}\", \"{$row['Category']}\", {$row['Price']}, {$row['Stock_Quantity']})'>Edit</a>
                        <a href='inventory.php?delete_id=" . urlencode($row['ID']) . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>
                        </td>
                  </tr>";
        }
        ?>
    </table>
</div>

<!-- Add Inventory Modal -->
<div id="inventoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span onclick="closeModal()" class="close" style="cursor: pointer;">&times;</span>
        <h2>Add Inventory</h2>
        <form action="inventory.php" method="POST">
            <input type="hidden" name="add_inventory" value="1">
            <label>Item Name</label>
            <input type="text" name="item_name" required>

            <label>Category</label>
            <input type="text" name="category" required>

            <label>Price</label>
            <input type="number" step="0.01" name="price" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" required>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- Edit Inventory Modal -->
<div id="editInventoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span onclick="closeEditModal()" class="close" style="cursor: pointer;">&times;</span>
        <h2>Edit Inventory</h2>
        <form action="inventory.php" method="POST">
            <input type="hidden" name="edit_inventory" value="1">
            <input type="hidden" id="edit_id" name="id">
            <label>Item Name</label>
            <input type="text" id="edit_item_name" name="item_name" required>
            <label>Category</label>
            <input type="text" id="edit_category" name="category" required>
            <label>Price</label>
            <input type="number" step="0.01" id="edit_price" name="price" required>
            <label>Stock Quantity</label>
            <input type="number" id="edit_stock_quantity" name="stock_quantity" required>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('inventoryModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('inventoryModal').style.display = 'none';
    }

    function openEditModal(id, itemName, category, price, stockQuantity) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_item_name').value = itemName;
        document.getElementById('edit_category').value = category;
        document.getElementById('edit_price').value = price;
        document.getElementById('edit_stock_quantity').value = stockQuantity;
        document.getElementById('editInventoryModal').style.display = 'block';
    }

    function closeEditModal() {
    document.getElementById('editInventoryModal').style.display = 'none';
}
</script>
</body>
</html>
