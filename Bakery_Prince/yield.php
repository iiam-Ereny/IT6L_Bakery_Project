<?php
session_start();
include 'db.php'; // Database connection

// Handle Adding or Updating Yield
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; 
    $product_name = trim($_POST['product_name']);
    $quantity = intval($_POST['quantity']);
    $date_produced = $_POST['date_produced'];
    $description = trim($_POST['description']);
    $baker = intval($_POST['baker']);

    if ($id > 0) {
        // Update existing record
        $query = "UPDATE yields SET product_name=?, quantity=?, date_produced=?, description=?, baker=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sissii", $product_name, $quantity, $date_produced, $description, $baker, $id);
    } else {
        // Insert new record
        $query = "INSERT INTO yields (product_name, quantity, date_produced, description, baker) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sissi", $product_name, $quantity, $date_produced, $description, $baker);
    }

    if ($stmt && mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: yield.php");
        exit();
    } else {
        die("Database Error: " . mysqli_error($conn));
    }
}

// Handle Deleting Yield
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $query = "DELETE FROM yields WHERE id=?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: yield.php");
    exit();
}

// Fetch yield records
$query = "SELECT y.*, e.First_name, e.Last_name, e.EmployeeID 
          FROM yields y 
          LEFT JOIN employee e ON y.baker = e.EmployeeID 
          ORDER BY y.date_produced DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error fetching yields: " . mysqli_error($conn));
}

// Fetch bakers for the dropdown
$baker_query = "SELECT EmployeeID, First_name, Last_name FROM employee WHERE position = 'Baker'";
$baker_result = mysqli_query($conn, $baker_query);
if (!$baker_result) {
    die("Error fetching bakers: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yield Management</title>
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
    <h1>Yield Management</h1>
    
    <button onclick="openModal()">Add Yield</button>

    <table border="1">
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Date Produced</th>
            <th>Description</th>
            <th>Baker</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= htmlspecialchars($row['quantity']); ?></td>
                <td><?= htmlspecialchars($row['date_produced']); ?></td>
                <td><?= htmlspecialchars($row['description']); ?></td>
                <td><?= htmlspecialchars($row['First_name'] . " " . $row['Last_name']); ?></td>
                <td>
                    <button class="edit-btn" onclick="editYield(
                        <?= $row['id'] ?>, 
                        '<?= htmlspecialchars($row['product_name'], ENT_QUOTES) ?>', 
                        <?= $row['quantity'] ?>, 
                        '<?= $row['date_produced'] ?>', 
                        '<?= htmlspecialchars($row['description'], ENT_QUOTES) ?>',
                        <?= intval($row['baker']) ?>
                    )">Edit</button>

                    <button class="delete-btn   " onclick="confirmDelete(<?= $row['id'] ?>)">Delete</button>
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
            <label>Baker:</label>
            <select name="baker" id="baker" required>
                <option value="">Select a baker</option>
                <?php while ($baker = mysqli_fetch_assoc($baker_result)) { ?>
                    <option value="<?= $baker['EmployeeID'] ?>">
                        <?= htmlspecialchars($baker['First_name'] . " " . $baker['Last_name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit" id="saveButton">Save</button>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Ensure modal is hidden when page loads
    document.getElementById('yieldModal').style.display = 'none';
});


function openModal() {
    document.getElementById('modalTitle').innerText = "Add Yield";
    document.getElementById('saveButton').innerText = "Save"; // Ensure it always says "Save"
    
    // Reset input fields
    document.getElementById('yieldId').value = "";
    document.getElementById('product_name').value = "";
    document.getElementById('quantity').value = "";
    document.getElementById('date_produced').value = "";
    document.getElementById('description').value = "";
    document.getElementById('baker').value = "";
    
    document.getElementById('yieldModal').style.display = 'block';
}


function closeModal() {
    document.getElementById('yieldModal').style.display = 'none';
}

function editYield(id, product_name, quantity, date_produced, description, baker) {
    document.getElementById('modalTitle').innerText = "Edit Yield";
    document.getElementById('saveButton').innerText = "Update"; // Set button to "Update" when editing
    
    // Fill in the existing data
    document.getElementById('yieldId').value = id;
    document.getElementById('product_name').value = product_name;
    document.getElementById('quantity').value = quantity;
    document.getElementById('date_produced').value = date_produced;
    document.getElementById('description').value = description;

    // Select the correct baker in the dropdown
    let bakerDropdown = document.getElementById('baker');
    for (let i = 0; i < bakerDropdown.options.length; i++) {
        if (parseInt(bakerDropdown.options[i].value) === baker) {
            bakerDropdown.options[i].selected = true;
            break;
        }
    }

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
