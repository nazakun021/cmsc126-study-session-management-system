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
            
            // Validate required fields
            $requiredFields = ['subjectID', 'reviewTitle', 'reviewDate', 'reviewStartTime', 'reviewEndTime', 'reviewLocation'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required fields',
                    'errors' => ['The following fields are required: ' . implode(', ', $missingFields)]
                ]);
                exit;
            }

            // Log the incoming data for debugging
            error_log('Received POST data for session creation: ' . print_r($_POST, true));

            $data = [
                'creatorUserID' => $_SESSION['userId'],
                'subjectID' => $_POST['subjectID'],
                'reviewTitle' => $_POST['reviewTitle'],
                'reviewDate' => $_POST['reviewDate'],
                'reviewStartTime' => $_POST['reviewStartTime'],
                'reviewEndTime' => $_POST['reviewEndTime'],
                'reviewLocation' => $_POST['reviewLocation'],
                'reviewDescription' => $_POST['reviewDescription'] ?? '',
                'reviewTopic' => $_POST['reviewTopic'] ?? '',
                'reviewStatus' => 'scheduled'
            ];

            $result = $this->studySessionModel->createSession($data);
            
            // Log the result for debugging
            error_log('Session creation result: ' . print_r($result, true));
            
            echo json_encode($result);
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