USE `studenthub`;

ALTER TABLE `students`
    ADD COLUMN IF NOT EXISTS `role` VARCHAR(10) NOT NULL DEFAULT 'student'
        COMMENT 'User role: student or admin';

ALTER TABLE `students`
    ADD COLUMN IF NOT EXISTS `remember_token` VARCHAR(64) DEFAULT NULL
        COMMENT 'SHA-256 hash of remember-me token; raw token lives only in the cookie';

ALTER TABLE `students`
    ADD COLUMN IF NOT EXISTS `token_expiry` DATETIME DEFAULT NULL
        COMMENT 'Expiry datetime for the remember-me token';

ALTER TABLE `students`
    ADD COLUMN IF NOT EXISTS `last_login` DATETIME DEFAULT NULL
        COMMENT 'Timestamp of the most recent successful login';

INSERT IGNORE INTO `students` (`email`, `mobile`, `password_hash`, `role`, `created_at`)
VALUES
    ('student10@test.com', '9000000001',
     '$2y$12$Q8H6x1mJfN7CKmOTz9WyFuVwZvX3aPsR5sLdKEbQnM4cGjpYrIfXy',
     'student', NOW()),

    ('admin10@test.com',   '9000000002',
     '$2y$12$Gy1BtVPqj5KlmNr4zXwEreOzCuFnQs7dE3aHMpb0cVjI6RkYuLwxa',
     'admin',   NOW());