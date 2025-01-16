<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate form inputs
if (isset($_POST['product_id']) && isset($_POST['stock']) && isset($_POST['price'])) {
    $product_id = $_POST['product_id'];
    $new_stock = $_POST['stock'];
    $new_price = $_POST['price'];

    // Update the product in the database
    $sql = "UPDATE product SET stock = ?, price = ? WHERE pid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $new_stock, $new_price, $product_id);

    if ($stmt->execute()) {
        echo "<h1>Product updated successfully!</h1>";
    } else {
        echo "<h1>Error updating product: " . $conn->error . "</h1>";
    }

    $stmt->close();
} else {
    echo "<h1>Error: Invalid input. Please fill in all fields.</h1>";
}

$conn->close();

echo '<a href="index.html">Back to Product Management</a>';
?>
