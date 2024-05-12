<?php
session_start();
require_once('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ensure there's an ID provided and it's numeric
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare and execute the delete statement
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    // After deletion, redirect back to the management page
    header("Location: club_user_management.php");
    exit;
} else {
    // Error handling for missing or invalid user ID
    echo "Club ID not provided or invalid.";
    exit;
}
?>
