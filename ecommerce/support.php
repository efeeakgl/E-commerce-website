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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $timestamp = date("Y-m-d H:i:s");

    if (!empty($name) && !empty($subject) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (name, subject, message, timestamp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $subject, $message, $timestamp);

        if ($stmt->execute()) {
            echo "<h3>Message sent successfully!</h3>";
        } else {
            echo "<h3>Error: Could not send message.</h3>";
        }

        $stmt->close();
    } else {
        echo "<h3>All fields are required!</h3>";
    }
}

// Fetch all messages from the same user
$nameFilter = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    if (!empty($name) && !empty($subject) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (name, subject, message, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $subject, $message);

        if ($stmt->execute()) {
            // Set the name filter to the submitted name for showing messages
            $nameFilter = $name;
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "All fields are required!";
    }
}

// Fetch all messages from the same user
$messages = [];
if (!empty($nameFilter)) {
    $stmt = $conn->prepare("SELECT subject, message, timestamp FROM messages WHERE name = ? ORDER BY timestamp DESC");
    $stmt->bind_param("s", $nameFilter);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Support Page</h1>
    <form action="support.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($nameFilter) ?>" required>

        <label for="subject">Subject:</label>
        <select id="subject" name="subject" required>
            <option value="Defected Product">Defected Product</option>
            <option value="Late Order">Late Order</option>
            <option value="Lost Product">Lost Product</option>
            <option value="Suggestion">Suggestion</option>
        </select>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>

        <button type="submit">Send Message</button>
    </form>

    <?php if (!empty($messages)): ?>
        <h2>Your Messages</h2>
        <table>
            <tr>
                <th>Subject</th>
                <th>Message</th>
                <th>Date & Time</th>
            </tr>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                    <td><?= htmlspecialchars($msg['message']) ?></td>
                    <td><?= htmlspecialchars($msg['timestamp']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    <div>
        <a href="index.html">Back to Home</a>
    </div>
</body>
</html>
