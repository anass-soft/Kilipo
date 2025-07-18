<?php
session_start();
require_once '../includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin.php");
    exit();
}

// Ban User
if (isset($_POST['ban_user'])) {
    $user_id = $_POST['user_id'];
    $duration = $_POST['duration'];
    $reason = $_POST['reason'];
    $admin_username = $_SESSION['admin_username'];

    // Get admin ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $admin_username);
    $stmt->execute();
    $stmt->bind_result($admin_id);
    $stmt->fetch();
    $stmt->close();

    // Calculate expiration date
    $expires_at = null;
    if ($duration !== 'forever' && $duration !== 'until_i_say_so') {
        $expires_at = date('Y-m-d H:i:s', strtotime("+" . str_replace(['h', 'd'], [' hours', ' days'], $duration)));
    }

    // Insert ban into the database
    $stmt = $conn->prepare("INSERT INTO bans (user_id, admin_id, reason, duration, expires_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $admin_id, $reason, $duration, $expires_at);
    $stmt->execute();
    $stmt->close();

    // Log the action
    $action = "Banned user with ID $user_id for $duration. Reason: $reason";
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_username, action, ip_address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $action, $ip_address);
    $stmt->execute();
    $stmt->close();

    header("Location: ../admin.php");
}

// Delete Post
if (isset($_GET['delete_post'])) {
    $post_id = $_GET['delete_post'];
    $admin_username = $_SESSION['admin_username'];

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    // Log the action
    $action = "Deleted post with ID $post_id";
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_username, action, ip_address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $action, $ip_address);
    $stmt->execute();
    $stmt->close();

    header("Location: ../admin.php");
}

// Delete Comment
if (isset($_GET['delete_comment'])) {
    $comment_id = $_GET['delete_comment'];
    $admin_username = $_SESSION['admin_username'];

    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->close();

    // Log the action
    $action = "Deleted comment with ID $comment_id";
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_username, action, ip_address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_username, $action, $ip_address);
    $stmt->execute();
    $stmt->close();

    header("Location: ../admin.php");
}
?>
