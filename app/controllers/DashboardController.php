<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\StudySession;
use App\Models\CourseModel;

class DashboardController extends Controller {
    private $studySessionModel;
    private $courseModel;

    public function __construct() {
        global $pdo;
        $this->studySessionModel = new StudySession($pdo);
        $this->courseModel = new CourseModel($pdo);
    }

    public function showDashboard() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $subjects = $this->courseModel->getAllSubjects();
        $sessions = $this->studySessionModel->getUpcomingSessions();
        $csrfToken = $this->generateCsrfToken();

        $this->view('dashboard', [
            'subjects' => $subjects,
            'sessions' => $sessions,
            'csrfToken' => $csrfToken
        ]);
    }

    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>
