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

// Firebase API details
$firebaseUrl = "https://ecommerce-445d3-default-rtdb.firebaseio.com/messages.json"; // Replace with your Firebase Realtime Database URL

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($subject) && !empty($message)) {
        // Insert into MySQL database
        $stmt = $conn->prepare("INSERT INTO messages (name, subject, message, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $subject, $message);

        if ($stmt->execute()) {
            // Store the message in Firebase
            $data = [
                'name' => $name,
                'subject' => $subject,
                'message' => $message,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $jsonData = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $firebaseUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $firebaseResponse = curl_exec($ch);

            if ($firebaseResponse === false) {
                echo "Error storing message in Firebase: " . curl_error($ch);
            }

            curl_close($ch);

            $stmt->close();

            // Redirect to message_details.php with the user's name
            header("Location: message_details.php?name=" . urlencode($name));
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required!";
    }
}

$conn->close();
?>
