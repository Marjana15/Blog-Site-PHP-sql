<?php
include "partials/header.php";

$category_query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $category_query);

// fetch post data from database if id is set
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM posts WHERE id= $id";
    $result = mysqli_query($connection, $query);
    $post = mysqli_fetch_assoc($result);
}
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Edit Post</h2>
        <form action="<?= ROOT_URL ?>admin/edit-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" value="<?= $post['title'] ?>" name="title" placeholder="Title">
            <input type="hidden" value="<?= $post['id'] ?>" name="id">
            <input type="hidden" value="<?= $post['thumbnail'] ?>" name="previous_thumbnail_name">
            <select name="category_id">
                <?php while ($category = mysqli_fetch_assoc($categories)) : ?>
                    <option value='<?= $category['id'] ?>' <?= $post['category_id'] == $category['id'] ? 'selected' : '' ?>>
                        <?= $category['title'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?php if (isset($_SESSION['user_is_admin'])) : ?>
                <div class="form__control inline">
                    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= $post['is_featured'] ? 'checked' : '' ?>>
                    <label for="is_featured">Featured</label>
                </div>
            <?php endif; ?>
            <!-- Quill Editor for the body -->
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
        bodyInput.value = quill.root.innerHTML; // Set the editor content to the hidden input
    };
</script>


<?php
include "../partials/footer.php";
?>
