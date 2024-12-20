<?php
require 'config/database.php';

header('Content-Type: application/json');

// Fetch the first 6 categories
$query = "SELECT id, title FROM categories ORDER BY title LIMIT 6";
$result = mysqli_query($connection, $query);

$categories = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($category = mysqli_fetch_assoc($result)) {
        $categories[] = [
            'id' => $category['id'],
            'title' => $category['title']
        ];
    }
}

echo json_encode($categories);
exit();
?>
