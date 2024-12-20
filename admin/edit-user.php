<?php
include "partials/header.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if a valid user ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        // Fetch user details from the database
        $query = "SELECT firstname, lastname, is_admin FROM users WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
        } else {
            $_SESSION['edit-user-error'] = "User not found.";
            header('Location: ' . ROOT_URL . 'admin/manage-users.php');
            exit();
        }
    } else {
        $_SESSION['edit-user-error'] = "Invalid user ID.";
        header('Location: ' . ROOT_URL . 'admin/manage-users.php');
        exit();
    }
} else {
    header('Location: ' . ROOT_URL . 'admin/manage-users.php');
    exit();
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit User</h2>

        <!-- Display any error messages -->
        <?php if (isset($_SESSION['edit-user-error'])): ?>
            <div class="alert__message error">
                <p><?= htmlspecialchars($_SESSION['edit-user-error']); unset($_SESSION['edit-user-error']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Edit User Form -->
        <form action="<?= ROOT_URL ?>admin/edit-user-logic.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <div class="form__control">
                <label for="firstname">First Name</label>
                <input type="text" name="firstname" id="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" placeholder="First Name" required>
            </div>
            <div class="form__control">
                <label for="lastname">Last Name</label>
                <input type="text" name="lastname" id="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" placeholder="Last Name" required>
            </div>
            <div class="form__control">
                <label for="userrole">User Role</label>
                <select name="userrole" id="userrole" required>
                    <option value="0" <?= $user['is_admin'] ? '' : 'selected' ?>>Author</option>
                    <option value="1" <?= $user['is_admin'] ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn">Update User</button>
        </form>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
