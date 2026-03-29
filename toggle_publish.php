<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
    
    // Check if user owns the blog
    $check_stmt = $conn->prepare("SELECT is_publish FROM blogs WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $blog_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $new_status = $row['is_publish'] ? 0 : 1;
        $update_stmt = $conn->prepare("UPDATE blogs SET is_publish = ? WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("iii", $new_status, $blog_id, $user_id);
        $update_stmt->execute();
    }
}

header("Location: dashboard.php?msg=Status updated successfully");
exit();
?>
