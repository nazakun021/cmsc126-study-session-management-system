<?php
    session_start();
    require_once 'db_connection.php'; // Uses the PDO connection setup

    // Initialize courses array and error flag
    $courses = [];
    $coursesError = false;

    try {
      $stmtCourses = $pdo->query("SELECT courseID, courseName FROM Courses ORDER BY courseName ASC");
      $courses = $stmtCourses->fetchAll();
      $stmtCourses->closeCursor();
    } catch (PDOException $e) {
      error_log("Error fetching courses for registration: " . $e->getMessage());
      $coursesError = true;
    }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Account</title>
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

    <label for="course">Course</label>
    <select id="course" name="courseID" required <?php if ($coursesError || empty($courses)) echo 'disabled'; ?>>
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

    <button type="submit" <?php if ($coursesError || empty($courses)) echo 'disabled'; ?>>Continue</button>
  </form>
  <div class="footer">
    Already have an account? <a href="login.php" style="color: #0071e3; text-decoration: none;">Sign in</a>
  </div>
</div>

<script src="validateForm.js"></script>

</body>
</html>