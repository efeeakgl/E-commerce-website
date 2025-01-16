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
    $customer_id = isset($_POST["customer_id"]) ? trim($_POST["customer_id"]) : '';

    // Validate input
    if (empty($customer_id)) {
        echo "<h1>Error: Please provide a Customer ID.</h1>";
        exit();
    }

    try {
        // Initialize the OUT parameter
        $conn->query("SET @total_spent = 0;");

        // Call the stored procedure
        $stmt = $conn->prepare("CALL GetCustomerOrderReport(?, @total_spent);");
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();

        // Fetch the result set (order details)
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            echo "<h1>Order Report for Customer ID: $customer_id</h1>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr>
                    <th>Order ID</th>
                    <th>Order Amount</th>
                  </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['order_id']}</td>
                        <td>{$row['order_amount']}</td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<h1>No orders found for Customer ID: $customer_id</h1>";
        }

        $stmt->close();

        // Retrieve the OUT parameter
        $result = $conn->query("SELECT @total_spent AS total_spent;");
        $row = $result->fetch_assoc();
        $total_spent = $row['total_spent'] ?? 0;

        echo "<h2>Total Amount Spent: $total_spent</h2>";

        // Clear any remaining result sets
        while ($conn->more_results() && $conn->next_result()) {
            $result = $conn->store_result();
            if ($result) {
                $result->free();
            }
        }
    } catch (mysqli_sql_exception $e) {
        echo "<h1>Error: Unable to fetch the report.</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
}

$conn->close();
?>
