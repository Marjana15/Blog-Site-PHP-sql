<?php
include '../config/database.php';
header('Content-Type: application/json');

if (isset($_GET['post_id'])) {
    $post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch likes count
    $likes_query = "SELECT COUNT(*) AS likes_count FROM post_likes WHERE post_id = $post_id";
    $likes_result = mysqli_query($connection, $likes_query);
    $likes_count = mysqli_fetch_assoc($likes_result)['likes_count'] ?? 0;

    // Fetch comments count
    $comments_query = "SELECT COUNT(*) AS comments_count FROM comments WHERE post_id = $post_id";
    $comments_result = mysqli_query($connection, $comments_query);
    $comments_count = mysqli_fetch_assoc($comments_result)['comments_count'] ?? 0;

    echo json_encode([
        'success' => true,
        'likes' => $likes_count,
        'comments' => $comments_count,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request. No post ID provided.']);
}
?>
