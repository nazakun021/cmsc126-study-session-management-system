<?php
session_start();
// Updated Access control - Check only for login status
if (!isset($_SESSION['isLoggedIn']) || $_SESSION['isLoggedIn'] !== true) {
    header('Location: login.php');
    exit;
}

// --- Rest of your dashboard content below ---
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Your User ID: <?php echo htmlspecialchars($_SESSION['userId']); ?></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>