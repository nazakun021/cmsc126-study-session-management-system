<?php
// For Error Checking
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/init.php';
requireLogin();

require_once __DIR__ . '/../config/db_connection.php';
$pdo = require __DIR__ . '/../config/db_connection.php';

require_once __DIR__ . '/../core/Model.php';

require_once __DIR__ . '/../Models/StudySession.php';
$studySessionModel = new \App\Models\StudySession($pdo);
$sessions = $studySessionModel->getAllSessions();

require_once __DIR__ . '/../Models/CourseModel.php';
$courseModel = new \App\Models\CourseModel($pdo);
$subjectsResult = $courseModel->getAllSubjects();
$subjects = $subjectsResult['success'] ? $subjectsResult['subjects'] : [];

$subjectMap = [];
foreach ($subjects as $subject) {
    $subjectMap[$subject['subjectID']] = $subject['subjectName'];
}

$currUserId = $_SESSION['userId'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $csrfToken; ?>">
    <title>Dashboard</title>
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
                    <li class="active">
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
                        <button class="sidebar-toggle" id="filter-toggle" style="width:100%;background:none;border:none;text-align:left;padding:0.75rem 1.5rem;color:#4f46e5;font-size:1rem;font-weight:500;cursor:pointer;display:flex;align-items:center;">
                            <i data-feather="filter" style="color:#4f46e5;width:18px;height:18px;margin-right:0.75rem;"></i>
                            <span style="color:#4f46e5;font-size:1rem;font-weight:500;">Filter Sessions</span>
                        </button>
                    </li>
                </ul>
                <div id="sidebar-filter-panel" style="display:none;padding:1rem 1.5rem 0 1.5rem;">
                    <form id="sidebar-filter-form">
                        <div class="form-group">
                            <label for="filter-subject">Subject</label>
                            <select id="filter-subject" name="subjectID">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['subjectID']); ?>"><?php echo htmlspecialchars($subject['subjectName']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-date">Date</label>
                            <input type="date" id="filter-date" name="reviewDate">
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;width:100%;">Apply Filter</button>
                        <button type="button" id="clear-filter" class="btn btn-secondary" style="margin-top:0.5rem;width:100%;">Clear</button>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $pageTitle = 'Dashboard';
            require_once __DIR__ . '/includes/header.php'; 
            ?>

            <!-- Dashboard Content -->
            <div class="content-container">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Total Sessions</h3>
                            <p class="stat-card-value" id="total-sessions"><?php echo count($sessions); ?></p>
                        </div>
                        <div class="stat-card-icon blue">
                            <i data-feather="calendar"></i>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-card-content">
                            <h3 class="stat-card-title">Upcoming Sessions</h3>
                            <p class="stat-card-value" id="upcoming-sessions"><?php
                                $upcomingSessions = array_filter($sessions, function($session) {
                                    $sessionDate = strtotime($session['reviewDate']);
                                    return $sessionDate >= strtotime('today');
                                });
                                echo count($upcomingSessions);
                            ?></p>
                        </div>
                        <div class="stat-card-icon orange" style="background:#ede9fe;color:#4f46e5;">
                            <i data-feather="clock"></i>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Reviews Section -->
                <section class="content-section">
                    <div class="section-header">
                        <h3>Upcoming Review Sessions</h3>
                        <div class="search-bar-container" style="display:none;">
                            <input type="text" id="session-search" class="search-bar" placeholder="Search sessions...">
                        </div>
                        <button id="add-session-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Session
                        </button>
                    </div>
                    <?php 
                    $now = strtotime('today');
                    $upcomingSessions = array_filter($sessions, function($session) use ($now) {
                        return strtotime($session['reviewDate']) >= $now;
                    });
                    ?>
                    <?php if (empty($upcomingSessions)): ?>
                        <div id="empty-add-btn" class="empty-state">
                            <div class="empty-state-icon">
                                <i data-feather="calendar"></i>
                            </div>
                            <h3>No review sessions</h3>
                            <p>Create your first review session to get started</p>
                            <button id="empty-add-btn" class="btn btn-primary">
                                <i data-feather="plus"></i> Add Session
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="card-grid" id="dashboard-session-list">
                            <?php foreach ($upcomingSessions as $session): ?>
                                <div class="card session-card">
                                    <div class="card-header">
                                        <h4 class="card-title"><?php echo htmlspecialchars($session['reviewTitle'] ?? ''); ?></h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-info">
                                            <div class="card-info-item">
                                                <i data-feather="book"></i>
                                                <span><?php $subjectID = $session['subjectID'] ?? null; echo isset($subjectMap[$subjectID]) ? htmlspecialchars($subjectMap[$subjectID]) : 'Unknown Subject'; ?></span>
                                            </div>
                                            <div class="card-info-item">
                                                <i data-feather="calendar"></i>
                                                <span><?php echo date('F j, Y', strtotime($session['reviewDate'])); ?></span>
                                            </div>
                                            <div class="card-info-item">
                                                <i data-feather="clock"></i>
                                                <span><?php echo date('g:i A', strtotime($session['reviewStartTime'])) . ' - ' . date('g:i A', strtotime($session['reviewEndTime'])); ?></span>
                                            </div>
                                            <div class="card-info-item">
                                                <i data-feather="map-pin"></i>
                                                <span><?php echo htmlspecialchars($session['reviewLocation'] ?? ''); ?></span>
                                            </div>
                                            <div class="card-info-item">
                                                <i data-feather="book-open"></i>
                                                <span><strong>Topic:</strong> <?php echo htmlspecialchars($session['reviewTopic'] ?? ''); ?></span>
                                            </div>
                                            <div class="card-info-item">
                                                <i data-feather="file-text"></i>
                                                <span><strong>Description:</strong> <?php echo htmlspecialchars($session['reviewDescription'] ?? ''); ?></span>
                                            </div>
                                        </div>
                                        <div class="session-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                            <a href="/cmsc126-study-session-management-system/app/views/review-sessions.php?id=<?php echo $session['reviewSessionID']; ?>" class="btn btn-icon" title="View Details">
                                                <i data-feather="eye"></i>
                                            </a>
                                            <!-- <button class="btn btn-icon edit-session" data-session-id="<?php echo $session['reviewSessionID']; ?>" title="Edit">
                                                <i data-feather="edit-2"></i>
                                            </button> -->
                                            <?php /* if ($session['creatorUserID'] == $currUserId): ?>
                                                <button class="btn btn-icon delete-session" data-session-id="<?php echo $session['reviewSessionID']; ?>" title="Delete">
                                                    <i data-feather="trash-2"></i>
                                                </button>
                                            <?php endif; */ ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
                <form action="/cmsc126-study-session-management-system/public/create-session.php" id="addSessionForm" method="POST">
                    <input type="hidden" name="action" value="create-session">
                    <div class="form-group">
                        <label for="sessionTitle">Title</label>
                        <input type="text" id="sessionTitle" name="reviewTitle" required placeholder="e.g., Midterm Review: Data Structures">
                    </div>
                    <div class="form-group">
                        <label for="sessionSubject">Subject</label>
                        <select id="sessionSubject" name="subjectID" required>
                            <option value="">Select a subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo htmlspecialchars($subject['subjectID']); ?>">
                                    <?php echo htmlspecialchars($subject['subjectName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sessionTopic">Topic</label>
                        <input type="text" id="sessionTopic" name="reviewTopic" required placeholder="e.g., Binary Trees and Graphs">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sessionDate">Date</label>
                            <input type="date" id="sessionDate" name="reviewDate" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="sessionStartTime">Start Time</label>
                            <input type="time" id="sessionStartTime" name="reviewStartTime" required>
                        </div>
                        <div class="form-group">
                            <label for="sessionEndTime">End Time</label>
                            <input type="time" id="sessionEndTime" name="reviewEndTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sessionLocation">Location</label>
                        <input type="text" id="sessionLocation" name="reviewLocation" required placeholder="e.g., Library Study Room 3">
                    </div>
                    <div class="form-group">
                        <label for="sessionDescription">Description</label>
                        <textarea id="sessionDescription" name="reviewDescription" rows="3" placeholder="Describe what will be covered in this session"></textarea>
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
                        <!-- <a href="#" class="card-menu-item edit-session">
                            <i data-feather="edit-2"></i> Edit
                        </a>
                        <a href="#" class="card-menu-item delete-session">
                            <i data-feather="trash-2"></i> Delete
                        </a> -->
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
                    <div class="card-info-item">
                        <i data-feather="book-open"></i>
                        <span class="card-topic"></span>
                    </div>
                    <div class="card-info-item">
                        <i data-feather="file-text"></i>
                        <span class="card-description"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Scripts -->
    <script src="/cmsc126-study-session-management-system/public/js/utils.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/script.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/dashboard.js"></script>
</body>
</html>
