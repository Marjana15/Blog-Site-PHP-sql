<?php
include "config/database.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if category ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        // Update posts belonging to this category to "Uncategorized" (category_id = 2)
        $update_query = "UPDATE posts SET category_id = 2 WHERE category_id = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $update_result = mysqli_stmt_execute($stmt);

        if ($update_result) {
            // Delete the category
            $delete_query = "DELETE FROM categories WHERE id = ? LIMIT 1";
            $stmt = mysqli_prepare($connection, $delete_query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            $delete_result = mysqli_stmt_execute($stmt);

            if ($delete_result) {
                $_SESSION['edit-category-success'] = "Category was deleted successfully.";
            } else {
                $_SESSION['edit-category-error'] = "Failed to delete category: " . mysqli_stmt_error($stmt);
            }
        } else {
            $_SESSION['edit-category-error'] = "Failed to update posts for the selected category: " . mysqli_stmt_error($stmt);
        }
    } else {
        $_SESSION['edit-category-error'] = "Invalid category ID.";
    }

    // Redirect to manage categories page with feedback
    header("Location: " . ROOT_URL . "admin/manage-categories.php");
    exit();
}

// Redirect to manage categories page if no ID is provided
header("Location: " . ROOT_URL . "admin/manage-categories.php");
exit();
?>
