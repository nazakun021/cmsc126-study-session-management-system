<?php
require_once __DIR__ . '/../../config/init.php';
requireLogin();
require_once __DIR__ . '/../../config/db_connection.php';
// Load models
require_once __DIR__ . '/../../core/Model.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/CourseModel.php';
global $pdo;

// Get user info
$userModel = new \App\Models\User($pdo);
$courseModel = new \App\Models\CourseModel($pdo);

$currentUser = getCurrentUser();
$user = $userModel->getUserById($currentUser['userId']);
$courseName = '';
if ($user && !empty($user['courseID'])) {
    $courseResult = $courseModel->getCourseById($user['courseID']);
    if ($courseResult && isset($courseResult['success']) && $courseResult['success']) {
        $courseName = $courseResult['course']['courseName'];
    }
}
?>
<header class="header">
    <div class="header-left">
        <h2><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
    </div>
    <div class="header-right">
        <div class="dropdown">
            <button class="dropdown-toggle">
                <div class="user-avatar"><?php echo strtoupper(substr(htmlspecialchars($user['userName'] ?? ''), 0, 2)); ?></div>
                <span class="user-name"><?php echo htmlspecialchars($user['userName'] ?? ''); ?></span>
                <i data-feather="chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="/cmsc126-study-session-management-system/app/views/profile.php" class="dropdown-item">Profile</a>
                <a href="/cmsc126-study-session-management-system/app/views/logout.php" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>
</header> 