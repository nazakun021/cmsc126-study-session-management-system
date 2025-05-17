<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\StudySession; // Added StudySession model

class AdminController extends Controller {
    private $userModel;
    private $studySessionModel; // Added studySessionModel property

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
        $this->studySessionModel = new StudySession($pdo); // Initialize StudySessionModel
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Ensure CSRF token is generated if not present
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function isAdmin() {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    private function verifyCsrfToken() {
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            return false;
        }
        return true;
    }

    public function index() {
        if (!$this->isAdmin()) {
            header('Location: /login'); // Redirect to login if not admin
            exit;
        }

        $users = $this->userModel->getAllUsers(); // Method to be added in User model
        $studySessions = $this->studySessionModel->getAllSessionsAdmin(); // Method to be added in StudySession model

        $this->view('admin_dashboard', ['users' => $users, 'studySessions' => $studySessions]);
    }

    public function deleteUser() {
        header('Content-Type: application/json');
        if (!$this->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if (!$this->verifyCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userID'])) {
            $userID = filter_var($_POST['userID'], FILTER_SANITIZE_NUMBER_INT);
            
            // Prevent admin from deleting themselves
            if (isset($_SESSION['userId']) && $userID == $_SESSION['userId']) {
                echo json_encode(['success' => false, 'message' => 'Admin cannot delete themselves.']);
                exit;
            }

            $result = $this->userModel->deleteUser($userID); // Assumes deleteUser method exists in User model
            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => $result['error'] ?? 'Failed to delete user.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
        exit;
    }

    public function deleteStudySession() {
        header('Content-Type: application/json');
        if (!$this->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        if (!$this->verifyCsrfToken()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reviewSessionID'])) {
            $reviewSessionID = filter_var($_POST['reviewSessionID'], FILTER_SANITIZE_NUMBER_INT);
            
            $result = $this->studySessionModel->deleteSession($reviewSessionID); // Assumes deleteSession method exists in StudySession model
            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'Study session deleted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => $result['error'] ?? 'Failed to delete study session.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
        exit;
    }
}
?>
