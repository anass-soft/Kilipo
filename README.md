# Real-Time Social Media Platform

This is a minimal, real-time social media platform built with PHP, MySQL, and vanilla JavaScript. It includes features like user registration, posting, commenting, one-to-one chat, and a full-featured admin dashboard.

## Features

### General Users
- **Register and Login:** Secure user authentication with `password_hash()`.
- **Feeds Page:** View posts with text, images, and file attachments.
- **Post Interaction:** Like and comment on posts.
- **Content Management:** Edit and delete personal posts and comments.
- **Real-Time Chat:**
    - One-to-one chat with any registered user.
    - Image and file sharing in chat.
    - Chat history is stored in the database and automatically deleted after 30 days.
    - Efficient real-time updates using AJAX polling every 2 seconds.

### Admin Account
- **Fixed Credentials:**
    - **Username:** `AnassElalouaoui`
    - **Password:** `72ul@VL-;F-0`
- **Admin Dashboard:** A separate UI for all administrative tasks.
- **Moderation:**
    - View lists of users, posts, comments, and chat logs.
    - Ban users with various durations (1h, 1.5h, 1d, 2d, 3d, 5d, 20d, 40d, "Until I say so", or Forever).
    - Provide a justification for each ban.
    - Delete any post or comment.
- **Logging:** All admin actions (bans, deletions, login attempts) are logged with timestamps and IP addresses.

## Tech Stack
- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, Vanilla JavaScript
- **Real-Time Communication:** AJAX Polling

## Project Structure
```
/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── main.js
├── controllers/
│   ├── admin_controller.php
│   ├── chat_controller.php
│   ├── comment_controller.php
│   ├── edit_comment_controller.php
│   ├── edit_post_controller.php
│   ├── login_controller.php
│   ├── post_controller.php
│   └── register_controller.php
├── includes/
│   └── db.php
├── models/
├── partials/
│   ├── footer.php
│   └── header.php
├── uploads/
│   ├── chat_files/
│   ├── chat_images/
│   ├── files/
│   └── images/
├── admin.php
├── ChopItUp.php
├── database.sql
├── edit_comment.php
├── edit_post.php
├── index.php
├── login.php
└── register.php
```

## Setup Steps

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd <repository-directory>
    ```

2.  **Database Setup:**
    - Create a new MySQL database named `social_media_db`.
    - Import the `database.sql` file into your database. This will create all the necessary tables.
    ```bash
    mysql -u your_username -p social_media_db < database.sql
    ```

3.  **Database Configuration:**
    - Open the `includes/db.php` file.
    - Update the database credentials (`$servername`, `$username`, `$password`, `$dbname`) to match your local environment.

4.  **Web Server:**
    - Make sure you have a PHP-enabled web server (like Apache or Nginx) running.
    - Place the project files in your web server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP).

5.  **Permissions:**
    - Ensure that the `uploads/` directory and its subdirectories are writable by the web server.
    ```bash
    chmod -R 777 uploads/
    ```

6.  **Run the application:**
    - Open your web browser and navigate to the project's URL (e.g., `http://localhost/your-project-folder`).

## Developer

- **EL4V - ANASS ELALOUAOUI**
