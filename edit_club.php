<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

// Check if user_id is set and is a valid integer
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $user_id = $_GET['id'];

    // Retrieve user data from the database
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the user exists
    if (!$user) {
        header("Location: error.php");
        exit();
    }
} else {
    header("Location: error.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $fullName = $_POST['full-name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Validate form data
    $errors = array();
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[a-zA-Z0-9._%+-]+@apiit\.lk$/", $email)) {
        $errors[] = "Invalid email format";
    }
    if (!preg_match("/^[a-zA-Z ]+$/", $username)) {
        $errors[] = "Username must contain only alphabetic characters.";
    }

    // Check if email or full name already exists
    if (empty($errors)) { // Only proceed if no previous errors were found
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "This email is already registered.";
        }

        // Only check for full name if no previous errors were found
        if (empty($errors)) {
            $stmt = $db->prepare("SELECT id FROM users WHERE full_name = ? AND id != ?");
            $stmt->execute([$fullName, $user_id]);
            if ($stmt->fetch()) {
                $errors[] = "This club already exists.";
            }
        }
    }

    // Update user data if there are no errors
    if (empty($errors)) {
        $params = [$username, $fullName, $email];
        $passwordUpdate = "";
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $passwordUpdate = ", password = ?";
            $params[] = $hashedPassword;
        }
        $params[] = $user_id;

        $stmt = $db->prepare("UPDATE users SET username = ?, full_name = ?, email = ?" . $passwordUpdate . " WHERE id = ?");
        $stmt->execute($params);

        // Redirect to success page
        header("Location: club_user_management.php");
        exit();
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Club</title>
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
        input[type="email"],
        input[type="password"] {
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

        .error-message p {
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
    <form id="edit-club-form" action="" method="post">
        <input type="hidden" name="user-type" value="Club">
        <h4>Edit Club</h4>
        <?php if (!empty($errors)) : ?>
            <div class="error-message">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="club-full-name">Club Name</label>
            <input type="text" class="form-control" id="club-full-name" name="full-name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="club-username">Club Patron</label>
            <input type="text" class="form-control" id="club-username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
    
        <div class="form-group">
            <label for="club-email">Email</label>
            <input type="email" class="form-control" id="club-email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="club-password">Password</label>
            <input type="password" class="form-control" id="club-password" name="password">
        </div>
        <div class="form-group">
            <label for="club-confirm-password">Confirm Password</label>
            <input type="password" class="form-control" id="club-confirm-password" name="confirm-password">
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
    <?php include 'footer.php'; ?>
</body>

</html>
