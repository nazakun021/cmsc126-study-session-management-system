<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($username, $password) {
        // Use correct table and column names
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE userName = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            // Account not found
            return [
                'success' => false,
                'error' => 'Account not found.'
            ];
        }
        if (!password_verify($password, $user['password'])) {
            // Wrong password
            return [
                'success' => false,
                'error' => 'Invalid password.'
            ];
        }
        // Success
        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function register($username, $email, $password, $confirmPassword, $courseId) {
        // Validate password match
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'error' => 'Passwords do not match!'
            ];
        }
        // Check if username or email already exists
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user WHERE userName = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->fetchColumn() > 0) {
            return [
                'success' => false,
                'error' => 'Username or email already exists.'
            ];
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO user (userName, email, password, courseID) VALUES (:username, :email, :password, :courseId)");
        $result = $stmt->execute([
            ':username' => $username, 
            ':email' => $email, 
            ':password' => $hashedPassword,
            ':courseId' => $courseId
        ]);
        if ($result) {
            return [
                'success' => true
            ];
        } else {
            return [
                'success' => false,
                'error' => 'Registration failed due to a system error.'
            ];
        }
    }
}
?>
