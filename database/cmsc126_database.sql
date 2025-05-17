-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 01:00 PM
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

--
-- Dumping data for table `reviewsession`
--

INSERT INTO `reviewsession` (`reviewSessionID`, `creatorUserID`, `subjectID`, `reviewTitle`, `reviewDate`, `reviewStartTime`, `reviewEndTime`, `reviewLocation`, `reviewDescription`, `reviewTopic`, `reviewStatus`) VALUES
(34, 1, 9, 'Finals Review', '2025-05-23', '13:00:00', '15:00:00', 'CSM Room 204', 'Everything after the Midterms till the end', 'Combinational Logic Circuits, Synchronous Sequential Circuits', 'scheduled'),
(35, 1, 7, 'Finals Review', '2025-05-20', '10:00:00', '12:00:00', 'CSM Room 222', 'Everything from the last LE till the end', 'Definition of Graphs and Graph Types', 'scheduled'),
(36, 1, 46, 'Finals Review', '2025-05-27', '17:00:00', '19:00:00', 'CSM Room 222', 'Everything from the last LE till the end', 'Correlation and Regression Analysis', 'scheduled'),
(37, 1, 4, 'Finals Review', '2025-05-21', '08:30:00', '12:00:00', 'CSM Room 206', 'Glory to our GOAT Prof Vic Calag, our Chancy', 'Introduction to Computer Science', 'scheduled'),
(38, 1, 8, 'Midterms Review', '2025-05-25', '18:00:00', '19:00:00', 'CSM Room 227', 'Just need a quick study for this...', 'Introduction to data structures', 'scheduled');

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
(4, 'group1140', 'durinasbestos@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$cU1MZk5SamttckNhMGo5Uw$lY1wO6zmsAImBl9+QnR/U6VBnO1+8KcG9TThz7yqRGY', 1, 'user'),
(6, 'admin', 'admin@example.com', '$2y$10$fxrSOvExgIsZT3POBZBHEeAaqCB5BjM8o6Ou//2Rq8ouzO7LUjASG', 1, 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseID`);

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
-- AUTO_INCREMENT for table `reviewsession`
--
ALTER TABLE `reviewsession`
  MODIFY `reviewSessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

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
