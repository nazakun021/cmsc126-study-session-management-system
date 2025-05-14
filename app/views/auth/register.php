<!DOCTYPE html>
<html>
<head>
  <title>Create Account</title>
  <link rel="stylesheet" href="/cmsc126-study-session-management-system/public/css/loginRegister.css">
</head>
<body>

<div class="form-container">
  <h2>Create Account</h2>

  <?php
    if (isset($_SESSION['error'])) {
      echo '<p class="error-message" style="color:red; text-align: center; margin-bottom: 15px;">' . htmlspecialchars($_SESSION['error']) . '</p>';
      unset($_SESSION['error']); 
    }
    if (isset($_SESSION['success'])) {
      echo '<p class="success-message" style="color:green; text-align: center; margin-bottom: 15px;">' . htmlspecialchars($_SESSION['success']) . '</p>';
      unset($_SESSION['success']);
    }
  ?>

  <form id="registerForm" action="/cmsc126-study-session-management-system/public/register" method="POST" onsubmit="return validateForm()">
    <input type="hidden" name="action" value="processRegister">
    
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autocomplete="username">
    <div id="usernameError" class="error"></div>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" required autocomplete="email">
    <div id="emailError" class="error"></div>

    <label for="course">Course</label>
    <select id="course" name="courseID" required 
    <?php if ($coursesError || empty($courses)) echo 'disabled'; ?>>
      <option value="" disabled selected>Select your course</option>
      <?php if (!$coursesError && !empty($courses)): ?>
        <?php foreach ($courses as $course): ?>
          <option value="<?php echo htmlspecialchars($course['courseID']); ?>">
            <?php echo htmlspecialchars($course['courseName']); ?>
          </option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
    <div id="courseError" class="error"></div>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="password">
    <div id="passwordError" class="error"></div>

    <label for="confirmPassword">Confirm Password</label>
    <input type="password" id="confirmPassword" name="confirmPassword" required autocomplete="password">
    <div id="confirmPasswordError" class="error"></div> 

    <button type="submit" <?php if (isset($coursesError) && ($coursesError || empty($courses))): ?>disabled<?php endif; ?>>Continue</button>
  </form>
  <div class="footer">
    Already have an account? <a href="/cmsc126-study-session-management-system/public/login" style="color: #0071e3; text-decoration: none;">Sign in</a>
  </div>
</div>

<script src="/cmsc126-study-session-management-system/public/js/validateRegister.js"></script>

</body>
</html>
