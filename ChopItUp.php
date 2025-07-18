<?php
session_start();
include 'partials/header.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all users except the current user
$current_user_id = $_SESSION['user_id'];
$users_result = $conn->query("SELECT id, username FROM users WHERE id != $current_user_id");
?>

<div class="container">
    <h2>Chat</h2>
    <div class="chat-container">
        <div class="users-list">
            <h3>Users</h3>
            <ul>
                <?php while ($user = $users_result->fetch_assoc()) : ?>
                    <li class="user" data-id="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></li>
                <?php endwhile; ?>
            </ul>
        </div>
        <div class="chat-window">
            <div class="chat-header">
                <h3 id="chat-with">Select a user to chat with</h3>
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will be loaded here via AJAX -->
            </div>
            <div class="chat-form">
                <form id="chat-form" action="controllers/chat_controller.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="receiver_id" id="receiver_id">
                    <input type="text" name="message" id="message" placeholder="Type a message...">
                    <input type="file" name="image" id="image">
                    <input type="file" name="file" id="file">
                    <button type="submit">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
