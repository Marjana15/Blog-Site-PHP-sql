<?php
include "partials/header.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if the category ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        // Fetch category details from the database
        $query = "SELECT * FROM categories WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $category = mysqli_fetch_assoc($result);
        } else {
            $_SESSION['edit-category-error'] = "Category not found.";
            header("Location: " . ROOT_URL . "admin/manage-categories.php");
            exit();
        }
    } else {
        $_SESSION['edit-category-error'] = "Invalid category ID.";
        header("Location: " . ROOT_URL . "admin/manage-categories.php");
        exit();
    }
} else {
    header("Location: " . ROOT_URL . "admin/manage-categories.php");
    exit();
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit Category</h2>

        <!-- Display error message if any -->
        <?php if (isset($_SESSION['edit-category-error'])): ?>
            <div class="alert__message error">
                <p><?= htmlspecialchars($_SESSION['edit-category-error']); unset($_SESSION['edit-category-error']); ?></p>
            </div>
        <?php endif; ?>

        <!-- Edit Category Form -->
        <form action="<?= ROOT_URL ?>admin/edit-category-logic.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($category['id']) ?>">
            <div class="form__control">
                <label for="title">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="<?= htmlspecialchars($category['title']) ?>" 
                    placeholder="Enter Category Title" 
                    required>
            </div>
            <div class="form__control">
                <label for="description">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Enter Category Description" 
                    required><?= htmlspecialchars($category['description']) ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn">Update Category</button>
        </form>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
