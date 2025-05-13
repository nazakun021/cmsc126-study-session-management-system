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
            $username = filter_var($username, FILTER_SANITIZE_STRING);

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

    public function register($username, $email, $password, $confirmPassword, $courseId) {
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
            $username = filter_var($username, FILTER_SANITIZE_STRING);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            $courseId = filter_var($courseId, FILTER_SANITIZE_NUMBER_INT);

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
            
            $data = [
                'userName' => $username,
                'email' => $email,
                'password' => $hashedPassword,
                'courseID' => $courseId
            ];

            if ($this->create($data)) {
                return ['success' => true];
            }

            return [
                'success' => false,
                'error' => 'Registration failed due to a system error.'
            ];
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
}
?>
