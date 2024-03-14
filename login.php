<?php
session_start();
require_once 'db.php'; // Ensure this path correctly points to your database connection file

// Check if the form has been submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    // Trim email and password to remove any accidental whitespace
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepare a statement to prevent SQL injection attacks
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Since hashing is not used (not recommended), directly compare the plaintext passwords
    if ($user && $password == $user['password']) {
        // Authentication successful, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];

        // Redirect the user based on their type
        switch ($user['user_type']) {
            case 'Student':
                header('Location: student_dashboard.php'); exit;
            case 'Lecturer':
                header('Location: lecturer_dashboard.php'); exit;
            case 'Alumni':
                header('Location: alumni_dashboard.php'); exit;
            case 'Admin':
                header('Location: admin_dashboard.php'); exit;
        }
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
