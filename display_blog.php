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
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <!-- Add Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this blog post?");
        }
    </script>
</head>

<body>
    <h1>Blog Posts</h1>
    <div class="blog-container">
        <?php foreach ($blogPosts as $post) : ?>
            <div class="blog-post">
                <div class="blog-content">
                    <div class="blog-info">
                        <h4><?php echo htmlspecialchars($post['blogTitle']); ?></h4>
                        <ul class="post-info">
                            <li><a href="#"><?php echo htmlspecialchars($post['author_name']); ?></a></li>
                            <li><a href="#"><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></a></li>
                        </ul>
                    </div>
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
                <div class="blog-thumb">
                    <?php if (!empty($post['blogImage'])) : ?>
                        <!-- Display the image if blogImage is not empty -->
                        <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" alt="Blog Image">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>