<?php
class User {
    private $pdo;

    public function __construct() {
        $this->pdo = require_once __DIR__ . '/../config/db_connection.php';
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        return $user && password_verify($password, $user['password']);
    }

    public function register($username, $email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }
}







// class User {
//     private $pdo;

//     public function __construct() {
//         require_once '../config/db_connection.php';
//         $this->pdo = $pdo;
//     }

//     // Register a New User
//     public function register($username, $email, $password, $confirmPassword, $courseId) {
//         // Validate password match
//         if ($password !== $confirmPassword) {
//             throw new Exception("Passwords do not match!");
//         }

//         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
//         $stmt = $this->pdo->prepare("INSERT INTO User (username, email, password, courseID) VALUES (:username, :email, :password, :courseId)");
//         return $stmt->execute([
//             ':username' => $username, 
//             ':email' => $email, 
//             ':password' => $hashedPassword, 
//             ':courseId' => $courseId
//         ]);
//     }

//     // Authenticate User
//     public function login($username, $password) {
//         $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE username = ?");
//         $stmt->execute([$username]);
//         $user = $stmt->fetch();

//         if ($user && password_verify($password, $user['password'])) {
//             return $user;
//         }
//         return false;
//     }
// }
?>
