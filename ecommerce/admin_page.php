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

// Handle reply submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply'], $_POST['message_id'])) {
    $reply = trim($_POST['reply']);
    $messageId = intval($_POST['message_id']);

    if (!empty($reply)) {
        $stmt = $conn->prepare("INSERT INTO replies (message_id, reply, timestamp) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $messageId, $reply);
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch all messages with their replies
$sql = "SELECT m.id, m.name, m.subject, m.message, m.timestamp AS message_timestamp, r.reply, r.timestamp AS reply_timestamp 
        FROM messages m 
        LEFT JOIN replies r ON m.id = r.message_id 
        ORDER BY m.timestamp DESC, r.timestamp ASC";
$result = $conn->query($sql);

// Organize messages and replies
$messages = [];
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    if (!isset($messages[$id])) {
        $messages[$id] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'subject' => $row['subject'],
            'message' => $row['message'],
            'timestamp' => $row['message_timestamp'],
            'replies' => []
        ];
    }
    if (!empty($row['reply'])) {
        $messages[$id]['replies'][] = [
            'reply' => $row['reply'],
            'timestamp' => $row['reply_timestamp']
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Respond to Messages</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>All User Messages</h2>
    <div class="container">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-block">
                    <p><strong>Name:</strong> <?= htmlspecialchars($message['name']) ?></p>
                    <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
                    <p><strong>Message:</strong> <?= htmlspecialchars($message['message']) ?></p>
                    <p><small><strong>Date:</strong> <?= htmlspecialchars($message['timestamp']) ?></small></p>

                    <h3>Replies:</h3>
                    <?php if (!empty($message['replies'])): ?>
                        <ul>
                            <?php foreach ($message['replies'] as $reply): ?>
                                <li>
                                    <p><?= htmlspecialchars($reply['reply']) ?></p>
                                    <p><small><strong>Date:</strong> <?= htmlspecialchars($reply['timestamp']) ?></small></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No replies yet.</p>
                    <?php endif; ?>

                    <form action="admin_page.php" method="POST">
                        <input type="hidden" name="message_id" value="<?= $message['id'] ?>">
                        <textarea name="reply" rows="2" placeholder="Write your reply here..." required></textarea>
                        <button type="submit">Send Reply</button>
                    </form>
                </div>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </div>
    <div class="back-home">
        <a href="index.html">Back to Home</a>
    </div>
</body>
</html>
