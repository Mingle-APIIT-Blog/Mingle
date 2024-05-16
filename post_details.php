<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
require_once 'db.php';

// Check if postId is provided in the URL
if (isset($_GET['postId'])) {
    $postId = $_GET['postId'];

    // Retrieve the blog post details based on postId
    $stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the post exists
    if ($post) {
?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="TemplateMo">
            <link href="https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <title>Mingle:: APIIT's Blog</title>

            <!-- Bootstrap core CSS -->
            <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

            <!-- Additional CSS Files -->
            <link rel="stylesheet" href="assets/css/fontawesome.css">
            <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
            <link rel="stylesheet" href="assets/css/owl.css">
        </head>

        <body>

            <!-- Header -->
            <?php include 'header.php'; ?>

            <!-- Banner Starts Here -->
            <div class="heading-page header-text">
                <section class="page-heading">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="text-content">
                                    <h4><?php echo htmlspecialchars($post['author_name']); ?></h4>
                                    <h2><?php  echo htmlspecialchars_decode($post['blogTitle']); ?></h2>

                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Banner Ends Here -->

            <section class="blog-posts grid-system">
                <div class="container">

                    <div class="blog-post">
                        <div class="blog-thumb">
                            <?php if (!empty($post['blogImage'])) : ?>
                                <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" alt="Blog Image">
                            <?php endif; ?>
                        </div>
                        <div class="down-content">
                         
                            <span><?php  echo htmlspecialchars_decode($post['blogTitle']); ?></span>
                           
                                <ul class="post-info">
                                    <li><a href="#"><?php echo htmlspecialchars($post['author_name']); ?></a></li>
                                    <li><a href="#"><?php echo htmlspecialchars($post['category']); ?></a></li> 
                                    <li><a href="#"><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></a></li>
                                </ul>
                                
                                <p><?php echo nl2br(htmlspecialchars_decode($post['blogContent'])); ?></p>

                        </div>
                    </div>

                </div>
            </section>

            <!-- Footer -->
            <footer>
                <!-- Your footer content here -->
            </footer>

            <!-- Include Bootstrap JS -->
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

            <!-- Additional Scripts -->
            <script src="assets/js/custom.js"></script>
            <script src="assets/js/owl.js"></script>
            <script src="assets/js/slick.js"></script>
            <script src="assets/js/isotope.js"></script>
            <script src="assets/js/accordions.js"></script>
        </body>

        <footer class="footer">
            <div class="container-footer">
                <div class="row-footer">
                    <div class="footer-col">
                        <h4>QUICK LINKS</h4>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="alumni_view.php">Alumni</a></li>
                            
                            <li><a href="aboutUs.html">About Us</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>AREAS OF STUDY</h4>
                        <ul>
                            <li><a href="https://apiit.lk/undergraduate/business-degrees/">Business</a></li>
                            <li><a href="https://apiit.lk/undergraduate/computing-degrees/">IT</a></li>
                            <li><a href="https://apiit.lk/undergraduate/law-degrees/">Law</a></li>
                            <li><a href="https://apiit.lk/foundation/">Foundation</a></li>
                            <li><a href="https://apiit.lk/courses/institute-of-chartered-accountants-in-england-and-wales-icaew/">Professional</a></li>
                        </ul>
                        <br>
                        
                    </div>

                    <div class="footer-col">
                        <h4>FOLLOW US</h4>
                        <div class="social-links">
                            <a href="https://www.facebook.com/APIITofficial" target="_blank"><i class="fa-brands fa-facebook"></i>
                                <a href="https://twitter.com/apiitsl?lang=en" target="_blank"><i class="fa-brands fa-twitter"></i>
                                    <a href="https://www.instagram.com/apiitsl/" target="_blank"><i class="fa-brands fa-instagram"></i>
                                        <a href="https://www.youtube.com/@APIITedu" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                                        <a href="https://www.linkedin.com/company/apiitsl" target="_blank"><i class="fa-brands fa-linkedin"></i></a>
                        </div>
                    </div>
                    <br>
                    <div class="footer-col">
                        <ul>
                            <li> <img src="assets/images/apiit-logo.png" alt="" width=""></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="copyrights">
                <p>copyright &copy;2024 powered by MACH<span></span></p>
            </div>

        </footer>

        </html>

<?php
        exit(); // Stop further execution of the script after displaying the post
    }
}

// If postId is not provided or the post doesn't exist, redirect to the index page
header("Location: index.php");
exit();
?>