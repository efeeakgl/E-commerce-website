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

// Fetch all messages for the same user
$userName = isset($_GET['name']) ? trim($_GET['name']) : '';
$messages = [];
if (!empty($userName)) {
    $sql = "SELECT subject, message, timestamp FROM messages WHERE name = ? ORDER BY timestamp DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userName);
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
    <title>Your Messages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Messages from <?= htmlspecialchars($userName) ?></h1>

    <?php if (!empty($messages)): ?>
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
    <?php else: ?>
        <p>No messages found for <?= htmlspecialchars($userName) ?>.</p>
    <?php endif; ?>

    <div class="back-home">
        <a href="index.html">Back to Home Page</a>
    </div>
</body>
</html>
