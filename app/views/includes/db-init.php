<?php
// Shared database initialization include
// Consolidates duplicate require_once and model instantiation patterns

if (!defined('DB_INIT_LOADED')) {
    define('DB_INIT_LOADED', true);
    
    // Database connection
    require_once __DIR__ . '/../../config/db_connection.php';
    global $pdo;
    $pdo = require __DIR__ . '/../../config/db_connection.php';
    
    // Core files
    require_once __DIR__ . '/../../core/Model.php';
    
    // Models
    require_once __DIR__ . '/../../Models/StudySession.php';
    require_once __DIR__ . '/../../Models/CourseModel.php';
    require_once __DIR__ . '/../../Models/User.php';
    
    // Model instances
    $studySessionModel = new \App\Models\StudySession($pdo);
    $courseModel = new \App\Models\CourseModel($pdo);
    $userModel = new \App\Models\User($pdo);
}
?>
