-- =======================================================================
-- STUDENTHUB PORTAL - PRACTICAL 8 MYSQL DATABASE SCHEMA & SEED DATA
-- =======================================================================

CREATE DATABASE IF NOT EXISTS `studenthub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub`;

-- Drop existing tables to ensure clean initialization
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;

-- 1. STUDENTS TABLE (User Accounts)
CREATE TABLE `students` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `mobile` VARCHAR(10) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. EVENTS TABLE (Campus Events)
CREATE TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `event_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. REGISTRATIONS TABLE (Event Registrations - Junction Table)
CREATE TABLE `registrations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_registrations_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_registrations_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `unique_student_event` UNIQUE (`student_id`, `event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =======================================================================
-- SEED DATA
-- Note: Passwords below are hashed for 'password123' using PHP password_hash()
-- Hash: $2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq
-- =======================================================================

INSERT INTO `students` (`id`, `email`, `mobile`, `password_hash`, `created_at`) VALUES
(1, 'student@example.com', '9876543210', '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq', NOW()),
(2, 'john.doe@example.com', '8765432109', '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq', NOW()),
(3, 'jane.smith@example.com', '7654321098', '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq', NOW());

INSERT INTO `events` (`id`, `name`, `description`, `event_date`, `created_at`) VALUES
(1, 'Annual Tech Symposium 2026', 'A flagship technical conference with coding contests, paper presentations, and tech talks.', '2026-11-15', NOW()),
(2, 'CodeSprint Hackathon 2026', '24-hour inter-college hackathon to build innovative web and AI solutions.', '2026-11-20', NOW()),
(3, 'Cultural Fest - Rhythm 2026', 'Annual cultural celebration featuring live music, dance performances, and art exhibitions.', '2026-12-05', NOW()),
(4, 'AI & Machine Learning Workshop', 'Hands-on practical workshop covering deep learning models and neural networks.', '2026-12-12', NOW());

INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `registered_at`) VALUES
(1, 1, 1, NOW()),
(2, 1, 2, NOW()),
(3, 2, 1, NOW());
