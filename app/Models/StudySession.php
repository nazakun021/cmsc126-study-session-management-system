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
            $stmt = $this->pdo->query("
                SELECT rs.*, u.username
                FROM {$this->table} rs
                LEFT JOIN user u ON rs.creatorUserID = u.userID
                ORDER BY rs.reviewDate ASC, rs.reviewStartTime ASC
            ");
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $sessions;
        } catch (PDOException $e) {
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
                    'message' => 'Validation failed',
                    'errors' => $errors
                ];
            }

            // Sanitize input data
            $sanitizedData = [
                ':creatorUserID' => filter_var($data['creatorUserID'], FILTER_SANITIZE_NUMBER_INT),
                ':subjectID' => filter_var($data['subjectID'], FILTER_SANITIZE_NUMBER_INT),
                ':reviewTitle' => strip_tags($data['reviewTitle']),
                ':reviewDate' => $data['reviewDate'],
                ':reviewStartTime' => $data['reviewStartTime'],
                ':reviewEndTime' => $data['reviewEndTime'],
                ':reviewLocation' => strip_tags($data['reviewLocation']),
                ':reviewDescription' => strip_tags($data['reviewDescription'] ?? ''),
                ':reviewTopic' => strip_tags($data['reviewTopic'] ?? ''),
                ':reviewStatus' => strip_tags($data['reviewStatus'] ?? 'scheduled')
            ];

            // Debug log the sanitized data
            error_log('Attempting to create session with data: ' . print_r($sanitizedData, true));

            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} (
                    creatorUserID,
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
                    :creatorUserID,
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
                $sessionId = $this->pdo->lastInsertId();
                error_log('Successfully created session with ID: ' . $sessionId);
                return [
                    'success' => true,
                    'message' => 'Study session created successfully',
                    'sessionId' => $sessionId
                ];
            }

            $errorInfo = $stmt->errorInfo();
            error_log('Failed to create study session. PDO Error: ' . print_r($errorInfo, true));
            return [
                'success' => false,
                'message' => 'Failed to create study session',
                'errors' => ['Database error: ' . $errorInfo[2]]
            ];
        } catch (PDOException $e) {
            error_log("Error creating study session: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred',
                'errors' => ['An error occurred while creating the study session: ' . $e->getMessage()]
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

    public function getSessionById($sessionId) {
        try {
            $sessionId = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE reviewSessionID = :sessionId");
            $stmt->execute([':sessionId' => $sessionId]);
            $session = $stmt->fetch(PDO::FETCH_ASSOC);
            return $session ? $session : null;
        } catch (PDOException $e) {
            error_log("Error fetching session by ID: " . $e->getMessage());
            return null;
        }
    }

    public function updateSession($sessionId, $data) {
        try {
            // Validate input data
            $errors = $this->validateSessionData($data);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Validation failed', // Added message for consistency
                    'errors' => $errors
                ];
            }

            $sessionId = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
            
            // Sanitize input data
            $sanitizedData = [
                ':subjectID' => filter_var($data['subjectID'], FILTER_SANITIZE_NUMBER_INT),
                ':reviewTitle' => strip_tags($data['reviewTitle']),
                ':reviewDate' => $data['reviewDate'],
                ':reviewStartTime' => $data['reviewStartTime'],
                ':reviewEndTime' => $data['reviewEndTime'],
                ':reviewLocation' => strip_tags($data['reviewLocation']),
                ':reviewDescription' => strip_tags($data['reviewDescription'] ?? ''),
                ':reviewTopic' => strip_tags($data['reviewTopic'] ?? ''),
                ':reviewStatus' => strip_tags($data['reviewStatus'] ?? 'scheduled'),
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
                WHERE reviewSessionID = :sessionId
            "); // Changed 'id' to 'reviewSessionID'

            if ($stmt->execute($sanitizedData)) {
                if ($stmt->rowCount() > 0) {
                    return [
                        'success' => true,
                        'message' => 'Study session updated successfully.'
                    ];
                } else {
                    // Check if session exists to differentiate no change vs not found
                    // Assuming $this->pdo is available and $this->table is correct
                    $checkStmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE reviewSessionID = :sessionId");
                    $checkStmt->execute([':sessionId' => $sessionId]); // Use the un-prefixed $sessionId
                    if ($checkStmt->fetchColumn() > 0) {
                         return [
                            'success' => true, // Still success, but no rows affected
                            'message' => 'No changes detected for the study session.'
                        ];
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Study session not found.'
                        ];
                    }
                }
            }

            $errorInfo = $stmt->errorInfo(); // Get error info if execute failed
            error_log("Failed to update study session (execute failed). PDO Error: " . print_r($errorInfo, true));
            return [
                'success' => false,
                'message' => 'Failed to update study session.', // Generic message for execute failure
                'error_detail' => $errorInfo[2] ?? 'Unknown database error' // Provide detail
            ];
        } catch (PDOException $e) {
            error_log("Error updating study session: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while updating the study session.', // Generic message for exception
                'error_detail' => $e->getMessage() // Provide detail
            ];
        }
    }

    public function deleteSession($sessionId) {
        try {
            $sessionId = filter_var($sessionId, FILTER_SANITIZE_NUMBER_INT);
            
            // Corrected to use reviewSessionID
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE reviewSessionID = :sessionId"); 
            
            if ($stmt->execute([':sessionId' => $sessionId])) {
                if ($stmt->rowCount() > 0) {
                    return [
                        'success' => true,
                        'message' => 'Study session deleted successfully.'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Session not found or already deleted.' // More specific message
                    ];
                }
            }

            $errorInfo = $stmt->errorInfo();
            error_log("Failed to delete study session. PDO Error: " . print_r($errorInfo, true));
            return [
                'success' => false,
                'message' => 'Failed to delete study session.', // Kept original message for this path
                'error' => 'Database error: ' . ($errorInfo[2] ?? 'Unknown error')
            ];
        } catch (PDOException $e) {
            error_log("Error deleting study session: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the study session.', // Kept original message
                'error' => $e->getMessage()
            ];
        }
    }

    public function getUpcomingSessions($limit = 3) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT rs.*, u.username 
                FROM {$this->table} rs
                LEFT JOIN user u ON rs.creatorUserID = u.userID
                WHERE rs.reviewDate >= CURDATE()
                ORDER BY rs.reviewDate ASC, rs.reviewStartTime ASC
                LIMIT :limit
            ");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $sessions;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Get sessions filtered by subject and/or date
     */
    public function getFilteredSessions($subjectID = null, $reviewDate = null) {
        try {
            $sql = "SELECT rs.*, u.username FROM {$this->table} rs LEFT JOIN user u ON rs.creatorUserID = u.userID WHERE 1=1";
            $params = [];

            if ($subjectID) {
                $sql .= " AND rs.subjectID = :subjectID";
                $params[':subjectID'] = $subjectID;
            }

            if ($reviewDate) {
                $sql .= " AND rs.reviewDate = :reviewDate";
                $params[':reviewDate'] = $reviewDate;
            }

            $sql .= " ORDER BY rs.reviewDate ASC, rs.reviewStartTime ASC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $sessions;
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
