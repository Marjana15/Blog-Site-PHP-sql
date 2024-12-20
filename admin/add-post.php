<?php
include "partials/header.php";

// Fetch categories from the database
$query = "SELECT * FROM categories";
$categories = mysqli_query($connection, $query);

// Retrieve and sanitize form data from the session if the form was invalid
$title = $_SESSION['add-post-data']['title'] ?? '';
$body = $_SESSION['add-post-data']['body'] ?? '';
unset($_SESSION['add-post-data']);
?>

<section class="form__section">
    <div class="container form__section-container">
        <h2>Add Post</h2>
        
        <!-- Display validation errors -->
        <?php if (isset($_SESSION['add-post'])): ?>
            <div class="alert__message error">
                <p><?= htmlspecialchars($_SESSION['add-post']); unset($_SESSION['add-post']); ?></p>
            </div>
        <?php endif; ?>
        
        <form action="<?= ROOT_URL ?>admin/add-post-logic.php" enctype="multipart/form-data" method="POST">
            <div class="form__control">
                <label for="title">Title</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="<?= htmlspecialchars($title) ?>" 
                    placeholder="Enter the post title" 
                    required>
            </div>
            
            <div class="form__control">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="" disabled selected>Select a category</option>
                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['title']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <?php if (isset($_SESSION["user_is_admin"])): ?>
            <div class="form__control inline">
                <input type="checkbox" name="is_featured" id="is_featured" value="1">
                <label for="is_featured">Mark as Featured</label>
            </div>
            <?php endif; ?>

            <!-- Quill Editor for body content -->
            <div class="form__control">
                <label for="editor">Body</label>
                <div id="editor">
                    <?= $body ? htmlspecialchars_decode($body) : '<p>Write your post content here...</p>' ?>
                </div>
            </div>
            <input type="hidden" name="body" id="body-input">

            <div class="form__control">
                <label for="thumbnail">Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnail" required>
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
    theme: 'snow',
    bounds: '.form__section-container', // Limit resizing to the parent container
    placeholder: 'Write your post content here...',
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{ 'header': 1 }, { 'header': 2 }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            [{ 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }],
            [{ 'size': ['small', false, 'large', 'huge'] }],
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'font': [] }],
            [{ 'align': [] }],
            ['clean']
        ]
    }
});

// Ensure Quill editor content is trimmed to avoid overloading
document.querySelector('form').onsubmit = function() {
    var bodyInput = document.querySelector('#body-input');
    bodyInput.value = quill.root.innerHTML.trim();

    if (quill.getLength() > 10000) {
        alert("The content is too large. Please reduce the text length.");
        return false; // Prevent form submission
    }
};


    // Ensure Quill editor content is sent in the form
    document.querySelector('form').onsubmit = function() {
        var bodyInput = document.querySelector('#body-input');
        bodyInput.value = quill.root.innerHTML.trim(); // Capture and set the HTML content
    };
</script>

<?php include '../partials/footer.php'; ?>
