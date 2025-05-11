<?php
require_once __DIR__ . '/../Models/StudySession.php';

class StudySessionController {
    private $studySessionModel;
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
        $this->studySessionModel = new StudySession($pdo);
    }

    public function createSession() {
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
            
            header("Location: /cmsc126-study-session-management-system/public/dashboard");
            exit;
        }
    }

    public function updateSession($sessionId) {
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
            
            header("Location: /cmsc126-study-session-management-system/public/dashboard");
            exit;
        }
    }

    public function deleteSession($sessionId) {
        if ($this->studySessionModel->deleteSession($sessionId)) {
            $_SESSION['success'] = "Study session deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete study session.";
        }
        
        header("Location: /cmsc126-study-session-management-system/public/dashboard");
        exit;
    }
}
?> 