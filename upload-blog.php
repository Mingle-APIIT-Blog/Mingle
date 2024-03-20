<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blogTitle']) && isset($_FILES['blogImage']) && isset($_POST['blogContent'])) {
    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $blogTitle = htmlspecialchars($_POST['blogTitle']);
        $blogContent = htmlspecialchars($_POST['blogContent']);

        // Get user ID from session
        $userId = $_SESSION['user_id'];
        $fullName = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';

        // Handle file upload
        $imageTmpName = $_FILES['blogImage']['tmp_name'];
        $imageData = file_get_contents($imageTmpName);
        $imageBase64 = base64_encode($imageData);

        // Get current date and time
        $currentDate = date('Y-m-d H:i:s');

        // Prepare SQL statement to insert data into the database
        $stmt = $db->prepare("INSERT INTO blog (user_id, blogTitle, blogImage, blogContent, creationDate) VALUES (:user_id, :blogTitle, :blogImage, :blogContent, :creationDate)");

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':blogTitle', $blogTitle);
        $stmt->bindParam(':blogImage', $imageBase64); // Store image data as base64 string
        $stmt->bindParam(':blogContent', $blogContent);
        $stmt->bindParam(':creationDate', $currentDate);

        // Execute the statement
        try {
            $stmt->execute();
            $_SESSION['success_message'] = "Blog post submitted successfully.";
            header('Location: display_blog.php'); // Redirect to display_blog.php after successful submission
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error: " . $e->getMessage();
            header('Location: index.php'); // Redirect to index.php if an error occurs
            exit;
        }
    } else {
        $_SESSION['error_message'] = "You must be logged in to submit a blog post.";
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
