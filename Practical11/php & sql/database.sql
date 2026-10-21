USE `studenthub`;

ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `name` VARCHAR(100) DEFAULT NULL AFTER `id`;
ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `course` VARCHAR(100) DEFAULT NULL AFTER `mobile`;

UPDATE `students` SET `name` = 'Student User', `course` = 'Computer Engineering' WHERE `id` = 1 AND `name` IS NULL;
UPDATE `students` SET `name` = 'John Doe', `course` = 'Computer Engineering' WHERE `id` = 2 AND `name` IS NULL;
UPDATE `students` SET `name` = 'Jane Smith', `course` = 'IT Engineering' WHERE `id` = 3 AND `name` IS NULL;

INSERT INTO `students` (`name`, `email`, `mobile`, `course`, `password_hash`) VALUES
('Aarav Patel',     'aarav.patel@example.com',     '9876501001', 'Computer Engineering',   '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Diya Sharma',     'diya.sharma@example.com',     '9876501002', 'IT Engineering',         '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Vihaan Mehta',    'vihaan.mehta@example.com',    '9876501003', 'Mechanical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Ananya Gupta',    'ananya.gupta@example.com',    '9876501004', 'Civil Engineering',       '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Arjun Reddy',     'arjun.reddy@example.com',     '9876501005', 'Computer Engineering',   '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Ishita Nair',     'ishita.nair@example.com',     '9876501006', 'Electrical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Rohan Joshi',     'rohan.joshi@example.com',     '9876501007', 'IT Engineering',         '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Kavya Desai',     'kavya.desai@example.com',     '9876501008', 'Computer Engineering',   '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Aditya Singh',    'aditya.singh@example.com',    '9876501009', 'Mechanical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Priya Kapoor',    'priya.kapoor@example.com',    '9876501010', 'Civil Engineering',       '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Sai Krishna',     'sai.krishna@example.com',     '9876501011', 'Electrical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Meera Iyer',      'meera.iyer@example.com',      '9876501012', 'IT Engineering',         '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Rahul Verma',     'rahul.verma@example.com',     '9876501013', 'Computer Engineering',   '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Sneha Kulkarni',  'sneha.kulkarni@example.com',  '9876501014', 'Mechanical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Vikram Rao',      'vikram.rao@example.com',      '9876501015', 'Civil Engineering',       '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Tanya Bhat',      'tanya.bhat@example.com',      '9876501016', 'Electrical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Nikhil Agarwal',  'nikhil.agarwal@example.com',  '9876501017', 'IT Engineering',         '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Riya Chauhan',    'riya.chauhan@example.com',    '9876501018', 'Computer Engineering',   '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Karan Malhotra',  'karan.malhotra@example.com',  '9876501019', 'Mechanical Engineering',  '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq'),
('Pooja Sethi',     'pooja.sethi@example.com',     '9876501020', 'Civil Engineering',       '$2y$10$uCUngVrOb2tQOqNAi2e/n.0SmF9bu2Qm1lKrtzaXjjtbg6fh31LKq')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `course` = VALUES(`course`);
