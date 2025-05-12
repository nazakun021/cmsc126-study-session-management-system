<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\CourseModel;

class AuthController extends Controller {
    private $userModel;
    private $courseModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->courseModel = new CourseModel();
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

        $username = trim($_POST['username'] ?? '');
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
            
            $this->redirect('/cmsc126-study-session-management-system/public/dashboard');
        } else {
            $_SESSION['error'] = $result['error'];
            $this->redirect('/cmsc126-study-session-management-system/public/login');
        }
    }

    public function register() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cmsc126-study-session-management-system/public/register');
        }

        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $courseId = trim($_POST['courseID']);

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
