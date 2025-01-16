<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Update if needed
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<h1>Connection failed: " . $conn->connect_error . "</h1>");
}

// Validate form inputs
if (isset($_POST['cid'], $_POST['name'], $_POST['email'], $_POST['password'])) {
    $cid = $_POST['cid'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // In production, use password_hash for security

    // Check if the customer already exists
    $checkSql = "SELECT * FROM customer WHERE cid = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $cid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Customer already exists
        $message = "<h1>Customer with ID $cid already exists.</h1>";
    } else {
        // Insert the new customer
        $sql = "INSERT INTO customer (cid, name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $cid, $name, $email, $password);

        if ($stmt->execute()) {
            $message = "<h1>Customer added successfully!</h1>";
        } else {
            $message = "<h1>Error adding customer: " . $conn->error . "</h1>";
        }

        $stmt->close();
    }

    $checkStmt->close();
} else {
    // Missing required fields
    $message = "<h1>Error: Please fill in all required fields.</h1>";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Customer</title>
</head>
<body>
    <?php echo $message; ?>
    <a href="index.html">Back to Home</a>
</body>
</html>
