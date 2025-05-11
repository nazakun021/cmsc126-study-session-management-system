<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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