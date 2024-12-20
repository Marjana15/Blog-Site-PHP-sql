<?php

require 'config/database.php';

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if user ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        // Fetch user details from the database
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Delete user's avatar if it exists
            if (!empty($user['avatar']) && $user['avatar'] !== 'default_avatar.png') {
                $avatar_path = "../images/" . $user['avatar'];
                if (file_exists($avatar_path)) {
                    unlink($avatar_path);
                }
            }

            // Delete all thumbnails of the user's posts
            $thumbnails_query = "SELECT thumbnail FROM posts WHERE author_id = ?";
            $thumbnails_stmt = mysqli_prepare($connection, $thumbnails_query);
            mysqli_stmt_bind_param($thumbnails_stmt, 'i', $id);
            mysqli_stmt_execute($thumbnails_stmt);
            $thumbnails_result = mysqli_stmt_get_result($thumbnails_stmt);

            while ($thumbnail = mysqli_fetch_assoc($thumbnails_result)) {
                $thumbnail_path = "../images/" . $thumbnail['thumbnail'];
                if (file_exists($thumbnail_path)) {
                    unlink($thumbnail_path);
                }
            }

            // Delete user from database
            $delete_user_query = "DELETE FROM users WHERE id = ?";
            $delete_user_stmt = mysqli_prepare($connection, $delete_user_query);
            mysqli_stmt_bind_param($delete_user_stmt, 'i', $id);

            if (mysqli_stmt_execute($delete_user_stmt)) {
                $_SESSION['delete-user-success'] = "User '{$user['firstname']} {$user['lastname']}' has been deleted successfully.";
            } else {
                $_SESSION['delete-user-error'] = "Failed to delete user '{$user['firstname']} {$user['lastname']}'.";
            }
        } else {
            $_SESSION['delete-user-error'] = "User not found.";
        }
    } else {
        $_SESSION['delete-user-error'] = "Invalid user ID.";
    }
} else {
    $_SESSION['delete-user-error'] = "No user ID provided.";
}

// Redirect to manage users page with feedback
header("Location: " . ROOT_URL . "admin/manage-users.php");
exit();
