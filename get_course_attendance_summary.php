<?php
header('Content-Type: application/json');
require_once 'db_connection2.php';

$courseId = $_GET['course_id'];

try {
    // Dersin tüm yoklamalarını al
    $stmt = $conn->prepare("
        SELECT a.attendance_id, a.attendance_date, a.start_time
        FROM active_attendance a
        WHERE a.course_id = :course_id
        ORDER BY a.attendance_date, a.start_time
    ");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Dersin tüm öğrencilerini al (student_number eklendi)
    $stmt = $conn->prepare("
        SELECT s.student_id, s.first_name, s.last_name, s.student_number
        FROM student_courses sc
        JOIN students s ON sc.student_id = s.student_id
        WHERE sc.course_id = :course_id
        ORDER BY s.last_name, s.first_name
    ");
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $attendanceSummary = [];

    foreach ($students as $student) {
        $studentAttendances = [];
        $presentCount = 0;

        foreach ($attendances as $attendance) {
            // Öğrencinin bu yoklamadaki durumunu kontrol et
            $stmt = $conn->prepare("
                SELECT status FROM attendance_records
                WHERE attendance_id = :attendance_id AND student_id = :student_id
            ");
            $stmt->bindParam(':attendance_id', $attendance['attendance_id']);
            $stmt->bindParam(':student_id', $student['student_id']);
            $stmt->execute();
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            $status = $record ? $record['status'] : 'absent';
            if ($status == 'present') $presentCount++;

            $studentAttendances[] = [
                'date' => $attendance['attendance_date'],
                'time' => $attendance['start_time'],
                'status' => $status
            ];
        }

        $attendanceRate = count($attendances) > 0 
            ? round(($presentCount / count($attendances)) * 100, 2)
            : 0;

        $attendanceSummary[] = [
            'student_id' => $student['student_id'],
            'first_name' => $student['first_name'],
            'last_name' => $student['last_name'],
            'student_number' => $student['student_number'], // Öğrenci numarası eklendi
            'attendances' => $studentAttendances,
            'attendance_rate' => $attendanceRate
        ];
    }

    echo json_encode([
        'success' => true,
        'attendance_summary' => $attendanceSummary
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
