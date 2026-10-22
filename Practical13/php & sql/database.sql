USE `studenthub`;

CREATE TABLE IF NOT EXISTS `courses` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `name`        VARCHAR(255) NOT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `icon`        VARCHAR(100) DEFAULT 'fas fa-book'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `courses` (`id`, `name`, `description`, `icon`) VALUES
(1, 'Web Development',    'HTML, CSS & JavaScript',  'fas fa-code'),
(2, 'Java Programming',   'Core Java Concepts',       'fas fa-laptop-code'),
(3, 'Database',           'SQL & MySQL',              'fas fa-database'),
(4, 'Computer Network',   'Networking Basics',        'fas fa-network-wired'),
(5, 'Digital Electronics','Logic Circuits',           'fas fa-microchip'),
(6, 'Cyber Security',     'Security Fundamentals',    'fas fa-shield-alt'),
(7, 'maths',              'mathematics',              'fas fa-book');
