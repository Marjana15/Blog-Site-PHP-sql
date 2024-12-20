<?php
session_start();
require "config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $author_id = $_SESSION['user-id'];
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $body = $_POST['body']; // Allow raw HTML from Quill editor
    $category_id = filter_var($_POST['category_id'], FILTER_SANITIZE_NUMBER_INT);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $thumbnail = $_FILES['thumbnail'];

    // Initialize error array
    $errors = [];

    // Validate inputs
    if (!$title) {
        $errors[] = "Enter post title.";
    }
    if (!$category_id) {
        $errors[] = "Select post category.";
    }
    if (!$body) {
        $errors[] = "Enter post body.";
    }
    if (!$thumbnail['name']) {
        $errors[] = "Choose post thumbnail.";
    }

    // Handle thumbnail upload if no validation errors
    $thumbnail_url = null;
    if (empty($errors)) {
        // Validate thumbnail file type and size
        $allowed_files = ['jpg', 'png', 'jpeg'];
        $extension = strtolower(pathinfo($thumbnail['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed_files)) {
            $errors[] = "File must be in JPG, PNG, or JPEG format.";
        } elseif ($thumbnail['size'] >= 2000000) { // 2MB limit
            $errors[] = "File size too big. Should be less than 2MB.";
        } else {
            // Upload image to ImgBB using cURL
            $thumbnail_tmp_name = $thumbnail['tmp_name'];
            $imgbb_api_key = "caadf7e83479d331c6910900a8a4f72d"; // Replace with your ImgBB API key
            $imgbb_upload_url = "https://api.imgbb.com/1/upload";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imgbb_upload_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'key' => $imgbb_api_key,
                'image' => new CURLFile($thumbnail_tmp_name)
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $response_data = json_decode($response, true);

            if ($response_data && isset($response_data['data']['url'])) {
                $thumbnail_url = $response_data['data']['url'];
            } else {
                $errors[] = "Failed to upload thumbnail to ImgBB.";
            }
        }
    }

    // Redirect back with errors if validation fails
    if (!empty($errors)) {
        $_SESSION['add-post-errors'] = $errors;
        $_SESSION['add-post-data'] = $_POST; // Preserve form data
        header('Location: ' . ROOT_URL . 'admin/add-post.php');
        exit();
    }

    // Reset all featured posts if this post is featured
    if ($is_featured) {
        $zero_all_is_featured_query = "UPDATE posts SET is_featured = 0";
        mysqli_query($connection, $zero_all_is_featured_query);
    }

    // Insert the post into the database using prepared statements
    $query = "INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ssssii", $title, $body, $thumbnail_url, $category_id, $author_id, $is_featured);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        $_SESSION['add-post-success'] = "New post added successfully.";
        header("Location: " . ROOT_URL . 'admin/index.php');
        exit();
    } else {
        $_SESSION['add-post'] = "Failed to add post: " . mysqli_stmt_error($stmt);
        header("Location: " . ROOT_URL . 'admin/add-post.php');
        exit();
    }
}

// Redirect to admin dashboard if accessed directly
header("Location: " . ROOT_URL . 'admin/index.php');
exit();
?>
