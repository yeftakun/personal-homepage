<?php
// Include connection file
include('../../connection.php');

// Initialize variables
$title = $author = $imagePath = $sourceLink = $category = $content = '';
$publishDate = date('Y-m-d'); // Current date

// Function to upload image
// Function to upload image
// Function to upload image
function uploadImage() {
    $targetDir = "../../assets/img/";
    $imageName = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $imageName;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $newName = ''; // Inisialisasi variabel $newName

    // Check if file already exists
    $counter = 1;
    while (file_exists($targetFile)) {
        $info = pathinfo($imageName);
        $newName = $info['filename'] . '-' . $counter . '.' . $info['extension'];
        $targetFile = $targetDir . $newName;
        $counter++;
    }

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["image"]["size"] > 512000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
        // if file is not uploaded, return default image path
        return "default.png";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // If the file is successfully uploaded, return the new file name
            return basename($targetFile);
        } else {
            echo "Sorry, there was an error uploading your file.";
            return '';
        }
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $password = $_POST['password'];

    // Check if password matches
    $query_password = "SELECT pass FROM password WHERE id = 1";
    $stmt_password = $conn->query($query_password);
    $stored_password = $stmt_password->fetchColumn();

    if ($password != $stored_password) {
        echo "<script>alert('Password salah'); history.back();</script>";
        exit(); // Stop further execution
    }

    // Continue processing the form if password matches

    // Retrieve other data from form
    $title = $_POST['title'];
    $author = $_POST['author'];
    $sourceLink = $_POST['source_link'];
    $category = $_POST['category'];
    $content = $_POST['content'];

    // Check if an image file is uploaded
    if ($_FILES['image']['size'] > 0) {
        // Upload image
        $imagePath = uploadImage();
    } else {
        // If no image is uploaded, set default image path
        $imagePath = "default.png";
    }

    // Check if a category is selected
    if (empty($_POST['category'])) {
        // If no category is selected, set category_id to 1000
        $category = 1000;
    } else {
        // If a category is selected, use the selected category_id
        $category = $_POST['category'];
    }

    // Insert data into database
    $query = "INSERT INTO posts (title, author, publish_date, image_path, source_link, category_id, content) 
              VALUES (:title, :author, :publish_date, :image_path, :source_link, :category, :content)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':publish_date', $publishDate);
    $stmt->bindParam(':image_path', $imagePath);
    $stmt->bindParam(':source_link', $sourceLink);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':content', $content);

    if ($stmt->execute()) {
        // Fetch the post ID based on the submitted title
    $queryPostId = "SELECT post_id FROM posts WHERE title = :title";
    $stmtPostId = $conn->prepare($queryPostId);
    $stmtPostId->bindParam(':title', $title);
    $stmtPostId->execute();
    $postId = $stmtPostId->fetchColumn();

    // Construct message for Telegram notification
    $message = "*New Blog Post Added*\n\n";
    $message .= "Title: $title\n";
    $message .= "Author: $author\n";
    $message .= "Publish Date: $publishDate\n";

    // Fetch category name based on the selected category_id
    $queryCategory = "SELECT title, categories.name AS category_name FROM posts 
                JOIN categories ON posts.category_id = categories.category_id 
                WHERE posts.post_id = :post_id";
    $stmtCategory = $conn->prepare($queryCategory);
    $stmtCategory->bindParam(':post_id', $postId);
    $stmtCategory->execute();
    $rowCategory = $stmtCategory->fetch(PDO::FETCH_ASSOC);
    $category = $rowCategory['category_name'];
    $message .= "Category: $category\n";

    // Construct the link to the newly added blog post
    $link = "yeftakun.my.id/dashboard/page/blog-list/blogcontent.php?id=$postId";
    $message .= "Link: $link";

    // Send notification to Telegram
    $telegramUrl = "https://api.telegram.org/bot7095664056:AAEiPyzvnPYcWBk2SVNgfYHk0TgRWaa-g4c/sendMessage"; // Ganti YOUR_BOT_TOKEN dengan token bot Anda
    $telegramParams = [
        'parse_mode' => 'markdown',
        'text' => $message
    ];

    // Fetch all chat IDs from the database
    $queryChatIds = "SELECT id_chat FROM telegram_subscribers";
    $stmtChatIds = $conn->query($queryChatIds);
    $chatIds = $stmtChatIds->fetchAll(PDO::FETCH_COLUMN);

    // Loop through each chat ID and send the notification
    foreach ($chatIds as $chatId) {
        $telegramParams['chat_id'] = $chatId;
        $telegramQuery = http_build_query($telegramParams);
        $telegramRequest = $telegramUrl . '?' . $telegramQuery;

        // Send the API request
        $telegramResponse = file_get_contents($telegramRequest);
    }

    // Redirect to blog CRUD page after successful insertion
    header("Location: blog-crud.php");
    exit();

    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Post</title>
    <link rel="icon" href="../../assets/logo/miaw.ico">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/contact.css">
    <style>
        .container {
            margin-top: 180px;
        }
        .uploadimg{
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background-color: #ff5361;
            color: white;
        }
        #category {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <header class="header">
        <a href="#" class="logo">Add Post</a>

        <nav class="navbar">
            <a href="blog-crud.php" style="--i:1">Cancel</a> <!-- Ganti Cancle dengan Cancel -->
        </nav>
    </header>
    <div class="container">
        <form method="POST" enctype="multipart/form-data" class="form-contact">
            <h3>Write a New Blog Post</h3>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="category">Category:</label>
            <select id="category" name="category">
                <option value="">Select Category</option>
                <?php
                // Fetch categories from the database excluding the "Other" category
                $query = "SELECT * FROM categories WHERE name != 'Other'";
                $stmt = $conn->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $row['category_id'] . '">' . $row['name'] . '</option>';
                }
                ?>
            </select>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>

            <label for="image" class="uploadimg">Upload Cover Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <img id="image-preview" src="#" alt="image-preview">

            <label for="source_link">Source Link:</label>
            <input type="text" id="source_link" name="source_link" required>

            <label for="content">Content:</label>
            <textarea id="content" name="content" placeholder="<p>Input content with HTML format</p>" rows="5" required></textarea>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button class="button" type="submit">Post</button>
        </form>
    </div>

    <script>
        // Function to preview the selected image
        function previewImage() {
            var preview = document.querySelector('#image-preview');
            var file = document.querySelector('input[type=file]').files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
                preview.style.display = 'block'; // Show the preview image
            }

            if (file) {
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                preview.src = ''; // Clear the preview if no file is selected
                preview.style.display = 'none'; // Hide the preview image
            }
        }

        // Event listener for file input change
        document.querySelector('input[type=file]').addEventListener('change', previewImage);
    </script>
</body>

</html>
