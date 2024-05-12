<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Check if the user is logged in and fetch their full name from the session
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
    $userId = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Assign the user's full name to a variable for later use
        $organizingParty = $user['full_name'];
    } else {
        // If the user does not exist, handle the error accordingly
        echo "Error: User not found.";
        exit();
    }
} else {
    // If the user is not logged in, redirect them to the login page
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate']; // Separate date input
    $eventTime = $_POST['eventTime']; // Separate time input
    $eventVenue = $_POST['eventVenue'];

    try {
        // Insert data into the database
        $stmt = $db->prepare("INSERT INTO events (name, event_date, event_time, venue, organizing_party) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$eventName, $eventDate, $eventTime, $eventVenue, $organizingParty]);
        
        // Redirect back to event management page with a notification
        header("Location: event_management.php?success=Event created successfully");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
