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

    public function updateSession() { // Removed $sessionId parameter
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'An unknown error occurred.'];

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['message'] = 'Invalid request method.';
                echo json_encode($response);
                exit;
            }

            // DEBUGGING CSRF
            error_log("UPDATE - SESSION CSRF Token: " . ($_SESSION['csrf_token'] ?? 'NOT SET'));
            error_log("UPDATE - POST CSRF Token: " . ($_POST['csrf_token'] ?? 'NOT SET'));

            if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $response['message'] = 'Invalid or missing CSRF token.';
                $response['debug_session_csrf'] = $_SESSION['csrf_token'] ?? 'Session token not set';
                $response['debug_post_csrf'] = $_POST['csrf_token'] ?? 'POST token not set';
                echo json_encode($response);
                exit;
            }

            $requiredFields = [
                'reviewSessionID', 'reviewTitle', 'subjectID', 'reviewTopic',
                'reviewDate', 'reviewStartTime', 'reviewEndTime', 'reviewLocation'
            ];
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $missingFields[] = $field;
                }
            }

            if (!empty($missingFields)) {
                $response['message'] = 'Missing required fields: ' . implode(', ', $missingFields);
                echo json_encode($response);
                exit;
            }

            $sessionId = trim($_POST['reviewSessionID']);
            
            // Permission check
            $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
            if (!$isAdmin) {
                $sessionDetails = $this->studySessionModel->getSessionById($sessionId); 
                if (!$sessionDetails || !isset($sessionDetails['creatorUserID']) || $sessionDetails['creatorUserID'] != $_SESSION['userId']) {
                    $response['message'] = 'You are not authorized to update this session or session not found.';
                    echo json_encode($response);
                    exit;
                }
            }

            $data = [
                'reviewTitle' => trim($_POST['reviewTitle']),
                'subjectID' => trim($_POST['subjectID']),
                'reviewTopic' => trim($_POST['reviewTopic']),
                'reviewDate' => trim($_POST['reviewDate']),
                'reviewStartTime' => trim($_POST['reviewStartTime']),
                'reviewEndTime' => trim($_POST['reviewEndTime']),
                'reviewLocation' => trim($_POST['reviewLocation']),
                'reviewDescription' => isset($_POST['reviewDescription']) ? trim($_POST['reviewDescription']) : '',
                // 'reviewStatus' => $_POST['reviewStatus'] ?? 'scheduled' // Status might not be directly updatable by user this way
            ];

            // Ensure creatorUserID is not part of the $data array for update, it should not be changed.
            // The permission check above should handle authorization.

            if ($this->studySessionModel->updateSession($sessionId, $data)) {
                $response = ['success' => true, 'message' => 'Study session updated successfully!'];
            } else {
                $response['message'] = 'Failed to update study session in the database.';
            }
        } catch (\PDOException $e) {
            error_log("Database error in updateSession: " . $e->getMessage());
            $response['message'] = "Database error: " . $e->getMessage(); // Consider a more generic message for production
        } catch (\Exception $e) {
            error_log("General error in updateSession: " . $e->getMessage());
            $response['message'] = "An error occurred: " . $e->getMessage(); // Consider a more generic message for production
        }
        
        echo json_encode($response);
        exit;
    }

    public function deleteSession() { // Removed $sessionId parameter
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'An unknown error occurred.'];

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $response['message'] = 'Invalid request method.';
                echo json_encode($response);
                exit;
            }

            // DEBUGGING CSRF
            error_log("DELETE - SESSION CSRF Token: " . ($_SESSION['csrf_token'] ?? 'NOT SET'));
            error_log("DELETE - POST CSRF Token: " . ($_POST['csrf_token'] ?? 'NOT SET'));

            if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $response['message'] = 'Invalid or missing CSRF token.';
                $response['debug_session_csrf'] = $_SESSION['csrf_token'] ?? 'Session token not set';
                $response['debug_post_csrf'] = $_POST['csrf_token'] ?? 'POST token not set';
                echo json_encode($response);
                exit;
            }

            if (empty($_POST['reviewSessionID'])) {
                $response['message'] = 'Missing reviewSessionID.';
                echo json_encode($response);
                exit;
            }
            
            $sessionId = trim($_POST['reviewSessionID']);

            // Permission check
            $isAdmin = isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
            if (!$isAdmin) {
                $sessionDetails = $this->studySessionModel->getSessionById($sessionId);
                if (!$sessionDetails || !isset($sessionDetails['creatorUserID']) || $sessionDetails['creatorUserID'] != $_SESSION['userId']) {
                    $response['message'] = 'You are not authorized to delete this session or session not found.';
                    echo json_encode($response);
                    exit;
                }
            }

            if ($this->studySessionModel->deleteSession($sessionId)) {
                $response = ['success' => true, 'message' => 'Study session deleted successfully!'];
            } else {
                $response['message'] = 'Failed to delete study session from the database.';
            }
        } catch (\PDOException $e) {
            error_log("Database error in deleteSession: " . $e->getMessage());
            $response['message'] = "Database error: " . $e->getMessage(); // Consider a more generic message for production
        } catch (\Exception $e) {
            error_log("General error in deleteSession: " . $e->getMessage());
            $response['message'] = "An error occurred: " . $e->getMessage(); // Consider a more generic message for production
        }

        echo json_encode($response);
        exit;
    }
}
?>