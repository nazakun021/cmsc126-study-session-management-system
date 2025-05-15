<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;
use DateTime;

class StudySession extends Model {
    protected $table = 'reviewsession';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllSessions() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT rs.*, c.courseName
                FROM {$this->table} rs
                JOIN courses c ON rs.subjectID = c.id
                ORDER BY rs.reviewDate ASC, rs.reviewStartTime ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Error getting all sessions: ' . $e->getMessage());
            return [];
        }
    }

    private function validateSessionData($data) {
        $errors = [];

        // Required fields validation
        $requiredFields = ['subjectID', 'reviewTitle', 'reviewDate', 'reviewStartTime', 'reviewEndTime', 'reviewLocation'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('review', '', $field)) . " is required.";
            }
        }

        // Date and time validation
        if (!empty($data['reviewDate'])) {
            $date = DateTime::createFromFormat('Y-m-d', $data['reviewDate']);
            if (!$date || $date->format('Y-m-d') !== $data['reviewDate']) {
                $errors[] = "Invalid date format. Use YYYY-MM-DD.";
            }
        }

        if (!empty($data['reviewStartTime']) && !empty($data['reviewEndTime'])) {
            $startTime = DateTime::createFromFormat('H:i', $data['reviewStartTime']);
            $endTime = DateTime::createFromFormat('H:i', $data['reviewEndTime']);
            
            if (!$startTime || !$endTime) {
                $errors[] = "Invalid time format. Use HH:MM.";
            } elseif ($startTime >= $endTime) {
                $errors[] = "End time must be after start time.";
            }
        }

        return $errors;
    }

    public function createSession($data) {
        try {
            // Validate input data
            $errors = $this->validateSessionData($data);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            // Sanitize input data
            $sanitizedData = [
                ':subjectID' => filter_var($data['subjectID'], FILTER_SANITIZE_NUMBER_INT),
                ':reviewTitle' => filter_var($data['reviewTitle'], FILTER_SANITIZE_STRING),
                ':reviewDate' => $data['reviewDate'],
                ':reviewStartTime' => $data['reviewStartTime'],
                ':reviewEndTime' => $data['reviewEndTime'],
                ':reviewLocation' => filter_var($data['reviewLocation'], FILTER_SANITIZE_STRING),
                ':reviewDescription' => filter_var($data['reviewDescription'] ?? '', FILTER_SANITIZE_STRING),
                ':reviewTopic' => filter_var($data['reviewTopic'] ?? '', FILTER_SANITIZE_STRING),
                ':reviewStatus' => filter_var($data['reviewStatus'] ?? 'scheduled', FILTER_SANITIZE_STRING)
            ];

            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (
                    subjectID, 
                    reviewTitle, 
                    reviewDate, 
                    reviewStartTime, 
                    reviewEndTime, 
                    reviewLocation, 
                    reviewDescription, 
                    reviewTopic, 
                    reviewStatus
                ) VALUES (
                    :subjectID,
                    :reviewTitle,
                    :reviewDate,
                    :reviewStartTime,
                    :reviewEndTime,
                    :reviewLocation,
                    :reviewDescription,
                    :reviewTopic,
                    :reviewStatus
                )
            ");

            if ($stmt->execute($sanitizedData)) {
                return [
                    'success' => true,
                    'sessionId' => $this->pdo->lastInsertId()
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to create study session.'
            ];
        } catch (PDOException $e) {
            error_log("Error creating study session: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while creating the study session.'
            ];
        }
    }

    public function getSessionsByStudent($studentId) {
        try {
            $studentId = filter_var($studentId, FILTER_SANITIZE_NUMBER_INT);
            
            $stmt = $this->pdo->prepare("
                SELECT * FROM {$this->table} 
                WHERE studentId = :studentId 
                ORDER BY reviewDate ASC, reviewStartTime ASC
            ");
            $stmt->execute([':studentId' => $studentId]);
            
            return [
                'success' => true,
                'sessions' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching study sessions: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to fetch study sessions.'
            ];
        }
    }

    public function updateSession($sessionId, $data) {
        try {
            // Validate input data
            $errors = $this->validateSessionData($data);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'errors' => $errors
                ];
            }

            $sessionId = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
            
            // Sanitize input data
            $sanitizedData = [
                ':subjectID' => filter_var($data['subjectID'], FILTER_SANITIZE_NUMBER_INT),
                ':reviewTitle' => filter_var($data['reviewTitle'], FILTER_SANITIZE_STRING),
                ':reviewDate' => $data['reviewDate'],
                ':reviewStartTime' => $data['reviewStartTime'],
                ':reviewEndTime' => $data['reviewEndTime'],
                ':reviewLocation' => filter_var($data['reviewLocation'], FILTER_SANITIZE_STRING),
                ':reviewDescription' => filter_var($data['reviewDescription'] ?? '', FILTER_SANITIZE_STRING),
                ':reviewTopic' => filter_var($data['reviewTopic'] ?? '', FILTER_SANITIZE_STRING),
                ':reviewStatus' => filter_var($data['reviewStatus'] ?? 'scheduled', FILTER_SANITIZE_STRING),
                ':sessionId' => $sessionId
            ];

            $stmt = $this->pdo->prepare("
                UPDATE {$this->table} 
                SET 
                    subjectID = :subjectID,
                    reviewTitle = :reviewTitle,
                    reviewDate = :reviewDate,
                    reviewStartTime = :reviewStartTime,
                    reviewEndTime = :reviewEndTime,
                    reviewLocation = :reviewLocation,
                    reviewDescription = :reviewDescription,
                    reviewTopic = :reviewTopic,
                    reviewStatus = :reviewStatus
                WHERE id = :sessionId
            ");

            if ($stmt->execute($sanitizedData)) {
                return [
                    'success' => true,
                    'message' => 'Study session updated successfully.'
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to update study session.'
            ];
        } catch (PDOException $e) {
            error_log("Error updating study session: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while updating the study session.'
            ];
        }
    }

    public function deleteSession($sessionId) {
        try {
            $sessionId = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
            
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :sessionId");
            
            if ($stmt->execute([':sessionId' => $sessionId])) {
                return [
                    'success' => true,
                    'message' => 'Study session deleted successfully.'
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to delete study session.'
            ];
        } catch (PDOException $e) {
            error_log("Error deleting study session: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'An error occurred while deleting the study session.'
            ];
        }
    }

    public function getUpcomingSessions() {
        try {
            $userId = $_SESSION['userId'];
            
            $stmt = $this->pdo->prepare("
                SELECT rs.*, s.subjectName 
                FROM {$this->table} rs
                INNER JOIN subjects s ON rs.subjectID = s.subjectID
                INNER JOIN course_subjects cs ON s.subjectID = cs.subjectID
                INNER JOIN user_courses uc ON cs.courseID = uc.courseID
                WHERE uc.userID = :userId 
                AND rs.reviewDate >= CURDATE()
                ORDER BY rs.reviewDate ASC, rs.reviewStartTime ASC
            ");
            
            $stmt->execute([':userId' => $userId]);
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'sessions' => $sessions
            ];
        } catch (PDOException $e) {
            error_log("Error fetching upcoming sessions: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to fetch upcoming sessions.'
            ];
        }
    }
}
?>
