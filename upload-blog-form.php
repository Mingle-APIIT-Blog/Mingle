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
    <link rel="stylesheet" type="text/css" href="styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="file"] {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        textarea {
            resize: vertical;
        }
        .submit-button{
            display: inline-block;
            font-size: 16px;
            padding: 8px 10px;
            border: 1px solid #1ea5c3;
            border-radius: 5px;
            background-color: #1ea5c3;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button {
            font-size: 16px;
            display: inline-block;
            padding: 8px 10px;
            border: 1px solid #1ea5c3;
            border-radius: 5px;
            background-color: transparent;
            color: #1ea5c3;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }
        input[type="submit"]:hover,
        .back-button:hover {
            background-color: #dbd9d9;
            border-color: #fff;
            color: #1ea5c3;
        }
        .error-message {
            color: #ff0000;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Your Blog Post</h1>
        <form id="blogForm" method="post" action="upload-blog.php" enctype="multipart/form-data" onsubmit="return validateForm()">
            <label for="blogTitle">Blog Title:</label><br>
            <input type="text" id="blogTitle" name="blogTitle" value="<?php echo htmlspecialchars($blogTitle); ?>"><br>
            
            <label for="blogImage">Blog Image:</label><br>
            <input type="file" id="blogImage" name="blogImage"><br>
            
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

            return true; 
        }

        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
