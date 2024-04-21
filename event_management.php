<?php
session_start();
require_once('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Pagination variables
$eventsPerPage = 5; // Number of events to display per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page, default is 1

// To calculate the offset for SQL query
$offset = ($page - 1) * $eventsPerPage;

// Get filter parameters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;

// To build the WHERE clause for date filtering
$whereClause = '';
if ($startDate && $endDate) {
    $whereClause = "WHERE event_date BETWEEN '$startDate' AND '$endDate'";
} elseif ($startDate) {
    $whereClause = "WHERE event_date >= '$startDate'";
} elseif ($endDate) {
    $whereClause = "WHERE event_date <= '$endDate'";
}

// Retrieve events for the current page with filtering
$stmt = $db->prepare("SELECT * FROM events $whereClause LIMIT :offset, :limit");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $eventsPerPage, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all events into an associative array named $events

// Count total number of events with filtering
$totalEvents = $db->query("SELECT COUNT(*) FROM events $whereClause")->fetchColumn();

// To calculate total number of pages
$totalPages = ceil($totalEvents / $eventsPerPage);

// To check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
?>

<!DOCTYPE html>
<html lang="en">

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

    .event-container {
        max-width: 1200px;
        margin: 20px auto;
        margin-bottom: 5%;
        padding: 20px;
        background-color: #fff;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .action-btns a {
        display: inline-block;
        margin-right: 5px;
        padding: 5px 10px;
        text-decoration: none;
        color: #fff;
        border-radius: 3px;
        transition: background-color 0.3s ease;
    }

    .action-btns a.edit-btn {
        background-color: #007bff;
    }

    .action-btns a.delete-btn {
        background-color: #dc3545;
    }

    .action-btns a:hover {
        background-color: #0d7b99;
    }

    .add-event-btn {
        display: block;
        margin: 20px auto;
        padding: 10px 20px;
        background-color: #0d7b99;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none;
        text-align: center;
        width: 150px;
    }

    .add-event-btn:hover {
        color: #0d7b99;
        background-color: #fff;
        border: 1px solid #0d7b99;
        border-color: #0d7b99;
    }

    /* pagination css */
    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a {
        color: #007bff;
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color 0.3s;
        border: 1px solid #007bff;
        margin: 0 5px;
        border-radius: 5px;
    }

    .pagination a.active {
        background-color: #007bff;
        color: #fff;
    }

    .pagination a:hover:not(.active) {
        background-color: #f2f2f2;
    }

    /* Filter form styling */
    form {
        margin-bottom: 20px;
        text-align: center;
    }

    label {
        margin-right: 10px;
    }

    input[type="date"] {
        padding: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    button[type="submit"] {
        padding: 8px 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 10px;
        color: #0d7b99;
        background-color: #fff;
        border: 1px solid #0d7b99;
        border-color: #0d7b99;
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="heading-page header-text">
        <!-- to get the space between the header and event management -->
    </div>

    <form action="" method="get">
        <label for="startDate">Start Date:</label>
        <input type="date" id="startDate" name="startDate">
        <label for="endDate">End Date:</label>
        <input type="date" id="endDate" name="endDate">
        <button type="submit">Filter</button>
    </form>

    <div class="event-container">
        <h2>Event Management</h2>
        <a href="add_event.php" class="add-event-btn">Add Event</a>
        <div class="table-responsive">
            <table class="table table-bordered">
                <table>
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Venue</th>
                            <th>Organizing Party</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo $event['name']; ?></td>
                            <td><?php echo $event['event_date']; ?></td>
                            <td><?php echo $event['event_time']; ?></td>
                            <td><?php echo $event['venue']; ?></td>
                            <td><?php echo $event['organizing_party']; ?></td>
                            <td class="action-btns">
                                <!-- Edit button with link to edit_event.php -->
                                <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="edit-btn">Edit</a>
                                <!-- Delete button with link to delete_event.php and confirmation dialog -->
                                <a href="delete_event.php?id=<?php echo $event['id']; ?>" class="delete-btn"
                                    onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        </div>
    </div>
    <!-- Pagination links -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <!-- Previous page link -->
        <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>

        <!-- Page number links -->
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?php echo $i; ?>" <?php if ($page == $i) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>

        <!-- Next page link -->
        <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    </div>

    <!-- footer -->
    <?php include 'footer.php'; ?>

</body>

</html>