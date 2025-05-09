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
    protected $pdo;
    
    public function __construct() {
        global $pdo; // Access PDO instance from db_connection.php 
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
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

        require_once $rootPath . '/views/auth/register.php'; // Render Register View
    }

    public function getAllCourses() {
        return $this->courseModel->getAllCourses();
    }

    // Handle Login Form Submission
    public function login() {
        session_start(); // Ensure session is started
        // Collect and Sanitize Inputs
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Please fill in both username and password.";
            header("Location: /cmsc126-study-session-management-system/public/login");
            exit;
        }

        // Use User model's login method
        $result = $this->userModel->login($username, $password);
        if ($result['success']) {
            // Login successful
            $user = $result['user'];
            session_regenerate_id(true);
            $_SESSION['userId'] = $user['userID'];
            $_SESSION['username'] = $user['userName'];
            $_SESSION['isLoggedIn'] = true;
            header("Location: /cmsc126-study-session-management-system/public/dashboard");
            exit;
        } else {
            // Login failed
            $_SESSION['error'] = $result['error'];
            header("Location: /cmsc126-study-session-management-system/public/login");
            exit;
        }
    }

    // Handle Registration Form Submission
    public function register() {
        session_start(); // Ensure session is started
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password']; 
            $confirmPassword = $_POST['confirmPassword'];
            $courseId = trim($_POST['courseID']);

            $result = $this->userModel->register($username, $email, $password, $confirmPassword, $courseId);
            if ($result['success']) {
                $_SESSION['success'] = "Account created successfully! Please log in.";
                header("Location: /cmsc126-study-session-management-system/public/login");
                exit;
            } else {
                $_SESSION['error'] = $result['error'];
                $courses = $this->courseModel->getAllCourses();
                $coursesError = ($courses === false);
                $rootPath = dirname(__DIR__, 1);
                require_once $rootPath . '/views/auth/register.php';
                exit;
            }
        } else {
            header("Location: /cmsc126-study-session-management-system/public/register");
            exit;
        }
    }

    // Show Dashboard (Protected Route)
    public function showDashboard() {
        session_start(); // Ensure session is started
        if (!isset($_SESSION['userId'])) {
            header("Location: /cmsc126-study-session-management-system/public/login");
            exit;
        }
        $rootPath = dirname(__DIR__, 1); // Go up one level from the 'controllers' directory to 'app'
        require_once $rootPath . '/views/dashboard.php';
    }

    // Logout User
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /login");
    }
}
?>
