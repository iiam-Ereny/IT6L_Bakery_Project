<?php
session_start();
include 'db.php'; // Database connection

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM yields WHERE YieldID = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Yield record not found.");
    }
} else {
    die("Invalid request.");
}

// Handle form update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $date_produced = mysqli_real_escape_string($conn, $_POST['date_produced']);
    $baker = mysqli_real_escape_string($conn, $_POST['baker']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $updateQuery = "UPDATE yields SET Product_Name='$product_name', Quantity='$quantity', Date_Produced='$date_produced', Baker='$baker', Description='$description' WHERE YieldID=$id";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: yield.php");
        exit();
    } else {
        echo "Error updating yield: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Yield</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <h2>Edit Yield</h2>
    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="product_name" value="<?php echo htmlspecialchars($row['Product_Name']); ?>" required>

        <label>Quantity</label>
        <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['Quantity']); ?>" required>

        <label>Date Produced</label>
        <input type="date" name="date_produced" value="<?php echo htmlspecialchars($row['Date_Produced']); ?>" required>

        <label>Baker</label>
        <input type="text" name="baker" value="<?php echo htmlspecialchars($row['Baker']); ?>" required>

        <label>Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($row['Description']); ?></textarea>

        <button type="submit">Update</button>
        <a href="yield.php">Cancel</a>
    </form>
</body>
</html>
