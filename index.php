<?php 
include 'partials/header.php';

// Ensure the database connection exists
if (!$connection) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Featured post query
$featured_query = "SELECT * FROM posts WHERE is_featured=1";
$featured_result = mysqli_query($connection, $featured_query);

if (!$featured_result) {
    die('Error with featured query: ' . mysqli_error($connection));
}

$featured = mysqli_fetch_assoc($featured_result);

// Fetch latest 9 posts excluding the featured one
$query = "SELECT * FROM posts WHERE is_featured=0 ORDER BY date_time DESC LIMIT 9";
$posts = mysqli_query($connection, $query);

if (!$posts) {
    die('Error with posts query: ' . mysqli_error($connection));
}

// Fetch trending posts based on likes
$trending_query = "
    SELECT posts.*, COUNT(post_likes.post_id) AS total_likes
    FROM posts
    LEFT JOIN post_likes ON posts.id = post_likes.post_id
    GROUP BY posts.id
    ORDER BY total_likes DESC
    LIMIT 5
";
$trending_posts = mysqli_query($connection, $trending_query);

if (!$trending_posts) {
    die('Error with trending posts query: ' . mysqli_error($connection));
}
?>

<section class="main-content">
    <div class="container main-content__container">
        <!-- Posts Section -->
        <div class="posts__wrapper">
            <h1 class="section-title">
                <i class="fas fa-home"></i> Home
            </h1>
            <div class="underline"></div>

            <!-- Featured Post -->
            <?php if ($featured) : ?>
                <article class="featured-post">
                    <div class="post__thumbnail">
                        <img src="<?= strpos($featured['thumbnail'], 'http') === 0 ? htmlspecialchars($featured['thumbnail']) : './images/' . htmlspecialchars($featured['thumbnail']) ?>" alt="Featured Thumbnail">
                    </div>
                    <div class="post__info">
                        <?php
                        $category_id = $featured['category_id'];
                        $category_query = "SELECT * FROM categories WHERE id = $category_id";
                        $category_result = mysqli_query($connection, $category_query);
                        $category = mysqli_fetch_assoc($category_result);

                        $author_id = $featured['author_id'];
                        $author_query = "SELECT * FROM users WHERE id = $author_id";
                        $author_result = mysqli_query($connection, $author_query);
                        $author = mysqli_fetch_assoc($author_result);
                        ?>
                        <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>" class="category__button">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($category['title']) ?>
                        </a>
                        <h2 class="post__title">
                            <a href="<?= ROOT_URL ?>post.php?id=<?= $featured['id'] ?>"><?= htmlspecialchars($featured['title']) ?></a>
                        </h2>
                        <p class="post__body"><?= substr(strip_tags($featured['body']), 0, 150) ?>...</p>
                        <div class="post__author">
                            <div class="post__author-avatar">
                                <img src="<?= strpos($author['avatar'], 'http') === 0 ? htmlspecialchars($author['avatar']) : './images/' . htmlspecialchars($author['avatar']) ?>" alt="Author Avatar">
                            </div>
                            <div class="post__author-info">
                                <h5>
                                    <i class="fas fa-user"></i> By: <?= htmlspecialchars($author['firstname'] . ' ' . $author['lastname']) ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endif; ?>

            <!-- Regular Posts -->
            <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
                <article class="post">
                    <div class="post__thumbnail">
                        <img src="<?= strpos($post['thumbnail'], 'http') === 0 ? htmlspecialchars($post['thumbnail']) : './images/' . htmlspecialchars($post['thumbnail']) ?>" alt="Post Thumbnail">
                    </div>
                    <div class="post__info">
                        <?php
                        $category_id = $post['category_id'];
                        $category_query = "SELECT * FROM categories WHERE id = $category_id";
                        $category_result = mysqli_query($connection, $category_query);
                        $category = mysqli_fetch_assoc($category_result);
                        ?>
                        <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>" class="category__button">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($category['title']) ?>
                        </a>
                        <h2 class="post__title">
                            <a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a>
                        </h2>
                        <p class="post__body"><?= substr(strip_tags($post['body']), 0, 150) ?>...</p>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>

        <!-- Sidebar Section -->
        <div class="sidebar__wrapper">
            <!-- Categories -->
            <div class="categories__wrapper">
                <h2 class="section-title">
                    <i class="fas fa-folder-open"></i> Stories from
                </h2>
                <div class="underline"></div>
                <div class="category__buttons-container">
                    <?php 
                    $all_categories_query = "SELECT * FROM categories";
                    $all_categories_result = mysqli_query($connection, $all_categories_query);
                    ?>
                    <?php while ($category = mysqli_fetch_assoc($all_categories_result)) : ?>
                        <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>" class="category__button">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($category['title']) ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Trending Posts -->
            <div class="trending__wrapper">
                <h2 class="section-title">
                    <i class="fas fa-fire"></i> Trending Posts
                </h2>
                <div class="underline"></div>
                <div class="trending__posts">
                    <?php while ($trending = mysqli_fetch_assoc($trending_posts)) : ?>
                        <?php
                        $author_id = $trending['author_id'];
                        $author_query = "SELECT * FROM users WHERE id = $author_id";
                        $author_result = mysqli_query($connection, $author_query);
                        $author = mysqli_fetch_assoc($author_result);
                        ?>
                        <div class="trending__item">
                            <div class="trending__info">
                                <h4>
                                    <a href="post.php?id=<?= $trending['id'] ?>">
                                        <i class="fas fa-book"></i> <?= htmlspecialchars($trending['title']) ?>
                                    </a>
                                </h4>
                                <small>
                                    <i class="fas fa-user"></i> By: <?= htmlspecialchars($author['firstname'] . ' ' . $author['lastname']) ?>
                                </small>
                                <small class="likes">
                                    <i class="fas fa-heart"></i> Likes: <?= htmlspecialchars($trending['total_likes']) ?>
                                </small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include './partials/footer.php'; ?>
