<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $fullName = $_POST['full-name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate form data
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Invalid email format.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $username)) {
        $errorMessage = "Full name must contain only alphabetic characters.";
    } else {
        // Check if the email already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errorMessage = "Email already exists.";
        } else {
            // Check if the clubname already exists
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE full_name = ?");
            $stmt->execute([$fullName]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errorMessage = "Club already exists";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert data into database
                $stmt = $db->prepare("INSERT INTO users (username, password, user_type, user_faculty, full_name, email) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $hashedPassword, 'patron', 'club', $fullName, $email]);

                // Redirect to success page 
                header("Location: club_user_management.php");
                exit();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Club Creation</title>
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

        .heading-page {
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="heading-page header-text">
        <!-- to get the space between the header and club user management -->
    </div>
    <form id="club-form" action="" method="post">
        <input type="hidden" name="user-type" value="Club">
        <h4>Club Patron Registration</h4>
        <?php if(isset($errorMessage)): ?>
        <div class="error-message">
            <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>
        
        
        <div class="form-group">
            <label for="club-username">Club Name</label>
            <input type="text" class="form-control" id="club-username" name="full-name" required>
        </div>
        <div class="form-group">
            <label for="club-full-name">Patron's Full Name</label>
            <input type="text" class="form-control" id="club-full-name" name="username" required>
        </div>

        <div class="form-group">
            <label for="club-email">Email</label>
            <input type="email" class="form-control" id="club-email" name="email" required>
        </div>
        <div class="form-group">
            <label for="club-password">Password</label>
            <input type="password" class="form-control" id="club-password" name="password" required>
        </div>
        <div class="form-group">
            <label for="club-confirm-password">Confirm Password</label>
            <input type="password" class="form-control" id="club-confirm-password" name="confirm-password" required>
        </div>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js">
    </script>
    <script>
        document.getElementById('club-form').addEventListener('submit', function (event) {
            var username = document.getElementById('club-username').value;
            var fullName = document.getElementById('club-full-name').value;
            var email = document.getElementById('club-email').value;
            var password = document.getElementById('club-password').value;
            var confirmPassword = document.getElementById('club-confirm-password').value;

            // Check if passwords match
            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                event.preventDefault();
                return false;
            }

            // Check if email includes the required domain
            if (!email.includes("apiit.lk")) {
                alert("Email must be a valid APIIT email address.");
                event.preventDefault();
                return false;
            }

            // Check if full name contains only alphabets
            if (!/^[a-zA-Z ]+$/.test(username)) {
                alert("Full name must contain only alphabetic characters.");
                event.preventDefault();
                return false;
            }
        });
    </script>


    <?php include 'footer.php'; ?>
</body>

</html>
