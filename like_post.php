<?php
// Start the session
session_start();

// Enable error reporting (disable display for API responses)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database connection
include './config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user-id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User not logged in',
    ]);
    exit;
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Parse the input
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID is required']);
    exit;
}

$post_id = filter_var($data['post_id'], FILTER_SANITIZE_NUMBER_INT);
$user_id = $_SESSION['user-id'];

// Check if the user has already liked the post
$check_query = "SELECT * FROM post_likes WHERE post_id = $post_id AND user_id = $user_id";
$check_result = mysqli_query($connection, $check_query);

if (!$check_result) {
    echo json_encode(['success' => false, 'message' => 'Database query failed']);
    exit;
}

if (mysqli_num_rows($check_result) > 0) {
    // User has already liked the post, remove the like
    $delete_query = "DELETE FROM post_likes WHERE post_id = $post_id AND user_id = $user_id";
    if (!mysqli_query($connection, $delete_query)) {
        echo json_encode(['success' => false, 'message' => 'Failed to remove like']);
        exit;
    }
    $has_liked = false;
} else {
    // User has not liked the post, add the like
    $insert_query = "INSERT INTO post_likes (post_id, user_id) VALUES ($post_id, $user_id)";
    if (!mysqli_query($connection, $insert_query)) {
        echo json_encode(['success' => false, 'message' => 'Failed to add like']);
        exit;
    }
    $has_liked = true;
}

// Get the total number of likes for the post
$likes_query = "SELECT COUNT(*) AS total_likes FROM post_likes WHERE post_id = $post_id";
$likes_result = mysqli_query($connection, $likes_query);

if (!$likes_result) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch like count']);
    exit;
}

$total_likes = mysqli_fetch_assoc($likes_result)['total_likes'];

// Return a valid JSON response
echo json_encode([
    'success' => true,
    'total_likes' => $total_likes,
    'has_liked' => $has_liked,
]);
exit;
