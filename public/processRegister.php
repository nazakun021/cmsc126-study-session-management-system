<?php
session_start(); // Needed for messages
require_once 'db_connection.phpconfig\db_connection.php'; // Adjust path as needed

// --- Basic Input Validation ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: register.php"); // Redirect if not POST
    exit;
}

// Collect and sanitize basic inputs (add more fields as needed)
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? ''; // Don't trim password
$confirmPassword = $_POST['confirmPassword'] ?? '';
$firstName = trim($_POST['firstName'] ?? '');
$lastName = trim($_POST['lastName'] ?? '');

// --- More Validation ---
if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: register.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "Invalid email format.";
    header("Location: register.php");
    exit;
}

if (strlen($password) < 6) { // Example: Minimum password length
    $_SESSION['error'] = "Password must be at least 6 characters long.";
    header("Location: register.php");
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: register.php");
    exit;
}

// --- Check if username or email already exists (Prepared Statement) ---
try {
    $stmtCheck = $pdo->prepare("SELECT userID FROM User WHERE username = :username OR email = :email");
    $stmtCheck->bindParam(':username', $username);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();

    if ($stmtCheck->fetch()) {
        $_SESSION['error'] = "Username or Email is already taken.";
        header("Location: register.php");
        exit;
    }
} catch (PDOException $e) {
    // Log error properly in real application
    $_SESSION['error'] = "Database check failed: " . $e->getMessage(); // Dev message
    header("Location: register.php");
    exit;
}


// --- Hash the Password (CRITICAL!) ---
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Recommended default hashing

// --- Insert into Database (Transaction) ---
try {
    $pdo->beginTransaction();

    // 1. Insert into User table
    $sqlUser = "INSERT INTO User (username, email, password, role) VALUES (:username, :email, :password, :role)";
    $stmtUser = $pdo->prepare($sqlUser);
    $role = 'student'; // Default role for registration
    $stmtUser->bindParam(':username', $username);
    $stmtUser->bindParam(':email', $email);
    $stmtUser->bindParam(':password', $hashed_password);
    $stmtUser->bindParam(':role', $role);
    $stmtUser->execute();
    $userId = $pdo->lastInsertId(); // Get the new User's ID

    // 2. Insert into Student table
    // Adjust SQL and bindings based on your actual fields
    $sqlStudent = "INSERT INTO Student (userID, firstName, lastName /*, departmentID, courseID, yearLevelID ... */)
                   VALUES (:userID, :firstName, :lastName /*, :departmentID, :courseID, :yearLevelID ... */)";
    $stmtStudent = $pdo->prepare($sqlStudent);
    $stmtStudent->bindParam(':userID', $userId);
    $stmtStudent->bindParam(':firstName', $firstName);
    $stmtStudent->bindParam(':lastName', $lastName);
    // Bind other student parameters... $stmtStudent->bindParam(':departmentID', $departmentID);

    $stmtStudent->execute();

    // If both inserts were successful, commit the transaction
    $pdo->commit();

    $_SESSION['success'] = "Registration successful! Please log in.";
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    // If any error occurred, roll back the transaction
    $pdo->rollBack();
    // Log error properly in real application
    $_SESSION['error'] = "Registration failed: " . $e->getMessage(); // Dev message
    header("Location: register.php");
    exit;
}

?>