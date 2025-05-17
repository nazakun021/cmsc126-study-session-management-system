<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class User extends Model {
    protected $table = 'user';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($username, $password) {
        try {
            // Input validation
            if (empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'error' => 'Username and password are required.'
                ];
            }

            // Sanitize username
            $username = strip_tags($username);

            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE userName = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user) {
                return [
                    'success' => false,
                    'error' => 'Account not found.'
                ];
            }

            if (!password_verify($password, $user['password'])) {
                return [
                    'success' => false,
                    'error' => 'Invalid password.'
                ];
            }

            // Remove sensitive data before returning
            unset($user['password']);

            return [
                'success' => true,
                'user' => $user
            ];
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred during login. Please try again.'
            ];
        }
    }

    public function register($username, $email, $password, $confirmPassword, $courseId, $role = 'user') {
        try {
            // Input validation
            if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                return [
                    'success' => false,
                    'error' => 'All fields are required.'
                ];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'error' => 'Invalid email format.'
                ];
            }

            if (strlen($password) < 8) {
                return [
                    'success' => false,
                    'error' => 'Password must be at least 8 characters long.'
                ];
            }

            if ($password !== $confirmPassword) {
                return [
                    'success' => false,
                    'error' => 'Passwords do not match!'
                ];
            }

            // Sanitize inputs
            $username = strip_tags($username);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $courseId = filter_var($courseId, FILTER_SANITIZE_NUMBER_INT);
            $role = strip_tags($role);

            // Check for existing user
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE userName = :username OR email = :email");
            $stmt->execute([':username' => $username, ':email' => $email]);
            
            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'error' => 'Username or email already exists.'
                ];
            }

            // Hash password with strong algorithm
            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
            
            // Insert new user
            $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (userName, email, password, courseID, role) VALUES (:username, :email, :password, :courseID, :role)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':courseID' => $courseId,
                ':role' => $role
            ]);

            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred during registration. Please try again.'
            ];
        }
    }

    public function getUserById($userId) {
        try {
            $userId = filter_var($userId, FILTER_SANITIZE_NUMBER_INT);
            $stmt = $this->pdo->prepare("SELECT userID, userName, email, courseID FROM {$this->table} WHERE userID = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        } catch (PDOException $e) {
            error_log("Error fetching user by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getAllUsers() { // Added getAllUsers method
        try {
            // Join with courses table to get courseName
            $sql = "SELECT u.userID, u.userName, u.email, u.courseID, u.role, c.courseName 
                    FROM {$this->table} u
                    LEFT JOIN courses c ON u.courseID = c.courseID
                    WHERE u.role != 'admin'";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all users: " . $e->getMessage());
            return [];
        }
    }

    public function deleteUser($userID) {
        try {
            $userID = filter_var($userID, FILTER_SANITIZE_NUMBER_INT);

            // Optional: Check if the user exists before attempting to delete
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE userID = :userID");
            $stmt->execute([':userID' => $userID]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'success' => false,
                    'error' => 'User not found.'
                ];
            }

            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE userID = :userID");
            if ($stmt->execute([':userID' => $userID])) {
                return ['success' => true];
            } else {
                $errorInfo = $stmt->errorInfo();
                return [
                    'success' => false,
                    'error' => 'Failed to delete user: ' . ($errorInfo[2] ?? 'Unknown database error')
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while deleting the user.'
            ];
        }
    }
}
?>
