<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\CourseModel;

class AuthController extends Controller {
    private $userModel;
    private $courseModel;
    
    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
        $this->courseModel = new CourseModel($pdo);
    }

    public function showLogin() {
        $this->view('auth/login');
    }

    public function showRegister() {
        $courses = $this->courseModel->getAllCourses();
        $coursesError = ($courses === false);
        
        $this->view('auth/register', [
            'courses' => $courses,
            'coursesError' => $coursesError
        ]);
    }

    public function login() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = strip_tags(trim($_POST['username'] ?? ''));
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Please fill in both username and password.";
            $this->redirect('/cmsc126-study-session-management-system/public/login');
        }

        $result = $this->userModel->login($username, $password);
        
        if ($result['success']) {
            $user = $result['user'];
            session_regenerate_id(true);
            $_SESSION['userId'] = $user['userID'];
            $_SESSION['username'] = $user['userName'];
            $_SESSION['isLoggedIn'] = true;
            $_SESSION['user'] = $user;
            
            // Check user role for redirection
            if (isset($user['role']) && $user['role'] === 'admin') {
                $this->redirect('/cmsc126-study-session-management-system/public/admin'); // Corrected redirection path
            } else {
                $this->redirect('/cmsc126-study-session-management-system/public/dashboard');
            }
        } else {
            $_SESSION['error'] = $result['error'];
            $this->redirect('/cmsc126-study-session-management-system/public/login');
        }
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear previous session messages
        unset($_SESSION['error']);
        unset($_SESSION['success']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cmsc126-study-session-management-system/public/register');
        }

        $username = strip_tags(trim($_POST['username']));
        $email = strip_tags(trim($_POST['email']));
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $courseId = filter_var(trim($_POST['courseID']), FILTER_SANITIZE_NUMBER_INT);

        $result = $this->userModel->register($username, $email, $password, $confirmPassword, $courseId);
        
        if ($result['success']) {
            $_SESSION['success'] = "Account created successfully! Please log in.";
            $this->redirect('/cmsc126-study-session-management-system/public/login');
        } else {
            $_SESSION['error'] = $result['error'];
            $courses = $this->courseModel->getAllCourses();
            $coursesError = ($courses === false);
            
            $this->view('auth/register', [
                'courses' => $courses,
                'coursesError' => $coursesError
            ]);
        }
    }

    public function showDashboard() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['userId']) || !isset($_SESSION['username'])) {
            $this->redirect('/cmsc126-study-session-management-system/public/login');
        }

        $this->view('dashboard');
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        $this->redirect('/cmsc126-study-session-management-system/public/login');
    }
}
?>
