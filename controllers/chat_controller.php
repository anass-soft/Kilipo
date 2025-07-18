<?php
session_start();
require_once '../includes/db.php';

// Send message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $image_path = null;
    $file_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../uploads/chat_images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        $image_path = "uploads/chat_images/" . basename($_FILES["image"]["name"]);
    }

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "../uploads/chat_files/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_path = $target_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
        $file_path = "uploads/chat_files/" . basename($_FILES["file"]["name"]);
    }

    $stmt = $conn->prepare("INSERT INTO chats (sender_id, receiver_id, message, image_path, file_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $image_path, $file_path);
    $stmt->execute();
    $stmt->close();
}

// Get messages
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_GET['receiver_id'];
    $last_timestamp = isset($_GET['last_timestamp']) ? $_GET['last_timestamp'] : 0;

    $stmt = $conn->prepare("SELECT chats.*, users.username as sender_username FROM chats JOIN users ON chats.sender_id = users.id WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND UNIX_TIMESTAMP(timestamp) > ? ORDER BY timestamp ASC");
    $stmt->bind_param("iiiid", $sender_id, $receiver_id, $receiver_id, $sender_id, $last_timestamp);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    $new_last_timestamp = $last_timestamp;
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
        $new_last_timestamp = strtotime($row['timestamp']);
    }

    echo json_encode(['messages' => $messages, 'last_timestamp' => $new_last_timestamp]);
}
?>
