<?php
// Include connection file
include('../../connection.php');

// Define uploadImage function di awal kode PHP
function uploadImage() {
    $targetDir = "../../assets/img/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
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
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            return basename( $_FILES["image"]["name"]);
        } else {
            echo "Sorry, there was an error uploading your file.";
            return '';
        }
    }
}

// Initialize variables
$title = $author = $imagePath = $sourceLink = $category = $content = '';
$publishDate = date('Y-m-d'); // Current date

// Check if post ID is provided
if (isset($_GET['id'])) {
    // Retrieve post ID from the URL
    $postId = $_GET['id'];

    // Query to fetch post data from the database
    $query = "SELECT * FROM posts WHERE post_id = :post_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':post_id', $postId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if post data exists
    if ($row) {
        // Assign retrieved data to variables
        $title = $row['title'];
        $author = $row['author'];
        $imagePath = $row['image_path'];
        $sourceLink = $row['source_link'];
        $category = $row['category_id'];
        $content = $row['content'];
        $publishDate = $row['publish_date']; // Update publish date with current value
    } else {
        // Redirect back to blog CRUD page if post does not exist
        header("Location: blog-crud.php");
        exit();
    }
} else {
    // Redirect back to blog CRUD page if post ID is not provided
    header("Location: blog-crud.php");
    exit();
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
    $publishDate = $_POST['publish_date']; // Update publish date with user input

    // Check if a new image file is uploaded
    if ($_FILES['image']['size'] > 0) {
        // Delete old image
        if ($row && !empty($row['image_path'])) {
            $imagePath = '../../assets/img/' . $row['image_path'];
            if (file_exists($imagePath)) {
                if ($imagePath != '../../assets/img/default.png'){
                    unlink($imagePath);
                }
            }
        }
        // Upload new image
        $imagePath = uploadImage();

    }

    // Check if a category is selected
    if (empty($_POST['category'])) {
        // If no category is selected, set category_id to 1000
        $category = 1000;
    } else {
        // If a category is selected, use the selected category_id
        $category = $_POST['category'];
    }

    // Update data into database
    $query = "UPDATE posts 
              SET title = :title, author = :author, publish_date = :publish_date, image_path = :image_path, source_link = :source_link, category_id = :category, content = :content 
              WHERE post_id = :post_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':author', $author);
    $stmt->bindParam(':publish_date', $publishDate);
    $stmt->bindParam(':image_path', $imagePath);
    $stmt->bindParam(':source_link', $sourceLink);
    $stmt->bindParam(':category', $category);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':post_id', $postId);

    if ($stmt->execute()) {
        // Redirect to blog CRUD page after successful update
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
    <title>Edit Post</title>
    <!-- Your CSS styles -->
    <link rel="icon" href="../../assets/logo/miaw.ico">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" href="../../styles/contact.css">
    <style>
        .container {
            margin-top: 250px;
        }
        label {
            align-items: left;
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
        label {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            /* display: block; */
        }

        input[type="file"] {
            display: none; 
        }

        .custom-file-upload {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 6px 12px;
            cursor: pointer;
            background-color: #f9f9f9;
            color: #333;
            border-radius: 4px;
        }

        .custom-file-upload:hover {
            background-color: #e0e0e0;
        }

        /* Style for the image preview */
        #image-preview {
            max-width: 300px;
            margin-top: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        /* #content {
            width: 600px;
        } */
        .button{
            padding: 15px;
            background: #ff5361;
            color: #fff;
            font-size: 18px;
            border: 0;
            outline: none;
            cursor: pointer;
            width: 150px;
            margin: 20px auto 0;
            border-radius: 30px; 
        }
        .tombol-kembali a{
            margin-top: 20px; /* Memberikan jarak atas */
            margin-bottom: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-left: -300px;
        }
        .atas {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="#" class="logo">Edit Post</a>

        <div class="navbar"> <!-- Tombol kembali -->
                <a href="blog-crud.php" style="--i:1">Cancle</a>
        </div>
    </header>
    <br>
    <br>
    <div class="container">
    <form method="POST" enctype="multipart/form-data" class="form-contact">
        <h3>Edit Blog Post</h3>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>
        
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" value="<?php echo $author; ?>" required>
        
        <label for="image" class="uploadimg">Change Cover</label>
        <input type="file" id="image" name="image" accept="image/*">
        <!-- Image preview element -->
        <img id="image-preview" src="../../assets/img/<?php echo $imagePath; ?>" alt="image-preview">
        
        
        <label for="source_link">Source Link:</label>
        <input type="text" id="source_link" name="source_link" value="<?php echo $sourceLink; ?>" required>
        
        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="">Select Category</option>
            <?php
            // Fetch categories from the database
            $query = "SELECT * FROM categories WHERE name != 'Other'";
            $stmt = $conn->query($query);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $selected = ($category == $row['category_id']) ? "selected" : "";
                echo '<option value="' . $row['category_id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
            }
            ?>
        </select>
        
        <label for="content">Content:</label>
        <textarea id="content" name="content" placeholder="<p>Input content with HTML format</p>" rows="5" required><?php echo $content; ?></textarea>
        
        <label for="publish_date">Publish Date:</label>
        <input type="date" id="publish_date" name="publish_date" value="<?php echo $publishDate; ?>" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <input class="button" type="submit" value="Update">
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
