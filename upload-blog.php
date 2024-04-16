<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['blogTitle'], $_FILES['blogImage'], $_POST['blogContent'], $_POST['category'])) {
    // To check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $blogTitle = htmlspecialchars($_POST['blogTitle']);
        $blogContent = htmlspecialchars($_POST['blogContent']);

        // Get user ID from session
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type']; //to redirect user after uploading to specific dashboard depending on user type

        // Handle file upload
        $imageTmpName = $_FILES['blogImage']['tmp_name'];
        $imageData = file_get_contents($imageTmpName);
        $imageBase64 = base64_encode($imageData);

        // Get current date and time
        $currentDate = date('Y-m-d H:i:s');

        // Determine category based on selected value
        $category = htmlspecialchars($_POST['category']);

        // Prepare SQL statement to insert data into the database
        $stmt = $db->prepare("INSERT INTO blog (user_id, blogTitle, blogImage, blogContent, creationDate, category) VALUES (:user_id, :blogTitle, :blogImage, :blogContent, :creationDate, :category)");

        // Bind parameters
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':blogTitle', $blogTitle);
        $stmt->bindParam(':blogImage', $imageBase64); // Store image data as base64 string
        $stmt->bindParam(':blogContent', $blogContent);
        $stmt->bindParam(':creationDate', $currentDate);
        $stmt->bindParam(':category', $category); // Bind category parameter

        // Execute the statement
        try {
            $stmt->execute();
            $_SESSION['success_message'] = "Blog post submitted successfully.";
            // Redirect to the appropriate dashboard based on user type
            switch ($userType) {
                case 'Student':
                    header('Location: student_dashboard.php');
                    break;
                case 'Lecturer':
                    header('Location: lecturer_dashboard.php');
                    break;
                case 'Alumni':
                    header('Location: alumni_dashboard.php');
                    break;
                default:
                    header('Location: index.php'); // Redirect to index.php if user type is unknown
                    break;
            }
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
