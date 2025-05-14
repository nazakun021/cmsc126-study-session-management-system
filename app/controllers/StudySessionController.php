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

    public function getAllSessions() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $sessions = $this->studySessionModel->getAllSessions();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'sessions' => $sessions]);
        exit;
    }

    public function createSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
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

            $result = $this->studySessionModel->createSession($data);
            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'Study session created successfully!', 'sessionId' => $result['sessionId']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create study session.', 'errors' => $result['errors'] ?? []]);
            }
            exit;
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