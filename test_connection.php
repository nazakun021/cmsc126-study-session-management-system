<?php
require_once __DIR__ . '/app/config/db_connection.php';
require_once __DIR__ . '/app/Models/StudySession.php';
require_once __DIR__ . '/app/Models/CourseModel.php';

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo->query("SELECT 1");
    echo "Database connection successful!\n\n";

    // Test courses table
    echo "Testing courses table...\n";
    $courseModel = new \App\Models\CourseModel($pdo);
    $courses = $courseModel->getAllCourses();
    echo "Found " . count($courses) . " courses\n";
    print_r($courses);
    echo "\n";

    // Test review sessions table
    echo "\nTesting review sessions table...\n";
    $sessionModel = new \App\Models\StudySession($pdo);
    $sessions = $sessionModel->getAllSessions();
    echo "Found " . count($sessions) . " sessions\n";
    print_r($sessions);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
