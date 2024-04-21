<?php
session_start();
require_once 'db.php';

// Check if the user is logged in
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

</head>


<body>
    <?php include 'header.php'; ?>
    
    <div class="heading-page header-text">
        <section class="about-page-heading">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-content">
                            <h4>APIIT Blog</h4>
                            <h2>About Us</h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <section class="about-us">
        <div class="container">
            <h4 class="aboutus-heading-text">Who Are We?</h4>
            <p class="about-content">Reputable higher education institution APIIT Sri Lanka was founded in 1999 in
                collaboration with Asia Pacific University of Technology and Innovation in Malaysia and Staffordshire
                University in the UK.We offer undergraduate and graduate degree programs in business management,
                computing/IT, and law from Staffordshire University in the UK.</p>

            <div class="icon-row">
                <div class="icon-item">
                    <i class="fas fa-thumbs-up"></i>
                    <p>Years of Excellence</p>
                    <p id="icon-text">24+</p>
                </div>
                <div class="icon-item">
                    <i class="fas fa-user-graduate"></i>
                    <p>Graduates</p>
                    <p id="icon-text">4000+</p>
                </div>
                <div class="icon-item">
                    <i class="fas fa-school"></i>
                    <p>Enrollments</p>
                    <p id="icon-text">1000+</p>
                </div>
                <div class="icon-item">
                    <i class="fas fa-book"></i>
                    <p>Programmes</p>
                    <p id="icon-text">15+</p>
                </div>
                <div class="icon-item">
                    <i class="fas fa-handshake"></i>
                    <p>Partners</p>
                    <p id="icon-text">5+</p>
                </div>
            </div>

            <h4 class="aboutus-heading-text">Our Purpose</h4>
            <p class="about-content">To transform the educational environment by inspiring a passion for the inherent
                worth of education in order to develop leaders who improve people's lives.</p>
            <p class="about-content">"Inspire love for learning" in all of our students and through them into society is
                the mission statement of APIIT Sri Lanka.</p>

            <h4 class="aboutus-heading-text">Schools</h4>
            <div class="card-container">
                <div class="school-card">
                    <img src="assets/images/city-campus.jpg" alt="City Campus">
                    <h5>APIIT City Campus</h5>
                    <p>Location: Union Place, Colombo</p>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.765612991586!2d79.85865397373263!3d6.918600618448528!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596d3cb8fe07%3A0x2b0ae2edd563a661!2sAsia%20Pacific%20Institute%20of%20Information%20Technology%20(APIIT)!5e0!3m2!1sen!2slk!4v1710394070052!5m2!1sen!2slk"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="school-card">
                    <img src="assets/images/law-school.jpg" alt="Law School">
                    <h5>APIIT Law School</h5>
                    <p>Location: Access Towers, Colombo</p>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.752166791424!2d79.85488907373268!3d6.920203418428854!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae25914a897a0d5%3A0x8de5fb5759da10c0!2sAPIIT%20Law%20School!5e0!3m2!1sen!2slk!4v1710394258515!5m2!1sen!2slk"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="school-card">
                    <img src="assets/images/apiit-kandy.jpg" alt="APIIT Kandy">
                    <h5>APIIT Kandy Campus</h5>
                    <p>Location: Peradeniya Road, Kandy</p>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6460510553056!2d80.6174665737356!3d7.2810528138993424!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3673f5e22ab3b%3A0xcbaef11262bec73!2sAPIIT%20Kandy%20Campus!5e0!3m2!1sen!2slk!4v1710394022377!5m2!1sen!2slk"
                        style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>