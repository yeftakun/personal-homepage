<?php
// Include connection file
include('../../connection.php');

// Check if post ID is provided
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
            unlink($imagePath); // Delete the image file
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
?>
