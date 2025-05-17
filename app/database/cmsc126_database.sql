-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 06:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cmsc126_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `courseID` int(11) NOT NULL,
  `courseName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseID`, `courseName`) VALUES
(1, 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `reviewattendance`
--

CREATE TABLE `reviewattendance` (
  `reviewAttendanceID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `reviewSessionID` int(11) NOT NULL,
  `attendanceStatus` enum('present','absent') NOT NULL DEFAULT 'absent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviewsession`
--

CREATE TABLE `reviewsession` (
  `reviewSessionID` int(11) NOT NULL,
  `creatorUserID` int(11) NOT NULL,
  `subjectID` int(11) NOT NULL,
  `reviewTitle` varchar(50) NOT NULL,
  `reviewDate` date NOT NULL,
  `reviewStartTime` time DEFAULT NULL,
  `reviewEndTime` time DEFAULT NULL,
  `reviewLocation` varchar(200) DEFAULT NULL,
  `reviewDescription` varchar(200) DEFAULT NULL,
  `reviewTopic` varchar(200) DEFAULT NULL,
  `reviewStatus` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subjectID` int(11) NOT NULL,
  `subjectName` varchar(100) NOT NULL,
  `courseID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subjectID`, `subjectName`, `courseID`) VALUES
(4, 'Intro to Computer Science', 1),
(5, 'Discrete Mathematical Structures in Computer Science', 1),
(6, 'Fundamentals of Programming', 1),
(7, 'Discrete Mathematical Structures in Computer Science II', 1),
(8, 'Data Structures', 1),
(9, 'Logic Design and Digital Computer Circuits', 1),
(10, 'Design and Implementation of Programming Languages', 1),
(11, 'File Processing and Database Systems', 1),
(12, 'Intro to Computer Organization and Machine Level Programming', 1),
(13, 'Numerical & Symbolic Computation', 1),
(14, 'Operating Systems', 1),
(15, 'Intro to Software Engineering', 1),
(16, 'Computer Architecture', 1),
(17, 'Data Communications and Networking', 1),
(18, 'Automata and Languages Theory', 1),
(19, 'Design and Analysis of Algorithms', 1),
(20, 'Undergraduate Seminar', 1),
(21, 'Undergraduate Thesis I', 1),
(22, 'Undergraduate Thesis II', 1),
(23, 'Intro to Computer Science', 1),
(24, 'Discrete Mathematical Structures in Computer Science', 1),
(25, 'Fundamentals of Programming', 1),
(26, 'Discrete Mathematical Structures in Computer Science II', 1),
(27, 'Data Structures', 1),
(28, 'Logic Design and Digital Computer Circuits', 1),
(29, 'Design and Implementation of Programming Languages', 1),
(30, 'File Processing and Database Systems', 1),
(31, 'Intro to Computer Organization and Machine Level Programming', 1),
(32, 'Numerical & Symbolic Computation', 1),
(33, 'Operating Systems', 1),
(34, 'Intro to Software Engineering', 1),
(35, 'Computer Architecture', 1),
(36, 'Data Communications and Networking', 1),
(37, 'Automata and Languages Theory', 1),
(38, 'Design and Analysis of Algorithms', 1),
(39, 'Undergraduate Seminar', 1),
(40, 'Undergraduate Thesis I', 1),
(41, 'Undergraduate Thesis II', 1),
(42, 'College Algebra & Trigonometry', 1),
(43, 'Calculus & Analytic Geometry', 1),
(44, 'Calculus & Analytic Geometry II', 1),
(45, 'General Physics I', 1),
(46, 'Elementary Statistics', 1),
(47, 'Calculus & Analytic Geometry III', 1),
(48, 'General Physics', 1),
(49, 'Statistical Methods', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `courseID` int(11) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `userName`, `email`, `password`, `courseID`, `role`) VALUES
(1, 'Nazakun', 'benedictnaza@gmail.com', '$2y$10$MgeubSQB3bpM3NvnZv2n6.gfuTsfM1VGrz6YCRgMw/GhHBwyFWC/6', 1, 'user'),
(2, 'NazakunML', 'benedictnazaml@gmail.com', '$2y$10$XTy.Bxi2oGBIM0JPmwuwaOe/o.sjHIGQy4MIKt37yOFd1mJ30fRky', 1, 'user'),
(3, 'Sailor Moon', 'sailormoon@gmail.com', '$2y$10$oRFhHWbicpj4onAvE19YO./w.qf5JvEyPkVeCx53maReY2lGP7JW6', 1, 'user'),

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseID`);

--
-- Indexes for table `reviewattendance`
--
ALTER TABLE `reviewattendance`
  ADD PRIMARY KEY (`reviewAttendanceID`),
  ADD UNIQUE KEY `userID` (`userID`,`reviewSessionID`),
  ADD KEY `reviewSessionID` (`reviewSessionID`);

--
-- Indexes for table `reviewsession`
--
ALTER TABLE `reviewsession`
  ADD PRIMARY KEY (`reviewSessionID`),
  ADD KEY `creatorUserID` (`creatorUserID`),
  ADD KEY `subjectID` (`subjectID`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subjectID`),
  ADD KEY `fk_course` (`courseID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `userName` (`userName`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `courseID` (`courseID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `courseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviewattendance`
--
ALTER TABLE `reviewattendance`
  MODIFY `reviewAttendanceID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviewsession`
--
ALTER TABLE `reviewsession`
  MODIFY `reviewSessionID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviewattendance`
--
ALTER TABLE `reviewattendance`
  ADD CONSTRAINT `reviewattendance_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviewattendance_ibfk_2` FOREIGN KEY (`reviewSessionID`) REFERENCES `reviewsession` (`reviewSessionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviewsession`
--
ALTER TABLE `reviewsession`
  ADD CONSTRAINT `reviewsession_ibfk_1` FOREIGN KEY (`creatorUserID`) REFERENCES `user` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviewsession_ibfk_2` FOREIGN KEY (`subjectID`) REFERENCES `subjects` (`subjectID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`),
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`) ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
