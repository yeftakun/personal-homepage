<?php
// Include connection file
include('../../connection.php');

// Check if password is provided via POST method
if (isset($_POST['password'])) {
    // Retrieve password from form
    $password = $_POST['password'];

    // Query to fetch stored password
    $query_password = "SELECT pass FROM password WHERE id = 1";
    $stmt_password = $conn->query($query_password);
    $stored_password = $stmt_password->fetchColumn();

    // Verify password
    if ($password == $stored_password) {
        // Password matched, proceed with deletion
        // Check if post ID is provided via GET method
        if (isset($_GET['id'])) {
            // Retrieve post ID from the URL
            $postId = $_GET['id'];

            // Query to fetch image path of the post
            $query = "SELECT image_path FROM posts WHERE post_id = :post_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':post_id', $postId);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // If image path exists, delete the image file
            if ($row && !empty($row['image_path'])) {
                $imagePath = '../../assets/img/' . $row['image_path'];
                if (file_exists($imagePath)) {
                    if ($imagePath != '../../assets/img/default.png') {
                        unlink($imagePath); // Delete the image file
                    }
                }
            }

            // Query to delete the post from the database
            $deleteQuery = "DELETE FROM posts WHERE post_id = :post_id";
            $deleteStmt = $conn->prepare($deleteQuery);
            $deleteStmt->bindParam(':post_id', $postId);

            // Execute the delete query
            if ($deleteStmt->execute()) {
                // Redirect to blog CRUD page after successful deletion
                header("Location: blog-crud.php");
                exit();
            } else {
                echo "Error deleting post.";
            }
        } else {
            echo "Post ID not provided.";
        }
    } else {
        // Incorrect password, show error alert and redirect back to blog CRUD page
        echo "<script>alert('Password Salah'); window.location.href = 'blog-crud.php';</script>";
    }
} else {
    // Redirect back to blog CRUD page if password is not provided
    // header("Location: blog-crud.php?error=password_required");
    // exit();
}
?>
