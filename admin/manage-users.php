<?php
include "partials/header.php";

// Check if the user is an admin
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Fetch all users except the current admin
$current_admin_id = $_SESSION['user-id'];
$query = "SELECT id, firstname, lastname, username, is_admin FROM users WHERE id != '$current_admin_id'";
$users = mysqli_query($connection, $query);

// Check for database query error
if (!$users) {
    die("Query failed: " . mysqli_error($connection));
}
?>

<section class="dashboard">
    <!-- Display messages -->
    <?php if (!empty($_SESSION['add-user-success'])): ?>
        <div class="alert__message success container">
            <p><?= $_SESSION['add-user-success']; unset($_SESSION['add-user-success']); ?></p>
        </div>
    <?php elseif (!empty($_SESSION['edit-user'])): ?>
        <div class="alert__message error container">
            <p><?= $_SESSION['edit-user']; unset($_SESSION['edit-user']); ?></p>
        </div>
    <?php elseif (!empty($_SESSION['edit-user-success'])): ?>
        <div class="alert__message success container">
            <p><?= $_SESSION['edit-user-success']; unset($_SESSION['edit-user-success']); ?></p>
        </div>
    <?php elseif (!empty($_SESSION['delete-user'])): ?>
        <div class="alert__message error container">
            <p><?= $_SESSION['delete-user']; unset($_SESSION['delete-user']); ?></p>
        </div>
    <?php elseif (!empty($_SESSION['delete-user-success'])): ?>
        <div class="alert__message success container">
            <p><?= $_SESSION['delete-user-success']; unset($_SESSION['delete-user-success']); ?></p>
        </div>
    <?php endif; ?>

    <div class="container dashboard__container">
        <button id="show__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-right-b"></i></button>
        <button id="hide__sidebar-btn" class="sidebar__toggle"><i class="uil uil-angle-left-b"></i></button>

        <!-- Sidebar -->
        <aside>
            <ul>
                <li><a href="<?= ROOT_URL ?>admin/add-post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/index.php"><i class="uil uil-postcard"></i><h5>Manage Posts</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/manage-profile.php"><i class="uil uil-user"></i><h5>Manage Profile</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/chat.php"><i class="uil uil-comments"></i><h5>Chat</h5></a></li> <!-- Chat Option -->
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li><a href="<?= ROOT_URL ?>admin/manage-posts.php"><i class="uil uil-postcard"></i><h5>Manage All Posts</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-users.php" class="active"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-category.php"><i class="uil uil-edit"></i><h5>Add Category</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
                <?php endif; ?>
            </ul>
        </aside>

        <!-- Main Content -->
        <main>
            <h2>Manage Users</h2>
            <?php if (mysqli_num_rows($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><?= htmlspecialchars($user["firstname"] . " " . $user['lastname']) ?></td>
                                <td><?= htmlspecialchars($user["username"]) ?></td>
                                <td><a href="<?= ROOT_URL ?>admin/edit-user.php?id=<?= $user['id'] ?>" class="btn sm">Edit</a></td>
                                <td><a href="<?= ROOT_URL ?>admin/delete-users.php?id=<?= $user['id'] ?>" class="btn sm danger">Delete</a></td>
                                <td><?= $user["is_admin"] ? 'Yes' : 'No' ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert__message error">No users found</div>
            <?php endif; ?>
        </main>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
