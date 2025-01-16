<?php
$conn = new mysqli("localhost", "root", "", "ecommerce");
if ($conn->connect_error) {
    die("<h1>Connection failed: " . $conn->connect_error . "</h1>");
}

$sql = "SELECT cid, name, email FROM customer";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <div class="nav">
        <a href="index.html">Home</a>
        <a href="view_orders.php">View Orders</a>
        <a href="view_products.php">View Products</a>
        <a href="view_customers.php" class="active">View Customers</a>
        <a href="view_customer_log.php">View Customer Log</a>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h1>All Customers</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['cid'] ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['email'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>
        <?php $conn->close(); ?>
    </div>
</body>
</html>
