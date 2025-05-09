<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // For Error Checking

session_start(); // Initializes Session Management

// Include Configuration and Controllers
require_once '../app/config/db_connection.php';
require_once '../app/core/Router.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/Models/User.php';
require_once '../app/Models/CourseModel.php';

// Define Routes
$router = new Router();
$router->addRoute('login', 'AuthController@showLogin');
$router->addRoute('register', 'AuthController@showRegister');
$router->addRoute('dashboard', 'AuthController@showDashboard');
$router->addRoute('logout', 'AuthController@logout');
$router->addRoute('processLogin', 'AuthController@login');
$router->addRoute('processRegister', 'AuthController@register');

// Get the path after /public/
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = dirname($_SERVER['SCRIPT_NAME']); // usually /cmsc126-study-session-management-system/public
$action = trim(str_replace($base, '', $path), '/');
if ($action === '') $action = 'login';

// Handle form Submissions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $router->dispatch($_POST['action']);
    }
} else {
    // Handle GET requests for displaying forms or other actions
    // Dispatch the Request    
    $router->dispatch($action);
}

?>
