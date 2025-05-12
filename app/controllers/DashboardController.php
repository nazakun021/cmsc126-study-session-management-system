<?php
require_once __DIR__ . '/../Models/StudySession.php';
require_once __DIR__ . '/../Models/CourseModel.php';

class DashboardController {
    private $studySessionModel;
    private $courseModel;

    public function __construct() {
        global $pdo;
        $this->studySessionModel = new StudySession($pdo);
        $this->courseModel = new CourseModel($pdo);
    }

    public function showDashboard() {
        // Fetch data from models
        $subjects = $this->courseModel->getAllSubjects();
        $sessions = $this->studySessionModel->getUpcomingSessions();
        $csrfToken = $this->generateCsrfToken();

        // Pass data to the view
        require_once '../app/views/dashboard.php';
    }

    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>
