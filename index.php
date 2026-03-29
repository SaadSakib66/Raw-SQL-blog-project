<?php
session_start();
require 'config.php';

$query = "SELECT blogs.*, users.name as author_name FROM blogs JOIN users ON blogs.user_id = users.id WHERE blogs.is_publish = 1 ORDER BY blogs.created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Raw SQL Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="create_blog.php">Create Blog</a>
            <a href="logout.php" style="float:right;">Logout (<?php echo $_SESSION['user_name']; ?>)</a>
        <?php else: ?>
            <a href="login.php" style="float:right;">Login</a>
            <a href="register.php" style="float:right;">Register</a>
        <?php endif; ?>
    </div>

    <div class="container" style="max-width: 800px;">
        <h2>Latest Blogs</h2>
        <?php if ($result->num_rows == 0) echo "<p>No published blogs yet.</p>"; ?>
        
        <?php while ($blog = $result->fetch_assoc()): ?>
            <div class="blog-card">
                <?php if ($blog['image']): ?>
                    <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Image" style="width:100%; max-height: 400px; object-fit: cover;">
                <?php endif; ?>
                <h3><a href="blog_details.php?id=<?php echo $blog['id']; ?>"><?php echo htmlspecialchars($blog['title']); ?></a></h3>
                <p class="meta">By <?php echo htmlspecialchars($blog['author_name']); ?> on <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($blog['short_description'])); ?></p>
                <a href="blog_details.php?id=<?php echo $blog['id']; ?>" class="btn">Read More</a>
            </div>
            <hr>
        <?php endwhile; ?>
    </div>
</body>
</html>
