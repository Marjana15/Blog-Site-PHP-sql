<?php
include 'partials/header.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include './config/database.php';

// Check database connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$post = null;
$author = null;
$likes = 0;
$has_liked = false;

// Check if a valid post ID is provided
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch post details
    $query = "SELECT * FROM posts WHERE id = $id";
    $result = mysqli_query($connection, $query) or die("Query failed: " . mysqli_error($connection));

    if ($post = mysqli_fetch_assoc($result)) {
        $author_id = $post['author_id'];
        $author_query = "SELECT * FROM users WHERE id = $author_id";
        $author_result = mysqli_query($connection, $author_query) or die("Author query failed: " . mysqli_error($connection));
        $author = mysqli_fetch_assoc($author_result);

        // Fetch total likes for the post
        $likes_query = "SELECT COUNT(*) AS total_likes FROM post_likes WHERE post_id = $id";
        $likes_result = mysqli_query($connection, $likes_query) or die("Likes query failed: " . mysqli_error($connection));
        $likes = mysqli_fetch_assoc($likes_result)['total_likes'];

        // Check if the current user has liked the post
        if (isset($_SESSION['user-id'])) {
            $user_id = $_SESSION['user-id'];
            $user_like_query = "SELECT * FROM post_likes WHERE post_id = $id AND user_id = $user_id";
            $user_like_result = mysqli_query($connection, $user_like_query) or die("User like query failed: " . mysqli_error($connection));
            $has_liked = mysqli_num_rows($user_like_result) > 0;
        } else {
            $user_id = null; // User is not logged in
        }
    } else {
        echo "Post not found!";
        exit;
    }
} else {
    echo "Invalid request. No post ID provided.";
    exit;
}
?>

<section id="app" class="singlepost">
    <div class="container singlepost__container">
        <div class="post_title">
        <h2><?= htmlspecialchars($post['title']) ?></h2>
        </div>
        <div class="post__author">
            <div class="post__author-avatar">
                <img src="<?= strpos($author['avatar'], 'http') === 0 ? $author['avatar'] : './images/' . htmlspecialchars($author['avatar']) ?>" alt="Author's Avatar">
            </div>
            <div class="post__author-info">
                <h5>By: <?= htmlspecialchars($author['firstname']) ?> <?= htmlspecialchars($author['lastname']) ?></h5>
                <small><?= date("M d, Y - g:i a", strtotime($post['date_time'])) ?></small>
            </div>
        </div>
        <div class="singlepost__thumbnail">
            <img src="<?= strpos($post['thumbnail'], 'http') === 0 ? $post['thumbnail'] : './images/' . htmlspecialchars($post['thumbnail']) ?>" alt="Post Thumbnail">
        </div>
        <div class="post-body">
            <?= $post['body'] ?>
        </div>

        <!-- Likes and Comments Section -->
        <div class="post-likes">
            <button @click="toggleLike" :class="{'like-btn': true, 'liked': hasLiked}">
                <i class="fas fa-heart"></i>
                <span>{{ likeCount }}</span>
            </button>
            <button @click="showCommentForm" class="comment-btn">
                <i class="fas fa-comment"></i>
                <span>{{ commentCount }}</span>
            </button>
        </div>

        <!-- Comments Section -->
        <div class="comments">
            <h3>Comments</h3>
            <div v-for="comment in comments" :key="comment.id" class="comment">
                <strong>{{ comment.author }}</strong>
                <p>{{ comment.content }}</p>
                <small>{{ comment.timestamp }}</small>
                <!-- Ensure the button appears only for the comment owner -->
                <button v-if="comment.user_id == userId" @click="deleteComment(comment.id)" class="delete-btn">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>

        <!-- Comment Form Modal -->
        <div v-if="showForm" class="comment-modal">
            <div class="comment-modal-content">
                <h4>Leave a Comment</h4>
                <textarea v-model="newComment" placeholder="Write your comment here"></textarea>
                <button @click="submitComment">Submit</button>
                <button style="background: var(--color-red);" @click="closeCommentForm">Close</button>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
new Vue({
    el: '#app',
    data: {
        postId: <?= $id ?>,
        userId: <?= isset($user_id) ? $user_id : 'null' ?>,
        likeCount: <?= $likes ?>,
        hasLiked: <?= $has_liked ? 'true' : 'false' ?>,
        commentCount: 0,
        comments: [],
        showForm: false,
        newComment: '',
    },
    created() {
        this.fetchComments();
    },
    methods: {
        toggleLike() {
            if (!this.userId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Logged In',
                    text: 'You need to be logged in to like this post.',
                });
                return;
            }

            fetch('./like_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: this.postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.likeCount = data.total_likes;
                    this.hasLiked = data.has_liked;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while liking the post.',
                });
                console.error('Error:', error);
            });
        },
        showCommentForm() {
            this.showForm = true;
        },
        closeCommentForm() {
            this.showForm = false;
        },
        fetchComments() {
            fetch('./fetch_comments.php?post_id=' + this.postId)
            .then(response => response.json())
            .then(data => {
                this.comments = data.comments;
                this.commentCount = data.comments.length;
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching comments.',
                });
                console.error('Error fetching comments:', error);
            });
        },
        submitComment() {
            if (!this.userId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Not Logged In',
                    text: 'You need to be logged in to comment on this post.',
                });
                return;
            }

            if (this.newComment.trim() === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Comment',
                    text: 'Comment cannot be empty.',
                });
                return;
            }

            fetch('./submit_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: this.postId, user_id: this.userId, comment: this.newComment })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Comment Added',
                        text: 'Your comment has been added successfully!',
                    });
                    this.newComment = '';
                    this.closeCommentForm();
                    this.fetchComments();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while submitting the comment.',
                });
                console.error('Error submitting comment:', error);
            });
        },
        deleteComment(commentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this comment!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('./delete_comment.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ comment_id: commentId, user_id: this.userId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Your comment has been deleted.',
                            });
                            this.fetchComments();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting the comment.',
                        });
                        console.error('Error deleting comment:', error);
                    });
                }
            });
        }
    }
});
</script>

<?php include './partials/footer.php'; ?>
