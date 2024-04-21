<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();
require_once 'db.php';

// To check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;

// Retrieve all blog posts from the database
if ($isLoggedIn && isset($_SESSION['user_faculty'])) {
    // If the user is logged in and their faculty is set, filter the blog posts by faculty
    $userFaculty = $_SESSION['user_faculty'];

    $stmt = $db->prepare("SELECT b.*, u.full_name AS author_name 
                          FROM blog b 
                          JOIN users u ON b.user_id = u.id 
                          WHERE u.user_faculty = :user_faculty 
                          ORDER BY b.id DESC");
    $stmt->bindParam(':user_faculty', $userFaculty);
    $stmt->execute();
    $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If the user is not logged in or their faculty is not set, retrieve all blog posts
    $stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id ORDER BY b.id DESC");
    $stmt->execute();
    $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Error handling for database query
if (!$stmt) {
    echo "Failed to retrieve blog posts.";
    exit;
}


// Initialize variables for recent posts and search results
$recentPosts = [];
$searchResults = [];

// Check if the search query is set
if (isset($_GET['q']) && !empty($_GET['q'])) {
    // Get the search query
    $searchQuery = $_GET['q'];

    // Prepare a SQL query to search for blog posts by title
    $stmt = $db->prepare("SELECT id, blogTitle, creationDate, category FROM blog WHERE blogTitle LIKE :query ORDER BY creationDate DESC");
    $stmt->bindValue(':query', "%$searchQuery%", PDO::PARAM_STR);
    $stmt->execute();
    $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no search query, retrieve recent posts
    $stmt = $db->prepare("SELECT id, blogTitle, creationDate, category FROM blog ORDER BY creationDate DESC LIMIT 3");
    $stmt->execute();
    $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
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

    <style>
      /* pagination css */
      .page-numbers li.active{
        background-color: #007bff;
        color: #fff;
        }
    </style>
    

</head>


<body>

    <?php include 'header.php'; ?>


   <!-- Banner Starts Here -->
  <div class="heading-page header-text">
    <section class="page-heading">
      <div class="container">
        <div class="row">
          <div class="col-lg-12">
            <div class="text-content">
              <h4>APIIT Blog</h4>
              <h2>Inspire Love For Learning</h2>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>



  <section class="blog-posts grid-system">
    <div class="container">
      <div class="row">
        <?php
        // Defined the number of posts per page
        $postsPerPage = 4;

        // To get the current page number
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

        // To calculate the offset for the query
        $offset = ($page - 1) * $postsPerPage;

        // Total number of posts
        $totalPosts = count($blogPosts);

        // To calculate total number of pages
        $totalPages = ceil($totalPosts / $postsPerPage);
        ?>

        <div class="col-lg-8">
          <div class="all-blog-posts">
            <div class="row">
              <?php 
              // Ro determine the range of blog posts to display for the current page
              $start = ($page - 1) * $postsPerPage;
              $end = $start + $postsPerPage - 1;
              
              // Looping through the blog posts to display for the current page
              for ($i = $start; $i <= $end && $i < $totalPosts; $i++) :
                $post = $blogPosts[$i];
              ?>
              <div class="col-lg-6">
                <div class="blog-post">
                  <div class="blog-thumb">
                    <?php if (!empty($post['blogImage'])) : ?>
                      <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" height="250px" alt="Blog Image">
                    <?php endif; ?>
                  </div>
                  <div class="down-content">
                    <a href="post_details.php?postId=<?php echo $post['id']; ?>">
                      <span><?php echo htmlspecialchars($post['blogTitle']); ?></span>
                    </a> 
                    <ul class="post-info">
                       <li><a href="#"><?php echo htmlspecialchars($post['author_name']); ?></a></li>
                       <li><a href="#"><?php echo htmlspecialchars($post['category']); ?></a></li> 
                       <li><a href="#"><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></a></li>
                    </ul>
                    <?php
                    // Extracting only the first sentence from the blog content
                    $firstSentence = strtok($post['blogContent'], '.');
                    echo "<p>$firstSentence</p>";
                    ?>
                  </div>
                </div>
              </div>
              <?php endfor; ?>
            </div>
            <div class="col-lg-12">
              <ul class="page-numbers">
                <?php 
                // To generate pagination links
                for ($i = 1; $i <= $totalPages; $i++) :
                ?>
                  <li <?php if ($i === $page) echo 'class="active"'; ?>>
                    <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
                    <section class="sidebar">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Search Bar -->
                                    <div class="sidebar-item search">
                                        <form id="search_form" name="gs" method="GET" action="#">
                                            <input type="text" name="q" class="searchText"
                                                placeholder="Type to search..." autocomplete="on">
                                        </form>
                                    </div>
                                    <div class="sidebar-item recent-posts">
                                        <div class="sidebar-heading">
                                            <h2>Search Results</h2>
                                        </div>
                                        <div class="content">
                                            <ul>
                                                <?php if (isset($_GET['q'])) { // Display search results ?>
                                                <?php foreach ($searchResults as $result) { ?>
                                                <li>
                                                    <a href="post_details.php?postId=<?php echo $result['id']; ?>">
                                                        <h5><?php echo htmlspecialchars($result['blogTitle']); ?></h5>
                                                        <span><?php echo htmlspecialchars($result['category']); ?> |
                                                            <?php echo date('M d, Y', strtotime($result['creationDate'])); ?></span>
                                                    </a>
                                                </li>
                                                <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="sidebar-item recent-posts">
                                        <div class="sidebar-heading">
                                            <h2>Recent Posts</h2>
                                        </div>
                                        <div class="content">
                                            <ul>
                                                <?php
                                                // Query to retrieve recent posts from the database
                                                $stmt = $db->prepare("SELECT id, blogTitle, creationDate, category FROM blog ORDER BY creationDate DESC LIMIT 3");
                                                $stmt->execute();
                                                $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                // Loop through the recent posts and display them
                                                foreach ($recentPosts as $post) {
                                                ?>
                                                <li>
                                                    <a href="post_details.php?postId=<?php echo $post['id']; ?>">
                                                        <h5><?php echo htmlspecialchars($post['blogTitle']); ?></h5>
                                                        <span><?php echo htmlspecialchars($post['category']); ?> |
                                                            <?php echo date('M d, Y', strtotime($post['creationDate'])); ?></span>
                                                    </a>
                                                </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

      </div>
    </div>
  </section>

    <!--footer-->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Additional Scripts -->
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/owl.js"></script>
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/isotope.js"></script>
    <script src="assets/js/accordions.js"></script>

    <script language="text/Javascript">
    cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
    function clearField(t) { //declaring the array outside of the
        if (!cleared[t.id]) { // function makes it static and global
            cleared[t.id] = 1; 
            t.value = ''; 
            t.style.color = '#fff';
        }
    }
    </script>

</body>

</html>
