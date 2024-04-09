<?php
session_start();
require_once 'db.php';

// Retrieve all blog posts from the database
$stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id ORDER BY b.id DESC");
$stmt->execute();
$blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
?>


<!DOCTYPE html>
<html lang="en">
<!--Just UI no user authentication etc...-->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="TemplateMo">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Mingle:: APIIT's Blog</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <link rel="stylesheet" href="assets/css/owl.css">


    <style>
    #calendar {
        margin: 50px auto;
        /* Center the calendar with top and bottom margin */
        max-width: 800px;
        /* Set max width to ensure it's fully visible */
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        position: relative;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #calendar-container {
        margin-bottom: 4%;

    }
    h3 {
        color: #0d7b99;
        text-align: center;
        font-size: 16px;
        font-weight: bold;
    }

   
    </style>

    <!-- Calendar js -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

</head>


<body>

    <?php include 'header.php'; ?>
    <div class="heading-page header-text">
        <section class="about-page-heading">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-content">
                            <h4>APIIT Blog</h4>
                            <h2>Our Events</h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    

    <div id='calendar-container'>
        <div id='calendar'></div>
    </div>

    <!-- Modal Popup -->
    <div id="eventDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Event Details</h3>
            <div id="eventDetails"></div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: 'events.php', // Path to your PHP script
            eventClick: function(info) {
                var eventDetails = "<strong>Event:</strong> " + info.event.title + "<br>" +
                    "<strong>Date:</strong> " + info.event.start.toLocaleDateString() + "<br>" +
                    "<strong>Time:</strong> " + info.event.start.toLocaleTimeString() + "<br>" +
                    "<strong>Venue:</strong> " + info.event.extendedProps.venue + "<br>" +
                    "<strong>Organizing Party:</strong> " + info.event.extendedProps
                    .organizing_party;
                document.getElementById('eventDetails').innerHTML = eventDetails;
                document.getElementById('eventDetailsModal').style.display = 'block';
            }
        });
        calendar.render();

        // Close the modal when clicking on the close button
        var closeBtn = document.getElementsByClassName("close")[0];
        closeBtn.onclick = function() {
            document.getElementById('eventDetailsModal').style.display = 'none';
        };
    });
    </script>


    <!--footer-->
    <?php include 'footer.php'; ?>


    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/isotope.js"></script>
    <script src="assets/js/accordions.js"></script>

    <script language="text/Javascript">
    cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
    function clearField(t) { //declaring the array outside of the
        if (!cleared[t.id]) { // function makes it static and global
            cleared[t.id] = 1; // you could use true and false, but that's more typing
            t.value = ''; // with more chance of typos
            t.style.color = '#fff';
        }
    }
    </script>

</body>

</html>