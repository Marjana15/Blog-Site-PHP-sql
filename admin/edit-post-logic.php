<?php
require 'config/database.php';

if (isset($_POST['submit'])) {
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $body = $_POST['body']; // Allow raw HTML from Quill editor
    $previous_thumbnail_name = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_STRING);
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // Validate form inputs
    if (!$title) {
        $_SESSION['edit-post'] = "Could not update post. Invalid title.";
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = "Could not update post. Invalid category.";
    } elseif (!$body) {
        $_SESSION['edit-post'] = "Could not update post. Body cannot be empty.";
    } else {
        // Handle new thumbnail upload if provided
        if ($thumbnail['name']) {
            $previous_thumbnail_destination = '../images/' . $previous_thumbnail_name;
            if (file_exists($previous_thumbnail_destination)) {
                unlink($previous_thumbnail_destination);
            }

            $time = time();
            $thumbnail_name = $time . "_" . $thumbnail['name'];
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $thumbnail_destination_path = "../images/" . $thumbnail_name;

            // Validate thumbnail file
            $allowed_files = ['jpg', 'png', 'jpeg'];
            $extension = strtolower(pathinfo($thumbnail_name, PATHINFO_EXTENSION));
            if (in_array($extension, $allowed_files)) {
                if ($thumbnail['size'] < 2000000) {
                    if (!move_uploaded_file($thumbnail_tmp_name, $thumbnail_destination_path)) {
                        $_SESSION['edit-post'] = "Failed to upload new thumbnail.";
                    }
                } else {
                    $_SESSION['edit-post'] = "Thumbnail size too large. Must be less than 2MB.";
                }
            } else {
                $_SESSION['edit-post'] = "Thumbnail must be a JPG, PNG, or JPEG file.";
            }
        }

        // Set thumbnail name for the update query
        $thumbnail_to_insert = isset($thumbnail_name) ? $thumbnail_name : $previous_thumbnail_name;

        // Redirect if there are errors
        if (isset($_SESSION['edit-post'])) {
            header('location: ' . ROOT_URL . 'admin/');
            exit();
        } else {
            // If featured, reset all other posts' featured status
            if ($is_featured == 1) {
                $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0";
                mysqli_query($connection, $zero_all_is_featured_query);
            }

            // Update the post
            $query = "UPDATE posts 
                      SET title = ?, 
                          body = ?, 
                          thumbnail = ?, 
                          category_id = ?, 
                          is_featured = ? 
                      WHERE id = ? LIMIT 1";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ssssii", $title, $body, $thumbnail_to_insert, $category_id, $is_featured, $id);
            $result = mysqli_stmt_execute($stmt);
        }

        if (!mysqli_errno($connection)) {
            $_SESSION['edit-post-success'] = "Post updated successfully.";
        }
    }
}

header('location: ' . ROOT_URL . 'admin/');
exit();
?>
