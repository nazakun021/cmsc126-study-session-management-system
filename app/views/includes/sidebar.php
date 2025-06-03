<?php
// Determine the current page for highlighting active navigation
$currentPage = basename($_SERVER['REQUEST_URI'], '.php');
$currentPath = $_SERVER['REQUEST_URI'];
$isProfilePage = strpos($currentPath, 'profile.php') !== false;
$isDashboardPage = strpos($currentPath, 'dashboard.php') !== false;
$isReviewSessionsPage = strpos($currentPath, 'review-sessions.php') !== false;
$isSubjectsPage = strpos($currentPath, 'subjects.php') !== false;
$isAttendancePage = strpos($currentPath, 'attendance.php') !== false;
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <h1 class="app-title">ReviewApp</h1>
        <button id="menu-toggle" class="menu-toggle">
            <i data-feather="menu"></i>
        </button>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <li<?php echo $isDashboardPage ? ' class="active"' : ''; ?>>
                <a href="/cmsc126-study-session-management-system/app/views/dashboard.php">
                    <i data-feather="home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li<?php echo $isReviewSessionsPage ? ' class="active"' : ''; ?>>
                <a href="/cmsc126-study-session-management-system/app/views/review-sessions.php">
                    <i data-feather="calendar"></i>
                    <span>Review Sessions</span>
                </a>
            </li>
            <?php if ($isReviewSessionsPage): ?>
            <li>
                <button class="sidebar-toggle" id="filter-toggle" style="width:100%;background:none;border:none;text-align:left;padding:0.75rem 1.5rem;color:#4f46e5;font-size:1rem;font-weight:500;cursor:pointer;display:flex;align-items:center;">
                    <i data-feather="filter" style="color:#4f46e5;width:18px;height:18px;margin-right:0.75rem;"></i>
                    <span style="color:#4f46e5;font-size:1rem;font-weight:500;">Filter Sessions</span>
                </button>
            </li>
            <?php endif; ?>
            <!-- Subjects and Attendance links are commented out in most views -->
            <?php if ($isSubjectsPage): ?>
            <li class="active">
                <a href="/cmsc126-study-session-management-system/app/views/subjects.php">
                    <i data-feather="book"></i>
                    <span>Subjects</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if ($isAttendancePage): ?>
            <li class="active">
                <a href="/cmsc126-study-session-management-system/app/views/attendance.php">
                    <i data-feather="users"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <?php if ($isReviewSessionsPage && isset($subjects)): ?>
        <!-- Filter panel for review sessions -->
        <div id="sidebar-filter-panel" style="display:none;padding:1rem 1.5rem 0 1.5rem;">
            <form id="sidebar-filter-form" method="GET" action="review-sessions.php">
                <div class="form-group">
                    <label for="filter-subject">Subject</label>
                    <select id="filter-subject" name="subjectID">
                        <option value="">All Subjects</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?php echo htmlspecialchars($subject['subjectID']); ?>" <?php if (isset($filterSubject) && $filterSubject == $subject['subjectID']) echo 'selected'; ?>><?php echo htmlspecialchars($subject['subjectName']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter-date">Date</label>
                    <input type="date" id="filter-date" name="reviewDate" value="<?php echo htmlspecialchars($filterDate ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;width:100%;">Apply Filter</button>
                <a href="review-sessions.php" id="clear-filter" class="btn btn-secondary" style="margin-top:0.5rem;width:100%;text-align:center;">Clear</a>
            </form>
        </div>
        <?php endif; ?>
    </nav>
</aside>
