<?php
require 'config/database.php';

header('Content-Type: application/json');

// Fetch the latest 6 posts
$query = "SELECT id, title FROM posts ORDER BY date_time DESC LIMIT 6";
$result = mysqli_query($connection, $query);

$posts = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($post = mysqli_fetch_assoc($result)) {
        $posts[] = [
            'id' => $post['id'],
            'title' => $post['title']
        ];
    }
}

echo json_encode($posts);
exit();
?>
