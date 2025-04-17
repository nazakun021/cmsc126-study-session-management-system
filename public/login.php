<?php
    session_start(); // Start session to access potential error/success messages
    if (isset($_SESSION['error'])) {
      echo '<p style="color:red; text-align: center;">' . htmlspecialchars($_SESSION['error']) . '</p>';
      unset($_SESSION['error']); // Clear message after displaying
    }
    // You might not need success messages on the login page, but you could add it:
    // if (isset($_SESSION['success'])) {
    //   echo '<p style="color:green; text-align: center;">' . htmlspecialchars($_SESSION['success']) . '</p>';
    //   unset($_SESSION['success']);
    // }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Study Session Management System Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="form-container">
  <h2>Login</h2>
  <form id="loginForm" action="processLogin.php" method="POST" onsubmit="return validateLogin()">
    <label for="userName">Username</label>
    <input type="text" id="userName" name="userName" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="role">Select Role</label>
    <select id="role" name="role" required>
      <option value="" disabled selected>Select role</option>
      <option value="student">Student</option>
      <option value="admin">Admin</option>
    </select>

    <button type="submit">Login</button>
  </form>
  <div class="footer">
    Don't have an account? <a href="Register.html">Create one</a>
  </div>
</div>

</body>
</html>