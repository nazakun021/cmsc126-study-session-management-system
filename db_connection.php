<?php
// db_connection.php (PDO Version)

$db_host = "localhost";     // Usually 'localhost'
$db_name = "cmsc126_database";
$db_user = "root";          // Default XAMPP/MAMP user
$db_pass = "";              // Default XAMPP/MAMP password is empty
$charset = 'utf8mb4';       // Recommended charset

// Data Source Name (DSN)
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// PDO Options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

try {
     // Create the PDO instance (your database connection object)
     $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (\PDOException $e) {
     // Log the error securely - don't echo detailed errors in production
     error_log("Database Connection Error: " . $e->getMessage());
     // Display a generic error message to the user
     exit("Database connection failed. Please check server logs or contact support."); // Stop script execution
}

// Now, any script that includes this file will have access to the $pdo object.
// No need for OpenCon/CloseCon functions with this PDO setup.
// The connection is established when the file is included.
// PDO automatically handles closing the connection when the script ends.
?>