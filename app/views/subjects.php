<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Dashboard</title>
    <link rel="stylesheet" href="/cmsc126-study-session-management-system/public/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>    <div class="app-container">
        <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $pageTitle = 'Dashboard';
            require_once __DIR__ . '/includes/header.php'; 
            ?>

            <!-- Content -->
            <div class="content-container">
                <div class="content-header">
                    <div class="content-header-left">
                        <h3>Welcome to the Review Dashboard</h3>
                    </div>
                </div>

                <!-- Dashboard Widgets -->
                <div class="dashboard-widgets">
                    <!-- Add your dashboard widgets here -->
                </div>
            </div>
        </main>
    </div>

    <script src="/cmsc126-study-session-management-system/public/js/utils.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/dropdown.js"></script>
</body>
</html>