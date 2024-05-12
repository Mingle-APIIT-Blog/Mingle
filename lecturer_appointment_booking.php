<?php
session_start();


// Include database connection script
require_once('db.php');


// Check if user is logged in as a lecturer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Lecturer') {
    // Redirect to login page if not logged in as lecturer
    header('Location: login.php');
    exit;
}

// Check if user is logged in and has the correct user type
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'Lecturer';


// Retrieve pending booking requests
$stmt = $db->prepare("SELECT * FROM appointments WHERE lecturer_id = ? AND status = 'pending'");
$stmt->execute([$_SESSION['user_id']]);
$pending_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve accepted appointments for the lecturer
$stmt = $db->prepare("SELECT * FROM appointments WHERE lecturer_id = ? AND status = 'accepted'");
$stmt->execute([$_SESSION['user_id']]);
$accepted_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Format accepted appointments for FullCalendar
$events = array();
foreach($accepted_appointments as $appointment) {
    // Split the time slot into start and end times
    $time_slots = explode(' - ', $appointment['timeslot']);
    $start_time = $time_slots[0]; // Start time
    $end_time = $time_slots[1]; // End time
    
    // Convert start time to a valid datetime format
    $start_datetime = date('Y-m-d H:i:s', strtotime($appointment['date'] . ' ' . $start_time));
    
    // Calculate end datetime based on the duration of the time slot
    $end_datetime = date('Y-m-d H:i:s', strtotime($appointment['date'] . ' ' . $end_time));

    // Add event to the events array
    $events[] = array(
        'title' => $appointment['student_id'], // Assuming you want to display the student ID as the event title
        'start' => $start_datetime,
        'end' => $end_datetime, // Set the end time of the event
        'message' => $appointment['message'],
    );
}

// Handle action on booking request (accept or decline)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $booking_id = $_POST['booking_id'];
    $action = $_POST['action'];

    if ($action === 'declined') {
        try {
            // Update booking status in the database
            $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
            $stmt->execute([$action, $booking_id]);
            $success_message = "Booking request $action successfully!";
            
            // Redirect to refresh the page and reflect the changes
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } catch(PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    } else { // If action is 'accepted'
        // Fetch booking information
        $stmt = $db->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking_info = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the lecturer already has an appointment scheduled for the same time
        $stmt = $db->prepare("SELECT COUNT(*) AS count FROM appointments WHERE lecturer_id = ? AND timeslot = ? AND status = 'accepted'");
        $stmt->execute([$booking_info['lecturer_id'], $booking_info['timeslot']]);
        $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_appointment['count'] > 0) {
            $error_message = "You already have an appointment scheduled for this time.";
        } else {
            try {
                // Update booking status in the database
                $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
                $stmt->execute([$action, $booking_id]);
                $success_message = "Booking request $action successfully!";
                
                // Redirect to refresh the page and reflect the changes
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } catch(PDOException $e) {
                $error_message = "Error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="TemplateMo">
    <title>Lecturer Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">


<style>
    
.header-1 {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        font-family: 'Roboto', sans-serif;
        z-index: 1000;
    }
        .appointment-container {
            width: 80%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        #apppointment-accept {
            padding: 8px 12px;
            border: none;
            background-color: #4CAF50;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
        }
        #apppointment-accept:hover { 
    background-color: darkgreen; 
}
        #apppointment-button {
            padding: 8px 12px;
            border: none;
            background-color: #1ea5c3;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
        }
        #appointment-button:hover {
            background-color: #45a049;
        }
        p {
            text-align: center;
        }
        .modal {
        z-index: 1050; 
    }
    #appointmentModalLabel{
        text-align: center !important;
    }
    #apppointment-decline{
        padding: 8px 12px;
            border: none;
            background-color: red;
            color: #fff;
            cursor: pointer;
            border-radius: 4px;
        }
        #apppointment-decline:hover { 
    background-color: darkred; 
}

