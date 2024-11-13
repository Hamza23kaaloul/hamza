<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_SESSION['receiver_id'];

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
    header("Location: chat.php");
}
$conn->close();
?>
