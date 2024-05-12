<?php
// Include database connection script
require_once('db.php');

// Check if the booking ID is provided
if (isset($_POST['booking_id'])) {
    $bookingId = $_POST['booking_id'];

    try {
        // Update booking status in the database
        $stmt = $db->prepare("UPDATE appointments SET status = 'declined' WHERE id = ?");
        $stmt->execute([$bookingId]);
        // Respond with success status
        echo json_encode(['status' => 'success']);
    } catch(PDOException $e) {
        // Respond with error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    // Respond with error message if booking ID is not provided
    echo json_encode(['status' => 'error', 'message' => 'Booking ID not provided']);
}
?>
