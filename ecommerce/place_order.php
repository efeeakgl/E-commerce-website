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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = isset($_POST["oid"]) ? trim($_POST["oid"]) : '';
    $customer_id = isset($_POST["customer_id"]) ? trim($_POST["customer_id"]) : '';
    $product_id = isset($_POST["product_id"]) ? trim($_POST["product_id"]) : '';
    $quantity = isset($_POST["quantity"]) ? (int)trim($_POST["quantity"]) : 0;

    if (empty($order_id) || empty($customer_id) || empty($product_id) || $quantity <= 0) {
        echo "<h1>Error: Please fill in all required fields.</h1>";
        exit();
    }

    try {
        // Call the PlaceOrder stored procedure
        $stmt = $conn->prepare("CALL PlaceOrder(?, ?, ?, ?)");
        $stmt->bind_param("sssi", $order_id, $customer_id, $product_id, $quantity);
        $stmt->execute();

        // Clear all result sets
        while ($conn->more_results() && $conn->next_result()) {
            $result = $conn->store_result();
            if ($result) {
                $result->free();
            }
        }

        $stmt->close();

        // Display success message
        echo "<h1>Order placed successfully!</h1>";
        echo "<p>Order ID: $order_id</p>";
        echo "<p>Customer ID: $customer_id</p>";
        echo "<p>Product ID: $product_id</p>";
        echo "<p>Quantity: $quantity</p>";
    } catch (mysqli_sql_exception $e) {
        echo "<h1>Error: Could not place order.</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
}

$conn->close();
?>
