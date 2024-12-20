<?php
require "./config/database.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST["submit"])) {
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $createpassword = filter_var($_POST['createpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $confirmpassword = filter_var($_POST['confirmpassword'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $avatar = $_FILES['avatar'];

    // Initialize error flag
    $hasError = false;

    // Validate input fields
    if (!$firstname) {
        $_SESSION['signup'] = 'Please enter your First Name';
        $hasError = true;
    } elseif (!$lastname) {
        $_SESSION['signup'] = 'Please enter your Last Name';
        $hasError = true;
    } elseif (!$username) {
        $_SESSION['signup'] = 'Please enter your Username';
        $hasError = true;
    } elseif (!$email) {
        $_SESSION['signup'] = 'Please enter a valid Email';
        $hasError = true;
    } elseif (strlen($createpassword) < 8 || strlen($confirmpassword) < 8) {
        $_SESSION['signup'] = 'Password should be 8+ characters';
        $hasError = true;
    } elseif ($createpassword !== $confirmpassword) {
        $_SESSION['signup'] = "Passwords do not match";
        $hasError = true;
    }

    // Check if username or email already exists
    if (!$hasError) {
        $user_check_query = "SELECT * FROM users WHERE username=? OR email=?";
        $stmt = $connection->prepare($user_check_query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $user_check_result = $stmt->get_result();

        if ($user_check_result->num_rows > 0) {
            $_SESSION['signup'] = "Username or Email already exists";
            $hasError = true;
        }
    }

    // Handle avatar upload or assign default
    $avatar_url = "https://cdn.icon-icons.com/icons2/1378/PNG/512/avatardefault_92824.png"; // Default avatar URL
    if (!$hasError && $avatar['name']) {
        $allowed_files = ['png', 'jpg', 'jpeg'];
        $extension = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));

        if (in_array($extension, $allowed_files)) {
            if ($avatar['size'] < 1000000) { // Validate file size < 1MB
                // Prepare API request for ImgBB
                $api_key = 'caadf7e83479d331c6910900a8a4f72d'; // Replace with your actual API key
                $imgbb_url = 'https://api.imgbb.com/1/upload';

                // Convert file to base64
                $base64_image = base64_encode(file_get_contents($avatar['tmp_name']));

                // Initialize cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $imgbb_url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    'key' => $api_key,
                    'image' => $base64_image,
                ]);

                $response = curl_exec($ch);
                curl_close($ch);

                $response_data = json_decode($response, true);

                // Check for successful upload
                if (isset($response_data['data']['url'])) {
                    $avatar_url = $response_data['data']['url'];
                } else {
                    $_SESSION['signup'] = "Failed to upload avatar to ImgBB. Please try again.";
                    $hasError = true;
                }
            } else {
                $_SESSION['signup'] = "File size too large. Should be less than 1MB.";
                $hasError = true;
            }
        } else {
            $_SESSION['signup'] = "File should be a png, jpg, or jpeg.";
            $hasError = true;
        }
    }

    // Redirect back to signup on error
    if ($hasError) {
        $_SESSION['signup-data'] = $_POST;
        header('Location: ' . ROOT_URL . 'signup.php');
        die();
    } else {
        // Insert user into the database
        $hashed_password = password_hash($createpassword, PASSWORD_DEFAULT);
        $insert_user_query = "INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $connection->prepare($insert_user_query);
        $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $hashed_password, $avatar_url);

        if ($stmt->execute()) {
            $_SESSION['signup-success'] = "Registration Successful! Please log in.";
            header('Location: ' . ROOT_URL . 'signin.php');
            die();
        } else {
            $_SESSION['signup'] = "Something went wrong. Please try again later.";
            header('Location: ' . ROOT_URL . 'signup.php');
            die();
        }
    }
} else {
    header('Location: ' . ROOT_URL . 'signup.php');
    die();
}
