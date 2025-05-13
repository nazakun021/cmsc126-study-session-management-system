<?php
class Router {
    protected $routes = [];

    public function addRoute($action, $controllerMethod) {
        $this->routes[$action] = $controllerMethod;
    }

    public function dispatch($action) {
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
