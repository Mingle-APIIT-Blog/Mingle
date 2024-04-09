<?php
session_start();
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Retrieve user ID from session
$userId = $_SESSION['user_id'];

// Retrieve blog posts associated with the logged-in user from the database
$stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id WHERE b.user_id = :user_id ORDER BY b.id DESC");
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Alumni Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <!-- Add Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this blog post?");
        }
    </script>

<style>
    .row.content {
        min-height: 100vh;
        margin-left: 250px;
    }

    .sidenav {
        background-color: #f1f1f1;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        overflow-y: auto;
    }

    @media screen and (max-width: 767px) {
        .row.content {
            min-height: auto;
            margin-left: 0;
        }

        .sidenav {
            position: static;
            width: 100%;
            margin-bottom: 20px;
            overflow-y: visible;
        }
    }

    @media screen and (min-width: 767px) {
     .col-sm-9{
        margin-left: 140px;
     }
    }


    .sidenav a {
        color: #1ea5c3;
    }

    .sidenav a:hover {
        background-color: #0d7b99;
    }

    .navbar-inverse .navbar-nav>li>a {
        background-color: #1ea5c3;
    }

    .blog-actions button {
        color: #24262b !important;
    }

    .nav>li.active>a,
    .nav>li.active>a:focus,
    .nav>li.active>a:hover {
        background-color: #1ea5c3;
    }

    .navbar.navbar-inverse {
        background-color: #fff !important;
        border-color: transparent !important;
    }

    /* Set menu icon color to white with background color #1ea5c3 */
    .navbar-toggle {
        background-color: #1ea5c3;
        border-color: transparent !important;
        border-radius: 0;
        padding: 12px;
    }

    /* Set background to white when menu icon is toggled */
    .navbar-collapse {
        background-color: #fff;
    }

    .navbar-inverse .navbar-collapse {
        border-top: none;
    }

    /* Set links to black color */
    .navbar-nav>li>a {
        color: #000 !important;
        text-transform: capitalize;
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px !important;
        transition: all 0.3s !important;
        font-family: 'Roboto', sans-serif !important;
        background-color: #fff !important;
        margin-top: -10px;
        margin-bottom: -10px;
    }

    /* Set active link to #1ea5c3 */
    .navbar-nav>.active>a,
    .navbar-nav>.active>a:hover,
    .navbar-nav>.active>a:focus {
        color: #1ea5c3 !important;
        background-color: #fff !important;
        border: transparent !important;
    }

    /* Override background color of menu icon when links are displayed */
    .navbar-default .navbar-toggle.collapsed,
    .navbar-default .navbar-toggle.collapsed:focus,
    .navbar-default .navbar-toggle.collapsed:hover {
        background-color: #1ea5c3 !important;
    }

    .navbar-brand img {
        margin-top: -20px;
    }

    .navbar-toggle:hover {
        background-color: #1ea5c3 !important;
        border-color: transparent !important;
        border-radius: 0 !important;
        padding: 12px !important;
    }

    .navbar.navbar-inverse:hover {
        background-color: #fff !important;
        border-color: transparent !important;
    }

  </style>
</head>

<body>

    <nav class="navbar navbar-inverse visible-xs">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="assets/images/BrandLogo.png" width="180px" height="60px"></a>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="#">Dashboard</a></li>
                    <li>
                        <hr>
                    </li>
                    <li><a href="upload-blog-form.php">Upload Blogs</a></li>
                    <li>
                        <hr>
                    </li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row content">
            <div class="col-sm-3 sidenav hidden-xs">
                <br>
                <img src="assets/images/BrandLogo.png" width="180px" height="60px" style="display: block; margin: 0 auto;">
                <br>
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="#section1">Dashboard</a></li>
                    <li><a href="upload-blog-form.php">Upload Blogs</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul><br>
            </div>
            <br>

            <div class="col-sm-9">
                <div class="well">
                    <h4>Welcome</h4>
                    <p>What do you want to do today?</p>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="well">
                            <div class="content">

                                <div class="blog-container">
                                    <!-- Blog Posts Loop -->
                                    <?php foreach ($blogPosts as $post) : ?>
                                        <div class="blog-post">
                                            <div class="blog-content">
                                                <div class="blog-info">
                                                    <h4><?php echo htmlspecialchars($post['blogTitle']); ?></h4>
                                                    <ul class="post-info">
                                                        <li><a href="#"><?php echo htmlspecialchars($post['author_name']); ?></a></li>
                                                        <li><a href="#"><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></a></li>
                                                    </ul>
                                                </div>
                                                <p><?php echo htmlspecialchars($post['blogContent']); ?></p>
                                                <!-- Floating buttons for edit and delete -->
                                                <div class="blog-actions">
                                                    <form style="display: inline;" method="post" action="delete_blog.php" onsubmit="return confirmDelete();">
                                                        <input type="hidden" name="blog_id" value="<?php echo $post['id']; ?>">
                                                        <button type="submit"><i class="fas fa-trash-alt"></i></button> <!-- Delete icon -->
                                                    </form>

                                                    <a href="edit_blog.php?postId=<?php echo $post['id']; ?>"><button><i class="fas fa-edit"></i></button></a> <!-- Edit icon -->
                                                </div>
                                            </div>
                                            <div class="blog-thumb">
                                                <?php if (!empty($post['blogImage'])) : ?>
                                                    <!-- Display the image if blogImage is not empty -->
                                                    <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" alt="Blog Image">
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>