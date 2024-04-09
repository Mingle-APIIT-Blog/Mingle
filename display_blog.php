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
