<?php
session_start();
require_once '../includes/db.php';

if (isset($_POST['update_post'])) {
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];

    // Check if the user owns the post
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id == $user_id) {
        $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE id = ?");
        $stmt->bind_param("si", $content, $post_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../index.php");
}
?>
