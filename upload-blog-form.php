<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
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

    /* Checkbox container */
    .checkbox-container {
        display: inline-block;
        margin-right: 20px;
    }

    /* Checkbox label */
    .checkbox-container label {
        display: block;
    }

    /* Checkbox input */
    .checkbox-container input {
        margin-right: 5px;
    }
    .low-font-weight {
        font-weight: lighter;
    }
    .column-container {
        width: 33.33%;
        float: left;
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

            <!-- Blog category selection using checkboxes -->
            <label>Category:</label><br>
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
                        $categories = ['Programming', 'Networking', 'Software Engineering', 'Artificial Intelligence', 'Cybersecurity', 'Web Development', 'Quality Assurance' , 'Mobile Applications', 'Information Systems', 'Cloud Computing', 'Computing', 'All Faculties'];
                        break;
                    case 'Business':
                        $categories = ['Finance', 'Marketing', 'Management', 'Entrepreneurship', 'Human Resources', 'Supply Chain Management', 'E-commerce', 'Digital Marketing', 'Business Analytics', 'Operations Management', 'Business', 'All Faculties'];
                        break;
                    case 'Law':
                        $categories = ['Criminal Law', 'Civil Law', 'International Law', 'Constitutional Law', 'Environmental Law', 'Intellectual Property Law', 'Sri Law', 'Human Rights Law', 'Tax Law', 'Employment Law', 'Law', 'All Faculties'];
                        break;
                    case 'Club':
                            $categories = ['Computing', 'Business', 'Law', 'Events', 'Sports', 'All Faculties'];
                            break;
                }

                echo "<div class='column-container'>";
                $categories_per_column = ceil(count($categories) / 3); // Calculate how many categories should be in each column
                $current_column_count = 0;

                foreach ($categories as $category) {
                    if ($current_column_count % $categories_per_column == 0 && $current_column_count != 0) {
                        echo "</div><div class='column-container'>"; // Start a new column
                    }

                    echo "<label class='low-font-weight'><input type='checkbox' name='categories[]' value='$category'> $category</label><br>";
                    $current_column_count++;
                }
                

                echo "</div>"; // Close the last column
            }
            ?><br><br>

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
            var checkboxes = document.querySelectorAll('input[name="categories[]"]:checked');

            if (title === '' || image === '' || content === '') {
                document.getElementById('errorMessage').innerText = 'Please fill in all fields.';
                return false; // To prevent form submission with empty fields
            }

            if (checkboxes.length === 0) {
            document.getElementById('errorMessage').innerText = 'Please select at least one category.';
            return false; // To prevent form submission with no categories selected
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
