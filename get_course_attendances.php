<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$courseId = $_GET['course_id'];

try {
    // Dersin yoklamalarını getir
    $stmt = $conn->prepare("
        SELECT a.attendance_id, a.attendance_date, a.start_time, a.attendance_code
        FROM active_attendance a
        WHERE a.course_id = :course_id
        ORDER BY a.attendance_date DESC, a.start_time DESC
    ");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'attendances' => $attendances
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>