<?php
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
</head>
<body>
    <div class="container">
        <h1>Create Your Blog Post</h1>
        <form id="blogForm" method="post" action="upload-blog.php" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="blogTitle">Blog Title:</label><br>
            <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($blogTitle); ?>"><br>
            
            <label for="blogImage">Blog Image</label><br>
            <input type="file" id="blogImage" name="blogImage" accept=".png, .jpg, .jpeg, .svg, .gif"><br>
            
            <label for="blogContent">Blog Content:</label><br>
            <textarea id="blogContent" name="blogContent" rows="6" cols="50"><?php echo htmlspecialchars($blogContent); ?></textarea><br>
            
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
                return false; // Prevent form submission
            }

            var allowedExtensions = /(\.png|\.jpg|\.jpeg|\.svg|\.gif)$/i;
            if (!allowedExtensions.exec(image)) {
                document.getElementById('errorMessage').innerText = 'Please upload PNG, JPG, JPEG, SVG, or GIF images only.';
                return false; // Prevent form submission
            }

            return true; 
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
