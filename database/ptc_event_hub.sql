CREATE DATABASE IF NOT EXISTS `ptc_event_hub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ptc_event_hub`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `student_id` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `year_section` VARCHAR(50) DEFAULT NULL,
  `role` ENUM('student', 'admin') DEFAULT 'student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Events Table
CREATE TABLE IF NOT EXISTS `events` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(150) NOT NULL,
  `description` TEXT NOT NULL,
  `category` ENUM('Online Games Tournament', 'Seminars', 'Foundation Day', 'Sportfest') NOT NULL,
  `event_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `venue` VARCHAR(150) NOT NULL,
  `capacity` INT NOT NULL,
  `rules` TEXT DEFAULT NULL,
  `created_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Registrations Table
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `event_id` INT NOT NULL,
  `ticket_code` VARCHAR(50) NOT NULL UNIQUE,
  `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_event` (`user_id`, `event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Pre-seeded Admin Accounts (Default Password for all: Admin2026!)
INSERT INTO `users` (`full_name`, `student_id`, `email`, `password_hash`, `year_section`, `role`) VALUES
('Dean Super Admin', 'ADMIN-001', 'admin.dean@ptc.edu.ph', '$2y$10$7R3v/9X4m7yQf3J1s6P/S.N1.f9vW/6O5B8.g/O.x9lQ.Qx/O3k/C', 'Faculty Admin', 'admin'),
('Prof. Alex Rivera (IT & Esports Lead)', 'ADMIN-002', 'admin.pcc@ptc.edu.ph', '$2y$10$7R3v/9X4m7yQf3J1s6P/S.N1.f9vW/6O5B8.g/O.x9lQ.Qx/O3k/C', 'Faculty Admin', 'admin'),
('Coach Marcus Santos (Sports Director)', 'ADMIN-003', 'admin.sports@ptc.edu.ph', '$2y$10$7R3v/9X4m7yQf3J1s6P/S.N1.f9vW/6O5B8.g/O.x9lQ.Qx/O3k/C', 'Faculty Admin', 'admin'),
('Samantha Vance (Student Council Lead)', 'ADMIN-004', 'admin.council@ptc.edu.ph', '$2y$10$7R3v/9X4m7yQf3J1s6P/S.N1.f9vW/6O5B8.g/O.x9lQ.Qx/O3k/C', 'Faculty Admin', 'admin');

-- 5. Initial Sample Events
INSERT INTO `events` (`title`, `description`, `category`, `event_date`, `start_time`, `end_time`, `venue`, `capacity`, `rules`, `created_by`) VALUES
('PCC Mobile Legends: Bang Bang Tournament', 'Official PCC MLBB Championship! 5v5 Tournament draft pick mode with diamond prizes, trophies, and campus rank honors.', 'Online Games Tournament', '2026-11-15', '10:00:00', '18:00:00', 'PTC Esports Arena & Live Stream', 64, 'Must be currently enrolled PTC student. Team captains must register complete 5-man roster.', 2),
('PCC Valorant Tactical Showdown', '5v5 Competitive Valorant Tournament. Custom lobby standard tournament rules with live caster commentary.', 'Online Games Tournament', '2026-11-18', '13:00:00', '19:00:00', 'Computer Lab 4 & Discord Hall', 32, 'Riot accounts must be registered in good standing. No toxicity or cheats permitted.', 2),
('Cloud Computing & AWS Architecture Summit', 'Learn modern Cloud DevOps, AWS Serverless architecture, and scalable software practices from industry tech leads.', 'Seminars', '2026-11-10', '09:00:00', '12:00:00', 'AVR Main Auditorium', 120, 'Open to all IT, CS, and Engineering students. Certificate of participation awarded.', 1),
('Cyberbullying Prevention & Digital Safety Workshop', 'Interactive session on digital wellness, cybersecurity awareness, mental health coping mechanisms, and safe social media practices.', 'Seminars', '2026-11-12', '13:30:00', '16:00:00', 'PTC Multi-Purpose Hall', 150, 'Attendance counted for NSTP/GE credit requirements.', 1),
('Annual Foundation Day Grand Cultural Showcase', 'Celebrating PTC Foundation Day with musical performances, dance battles, food bazaar, and fireworks.', 'Foundation Day', '2026-12-01', '17:00:00', '22:00:00', 'PTC Grand Plaza', 500, 'Valid PTC Student ID required at gate entry.', 4),
('Sportsfest 2026: Intramural Basketball Cup', 'Inter-department 5-on-5 basketball tournament. Support your college department team!', 'Sportfest', '2026-11-25', '08:00:00', '17:00:00', 'PTC Gymnasium Court 1', 200, 'Players must wear official college jersey uniforms.', 3);