<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects | Review Dashboard</title>
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
                    <li class="active">
                        <a href="/cmsc126-study-session-management-system/app/views/subjects.php">
                            <i data-feather="book"></i>
                            <span>Subjects</span>
                        </a>
                    </li>
                    <li>
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
            $pageTitle = 'Subjects';
            require_once __DIR__ . '/includes/header.php'; 
            ?>

            <!-- Content -->
            <div class="content-container">
                <div class="content-header">
                    <div class="content-header-left">
                        <h3>All Subjects</h3>
                    </div>
                    <div class="content-header-right">
                        <button id="add-subject-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Subject
                        </button>
                    </div>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="empty-state">
                    <div class="empty-state-icon">
                        <i data-feather="book"></i>
                    </div>
                    <h3>No subjects found</h3>
                    <p>Create your first subject to get started</p>
                    <button id="empty-add-btn" class="btn btn-primary">
                        <i data-feather="plus"></i> Add Subject
                    </button>
                </div>

                <!-- Subjects Container (hidden initially) -->
                <div id="subjects-container" style="display: none;">
                    <div class="subjects-grid" id="subjects-grid">
                        <!-- Subjects will be added here dynamically -->
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Subject Modal -->
    <div id="add-subject-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Subject</h3>
                <button id="close-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-subject-form">
                    <div class="form-group">
                        <label for="subject-name">Subject Name</label>
                        <input type="text" id="subject-name" required placeholder="e.g., Computer Science">
                    </div>
                    <div class="form-group">
                        <label for="subject-code">Subject Code</label>
                        <input type="text" id="subject-code" required placeholder="e.g., CS101">
                    </div>
                    <div class="form-group">
                        <label for="subject-description">Description</label>
                        <textarea id="subject-description" rows="3" placeholder="Brief description of the subject"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="subject-color">Color</label>
                        <input type="color" id="subject-color" value="#4f46e5">
                    </div>
                    <div class="form-actions">
                        <button type="button" id="cancel-add" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Subject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Subject</h3>
                <button id="close-delete-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this subject? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" id="cancel-delete" class="btn btn-secondary">Cancel</button>
                    <button type="button" id="confirm-delete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Template -->
    <template id="subject-template">
        <div class="subject-card">
            <div class="subject-card-header">
                <div class="subject-color"></div>
                <div class="subject-actions">
                    <button class="btn btn-icon edit-subject" title="Edit Subject">
                        <i data-feather="edit-2"></i>
                    </button>
                    <button class="btn btn-icon delete-subject" title="Delete Subject">
                        <i data-feather="trash-2"></i>
                    </button>
                </div>
            </div>
            <div class="subject-card-body">
                <h4 class="subject-name"></h4>
                <div class="subject-code"></div>
                <p class="subject-description"></p>
            </div>
            <div class="subject-card-footer">
                <div class="subject-stats">
                    <div class="subject-stat">
                        <i data-feather="calendar"></i>
                        <span class="session-count">0 sessions</span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script src="/cmsc126-study-session-management-system/public/js/script.js"></script>
</body>
</html>