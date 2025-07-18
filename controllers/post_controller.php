<?php
session_start();
require_once '../includes/db.php';

// Create Post
if (isset($_POST['create_post'])) {
    $user_id = $_SESSION['user_id'];
    $content = $_POST['content'];
    $image_path = null;
    $file_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        $image_path = "uploads/images/" . basename($_FILES["image"]["name"]);
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "../uploads/files/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_path = $target_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        $file_path = "uploads/files/" . basename($_FILES["file"]["name"]);
    }

    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, image_path, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $content, $image_path, $file_path);
    $stmt->execute();
    $stmt->close();

    header("Location: ../index.php");
}

// Delete Post
if (isset($_GET['delete_post'])) {
    $post_id = $_GET['delete_post'];
    $user_id = $_SESSION['user_id'];

    // Check if the user owns the post
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();

    if ($owner_id == $user_id) {
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: ../index.php");
}
?>
