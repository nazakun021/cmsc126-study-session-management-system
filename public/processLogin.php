<?php
session_start(); // Start the session at the very beginning
require_once 'cmsc126-study-session-management-system/config/db_connection.php'; // Adjust path as needed

// --- Basic Input Validation ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    // Optional: Set an error message if accessed directly
    // $_SESSION['error'] = "Invalid request method.";
    header("Location: login.php"); // Redirect if not POST
    exit;
}

// --- Collect and Sanitize Inputs ---
$username = trim($_POST['userName'] ?? ''); // Match the 'name' attribute from login.php
$password = $_POST['password'] ?? '';     // Don't trim password
$role = trim($_POST['role'] ?? '');       // Match the 'name' attribute from login.php

// --- More Validation ---
if (empty($username) || empty($password) || empty($role)) {
    $_SESSION['error'] = "Please fill in username, password, and select a role.";
    header("Location: login.php");
    exit;
}

// Optional: Validate if the role is one of the expected values
if ($role !== 'student' && $role !== 'admin') {
     $_SESSION['error'] = "Invalid role selected.";
     header("Location: login.php");
     exit;
}


// --- Check Credentials Against Database (Prepared Statement) ---
try {
    // Prepare SQL to find the user by username AND role
    // We select the userID and the stored hashed password
    $sql = "SELECT userID, password FROM User WHERE username = :username AND role = :role";
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':role', $role);

    // Execute the query
    $stmt->execute();

    // Fetch the user data (if found)
    // Use fetch() because username should be unique for a given role (or unique overall)
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch as associative array

    // --- Verify User and Password ---
    if ($user) {
        // User found, now verify the password
        // password_verify() compares the plain text password with the stored hash
        if (password_verify($password, $user['password'])) {
            // Password is correct! Login successful.

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user information in the session
            $_SESSION['user_id'] = $user['userID'];
            $_SESSION['username'] = $username; // Store the username
            $_SESSION['role'] = $role;         // Store the role
            $_SESSION['logged_in'] = true;     // Flag to indicate user is logged in

            // --- Redirect based on role ---
            if ($role === 'admin') {
                // Redirect to the admin dashboard (create this page)
                header("Location: admin_dashboard.php");
                exit;
            } else { // Default to student if not admin (or add more roles)
                // Redirect to the student dashboard (create this page)
                header("Location: student_dashboard.php");
                exit;
            }

        } else {
            // Password incorrect
            $_SESSION['error'] = "Invalid username, password, or role."; // Keep error generic
            header("Location: login.php");
            exit;
        }
    } else {
        // User not found with that username and role combination
        $_SESSION['error'] = "Invalid username, password, or role."; // Keep error generic
        header("Location: login.php");
        exit;
    }

} catch (PDOException $e) {
    // Handle potential database errors during login
    // Log the error properly in a real application (e.g., error_log($e->getMessage());)
    $_SESSION['error'] = "Login failed due to a system error. Please try again later."; // User-friendly message
    // Optionally log the detailed error: error_log("Login PDOException: " . $e->getMessage());
    header("Location: login.php");
    exit;
}

// Close connection? Usually not needed with PDO if script ends, but good practice if script continues.
// $pdo = null; // Not strictly necessary here as the script exits shortly after

?>