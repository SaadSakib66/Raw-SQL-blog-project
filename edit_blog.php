<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$blog_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if blog belongs to user
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Blog not found or permission denied.");
}
$blog = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $short_desc = $_POST['short_description'];
    $long_desc = $_POST['long_description'];
    $is_publish = isset($_POST['is_publish']) ? 1 : 0;
    
    $image = $blog['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    }

    $update_stmt = $conn->prepare("UPDATE blogs SET title=?, short_description=?, long_description=?, image=?, is_publish=? WHERE id=? AND user_id=?");
    $update_stmt->bind_param("ssssiii", $title, $short_desc, $long_desc, $image, $is_publish, $blog_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: dashboard.php?msg=Blog updated successfully");
        exit();
    } else {
        $error = "Error: " . $update_stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Blog - Raw SQL Blog</title>
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
        <h2>Edit Blog</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <label>Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
            
            <label>Short Description:</label>
            <textarea name="short_description" rows="3" required><?php echo htmlspecialchars($blog['short_description']); ?></textarea>
            
            <label>Long Description:</label>
            <textarea name="long_description" rows="8" required><?php echo htmlspecialchars($blog['long_description']); ?></textarea>
            
            <label>Image:</label><br>
            <?php if ($blog['image']): ?>
                <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" width="100" alt="Current Image"><br>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">
            <small>Leave blank to keep current image</small><br><br>
            
            <label>
                <input type="checkbox" name="is_publish" value="1" <?php echo $blog['is_publish'] ? 'checked' : ''; ?>> Publish
            </label>
            <br><br>
            
            <button type="submit">Update Blog</button>
        </form>
    </div>
</body>
</html>
