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

$comment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Fetch the comment from the database
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $comment_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();
$stmt->close();

if (!$comment) {
    header("Location: index.php");
    exit();
}
?>

<div class="container">
    <h2>Edit Comment</h2>
    <form action="controllers/edit_comment_controller.php" method="post">
        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
        <textarea name="comment" placeholder="Write a comment..."><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <button type="submit" name="update_comment">Update Comment</button>
    </form>
</div>

<?php include 'partials/footer.php'; ?>
