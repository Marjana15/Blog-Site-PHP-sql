<?php
include "partials/header.php";

// Fetch current user details
$current_user_id = $_SESSION['user-id'];
$query = "SELECT firstname, lastname, username, avatar FROM users WHERE id = $current_user_id";
$result = mysqli_query($connection, $query);

// Check for query errors
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

$user = mysqli_fetch_assoc($result);

// Handle form submission
if (isset($_POST['submit'])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Handle avatar upload
    $avatar = $_FILES['avatar'];
    $avatar_name = $user['avatar']; // Default to current avatar

    if ($avatar['name']) { // If a new avatar is uploaded
        $avatar_tmp_name = $avatar['tmp_name'];

        // Validate file type
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
        if (in_array($extension, $allowed_files)) {
            // Validate file size
            if ($avatar['size'] < 1000000) { // 1MB limit
                // Upload the avatar to ImgBB
                $api_key = "caadf7e83479d331c6910900a8a4f72d"; // Your ImgBB API key
                $imgbb_url = "https://api.imgbb.com/1/upload";
                
                // cURL request
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $imgbb_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'key' => $api_key,
                    'image' => base64_encode(file_get_contents($avatar_tmp_name)),
                ]);

                $response = curl_exec($ch);
                curl_close($ch);

                $response_data = json_decode($response, true);

                if ($response_data && isset($response_data['data']['url'])) {
                    $avatar_name = $response_data['data']['url'];

                    // Delete old avatar if it exists and is not a default avatar
                    if ($user['avatar'] && strpos($user['avatar'], 'http') === false && $user['avatar'] !== 'default_avatar.png') {
                        $old_avatar_path = '../images/' . $user['avatar'];
                        if (file_exists($old_avatar_path)) {
                            unlink($old_avatar_path);
                        }
                    }
                } else {
                    $_SESSION['profile-update-error'] = "Failed to upload avatar to ImgBB.";
                    header("Location: " . ROOT_URL . "admin/manage-profile.php");
                    exit();
                }
            } else {
                $_SESSION['profile-update-error'] = "File size too large. Should be less than 1MB.";
            }
        } else {
            $_SESSION['profile-update-error'] = "File must be a png, jpg, or jpeg.";
        }
    }

    // Update user details in the database
    if (!isset($_SESSION['profile-update-error'])) {
        $update_query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', username = '$username', avatar = '$avatar_name'";
        if ($password) {
            $update_query .= ", password = '$password'";
        }
        $update_query .= " WHERE id = $current_user_id";
    
        $update_result = mysqli_query($connection, $update_query);
    
        if ($update_result) {
            $_SESSION['profile-update-success'] = "Profile updated successfully!";
            // SweetAlert Success Popup
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Profile Updated!',
                    text: 'Your profile has been updated successfully.',
                    showConfirmButton: true,
                    confirmButtonText: 'Go to Dashboard'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '" . ROOT_URL . "admin/index.php';
                    }
                });
            </script>";
            exit();
        } else {
            $_SESSION['profile-update-error'] = "Failed to update profile: " . mysqli_error($connection);
        }
    }    
}
?>

<section class="dashboard">
    <div class="container dashboard__container">
        <aside>
            <ul>
                <li><a href="<?= ROOT_URL ?>admin/add-post.php"><i class="uil uil-pen"></i><h5>Add Post</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/index.php"><i class="uil uil-postcard"></i><h5>Manage Posts</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/manage-profile.php" class="active"><i class="uil uil-user"></i><h5>Manage Profile</h5></a></li>
                <li><a href="<?= ROOT_URL ?>admin/chat.php"><i class="uil uil-comments"></i><h5>Chat</h5></a></li> <!-- Chat Option -->
                <?php if (isset($_SESSION['user_is_admin'])): ?>
                    <li><a href="<?= ROOT_URL ?>admin/manage-posts.php"><i class="uil uil-postcard"></i><h5>Manage All Posts</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-user.php"><i class="uil uil-user-plus"></i><h5>Add User</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-users.php"><i class="uil uil-users-alt"></i><h5>Manage Users</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/add-category.php"><i class="uil uil-edit"></i><h5>Add Category</h5></a></li>
                    <li><a href="<?= ROOT_URL ?>admin/manage-categories.php"><i class="uil uil-list-ul"></i><h5>Manage Categories</h5></a></li>
                <?php endif; ?>
            </ul>
        </aside>
        <main>
            <h2>Manage Profile</h2>
            <?php if (isset($_SESSION['profile-update-success'])): ?>
                <div class="alert__message success">
                    <p><?= $_SESSION['profile-update-success']; unset($_SESSION['profile-update-success']); ?></p>
                </div>
            <?php elseif (isset($_SESSION['profile-update-error'])): ?>
                <div class="alert__message error">
                    <p><?= $_SESSION['profile-update-error']; unset($_SESSION['profile-update-error']); ?></p>
                </div>
            <?php endif; ?>
            <form action="<?= ROOT_URL ?>admin/manage-profile.php" method="POST" enctype="multipart/form-data">
                <div class="form__control">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" id="firstname" value="<?= $user['firstname'] ?>" placeholder="First Name" required>
                </div>
                <div class="form__control">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" id="lastname" value="<?= $user['lastname'] ?>" placeholder="Last Name" required>
                </div>
                <div class="form__control">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" value="<?= $user['username'] ?>" placeholder="Username" required>
                </div>
                <div class="form__control">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" placeholder="New Password (optional)">
                </div>
                <div class="form__control">
                    <label for="avatar">Avatar</label>
                    <input type="file" name="avatar" id="avatar">
                    <?php if ($user['avatar']): ?>
                        <img src="<?= strpos($user['avatar'], 'http') === 0 ? $user['avatar'] : '../images/' . $user['avatar'] ?>" 
                             alt="Current Avatar" 
                             class="avatar-preview" 
                             style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #ccc;">
                    <?php endif; ?>
                </div>
                <button type="submit" name="submit" class="btn">Update Profile</button>
            </form>
        </main>
    </div>
</section>

<?php
include "../partials/footer.php";
?>
