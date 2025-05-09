<?php
// get_forum_messages.php

require_once 'db_connection2.php';

$course_id = $_GET['course_id'];

try {
    $stmt = $conn->prepare("
        SELECT cf.message_id, cf.student_id, cf.academic_id, cf.message, cf.created_at, 
               s.first_name AS student_first_name, s.last_name AS student_last_name,
               a.first_name AS academic_first_name, a.last_name AS academic_last_name
        FROM course_forum cf
        LEFT JOIN students s ON cf.student_id = s.student_id
        LEFT JOIN academics a ON cf.academic_id = a.academic_id
        WHERE cf.course_id = :course_id
        ORDER BY cf.created_at ASC
    ");
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'messages' => $messages]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
