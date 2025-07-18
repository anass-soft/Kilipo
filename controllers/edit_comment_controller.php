<?php
session_start();
require_once '../includes/db.php';

if (isset($_POST['update_comment'])) {
    $comment_id = $_POST['comment_id'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    // Check if the user owns the comment
    $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id == $user_id) {
        $stmt = $conn->prepare("UPDATE comments SET comment = ? WHERE id = ?");
        $stmt->bind_param("si", $comment, $comment_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../index.php");
}
?>
