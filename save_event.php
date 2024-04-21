<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $eventName = $_POST['eventName'];
    $eventDate = $_POST['eventDate']; // Separate date input
    $eventTime = $_POST['eventTime']; // Separate time input
    $eventVenue = $_POST['eventVenue'];
    $organizingParty = $_POST['organizingParty'];

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
