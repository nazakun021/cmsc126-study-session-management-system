<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance | Review Dashboard</title>
    <link rel="stylesheet" href="/cmsc126-study-session-management-system/public/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="app-title">ReviewApp</h1>
                <button id="menu-toggle" class="menu-toggle">
                    <i data-feather="menu"></i>
                </button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/cmsc126-study-session-management-system/app/views/dashboard.php">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cmsc126-study-session-management-system/app/views/review-sessions.php">
                            <i data-feather="calendar"></i>
                            <span>Review Sessions</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cmsc126-study-session-management-system/app/views/subjects.php">
                            <i data-feather="book"></i>
                            <span>Subjects</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="/cmsc126-study-session-management-system/app/views/attendance.php">
                            <i data-feather="users"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $pageTitle = 'Attendance';
            require_once __DIR__ . '/includes/header.php'; 
            ?>

            <!-- Content -->
            <div class="content-container">
                <div class="content-header">
                    <div class="content-header-left">
                        <h3>Attendance Records</h3>
                    </div>
                    <div class="content-header-right">
                        <button id="add-record-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Record
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="empty-state">
                    <div class="empty-state-icon">
                        <i data-feather="users"></i>
                    </div>
                    <h3>No attendance records found</h3>
                    <p>Create your first attendance record to get started</p>
                    <button id="empty-add-btn" class="btn btn-primary">
                        <i data-feather="plus"></i> Add Record
                    </button>
                </div>

                <!-- Attendance Container (hidden initially) -->
                <div id="attendance-container" style="display: none;">
                    <div class="attendance-list" id="attendance-list">
                        <!-- Attendance records will be added here dynamically -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Attendance Record Modal -->
    <div id="add-record-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add Attendance Record</h3>
                <button id="close-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-record-form">
                    <div class="form-group">
                        <label for="record-session">Review Session</label>
                        <input type="text" id="record-session" required placeholder="e.g., Midterm Review: Data Structures">
                    </div>
                    <div class="form-group">
                        <label for="record-date">Date</label>
                        <input type="date" id="record-date" required>
                    </div>
                    <div class="form-group">
                        <label for="record-attendees">Number of Attendees</label>
                        <input type="number" id="record-attendees" required min="1" placeholder="e.g., 15">
                    </div>
                    <div class="form-group">
                        <label for="record-notes">Notes</label>
                        <textarea id="record-notes" rows="3" placeholder="Any additional notes about this session"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" id="cancel-add" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Record</h3>
                <button id="close-delete-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this attendance record? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" id="cancel-delete" class="btn btn-secondary">Cancel</button>
                    <button type="button" id="confirm-delete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Record Template -->
    <template id="record-template">
        <div class="attendance-item">
            <div class="attendance-content">
                <h4 class="record-session"></h4>
                <div class="record-details">
                    <div class="record-detail">
                        <i data-feather="calendar"></i>
                        <span class="record-date"></span>
                    </div>
                    <div class="record-detail">
                        <i data-feather="users"></i>
                        <span class="record-attendees"></span>
                    </div>
                </div>
                <p class="record-notes"></p>
            </div>
            <div class="attendance-actions">
                <button class="btn btn-icon view-record" title="View Details">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-icon edit-record" title="Edit Record">
                    <i data-feather="edit-2"></i>
                </button>
                <button class="btn btn-icon delete-record" title="Delete Record">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
    </template>

    <script src="/cmsc126-study-session-management-system/public/js/script.js"></script>
</body>
</html>