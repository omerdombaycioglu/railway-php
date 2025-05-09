<?php
header('Content-Type: application/json');
require_once 'db_connection2.php';

$courseId = $_GET['course_id'];

try {
    $stmt = $conn->prepare("
        SELECT s.student_id, s.first_name, s.last_name, s.student_number, s.email
        FROM student_courses sc
        JOIN students s ON sc.student_id = s.student_id
        WHERE sc.course_id = :course_id
        ORDER BY s.last_name, s.first_name
    ");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'students' => $students
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
