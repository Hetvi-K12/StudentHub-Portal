USE `studenthub`;

ALTER TABLE `events`
    ADD COLUMN IF NOT EXISTS `venue` VARCHAR(255) DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `poster_path` VARCHAR(500) DEFAULT NULL;

INSERT IGNORE INTO `events` (`id`, `name`, `description`, `event_date`, `venue`, `poster_path`) VALUES
(1, 'Annual Tech Symposium 2026', 'A flagship technical conference with coding contests and tech talks.', '2026-11-15', 'Main Auditorium', NULL),
(2, 'CodeSprint Hackathon 2026', '24-hour inter-college hackathon to build innovative web and AI solutions.', '2026-11-20', 'Lab Block B', NULL),
(3, 'Cultural Fest – Rhythm 2026', 'Annual cultural celebration featuring live music, dance, and art exhibitions.', '2026-12-05', 'Open-Air Amphitheater', NULL),
(4, 'AI & Machine Learning Workshop', 'Hands-on practical workshop covering deep learning models and neural networks.', '2026-12-12', 'Seminar Hall 1', NULL);
