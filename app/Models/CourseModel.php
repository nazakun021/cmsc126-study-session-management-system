<?php
namespace App\Models;

use App\Core\Model;

class CourseModel extends Model {
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCourses() {
        try{
            $stmtCourses = $this->pdo->query("SELECT courseID, courseName FROM Courses ORDER BY courseName ASC");
            $courses = $stmtCourses->fetchAll();
            $stmtCourses->closeCursor();
            return $courses;
        } catch (PDOException $e) {
            error_log("Error fetching courses from Database: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSubjects() {
        try {
            $stmt = $this->pdo->query("SELECT subjectID, subjectName FROM Subjects ORDER BY subjectName ASC");
            $subjects = $stmt->fetchAll();
            $stmt->closeCursor();
            return $subjects;
        } catch (\PDOException $e) {
            error_log("Error fetching subjects from Database: " . $e->getMessage());
            return false;
        }
    }
}
?>
