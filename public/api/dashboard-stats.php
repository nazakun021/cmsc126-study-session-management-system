<?php
// For Error Checking (disable for production APIs)
error_reporting(E_ERROR | E_PARSE);

file_put_contents(__DIR__ . '/debug.log', date('c') . ' - dashboard-stats.php called' . PHP_EOL, FILE_APPEND);

require_once __DIR__ . '/../../app/config/init.php';
require_once __DIR__ . '/../../app/config/db_connection.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/Models/StudySession.php';
require_once __DIR__ . '/../../app/Models/CourseModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$userId = $_SESSION['userId'];
$pdo = require __DIR__ . '/../../app/config/db_connection.php';
$studySessionModel = new \App\Models\StudySession($pdo);
$courseModel = new \App\Models\CourseModel($pdo);
$sessions = $studySessionModel->getAllSessions();
$subjects = $courseModel->getSubjectsByUserId($userId);
$upcomingSessions = array_filter($sessions, function($session) {
    return strtotime($session['reviewDate']) >= strtotime('today');
});
$avgAttendance = 100; // Placeholder, replace with real calculation if available
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'sessions' => $sessions,
    'totalSessions' => count($sessions),
    'upcomingSessions' => count($upcomingSessions),
    'totalSubjects' => count($subjects),
    'avgAttendance' => $avgAttendance
]);
exit; 