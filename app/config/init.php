<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate and store CSRF token if one doesn't exist or on certain conditions
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['userId']) && isset($_SESSION['username']);
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /cmsc126-study-session-management-system/public/login");
        exit;
    }
}

// Get current user data
function getCurrentUser() {
    return [
        'userId' => $_SESSION['userId'] ?? null,
        'username' => $_SESSION['username'] ?? null
    ];
}