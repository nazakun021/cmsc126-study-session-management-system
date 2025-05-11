<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
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
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <h2>My Profile</h2>
            </div>
            <div class="header-right">
                <div class="dropdown">
                    <button class="dropdown-toggle">
                        <div class="user-avatar">UP</div>
                        <span class="user-name">BLANK</span>
                        <i data-feather="chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="/cmsc126-study-session-management-system/app/views/profile.php" class="dropdown-item">Profile</a>
                        <a href="/cmsc126-study-session-management-system/app/views/logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Profile Content -->
        <div class="content-container">
            <div class="profile-card">
                <div class="profile-item">
                    <div class="label">Username</div>
                    <div class="value">adrianepena</div>
                </div>

                <div class="profile-item">
                    <div class="label">Email Address</div>
                    <div class="value">adriane.pena@example.com</div>
                </div>

                <div class="profile-item">
                    <div class="label">Course</div>
                    <div class="value">BS Computer Science</div>
                </div>
            </div>
        </div>
    </main>
  </div>

  <script src="/cmsc126-study-session-management-system/public/js/script.js"></script>
</body>
</html>
