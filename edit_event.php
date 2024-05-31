<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// To check if event ID is provided in the URL
if (isset($_GET['id'])) {
    $eventId = $_GET['id'];
    
    // To fetch event details from the database
    $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    // To check if the event exists
    if (!$event) {
        echo "Event not found.";
        exit;
    }

    // Handling form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieving form data
        $eventName = $_POST['eventName'];
        $eventDate = $_POST['eventDate']; 
        $eventTime = $_POST['eventTime']; 
        $eventVenue = $_POST['eventVenue'];
        $organizingParty = $_POST['organizingParty'];

        try {
            // Update event data in the database
            $stmt = $db->prepare("UPDATE events SET name = ?, event_date = ?, event_time = ?, venue = ?, organizing_party = ? WHERE id = ?");
            $stmt->execute([$eventName, $eventDate, $eventTime, $eventVenue, $organizingParty, $eventId]);
            echo "Event updated successfully";

            // Redirect to event management page after updating the event
            header("Location: event_management.php");
            exit;
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Event ID not provided.";
    exit;
}


// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="TemplateMo">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <link rel="stylesheet" href="assets/css/owl.css">

    <style>
        h2 {
            color: #333;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
            margin: 50px auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button[type="submit"] {
            background-color: #0d7b99;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            color: #0d7b99;
            background-color: #fff;
            border: 1px solid #0d7b99;
            border-color: #0d7b99;

        }

        .header-1 {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="heading-page header-text">
        <!-- to get the space between the header and event management -->
    </div>

    <form id="eventForm" action="" method="post">
        <h2>Edit Event</h2>
        <label for="eventName">Event Name:</label>
        <input type="text" id="eventName" name="eventName" value="<?php echo $event['name']; ?>" required>

        <label for="eventDate">Event Date:</label>
        <input type="date" id="eventDate" name="eventDate" value="<?php echo $event['event_date']; ?>" required>

        <label for="eventTime">Event Time:</label>
        <input type="time" id="eventTime" name="eventTime" value="<?php echo $event['event_time']; ?>" required>

        <label for="eventVenue">Venue:</label>
        <input type="text" id="eventVenue" name="eventVenue" value="<?php echo $event['venue']; ?>" required>

        
        <input type="text" id="organizingParty" name="organizingParty" value="<?php echo $event['organizing_party']; ?>" readonly style="display:none;" required>



        <button type="submit">Update Event</button>
    </form>
    
    <?php include 'footer.php'; ?>