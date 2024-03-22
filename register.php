<?php
session_start();
require_once 'db.php'; // Ensure this path correctly points to your database connection file

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user-type']) && isset($_POST['full-name']) && isset($_POST['username'])  && isset($_POST['email']) && isset($_POST['confirm-password'])) {
    // Trim form inputs to remove any accidental whitespace
    $user_type = $_POST['user-type'];
    $full_name = trim($_POST['full-name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $confirm_password = trim($_POST['confirm-password']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back to the register page with an error query parameter for invalid email format
        header('Location: register.html?error=email'); 
        exit;
    }

    // Validate email domain
    if (strpos($email, 'apiit.lk') === false) {
        // Redirect back to the register page with an error query parameter for invalid email domain
        header('Location: register.html?error=email_domain'); 
        exit;
    }

    // Check if password and confirm password match
    if ($confirm_password != $_POST['password']) {
        // Redirect back to the register page with an error query parameter for password mismatch
        header('Location: register.html?error=password_mismatch'); 
        exit;
    }

    // Check if username already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing_user) {
        // Redirect back to the register page with an error query parameter for existing username
        header('Location: register.html?error=username_exists'); 
        exit;
    }

    // Check if email already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $existing_email = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing_email) {
        // Redirect back to the register page with an error query parameter for existing email
        header('Location: register.html?error=email_exists'); 
        exit;
    }

    // If all validations pass, insert the user data into the database
    $stmt = $db->prepare("INSERT INTO users (username, password, user_type, full_name, email) VALUES (?, ?, ?, ?, ?)");
    //$hashed_password = password_hash($confirm_password, PASSWORD_DEFAULT); // Hash the password for security
    $stmt->execute([$username, $confirm_password, $user_type, $full_name, $email]);

    // Redirect to a success page or login page after registration
    header('Location: login.html'); 
    exit;
} else {
    // If not a POST request or if essential fields are missing, redirect to the registration page with an error query parameter
    header('Location: register.html?error=missing_fields'); 
    exit;
}
?>
