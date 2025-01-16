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
if (isset($_POST['pid'], $_POST['name'], $_POST['price'], $_POST['stock'], $_POST['submit_action'])) {
    $pid = $_POST['pid'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $action = $_POST['submit_action'];

    if ($action === "add") {
        // Add new product
        $checkSql = "SELECT * FROM product WHERE pid = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $pid);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h1>Product with ID $pid already exists. Cannot add again.</h1>";
        } else {
            $insertSql = "INSERT INTO product (pid, name, price, stock) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("ssdi", $pid, $name, $price, $stock);

            if ($stmt->execute()) {
                echo "<h1>Product added successfully!</h1>";
            } else {
                echo "<h1>Error adding product: " . $conn->error . "</h1>";
            }

            $stmt->close();
        }

        $checkStmt->close();
    } elseif ($action === "update") {
        // Update existing product
        $updateSql = "UPDATE product SET name = ?, price = ?, stock = ? WHERE pid = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sdis", $name, $price, $stock, $pid);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<h1>Product updated successfully!</h1>";
            } else {
                echo "<h1>No product found with ID $pid to update.</h1>";
            }
        } else {
            echo "<h1>Error updating product: " . $conn->error . "</h1>";
        }

        $stmt->close();
    } else {
        echo "<h1>Invalid action.</h1>";
    }
} else {
    echo "<h1>Error: Please fill in all required fields.</h1>";
}

$conn->close();

echo '<a href="index.html">Back to Home</a>';
?>
