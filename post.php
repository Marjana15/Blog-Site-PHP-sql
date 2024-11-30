<?php 
include 'partials/header.php';

// Fetch post
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM posts WHERE id = $id";
    $result = mysqli_query($connection, $query);
    if ($post = mysqli_fetch_assoc($result)) {
        $author_id = $post['author_id'];
        $author_query = "SELECT * FROM users WHERE id = $author_id";
        $author_result = mysqli_query($connection, $author_query);
        $author = mysqli_fetch_assoc($author_result);
    } else {
        // Redirect if no post is found
        header('location: ' . ROOT_URL . 'blog.php');
        exit;
    }
} else {
    // Redirect if id is not provided
    header('location: ' . ROOT_URL . 'blog.php');
    exit;
}
?>

<section class="singlepost">
    <div class="container singlepost__container">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        <div class="post__author">
            <div class="post__author-avatar">
                <img src="./images/<?= htmlspecialchars($author['avatar']) ?>" alt="Author's Avatar">
            </div>
            <div class="post__author-info">
                <h5>By: <?= htmlspecialchars($author['firstname']) ?> <?= htmlspecialchars($author['lastname']) ?></h5>
                <small><?= date("M d, Y - g:i a", strtotime($post['date_time'])) ?></small>
            </div>
        </div>
        <div class="singlepost__thumbnail">
            <img src="./images/<?= htmlspecialchars($post['thumbnail']) ?>" alt="Post Thumbnail">
        </div>
        <!-- Render the body content as HTML -->
        <div class="post-body">
            <?= $post['body'] ?>
        </div>
    </div>
</section>

<?php include './partials/footer.php'; ?>