.fc-toolbar-title {
    font-size: 20px !important;
}

.table-container table th:nth-child(4),
.table-container table td:nth-child(4) {
    width: 200px; 
    max-width: 200px;
    word-wrap: break-word;
}


/* Responsive*/

@media (max-width: 768px) {
    .appointment-container {
        width: 90%;
        padding: 10px;
    }
    
    table {
        font-size: 14px;
    }

    .table-container {
    overflow-x: auto;
    max-width: 100%;
    
}

.fc-toolbar-title {
    font-size: 10px !important;
}
    
   
}




    </style>
    

</head>
<body>

<?php include 'header.php'; ?>

<div class="heading-page header-text">
    <!-- to get the space between the header and event management -->
</div>

<div class="card mx-auto" style="width: 80%;">
        <div class="card-body">
<div class="appointment-container">

    
    <?php if(isset($success_message)): ?>
        <div><?php echo $success_message; ?></div>
    <?php endif; ?>

    

    <h2 style="font-size: 25px; font-weight: bold;">Pending Booking Requests</h2>
    <div class="table-container">
    
    <?php if(!empty($pending_bookings)): ?>
        <table class="table table-bordered">
            <tr>
                <th>Student</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th style="width: 200px;">Message</th>
                <th>Action</th>
            </tr>
            <?php foreach($pending_bookings as $booking): ?>
                <tr>
                    <td><?php echo $booking['student_id']; ?></td>
                    <td><?php echo $booking['date']; ?></td>
                    <td><?php echo $booking['timeslot']; ?></td>
                    <td><?php echo $booking['message']; ?></td>
                    <td>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                            <button id="apppointment-accept" type="submit" name="action" value="accepted">Accept</button>
                            <button id="apppointment-decline" type="submit" name="action" value="declined">Decline</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <br>
            <div class="alert alert-info" role="alert">
            <h5 style="font-size: 15px;  text-align: center;" >No pending booking requests</h5>
          </div>
        <?php endif; ?>


      <?php if(isset($error_message)): ?>
                <div id="error-container" class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>
    </div>
    </div>


    <br>

    <hr>

    <br>
    <h2 style="font-size: 25px; font-weight: bold;">Accepted Appointments</h2>
    <div id='calendar'></div>

    <!-- Modal for pop up-->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="appointmentModalLabel" style="font-size: 15px;">Appointment Details</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Student:</strong> <span id="modal-student-id"></span></p>
                <p><strong>Date:</strong> <span id="modal-date"></span></p>
                <p><strong>Time Slot:</strong> <span id="modal-time-slot"></span></p>
                <p><strong>Message:</strong> <span id="modal-message"></span></p>
            </div>
            <div class="modal-footer">
                <!-- <button id="appointment-cancel" type="button" class="btn btn-danger">Cancel Appointment</button> -->
                <button id="apppointment-button" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


     

 </div>
 </div>





<!-- Your scripts here -->


  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        slotMinTime: '08:00:00',
        slotMaxTime: '19:00:00',
        //height: '400px',
        height: 'auto',
        aspectRatio: 1.5,
        width: 'auto',
        allDaySlot: false,
        events: <?php echo json_encode($events); ?>,
        eventClick: function(info) {
            var event = info.event;
            var studentId = event.title;
            var timeSlot = event.start;
            var formattedTimeSlot = moment(timeSlot).format('dddd, MMMM Do, h:mm a');
            var message = event.extendedProps.message;

            document.getElementById('modal-student-id').textContent = studentId;
            document.getElementById('modal-date').textContent = moment(timeSlot).format('YYYY-MM-DD');
            document.getElementById('modal-time-slot').textContent = formattedTimeSlot;
            document.getElementById('modal-message').textContent = message;

            $('#appointmentModal').modal('show');
        }
    });

    calendar.render();
});


</script>




<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>



    <br><br>
    
    </div>
    </div>

     
<!-- footer -->
<?php include 'footer.php'; ?>

</body>
</html>
