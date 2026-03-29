<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $short_desc = $_POST['short_description'];
    $long_desc = $_POST['long_description'];
    $is_publish = isset($_POST['is_publish']) ? 1 : 0;
    $user_id = $_SESSION['user_id'];

    $image = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    }

    $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, short_description, long_description, image, is_publish) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssi", $user_id, $title, $short_desc, $long_desc, $image, $is_publish);

    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Blog created successfully");
        exit();
    } else {
        $error = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Blog - Raw SQL Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="create_blog.php">Create Blog</a>
        <a href="logout.php" style="float:right;">Logout</a>
    </div>

    <div class="container">
        <h2>Create a New Blog</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" required>
            
            <label>Short Description:</label>
            <textarea name="short_description" rows="3" required></textarea>
            
            <label>Long Description:</label>
            <textarea name="long_description" rows="8" required></textarea>
            
            <label>Image:</label>
            <input type="file" name="image" accept="image/*" required>
            
            <label>
                <input type="checkbox" name="is_publish" value="1" checked> Publish immediately
            </label>
            <br><br>
            
            <button type="submit">Create Blog</button>
        </form>
    </div>
</body>
</html>
