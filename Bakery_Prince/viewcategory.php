<?php
session_start();
include 'db.php'; // Database connection

// Handle deletion if 'delete' is set in the URL
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Delete the category
    $query = "DELETE FROM categories WHERE CategoryID = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Re-sequence IDs after deletion
        mysqli_query($conn, "SET @count = 0;");
        mysqli_query($conn, "UPDATE categories SET CategoryID = @count := @count + 1;");
        mysqli_query($conn, "ALTER TABLE categories AUTO_INCREMENT = 1;");

        header("Location: viewcategory.php?msg=Category deleted successfully");
        exit();
    } else {
        echo "Error deleting category: " . mysqli_error($conn);
    }
}

// Handle update (editing a category)
if (isset($_POST['update'])) {
    $id = intval($_POST['category_id']);
    $name = mysqli_real_escape_string($conn, $_POST['category_name']);

    $query = "UPDATE categories SET CategoryName='$name' WHERE CategoryID=$id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: viewcategory.php?msg=Category updated successfully");
        exit();
    } else {
        echo "Error updating category: " . mysqli_error($conn);
    }
}

// Fetch category details for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $query = "SELECT CategoryID, CategoryName FROM categories WHERE CategoryID = $edit_id";
    $result = mysqli_query($conn, $query);
    $edit_category = mysqli_fetch_assoc($result);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <link rel="stylesheet" href="main.css">
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this category?")) {
                window.location.href = "viewcategory.php?delete=" + id;
            }
        }
    </script>
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
        <h1>Category List</h1>

        <!-- Edit Form (Shows only if an edit button is clicked) -->
        <?php if ($edit_category): ?>
            <h2>Edit Category</h2>
            <form method="POST" action="viewcategory.php">
                <input type="hidden" name="category_id" value="<?php echo $edit_category['CategoryID']; ?>">
                <label>Category Name:</label>
                <input type="text" name="category_name" value="<?php echo htmlspecialchars($edit_category['CategoryName']); ?>" required>
                <button type="submit" name="update">Update Category</button>
                <a href="viewcategory.php">Cancel</a>
            </form>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>

            <?php
            $query = "SELECT CategoryID, CategoryName FROM categories ORDER BY CategoryID ASC";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['CategoryID']}</td>
                        <td>{$row['CategoryName']}</td>
                        <td>
                            <a href='viewcategory.php?edit={$row['CategoryID']}' class='edit-btn'>Edit</a>
                            <a href='#' onclick='confirmDelete({$row['CategoryID']})' class='delete-btn'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>
