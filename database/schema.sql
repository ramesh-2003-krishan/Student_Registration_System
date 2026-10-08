CREATE DATABASE IF NOT EXISTS `student_registration_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `student_registration_db`;

DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_code` VARCHAR(20) NOT NULL UNIQUE,
  `course_name` VARCHAR(100) NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `students` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `index_number` VARCHAR(30) NOT NULL UNIQUE,
  `first_name` VARCHAR(50) NOT NULL,
  `last_name` VARCHAR(50) NOT NULL,
  `dob` DATE NOT NULL,
  `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `phone` VARCHAR(20) NOT NULL,
  `address` TEXT NOT NULL,
  `nic` VARCHAR(20) DEFAULT NULL,
  `guardian_name` VARCHAR(100) NOT NULL,
  `guardian_phone` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `enrollments` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `enrolled_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_student_course` (`student_id`, `course_id`),
  CONSTRAINT `fk_enrollment_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`username`, `password_hash`, `full_name`) VALUES
('admin', '$2y$10$K9CD2OCkSGGCfhF43zszOOkZSVhJgn/xxdbZucxMKRu8.Cw3jsbtY6', 'System Administrator');

INSERT INTO `courses` (`course_code`, `course_name`, `duration`, `description`, `status`) VALUES
('CS101', 'Introduction to Computer Science', '1 Year', 'Foundational concepts of computer science.', 'active'),
('SE201', 'Software Engineering Principles', '6 Months', 'Agile principles and software development.', 'active'),
('WD150', 'Web Programming', '6 Months', 'HTML, CSS, JavaScript, PHP, and MySQL.', 'active');

INSERT INTO `students` (`index_number`, `first_name`, `last_name`, `dob`, `gender`, `email`, `phone`, `address`, `nic`, `guardian_name`, `guardian_phone`, `created_at`) VALUES
('HS/2022/0328', 'Kasun', 'Perera', '2001-05-14', 'Male', 'kasun@example.com', '0771234567', 'No. 45, Galle Road, Colombo 03', '200113401234', 'Sunil Perera', '0719876543', NOW()),
('HS/2022/0329', 'Nimali', 'Fernando', '2002-08-22', 'Female', 'nimali@example.com', '0712345678', '12/A, Kandy Road, Gampaha', '200268904567', 'Kamal Fernando', '0778765432', NOW()),
('HS/2022/0330', 'Dilshan', 'Jayasinghe', '2000-11-03', 'Male', 'dilshan@example.com', '0753456789', '88, Main Street, Negombo', '200030809876', 'Saman Jayasinghe', '0701122334', NOW()),
('HS/2022/0331', 'Anuki', 'Silva', '2003-02-18', 'Female', 'anuki@example.com', '0784567890', 'No. 102, Highlevel Road, Nugegoda', '200355108899', 'Rohan Silva', '0723344556', NOW()),
('HS/2022/0332', 'Tharindu', 'Wickramasinghe', '1999-07-30', 'Male', 'tharindu@example.com', '0765678901', '45/2, Temple Road, Kurunegala', '199921104433', 'Wimal Wickramasinghe', '0764455667', NOW());

INSERT INTO `enrollments` (`student_id`, `course_id`) VALUES
(1, 1),
(2, 3),
(3, 1),
(4, 2),
(5, 3);
