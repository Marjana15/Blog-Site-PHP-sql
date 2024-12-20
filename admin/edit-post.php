<?php
include "partials/header.php";

// Fetch all categories from the database
$category_query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $category_query);

// Fetch post data if ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($id) {
        $query = "SELECT * FROM posts WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) === 1) {
            $post = mysqli_fetch_assoc($result);
        } else {
            $_SESSION['edit-post-error'] = "Post not found.";
            header('Location: ' . ROOT_URL . 'admin/');
            exit();
        }
    } else {
        $_SESSION['edit-post-error'] = "Invalid post ID.";
        header('Location: ' . ROOT_URL . 'admin/');
        exit();
    }
} else {
    header('Location: ' . ROOT_URL . 'admin/');
    exit();
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit Post</h2>
        <?php if (isset($_SESSION['edit-post-error'])): ?>
            <div class="alert__message error">
                <p><?= htmlspecialchars($_SESSION['edit-post-error']); unset($_SESSION['edit-post-error']); ?></p>
            </div>
        <?php endif; ?>
        <form action="<?= ROOT_URL ?>admin/edit-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" placeholder="Title" required>
            <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
            <input type="hidden" name="previous_thumbnail_name" value="<?= htmlspecialchars($post['thumbnail']) ?>">
            <select name="category_id" required>
                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>" <?= $post['category_id'] == $category['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['title']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?php if (isset($_SESSION['user_is_admin'])) : ?>
                <div class="form__control inline">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?>>
                    <label for="is_featured">Featured</label>
                </div>
            <?php endif; ?>
            <div id="editor">
                <?= htmlspecialchars_decode($post['body']) ?>
            </div>
            <input type="hidden" name="body" id="body-input">
            <div class="form__control">
                <label for="thumbnail">Change Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Update Post</button>
        </form>
    </div>
</section>

<!-- Include Quill styles and scripts -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    var quill = new Quill('#editor', {
        theme: 'snow'
    });

    document.querySelector('form').onsubmit = function() {
        var bodyInput = document.querySelector('#body-input');
        bodyInput.value = quill.root.innerHTML; // Get the editor content and set it to the hidden input
    };
</script>

<?php
include "../partials/footer.php";
?>
