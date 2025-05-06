<?php
// session_start(); // Start the session at the very beginning
// require_once 'db_connection.php'; 

// // --- Basic Input Validation ---
// if ($_SERVER["REQUEST_METHOD"] != "POST") {
//     header("Location: login.php");
//     exit;
// }

// // --- Collect and Sanitize Inputs ---
// $username = trim($_POST['username'] ?? '');
// $password = $_POST['password'] ?? '';

// // --- More Validation ---
// if (empty($username) || empty($password)) {
//     $_SESSION['error'] = "Please fill in both username and password.";
//     header("Location: login.php");
//     exit;
// }

// // --- Check Credentials Against Database (Prepared Statement) ---
// $stmt = null;
// try {
//     $sql = "SELECT userID, password FROM User WHERE username = :username";
//     $stmt = $pdo->prepare($sql);

//     // Bind parameters
//     $stmt->bindParam(':username', $username);

//     // Execute the query
//     $stmt->execute();

//     // Fetch the user data (if found)
//     $user = $stmt->fetch();

//     // --- Verify User and Password ---
//     if ($user) {
//         // User found, now verify the password
//         if (password_verify($password, $user['password'])) {
//             // Password is correct! Login successful.

//             // Regenerate session ID for security
//             session_regenerate_id(true);

//             // Store user information in the session
//             $_SESSION['userId'] = $user['userID'];      
//             $_SESSION['username'] = $username;  
//             $_SESSION['isLoggedIn'] = true; // Flag for access control

//             // *** REDIRECT ALL USERS TO THE SAME DASHBOARD ***
//             header("Location: dashboard.php");
//             exit; 

//         } else {
//             // Password incorrect
//             $_SESSION['error'] = "Invalid username or password.";
//             header("Location: login.php");
//             exit;
//         }
//     } else {
//         // User not found with that username
//         $_SESSION['error'] = "Invalid username or password.";
//         header("Location: login.php");
//         exit;
//     }

// } catch (PDOException $e) {
//     // Handle potential database errors during login
//     error_log("Login PDOException: " . $e->getMessage());
//     $_SESSION['error'] = "Login failed due to a system error. Please try again later."; 
//     header("Location: login.php");
//     exit;
// } finally {
//      // Close cursor if statement was prepared
//      if ($stmt) {
//          $stmt->closeCursor();
//      }
// }
?>