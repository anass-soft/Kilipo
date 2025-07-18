<?php
session_start();
include 'partials/header.php';
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch posts from the database
$posts_result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY posts.created_at DESC");
?>

<div class="container">
    <h2>Feed</h2>

    <!-- Create Post Form -->
    <div class="post-form">
        <form action="controllers/post_controller.php" method="post" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?"></textarea>
            <input type="file" name="image">
            <input type="file" name="file">
            <button type="submit" name="create_post">Post</button>
        </form>
    </div>

    <!-- Posts -->
    <div class="posts">
        <?php while ($post = $posts_result->fetch_assoc()) : ?>
            <div class="post">
                <div class="post-header">
                    <strong><?php echo htmlspecialchars($post['username']); ?></strong>
                    <small><?php echo $post['created_at']; ?></small>
                </div>
                <div class="post-content">
                    <p><?php echo htmlspecialchars($post['content']); ?></p>
                    <?php if ($post['image_path']) : ?>
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Post Image">
                    <?php endif; ?>
                    <?php if ($post['file_path']) : ?>
                        <a href="<?php echo htmlspecialchars($post['file_path']); ?>" download>Download File</a>
                    <?php endif; ?>
                </div>
                <div class="post-actions">
                    <?php if ($post['user_id'] == $_SESSION['user_id']) : ?>
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>">Edit</a>
                        <a href="controllers/post_controller.php?delete_post=<?php echo $post['id']; ?>">Delete</a>
                    <?php endif; ?>
                    <!-- Like and Comment buttons will be added later -->
                    <div class="comments">
                        <!-- Comment Form -->
                        <form action="controllers/comment_controller.php" method="post">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <textarea name="comment" placeholder="Write a comment..."></textarea>
                            <button type="submit" name="create_comment">Comment</button>
                        </form>

                        <!-- Comments List -->
                        <?php
                        $post_id = $post['id'];
                        $comments_result = $conn->query("SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = $post_id ORDER BY comments.created_at DESC");
                        while ($comment = $comments_result->fetch_assoc()) :
                        ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['username']); ?></strong>
                                <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                                <?php if ($comment['user_id'] == $_SESSION['user_id']) : ?>
                                    <a href="edit_comment.php?id=<?php echo $comment['id']; ?>">Edit</a>
                                    <a href="controllers/comment_controller.php?delete_comment=<?php echo $comment['id']; ?>">Delete</a>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
