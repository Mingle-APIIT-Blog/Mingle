<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);



// Include database connection script
require_once('db.php');


// Check if user is logged in and has the correct user type
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'Student';


// Check if user is logged in and has the correct user type
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Student') {
    // Redirect to login page if not logged in as lecturer
    header('Location: login.php');
    exit;
}


$success_message = '';
$error_message = '';

// Check if the student has submitted a booking request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_booking'])) {
    // Retrieve selected lecturer, date, time slot, and message
    $lecturer_id = $_POST['lecturer'];
    $booking_date = $_POST['booking_date'];
    $timeslot = $_POST['timeslot'];
    $message = $_POST['message'];

    // Check if the lecturer already has an appointment scheduled for the same date and time slot
    $stmt = $db->prepare("SELECT COUNT(*) AS count FROM appointments WHERE lecturer_id = ? AND date = ? AND timeslot = ? AND status = 'accepted'");
    $stmt->execute([$lecturer_id, $booking_date, $timeslot]);
    $existing_appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_appointment['count'] > 0) {
        $error_message = "The lecturer already has an appointment scheduled for this time.";
    } else {
        // Insert booking request into the database
        try {
            $stmt = $db->prepare("INSERT INTO appointments (student_id, lecturer_id, date, timeslot, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $lecturer_id, $booking_date, $timeslot, $message]);
            $success_message = "Booking request submitted successfully!";
            // Redirect to prevent form resubmission on page refresh
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}



// Query to fetch available lecturers from the same faculty as the student and include a specific lecturer
$stmt = $db->prepare("
    SELECT id, username 
    FROM users 
    WHERE user_type = 'lecturer' 
    AND (user_faculty = (SELECT user_faculty FROM users WHERE id = ?) 
    OR email = 'kaushali@apiit.lk')
    GROUP BY id, username
");
$stmt->execute([$_SESSION['user_id']]);
$lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);


$_SESSION['username'] = isset($_SESSION['username']) ? $_SESSION['username'] : ''; // Set username from session data

// Query to fetch appointments specific to the current student
$stmt = $db->prepare("SELECT appointments.*, users.username AS lecturer_name, students.username AS student_name FROM appointments INNER JOIN users ON appointments.lecturer_id = users.id INNER JOIN users AS students ON appointments.student_id = students.id WHERE appointments.student_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="TemplateMo">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Student Dashboard</title>

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

.header-1 {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

        /* body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            
        } */
        .booking-container {
        max-width: 1200px;
        margin: 20px auto;
        margin-bottom: 5%;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
        /* .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        } */
        h1, h2 {
            color: #333;
        }
        
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        select, input[type="date"] {
            width: calc(100% - 16px); 
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        #submitRequest {
            width: 100%;
            padding: 10px 20px;
            background-color: #1ea5c3;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #submitRequest:hover {
            background-color: #0056b3;
        } 
        table {
    width: 80%; /* Reduce the width to 80% */
    margin: 20px auto; /* Center the table */
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f2f2f2; /* Background color for header row */
    font-weight: bold;
}

tr:nth-child(even) {
    background-color: #fff; /* Set background color of even rows to white */
}

tr:hover {
    background-color: #f2f2f2; /* Hover effect color */
}



        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .card {
            max-width: 40%;
            margin: 0 auto; /* Centering the card */
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .message-column {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
        
        @media (max-width: 768px) {
            .booking-container {
                padding: 10px;
            }
            .card {
                max-width: 95%;
            }
            /* Additional responsive styles */
        }

        @media (max-width: 576px) {
            table {
                width: 80%;
                margin: 20px auto;
            }
        }

        .table-wrapper {
            max-width: 100%;
            overflow-x: auto;
            align-self: center;
        }

        .table-wrapper table th:nth-child(4),
.table-wrapper table td:nth-child(4) {
    width: 200px; 
    max-width: 200px;
    word-wrap: break-word;
}

        


    </style>


</head>
<body>
<?php include 'header.php'; ?>

<div class="heading-page header-text">
    <!-- to get the space between the header and event management -->
</div>
    <?php if(isset($success_message)): ?>
        <div><?php echo $success_message; ?></div>
    <?php endif; ?>

   

    <div class="booking-container">
    <div class="card">
            <div style="text-align: center;">
            <h2>Book an Appointment</h2>
            </div>
<br>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="lecturer">Select Lecturer / Head of SSS:</label>
                <select name="lecturer" id="lecturer" required>
                <option value="">Select</option>
                    <?php foreach($lecturers as $lecturer): ?>
                        <option value="<?php echo $lecturer['id']; ?>"><?php echo $lecturer['username']; ?></option>
                    <?php endforeach; ?>
                </select><br><br>

                <label for="booking_date">Select Date:</label>
                <input type="date" name="booking_date" id="booking_date" required><br><br>

                <label for="timeslot">Select Time Slot:</label>
                <select name="timeslot" id="timeslot" required>
                    <option value="">Select a time slot</option>
                
                    <option value="8:00 AM - 9:00 AM">8:00 AM - 9:00 AM</option>
                    <option value="9:00 AM - 10:00 AM">9:00 AM - 10:00 AM</option>
                    <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                    <option value="11:00 AM - 12:00 PM">11:00 AM - 12:00 PM</option>
                    <option value="12:00 PM - 1:00 PM">12:00 PM - 1:00 PM</option>
                    <option value="1:00 PM - 2:00 PM">1:00 PM - 2:00 PM</option>
                    <option value="2:00 PM - 3:00 PM">2:00 PM - 3:00 PM</option>
                    <option value="3:00 PM - 4:00 PM">3:00 PM - 4:00 PM</option>
                    <option value="4:00 PM - 5:00 PM">4:00 PM - 5:00 PM</option>
                    <option value="5:00 PM - 6:00 PM">5:00 PM - 6:00 PM</option>


         </select><br><br>

        <label for="message">Reason for Booking:</label><br>
        <textarea name="message" id="message" rows="3" style="width: calc(100% - 12px);" placeholder="Please mention the reason for this booking and the type of meeting preffered (Online/Physical)." required></textarea><br><br>
        <?php if(!empty($error_message)): ?>
    <div id="error-container" class="alert alert-danger" role="alert"><?php echo $error_message; ?></div>
<?php endif; ?>

        <button id='submitRequest' type="submit" name="submit_booking">Submit Booking Request</button>
    </form>
    </div>

    <br><br>
   
    <div style="text-align: center;">
    <h2>Your Appointments</h2>
</div>
<?php if (!empty($appointments)): ?>
    <div class="table-wrapper">
    <table>
        <tr>
            <th>Lecturer</th>
            <th>Date</th>
            <th>Time Slot</th>
            <th style="width: 200px;">Message</th>
            <th>Status</th>
        </tr>
        <?php foreach($appointments as $appointment): ?>
            <tr>
                <td><?php echo $appointment['lecturer_name']; ?></td>
                <td><?php echo date('Y-m-d', strtotime($appointment['date'])); ?></td>
                <td><?php echo $appointment['timeslot']; ?></td>
                <td><?php echo $appointment['message']; ?></td>
                <td><?php echo ucfirst($appointment['status']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
<?php else: ?>
    <p>No appointments found.</p>
<?php endif; ?>

</div>

<!-- footer -->
<?php include 'footer.php'; ?>


<script>
    // Get today's date
    var today = new Date().toISOString().split('T')[0];

    // Set the minimum date to today
    document.getElementById('booking_date').setAttribute('min', today);
</script>
    
</body>
</html>
