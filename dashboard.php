<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Raw SQL Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="navbar">
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="create_blog.php">Create Blog</a>
        <a href="logout.php" style="float:right;">Logout (<?php echo $_SESSION['user_name']; ?>)</a>
    </div>

    <div class="container" style="max-width: 1000px;">
        <h2>My Blogs</h2>
        <?php if (isset($_GET['msg'])) echo "<p class='success'>" . htmlspecialchars($_GET['msg']) . "</p>"; ?>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td>
                        <?php if ($row['image']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" width="50" alt="Image">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $row['is_publish'] ? 'Published' : 'Draft'; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="edit_blog.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                        <a href="toggle_publish.php?id=<?php echo $row['id']; ?>" class="btn">
                            <?php echo $row['is_publish'] ? 'Unpublish' : 'Publish'; ?>
                        </a>
                        <a href="blog_details.php?id=<?php echo $row['id']; ?>" class="btn" target="_blank">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php if ($result->num_rows == 0) echo "<p>You haven't created any blogs yet.</p>"; ?>
    </div>
</body>
</html>
