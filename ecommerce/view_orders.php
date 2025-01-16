<?php
$conn = new mysqli("localhost", "root", "", "ecommerce");
if ($conn->connect_error) {
    die("<h1>Connection failed: " . $conn->connect_error . "</h1>");
}

$sql = "SELECT 
            mo.oid AS order_id, 
            mo.cid AS customer_id, 
            mo.order_date, 
            od.product_id, 
            p.name AS product_name, 
            od.quantity 
        FROM make_order mo
        JOIN order_details od ON mo.oid = od.order_id
        JOIN product p ON od.product_id = p.pid
        ORDER BY mo.order_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <div class="nav">
        <a href="index.html">Home</a>
        <a href="view_orders.php" class="active">View Orders</a>
        <a href="view_products.php">View Products</a>
        <a href="view_customers.php">View Customers</a>
        <a href="view_customer_log.php">View Customer Log</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>All Orders</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Order Date</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= $row['customer_id'] ?></td>
                        <td><?= $row['order_date'] ?></td>
                        <td><?= $row['product_id'] ?></td>
                        <td><?= $row['product_name'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
        <?php $conn->close(); ?>
    </div>
</body>
</html>
