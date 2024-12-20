<?php
require 'config/database.php';

// Check if post ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        // Fetch post details from the database
        $query = "SELECT * FROM posts WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Ensure exactly one record was fetched
        if (mysqli_num_rows($result) === 1) {
            $post = mysqli_fetch_assoc($result);

            // Delete the thumbnail if it exists
            $thumbnail_name = $post['thumbnail'];
            $thumbnail_path = "../images/" . $thumbnail_name;

            if (file_exists($thumbnail_path)) {
                unlink($thumbnail_path);
            }

            // Delete the post from the database
            $delete_post_query = "DELETE FROM posts WHERE id = ? LIMIT 1";
            $delete_stmt = mysqli_prepare($connection, $delete_post_query);
            mysqli_stmt_bind_param($delete_stmt, 'i', $id);

            if (mysqli_stmt_execute($delete_stmt)) {
                $_SESSION['edit-post-success'] = "Post deleted successfully.";
            } else {
                $_SESSION['edit-post-error'] = "Failed to delete the post: " . mysqli_stmt_error($delete_stmt);
            }
        } else {
            $_SESSION['edit-post-error'] = "No post found with the specified ID.";
        }
    } else {
        $_SESSION['edit-post-error'] = "Invalid post ID.";
    }
} else {
    $_SESSION['edit-post-error'] = "Post ID not provided.";
}

// Redirect to admin dashboard with feedback
header('Location: ' . ROOT_URL . 'admin/');
exit();
