<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("<h1>Connection failed: " . $conn->connect_error . "</h1>");
}

$sql = "SELECT pid, name, price, stock FROM product";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <div class="nav">
        <a href="index.html">Home</a>
        <a href="view_orders.php">View Orders</a>
        <a href="view_products.php">View Products</a>
        <a href="view_customers.php">View Customers</a>
        <a href="view_customer_log.php">View Customer Log</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>All Products</h1>
        <?php if ($result->num_rows > 0): ?>
            <table class="styled-table">
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['pid'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td>$<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['stock'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
        <?php $conn->close(); ?>
    </div>
</body>
</html>
