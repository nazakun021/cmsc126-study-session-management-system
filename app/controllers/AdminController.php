<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AdminController extends Controller {
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function isAdmin() {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
    }

    public function deleteUser() {
        header('Content-Type: application/json');
        if (!$this->isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
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
}
?>
