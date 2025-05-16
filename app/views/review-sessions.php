<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
require_once __DIR__ . '/../config/db_connection.php';
$pdo = require __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../Models/StudySession.php';
require_once __DIR__ . '/../Models/CourseModel.php';
$studySessionModel = new \App\Models\StudySession($pdo);
$courseModel = new \App\Models\CourseModel($pdo);

// Filter logic
$filterSubject = $_GET['subjectID'] ?? '';
$filterDate = $_GET['reviewDate'] ?? '';

if ($filterSubject || $filterDate) {
    $sessions = $studySessionModel->getFilteredSessions($filterSubject, $filterDate);
} else {
    $sessions = $studySessionModel->getAllSessions();
}

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
    <title>Review Sessions | Review Dashboard</title>
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
                    <li class="active">
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
                    <form id="sidebar-filter-form" method="GET" action="review-sessions.php">
                        <div class="form-group">
                            <label for="filter-subject">Subject</label>
                            <select id="filter-subject" name="subjectID">
                                <option value="">All Subjects</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo htmlspecialchars($subject['subjectID']); ?>" <?php if ($filterSubject == $subject['subjectID']) echo 'selected'; ?>><?php echo htmlspecialchars($subject['subjectName']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-date">Date</label>
                            <input type="date" id="filter-date" name="reviewDate" value="<?php echo htmlspecialchars($filterDate); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;width:100%;">Apply Filter</button>
                        <a href="review-sessions.php" id="clear-filter" class="btn btn-secondary" style="margin-top:0.5rem;width:100%;text-align:center;">Clear</a>
                    </form>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $pageTitle = 'Review Sessions';
            require_once __DIR__ . '/includes/header.php'; 
            ?>

            <!-- Content -->
            <div class="content-container">
                <div class="content-header">
                    <div class="content-header-left">
                        <h3>All Review Sessions</h3>
                    </div>
                    <div class="content-header-right">
                        <div class="search-bar-container" style="display:none;">
                            <input type="text" id="session-search" class="search-bar" placeholder="Search sessions...">
                        </div>
                        <button id="add-session-btn" class="btn btn-primary">
                            <i data-feather="plus"></i> Add Session
                        </button>
                    </div>
                </div>

                <div id="sessions-container">
                    <div class="sessions-list" id="sessions-list">
                        <?php if (empty($sessions)): ?>
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <i data-feather="calendar"></i>
                                </div>
                                <h3>No review sessions found</h3>
                                <p>Create your first review session to get started</p>
                                <button id="empty-add-btn" class="btn btn-primary">
                                    <i data-feather="plus"></i> Add Session
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($sessions as $session): ?>
                                <div class="session-item">
                                    <div class="session-content">
                                        <h4 class="session-title"><?php echo htmlspecialchars($session['reviewTitle'] ?? ''); ?></h4>
                                        <div class="session-details">
                                            <div class="session-detail">
                                                <i data-feather="book"></i>
                                                <span class="session-subject"><?php echo htmlspecialchars($subjectMap[$session['subjectID']] ?? 'Unknown Subject'); ?></span>
                                            </div>
                                            <div class="session-detail">
                                                <i data-feather="calendar"></i>
                                                <span class="session-date"><?php echo htmlspecialchars(date('F j, Y', strtotime($session['reviewDate']))); ?></span>
                                            </div>
                                            <div class="session-detail">
                                                <i data-feather="clock"></i>
                                                <span class="session-time"><?php echo htmlspecialchars(date('g:i A', strtotime($session['reviewStartTime']))) . ' - ' . htmlspecialchars(date('g:i A', strtotime($session['reviewEndTime']))); ?></span>
                                            </div>
                                            <div class="session-detail">
                                                <i data-feather="map-pin"></i>
                                                <span class="session-location"><?php echo htmlspecialchars($session['reviewLocation'] ?? ''); ?></span>
                                            </div>
                                            <div class="session-detail">
                                                <i data-feather="align-left"></i>
                                                <span class="session-topic"><strong>Topic:</strong> <?php echo htmlspecialchars($session['reviewTopic'] ?? ''); ?></span>
                                            </div>
                                            <div class="session-detail">
                                                <i data-feather="file-text"></i>
                                                <span class="session-description"><strong>Description:</strong> <?php echo htmlspecialchars($session['reviewDescription'] ?? ''); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="session-actions">
                                        <button class="btn btn-icon edit-session" data-session-id="<?php echo $session['reviewSessionID']; ?>" title="Edit">
                                            <i data-feather="edit-2"></i>
                                        </button>
                                        <?php if ($session['creatorUserID'] == $currUserId): ?>
                                            <button class="btn btn-icon delete-session" data-session-id="<?php echo $session['reviewSessionID']; ?>" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
                <form action="/cmsc126-study-session-management-system/public/create-session" id="add-session-form" method="POST">
                    <input type="hidden" name="action" value="create-session">
                    <div class="form-group">
                        <label for="session-title">Title</label>
                        <input type="text" id="session-title" name="reviewTitle" required placeholder="e.g., Midterm Review: Data Structures">
                    </div>
                    <div class="form-group">
                        <label for="session-subject">Subject</label>
                        <select id="session-subject" name="subjectID" required>
                            <option value="">Select a subject</option>
                            <?php 
                            // $courseModel is instantiated at the top of the file
                            // $subjects is also available from the top of the file
                            foreach ($subjects as $subjectModal): ?>
                                <option value="<?php echo htmlspecialchars($subjectModal['subjectID']); ?>">
                                    <?php echo htmlspecialchars($subjectModal['subjectName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="session-topic">Topic</label>
                        <input type="text" id="session-topic" name="reviewTopic" required placeholder="e.g., Binary Trees and Graphs">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="session-date">Date</label>
                            <input type="date" id="session-date" name="reviewDate" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="session-start-time">Start Time</label>
                            <input type="time" id="session-start-time" name="reviewStartTime" required>
                        </div>
                        <div class="form-group">
                            <label for="session-end-time">End Time</label>
                            <input type="time" id="session-end-time" name="reviewEndTime" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="session-location">Location</label>
                        <input type="text" id="session-location" name="reviewLocation" required placeholder="e.g., Library Study Room 3">
                    </div>
                    <div class="form-group">
                        <label for="session-description">Description</label>
                        <textarea id="session-description" name="reviewDescription" rows="3" placeholder="Describe what will be covered in this session"></textarea>
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

    <!-- Session Template -->
    <template id="session-template">
        <div class="session-item">
            <div class="session-content">
                <h4 class="session-title"></h4>
                <div class="session-details">
                    <div class="session-detail">
                        <i data-feather="book"></i>
                        <span class="session-subject"></span>
                    </div>
                    <div class="session-detail">
                        <i data-feather="calendar"></i>
                        <span class="session-date"></span>
                    </div>
                    <div class="session-detail">
                        <i data-feather="clock"></i>
                        <span class="session-time"></span>
                    </div>
                    <div class="session-detail">
                        <i data-feather="map-pin"></i>
                        <span class="session-location"></span>
                    </div>
                </div>
            </div>
            <div class="session-actions">
                <button class="btn btn-icon edit-session" title="Edit Session">
                    <i data-feather="edit-2"></i>
                </button>
                <button class="btn btn-icon delete-session" title="Delete Session">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
    </template>

    <script src="/cmsc126-study-session-management-system/public/js/utils.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/review-sessions.js"></script>
    <script src="/cmsc126-study-session-management-system/public/js/dropdown.js"></script>
</body>
</html>