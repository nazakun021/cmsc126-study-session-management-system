<?php
session_start(); // Needed for messages
require_once 'db_connection.php'; // Make sure this includes the NEW PDO connection file

// --- Basic Input Validation ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: register.php"); // Redirect if not POST
    exit;
}

// Collect and sanitize basic inputs (using camelCase as requested)
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? ''; // Don't trim password
$confirmPassword = $_POST['confirmPassword'] ?? '';
$firstName = trim($_POST['firstName'] ?? ''); // Use camelCase
$lastName = trim($_POST['lastName'] ?? '');   // Use camelCase

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
// Note: $pdo object comes from the included db_connection.php
$stmtCheck = null; // Initialize statement variable
$stmtUser = null;
$stmtStudent = null;
try {
    $stmtCheck = $pdo->prepare("SELECT userID FROM User WHERE username = :username OR email = :email");
    $stmtCheck->bindParam(':username', $username);
    $stmtCheck->bindParam(':email', $email);
    $stmtCheck->execute();

    if ($stmtCheck->fetch()) { // PDO::FETCH_ASSOC is the default now
        $_SESSION['error'] = "Username or Email is already taken.";
        header("Location: register.php");
        exit;
    }
    $stmtCheck->closeCursor(); // Close cursor after fetching

} catch (PDOException $e) {
    error_log("Database Check Error (Register): " . $e->getMessage()); // Log detailed error
    $_SESSION['error'] = "An error occurred checking user existence. Please try again."; // Generic user message
    header("Location: register.php");
    exit;
}

// --- Hash the Password (CRITICAL!) ---
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

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
    $stmtUser->closeCursor(); // Close cursor

    // 2. Insert into Student table (Using correct variable names)
    $sqlStudent = "INSERT INTO Student (userID, firstName, lastName /*, other_columns... */)
                   VALUES (:userId, :firstName, :lastName /*, :other_values... */)";
    $stmtStudent = $pdo->prepare($sqlStudent);
    $stmtStudent->bindParam(':userId', $userId, PDO::PARAM_INT); // Good practice to specify type
    $stmtStudent->bindParam(':firstName', $firstName);          // Correct variable name
    $stmtStudent->bindParam(':lastName', $lastName);            // Correct variable name
    // --- Bind other student parameters here if collected from the form ---

    $stmtStudent->execute();
    $stmtStudent->closeCursor(); // Close cursor

    // If both inserts were successful, commit the transaction
    $pdo->commit();

    $_SESSION['success'] = "Registration successful! Please log in.";
    header("Location: login.php");
    exit;

} catch (PDOException $e) {
    // If any error occurred, roll back the transaction
    if ($pdo->inTransaction()) {
         $pdo->rollBack();
    }
    error_log("Registration Error: " . $e->getMessage()); // Log detailed error
    $_SESSION['error'] = "Registration failed due to a system error. Please try again."; // Generic user message
    header("Location: register.php");
    exit;
} finally {
    // PDO automatically closes the connection when the script ends or the $pdo object goes out of scope.
    // Closing cursors was done inside the blocks.
}
?>