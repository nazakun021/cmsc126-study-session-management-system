<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/cmsc126-study-session-management-system/public/css/styles.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* Admin-specific styles can be added here if needed, or integrated into styles.css */
        .user-card, .session-card-admin {
            background-color: #fff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .user-card h4, .session-card-admin h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #4f46e5;
            margin-bottom: 0.75rem;
        }
        .user-info p, .session-info p {
            margin-bottom: 0.5rem;
            color: #64748b;
            font-size: 0.9rem;
        }
        .user-info p strong, .session-info p strong {
            color: #334155;
        }
        .actions .btn-danger { /* Ensure btn-danger is styled if not already in main styles.css */
            background-color: #ef4444;
            color: white;
        }
        .actions .btn-danger:hover {
            background-color: #dc2626;
        }
        .card-grid-admin {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .table-responsive {
            overflow-x: auto;
        }
        /* Styles for table view, if preferred over cards for density */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 0.5rem;
            overflow: hidden; /* Ensures border-radius is respected by table */
        }
        .admin-table th, .admin-table td {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.9rem;
        }
        .admin-table th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #334155;
        }
        .admin-table td {
            color: #475569;
        }
        .admin-table .actions button {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    <?php
    // Ensure CSRF token is set, if not, generate one.
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];
    ?>

    <div class="app-container"> <!-- Changed class from container to app-container -->
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h1 class="app-title">ReviewApp</h1>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <!-- User Dashboard link removed -->
                    <li class="active"> 
                        <a href="/cmsc126-study-session-management-system/public/admin">
                            <i data-feather="settings"></i>
                            <span>Admin Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/cmsc126-study-session-management-system/public/logout">
                            <i data-feather="log-out"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <?php 
            $pageTitle = 'ReviewApp Admin Dashboard'; // Changed page title
            // Removed redundant include of header.php from here
            // include __DIR__ . '/includes/header.php'; 
            ?>

            <div class="content-container">
                <div class="content-header">
                    <h3>Manage Users</h3>
                </div>
                <?php if (!empty($data['users'])): ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Course Name</th> <!-- Changed from Course ID -->
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['users'] as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['userID']) ?></td>
                                        <td><?= htmlspecialchars($user['userName']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td><?= htmlspecialchars($user['courseName'] ?? 'N/A') ?></td> <!-- Display courseName -->
                                        <td class="actions">
                                            <button class="btn btn-danger delete-user-btn" data-userid="<?= htmlspecialchars($user['userID']) ?>">
                                                <i data-feather="trash-2"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i data-feather="users" class="empty-state-icon"></i>
                        <h3>No Users Found</h3>
                        <p>There are no users to display at the moment (excluding admin accounts).</p>
                    </div>
                <?php endif; ?>

                <div class="content-header" style="margin-top: 2rem;">
                    <h3>Manage Study Sessions</h3>
                </div>
                <?php if (!empty($data['studySessions'])): ?>
                     <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Creator</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['studySessions'] as $session): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($session['reviewSessionID']) ?></td>
                                        <td><?= htmlspecialchars($session['reviewTitle']) ?></td>
                                        <td><?= htmlspecialchars($session['creatorName'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($session['subjectName'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars(date('M d, Y', strtotime($session['reviewDate']))) ?></td>
                                        <td><?= htmlspecialchars(date('h:i A', strtotime($session['reviewStartTime']))) ?> - <?= htmlspecialchars(date('h:i A', strtotime($session['reviewEndTime']))) ?></td>
                                        <td><?= htmlspecialchars($session['reviewLocation']) ?></td>
                                        <td><?= htmlspecialchars($session['reviewStatus']) ?></td>
                                        <td class="actions">
                                            <button class="btn btn-danger delete-session-btn" data-sessionid="<?= htmlspecialchars($session['reviewSessionID']) ?>">
                                                <i data-feather="trash-2"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i data-feather="calendar" class="empty-state-icon"></i>
                        <h3>No Study Sessions Found</h3>
                        <p>There are no study sessions to display at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace(); // Initialize Feather icons
            const csrfToken = <?php echo json_encode($csrfToken); ?>;

            // Delete User
            document.querySelectorAll('.delete-user-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userid;
                    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                        fetch('/cmsc126-study-session-management-system/public/admin/deleteUser', { // Corrected URL
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'userID=' + userId + '&csrf_token=' + encodeURIComponent(csrfToken)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload(); 
                            } else {
                                alert('Error: ' + (data.message || 'Could not delete user.'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the user.');
                        });
                    }
                });
            });

            // Delete Study Session
            document.querySelectorAll('.delete-session-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const sessionId = this.dataset.sessionid;
                    if (confirm('Are you sure you want to delete this study session? This action cannot be undone.')) {
                        fetch('/cmsc126-study-session-management-system/public/admin/deleteStudySession', { // Corrected URL
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'reviewSessionID=' + sessionId + '&csrf_token=' + encodeURIComponent(csrfToken)
                        })
                        .then(response => response.json())
                        .then(data => { // Corrected: added parentheses around data
                            if (data.success) {
                                alert(data.message);
                                location.reload(); 
                            } else {
                                alert('Error: ' + (data.message || 'Could not delete session.'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while deleting the study session.');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
