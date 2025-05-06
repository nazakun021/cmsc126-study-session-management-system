<?php
class CourseModel {
    private $pdo;

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
}
?>
