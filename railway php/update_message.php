<?php
// update_message.php

require_once 'db_connection2.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$messageId = $data['message_id'];
$newMessage = $data['new_message'];

try {
    $stmt = $conn->prepare("UPDATE course_forum SET message = :new_message WHERE message_id = :message_id");
    $stmt->bindParam(':new_message', $newMessage);
    $stmt->bindParam(':message_id', $messageId);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
