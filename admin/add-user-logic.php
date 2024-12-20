<?php
require "config/database.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["submit"])) {
    // Sanitize input data
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);
    $avatar = $_FILES['avatar'];

    // Initialize an error array
    $errors = [];

    // Validate inputs
    if (!$firstname) $errors[] = 'Please enter your First Name';
    if (!$lastname) $errors[] = 'Please enter your Last Name';
    if (!$username) $errors[] = 'Please enter your Username';
    if (!$email) $errors[] = 'Please enter a valid Email';
    if (!in_array($is_admin, [0, 1])) $errors[] = 'Please select a valid user role';
    if (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) $errors[] = 'Password should be at least 8 characters long';
    if ($createpassword !== $confirmpassword) $errors[] = 'Passwords do not match';
    if (!$avatar['name']) $errors[] = 'Please upload an avatar';

    // Check for existing user
    if (empty($errors)) {
        $user_check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $stmt = mysqli_prepare($connection, $user_check_query);
        mysqli_stmt_bind_param($stmt, 'ss', $username, $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $errors[] = 'Username or Email already exists';
        }
    }

    // Handle avatar upload
    $avatar_name = '';
    if (empty($errors)) {
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_files)) {
            $errors[] = 'File should be in PNG, JPG, or JPEG format';
        } elseif ($avatar['size'] > 1000000) { // 1MB limit
            $errors[] = 'File size too large. Should be less than 1MB';
        } else {
            // Upload to ImgBB
            $api_key = "caadf7e83479d331c6910900a8a4f72d"; // Your ImgBB API key
            $imgbb_url = "https://api.imgbb.com/1/upload";
            
            // cURL request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $imgbb_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'key' => $api_key,
                'image' => base64_encode(file_get_contents($avatar['tmp_name'])),
            ]);

            $response = curl_exec($ch);
            curl_close($ch);

            $response_data = json_decode($response, true);

            if ($response_data && isset($response_data['data']['url'])) {
                $avatar_name = $response_data['data']['url'];
            } else {
                $errors[] = 'Failed to upload avatar to ImgBB';
            }
        }
    }

    // Redirect back on errors
    if (!empty($errors)) {
        $_SESSION['add-user-errors'] = $errors;
        $_SESSION['add-user-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'admin/add-user.php');
        exit();
    }

    // Insert new user into database
    $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);
    $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $insert_user_query);
    mysqli_stmt_bind_param($stmt, 'ssssssi', $firstname, $lastname, $username, $email, $hashed_password, $avatar_name, $is_admin);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['add-user-success'] = "User '$username' added successfully";
        header('Location: ' . ROOT_URL . 'admin/manage-users.php');
        exit();
    } else {
        $_SESSION['add-user-errors'] = ['Failed to add user: ' . mysqli_stmt_error($stmt)];
        header('Location: ' . ROOT_URL . 'admin/add-user.php');
        exit();
    }
}

// Redirect to add-user page if accessed directly
header('Location: ' . ROOT_URL . 'admin/add-user.php');
exit();
?>
