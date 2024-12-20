<?php

require 'config/database.php';

// Check if user is an admin
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

if (isset($_POST['submit'])) {
    // Get and sanitize form data
    $title = filter_var($_POST['title'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Initialize error array
    $errors = [];

    // Validate inputs
    if (!$title) {
        $errors[] = "Enter a valid title.";
    }
    if (!$description) {
        $errors[] = "Enter a valid description.";
    }

    // Redirect back if there are errors
    if (!empty($errors)) {
        $_SESSION['add-category-errors'] = $errors;
        $_SESSION['add-category-data'] = $_POST; // Preserve form data
        header('Location: ' . ROOT_URL . 'admin/add-category.php');
        exit();
    }

    // Insert category into the database
    $query = "INSERT INTO categories (title, description) VALUES (?, ?)";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $title, $description);
        $execution_result = mysqli_stmt_execute($stmt);

        if ($execution_result) {
            $_SESSION['add-category-success'] = "Category '$title' added successfully.";
            header('Location: ' . ROOT_URL . 'admin/manage-categories.php');
            exit();
        } else {
            $_SESSION['add-category'] = "Database error: " . mysqli_stmt_error($stmt);
            header('Location: ' . ROOT_URL . 'admin/add-category.php');
            exit();
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['add-category'] = "Failed to prepare the database statement.";
        header('Location: ' . ROOT_URL . 'admin/add-category.php');
        exit();
    }
}

// Redirect if accessed directly
header('Location: ' . ROOT_URL . 'admin/manage-categories.php');
exit();
