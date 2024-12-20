<?php
include "partials/header.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Retrieve form data from session if available
$title = $_SESSION['add-category-data']['title'] ?? '';
$description = $_SESSION['add-category-data']['description'] ?? '';

// Clear session data after retrieval
unset($_SESSION['add-category-data']);
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add Category</h2>

        <!-- Display error message if available -->
        <?php if (isset($_SESSION['add-category'])): ?>
            <div class="alert__message error">
                <p>
                    <?= $_SESSION['add-category']; ?>
                    <?php unset($_SESSION['add-category']); ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <form action="<?= ROOT_URL ?>admin/add-category-logic.php" method="POST">
            <div class="form__control">
                <label for="title">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="<?= htmlspecialchars($title) ?>" 
                    placeholder="Title" 
                    required>
            </div>
            <div class="form__control">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Description" 
                    required><?= htmlspecialchars($description) ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn">Add Category</button>
        </form>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
