<?php 
include './partials/header.php';

// Featured Post
$featured_query = "SELECT * FROM posts WHERE is_featured = 1";
$featured_result = mysqli_query($connection, $featured_query);
$featured = mysqli_fetch_assoc($featured_result);

// Fetch All Posts
$query = "SELECT * FROM posts ORDER BY date_time DESC";
$posts = mysqli_query($connection, $query);
?>
<section class="search__bar">
    <form class="container search__bar-container" action="<?= ROOT_URL ?>search.php" method="GET">
        <div>
            <i class="uil uil-search"></i>
            <input type="search" name="search" placeholder="Search">
            <button type="submit" name="submit" class="btn">Go</button>
        </div>
    </form>
</section>
<!-- ===================END OF SEARCH================-->

<!-- #region POSTS -->
<section class="posts <?= $featured ? '' : 'section__extra-margin' ?>">
    <div class="container posts__container">
        <?php while ($post = mysqli_fetch_assoc($posts)) : ?>
            <article class="post">
                <div class="post__thumbnail" style="width: 300px; height: 200px;">
                    <!-- Check if thumbnail is an external URL -->
                    <img src="<?= strpos($post['thumbnail'], 'http') === 0 ? htmlspecialchars($post['thumbnail']) : './images/' . htmlspecialchars($post['thumbnail']) ?>" alt="Post Thumbnail">
                </div>
                <div class="post__info">
                    <?php 
                    // Fetch category from categories using category_id
                    $category_id = $post['category_id'];
                    $category_query = "SELECT title FROM categories WHERE id = ?";
                    $stmt = mysqli_prepare($connection, $category_query);
                    mysqli_stmt_bind_param($stmt, "i", $category_id);
                    mysqli_stmt_execute($stmt);
                    $category_result = mysqli_stmt_get_result($stmt);
                    $category = mysqli_fetch_assoc($category_result);
                    ?>
                    <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $post['category_id'] ?>" class="category__button"><?= htmlspecialchars($category['title']) ?></a>
                    <h2 class="post__title"><a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                    <a href="<?= ROOT_URL ?>post.php?id=<?= $post['id'] ?>">
                        <p class="post__body" style="min-height: 10px;">
                            <?= htmlspecialchars(substr(strip_tags($post['body']), 0, 120)) ?>...
                        </p>
                    </a>

                    <div class="post__author">
                        <?php
                        // Fetch author from users table using author_id
                        $author_id = $post['author_id'];
                        $author_query = "SELECT firstname, lastname, avatar FROM users WHERE id = ?";
                        $stmt = mysqli_prepare($connection, $author_query);
                        mysqli_stmt_bind_param($stmt, "i", $author_id);
                        mysqli_stmt_execute($stmt);
                        $author_result = mysqli_stmt_get_result($stmt);
                        $author = mysqli_fetch_assoc($author_result);
                        ?>
                        <div class="post__author-avatar">
                            <!-- Check if avatar is an external URL -->
                            <img src="<?= strpos($author['avatar'], 'http') === 0 ? htmlspecialchars($author['avatar']) : './images/' . htmlspecialchars($author['avatar']) ?>" alt="Author Avatar">
                        </div>
                        <div class="post__author-info">
                            <h5>By: <?= htmlspecialchars($author['firstname']) ?> <?= htmlspecialchars($author['lastname']) ?></h5>
                            <small><?= date("M d, Y - g:i a", strtotime($post['date_time'])) ?></small>
                        </div>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</section>
<!--=======================END OF THE POSTS===============================-->

<section class="category__buttons">
    <div class="container category__buttons-container">
        <?php 
        $all_categories_query = "SELECT id, title FROM categories";
        $all_categories_result = mysqli_query($connection, $all_categories_query);
        ?>
        <?php while ($category = mysqli_fetch_assoc($all_categories_result)) : ?>
            <a href="<?= ROOT_URL ?>category-posts.php?id=<?= $category['id'] ?>" class="category__button"><?= htmlspecialchars($category['title']) ?></a>
        <?php endwhile; ?>
    </div>
</section>
<!--=======================END OF CATEGORY===================================-->

<?php include './partials/footer.php'; ?>
