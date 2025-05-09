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
        $this->userModel = new User();
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
        require_once __DIR__ . '/../config/db_connection.php';

        // Basic Input Validation
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header("Location: login.php");
            exit;
        }

        // Collect and Sanitize Inputs
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // More Validation

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Please fill in both username and password.";
            header("Location: login.php");
            exit;
        }

        // Check Credentials Against Database
        $stmt = null;
        try {
            $sql = "SELECT userID, password FROM User WHERE username = :username";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':username', $username);

            // Execute the Query
            $stmt->execute();

            // Fetch the user data (if found)
            $user = $stmt->fetch();

            // Verify User and Password
            if ($user) {
                // User found, now verify the password
                if (password_verify($password, $user['password'])) {
                    // Password Correct, Login Sucessful.

                    // Regenerate Session ID for Security
                    session_regenerate_id(true);

                    // Store user information in the Session
                    $_SESSION['userId'] = $user['userID'];
                    $_SESSION['username'] = $username;
                    $_SESSION['isLoggedIn'] = true; // Flag for access control

                    // REDIRECT ALL USERS TO DASHBOARD
                    header("Location: /dashboard");
                    exit;
                } else {
                    $_SESSION['error'] = "Invalid username or password.";
                    header("Location: login.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Invalid username or password.";
                header("Location: login.php");
                exit;
            }
        } catch (PDOException $e) {
            // Handle potential database errors during login
            error_log("Login PDOException: " . $e->getMessage());
            $_SESSION['error'] = "Login failed due to a system error. Please try again leter.";
            header("Location: login.php");
            exit;
        } finally {
            // Close cursor if statement was prepared
            if ($stmt) {
                $stmt->closeCursor();
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
                $courses = $this->courseModel->getAllCourses();
                $coursesError = ($courses === false);
                require_once '../views/auth/register.php';
                exit;
            }
        } else {
            header("Location: /register");
            exit;
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
