USE `studenthub`;

CREATE TABLE IF NOT EXISTS `audit_logs` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id`   INT NOT NULL,
    `action`     VARCHAR(100) NOT NULL,
    `details`    TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
