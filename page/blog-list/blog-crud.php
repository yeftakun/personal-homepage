<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog CRUD</title>
    <style>
        .container {
            margin-left: 20px;
            margin-right: 20px;
            margin-top: 100px;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: auto; /* Tambahkan ini untuk menyesuaikan lebar kolom */
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 290px; /* Tambahkan ini untuk membatasi lebar teks */
        }

        th {
            background-color: #efefef;
            color: black;
        }

        tr:hover {
            background-color: #00EEFF;
            color: black;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #dc3545;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #c82333;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 20% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
            text-align: center;
            color: black;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        <style>
    /* Styles for the label and file input (same as before) */
        label {
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: block;
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
        #content {
            width: 600px;
        }
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
        .ini-tombol {
        margin-top: 0px; /* Memberikan jarak atas */
        margin-bottom: 20px;
        }

        .ini-tombol a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        }

        .ini-tombol a:hover {
        background-color: #0056b3;
        }
        .control {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .control input {
            padding: 10px;  
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="icon" href="../assets/logo/miaw.ico">
</head>

<body>
    <div class="container">
        
    <header class="header">
        <a href="#" class="logo">Blog CRUD.</a>

        <nav class="navbar">
            <a href="../blog.php" style="--i:1">Back to Blog</a>
        </nav>
    </header>

    <div class="container">
    <!-- <a href="" style="margin-bottom: 20px; display: block;">Add New Post</a> -->
    <div class="control">
        <div class="ini-tombol">
                    <!-- Tombol Tambah postingan -->
                    <a href="add-blog.php">Add New Post</a>
        </div>
    
        <input type="text" id="searchInput" placeholder="Search...">
    </div>


    <table>
        <thead>
            <tr>
                <th id="post_id">Post ID</th>
                <th id="title">Title</th>
                <th id="author">Author</th>
                <th id="publish_date">Publish Date</th>
                <th id="category">Category</th>
                <th id="image_path">Image Path</th>
                <th id="source_link">Source Link</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Include connection file
            include('../../connection.php');

            // Query to fetch posts from the database
            $query = "SELECT posts.*, categories.name AS category_name FROM posts 
            LEFT JOIN categories ON posts.category_id = categories.category_id";
            $stmt = $conn->query($query);

            // Loop through the fetched posts and display them in table rows
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Save the title of the post
                $postTitle = $row['title'];
                echo '<tr>';
                echo '<td style="max-width: 75px;">' . $row['post_id'] . '</td>';
                echo '<td>' . $row['title'] . '</td>';
                echo '<td style="max-width: 150px;">' . $row['author'] . '</td>';
                echo '<td style="max-width: 80px;">' . $row['publish_date'] . '</td>';
                echo '<td style="max-width: 80px;">' . $row['category_name'] . '</td>';
                echo '<td style="max-width: 80px;">' . $row['image_path'] . '</td>';
                echo '<td style="max-width: 150px;">' . $row['source_link'] . '</td>';
                // Add edit and delete buttons with appropriate links and attributes
                echo '<td>';
                echo '<div class="ini-tombol"><a href="edit-blog.php?id=' . $row['post_id'] . '">Edit</a></div>';
                echo '<button onclick="deletePost(' . $row['post_id'] . ', \'' . $row['title'] . '\')">Delete</button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    </div>

    <!-- Modal for delete confirmation -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
        <span class="close">&times;</span>
        <p id="modalMessage"></p>
        <form id="deleteForm" method="POST">
            <label for="deletePassword">Enter Password:</label>
            <input type="password" id="deletePassword" name="password" required>
            <button type="submit" id="confirmDelete">Delete</button>
        </form>
    </div>
    </div>

    </div>
    <script>
        // Function to open the delete confirmation modal
        function deletePost(postId, postTitle) {
            var modal = document.getElementById("deleteModal");
            modal.style.display = "block";

            // Set the post title in the modal message
            var modalMessage = document.getElementById("modalMessage");
            modalMessage.textContent = "Are you sure you want to delete the post '" + postTitle + "'?";

            var deleteForm = document.getElementById("deleteForm");
            deleteForm.action = "delete-blog.php?id=" + postId; // Set form action with post id

            // Close the modal when the close button (x) is clicked
            var span = document.getElementsByClassName("close")[0];
            span.onclick = function() {
                modal.style.display = "none";
            }
        }
    </script>
    <script>
    // Ambil elemen input pencarian
    var searchInput = document.getElementById("searchInput");

    // Tambahkan event listener untuk memicu pencarian setiap kali teks di input berubah
    searchInput.addEventListener("input", function() {
        var filter = searchInput.value.toUpperCase();
        var table = document.querySelector("table");
        var rows = table.getElementsByTagName("tr");

        // Loop melalui semua baris dan sembunyikan yang tidak cocok dengan pencarian
        for (var i = 0; i < rows.length; i++) {
            var cells = rows[i].getElementsByTagName("td");
            var showRow = false;
            for (var j = 0; j < cells.length; j++) {
                var cell = cells[j];
                if (cell) {
                    var txtValue = cell.textContent || cell.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        showRow = true;
                        break;
                    }
                }
            }
            // Tampilkan baris jika cocok dengan pencarian, sembunyikan jika tidak
            if (showRow) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });
</script>

    <script src="../script/anim-type.js"></script>
    <script src="../script/script.js"></script>
</body>


</html>
