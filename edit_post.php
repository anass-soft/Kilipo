<?php
session_start();
include 'partials/header.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$post_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the post from the database
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <h2>Edit Post</h2>
    <form action="controllers/edit_post_controller.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <textarea name="content" placeholder="What's on your mind?"><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit" name="update_post">Update Post</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>
