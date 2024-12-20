<?php
include './config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read the incoming JSON payload
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['comment_id'], $input['user_id'])) {
    $comment_id = filter_var($input['comment_id'], FILTER_SANITIZE_NUMBER_INT);
    $user_id = filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the user owns the comment or is an admin
    $query = "SELECT * FROM comments WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $comment_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Delete the comment
        $delete_query = "DELETE FROM comments WHERE id = ?";
        $delete_stmt = mysqli_prepare($connection, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, 'i', $comment_id);
        if (mysqli_stmt_execute($delete_stmt)) {
            echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete comment']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or comment not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
