<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

$sender_id = $_SESSION['user_id'];
$receiver_id = $_SESSION['receiver_id'];

$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY created_at";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
    echo "<div class='message'>";
    echo "<b>" . ($row['sender_id'] == $sender_id ? "أنت" : $_SESSION['receiver_name']) . ":</b> ";
    echo htmlspecialchars($row['message']);
    echo "</div>";
}
$conn->close();
?>
