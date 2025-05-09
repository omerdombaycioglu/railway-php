<?php
include 'db_connection2.php';

header("Content-Type: application/json");

$attendance_id = $_GET['attendance_id'];
$student_id = $_GET['student_id'];

try {
    $sql = "SELECT * FROM attendance_records WHERE attendance_id = :attendance_id AND student_id = :student_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':attendance_id' => $attendance_id, ':student_id' => $student_id]);
    $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($attendance) {
        echo json_encode(["attended" => true]);
    } else {
        echo json_encode(["attended" => false]);
    }
} catch (PDOException $e) {
    echo json_encode(["attended" => false, "error" => $e->getMessage()]);
}
?>
