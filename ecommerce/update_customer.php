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
if (isset($_POST['cid'])) {
    $cid = $_POST['cid'];
    $new_name = isset($_POST['new_name']) ? $_POST['new_name'] : null;
    $new_email = isset($_POST['new_email']) ? $_POST['new_email'] : null;

    // Check if the customer exists
    $checkSql = "SELECT * FROM customer WHERE cid = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $cid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // Prepare the update statement
        $updateSql = "UPDATE customer SET ";
        $params = [];
        $types = "";

        if ($new_name) {
            $updateSql .= "name = ?, ";
            $params[] = $new_name;
            $types .= "s";
        }
        if ($new_email) {
            $updateSql .= "email = ?, ";
            $params[] = $new_email;
            $types .= "s";
        }

        // Remove trailing comma
        $updateSql = rtrim($updateSql, ", ") . " WHERE cid = ?";
        $params[] = $cid;
        $types .= "s";

        // Execute the update query
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "<h1>Customer information updated successfully!</h1>";
            } else {
                echo "<h1>No changes made. The information might already be up-to-date.</h1>";
            }
        } else {
            echo "<h1>Error updating customer: " . $stmt->error . "</h1>";
        }

        $stmt->close();
    } else {
        echo "<h1>No customer found with the given ID.</h1>";
    }

    $checkStmt->close();
} else {
    echo "<h1>Error: Customer ID is required.</h1>";
}

$conn->close();

echo '<a href="index.html">Back to Home</a>';
?>
