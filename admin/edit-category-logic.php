<?php
require "config/database.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and validate input
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$id || !$title || !$description) {
        $_SESSION['edit-category'] = "Invalid form input on edit category page.";
    } else {
        // Prepare an SQL statement to prevent SQL injection
        $query = "UPDATE categories SET title = ?, description = ? WHERE id = ? LIMIT 1";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'ssi', $title, $description, $id);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['edit-category-success'] = "Category '$title' was updated successfully.";
        } else {
            $_SESSION['edit-category'] = "Could not update category: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

// Redirect back to the manage categories page
header("Location: " . ROOT_URL . "admin/manage-categories.php");
exit();
