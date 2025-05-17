<?php
class Router {
    protected $routes = [];

    public function addRoute($action, $controllerMethod) {
        $this->routes[$action] = $controllerMethod;
    }

    public function dispatch($action) {
        // Add a default route for the root path if not already set
        if (empty($action) && !isset($this->routes[''])) {
            $action = 'login'; // Or your default landing page action
        }
        
        // Admin routes
        if (strpos($action, 'admin') === 0) {
            // Basic route protection: check if user is admin
            // More robust checking should be done within the controller methods themselves
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
                // If trying to access admin area without being an admin, redirect to login
                // Allow access to login/register pages even if not admin
                if ($action !== 'login' && $action !== 'register' && $action !== 'auth/login' && $action !== 'auth/register') {
                    // Check if the current action is already a redirection target to prevent loop
                    if ($action !== 'login') { // Basic check to prevent immediate redirect loop
                         header('Location: /login');
                         exit;
                    }
                }
            }
        }

        if (isset($this->routes[$action])) {
            list($controller, $method) = explode('@', $this->routes[$action]);
            $controller = "App\\Controllers\\$controller";
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Page not found!";
            exit;
        }
    }
}
?>
