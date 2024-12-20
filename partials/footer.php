<footer>
    <div class="footer__socials">
        <a href="https://github.com/Marjana15" target="_blank"><i class="uil uil-youtube"></i></a>
        <a href="https://github.com/Marjana15" target="_blank"><i class="uil uil-instagram-alt"></i></a>
        <a href="https://github.com/Marjana15" target="_blank"><i class="uil uil-linkedin"></i></a>
        <a href="" target="_blank"><i class="uil uil-facebook-f"></i></a>
    </div>
    <div class="container footer__container">
        <!-- Categories Section -->
        <article id="footer-categories">
            <h4>Categories</h4>
            <ul>
                <li v-for="(category, index) in categories.slice(0, 6)" :key="category.id">
                    <a :href="`<?= ROOT_URL ?>category-posts.php?id=${category.id}`">{{ category.title }}</a>
                </li>
            </ul>
        </article>
        <!-- Blog Section -->
        <article id="footer-blogs">
            <h4>Blog</h4>
            <ul>
                <li v-for="(post, index) in posts.slice(0, 6)" :key="post.id">
                    <a :href="`<?= ROOT_URL ?>post.php?id=${post.id}`">{{ post.title }}</a>
                </li>
            </ul>
        </article>
        <article>
            <h4>Support</h4>
            <ul>
                <li><a href="">Online Support</a></li>
                <li><a href="">Call Numbers</a></li>
                <li><a href="">Emails</a></li>
                <li><a href="">Social Support</a></li>
                <li><a href="">Location</a></li>
            </ul>
        </article>
        <article>
            <h4>PermaLinks</h4>
            <ul>
                <li><a href="<?= ROOT_URL ?>">Home</a></li>
                <li><a href="<?= ROOT_URL ?>blog.php">Blog</a></li>
                <li><a href="<?= ROOT_URL ?>about.php">About</a></li>
                <li><a href="<?= ROOT_URL ?>contact.php">Contact</a></li>
            </ul>
        </article>
    </div>
    <div class="footer__copyright">
        <small>Copyright &copy; Marjana and Mou</small>
    </div>
    <script src="<?= ROOT_URL ?>js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script>
        // Vue instance for Categories
        new Vue({
            el: '#footer-categories',
            data: {
                categories: []
            },
            created() {
                fetch('<?= ROOT_URL ?>fetch_categories.php')
                    .then(response => response.json())
                    .then(data => {
                        this.categories = data.slice(0, 6);
                    })
                    .catch(error => console.error('Error fetching categories:', error));
            }
        });

        // Vue instance for Blogs
        new Vue({
            el: '#footer-blogs',
            data: {
                posts: []
            },
            created() {
                fetch('<?= ROOT_URL ?>fetch_latest_posts.php')
                    .then(response => response.json())
                    .then(data => {
                        this.posts = data.slice(0, 6);
                    })
                    .catch(error => console.error('Error fetching posts:', error));
            }
        });
    </script>
</footer>
