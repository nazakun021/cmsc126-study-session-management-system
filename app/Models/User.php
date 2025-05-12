<?php
namespace App\Models;

use App\Core\Model;

class User extends Model {
    protected $table = 'user';

    public function login($username, $password) {
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

        return [
            'success' => true,
            'user' => $user
        ];
    }

    public function register($username, $email, $password, $confirmPassword, $courseId) {
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'error' => 'Passwords do not match!'
            ];
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE userName = :username OR email = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        
        if ($stmt->fetchColumn() > 0) {
            return [
                'success' => false,
                'error' => 'Username or email already exists.'
            ];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
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
    }
}
?>
