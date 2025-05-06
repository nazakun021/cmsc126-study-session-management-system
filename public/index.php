<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // For Error Checking

session_start(); // Initializes Session Management

// Include Configuration and Controllers
require_once '../app/config/db_connection.php';
require_once '../app/core/Router.php';
require_once '../app/controllers/AuthController.php';

// Define Routes
$router = new Router();
$router->addRoute('login', 'AuthController@showLogin');
$router->addRoute('register', 'AuthController@showRegister');
$router->addRoute('dashboard', 'AuthController@showDashboard');
$router->addRoute('logout', 'AuthController@logout');

// Handle form Submissions (POST requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'processLogin') {
        $router->dispatch('processLogin');
    } elseif (isset($_POST['action']) && $_POST['action'] === 'processRegister') {
        $router->dispatch('processRegister');
    }
} else {
    // Handle GET requests for displaying froms or other actions
    $action = $_GET['action'] ?? 'login';
    // Dispatch the Request
    $router->dispatch($action);
}

?>
