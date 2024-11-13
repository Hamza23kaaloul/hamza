<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app";

$conn = new mysqli($servername, $username, $password, $dbname);

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

// جلب اسم المستخدم الحالي
$current_user_name = $_SESSION['username'];

// التحقق من وجود المستقبل
if (isset($_GET['user'])) {
    $receiver_id = $_GET['user'];
    
    // جلب بيانات المستخدم المستقبل
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        $_SESSION['receiver_id'] = $receiver_id;
        $_SESSION['receiver_name'] = $user['name'];
    } else {
        $error_message = "المستخدم غير موجود";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Room</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <!-- عرض اسم المستخدم الحالي -->
        <div class="header">
            <h2>مرحباً، <?php echo htmlspecialchars($current_user_name); ?></h2>
        </div>

        <!-- قائمة المستخدمين -->
        <div class="users-list">
            <h3>المستخدمون</h3>
            <?php
            $stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $users = $stmt->get_result();
            
            while ($user = $users->fetch_assoc()) {
                echo "<a href='chat.php?user=" . $user['id'] . "'>" . htmlspecialchars($user['name']) . "</a><br>";
            }
            ?>
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </div>

        <!-- واجهة المحادثة -->
        <div class="chat-box">
            <h3>المحادثة مع: <?php echo isset($_SESSION['receiver_name']) ? htmlspecialchars($_SESSION['receiver_name']) : "اختر مستخدمًا"; ?></h3>
            <div class="messages">
                <!-- الرسائل سيتم عرضها هنا -->
            </div>
            <form action="send_message.php" method="POST">
                <input type="text" name="message" placeholder="اكتب رسالتك هنا" required>
                <button type="submit">إرسال</button>
            </form>
        </div>
    </div>

    <script>
        function loadMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "load_messages.php", true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.querySelector(".messages").innerHTML = this.responseText;
                }
            };
            xhr.send();
        }

        setInterval(loadMessages, 2000);
        loadMessages();
    </script>
</body>
</html>
