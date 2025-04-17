<?php
    session_start();
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

  <?php
    if (isset($_SESSION['error'])) {
      echo '<p style="color:red;">' . htmlspecialchars($_SESSION['error']) . '</p>';
      unset($_SESSION['error']); // Clear message after displaying
    }
    if (isset($_SESSION['success'])) {
      echo '<p style="color:green;">' . htmlspecialchars($_SESSION['success']) . '</p>';
      unset($_SESSION['success']);
    }
  ?>

  <form action="processRegister.php" id="registerForm" method="POST" onsubmit="return validateForm()">
    
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autocomplete="username">
    <div id="usernameError" class="error"></div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required autocomplete="email">
    <div id="emailError" class="error"></div>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="password">
    <div id="passwordError" class="error"></div>

    <label for="confirmPassword">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required autocomplete="password">
    <div id="confirmPasswordError" class="error"></div>

    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" required autocomplete="firstName">
    <div id="firstNameError" class="error"></div>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" required autocomplete="lastName">
    <div id="lastNameError" class="error"></div>

    <button type="submit">Continue</button>
  </form>
  <div class="footer">
    Already have an account? <a href="login.php" style="color: #0071e3; text-decoration: none;">Sign in</a>
  </div>
</div>

<script src="validateForm.js"></script>

</body>
</html>