<?php
// For Error Checking
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

// API endpoint for dashboard stats
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/public/api/dashboard/stats') !== false) {
    require_once __DIR__ . '/../app/config/db_connection.php';
    $pdo = require __DIR__ . '/../app/config/db_connection.php';
    require_once __DIR__ . '/../app/core/Model.php';
    require_once __DIR__ . '/../app/Models/StudySession.php';
    require_once __DIR__ . '/../app/Models/CourseModel.php';
    session_start();
    if (!isset($_SESSION['userId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    $userId = $_SESSION['userId'];
    $studySessionModel = new \App\Models\StudySession($pdo);
    $courseModel = new \App\Models\CourseModel($pdo);
    $sessions = $studySessionModel->getAllSessions();
    $subjects = $courseModel->getSubjectsByUserId($userId);
    $upcomingSessions = array_filter($sessions, function($session) {
        return strtotime($session['reviewDate']) >= strtotime('today');
    });
    $avgAttendance = 100; // Placeholder, replace with real calculation if available
    header('Content-Type: application/json');
    echo json_encode([
        'totalSessions' => count($sessions),
        'upcomingSessions' => count($upcomingSessions),
        'totalSubjects' => count($subjects),
        'avgAttendance' => $avgAttendance,
        'sessions' => $sessions
    ]);
    exit;
}

session_start();
require_once '../app/config/init.php'; // Ensure CSRF token and other initializations are done early

// Include Configuration and Controllers
require_once '../app/config/db_connection.php';
require_once '../app/core/Router.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/StudySessionController.php';
require_once '../app/controllers/AdminController.php'; // Added AdminController include
require_once '../app/Models/User.php';
require_once '../app/Models/CourseModel.php';
require_once '../app/Models/StudySession.php';

// Define Routes
$router = new Router();
$router->addRoute('login', 'AuthController@showLogin');
$router->addRoute('register', 'AuthController@showRegister');
$router->addRoute('dashboard', 'AuthController@showDashboard');
$router->addRoute('logout', 'AuthController@logout');
$router->addRoute('processLogin', 'AuthController@login');
$router->addRoute('processRegister', 'AuthController@register');

// Study Session Routes
$router->addRoute('create-session', 'StudySessionController@createSession');
$router->addRoute('update-session', 'StudySessionController@updateSession');
$router->addRoute('delete-session', 'StudySessionController@deleteSession');

// Admin Routes
$router->addRoute('admin', 'AdminController@index'); // Route for the admin dashboard
$router->addRoute('admin/', 'AdminController@index'); // Route for the admin dashboard with trailing slash
$router->addRoute('admin/dashboard', 'AdminController@index'); // Alias for admin dashboard
$router->addRoute('admin/deleteUser', 'AdminController@deleteUser');
$router->addRoute('admin/deleteStudySession', 'AdminController@deleteStudySession');

// Get the path after /public/
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = dirname($_SERVER['SCRIPT_NAME']); // usually /cmsc126-study-session-management-system/public
$action = trim(str_replace($base, '', $path), '/');
if ($action === '') $action = 'login';

// Revised dispatch logic:
$dispatch_target_action = $action; // Default to URL-based action

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && !empty($_POST['action'])) {
        // Allow $_POST['action'] to override for specific form submissions (e.g., login, register)
        $dispatch_target_action = $_POST['action'];
    }
    // Always dispatch for POST requests, using URL-based action if $_POST['action'] is not set/empty
    $router->dispatch($dispatch_target_action);
} else { // For GET requests
    // Use the URL-based action
    $router->dispatch($action);
}
?>
