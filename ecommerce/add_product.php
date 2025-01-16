<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<h1>Connection failed: " . $conn->connect_error . "</h1>");
}

// Validate form inputs
if (isset($_POST['pid'], $_POST['name'], $_POST['price'], $_POST['stock'])) {
    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Check if the product already exists
    $checkSql = "SELECT * FROM product WHERE pid = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $pid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Product already exists
        echo "<h1>Product with ID $pid already exists. Please use a different ID.</h1>";
    } else {
        // Insert the new product
        $sql = "INSERT INTO product (pid, name, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $pid, $name, $price, $stock);

        if ($stmt->execute()) {
            echo "<h1>Product added successfully!</h1>";
        } else {
            echo "<h1>Error adding product: " . $conn->error . "</h1>";
        }

        $stmt->close();
    }

    $checkStmt->close();
} else {
    echo "<h1>Error: Please fill in all required fields.</h1>";
}

$conn->close();

echo '<a href="index.html">Back to Home</a>';
?>
