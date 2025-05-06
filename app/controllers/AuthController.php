<?php
// // Add this BEFORE the require_once
// $userModelPath = __DIR__ . '/../../Models/User.php';
// if (!file_exists($userModelPath)) {
//     die("ERROR: User.php not found at: $userModelPath");
// }
// require_once $userModelPath;

require_once __DIR__ . '/../../Models/User.php'; 
// require_once '../Models/User.php';

class AuthController {
    protected $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function showLogin() {
        require_once '../views/auth/login.php'; // Render Login View
    }

    public function showRegister() {
        require_once '../views/auth/register.php'; // Render Register View
    }

    // Handle Login Form Submission
    public function login() {
        // Basic Input Validation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);

            if ($user) {
                $_SESSION['userId'] = $user['userID'];      
                $_SESSION['username'] = $username;  

                // REDIRECT ALL USERS TO THE SAME DASHBOARD 
                header("Location: /dashboard");
                exit;
            } else {
            // Password incorrect
            $error = "Invalid credentials!";
                header("Location: /login");
            }
        }
    }

    // Handle Registration Form Submission
    public function register() {
        // Basic Input Validation
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password']; 
            $confirmPassword = $_POST['confirmPassword'];
            $courseId = trim($_POST['courseID']);
            
            if ($this->userModel->register($username, $email, $password, $confirmPassword, $courseId)) {
                header("Location: /login?success=1");
                exit;
            } else {
                $error = "Registration failed!";
                require_once '../views/auth/register.php';
            }
        }
    }

    // Show Dashboard (Protected Route)
    public function showDashboard() {
        if (!isset($_SESSION['userId'])) {
            header("Location: /login");
            exit;
        }
        require_once '../views/dashboard.php';
    }

    // Logout User
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /login");
    }
}
?>
