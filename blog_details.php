<?php
session_start();
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET['id'];

// Get blog details
$stmt = $conn->prepare("SELECT blogs.*, users.name as author_name FROM blogs JOIN users ON blogs.user_id = users.id WHERE blogs.id = ? AND blogs.is_publish = 1");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // If not published, but the user is the owner, still let them view
    if (isset($_SESSION['user_id'])) {
        $stmt_owner = $conn->prepare("SELECT blogs.*, users.name as author_name FROM blogs JOIN users ON blogs.user_id = users.id WHERE blogs.id = ? AND blogs.user_id = ?");
        $stmt_owner->bind_param("ii", $blog_id, $_SESSION['user_id']);
        $stmt_owner->execute();
        $result = $stmt_owner->get_result();
        if ($result->num_rows == 0) {
            die("Blog not found or not published.");
        }
    } else {
        die("Blog not found or not published.");
    }
}
$blog = $result->fetch_assoc();

// Handle new comment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment']) && isset($_SESSION['user_id'])) {
    $comment_text = $_POST['comment'];
    $user_id = $_SESSION['user_id'];
    
    $comment_stmt = $conn->prepare("INSERT INTO comments (blog_id, user_id, comment) VALUES (?, ?, ?)");
    $comment_stmt->bind_param("iis", $blog_id, $user_id, $comment_text);
    $comment_stmt->execute();
    
    header("Location: blog_details.php?id=" . $blog_id);
    exit();
}

// Get comments
$comments_stmt = $conn->prepare("SELECT comments.*, users.name as commenter_name FROM comments JOIN users ON comments.user_id = users.id WHERE comments.blog_id = ? ORDER BY comments.created_at DESC");
$comments_stmt->bind_param("i", $blog_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Raw SQL Blog</title>
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
        <h2><?php echo htmlspecialchars($blog['title']); ?></h2>
        <p class="meta">By <?php echo htmlspecialchars($blog['author_name']); ?> on <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></p>
        
        <?php if ($blog['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($blog['image']); ?>" alt="Image" style="width:100%; max-height: 500px; object-fit: cover;">
        <?php endif; ?>
        
        <div class="content" style="margin-top: 20px;">
            <p><strong><?php echo nl2br(htmlspecialchars($blog['short_description'])); ?></strong></p>
            <p><?php echo nl2br(htmlspecialchars($blog['long_description'])); ?></p>
        </div>

        <hr style="margin: 40px 0;">

        <h3>Comments (<?php echo $comments_result->num_rows; ?>)</h3>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="">
                <textarea name="comment" rows="3" placeholder="Leave a comment..." required style="width: 100%;"></textarea>
                <button type="submit" style="margin-top: 10px;">Post Comment</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Login</a> to leave a comment.</p>
        <?php endif; ?>

        <div class="comments-list" style="margin-top: 20px;">
            <?php while ($comm = $comments_result->fetch_assoc()): ?>
                <div class="comment" style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
                    <p class="meta"><strong><?php echo htmlspecialchars($comm['commenter_name']); ?></strong> - <?php echo date('M d, Y H:i', strtotime($comm['created_at'])); ?></p>
                    <p style="margin: 5px 0 0;"><?php echo nl2br(htmlspecialchars($comm['comment'])); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
