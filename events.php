<?php
// events.php

// Include database connection script
require_once('db.php');

try {
    // Fetch events from the database
    $sql = "SELECT * FROM events";
    $stmt = $db->query($sql);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $calendar_events = [];

    foreach ($events as $event) {
        $calendar_event = [
            'title' => $event['name'],
            'start' => $event['event_date'] . 'T' . $event['event_time'], // Combine date and time
            'end' => $event['event_date'] . 'T' . $event['event_time'], // Assuming events have no end time
            'venue' => $event['venue'], // You can add additional properties if needed
            'organizing_party' => $event['organizing_party']
        ];
    
        $calendar_events[] = $calendar_event;
    }
    
    echo json_encode($calendar_events);
} catch (PDOException $e) {
    // Handle database connection or query errors
    echo "Error: " . $e->getMessage();
}
?>
