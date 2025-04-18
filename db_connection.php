<?php
// db_connection.php (PDO Version)

$db_host = "localhost";        // Usually 'localhost'
$db_name = "cmsc126_database"; // DB name
$db_user = "root";             // Default XAMPP/MAMP user
$db_pass = "";                 // Default XAMPP/MAMP password is empty
$charset = 'utf8mb4';          // Recommended charset

// Data Source Name (DSN) for PDO
// Specifies the database type (mysql), host, database name, and character set
$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

// PDO Options Array
$options = [
     // Error Reporting: Throw exceptions on errors for robust handling
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     // Default Fetch Mode: Fetch results as associative arrays (key => value)
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     // Emulated Prepares: Disable emulation for security and performance with MySQL
     PDO::ATTR_EMULATE_PREPARES   => false,
 ];
 
 try {
      // Attempt to create the PDO instance (database connection object)
      $pdo = new PDO($dsn, $db_user, $db_pass, $options);
 
 } catch (\PDOException $e) {
      // Catch any errors during connection
      // Log the detailed error securely (check your PHP error log)
      error_log("Database Connection Error: " . $e->getMessage());
 
      // Display a generic error message to the user and stop the script
      exit("Database connection failed. Please check server logs or contact support.");
 }
 
 // If the script reaches this point without exiting, the connection was successful.
 // Any script that includes this file (e.g., using require_once 'db_connection.php';)
 // will now have access to the $pdo object to interact with the database.
 ?>