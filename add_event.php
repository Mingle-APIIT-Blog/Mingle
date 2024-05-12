<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

// Check if the user is logged in and fetch their full name from the database
if ($isLoggedIn) {
    // Establish a database connection 
    require_once('db.php');

    // Retrieve the user's full name from the database
    $userId = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT full_name FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists in the database
    if ($user) {
        // Assign the user's full name to a session variable for later use
        $_SESSION['full_name'] = $user['full_name'];
    } else {
        // If the user does not exist, redirect them to the login page
        header('Location: login.php');
        exit;
    }
} else {
    // If the user is not logged in, redirect them to the login page
    header('Location: login.php');
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Event Creation</title>
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
        margin-bottom: 5px;
        color: #555;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
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
    <form id="eventForm" action="save_event.php" method="post">
        <h2>Save New Event</h2>
        <label for="eventName">Event Name:</label>
        <input type="text" id="eventName" name="eventName" required><br><br>

        <label for="eventDate">Event Date:</label>
        <input type="date" id="eventDate" name="eventDate" required><br><br>

        <label for="eventTime">Event Time:</label>
        <input type="time" id="eventTime" name="eventTime" required><br><br>

        <label for="eventVenue">Venue:</label>
        <input type="text" id="eventVenue" name="eventVenue" required><br><br>

        <label for="organizingParty">Organizing Club:</label>
<input type="text" id="organizingParty" name="organizingParty" value="<?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : ''; ?>" readonly><br><br>

        <button type="submit">Save Event</button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
    </script>
    <script>
    $(document).ready(function() {
        // Get the current date
        var today = new Date().toISOString().split('T')[0];

        // Set the minimum date for eventDate input field
        $('#eventDate').attr('min', today);
    });
    </script>
 <?php include 'footer.php'; ?>