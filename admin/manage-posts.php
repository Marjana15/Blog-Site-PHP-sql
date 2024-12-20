<?php
include "partials/header.php";

// Ensure the user is an admin
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Query to fetch all posts along with category and author details
$query = "
    SELECT posts.id, posts.title, posts.body, posts.is_featured, 
           categories.title AS category_title, 
           users.username AS author 
    FROM posts
    JOIN categories ON posts.category_id = categories.id
    JOIN users ON posts.author_id = users.id
    ORDER BY posts.id DESC
";

$posts = mysqli_query($connection, $query);

// Handle query execution errors
if (!$posts) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<section class="dashboard">
    <div class="container dashboard__container">
        <aside>
            <ul>
                <li><a href="<?= ROOT_URL ?>admin/add-post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/index.php"><i class="uil uil-postcard"></i><h5>Manage Posts</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/manage-profile.php"><i class="uil uil-user"></i><h5>Manage Profile</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/chat.php"><i class="uil uil-comments"></i><h5>Chat</h5></a></li> <!-- Chat Option -->
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li><a href="<?= ROOT_URL ?>admin/manage-posts.php" class="active"><i class="uil uil-postcard"></i><h5>Manage All Posts</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-users.php"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-category.php"><i class="uil uil-edit"></i><h5>Add Category</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
                <?php endif; ?>
            </ul>
        </aside>
        <main>
            <h2>Manage All Posts</h2>
            <?php if (mysqli_num_rows($posts) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Featured</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['title']); ?></td>
                                <td><?= htmlspecialchars($post['category_title']); ?></td>
                                <td><?= htmlspecialchars($post['author']); ?></td>
                                <td><?= $post['is_featured'] ? 'Yes' : 'No'; ?></td>
                                <td><a href="<?= ROOT_URL ?>admin/edit-post.php?id=<?= $post['id']; ?>" class="btn sm">Edit</a></td>
                                <td><a href="<?= ROOT_URL ?>admin/delete-post.php?id=<?= $post['id']; ?>" class="btn sm danger">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert__message error">No posts found</div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
