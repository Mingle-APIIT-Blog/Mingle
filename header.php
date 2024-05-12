<!-- Header -->
<header class="header-1">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="assets/images/BrandLogo.png" width="180px" height="60px"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'aboutUs.php' ? 'active' : ''; ?>">
                        <a class="nav-link" href="aboutUs.php">About Us</a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'alumni_view.php' ? 'active' : ''; ?>">
                        <a class="nav-link" href="alumni_view.php">Alumni</a>
                    </li>
                    <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'calendar.php' ? 'active' : ''; ?>">
                        <a class="nav-link" href="calendar.php">Events</a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo getDashboardLink($_SESSION['user_type']); ?>">Dashboard</a>
                        </li>
                        <button class="login-button">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </button>
                    <?php else: ?>
                        <button class="login-button">
                            <a class="nav-link" href="login.html">Login</a>
                        </button>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<?php
// Define the getDashboardLink function
function getDashboardLink($userType) {
    switch ($userType) {
        case 'Student':
            return 'student_dashboard.php';
        case 'Lecturer':
            return 'lecturer_dashboard.php';
        case 'Alumni':
            return 'alumni_dashboard.php';
        case 'Admin':
            return 'admin_dashboard.php';
        case 'Patron':
            return 'club_patron_dashboard.php';
        default:
            return ''; // Return an empty string for unknown user types
    }
}
?>
