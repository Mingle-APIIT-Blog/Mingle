<?php
session_start();
require_once 'db.php';

// Initialize form data
$blogTitle = '';
$bloggerName = '';
$blogContent = '';

// Check if session variables exist and populate form fields
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    $blogTitle = isset($form_data['blogTitle']) ? $form_data['blogTitle'] : '';
    $bloggerName = isset($form_data['bloggerName']) ? $form_data['bloggerName'] : '';
    // Assuming blogImage is not stored in session due to its size
    $blogContent = isset($form_data['blogContent']) ? $form_data['blogContent'] : '';
    unset($_SESSION['form_data']); // Clear session data after populating form fields
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Blog Post Form</title>
    <link rel="stylesheet" href="style.css">
    <style>
    select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Create Your Blog Post</h1>
        <form id="blogForm" method="post" action="upload-blog.php" enctype="multipart/form-data"
            onsubmit="return validateForm()">
            <label for="blogTitle">Blog Title:</label><br>
            <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($blogTitle); ?>"><br>

            <!-- Blog category selection -->
            <label for="category">Category:</label><br>
            <select id="category" name="category">
                <?php
                // Retrieving user's faculty information from the database
                if (isset($_SESSION['user_id'])) {
                    $userId = $_SESSION['user_id'];
                    $stmt = $db->prepare("SELECT user_faculty FROM users WHERE id = :user_id");
                    $stmt->bindParam(':user_id', $userId);
                    $stmt->execute();
                    $userFaculty = $stmt->fetchColumn();

                    // To determine category options based on user's faculty
                    switch ($userFaculty) {
                        case 'Computing':
                            $categories = ['Programming', 'Networking', 'Software Engineering','Events', 'Sports', 'General'];
                            break;
                        case 'Business':
                            $categories = ['Finance', 'Marketing', 'Management', 'Events', 'Sports', 'General'];
                            break;
                        case 'Law':
                            $categories = ['Criminal Law', 'Civil Law', 'International Law','Events', 'Sports', 'General'];
                            break;
                        default:
                            $categories = ['Events', 'Sports', 'General'];
                            break;
                    }

                    // Prepare category options for HTML select element
                    $categoryOptions = '';
                    foreach ($categories as $category) {
                        $categoryOptions .= "<option value='$category'>$category</option>";
                    }

                    // Output category options
                    echo $categoryOptions;
                }
                ?>
            </select><br>

            <label for="blogImage">Blog Image</label><br>
            <input type="file" id="blogImage" name="blogImage" accept=".png, .jpg, .jpeg, .svg, .gif"><br>

            <label for="blogContent">Blog Content:</label><br>
            <textarea id="blogContent" name="blogContent" rows="6"
                cols="50"><?php echo htmlspecialchars($blogContent); ?></textarea><br>

            <input class="submit-button" type="submit" value="Upload">
            <button class="back-button" onclick="goBack()">Back</button>

        </form>
        <div id="errorMessage" class="error-message"></div>
    </div>

    <script>
        function validateForm() {
            var title = document.getElementById('blogTitle').value.trim();
            var image = document.getElementById('blogImage').value.trim();
            var content = document.getElementById('blogContent').value.trim();

            if (title === '' || image === '' || content === '') {
                document.getElementById('errorMessage').innerText = 'Please fill in all fields.';
                return false; // To prevent form submission with empty fields
            }

            var allowedExtensions = /(\.png|\.jpg|\.jpeg|\.svg|\.gif)$/i;
            if (!allowedExtensions.exec(image)) {
                document.getElementById('errorMessage').innerText =
                'Please upload PNG, JPG, JPEG, SVG, or GIF images only.';
                return false; // To prevent form submission with incorrect image extensions
            }

            return true;
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>

</html>