<?php
include "partials/header.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Fetch categories from the database
$query = "SELECT * FROM categories ORDER BY title ASC";
$categories = mysqli_query($connection, $query);

// Check for query errors
if (!$categories) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<section class="dashboard">
    <!-- Display feedback messages -->
    <?php if (isset($_SESSION['add-category-success'])): ?>
        <div class="alert__message success container">
            <p><?= htmlspecialchars($_SESSION['add-category-success']); unset($_SESSION['add-category-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['add-category'])): ?>
        <div class="alert__message error container">
            <p><?= htmlspecialchars($_SESSION['add-category']); unset($_SESSION['add-category']); ?></p>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['edit-category-success'])): ?>
        <div class="alert__message success container">
            <p><?= htmlspecialchars($_SESSION['edit-category-success']); unset($_SESSION['edit-category-success']); ?></p>
        </div>
    <?php elseif (isset($_SESSION['edit-category'])): ?>
        <div class="alert__message error container">
            <p><?= htmlspecialchars($_SESSION['edit-category']); unset($_SESSION['edit-category']); ?></p>
        </div>
    <?php endif; ?>

    <div class="container dashboard__container">
        <button id="show__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-right-b"></i></button>
        <button id="hide__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-left-b"></i></button>

        <aside>
            <ul>
                <li><a href="<?= ROOT_URL ?>admin/add-post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/index.php"><i class="uil uil-postcard"></i><h5>Manage Posts</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/manage-profile.php"><i class="uil uil-user"></i><h5>Manage Profile</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/chat.php"><i class="uil uil-comments"></i><h5>Chat</h5></a></li> <!-- Chat Option -->
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li><a href="<?= ROOT_URL ?>admin/manage-posts.php"><i class="uil uil-postcard"></i><h5>Manage All Posts</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-users.php"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-category.php"><i class="uil uil-edit"></i><h5>Add Category</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-categories.php" class="active"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <main>
            <h2>Manage Categories</h2>
            <?php if (mysqli_num_rows($categories) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['title']); ?></td>
                                <td><a href="<?= ROOT_URL ?>admin/edit-category.php?id=<?= htmlspecialchars($category['id']); ?>" class="btn sm">Edit</a></td>
                                <td><a href="<?= ROOT_URL ?>admin/delete-category.php?id=<?= htmlspecialchars($category['id']); ?>" class="btn sm danger">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert__message error">No categories found</div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php include "../partials/footer.php"; ?>
