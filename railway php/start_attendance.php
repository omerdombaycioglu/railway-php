<?php
include 'db_connection2.php';

// Saat dilimini İstanbul olarak ayarla
date_default_timezone_set('Europe/Istanbul');

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "error" => "Geçersiz veri."]);
    exit();
}

$courseId = $data['course_id'];
$academicId = $data['academic_id'];
$attendanceDate = $data['attendance_date'];
$durationMinutes = $data['duration_minutes']; // int olarak alıyoruz

// Başlangıç zamanını şu an olarak ayarla
$startTime = date("H:i:s");

// Bitiş zamanını başlangıç zamanı + durationMinutes olarak ayarla
$endTime = date("H:i:s", strtotime("+$durationMinutes minutes"));

// 6 haneli rastgele bir kod oluştur
$attendanceCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

try {
    $sql = "INSERT INTO active_attendance (course_id, academic_id, attendance_date, start_time, end_time, attendance_code) 
            VALUES (:course_id, :academic_id, :attendance_date, :start_time, :end_time, :attendance_code)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':course_id' => $courseId,
        ':academic_id' => $academicId,
        ':attendance_date' => $attendanceDate,
        ':start_time' => $startTime,
        ':end_time' => $endTime,
        ':attendance_code' => $attendanceCode,
    ]);

    echo json_encode(["success" => true, "attendance_code" => $attendanceCode]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
