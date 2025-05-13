<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();

// Destroy session and redirect to login
session_unset();
session_destroy();
header('Location: /cmsc126-study-session-management-system/app/views/auth/login.php');
exit();
?>