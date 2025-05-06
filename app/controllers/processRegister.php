<?php
// session_start(); // Needed for messages
// require_once 'db_connection.php'; 

// // --- Basic Input Validation ---
// if ($_SERVER["REQUEST_METHOD"] != "POST") {
//     header("Location: register.php"); // Redirect if not POST
//     exit;
// }

// // Collect and sanitize basic inputs
// $username = trim($_POST['username'] ?? '');
// $email = trim($_POST['email'] ?? '');
// $password = $_POST['password'] ?? ''; // Don't trim password
// $confirmPassword = $_POST['confirmPassword'] ?? '';
// $courseId = trim($_POST['courseID'] ?? '');

// // --- More Validation ---
// if (empty($username) || empty($email) || empty($password) || empty($courseId)) {
//     $_SESSION['error'] = "Please fill in all required fields.";
//     header("Location: register.php");
//     exit;
// }

// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     $_SESSION['error'] = "Invalid email format.";
//     header("Location: register.php");
//     exit;
// }

// if (strlen($password) < 6) { // Minimum password length
//     $_SESSION['error'] = "Password must be at least 6 characters long.";
//     header("Location: register.php");
//     exit;
// }

// if ($password !== $confirmPassword) {
//     $_SESSION['error'] = "Passwords do not match.";
//     header("Location: register.php");
//     exit;
// }

// // Check if username or email already exists (Prepared Statement) 
// $stmtCheck = null; 
// $stmtUser = null;

// try {
//     $stmtCheck = $pdo->prepare("SELECT userID FROM User WHERE username = :username OR email = :email");
//     $stmtCheck->bindParam(':username', $username);
//     $stmtCheck->bindParam(':email', $email);
//     $stmtCheck->execute();

//     if ($stmtCheck->fetch()) { 
//         $_SESSION['error'] = "Username or Email is already taken.";
//         header("Location: register.php");
//         exit;  
//     }
//     $stmtCheck->closeCursor(); 

// } catch (PDOException $e) {
//     error_log("Database Check Error (Register): " . $e->getMessage());
//     $_SESSION['error'] = "An error occurred checking user existence. Please try again."; 
//     header("Location: register.php");
//     exit;
// }

// // --- Hash the Password (CRITICAL!) ---
// $hashed_password = password_hash($password, PASSWORD_DEFAULT);

// // --- Insert into Database ---
// try {
//     // Insert into User table
//     $sqlUser = "INSERT INTO User (username, email, password, courseID) VALUES (:username, :email, :password, :courseId)";
//     $stmtUser = $pdo->prepare($sqlUser);
//     $stmtUser->bindParam(':username', $username);
//     $stmtUser->bindParam(':email', $email);
//     $stmtUser->bindParam(':password', $hashed_password);
//     $stmtUser->bindParam(':courseId', $courseId, PDO::PARAM_INT);

//     $stmtUser->execute();
//     $stmtUser->closeCursor(); 

//     $_SESSION['success'] = "Registration successful! Please log in.";
//     header("Location: login.php");
//     exit;

// } catch (PDOException $e) {
//     error_log("Registration Error: " . $e->getMessage()); 
//     if ($e->getCode() == '23000') { // Integrity constraint violation
//         $_SESSION['error'] = "Registration failed: Invalid course selected or database issue.";
//    } else {
//         $_SESSION['error'] = "Registration failed due to a system error. Please try again.";
//    }
//    header("Location: register.php");
//    exit;
// } finally {
//     // Cleanup statements if they were initialized
//     // if ($stmtCheck) $stmtCheck->closeCursor(); // Already closed above
//     // if ($stmtUser) $stmtUser->closeCursor(); // Already closed above
// }
?>