<?php
session_start();
require_once 'db.php';

// Retrieve all blog posts from the database
$stmt = $db->prepare("SELECT b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id ORDER BY b.id DESC");
$stmt->execute();
$blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
?>


<!DOCTYPE html>
<html lang="en">
<!--Just UI no user authentication etc...-->

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


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-stand-blog.css">
    <link rel="stylesheet" href="assets/css/owl.css">

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
              <h2>Inspire Love For Living</h2>
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
        // Define the number of posts per page
        $postsPerPage = 4;

        // Get the current page number
        $page = isset($_GET['page']) && is_numeric($_GET['page']) ? $_GET['page'] : 1;

        // Calculate the offset for the query
        $offset = ($page - 1) * $postsPerPage;

        // Query to retrieve paginated blog posts from the database
        $stmt = $db->prepare("SELECT SQL_CALC_FOUND_ROWS b.*, u.full_name AS author_name FROM blog b JOIN users u ON b.user_id = u.id ORDER BY creationDate DESC LIMIT :offset, :postsPerPage");
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':postsPerPage', $postsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Total number of posts
        $totalPosts = $db->query("SELECT FOUND_ROWS() as total")->fetch()['total'];

        // Calculate total number of pages
        $totalPages = ceil($totalPosts / $postsPerPage);
        ?>

        <div class="col-lg-8">
          <div class="all-blog-posts">
            <div class="row">
              <?php foreach ($blogPosts as $post) : ?>
                <div class="col-lg-6">
                  <div class="blog-post">
                    <div class="blog-thumb">
                      <?php if (!empty($post['blogImage'])) : ?>
                        <img src="data:image/jpeg;base64,<?php echo $post['blogImage']; ?>" height="250px" alt="Blog Image">
                      <?php endif; ?>
                    </div>
                    <div class="down-content">
                      <a href="post_details.php?postId=<?php echo $post['id']; ?>">
                        <h4><?php echo htmlspecialchars($post['blogTitle']); ?></h4>
                      </a>
                      <ul class="post-info">
                        <li><a href="#"><?php echo htmlspecialchars($post['author_name']); ?></a></li>
                        <li><a href="#"><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></a></li>
                      </ul>
                      <?php
                      // Extracting the first sentence from the blog content
                      $firstSentence = strtok($post['blogContent'], '.');
                      echo "<p>$firstSentence</p>";
                      ?>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="col-lg-12">
              <ul class="page-numbers">
                <?php $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page, default to 1 if not set

                // Start generating page numbers
                for ($i = 1; $i <= $totalPages; $i++) :
                ?>
                  <li <?php if ($i === $currentPage) echo 'class="active"'; ?>>
                    <a href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                  </li>
                <?php endfor; ?>

              </ul>
            </div>

          </div>
        </div>

        <div class="col-lg-4">
          <div class="sidebar">
            <div class="row">
              <!--div class="col-lg-12">
                <div class="sidebar-item search">
                  <form id="search_form" name="gs" method="GET" action="#">
                    <input type="text" name="q" class="searchText" placeholder="type to search..." autocomplete="on">
                  </form>
                </div>
              </div-->
              <div class="col-lg-12">
                <div class="sidebar-item recent-posts">
                  <div class="sidebar-heading">
                    <h2>Recent Posts</h2>
                  </div>
                  <div class="content">
                    <ul>
                      <?php
                      // Query to retrieve recent posts from the database
                      $stmt = $db->prepare("SELECT id, blogTitle, creationDate FROM blog ORDER BY creationDate DESC LIMIT 3");
                      $stmt->execute();
                      $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                      // Loop through the recent posts and display them
                      foreach ($recentPosts as $post) {
                      ?>
                        <li>
                          <a href="post_details.php?postId=<?php echo $post['id']; ?>">
                            <h5><?php echo htmlspecialchars($post['blogTitle']); ?></h5>
                            <span><?php echo date('M d, Y', strtotime($post['creationDate'])); ?></span>
                          </a>
                        </li>
                      <?php
                      }
                      ?>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <style>
            .sidebar-item {
              margin-bottom: 20px;
              /* Adjust the value as needed */
            }
          </style>
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
            cleared[t.id] = 1; // you could use true and false, but that's more typing
            t.value = ''; // with more chance of typos
            t.style.color = '#fff';
        }
    }
    </script>

</body>

</html>