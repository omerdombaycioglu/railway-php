<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$attendanceId = $_GET['attendance_id'];

try {
    // Yoklamaya katılan öğrencileri getir
    $stmt = $conn->prepare("
        SELECT s.student_id, s.first_name, s.last_name, s.student_number, 
               ar.status, ar.recorded_at
        FROM attendance_records ar
        JOIN students s ON ar.student_id = s.student_id
        WHERE ar.attendance_id = :attendance_id
        ORDER BY s.last_name, s.first_name
    ");
    $stmt->bindParam(':attendance_id', $attendanceId);
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