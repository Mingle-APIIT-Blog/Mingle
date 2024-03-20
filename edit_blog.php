<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit;
}

// Retrieve blog post data for editing
if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];
    $stmt = $db->prepare("SELECT blogTitle, blogContent FROM blog WHERE id = :postId");
    $stmt->bindParam(':postId', $postId);
    $stmt->execute();
    $blogPost = $stmt->fetch(PDO::FETCH_ASSOC);
    $blogTitle = $blogPost['blogTitle'];
    $blogContent = $blogPost['blogContent'];

    // Display the edit form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Blog Post</title>
        <link rel="stylesheet" href="styles.css">
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
        }
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            resize: vertical;
        }
        .update-blog-title{
            padding-top: 5px;
            padding-bottom: 20px;
        }
        #blogTitle{
            padding-top: 4px;
            padding-bottom: 4px;
        }
        input[type="submit"] {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    </head>
    <body>
        <div class="container">
            <h1>Edit Blog Post</h1>
            <form method="post" action="edit_blog.php">
                <label for="blogTitle">Title:</label><br>
                <div class="update-blog-title">
                <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($blogTitle); ?>"><br>
                </div>
                <label for="blogContent">Blog Content:</label><br>
                <textarea id="blogContent" name="blogContent" rows="8"><?php echo htmlspecialchars($blogContent); ?></textarea><br>

                <input type="hidden" name="postId" value="<?php echo $postId; ?>">
                <input type="submit" value="Update">
            </form>
        </div>
    </body>
    </html>

    <?php
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blogContent']) && isset($_POST['postId']) && isset($_POST['blogTitle'])) {
    // Update blog post content in the database
    $postId = $_POST['postId'];
    $blogTitle = htmlspecialchars($_POST['blogTitle']);
    $blogContent = htmlspecialchars($_POST['blogContent']);

    $stmt = $db->prepare("UPDATE blog SET blogTitle = :blogTitle, blogContent = :blogContent WHERE id = :postId");
    $stmt->bindParam(':blogTitle', $blogTitle);
    $stmt->bindParam(':blogContent', $blogContent);
    $stmt->bindParam(':postId', $postId);
    $stmt->execute();

    $_SESSION['success_message'] = "Blog post updated successfully.";
    header('Location: display_blog.php'); // Redirect to display_blog.php after successful update
    exit;
} else {
    // Redirect if postId is not provided or request method is not POST
    header('Location: index.php');
    exit;
}
?>
