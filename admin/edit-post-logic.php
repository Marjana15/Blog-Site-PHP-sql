<?php
require 'config/database.php';

// Ensure the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and validate input data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $body = $_POST['body']; // Raw HTML allowed
    $previous_thumbnail_url = filter_var($_POST['previous_thumbnail_name'], FILTER_SANITIZE_STRING);
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $thumbnail = $_FILES['thumbnail'];

    // Initialize an error session
    $error_flag = false;

    // Validate required fields
    if (!$title) {
        $_SESSION['edit-post'] = "Title cannot be empty.";
        $error_flag = true;
    } elseif (!$category_id) {
        $_SESSION['edit-post'] = "Category selection is required.";
        $error_flag = true;
    } elseif (!$body) {
        $_SESSION['edit-post'] = "Post body cannot be empty.";
        $error_flag = true;
    }

    // Handle thumbnail upload if provided
    $thumbnail_to_insert = $previous_thumbnail_url; // Default to previous thumbnail
    if (!$error_flag && $thumbnail['name']) {
        // Validate file type and size
        $allowed_files = ['jpg', 'png', 'jpeg'];
        $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed_files)) {
            $_SESSION['edit-post'] = "Thumbnail must be a JPG, PNG, or JPEG file.";
            $error_flag = true;
        } elseif ($thumbnail['size'] >= 2000000) {
            $_SESSION['edit-post'] = "Thumbnail size too large. Must be less than 2MB.";
            $error_flag = true;
        } else {
            // Upload to ImgBB
            $api_key = "caadf7e83479d331c6910900a8a4f72d"; // Your ImgBB API key
            $imgbb_url = "https://api.imgbb.com/1/upload";
            
            // cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imgbb_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'key' => $api_key,
                'image' => base64_encode(file_get_contents($thumbnail['tmp_name'])),
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $response_data = json_decode($response, true);

            if ($response_data && isset($response_data['data']['url'])) {
                $thumbnail_to_insert = $response_data['data']['url'];
            } else {
                $_SESSION['edit-post'] = "Failed to upload the new thumbnail to ImgBB.";
                $error_flag = true;
            }
        }
    }

    // Redirect back if there are validation errors
    if ($error_flag) {
        header('Location: ' . ROOT_URL . 'admin/');
        exit();
    }

    // Reset all posts' featured status if the current post is set as featured
    if ($is_featured) {
        $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0";
        mysqli_query($connection, $zero_all_is_featured_query);
    }

    // Update the post in the database
    $query = "UPDATE posts 
              SET title = ?, 
                  body = ?, 
                  thumbnail = ?, 
                  category_id = ?, 
                  is_featured = ? 
              WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "sssiii", $title, $body, $thumbnail_to_insert, $category_id, $is_featured, $id);
    $result = mysqli_stmt_execute($stmt);

    // Check for query success
    if ($result) {
        $_SESSION['edit-post-success'] = "Post updated successfully.";
    } else {
        $_SESSION['edit-post'] = "Failed to update the post.";
    }
}

// Redirect back to the admin dashboard
header('Location: ' . ROOT_URL . 'admin/');
exit();
