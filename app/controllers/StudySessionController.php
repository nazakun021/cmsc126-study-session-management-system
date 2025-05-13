<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\StudySession;

class StudySessionController extends Controller {
    private $studySessionModel;

    public function __construct() {
        global $pdo;
        $this->studySessionModel = new StudySession($pdo);
    }

    public function createSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'subjectID' => $_POST['subjectID'],
                'reviewTitle' => $_POST['reviewTitle'],
                'reviewDate' => $_POST['reviewDate'],
                'reviewStartTime' => $_POST['reviewStartTime'],
                'reviewEndTime' => $_POST['reviewEndTime'],
                'reviewLocation' => $_POST['reviewLocation'],
                'reviewDescription' => $_POST['reviewDescription'],
                'reviewTopic' => $_POST['reviewTopic'],
                'reviewStatus' => 'scheduled'
            ];

            if ($this->studySessionModel->createSession($data)) {
                $_SESSION['success'] = "Study session created successfully!";
            } else {
                $_SESSION['error'] = "Failed to create study session.";
            }
            $this->redirect('/cmsc126-study-session-management-system/public/dashboard');
        }
    }

    public function updateSession($sessionId) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'subjectID' => $_POST['subjectID'],
                'reviewTitle' => $_POST['reviewTitle'],
                'reviewDate' => $_POST['reviewDate'],
                'reviewStartTime' => $_POST['reviewStartTime'],
                'reviewEndTime' => $_POST['reviewEndTime'],
                'reviewLocation' => $_POST['reviewLocation'],
                'reviewDescription' => $_POST['reviewDescription'],
                'reviewTopic' => $_POST['reviewTopic'],
                'reviewStatus' => $_POST['reviewStatus'] ?? 'scheduled'
            ];

            if ($this->studySessionModel->updateSession($sessionId, $data)) {
                $_SESSION['success'] = "Study session updated successfully!";
            } else {
                $_SESSION['error'] = "Failed to update study session.";
            }
            $this->redirect('/cmsc126-study-session-management-system/public/dashboard');
        }
    }

    public function deleteSession($sessionId) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($this->studySessionModel->deleteSession($sessionId)) {
            $_SESSION['success'] = "Study session deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete study session.";
        }
        $this->redirect('/cmsc126-study-session-management-system/public/dashboard');
    }
}
?> 