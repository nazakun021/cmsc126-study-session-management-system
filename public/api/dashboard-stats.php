<?php
// For Error Checking
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../app/config/db_connection.php';
$pdo = require __DIR__ . '/../../app/config/db_connection.php';
require_once __DIR__ . '/../../app/core/Model.php';
require_once __DIR__ . '/../../app/Models/StudySession.php';
require_once __DIR__ . '/../../app/Models/CourseModel.php';
session_start();
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$userId = $_SESSION['userId'];
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
    'totalSessions' => count($sessions),
    'upcomingSessions' => count($upcomingSessions),
    'totalSubjects' => count($subjects),
    'avgAttendance' => $avgAttendance,
    'sessions' => $sessions
]);
exit; 