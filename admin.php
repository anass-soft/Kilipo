<?php
session_start();
include 'partials/header.php';
require_once 'includes/db.php';

// Admin credentials
$admin_username = "AnassElalouaoui";
$admin_password = "72ul@VL-;F-0";

// Check if admin user exists, if not create it
$result = $conn->query("SELECT id, password FROM users WHERE username = '$admin_username'");
if ($result->num_rows == 0) {
    $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
    $conn->query("INSERT INTO users (username, password) VALUES ('$admin_username', '$hashed_password')");
    $admin_db_password = $hashed_password;
} else {
    $admin_user = $result->fetch_assoc();
    $admin_db_password = $admin_user['password'];
}

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not logged in, show login form
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_POST['username'] === $admin_username && password_verify($_POST['password'], $admin_db_password)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_username;
            // Log successful login
            $action = "Admin login successful";
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_username, action, ip_address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $admin_username, $action, $ip_address);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Invalid credentials";
            // Log failed login attempt
            $action = "Admin login failed";
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $stmt = $conn->prepare("INSERT INTO admin_logs (admin_username, action, ip_address) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['username'], $action, $ip_address);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        ?>
        <div class="container">
            <h2>Admin Login</h2>
            <form action="admin.php" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
        <?php
        exit();
    }
}

// If admin is logged in, show the dashboard
?>
<div class="container">
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['admin_username']; ?>!</p>
    <a href="admin.php?logout=true">Logout</a>

    <!-- Admin content will go here -->
    <div class="admin-sections">
        <!-- Users -->
        <div class="admin-section">
            <h3>Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users_result = $conn->query("SELECT * FROM users");
                    while ($user = $users_result->fetch_assoc()) :
                    ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td>
                                <!-- Ban User Form -->
                                <form action="controllers/admin_controller.php" method="post">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="duration">
                                        <option value="1h">1 hour</option>
                                        <option value="1.5h">1.5 hours</option>
                                        <option value="1d">1 day</option>
                                        <option value="2d">2 days</option>
                                        <option value="3d">3 days</option>
                                        <option value="5d">5 days</option>
                                        <option value="20d">20 days</option>
                                        <option value="40d">40 days</option>
                                        <option value="until_i_say_so">Until I say so</option>
                                        <option value="forever">Forever</option>
                                    </select>
                                    <textarea name="reason" placeholder="Reason for ban"></textarea>
                                    <button type="submit" name="ban_user">Ban</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Posts -->
        <div class="admin-section">
            <h3>Posts</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Content</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $posts_result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id");
                    while ($post = $posts_result->fetch_assoc()) :
                    ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td><?php echo htmlspecialchars($post['username']); ?></td>
                            <td><?php echo htmlspecialchars($post['content']); ?></td>
                            <td><a href="controllers/admin_controller.php?delete_post=<?php echo $post['id']; ?>">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Comments -->
        <div class="admin-section">
            <h3>Comments</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Comment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $comments_result = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id");
                    while ($comment = $comments_result->fetch_assoc()) :
                    ?>
                        <tr>
                            <td><?php echo $comment['id']; ?></td>
                            <td><?php echo htmlspecialchars($comment['username']); ?></td>
                            <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                            <td><a href="controllers/admin_controller.php?delete_comment=<?php echo $comment['id']; ?>">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Chat Logs -->
        <div class="admin-section">
            <h3>Chat Logs</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Message</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $chats_result = $conn->query("SELECT chats.*, sender.username as sender_username, receiver.username as receiver_username FROM chats JOIN users as sender ON chats.sender_id = sender.id JOIN users as receiver ON chats.receiver_id = receiver.id ORDER BY chats.timestamp DESC");
                    while ($chat = $chats_result->fetch_assoc()) :
                    ?>
                        <tr>
                            <td><?php echo $chat['id']; ?></td>
                            <td><?php echo htmlspecialchars($chat['sender_username']); ?></td>
                            <td><?php echo htmlspecialchars($chat['receiver_username']); ?></td>
                            <td><?php echo htmlspecialchars($chat['message']); ?></td>
                            <td><?php echo $chat['timestamp']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
}

include 'partials/footer.php';
?>
