<?php
require '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sender_id = filter_var($_GET['sender_id'], FILTER_SANITIZE_NUMBER_INT);
    $receiver_id = filter_var($_GET['receiver_id'], FILTER_SANITIZE_NUMBER_INT);

    $messages_query = "SELECT messages.*, 
                              sender.firstname AS sender_name, 
                              receiver.firstname AS receiver_name 
                       FROM messages 
                       JOIN users AS sender ON messages.sender_id = sender.id 
                       JOIN users AS receiver ON messages.receiver_id = receiver.id 
                       WHERE (sender_id = $sender_id AND receiver_id = $receiver_id) 
                          OR (sender_id = $receiver_id AND receiver_id = $sender_id) 
                       ORDER BY timestamp ASC";
    $messages_result = mysqli_query($connection, $messages_query);

    $messages = mysqli_fetch_all($messages_result, MYSQLI_ASSOC);
    echo json_encode($messages);
    exit();
}
?>
