<?php
include "partials/header.php";

// fetch categories from database
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);

// get back form data if form was invalid
$title = $_SESSION['add-post-data']['title'] ?? null;
$body = $_SESSION['add-post-data']['body'] ?? null;
unset($_SESSION['add-post-data']);
?>
<section class="form__section">
    <div class="container form__section-container">
        <h2>Add Post</h2>
        <?php if(isset($_SESSION['add-post'])): ?>
        <div class="alert__message error">
            <p>
                <?= $_SESSION['add-post']; unset($_SESSION['add-post']); ?>
            </p>
        </div>
        <?php endif; ?>
        <form action="<?= ROOT_URL ?>admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
            <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Title">
            <select name="category_id">
                <?php while($category = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['title']) ?></option>
                <?php endwhile; ?>
            </select>
            <?php if(isset($_SESSION["user_is_admin"])): ?>
            <div class="form__control inline">
                <input type="checkbox" name="is_featured" id="is_featured" value="1" checked>
                <label for="is_featured">Featured</label>
            </div>
            <?php endif; ?>
            <!-- Quill Editor -->
            <div id="editor">
                <p>Write your post content here...</p>
            </div>
            <input type="hidden" name="body" id="body-input">
            
            <div class="form__control">
                <label for="thumbnail">Add Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail">
            </div>
            <button type="submit" name="submit" class="btn">Add Post</button>
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
        bodyInput.value = quill.root.innerHTML; // Get the HTML content
    };
</script>


<?php include '../partials/footer.php'; ?>
