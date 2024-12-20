<?php
include "partials/header.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Retrieve form data from session if the form submission failed
$firstname = $_SESSION['add-user-data']['firstname'] ?? '';
$lastname = $_SESSION['add-user-data']['lastname'] ?? '';
$username = $_SESSION['add-user-data']['username'] ?? '';
$email = $_SESSION['add-user-data']['email'] ?? '';
$createpassword = $_SESSION['add-user-data']['createpassword'] ?? '';
$confirmpassword = $_SESSION['add-user-data']['confirmpassword'] ?? '';
unset($_SESSION['add-user-data']); // Clear form data from session
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add User</h2>

        <!-- Display success or error messages -->
        <?php if (isset($_SESSION['add-user-success'])): ?>
            <div class="alert__message success">
                <p><?= htmlspecialchars($_SESSION['add-user-success']); unset($_SESSION['add-user-success']); ?></p>
            </div>
        <?php elseif (isset($_SESSION['add-user'])): ?>
            <div class="alert__message error">
                <p><?= htmlspecialchars($_SESSION['add-user']); unset($_SESSION['add-user']); ?></p>
            </div>
        <?php endif; ?>

        <form action="<?= ROOT_URL ?>admin/add-user-logic.php" enctype="multipart/form-data" method="POST">
            <div class="form__control">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" value="<?= htmlspecialchars($firstname) ?>" placeholder="First Name" required>
            </div>
            <div class="form__control">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" value="<?= htmlspecialchars($lastname) ?>" placeholder="Last Name" required>
            </div>
            <div class="form__control">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Username" required>
            </div>
            <div class="form__control">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" required>
            </div>
            <div class="form__control">
                <label for="createpassword">Password</label>
                <input type="password" id="createpassword" name="createpassword" value="<?= htmlspecialchars($createpassword) ?>" placeholder="Password" required>
            </div>
            <div class="form__control">
                <label for="confirmpassword">Confirm Password</label>
                <input type="password" id="confirmpassword" name="confirmpassword" value="<?= htmlspecialchars($confirmpassword) ?>" placeholder="Confirm Password" required>
            </div>
            <div class="form__control">
                <label for="userrole">User Role</label>
                <select id="userrole" name="userrole" required>
                    <option value="0" <?= isset($_SESSION['add-user-data']['userrole']) && $_SESSION['add-user-data']['userrole'] == "0" ? "selected" : "" ?>>Author</option>
                    <option value="1" <?= isset($_SESSION['add-user-data']['userrole']) && $_SESSION['add-user-data']['userrole'] == "1" ? "selected" : "" ?>>Admin</option>
                </select>
            </div>
            <div class="form__control">
                <label for="avatar">User Avatar</label>
                <input type="file" name="avatar" id="avatar" required>
            </div>
            <button type="submit" name="submit" class="btn">Add User</button>
        </form>
    </div>
</section>

<?php include '../partials/footer.php'; ?>
