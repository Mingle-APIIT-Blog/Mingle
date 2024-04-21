<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Check if event ID is provided in the URL
if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    try {
        // Delete the event from the database
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$eventId]);
        echo "Event deleted successfully";

        // Redirect to event management page after deleting the event
        header("Location: event_management.php");
        exit;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Event ID not provided.";
    exit;
}
?>
