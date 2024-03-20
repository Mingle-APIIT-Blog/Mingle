<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Check if the blog ID is provided via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['blog_id'])) {
    // Get the blog ID from the form data
    $blogId = $_POST['blog_id'];

    // Prepare a SQL statement to delete the blog post
    $stmt = $db->prepare("DELETE FROM blog WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $blogId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);

    // Execute the SQL statement
    try {
        $stmt->execute();
        $_SESSION['success_message'] = "Blog post deleted successfully.";
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting blog post: " . $e->getMessage();
    }
}

// Redirect back to the page where the user came from
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;
?>
