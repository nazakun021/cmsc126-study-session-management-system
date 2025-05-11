<?php
require_once __DIR__ . '/../../config/init.php';
requireLogin();

$user = getCurrentUser();
?>
<header class="header">
    <div class="header-left">
        <h2><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
    </div>
    <div class="header-right">
        <div class="dropdown">
            <button class="dropdown-toggle">
                <div class="user-avatar">UP</div>
                <span class="user-name"><?php echo htmlspecialchars($user['username']); ?></span>
                <i data-feather="chevron-down"></i>
            </button>
            <div class="dropdown-menu">
                <a href="/cmsc126-study-session-management-system/app/views/profile.php" class="dropdown-item">Profile</a>
                <a href="/cmsc126-study-session-management-system/public/logout" class="dropdown-item">Logout</a>
            </div>
        </div>
    </div>
</header> 