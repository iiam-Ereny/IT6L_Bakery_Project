<?php
session_start();
include 'db.php'; // Database connection

// Handle form submission (insert product)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock_quantity = mysqli_real_escape_string($conn, $_POST['stock_quantity']);

    $query = "INSERT INTO Product (Product_Name, CategoryID, Price, Stock_quantity) 
              VALUES ('$name', '$category', '$price', '$stock_quantity')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
    exit();
}

// Fetch products with category names
$query = "SELECT p.ProductID, p.Product_Name, c.CategoryName, p.Price, p.Stock_quantity 
          FROM Product p
          JOIN categories c ON p.CategoryID = c.CategoryID
          ORDER BY p.ProductID ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<button onclick="openModal()" class="add-btn" style="position: absolute; top: 10px; right: 10px;">Add Product</button>

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
    <h1>Product List</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock Quantity</th> 
                <th>Action</th> 
            </tr>
        </thead>
        <tbody id="productTable">
            <?php $counter = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $counter++; ?></td>
                <td><?php echo htmlspecialchars($row['Product_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                <td>₱<?php echo number_format($row['Price'], 2); ?></td>
                <td><?php echo htmlspecialchars($row['Stock_quantity']); ?></td>
                <td>
                    <a href="editproduct.php?id=<?php echo $row['ProductID']; ?>" class="edit-btn">Edit</a>
                    <a href="deleteproduct.php?id=<?php echo $row['ProductID']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal for Adding Product -->
<div id="productModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span onclick="closeModal()" class="close">&times;</span>
        <h2>Add Product</h2>
        <form id="addProductForm">
            <label>Name of Product</label>
            <input type="text" name="name" id="name" required>

            <label>Category</label>
            <select name="category" id="category" required>
                <option value="">Select Category</option>
                <?php
                $categoryQuery = "SELECT * FROM categories";
                $categoryResult = mysqli_query($conn, $categoryQuery);
                while ($row = mysqli_fetch_assoc($categoryResult)) { ?>
                    <option value="<?php echo $row['CategoryID']; ?>">
                        <?php echo $row['CategoryName']; ?>
                    </option>
                <?php } ?>
            </select>

            <label>Price</label>
            <input type="number" step="0.01" name="price" id="price" required>

            <label>Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" required>

            <button type="submit">Save</button>
        </form>
    </div>
</div>

<!-- Modal for Editing Product -->
<div id="editProductModal" class="modal" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); width: 400px;">
    <div class="modal-content" style="text-align: left;">
        <h2 style="margin-bottom: 10px;">Edit Product</h2>
        <form id="editProductForm">
            <input type="hidden" name="edit_id" id="edit_id">
            
            <label style="font-weight: bold;">Product Name:</label>
            <input type="text" name="edit_name" id="edit_name" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            
            <label style="font-weight: bold;">Category:</label>
            <select name="edit_category" id="edit_category" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="">Select Category</option>
                <?php
                $categoryQuery = "SELECT * FROM categories";
                $categoryResult = mysqli_query($conn, $categoryQuery);
                while ($row = mysqli_fetch_assoc($categoryResult)) { ?>
                    <option value="<?php echo $row['CategoryID']; ?>">
                        <?php echo $row['CategoryName']; ?>
                    </option>
                <?php } ?>
            </select>
            
            <label style="font-weight: bold;">Price:</label>
            <input type="number" step="0.01" name="edit_price" id="edit_price" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            
            <label style="font-weight: bold;">Stock Quantity:</label>
            <input type="number" name="edit_stock_quantity" id="edit_stock_quantity" required style="width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
            
            <button type="submit" style="width: 100%; padding: 10px; background: black; color: white; border: none; border-radius: 5px; font-size: 16px;">Update Product</button>
        </form>
    </div>
</div>


<script>
    function openModal() {
        document.getElementById('productModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('productModal').style.display = 'none';
    }

    document.getElementById("addProductForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        fetch("products.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert("Product added successfully!");
                location.reload(); // Refresh the page to update the product list
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });

    window.onclick = function(event) {
        var modal = document.getElementById('productModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

</script>

</body>
</html>
