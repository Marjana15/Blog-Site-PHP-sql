<?php
require "./config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['submit'])) {
    // Sanitize and validate inputs
    $username_email = filter_var($_POST['username_email'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password'];

    // Input validation
    if (!$username_email) {
        $_SESSION['signin'] = 'Please enter your username or email.';
    } elseif (!$password) {
        $_SESSION['signin'] = 'Password is required.';
    } else {
        // Prepare a secure query to fetch the user
        $stmt = $connection->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_email, $username_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if the user exists
        if ($result->num_rows === 1) {
            // Fetch user data
            $user_record = $result->fetch_assoc();
            $db_password = $user_record['password'];

            // Verify the password
            if (password_verify($password, $db_password)) {
                // Set session variables for access control
                $_SESSION['user-id'] = $user_record['id'];
                $_SESSION['signin-success'] = "User successfully logged in";

                // Check if the user is an admin
                if ($user_record['is_admin'] == 1) {
                    $_SESSION['user_is_admin'] = true;
                }

                // Redirect to the admin dashboard
                header('Location: ' . ROOT_URL . 'admin/index.php');
                exit();
            } else {
                $_SESSION['signin'] = 'Invalid password.';
            }
        } else {
            $_SESSION['signin'] = 'User not found.';
        }
    }

    // Redirect back to the signin page with error
    if (isset($_SESSION['signin'])) {
        $_SESSION['signin-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'signin.php');
        exit();
    }
} else {
    // Redirect to the signin page if the form is not submitted
    header('Location: ' . ROOT_URL . 'signin.php');
    exit();
}
