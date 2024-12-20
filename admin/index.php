<?php
include "partials/header.php";

// Ensure user is logged in
if (!isset($_SESSION['user-id'])) {
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Fetch current user ID from session
$current_user_id = $_SESSION['user-id'];

// Fetch posts along with category titles for the current user
$query = "SELECT posts.id, posts.title, categories.title AS category_title
          FROM posts
          JOIN categories ON posts.category_id = categories.id
          WHERE posts.author_id = ?
          ORDER BY posts.id DESC";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, "i", $current_user_id);
mysqli_stmt_execute($stmt);
$posts = mysqli_stmt_get_result($stmt);
?>

<section class="dashboard">
    <!-- Display messages -->
    <?php if (isset($_SESSION['signin-success'])): ?>
        <div class="alert__message success container">
            <p><?= htmlspecialchars($_SESSION['signin-success']); unset($_SESSION['signin-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['add-post'])): ?>
        <div class="alert__message error container">
            <p><?= htmlspecialchars($_SESSION['add-post']); unset($_SESSION['add-post']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['add-post-success'])): ?>
        <div class="alert__message success container">
            <p><?= htmlspecialchars($_SESSION['add-post-success']); unset($_SESSION['add-post-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['edit-post'])): ?>
        <div class="alert__message error container">
            <p><?= htmlspecialchars($_SESSION['edit-post']); unset($_SESSION['edit-post']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['edit-post-success'])): ?>
        <div class="alert__message success container">
            <p><?= htmlspecialchars($_SESSION['edit-post-success']); unset($_SESSION['edit-post-success']); ?></p>
        </div>
    <?php endif; ?>

    <div class="container dashboard__container">
        <button id="show__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-right-b"></i></button>
        <button id="hide__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-left-b"></i></button>

        <aside>
            <ul>
                <li><a href="<?= ROOT_URL ?>admin/add-post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/index.php" class="active"><i class="uil uil-postcard"></i><h5>Manage Posts</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/manage-profile.php"><i class="uil uil-user"></i><h5>Manage Profile</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/chat.php"><i class="uil uil-comments"></i><h5>Chat</h5></a></li> <!-- Chat Option -->
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li><a href="<?= ROOT_URL ?>admin/manage-posts.php"><i class="uil uil-postcard"></i><h5>Manage All Posts</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-users.php"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-category.php"><i class="uil uil-edit"></i><h5>Add Category</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <main>
            <h2>Manage Posts</h2>
            <?php if (mysqli_num_rows($posts) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = mysqli_fetch_assoc($posts)): ?>
                            <tr>
                                <td><?= htmlspecialchars($post['title']); ?></td>
                                <td><?= htmlspecialchars($post['category_title']); ?></td>
                                <td><a href="<?= ROOT_URL ?>admin/edit-post.php?id=<?= htmlspecialchars($post['id']); ?>" class="btn sm">Edit</a></td>
                                <td><a href="<?= ROOT_URL ?>admin/delete-post.php?id=<?= htmlspecialchars($post['id']); ?>" class="btn sm danger">Delete</a></td>
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
