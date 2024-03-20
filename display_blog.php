<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Retrieve user ID from session
$userId = $_SESSION['user_id'];

// Retrieve blog posts associated with the logged-in user from the database
$stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id WHERE b.user_id = :user_id ORDER BY b.id DESC");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Posts</title>
    <!--link rel="stylesheet" type="text/css" href="styles.css-->
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <!-- Add Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .blog-post {
            position: relative; /* Set the position of the blog post container to relative */
            width: 45%; /* Adjust the width of each blog post */
            margin: 20px; /* Add margin to create spacing between posts */
            display: inline-block; /* Ensure blog posts appear in a row */
            vertical-align: top; /* Align posts at the top */
            background-color: #f8f4f4;
            border: 1px solid #ccc; /* Add grey border */
            border-radius: 5px;
        }
        .blog-thumb img {
            width: 100%;
            overflow: hidden;
        }
        .down-content {
            padding: 40px;
            border-right: 1px solid #eee;
            border-left: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .down-content span {
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 900;
            color: #1ea5c3;
        }
        .down-content h4 {
            font-size: 24px;
            text-transform: capitalize;
            letter-spacing: 0.5px;
            font-weight: 900;
            color: #20232e;
            margin: 10px 0px 12px 0px;
        }
        .down-content ul.post-info li {
            display: inline-block;
            margin-right: 8px;
        }
        .down-content ul.post-info li:after {
            content: '|';
            color: #aaa;
            margin-left: 8px;
        }
        .down-content ul.post-info li:last-child::after {
            display: none;
        }
        .down-content ul.post-info li a {
            font-size: 14px;
            color: #aaa;
            font-weight: 400;
            transition: all .3s;
        }
        .down-content ul.post-info li a:hover {
            color: #1ea5c3;
        }
        .down-content p {
            padding: 25px 0px;
            margin: 25px 0px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }
        .blog-actions {
            position: absolute; /* Position the actions div absolutely within the blog post */
            top: 10px; /* Align to the top */
            right: 10px; /* Align to the right */
        }
        .blog-actions button {
            margin-left: 5px;
            border: none;
            background: none;
            cursor: pointer;
        }
        .fa-edit{
            font-size: 18px;
        }
        .fa-trash-alt{
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>Blog Posts</h1>
    <?php foreach ($blogPosts as $post): ?>
    <div class="blog-post">
        <div class="blog-thumb">
            <?php if (!empty($post['blogImage'])): ?>
                <!-- Display the image if blogImage is not empty -->
                <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" alt="Blog Image">
            <?php endif; ?>
        </div>
        <div class="down-content">
            <span>Author: <?php echo htmlspecialchars($post['author_name']); ?></span>
            <h4><?php echo htmlspecialchars($post['blogTitle']); ?></h4>
            <p><?php echo htmlspecialchars($post['blogContent']); ?></p>
            <!-- Floating buttons for edit and delete -->
            <div class="blog-actions">
                <form style="display: inline;" method="post" action="delete_blog.php" onsubmit="return confirmDelete();">
                    <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">
                    <button type="submit"><i class="fas fa-trash-alt"></i></button> <!-- Delete icon -->
                </form>
                
                <a href="edit_blog.php?postId=<?php echo $post['id']; ?>"><button><i class="fas fa-edit"></i></button></a> <!-- Edit icon -->
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</body>
</html>
