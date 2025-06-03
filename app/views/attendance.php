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
                        <h3>Welcome to ReviewApp</h3>
                    </div>
                </div>

                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-icon">
                            <i data-feather="book-open"></i>
                        </div>
                        <div class="card-content">
                            <h4>Total Review Sessions</h4>
                            <p id="total-sessions">0</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon">
                            <i data-feather="users"></i>
                        </div>
                        <div class="card-content">
                            <h4>Total Attendees</h4>
                            <p id="total-attendees">0</p>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-icon">
                            <i data-feather="calendar"></i>
                        </div>
                        <div class="card-content">
                            <h4>Upcoming Sessions</h4>
                            <p id="upcoming-sessions">0</p>
                        </div>
                    </div>
                </div>

                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    <div id="activity-list">
                        <!-- Recent activity items will be populated here -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="/cmsc126-study-session-management-system/public/js/utils.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/dashboard.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/dropdown.js"></script>
</body>
</html>