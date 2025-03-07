<?php
session_start();
include 'db.php'; // Database connection

// Handle Adding or Updating Yield
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $date_produced = $_POST['date_produced'];
    $description = $_POST['description'];
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    if (!empty($id)) {
        // Update existing record
        $query = "UPDATE yields SET product_name=?, quantity=?, date_produced=?, description=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sissi", $product_name, $quantity, $date_produced, $description, $id);
    } else {
        // Insert new record
        $query = "INSERT INTO yields (product_name, quantity, date_produced, description) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "siss", $product_name, $quantity, $date_produced, $description);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: yield.php");
    exit();
}

// Handle Deleting Yield
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM yields WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: yield.php");
    exit();
}

// Fetch yield records
$query = "SELECT * FROM yields ORDER BY date_produced DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yield Management</title>
    <link rel="stylesheet" href="main.css">
    <style>
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px gray; }
        .modal-content { padding: 20px; }
        .close { float: right; font-size: 20px; cursor: pointer; }
        .edit-btn, .delete-btn { padding: 8px 12px; font-size: 14px; border: none; cursor: pointer; border-radius: 4px; text-align: center; }
        .edit-btn { background-color: #4CAF50; color: white; }
        .delete-btn { background-color: #f44336; color: white; }
        .edit-btn:hover, .delete-btn:hover { opacity: 0.8; }
    </style>
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
    <h1>Yield Management</h1>
    
    <button onclick="openModal()">+ Add Yield</button>

    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Date Produced</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= htmlspecialchars($row['quantity']); ?></td>
                <td><?= htmlspecialchars($row['date_produced']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td>
                    <button class="edit-btn" onclick="editYield(<?= $row['id'] ?>, '<?= htmlspecialchars($row['product_name'], ENT_QUOTES) ?>', <?= $row['quantity'] ?>, '<?= $row['date_produced'] ?>', '<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>')">Edit</button>
                    <button class="delete-btn" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- Modal Form -->
<div id="yieldModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Yield</h2>
        <form method="POST" action="yield.php">
            <input type="hidden" name="id" id="yieldId">
            <label>Product Name:</label>
            <input type="text" name="product_name" id="product_name" required>
            <label>Quantity:</label>
            <input type="number" name="quantity" id="quantity" required>
            <label>Date Produced:</label>
            <input type="date" name="date_produced" id="date_produced" required>
            <label>Description:</label>
            <textarea name="description" id="description" rows="3" required></textarea>
            <button type="submit" id="saveButton">Save</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').innerText = "Add Yield";
        document.getElementById('saveButton').innerText = "Save";
        document.getElementById('yieldId').value = "";
        document.getElementById('product_name').value = "";
        document.getElementById('quantity').value = "";
        document.getElementById('date_produced').value = "";
        document.getElementById('description').value = "";
        document.getElementById('yieldModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('yieldModal').style.display = 'none';
    }

    function editYield(id, product_name, quantity, date_produced, description) {
        document.getElementById('yieldId').value = id;
        document.getElementById('product_name').value = product_name;
        document.getElementById('quantity').value = quantity;
        document.getElementById('date_produced').value = date_produced;
        document.getElementById('description').value = description;
        document.getElementById('modalTitle').innerText = "Edit Yield";
        document.getElementById('saveButton').innerText = "Update";
        document.getElementById('yieldModal').style.display = 'block';
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this yield?")) {
            window.location.href = "yield.php?delete_id=" + id;
        }
    }
</script>

</body>
</html>