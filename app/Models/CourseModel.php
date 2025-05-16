<?php
namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

class CourseModel extends Model {
    protected $table = 'Courses';

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCourses() {
        try {
            $stmt = $this->pdo->query("
                SELECT courseID, courseName 
                FROM {$this->table} 
                ORDER BY courseName ASC
            ");
            
            return [
                'success' => true,
                'courses' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching courses from Database: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to fetch courses.'
            ];
        }
    }

    public function getAllSubjects() {
        try {
            $stmt = $this->pdo->query("
                SELECT MIN(subjectID) as subjectID, subjectName 
                FROM Subjects 
                GROUP BY subjectName 
                ORDER BY subjectName ASC
            ");
            
            return [
                'success' => true,
                'subjects' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching subjects from Database: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to fetch subjects.'
            ];
        }
    }

    public function getCourseById($courseId) {
        try {
            $courseId = filter_var($courseId, FILTER_SANITIZE_NUMBER_INT);
            
            $stmt = $this->pdo->prepare("
                SELECT courseID, courseName 
                FROM {$this->table} 
                WHERE courseID = :courseId
            ");
            
            $stmt->execute([':courseId' => $courseId]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($course) {
                return [
                    'success' => true,
                    'course' => $course
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Course not found.'
            ];
        } catch (PDOException $e) {
            error_log("Error fetching course from Database: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to fetch course details.'
            ];
        }
    }

    public function getSubjectsByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT MIN(s.subjectID) as subjectID, s.subjectName 
                FROM subjects s
                INNER JOIN course_subjects cs ON s.subjectID = cs.subjectID
                INNER JOIN user_courses uc ON cs.courseID = uc.courseID
                WHERE uc.userID = ?
                GROUP BY s.subjectName
                ORDER BY s.subjectName ASC
            ");
            
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching subjects for user: " . $e->getMessage());
            return [];
        }
    }
}
?>
