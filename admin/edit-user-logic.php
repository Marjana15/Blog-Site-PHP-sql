<?php
require "config/database.php";

// Ensure only admin users can access this page
if (!isset($_SESSION['user_is_admin'])) {
    session_destroy();
    header("Location: " . ROOT_URL . "logout.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize and validate input data
    $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    $firstname = filter_var($_POST['firstname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $lastname = filter_var($_POST['lastname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $is_admin = filter_var($_POST['userrole'], FILTER_SANITIZE_NUMBER_INT);

    // Validate required fields
    if (!$id || !$firstname || !$lastname || !in_array($is_admin, [0, 1])) {
        $_SESSION['edit-user'] = "Invalid input data on the edit user page.";
        header("Location: " . ROOT_URL . "admin/manage-users.php");
        exit();
    }

    // Update the user in the database using a prepared statement
    $query = "UPDATE users SET firstname = ?, lastname = ?, is_admin = ? WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ssii', $firstname, $lastname, $is_admin, $id);
    $result = mysqli_stmt_execute($stmt);

    // Handle query result
    if ($result) {
        $_SESSION['edit-user-success'] = "User '{$firstname} {$lastname}' updated successfully.";
    } else {
        $_SESSION['edit-user'] = "Failed to update user: " . mysqli_error($connection);
    }
}

// Redirect back to the manage users page
header("Location: " . ROOT_URL . "admin/manage-users.php");
exit();
?>
