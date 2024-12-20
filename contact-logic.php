<?php
session_start();

if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    if (!$name || !$email || !$message) {
        $_SESSION['contact-message'] = "All fields are required.";
        $_SESSION['contact-message-type'] = "error";
        header('Location: contact.php');
        exit();
    }
    // Email settings
    $to = "marjanamarjanabegum5@gmail.com"; // Replace with your recipient email address
    $subject = "Contact Form Submission from $name";
    $body = "You have received a new message from your website contact form:\n\n".
            "Name: $name\n".
            "Email: $email\n\n".
            "Message:\n$message";
    $headers = "From: $email\r\n" .
               "Reply-To: $email\r\n";

    // Send the email
    if (mail($to, $subject, $body, $headers)) {
        $_SESSION['contact-message'] = "Your message has been sent successfully.";
        $_SESSION['contact-message-type'] = "success";
    } else {
        $_SESSION['contact-message'] = "Failed to send your message. Please try again later.";
        $_SESSION['contact-message-type'] = "error";
    }

    header('Location: contact.php');
    exit();
}
