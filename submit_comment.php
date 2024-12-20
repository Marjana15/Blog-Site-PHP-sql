<?php
include './config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read the incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['post_id'], $input['user_id'], $input['comment'])) {
    $post_id = filter_var($input['post_id'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT);
    $comment = htmlspecialchars(trim($input['comment']));

    if (empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
        exit;
    }

    // Insert comment into the database
    $query = "INSERT INTO comments (post_id, user_id, content, timestamp) VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'iis', $post_id, $user_id, $comment);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add comment']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
}
