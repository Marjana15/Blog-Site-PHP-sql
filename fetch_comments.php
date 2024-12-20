<?php
include './config/database.php';

if (isset($_GET['post_id'])) {
    $post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT);

    $query = "SELECT comments.id, comments.content, comments.timestamp, comments.user_id, 
              users.firstname, users.lastname 
              FROM comments 
              JOIN users ON comments.user_id = users.id 
              WHERE comments.post_id = $post_id 
              ORDER BY comments.timestamp DESC";
    $result = mysqli_query($connection, $query);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch comments']);
        exit;
    }

    $comments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $comments[] = [
            'id' => $row['id'],
            'content' => htmlspecialchars($row['content']),
            'author' => htmlspecialchars($row['firstname'] . ' ' . $row['lastname']),
            'timestamp' => date("M d, Y - g:i a", strtotime($row['timestamp'])),
            'user_id' => $row['user_id'], // Include user_id for delete button validation
        ];
    }

    echo json_encode(['success' => true, 'comments' => $comments]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request. No post ID provided.']);
}
