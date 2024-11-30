<?php
require "config/database.php";

if (isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $body = $_POST['body']; // Allow raw HTML from Quill editor
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $thumbnail = $_FILES['thumbnail'];

    // Validate form inputs
    if (!$title) {
        $_SESSION['add-post'] = "Enter post title.";
    } elseif (!$category_id) {
        $_SESSION['add-post'] = "Select post category.";
    } elseif (!$body) {
        $_SESSION['add-post'] = "Enter post body.";
    } elseif (!$thumbnail['name']) {
        $_SESSION['add-post'] = "Choose post thumbnail.";
    } else {
        // Handle thumbnail upload
        $time = time(); // Unique name for the image
        $thumbnail_name = $time . "_" . $thumbnail['name'];
        $thumbnail_tmp_name = $thumbnail['tmp_name'];
        $thumbnail_destination_path = "../images/" . $thumbnail_name;

        // Validate thumbnail file type
        $allowed_files = ['jpg', 'png', 'jpeg'];
        $extension = strtolower(pathinfo($thumbnail_name, PATHINFO_EXTENSION));
        if (in_array($extension, $allowed_files)) {
            // Validate thumbnail file size (< 2MB)
            if ($thumbnail['size'] < 2000000) {
                // Move the uploaded file
                if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path)) {
                    $_SESSION['add-post'] = "Failed to upload thumbnail.";
                }
            } else {
                $_SESSION['add-post'] = "File size too big. Should be less than 2MB.";
            }
        } else {
            $_SESSION['add-post'] = "File must be in JPG, PNG, or JPEG format.";
        }
    }

    // Redirect back if any validation error occurs
    if (isset($_SESSION['add-post'])) {
        $_SESSION['add-post-data'] = $_POST; // Preserve form data
        header('location: ' . ROOT_URL . 'admin/add-post.php');
        exit();
    } else {
        // If the post is featured, reset all others to not featured
        if ($is_featured == 1) {
            $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0";
            mysqli_query($connection, $zero_all_is_featured_query);
        }

        // Insert the post into the database using prepared statements
        $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "ssssii", $title, $body, $thumbnail_name, $category_id, $author_id, $is_featured);
        $result = mysqli_stmt_execute($stmt);

        if (!$result) {
            $_SESSION['add-post'] = "Failed to add post: " . mysqli_error($connection);
            header("location: " . ROOT_URL . 'admin/add-post.php');
            exit();
        } else {
            $_SESSION['add-post-success'] = "New post added successfully.";
            header("location: " . ROOT_URL . 'admin/index.php');
            exit();
        }
    }
}

// Redirect to admin dashboard if no POST request is made
header("location: " . ROOT_URL . 'admin/index.php');
exit();
?>
