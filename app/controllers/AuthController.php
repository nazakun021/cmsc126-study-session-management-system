<?php
$userModelPath = __DIR__ . '/../Models/User.php';
if (!file_exists($userModelPath)) {
    die("ERROR: User.php not found at: $userModelPath");
}
require_once $userModelPath;

$courseModelPath = __DIR__ . '/../Models/CourseModel.php';
if (!file_exists($courseModelPath)) {
    die("ERROR: CourseModel.php not found at: $courseModelPath");
}
require_once $courseModelPath;

require_once __DIR__ . '/../config/db_connection.php';

class AuthController {
    protected $userModel;
    protected $courseModel;
    
    public function __construct() {
        $this->userModel = new User();
        global $pdo; // Access PDO instance from db_connection.php 
        $this->courseModel = new CourseModel($pdo);
    }

    public function showLogin() {
        $rootPath = dirname(__DIR__, 1); // Go up one level from the 'controllers' directory to 'app'
        require_once $rootPath . '../views/auth/login.php'; // Render Login View
    }

    public function showRegister() {
        $rootPath = dirname(__DIR__, 1); // Go up one level from the 'controllers' directory to 'app'
        // Fetch courses for the registration from CourseModel
        $courses = $this->courseModel->getAllCourses();
        $coursesError = ($courses === false);

        require_once $rootPath . '../views/auth/register.php'; // Render Register View
    }

    public function getAllCourses() {
        return $this->courseModel->getAllCourses();
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
