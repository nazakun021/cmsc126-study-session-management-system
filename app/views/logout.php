<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <link rel="stylesheet" href="/cmsc126-study-session-management-system/public/css/styles.css">
</head>
<body>
    <div class="content-container" style="max-width: 400px; margin: 100px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 2rem; text-align: center;">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <form method="post" action="/cmsc126-study-session-management-system/app/controllers/AuthController.php?action=logout">
            <button type="submit" name="confirm_logout" class="btn btn-danger" style="margin-right: 1rem;">Yes, Log me out</button>
            <a href="/cmsc126-study-session-management-system/app/views/dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html> 