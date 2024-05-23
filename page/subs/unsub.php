<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe</title>
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/contact.css">
    <link rel="icon" href="../../assets/logo/miaw.ico">
</head>

<body>

    <header class="header">
        <a href="#" class="logo">Unsubscribe Notification.</a>

        <nav class="navbar">
            <a href="./sub.php" style="--i:1">Subscribe</a>
            <a href="../blog.php" style="--i:2">Back to Blog</a>
        </nav>
    </header>

    <div class="container">
        <form method="POST" class="form-contact">
            <h3>Unsubscribe from Blog Notifications</h3>
            <label for="chat_id">Masukkan Chat ID:</label>
            <input type="text" id="chat_id" name="chat_id" placeholder="Chat ID" required>
            <button type="submit" class="button" name="unsubscribe">Unsubscribe</button>
        </form>
    </div>

    <?php
    // Include database connection file
    include('../../connection.php');

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unsubscribe'])) {
        // Retrieve chat_id from form
        $chat_id = $_POST['chat_id'];

        // Check if chat_id exists in the database
        $check_query = "SELECT COUNT(*) FROM telegram_subscribers WHERE id_chat = :chat_id";
        $stmt_check = $conn->prepare($check_query);
        $stmt_check->bindParam(':chat_id', $chat_id);
        $stmt_check->execute();
        $count = $stmt_check->fetchColumn();

        if ($count > 0) {
            // Delete chat_id from the database
            try {
                $delete_stmt = $conn->prepare("DELETE FROM telegram_subscribers WHERE id_chat = :chat_id");
                $delete_stmt->bindParam(':chat_id', $chat_id);
                $delete_stmt->execute();
                // Show alert after successful unsubscription
                echo '<script>alert("Berhasil menghapus Chat ID!");</script>';
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Chat ID tidak terdaftar
            echo '<script>alert("Chat ID tidak terdaftar, periksa kembali!");</script>';
        }
    }
    ?>

</body>

</html>
