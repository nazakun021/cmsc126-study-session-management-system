<?php
namespace App\Core;

class Controller {
    protected function view($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Build the view path
        $viewPath = dirname(__DIR__, 1) . '/views/' . $view . '.php';
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new \Exception("View {$view} not found");
        }
    }

    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
} 