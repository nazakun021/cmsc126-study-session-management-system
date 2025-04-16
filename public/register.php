<?php
    session_start();
    if (isset($_SESSION['error'])) {
      echo '<p style="color:red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
      unset($_SESSION['error']); // Clear message after displaying
    }
    if (isset($_SESSION['success'])) {
      echo '<p style="color:green;">' . htmlspecialchars($_SESSION['success']) . '</p>';
      unset($_SESSION['success']);
    }
  ?>

<!DOCTYPE html>
<html>
<head>
  <title>Study Session Management System Registration</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="form-container">
  <h2>Create Account</h2>

  <form action="processRegister.php" id="registerForm" method="POST" onsubmit="return validateForm()">
    
    <label for="userName">Username</label>
    <input type="text" id="userName" name="userName" required>
    <div id="usernameError" class="error"></div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <label for="confirmPassword">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required>

    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" required><br>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" required><br>

    <button type="submit">Continue</button>
  </form>
  <div class="footer">
    Already have an account? <a href="login.php" style="color: #0071e3; text-decoration: none;">Sign in</a>
  </div>
</div>

<script src="validateForm.js"></script>

</body>
</html>