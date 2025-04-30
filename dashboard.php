<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="./css/styles.css">
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
                    <li class="active">
                        <a href="index.html">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="review-sessions.html">
                            <i data-feather="calendar"></i>
                            <span>Review Sessions</span>
                        </a>
                    </li>
                    <li>
                        <a href="subjects.html">
                            <i data-feather="book"></i>
                            <span>Subjects</span>
                        </a>
                    </li>
                    <li>
                        <a href="attendance.html">
                            <i data-feather="users"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="header-left">
                    <h2>Dashboard</h2>
                </div>
                <div class="header-right">
                    <div class="dropdown">
                        <button class="dropdown-toggle">
                            <div class="user-avatar">UP</div>
                            <span class="user-name">BLANK</span>
                            <i data-feather="chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item">Profile</a>
                            <a href="#" class="dropdown-item">Logout</a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-container">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Total Sessions</h3>
                            <p class="stat-card-value" id="total-sessions">0</p>
                        </div>
                        <div class="stat-card-icon blue">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Total Subjects</h3>
                            <p class="stat-card-value" id="total-subjects">0</p>
                        </div>
                        <div class="stat-card-icon green">
                            <i data-feather="book"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Upcoming Sessions</h3>
                            <p class="stat-card-value" id="upcoming-sessions">0</p>
                        </div>
                        <div class="stat-card-icon orange">
                            <i data-feather="clock"></i>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Avg. Attendance</h3>
                            <p class="stat-card-value" id="avg-attendance">0%</p>
                        </div>
                        <div class="stat-card-icon purple">
                            <i data-feather="users"></i>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Reviews Section -->
                <section class="content-section">
                    <div class="section-header">
                        <h3>Upcoming Review Sessions</h3>
                        <button id="add-session-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Session
                        </button>
                    </div>
                    
                    <!-- Empty State -->
                    <div id="empty-state" class="empty-state">
                        <div class="empty-state-icon">
                            <i data-feather="calendar"></i>
                        </div>
                        <h3>No upcoming review sessions</h3>
                        <p>Create your first review session to get started</p>
                        <button id="empty-add-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Session
                        </button>
                    </div>
                    
                    <!-- Sessions Container (hidden initially) -->
                    <div id="sessions-container" class="card-grid" style="display: none;">
                        <!-- Sessions will be added here dynamically -->
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Add Session Modal -->
    <div id="add-session-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Review Session</h3>
                <button id="close-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form action="processDashboard.php" id="addSessionForm" method="POST">
                    <div class="form-group">
                        <label for="sessionTitle">Title</label>
                        <input type="text" id="sessionTitle" required placeholder="e.g., Midterm Review: Data Structures">
                    </div>
                    <div class="form-group">
                        <label for="sessionSubject">Subject</label>
                        <input type="text" id="sessionSubject" required placeholder="e.g., Computer Science">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sessionDate">Date</label>
                            <input type="date" id="sessionDate" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sessionStartTime">Start Time</label>
                            <input type="time" id="sessionStartTime" required>
                        </div>
                        <div class="form-group">
                            <label for="sessionEndTime">End Time</label>
                            <input type="time" id="sessionEndTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sessionLocation">Location</label>
                        <input type="text" id="sessionLocation" required placeholder="e.g., Library Study Room 3">
                    </div>
                    <div class="form-actions">
                        <button type="button" id="cancel-add" class="btn btn-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Session</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Delete Session</h3>
                <button id="close-delete-modal" class="close-btn">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this session? This action cannot be undone.</p>
                <div class="form-actions">
                    <button type="button" id="cancel-delete" class="btn btn-secondary">Cancel</button>
                    <button type="button" id="confirm-delete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Card Template -->
    <template id="session-card-template">
        <div class="card session-card">
            <div class="card-header">
                <h4 class="card-title"></h4>
                <div class="card-menu">
                    <button class="card-menu-btn">
                        <i data-feather="more-vertical"></i>
                    </button>
                    <div class="card-menu-dropdown">
                        <a href="#" class="card-menu-item edit-session">
                            <i data-feather="edit-2"></i> Edit
                        </a>
                        <a href="#" class="card-menu-item delete-session">
                            <i data-feather="trash-2"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="card-info">
                    <div class="card-info-item">
                        <i data-feather="book"></i>
                        <span class="card-subject"></span>
                    </div>
                    <div class="card-info-item">
                        <i data-feather="calendar"></i>
                        <span class="card-date"></span>
                    </div>
                    <div class="card-info-item">
                        <i data-feather="clock"></i>
                        <span class="card-time"></span>
                    </div>
                    <div class="card-info-item">
                        <i data-feather="map-pin"></i>
                        <span class="card-location"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script src="script.js"></script>
</body>
</html>