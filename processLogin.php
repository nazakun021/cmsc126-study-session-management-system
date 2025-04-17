<?php
session_start(); // Start the session at the very beginning
require_once 'db_connection.php'; // Make sure this includes the NEW PDO connection file

// --- Basic Input Validation ---
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php"); // Redirect if not POST
    exit;
}

// --- Collect and Sanitize Inputs ---
$username = trim($_POST['username'] ?? ''); // Match the 'name' attribute from login.php
$password = $_POST['password'] ?? '';     // Don't trim password

// --- More Validation ---
if (empty($username) || empty($password)) {
    // More specific error message
    $_SESSION['error'] = "Please fill in both username and password.";
    header("Location: login.php");
    exit;
}

// --- Check Credentials Against Database (Prepared Statement) ---
// Note: $pdo object comes from the included db_connection.php
$stmt = null; // Initialize statement variable
try {
    // Prepare SQL to find the user by username
    // Select userID, password HASH, and the user's role
    $sql = "SELECT userID, password, role FROM User WHERE username = :username"; // Added role
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':username', $username);

    // Execute the query
    $stmt->execute();

    // Fetch the user data (if found)
    $user = $stmt->fetch(); // PDO::FETCH_ASSOC is the default now

    // --- Verify User and Password ---
    if ($user) {
        // User found, now verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct! Login successful.

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store user information in the session (using camelCase for consistency)
            $_SESSION['userId'] = $user['userID'];      // Store the user ID
            $_SESSION['username'] = $username;          // Store the username
            $_SESSION['role'] = $user['role'];          // Store the role fetched from DB
            $_SESSION['isLoggedIn'] = true;             // Flag to indicate user is logged in

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php"); // Redirect admin
                exit;
            } elseif ($user['role'] === 'student') {
                header("Location: student_dashboard.php"); // Redirect student
                exit;
            } else {
                // Handle unexpected role (optional, maybe redirect to a default page or show error)
                $_SESSION['error'] = "Login successful, but role is undefined. Contact support.";
                header("Location: login.php"); // Redirect back to login for now
                exit;
            }

        } else {
            // Password incorrect
            $_SESSION['error'] = "Invalid username or password."; // Keep error generic for security
            header("Location: login.php");
            exit;
        }
    } else {
        // User not found with that username
        $_SESSION['error'] = "Invalid username or password."; // Keep error generic for security
        header("Location: login.php");
        exit;
    }

} catch (PDOException $e) {
    // Handle potential database errors during login
    error_log("Login PDOException: " . $e->getMessage()); // Log detailed error
    $_SESSION['error'] = "Login failed due to a system error. Please try again later."; // User-friendly message
    header("Location: login.php");
    exit;
} finally {
     // Close cursor if statement was prepared
     if ($stmt) {
         $stmt->closeCursor();
     }
    // PDO automatically closes the connection when the script ends
}
?>