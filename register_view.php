<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <style>
    .user-type-buttons {
        margin-bottom: 20px;
    }

    .user-type-button {
        margin-right: 10px;
    }

    .registration-form {
        display: none;
    }

    .registration-form.active {
        display: block;
    }

    .form-heading {
        margin-top: 20px;
        margin-bottom: 10px;
    }

    .container {
        margin-bottom: 5%;
    }

    h4 {
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 5%;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="login-pg-logos">
                    <img src="assets/images/apiit-logo.png" alt="" width="auto" height="75px">
                    <img src="assets/images/BrandLogo.png" alt="" width="250px" height="100px">
                </div>
                <div class="card">
                    <h5 class="card-header">Registration</h5>
                    <div class="card-body">
                        <div class="form-heading"></div>

                        <?php
                        // Display error message if exists
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
                            unset($_SESSION['error']); // Clear error message
                        }
                        ?>
                        <div class="user-type-buttons">
                            <button class="btn btn-primary user-type-button"
                                onclick="showForm('student')">Student</button>
                            <button class="btn btn-primary user-type-button"
                                onclick="showForm('lecturer')">Lecturer</button>
                            <button class="btn btn-primary user-type-button"
                                onclick="showForm('alumni')">Alumni</button>
                        </div>

                        <!-- Student Registration Form -->
                        <form id="student-form"
                            class="registration-form <?php echo isset($_SESSION['valid_data']) && $_SESSION['valid_data']['user-type'] == 'Student' ? 'active' : ''; ?>"
                            action="register.php" method="post">
                            <input type="hidden" name="user-type" value="Student">
                            <h4>Student Registration</h4>
                            <div class="form-group">
                                <label for="student-full-name">Full Name</label>
                                <input type="text" class="form-control" id="student-full-name" name="full-name"
                                    value="<?php echo isset($_SESSION['valid_data']['full-name']) ? $_SESSION['valid_data']['full-name'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="student-username">Username</label>
                                <input type="text" class="form-control" id="student-username" name="username"
                                    value="<?php echo isset($_SESSION['valid_data']['username']) ? $_SESSION['valid_data']['username'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="student-email">APIIT Email address</label>
                                <input type="email" class="form-control" id="student-email" name="email"
                                    value="<?php echo isset($_SESSION['valid_data']['email']) ? $_SESSION['valid_data']['email'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="student-password">Password</label>
                                <input type="password" class="form-control" id="student-password" name="password"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="student-confirm-password">Confirm Password</label>
                                <input type="password" class="form-control" id="student-confirm-password"
                                    name="confirm-password" required>
                            </div>
                            <div class="form-group">
                                <label for="student-faculty">Faculty</label>
                                <select class="form-control" id="student-faculty" name="user-faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="Computing">Computing</option>
                                    <option value="Business">Business</option>
                                    <option value="Law">Law</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>

                        <!-- Lecturer Registration Form -->
                        <form id="lecturer-form"
                            class="registration-form <?php echo isset($_SESSION['valid_data']) && $_SESSION['valid_data']['user-type'] == 'Lecturer' ? 'active' : ''; ?>"
                            action="register.php" method="post">
                            <input type="hidden" name="user-type" value="Lecturer">
                            <h4>Lecturer Registration</h4>
                            <div class="form-group">
                                <label for="lecturer-full-name">Full Name</label>
                                <input type="text" class="form-control" id="lecturer-full-name" name="full-name"
                                    value="<?php echo isset($_SESSION['valid_data']['full-name']) ? $_SESSION['valid_data']['full-name'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer-username">Username</label>
                                <input type="text" class="form-control" id="lecturer-username" name="username"
                                    value="<?php echo isset($_SESSION['valid_data']['username']) ? $_SESSION['valid_data']['username'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer-email">Email</label>
                                <input type="email" class="form-control" id="lecturer-email" name="email"
                                    value="<?php echo isset($_SESSION['valid_data']['email']) ? $_SESSION['valid_data']['email'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer-password">Password</label>
                                <input type="password" class="form-control" id="lecturer-password" name="password"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer-confirm-password">Confirm Password</label>
                                <input type="password" class="form-control" id="lecturer-confirm-password"
                                    name="confirm-password" required>
                            </div>
                            <div class="form-group">
                                <label for="lecturer-faculty">Faculty</label>
                                <select class="form-control" id="lecturer-faculty" name="user-faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="Computing">Computing</option>
                                    <option value="Business">Business</option>
                                    <option value="Law">Law</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>


                        <!-- Alumni Registration Form -->
                        <form id="alumni-form"
                            class="registration-form <?php echo isset($_SESSION['valid_data']) && $_SESSION['valid_data']['user-type'] == 'Alumni' ? 'active' : ''; ?>"
                            action="register.php" method="post">
                            <input type="hidden" name="user-type" value="Alumni">
                            <h4>Alumni Registration</h4>
                            <div class="form-group">
                                <label for="alumni-full-name">Full Name</label>
                                <input type="text" class="form-control" id="alumni-full-name" name="full-name"
                                    value="<?php echo isset($_SESSION['valid_data']['full-name']) ? $_SESSION['valid_data']['full-name'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-username">Username</label>
                                <input type="text" class="form-control" id="alumni-username" name="username"
                                    value="<?php echo isset($_SESSION['valid_data']['username']) ? $_SESSION['valid_data']['username'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-email">Email</label>
                                <input type="email" class="form-control" id="alumni-email" name="email"
                                    value="<?php echo isset($_SESSION['valid_data']['email']) ? $_SESSION['valid_data']['email'] : ''; ?>"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-password">Password</label>
                                <input type="password" class="form-control" id="alumni-password" name="password"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-confirm-password">Confirm Password</label>
                                <input type="password" class="form-control" id="alumni-confirm-password"
                                    name="confirm-password" required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-association-id">Alumni Association ID</label>
                                <input type="text" class="form-control" id="alumni-association-id"
                                    name="alumni-association-id" required>
                            </div>
                            <div class="form-group">
                                <label for="alumni-faculty">Faculty</label>
                                <select class="form-control" id="alumni-faculty" name="user-faculty" required>
                                    <option value="">Select Faculty</option>
                                    <option value="Computing">Computing</option>
                                    <option value="Business">Business</option>
                                    <option value="Law">Law</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>

                    </div>
                </div>
                <div class="mt-3" style="padding-left: 3px;">
                    <p>Already have an account? Click here to <a href="login.html"
                            style="color: #1ea5c3;">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // To show student form by default
        showForm('student');
    });

    function showForm(userType) {
        // To hide all forms
        document.querySelectorAll('.registration-form').forEach(form => {
            form.classList.remove('active');
        });

        // To show the form corresponding to the selected user type
        document.getElementById(userType + '-form').classList.add('active');
    }
    </script>
</body>

</html>