<?php include 'partials/header.php'; ?>

<div class="container">
    <h2>Login</h2>
    <form action="controllers/login_controller.php" method="post">
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

<?php include 'partials/footer.php'; ?>
