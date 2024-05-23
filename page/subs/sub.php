<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe</title>
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/contact.css">
    <link rel="icon" href="../../assets/logo/miaw.ico">
</head>

<body>

    <header class="header">
        <a href="#" class="logo">Subscribe Notification.</a>

        <nav class="navbar">
            <a href="./unsub.php" style="--i:1">Unsubscribe</a>
            <a href="../blog.php" style="--i:2">Back to Blog</a>
        </nav>
    </header>

    <div class="container">
        <form method="POST" class="form-contact">
            <h3>Subscribe to Blog Notifications</h3>
            <label for="chat_id">Masukkan Chat ID:</label>
            <input type="text" id="chat_id" name="chat_id" placeholder="Chat ID" required>
            <p><a href="https://t.me/blognotification_bot">Get your ChatID</a></p>
            <button type="submit" class="button" name="subscribe">Subscribe</button>
            <button class="button" onclick="redirectToBot()">Init</button>
        </form>
    </div>

    <?php
    // Include database connection file
    include('../../connection.php');

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subscribe'])) {
        // Retrieve chat_id from form
        $chat_id = $_POST['chat_id'];

        // Check if chat_id already exists
        $check_query = "SELECT COUNT(*) FROM telegram_subscribers WHERE id_chat = :chat_id";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bindParam(':chat_id', $chat_id);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            // Chat ID sudah terdaftar
            echo '<script>alert("Chat ID sudah terdaftar!");</script>';
        } else {
            // Jika chat_id belum terdaftar, masukkan ke database
            try {
                $stmt = $conn->prepare("INSERT INTO telegram_subscribers (id_chat) VALUES (:chat_id)");
                $stmt->bindParam(':chat_id', $chat_id);
                $stmt->execute();
                // Show alert after successful subscription
                echo '<script>alert("Berhasil subscribe!");</script>';
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
    }
    ?>

    <script>
        // Redirect to bot URL
        function redirectToBot() {
            window.location.href = "https://t.me/blognotification_bot";
        }
    </script>
</body>

</html>
