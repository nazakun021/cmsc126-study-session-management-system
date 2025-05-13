<?php
namespace App\Models;

use App\Core\Model;
use \PDO;
use \PDOException;

class StudySession extends Model {
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createSession($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO reviewsession (
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

            return $stmt->execute([
                ':subjectID' => $data['subjectID'],
                ':reviewTitle' => $data['reviewTitle'],
                ':reviewDate' => $data['reviewDate'],
                ':reviewStartTime' => $data['reviewStartTime'],
                ':reviewEndTime' => $data['reviewEndTime'],
                ':reviewLocation' => $data['reviewLocation'],
                ':reviewDescription' => $data['reviewDescription'],
                ':reviewTopic' => $data['reviewTopic'],
                ':reviewStatus' => $data['reviewStatus'] ?? 'scheduled'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating study session: " . $e->getMessage());
            return false;
        }
    }

    public function getSessionsByStudent($studentId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM reviewsession 
                WHERE studentId = :studentId 
                ORDER BY reviewDate ASC, reviewStartTime ASC
            ");
            $stmt->execute([':studentId' => $studentId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching study sessions: " . $e->getMessage());
            return false;
        }
    }

    public function updateSession($sessionId, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE reviewsession 
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

            $data[':sessionId'] = $sessionId;
            return $stmt->execute($data);
        } catch (PDOException $e) {
            error_log("Error updating study session: " . $e->getMessage());
            return false;
        }
    }

    public function deleteSession($sessionId) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM reviewsession WHERE id = :sessionId");
            return $stmt->execute([':sessionId' => $sessionId]);
        } catch (PDOException $e) {
            error_log("Error deleting study session: " . $e->getMessage());
            return false;
        }
    }

    public function getUpcomingSessions() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM reviewsession WHERE reviewDate >= CURDATE() ORDER BY reviewDate ASC, reviewStartTime ASC");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log('Error fetching upcoming sessions: ' . $e->getMessage());
            return false;
        }
    }
}
?>
