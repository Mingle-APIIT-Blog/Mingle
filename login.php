<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

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

        // Fetch and store user's faculty information
        $_SESSION['user_faculty'] = $user['user_faculty'];

        // Redirect the user to the index.php page
        header('Location: index.php'); exit;
    } else {
        // Log the failed login attempt for debugging
        error_log("Login failed for email: $email"); 

        // Redirect back to the login page with an error query parameter
        header('Location: login.html?error=1'); exit;
    }
} else {
    // If not a POST request or if essential fields are missing, redirect to the login page
    header('Location: login.html'); exit;
}
?>
