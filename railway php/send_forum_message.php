<?php
// send_forum_message.php

require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$course_id = $data['course_id'];
$student_id = $data['student_id'] ?? null;
$academic_id = $data['academic_id'] ?? null;
$message = $data['message'];

try {
    $stmt = $conn->prepare("
        INSERT INTO course_forum (student_id, academic_id, course_id, message, created_at)
        VALUES (:student_id, :academic_id, :course_id, :message, NOW())
    ");
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':academic_id', $academic_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':message', $message);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>