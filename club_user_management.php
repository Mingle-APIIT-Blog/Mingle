<?php
session_start();
require_once('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Check if user type is Admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    // Redirect to unauthorized page or homepage
    header('Location: unauthorized.php');
    exit;
}

// To check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

// Query to select users with user_type "Patron"
$stmt = $db->prepare("SELECT * FROM users WHERE user_type = 'Patron'");
$stmt->execute();
$patronUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="heading-page header-text">
        <!-- to get the space between the header and event management -->
    </div>
    
    <div class="event-container">

    <div class="club-user-management-container">
        <h2>Club Patron User Management</h2>
        <a href="club_form_view.php" class="add-event-btn">Add Club</a>
        

    
    <table class="table">
        <thead>
            <tr>
                <th>Club name</th>
                <th>Club Patron</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patronUsers as $user) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td class="action-btns">
                                <!-- Edit button with link to edit_club.php -->
                                <a href="edit_club.php?id=<?php echo $user['id']; ?>" class="edit-btn">Edit</a>
                                <!-- Delete button with link to delete_club.php and confirmation dialog -->
                                <a href="delete_club.php?id=<?php echo $user['id']; ?>" class="delete-btn"
                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>

                            </td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
            </div>
    
    
    <!-- footer -->
    <?php include 'footer.php'; ?>

</body>

</html>