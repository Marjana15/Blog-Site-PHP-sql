<?php
require 'config/database.php';
if (isset($_SESSION['user-id'])) {
    $id = filter_var($_SESSION['user-id'], FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT avatar FROM users WHERE id='$id'";
    $result = mysqli_query($connection, $query);
    $avatar = mysqli_fetch_assoc($result);
    $avatar_url = $avatar['avatar'] ? $avatar['avatar'] : ROOT_URL . 'images/default_avatar.png'; // Fallback to default avatar if none set
}


// Fetch categories for dropdown
$categoriesQuery = "SELECT * FROM categories LIMIT 6";
$categoriesResult = mysqli_query($connection, $categoriesQuery);
$categories = [];
if ($categoriesResult) {
    while ($row = mysqli_fetch_assoc($categoriesResult)) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="max-age=3600">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContentConnect</title>
        <link rel="shortcut icon" href="https://t3.ftcdn.net/jpg/03/47/53/38/360_F_347533897_K39mGJqveEng84SEgJpBbOQWsavLbXAm.jpg" type="image/x-icon">

    <!-- CUSTOM STYLESHEET -->
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/style.css">
    <!-- ICONSCOUT CDN -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <!-- GOOGLE FONT(MONTSERRAT) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,800;1,700&display=swap" rel="stylesheet"> 
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT_URL ?>css/index.css">
</head> 
<body>

    <nav>
        <div class="container nav__container">
            <a href="<?= ROOT_URL ?>index.php" class="nav__logo">ContentConnect</a>
            <ul class="nav__items">
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : '' ?>"><a href="<?= ROOT_URL ?>blog.php">Blog</a></li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>"><a href="<?= ROOT_URL ?>about.php">About</a></li>
                <li class="<?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>"><a href="<?= ROOT_URL ?>contact.php">Contact</a></li>
                <!-- Categories Dropdown -->
                <li class="nav__categories">
                    <button class="categories__btn">Categories <i class="uil uil-angle-down"></i></button>
                    <ul class="categories__dropdown">
                        <?php foreach ($categories as $category): ?>
                            <li><a href="<?= ROOT_URL . 'category-posts.php?id=' . $category['id'] ?>"><?= htmlspecialchars($category['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <!-- Profile/SignIn Section -->
                <?php if (isset($_SESSION['user-id'])) : ?>
                <li class="nav__profile">
                <div class="avatar">
                    <img src="<?= htmlspecialchars($avatar_url) ?>" alt="User Avatar">
                </div>

                    <ul>
                        <li><a href="<?= ROOT_URL ?>./admin/add-post.php">Make Post</a></li>
                        <li><a href="<?= ROOT_URL ?>./admin/index.php">Dashboard</a></li>
                        <li><a href="<?= ROOT_URL ?>logout.php">Logout</a></li>
                    </ul>
                </li>
                <?php else : ?>
                    <li><a href="<?= ROOT_URL ?>signin.php">Sign in | Sign up</a></li>
                <?php endif ?>
            </ul>
            
            <button id="open__nav-btn"><i class="uil uil-bars"></i></button>
            <button id="close__nav-btn"><i class="uil uil-multiply"></i></button>
        </div>
    </nav>
    <!-- ======================== END OF NAV ======================== -->

<style>
.nav__categories {
  position: relative; /* Ensures the dropdown is positioned relative to this element */
}

.categories__btn {
  background: none;
  color: var(--color-white);
  border: none;
  cursor: pointer;
  font-size: 16px;
  display: flex;
  align-items: center;
}

.categories__dropdown {
  display: none; /* Hidden by default */
  position: absolute;
  top: 100%; /* Position dropdown below the button */
  left: 0;
  background: var(--color-gray-900);
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  list-style: none;
  padding: 10px;
  margin: 0;
  z-index: 100;
  border-radius: var(--card-border-radius-3);
  width: max-content; /* Adjust dropdown width to content */
}

.categories__dropdown li {
  margin: 5px 0;
}

.categories__dropdown li a {
  text-decoration: none;
  color: var(--color-white);
  display: block;
  padding: 0.5rem 1rem;
  border-radius: var(--card-border-radius-2);
  transition: var(--transition);
}

.categories__dropdown li a:hover {
  background: var(--color-primary-light); /* Light shade for hover */
  color: var(--color-white); /* Change text color on hover */
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Add subtle shadow */
}

.nav__categories:hover .categories__dropdown {
  display: block; /* Show dropdown on hover */
}

</style>
