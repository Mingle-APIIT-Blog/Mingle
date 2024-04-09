<?php
session_start();
require_once 'db.php'; // Ensure this path correctly points to your database connection file

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    // Trim email to remove any accidental whitespace
    $email = trim($_POST['email']);
    
    // Fetch the user's record from the database
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify if user exists and if the hashed password matches
    if ($user && password_verify($_POST['password'], $user['password'])) {
        // Authentication successful, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];

        // Redirect the user to the index.php page
        header('Location: index.php'); exit;
    } else {
        // Log the failed login attempt for debugging
        error_log("Login failed for email: $email"); // Check your server's error log

        // Redirect back to the login page with an error query parameter
        header('Location: login.html?error=1'); exit;
    }
} else {
    // If not a POST request or if essential fields are missing, redirect to the login page
    header('Location: login.html'); exit;
}
?>
