-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2025 at 07:34 PM
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
-- Database: `languagelearn`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `full_name`, `password_hash`, `email`, `role`, `created_at`, `status`, `last_login`, `photo`, `signature`) VALUES
(1, 'misaac', 'Isaac Mukonyezi', '$2y$10$YHzSNx/DOaQFXG3SYtqXRukl/wP2XXM5V4uhx9ROOAM3WGQZwp9ea', 'isaac@system.com', 'admin', '2025-04-12 11:33:41', 'active', '2025-04-13 09:28:24', '68012525e01e5_my pic.jpg', '680125a6ba226_my_sig-removebg-preview.png');

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `action_type` enum('create','update','delete','system','other') DEFAULT 'other',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`log_id`, `user_id`, `action`, `action_type`, `ip_address`, `user_agent`, `created_at`, `metadata`) VALUES
(1, 1, 'Approved course: Learn Luganda 101', 'update', NULL, NULL, '2025-05-21 21:05:53', NULL),
(2, 1, 'Deleted user #42', 'delete', NULL, NULL, '2025-05-21 21:05:53', '{\"user_email\":\"olduser@example.com\"}');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'e.g., Beginner Luganda',
  `icon` varchar(50) DEFAULT NULL COMMENT 'FontAwesome class'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `certificate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `issued_at` datetime DEFAULT current_timestamp(),
  `download_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `language` varchar(50) NOT NULL COMMENT 'e.g., Luganda',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `thumbnail_url` varchar(255) DEFAULT NULL,
  `status` enum('pending','published','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_free` tinyint(1) DEFAULT 0,
  `level` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL COMMENT 'Admin user ID who approved',
  `approved_at` datetime DEFAULT NULL COMMENT 'When course was approved',
  `rejection_reason` text DEFAULT NULL COMMENT 'Reason for rejection if applicable',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1=featured, 0=normal',
  `duration_minutes` int(11) DEFAULT 0,
  `duration_hours` int(11) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `lessons` varchar(255) DEFAULT NULL,
  `course_materials` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `tutor_id`, `title`, `description`, `language`, `price`, `thumbnail_url`, `status`, `created_at`, `updated_at`, `is_free`, `level`, `category`, `approved_by`, `approved_at`, `rejection_reason`, `is_featured`, `duration_minutes`, `duration_hours`, `file_path`, `lessons`, `course_materials`) VALUES
(1, 5, 'Luganda 101 for Beginners', 'introductory ', 'Luganda', 10000.00, '/uploads/course_thumbs/682f68beac82e_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-22 21:11:10', '2025-05-27 21:44:14', 0, 'beginner', NULL, 1, '2025-05-27 21:44:14', NULL, 0, 0, 0, NULL, NULL, NULL),
(2, 5, 'Luganda 101 for Beginners', 'introductory ', 'Luganda', 10000.00, '/uploads/course_thumbs/682f691954c0d_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-22 21:12:41', '2025-05-25 05:17:37', 0, 'beginner', NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL),
(3, 5, 'Luganda 101 for Beginners', 'introductory ', 'Luganda', 10000.00, '/uploads/course_thumbs/682f6ae1b1829_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-22 21:20:17', '2025-05-25 05:18:10', 0, 'beginner', NULL, 1, '2025-05-25 05:18:10', NULL, 0, 0, 0, NULL, NULL, NULL),
(4, 5, 'Luganda 101 for Beginners', 'introductory ', 'Luganda', 10000.00, '/uploads/course_thumbs/682f6b133f81f_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-22 21:21:07', '2025-05-27 21:44:08', 0, 'beginner', NULL, 1, '2025-05-27 21:44:08', NULL, 0, 0, 0, NULL, NULL, NULL),
(5, 7, 'Runyoro 101', 'This course is designed for absolute beginners who want to start speaking Runyoro confidently. Through interactive lessons, real-life dialogues, and cultural insights, learners will build a strong foundation in common Runyoro phrases, greetings, numbers, and everyday communication.', 'Runyoro', 0.00, '/uploads/course_thumbs/683286842bf76_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-25 05:55:00', '2025-05-25 05:56:05', 1, 'beginner', NULL, 1, '2025-05-25 05:56:00', NULL, 1, 0, 0, NULL, NULL, NULL),
(7, 9, 'Understanding Runyoro language for Foreigners', 'This course is designed for absolute beginners who want to learn the Runyoro language, spoken in Western Uganda. By the end of the course, learners will be able to confidently greet, introduce themselves, and hold basic conversations in Runyoro. The course emphasizes pronunciation, vocabulary, and cultural context.', 'Runyoro', 15000.00, '/uploads/course_thumbs/683342ed32a36_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-25 19:18:53', '2025-05-25 19:21:03', 0, 'beginner', NULL, 1, '2025-05-25 19:20:59', NULL, 1, 90, 0, NULL, NULL, NULL),
(14, 9, 'Master Kiswahili in 3 Hours', 'description of Master Kiswahili in 3 Hours', 'Luganda', 200000.00, '/uploads/course_thumbs/6836076c5d952_download.jpg', 'published', '2025-05-27 21:41:48', '2025-05-27 21:44:11', 0, 'beginner', NULL, 1, '2025-05-27 21:44:11', NULL, 0, 90, 0, NULL, NULL, NULL),
(15, 9, 'Introductory Lessons for Runyoro', 'introduction testing', 'Runyoro', 30000.00, '/uploads/course_thumbs/68377a703bf46_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-29 00:04:48', '2025-05-29 00:05:35', 0, 'beginner', NULL, 1, '2025-05-29 00:05:35', NULL, 0, 150, 0, NULL, NULL, NULL),
(16, 9, 'Complete Luganda for Beginners', 'testing', 'English', 1000000.00, '/uploads/course_thumbs/683780ce0958f_luganda.jpg', 'published', '2025-05-29 00:31:58', '2025-05-29 00:33:05', 0, 'beginner', NULL, 1, '2025-05-29 00:33:05', NULL, 0, 150, 0, NULL, NULL, NULL),
(17, 9, 'Okusoma Oluganda - Basic Luganda for Beginners', 'Welcome to \"Okusoma Oluganda\"! This course will teach you:  \r\n- Greetings and basic conversations  \r\n- Numbers and telling time  \r\n- Family and relationships  \r\n- Common phrases for daily life  \r\n\r\nBy the end, you\'ll be able to:  \r\n- Introduce yourself in Luganda  \r\n- Ask simple questions  \r\n- Understand basic responses  ', 'Luganda', 0.00, '/uploads/course_thumbs/683789885ee3e_luganda.jpg', 'published', '2025-05-29 01:09:12', '2025-05-29 01:10:02', 1, 'beginner', NULL, 1, '2025-05-29 01:09:59', NULL, 1, 150, 0, NULL, NULL, NULL),
(18, 9, 'Okusoma Orunyoro - Basic Runyoro for Beginners', 'Basic Runyoro for Beginners', 'Runyoro', 200000.00, '/uploads/course_thumbs/68396501dd34d_freepik__a-course-thumbnail-design-for-learning-the-runyoro__72363.png', 'published', '2025-05-30 10:57:53', '2025-05-30 10:58:31', 0, 'beginner', NULL, 1, '2025-05-30 10:58:27', NULL, 1, 150, 0, NULL, NULL, NULL),
(27, 9, 'Beginner’s Guide to Speaking Runyoro Native Language', 'This course introduces learners to the basics of Runyoro, a Bantu language spoken in Western Uganda. The course is designed for absolute beginners who want to start speaking and understanding common everyday Runyoro words and expressions. You will learn greetings, numbers, pronouns, common verbs, sentence structures, and basic conversations.', 'Runyoro', 370000.00, 'uploads/course_thumbs/thumb_6842fa59e06576.20549814.png', 'published', '2025-06-06 17:25:29', '2025-06-06 17:26:17', 0, 'beginner', 'Culture', 1, '2025-06-06 17:26:14', NULL, 1, 1, 0, NULL, NULL, NULL),
(29, 9, 'Beginner’s Guide to Speaking Kiswahili (Swahili)', 'This course is designed for complete beginners who want to speak and understand Kiswahili (Swahili) — one of the most widely spoken languages in East Africa. It covers daily conversations, pronunciation, grammar basics, and cultural context.', 'Swahili', 0.00, 'uploads/course_thumbs/thumb_6843231203cee6.62562331.png', 'published', '2025-06-06 20:19:14', '2025-06-06 20:20:14', 1, 'beginner', 'business', 1, '2025-06-06 20:20:10', NULL, 1, 78, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_materials`
--

CREATE TABLE `course_materials` (
  `material_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('pdf','video','audio','image') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_materials`
--

INSERT INTO `course_materials` (`material_id`, `course_id`, `file_name`, `file_path`, `file_type`, `uploaded_at`, `created_at`, `updated_at`, `title`) VALUES
(1, 7, 'kumpi_wano_terra_watts_official_video_4k_aac_63319.m4a', '/uploads/course_materials/683342ed3646b_kumpi_wano_terra_watts_official_video_4k_aac_63319.m4a', 'audio', '2025-05-25 16:18:53', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(2, 7, 'Redesign work 1.pdf', '/uploads/course_materials/683342ed3db21_Redesign work 1.pdf', 'pdf', '2025-05-25 16:18:53', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(3, 14, 'AUD-20250525-WA0019.m4a', '/uploads/course_materials/6836076c6c6f0_AUD-20250525-WA0019.m4a', 'audio', '2025-05-27 18:41:48', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(4, 14, 'AUD-20250525-WA0018.m4a', '/uploads/course_materials/6836076c6db6d_AUD-20250525-WA0018.m4a', 'audio', '2025-05-27 18:41:48', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(5, 15, 'AUD-20250525-WA0019.m4a', '/uploads/course_materials/68377a7048813_AUD-20250525-WA0019.m4a', 'audio', '2025-05-28 21:04:48', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(6, 15, 'AUD-20250525-WA0018.m4a', '/uploads/course_materials/68377a704ad7c_AUD-20250525-WA0018.m4a', 'audio', '2025-05-28 21:04:48', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(7, 16, 'White and Blue Professional Modern Technology Pitch Deck Presentation.pptx', '/uploads/course_materials/683780ce28fc2_White and Blue Professional Modern Technology Pitch Deck Presentation.pptx', '', '2025-05-28 21:31:58', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(8, 17, 'Redesign work 1.pdf', '/uploads/course_materials/6837898865c68_Redesign work 1.pdf', 'pdf', '2025-05-28 22:09:12', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(9, 17, 'SSEMWEZI HERMAN 2023DCSDAY1083G.pdf', '/uploads/course_materials/683789886647f_SSEMWEZI HERMAN 2023DCSDAY1083G.pdf', 'pdf', '2025-05-28 22:09:12', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(10, 18, 'Masinde Doreen N coursework 1.pdf', '/uploads/course_materials/6839650291f0e_Masinde Doreen N coursework 1.pdf', 'pdf', '2025-05-30 07:57:54', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(11, 18, 'AUD-20250525-WA0019.m4a', '/uploads/course_materials/6839650293135_AUD-20250525-WA0019.m4a', 'audio', '2025-05-30 07:57:54', '2025-06-06 17:14:37', '2025-06-06 17:14:37', NULL),
(12, 27, '', 'uploads/course_materials/material_6842fa59e151c2.16560281.pdf', 'pdf', '2025-06-06 14:25:29', '2025-06-06 17:25:29', '2025-06-06 17:25:29', 'Introduction slides'),
(13, 29, '', 'uploads/course_materials/material_68432312148dc4.12830715.pdf', 'pdf', '2025-06-06 17:19:14', '2025-06-06 20:19:14', '2025-06-06 20:19:14', 'Introduction slides');

-- --------------------------------------------------------

--
-- Table structure for table `course_sections`
--

CREATE TABLE `course_sections` (
  `section_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_sections`
--

INSERT INTO `course_sections` (`section_id`, `course_id`, `title`, `order`) VALUES
(2, 7, 'introduction', 1),
(9, 14, 'introduction', 1),
(10, 15, 'introduction', 1),
(11, 16, 'testing', 1),
(12, 16, 'dabbing', 2),
(13, 17, 'Common Phrases', 1),
(14, 17, 'greetings', 2),
(15, 18, 'intro', 1),
(16, 18, 'good bye ', 2),
(25, 27, 'into test', 1),
(26, 27, 'Greetings and Common Expressions', 2),
(27, 27, 'Pronouns and Simple Sentences', 3),
(28, 27, 'Numbers and Counting', 4),
(31, 29, 'Introduction to Kiswahili', 1),
(32, 29, 'Everyday Conversation', 2),
(33, 29, 'Grammar and Vocabulary', 3),
(34, 29, 'Practical Communication', 4);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `enrolled_at` datetime DEFAULT current_timestamp(),
  `expiry_date` datetime DEFAULT NULL COMMENT 'For subscription models'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `user_id`, `course_id`, `payment_id`, `enrolled_at`, `expiry_date`) VALUES
(1, 1, 5, NULL, '2025-05-25 06:07:08', NULL),
(2, 6, 5, NULL, '2025-05-25 06:25:06', NULL),
(3, 8, 5, NULL, '2025-05-25 07:24:43', NULL),
(4, 9, 5, NULL, '2025-05-25 18:57:21', NULL),
(5, 10, 5, NULL, '2025-05-26 03:23:36', NULL),
(6, 11, 5, NULL, '2025-05-28 23:06:23', NULL),
(7, 10, 1, 13, '2025-05-28 23:56:49', NULL),
(8, 10, 15, 14, '2025-05-29 00:06:53', NULL),
(9, 10, 16, 15, '2025-05-29 00:37:51', NULL),
(10, 10, 17, NULL, '2025-05-29 01:12:11', NULL),
(11, 12, 17, NULL, '2025-05-30 08:27:34', NULL),
(12, 12, 2, 16, '2025-05-30 09:00:15', NULL),
(13, 10, 18, 17, '2025-05-30 11:00:10', NULL),
(14, 1, 18, 18, '2025-05-30 11:56:05', NULL),
(15, 13, 18, 19, '2025-05-30 17:14:01', NULL),
(16, 14, 18, 20, '2025-06-03 14:08:57', NULL),
(17, 10, 27, 21, '2025-06-06 17:31:32', NULL),
(18, 10, 29, NULL, '2025-06-06 20:24:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'In seconds',
  `notes_url` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL COMMENT 'Sequence in course',
  `duration_minutes` int(11) DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `lesson_type` varchar(50) DEFAULT NULL,
  `content` varchar(50) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `section_id`, `course_id`, `title`, `video_url`, `duration`, `notes_url`, `order`, `duration_minutes`, `duration_hours`, `lesson_type`, `content`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 9, 14, 'Greetings', '', NULL, NULL, 1, NULL, NULL, 'audio', 'File would be uploaded here', NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(2, 9, 14, 'Speaking basic words', '', NULL, NULL, 2, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(3, 10, 15, 'Say hello', '', NULL, NULL, 1, NULL, NULL, 'audio', 'File would be uploaded here', NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(4, 10, 15, 'hi test', '', NULL, NULL, 2, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(5, 13, 17, 'luganda alphabets', '', NULL, NULL, 1, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(6, 13, 17, 'Emergency phrases', '', NULL, NULL, 2, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(7, 14, 17, 'greetings', '', NULL, NULL, 1, NULL, NULL, 'audio', 'File would be uploaded here', NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(8, 15, 18, 'okwanjura', '', NULL, NULL, 1, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(9, 15, 18, 'check this video if it plays', '', NULL, NULL, 2, NULL, NULL, 'video', 'File would be uploaded here', NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(10, 16, 18, 'good bye', '', NULL, NULL, 1, NULL, NULL, 'text', NULL, NULL, '2025-06-06 17:13:28', '2025-06-06 17:13:28'),
(12, 25, 27, 'test text', NULL, NULL, NULL, 1, 1, NULL, 'text', 'This course introduces learners to the basics of R', NULL, '2025-06-06 17:25:29', '2025-06-06 17:25:29'),
(13, 25, 27, 'Introduction to the Runyoro Language', NULL, NULL, NULL, 2, NULL, NULL, 'pdf_file', NULL, 'uploads/course_materials/lessonfile_6842ffc00e3a18.18164553.pdf', '2025-06-06 17:48:32', '2025-06-06 17:48:32'),
(14, 26, 27, 'Basic Greetings in Runyoro', 'https://youtu.be/U8vnIUfLs6I?si=bRqnMN6MPGzP_ha1', NULL, NULL, 1, NULL, NULL, 'video_url', NULL, NULL, '2025-06-06 17:51:39', '2025-06-06 17:51:39'),
(15, 27, 27, 'Personal Pronouns', NULL, NULL, NULL, 1, NULL, NULL, 'audio_file', NULL, 'uploads/course_materials/lessonfile_684300c59e1718.96776457.m4a', '2025-06-06 17:52:53', '2025-06-06 17:52:53'),
(18, 31, 29, 'What is Kiswahili? (History &amp; Regions Spoken)', NULL, NULL, NULL, 1, 2, NULL, 'text', 'Kiswahili, also known as Swahili, is a Bantu langu', NULL, '2025-06-06 20:19:14', '2025-06-06 20:19:14'),
(19, 32, 29, 'Shopping &amp; Bargaining Vocabulary', NULL, NULL, NULL, 1, 4, NULL, 'audio_file', NULL, 'uploads/course_materials/lesson_684323120a1104.53421079.m4a', '2025-06-06 20:19:14', '2025-06-06 20:19:14'),
(20, 33, 29, 'Verb Conjugation – Present, Past, Future', NULL, NULL, NULL, 1, 38, NULL, 'pdf_file', NULL, 'uploads/course_materials/lesson_684323120b1cc8.01475224.pdf', '2025-06-06 20:19:14', '2025-06-06 20:19:14'),
(21, 34, 29, 'alking About Family and Hobbies', 'https://youtu.be/_gj821unoOI?si=a4SrAYmvc36PFrw5', NULL, NULL, 1, 34, NULL, 'video_url', NULL, NULL, '2025-06-06 20:19:14', '2025-06-06 20:19:14');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_accessed` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_progress`
--

INSERT INTO `lesson_progress` (`progress_id`, `user_id`, `lesson_id`, `started_at`, `last_accessed`, `completed_at`) VALUES
(1, 10, 1, '2025-05-27 21:46:19', '2025-05-27 21:53:59', NULL),
(3, 10, 2, '2025-05-27 21:46:50', NULL, NULL),
(7, 10, 5, '2025-05-29 01:13:38', '2025-05-29 19:47:19', NULL),
(11, 10, 7, '2025-05-29 01:45:01', NULL, NULL),
(20, 12, 5, '2025-05-30 08:27:54', '2025-05-30 08:58:09', NULL),
(22, 10, 10, '2025-05-30 11:00:19', '2025-05-30 11:00:36', NULL),
(24, 1, 9, '2025-05-30 11:56:24', NULL, NULL),
(25, 1, 8, '2025-05-30 12:07:16', NULL, NULL),
(26, 13, 9, '2025-05-30 17:14:07', NULL, NULL),
(27, 14, 8, '2025-06-03 14:09:10', '2025-06-03 14:09:56', NULL),
(35, 10, 12, '2025-06-06 17:31:40', '2025-06-06 17:53:53', NULL),
(40, 10, 15, '2025-06-06 17:54:17', '2025-06-06 19:46:02', NULL),
(42, 10, 14, '2025-06-06 19:05:05', '2025-06-06 19:45:52', NULL),
(45, 10, 13, '2025-06-06 19:06:36', NULL, NULL),
(58, 10, 18, '2025-06-06 20:24:33', NULL, NULL),
(59, 10, 19, '2025-06-06 20:24:51', NULL, NULL),
(60, 10, 20, '2025-06-06 20:25:04', NULL, NULL),
(61, 10, 21, '2025-06-06 20:25:28', '2025-06-06 20:26:05', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `loan_number` varchar(20) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `application_date` date DEFAULT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `monthly_repayment` decimal(12,2) NOT NULL,
  `total_repayment` decimal(12,2) NOT NULL,
  `status` enum('pending','approved','rejected','completed') DEFAULT 'pending',
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `member_id`, `loan_number`, `amount`, `application_date`, `interest_rate`, `term_months`, `purpose`, `monthly_repayment`, `total_repayment`, `status`, `processed_by`, `processed_at`, `updated_by`, `updated_at`, `created_by`, `created_at`, `approved_at`, `approved_by`) VALUES
(1, 3, 'LN-20250413-0816', 200000.00, NULL, 10.00, 6, 'Marriage', 35000.00, 210000.00, 'approved', NULL, NULL, NULL, NULL, 1, '2025-04-13 09:39:58', NULL, NULL),
(3, 6, 'LN-20250413-0412', 2000000.00, NULL, 6.00, 24, 'School fees', 93333.33, 2240000.00, 'approved', NULL, '2025-04-26 02:49:06', NULL, NULL, 1, '2025-04-13 15:34:33', NULL, NULL),
(4, 15, 'LN-20250413-0601', 10000.00, NULL, 5.00, 12, '', 875.00, 10500.00, 'approved', NULL, NULL, NULL, NULL, 1, '2025-04-13 18:52:08', NULL, NULL),
(5, 15, 'LN-20250413-3743', 10000.00, NULL, 5.00, 12, '', 875.00, 10500.00, 'pending', NULL, NULL, NULL, NULL, 1, '2025-04-13 18:52:33', NULL, NULL),
(6, 4, 'LN-20250417-6452', 50000.00, '2025-04-26', 10.00, 12, 'School fees', 4583.33, 55000.00, 'approved', NULL, '2025-04-26 04:02:07', 1, '2025-04-26 03:22:34', 1, '2025-04-17 17:03:20', NULL, NULL),
(9, 13, 'LN-20250426-9242', 50000.00, NULL, 10.00, 6, 'School fees', 8750.00, 52500.00, 'approved', NULL, '2025-04-26 02:50:54', NULL, NULL, 1, '2025-04-25 23:50:43', NULL, NULL),
(10, 23, 'LN-20250427-9024', 10000.00, NULL, 10.00, 6, 'd', 1750.00, 10500.00, 'approved', NULL, '2025-04-27 12:09:16', NULL, NULL, 1, '2025-04-27 09:08:34', NULL, NULL),
(11, 6, 'LN-20250430-6282', 50000.00, NULL, 10.00, 6, 'School fees', 8750.00, 52500.00, 'approved', NULL, '2025-04-30 06:34:37', NULL, NULL, 1, '2025-04-30 03:32:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `loan_repayments`
--

CREATE TABLE `loan_repayments` (
  `id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `amount_paid` decimal(12,2) DEFAULT 0.00,
  `payment_date` datetime DEFAULT NULL,
  `status` enum('pending','partial','paid','late') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loan_repayments`
--

INSERT INTO `loan_repayments` (`id`, `loan_id`, `due_date`, `amount`, `amount_paid`, `payment_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-05-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:36:05'),
(2, 1, '2025-06-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:36:16'),
(3, 1, '2025-07-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:35:37'),
(4, 1, '2025-08-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:33:14'),
(5, 1, '2025-09-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:35:52'),
(6, 1, '2025-10-13', 35000.00, 35000.00, '2025-04-26 00:00:00', 'paid', '2025-04-13 09:39:58', '2025-04-26 05:35:17'),
(13, 3, '2025-05-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(14, 3, '2025-06-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(15, 3, '2025-07-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(16, 3, '2025-08-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(17, 3, '2025-09-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(18, 3, '2025-10-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(19, 3, '2025-11-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(20, 3, '2025-12-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(21, 3, '2026-01-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(22, 3, '2026-02-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(23, 3, '2026-03-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(24, 3, '2026-04-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(25, 3, '2026-05-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(26, 3, '2026-06-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(27, 3, '2026-07-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(28, 3, '2026-08-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(29, 3, '2026-09-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(30, 3, '2026-10-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(31, 3, '2026-11-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(32, 3, '2026-12-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(33, 3, '2027-01-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(34, 3, '2027-02-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(35, 3, '2027-03-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(36, 3, '2027-04-13', 93333.33, 0.00, NULL, 'pending', '2025-04-13 15:34:33', '2025-04-13 15:34:33'),
(37, 4, '2025-05-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(38, 4, '2025-06-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(39, 4, '2025-07-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(40, 4, '2025-08-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(41, 4, '2025-09-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(42, 4, '2025-10-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(43, 4, '2025-11-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(44, 4, '2025-12-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:10', '2025-04-13 18:52:10'),
(45, 4, '2026-01-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:11', '2025-04-13 18:52:11'),
(46, 4, '2026-02-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:11', '2025-04-13 18:52:11'),
(47, 4, '2026-03-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:11', '2025-04-13 18:52:11'),
(48, 4, '2026-04-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:11', '2025-04-13 18:52:11'),
(49, 5, '2025-05-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(50, 5, '2025-06-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(51, 5, '2025-07-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(52, 5, '2025-08-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(53, 5, '2025-09-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(54, 5, '2025-10-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(55, 5, '2025-11-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(56, 5, '2025-12-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(57, 5, '2026-01-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(58, 5, '2026-02-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(59, 5, '2026-03-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(60, 5, '2026-04-13', 875.00, 0.00, NULL, 'pending', '2025-04-13 18:52:33', '2025-04-13 18:52:33'),
(61, 6, '2025-05-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(62, 6, '2025-06-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(63, 6, '2025-07-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(64, 6, '2025-08-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(65, 6, '2025-09-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(66, 6, '2025-10-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(67, 6, '2025-11-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(68, 6, '2025-12-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(69, 6, '2026-01-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(70, 6, '2026-02-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(71, 6, '2026-03-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(72, 6, '2026-04-17', 4583.33, 0.00, NULL, 'pending', '2025-04-17 17:03:20', '2025-04-17 17:03:20'),
(91, 3, '2025-05-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(92, 3, '2025-06-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(93, 3, '2025-07-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(94, 3, '2025-08-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(95, 3, '2025-09-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(96, 3, '2025-10-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(97, 3, '2025-11-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(98, 3, '2025-12-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(99, 3, '2026-01-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(100, 3, '2026-02-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(101, 3, '2026-03-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(102, 3, '2026-04-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(103, 3, '2026-05-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(104, 3, '2026-06-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(105, 3, '2026-07-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(106, 3, '2026-08-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(107, 3, '2026-09-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(108, 3, '2026-10-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(109, 3, '2026-11-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(110, 3, '2026-12-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(111, 3, '2027-01-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(112, 3, '2027-02-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(113, 3, '2027-03-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(114, 3, '2027-04-26', 93333.33, 0.00, NULL, 'pending', '2025-04-25 23:49:06', '2025-04-25 23:49:06'),
(115, 9, '2025-05-26', 8750.00, 8750.00, '2025-04-26 00:00:00', 'paid', '2025-04-25 23:50:43', '2025-04-26 00:00:19'),
(116, 9, '2025-06-26', 8750.00, 8750.00, '2025-04-26 00:00:00', 'paid', '2025-04-25 23:50:43', '2025-04-26 00:30:44'),
(117, 9, '2025-07-26', 8750.00, 8750.00, '2025-04-26 00:00:00', 'paid', '2025-04-25 23:50:43', '2025-04-26 00:31:10'),
(118, 9, '2025-08-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:43', '2025-04-25 23:50:43'),
(119, 9, '2025-09-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:43', '2025-04-25 23:50:43'),
(120, 9, '2025-10-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:43', '2025-04-25 23:50:43'),
(121, 9, '2025-05-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:54', '2025-04-25 23:50:54'),
(122, 9, '2025-06-26', 8750.00, 8750.00, '2025-04-26 00:00:00', 'paid', '2025-04-25 23:50:54', '2025-04-26 00:01:58'),
(123, 9, '2025-07-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:54', '2025-04-25 23:50:54'),
(124, 9, '2025-08-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:54', '2025-04-25 23:50:54'),
(125, 9, '2025-09-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:54', '2025-04-25 23:50:54'),
(126, 9, '2025-10-26', 8750.00, 0.00, NULL, 'pending', '2025-04-25 23:50:54', '2025-04-25 23:50:54'),
(127, 6, '2025-05-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(128, 6, '2025-06-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(129, 6, '2025-07-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(130, 6, '2025-08-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(131, 6, '2025-09-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(132, 6, '2025-10-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(133, 6, '2025-11-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(134, 6, '2025-12-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(135, 6, '2026-01-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(136, 6, '2026-02-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(137, 6, '2026-03-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(138, 6, '2026-04-26', 4583.33, 0.00, NULL, 'pending', '2025-04-26 01:02:07', '2025-04-26 01:02:07'),
(139, 10, '2025-05-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(140, 10, '2025-06-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(141, 10, '2025-07-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(142, 10, '2025-08-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(143, 10, '2025-09-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(144, 10, '2025-10-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:08:34', '2025-04-27 09:08:34'),
(145, 10, '2025-05-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(146, 10, '2025-06-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(147, 10, '2025-07-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(148, 10, '2025-08-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(149, 10, '2025-09-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(150, 10, '2025-10-27', 1750.00, 0.00, NULL, 'pending', '2025-04-27 09:09:16', '2025-04-27 09:09:16'),
(151, 11, '2025-05-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(152, 11, '2025-06-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(153, 11, '2025-07-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(154, 11, '2025-08-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(155, 11, '2025-09-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(156, 11, '2025-10-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:32:59', '2025-04-30 03:32:59'),
(157, 11, '2025-05-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37'),
(158, 11, '2025-06-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37'),
(159, 11, '2025-07-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37'),
(160, 11, '2025-08-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37'),
(161, 11, '2025-09-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37'),
(162, 11, '2025-10-30', 8750.00, 0.00, NULL, 'pending', '2025-04-30 03:34:37', '2025-04-30 03:34:37');

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `member_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `shares_balance` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`member_id`, `first_name`, `last_name`, `id_number`, `phone`, `email`, `address`, `registration_date`, `status`, `shares_balance`) VALUES
(1, 'John', 'Doe', '1', '254712345678', 'john@rukindo.com', 'nakawa', '2025-04-11 00:00:00', 'active', 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `members_backup`
--

CREATE TABLE `members_backup` (
  `member_id` int(11) NOT NULL DEFAULT 0,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `id_number` varchar(20) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `shares_balance` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members_backup`
--

INSERT INTO `members_backup` (`member_id`, `first_name`, `last_name`, `id_number`, `phone`, `email`, `address`, `registration_date`, `status`, `shares_balance`) VALUES
(1, 'John', 'Doe', '1', '254712345678', 'john@rukindo.com', 'nakawa', '2025-04-11 00:00:00', 'active', 500000.00);

-- --------------------------------------------------------

--
-- Table structure for table `memberz`
--

CREATE TABLE `memberz` (
  `id` int(11) NOT NULL,
  `member_no` varchar(20) NOT NULL DEFAULT 'TEMP',
  `full_name` varchar(100) NOT NULL,
  `nin_number` varchar(14) NOT NULL COMMENT 'Uganda National ID',
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date NOT NULL,
  `occupation` varchar(50) DEFAULT NULL,
  `phone` varchar(10) NOT NULL COMMENT 'UG format without +256',
  `email` varchar(100) DEFAULT NULL,
  `district` varchar(50) NOT NULL,
  `subcounty` varchar(50) DEFAULT NULL,
  `village` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `next_of_kin_name` varchar(100) DEFAULT NULL,
  `next_of_kin_contact` varchar(10) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memberz`
--

INSERT INTO `memberz` (`id`, `member_no`, `full_name`, `nin_number`, `gender`, `dob`, `occupation`, `phone`, `email`, `district`, `subcounty`, `village`, `address`, `next_of_kin_name`, `next_of_kin_contact`, `reg_date`, `status`) VALUES
(1, 'UG-HOI-2025-6067', 'Isaac Mukonyezi', 'CM01006109YADK', 'Male', '2007-03-28', 'Software Developer', '754902068', 'isaacvital44@gmail.com', 'Hoima', 'Bugambe', 'Nyamulima', NULL, 'Hope Babirye', '7846366363', '2025-04-11 12:45:01', 'active'),
(3, 'UG-KAM-2025-2349', 'Kyogabire Lucky', 'CF0373523663KL', 'Female', '2004-03-30', 'Student', '763546366', 'lucky@rukindo.com', 'Kampala', 'Nakawa', 'Naguru', NULL, 'Jeni', '7846366363', '2025-04-11 16:06:46', 'active'),
(4, 'UG-MBA-2025-2451', 'Anold Lee', 'CM01056309LEDK', 'Male', '1994-06-08', 'Data Engineer', '754862068', 'anoldlee@gmail.com', 'Mbarara', 'Lwampara', 'Rukindo', NULL, 'Ahuura Sam', '7846366356', '2025-04-11 16:09:03', 'active'),
(5, 'UG-KAM-2025-2031', 'Muwanika de Eric', 'CM01206109YA10', 'Male', '2003-09-25', 'Django Contributor', '754950068', 'eric@muwanika.com', 'Kampala', 'Kyaliwajala', 'Karo Karungi', NULL, 'Martin Odegard', '7846456356', '2025-04-11 16:11:55', 'active'),
(6, 'UG-KAM-2025-6520', 'Doreen Re2no', 'CF0473523663KL', 'Female', '2004-09-08', 'Graphics Designer', '750490222', 're2no@graphicshub.com', 'Kampala', 'Mbuya', 'Nakawa', NULL, 'Ahuura Sam', '7846456390', '2025-04-11 16:14:28', 'active'),
(7, 'UG-MAS-2025-2762', 'Hajjati Shamsa Nanyonjo', 'CF0373523663SH', 'Female', '2007-03-27', 'Seasoned Web Developer', '735319710', 'shamsa@algeria.com', 'Masaka', 'Masaka Town Council', 'Masaka', NULL, 'Hajjati Zamzam', '7846346367', '2025-04-11 16:17:12', 'active'),
(9, 'UG-WAK-2025-0944', 'Jon Doe', 'CM01034109YADK', 'Male', '2007-03-28', 'Software Developer', '754902068', 'john@doe.com', 'Wakiso', 'Kyaliwajala', 'Rukindo', NULL, 'Ahuura Sam', '7846456356', '2025-04-11 17:20:24', 'active'),
(10, 'UG-HOI-2025-4760', 'Jeni Rose', 'CF0373583663KL', 'Female', '2007-04-08', 'Student', '754902068', 'jeni@uict.com', 'Hoima', 'Mubende', 'N/A', NULL, 'isaac mukonyezi', '7846456356', '2025-04-11 17:24:15', 'active'),
(13, 'UG-FOR-2025-1285', 'Belamy Blake', 'CM99456109YADK', 'Male', '2007-04-02', 'Movie Actor', '784376626', 'belamy@blake.com', 'Fort Portal', 'Mbuya', 'Nyamulima', NULL, 'Octavia Blake', '736377333', '2025-04-13 03:19:46', 'active'),
(18, 'UG-HOI-2025-8204', 'Ivan Mutasa', 'CF0473523663MK', 'Male', '2007-03-28', 'Student', '754930068', 'ivan@system.com', 'Hoima', 'Bugambe', 'Lwampala', NULL, 'Ahuura Sam', '7846456334', '2025-04-16 06:34:42', 'active'),
(19, 'UG-SOR-2025-2636', 'Jon Wick', 'CM0382DJ37B373', 'Male', '2006-07-04', 'Students', '702567491', 'johnwick34@system.com', 'Soroti', 'Bugambe', 'Kikuube', NULL, 'Babirye Hope Musoke', '784659561', '2025-04-17 14:15:29', 'active'),
(20, 'UG-HOI-2025-5463', 'Ethan Banderous', 'CM01006109PUNK', 'Other', '2007-04-05', 'Student', '754667823', 'bandana@gmail.com', 'Hoima', 'Buhimba', 'Central', NULL, 'Otim Robert', '7846346300', '2025-04-20 15:18:18', 'active'),
(21, 'UG-KAB-2025-0642', 'Tumukunde Matia Mulumba', 'CM90PUNK234389', 'Other', '2007-04-03', 'Farmer', '753673637', 'tumu@rksystem.com', 'Kabale', 'Mbuya', 'Rukindo', NULL, 'Tolo', '7846346306', '2025-04-23 05:08:36', 'active'),
(23, 'UG-ENT-2025-8417', 'Ntale Ronnie', 'CM01006109Y262', 'Male', '2007-03-28', 'Software Developer', '797377377', 'isaacvital44@gmail.com', 'Entebbe', 'Bugambe', 'Nyamulima', NULL, 'isaac mukonyezi', '7846346389', '2025-04-26 01:07:33', 'active'),
(25, 'UG-HOI-2025-3833', 'Hope Babirye', 'CF01006109YADK', 'Female', '2007-05-01', 'Student', '788306796', 'hopemukonyezi@gmail.com', 'Hoima', 'Bugambe', 'Nyamulima', NULL, 'isaac mukonyezi', '7846346367', '2025-05-01 09:22:29', 'active'),
(26, 'UG-ENT-2025-1386', 'Aendru Davis', 'CM047352366HWS', 'Male', '2007-05-01', 'Graphics Designer', '456543227', 'isaacvital44@gmail.com', 'Entebbe', 'Kyaliwajala', 'Mbuya', NULL, 'isaac mukonyezi', '7889346367', '2025-05-01 09:29:44', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `course_id`, `amount`, `payment_method`, `transaction_id`, `status`, `created_at`) VALUES
(1, 6, 2, 10000.00, 'mobile_money', 'txn_4931f064a3844079', 'completed', '2025-05-25 06:47:26'),
(2, 8, 3, 10000.00, 'mobile_money', 'txn_687df3f122d28903', 'completed', '2025-05-25 07:25:16'),
(3, 8, 2, 10000.00, 'mobile_money', 'txn_0efa61695a8da003', 'completed', '2025-05-25 07:42:28'),
(4, 6, 3, 10000.00, 'mobile_money', 'txn_ac09a347c46b5aae', 'pending', '2025-05-25 18:46:20'),
(5, 10, 7, 15000.00, 'mobile_money', 'txn_97405460adddd488', 'pending', '2025-05-25 19:23:13'),
(6, 10, 3, 10000.00, 'mobile_money', 'txn_6fccf6693b1703fd', 'pending', '2025-05-26 04:19:50'),
(7, 10, 2, 10000.00, 'mobile_money', 'txn_ae5fd9cdc5b7bb2b', 'pending', '2025-05-26 09:54:13'),
(8, 10, 14, 200000.00, 'mobile_money', 'txn_4dd92c96d456bab1', 'pending', '2025-05-27 21:46:11'),
(9, 10, 4, 10000.00, 'mobile_money', 'txn_5331bf6cf5e840c2', 'pending', '2025-05-28 22:54:11'),
(10, 11, 14, 200000.00, 'mobile_money', 'txn_66497ebea19b3a70', 'pending', '2025-05-28 23:00:53'),
(13, 10, 1, 10000.00, 'mobile_money', 'TXN-1748465809-a1cfeab6', 'completed', '2025-05-28 23:56:49'),
(14, 10, 15, 30000.00, 'mobile_money', 'TXN-1748466413-48e57494', 'completed', '2025-05-29 00:06:53'),
(15, 10, 16, 1000000.00, 'mobile_money', 'TXN-1748468271-42e64323', 'completed', '2025-05-29 00:37:51'),
(16, 12, 2, 10000.00, 'mobile_money', 'TXN-1748584815-8bcdf12e', 'completed', '2025-05-30 09:00:15'),
(17, 10, 18, 200000.00, 'mobile_money', 'TXN-1748592010-8e2a432f', 'completed', '2025-05-30 11:00:10'),
(18, 1, 18, 200000.00, 'mobile_money', 'TXN-1748595365-93a4523f', 'completed', '2025-05-30 11:56:05'),
(19, 13, 18, 200000.00, 'mobile_money', 'TXN-1748614441-98f5fe36', 'completed', '2025-05-30 17:14:01'),
(20, 14, 18, 200000.00, 'mobile_money', 'TXN-1748948937-5952e457', 'completed', '2025-06-03 14:08:57'),
(21, 10, 27, 370000.00, 'mobile_money', 'TXN-1749220292-377cf544', 'completed', '2025-06-06 17:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `payouts`
--

CREATE TABLE `payouts` (
  `payout_id` int(11) NOT NULL,
  `tutor_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(100) DEFAULT NULL COMMENT 'Payout transaction ID',
  `status` enum('pending','processed','failed') DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `passing_score` int(11) NOT NULL COMMENT 'Percentage e.g., 70',
  `max_attempts` int(11) DEFAULT 1,
  `time_limit` int(11) DEFAULT NULL COMMENT 'Minutes (optional)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `attempt_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `score` int(11) NOT NULL COMMENT 'Percentage',
  `completed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','true_false','fill_blank') NOT NULL DEFAULT 'mcq',
  `points` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `savings`
--

CREATE TABLE `savings` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `received_by` varchar(100) DEFAULT NULL,
  `receipt_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `savings`
--

INSERT INTO `savings` (`id`, `member_id`, `amount`, `date`, `received_by`, `receipt_no`, `notes`, `created_at`) VALUES
(1, 6, 50000.00, '2025-04-17', NULL, '678', 'initial savings', '2025-04-17 17:26:29'),
(2, 19, 200000.00, '2025-04-17', NULL, '45', 'initial savings', '2025-04-17 17:29:32'),
(3, 4, 50000.00, '2025-04-18', NULL, '45', 'week two', '2025-04-18 06:03:56'),
(4, 4, 200000.00, '2025-04-19', NULL, '45', 'week3', '2025-04-18 06:04:40'),
(8, 3, 210000.00, '2025-04-18', NULL, 'RCPT-20250418-0005', 'Initial Saving for April', '2025-04-18 09:09:32'),
(9, 1, 45000.00, '2025-04-18', NULL, 'RCPT-20250418-0006', 'weekly savings', '2025-04-18 17:44:37'),
(10, 6, 1000.00, '2025-04-19', NULL, 'RCPT-20250419-0002', 'second savings', '2025-04-19 08:07:12'),
(11, 4, 140000.00, '2025-04-19', NULL, 'RCPT-20250419-0003', 'testing', '2025-04-19 09:57:20'),
(12, 7, 700000.00, '2025-04-19', NULL, 'RCPT-20250419-0004', 'savings', '2025-04-19 17:07:05'),
(14, 19, 50000.00, '2025-04-20', NULL, 'RCPT-20250420-0002', 'savings', '2025-04-20 15:24:01'),
(15, 21, 2000.00, '2025-04-23', NULL, 'RCPT-20250423-0001', 'testing', '2025-04-23 05:36:55'),
(24, 1, 50000.00, '2025-04-25', NULL, 'RCPT-20250425-0001', 'notes', '2025-04-25 18:29:37'),
(25, 1, 50000.00, '2025-04-25', NULL, 'RCPT-20250425-0002', 'no notes', '2025-04-25 18:30:11'),
(26, 5, 3000.00, '2025-04-25', NULL, 'RCPT-20250425-0003', 'notes', '2025-04-25 18:34:00'),
(27, 6, 200000.00, '2025-04-25', NULL, 'RCPT-20250425-0004', '', '2025-04-25 20:26:00'),
(28, 23, 50000.00, '2025-04-26', NULL, 'RCPT-20250426-0001', 'savings', '2025-04-26 01:08:22'),
(29, 3, 50000.00, '2025-04-26', NULL, 'RCPT-20250426-0002', 'save', '2025-04-26 05:31:53'),
(30, 3, 50000.00, '2025-04-30', NULL, 'RCPT-20250430-0001', '', '2025-04-30 03:28:55'),
(32, 7, 200000.00, '2025-04-30', NULL, 'RCPT-20250430-0002', '', '2025-04-30 03:31:39'),
(33, 4, 50000.00, '2025-04-30', NULL, 'RCPT-20250430-0003', '', '2025-04-30 03:42:45'),
(34, 18, 5000.00, '2025-05-01', NULL, 'RCPT-20250501-0001', 'savings deposiut\r\n', '2025-05-01 09:18:00');

-- --------------------------------------------------------

--
-- Table structure for table `savings_accounts`
--

CREATE TABLE `savings_accounts` (
  `account_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `balance` decimal(12,2) DEFAULT 0.00,
  `last_updated` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `subscribed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `course_id`, `subscribed_at`) VALUES
(1, 1, 5, '2025-05-25 06:07:08'),
(2, 6, 5, '2025-05-25 06:25:06'),
(3, 6, 2, '2025-05-25 06:47:26'),
(4, 8, 5, '2025-05-25 07:24:43'),
(5, 8, 3, '2025-05-25 07:25:16'),
(6, 8, 2, '2025-05-25 07:42:28'),
(7, 6, 3, '2025-05-25 18:46:20'),
(8, 9, 5, '2025-05-25 18:57:21'),
(9, 10, 7, '2025-05-25 19:23:13'),
(10, 10, 5, '2025-05-26 03:23:36'),
(11, 10, 3, '2025-05-26 04:19:50'),
(12, 10, 2, '2025-05-26 09:54:13'),
(13, 10, 14, '2025-05-27 21:46:11'),
(14, 10, 4, '2025-05-28 22:54:11'),
(15, 11, 14, '2025-05-28 23:00:53'),
(16, 11, 5, '2025-05-28 23:06:23'),
(17, 10, 1, '2025-05-28 23:56:49'),
(18, 10, 15, '2025-05-29 00:06:53'),
(19, 10, 16, '2025-05-29 00:37:51'),
(20, 10, 17, '2025-05-29 01:12:11'),
(21, 12, 17, '2025-05-30 08:27:34'),
(22, 12, 2, '2025-05-30 09:00:15'),
(23, 10, 18, '2025-05-30 11:00:10'),
(24, 1, 18, '2025-05-30 11:56:05'),
(25, 13, 18, '2025-05-30 17:14:01'),
(26, 14, 18, '2025-06-03 14:08:57'),
(27, 10, 27, '2025-06-06 17:31:32'),
(28, 10, 29, '2025-06-06 20:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `transaction_type` enum('deposit','loan','repayment','fine') NOT NULL,
  `transaction_date` date DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `member_id`, `amount`, `transaction_type`, `transaction_date`, `reference`, `notes`, `created_at`) VALUES
(1, 1, 50000.00, 'deposit', '2025-04-25', 'RCPT-20250425-0001', NULL, '2025-04-25 18:29:37'),
(2, 1, 50000.00, 'deposit', '2025-04-25', 'RCPT-20250425-0002', NULL, '2025-04-25 18:30:11'),
(3, 5, 3000.00, 'deposit', '2025-04-25', 'RCPT-20250425-0003', NULL, '2025-04-25 18:34:00'),
(4, 6, 200000.00, 'deposit', '2025-04-25', 'RCPT-20250425-0004', NULL, '2025-04-25 20:26:00'),
(5, 23, 50000.00, 'deposit', '2025-04-26', 'RCPT-20250426-0001', NULL, '2025-04-26 01:08:22'),
(6, 3, 50000.00, 'deposit', '2025-04-26', 'RCPT-20250426-0002', NULL, '2025-04-26 05:31:53'),
(7, 3, 50000.00, 'deposit', '2025-04-30', 'RCPT-20250430-0001', NULL, '2025-04-30 03:28:55'),
(9, 7, 200000.00, 'deposit', '2025-04-30', 'RCPT-20250430-0002', NULL, '2025-04-30 03:31:39'),
(10, 4, 50000.00, 'deposit', '2025-04-30', 'RCPT-20250430-0003', NULL, '2025-04-30 03:42:45'),
(11, 18, 5000.00, 'deposit', '2025-05-01', 'RCPT-20250501-0001', NULL, '2025-05-01 09:18:00');

-- --------------------------------------------------------

--
-- Table structure for table `tutors`
--

CREATE TABLE `tutors` (
  `tutor_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL COMMENT 'Ugandan format (+256...)',
  `password_hash` varchar(255) NOT NULL COMMENT 'bcrypt/Argon2',
  `role` enum('learner','tutor','admin') NOT NULL DEFAULT 'learner',
  `profile_pic` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `phone`, `password_hash`, `role`, `profile_pic`, `bio`, `created_at`, `last_login`, `status`, `is_active`, `approved_at`, `rejection_reason`, `approved_by`, `reviewed_at`, `reviewed_by`) VALUES
(1, 'Mr Robot', 'robot@lalapi.com', '+256754902068', '$2y$10$WI1wuQgnPYP4MB1DDX9JxefCVCABg4vtPJQT3knzFaOC0V2HpMKNq', 'admin', NULL, 'admin god', '2025-05-21 23:52:11', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(2, 'Johner', 'johner@lalapi.com', '+256700461140', '$2y$10$Q8WoHjZ4vLnZhG3VclA6SeaTXsh7nsoEithXwEEyxqwJh9ju/oMuS', 'admin', NULL, '', '2025-05-22 01:01:11', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(3, 'King Gwangaeto', 'king@lalapi.com', '+256776121422', '$2y$10$AYpUprkBjRV4Xvk5/ybPdOzhg9FMbBxgWmcnQxR4tHwRVBfDfaKjy', 'learner', NULL, NULL, '2025-05-22 08:49:08', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(4, 'Tutor', 'tutor@lapapi.com', '+256754902000', '$2y$10$H.1WVv5YRs6PShC6iWlYEuiySafuqY9nWVmJKEmtYcqTZeJa8RZgC', 'tutor', NULL, '', '2025-05-22 09:17:38', NULL, 'rejected', 1, NULL, 'reject', NULL, '2025-05-30 09:23:05', 1),
(5, 'Testing', 'test@lalapi.com', '+256784902068', '$2y$10$dXeeWILFX9Nh7sSQYOHqz.BVyE2bp6NGy80/NsFoWIzemPjQfW2/K', 'tutor', NULL, NULL, '2025-05-22 20:55:45', NULL, 'approved', 1, '2025-05-30 10:51:28', NULL, 1, NULL, NULL),
(6, 'Mr Nobody', 'nobody@lalapi.com', '+256736886695', '$2y$10$4ifDn0S7uDu07eA4nUeEZe8XpHte3PFyCxI2MCuRtcRRKKEYqIj1i', 'learner', NULL, NULL, '2025-05-25 05:20:38', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(7, 'John Doe', 'johndoe@lalapi.com', '+256776121420', '$2y$10$USrrnVCtZNZRcJXhoA1elu.n/.RP7Wrhwv8loZ7QATFhBmwvYxpOS', 'tutor', NULL, NULL, '2025-05-25 05:49:16', NULL, 'approved', 1, '2025-05-30 10:51:21', NULL, 1, NULL, NULL),
(8, 'Qualified Learner', 'learner@lalapi.com', '+256736886660', '$2y$10$o6pUV8Y07tCKJTfnfh6x2OyaixtdVlJ4u8a4uyiqZAwMQnvXhKgRi', 'learner', NULL, NULL, '2025-05-25 07:24:19', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(9, 'Ahuura Sam', 'teach@lalapi.com', '+256754902067', '$2y$10$rTZ3DVzON9TxofJ8tI9da.WLUF.IS1.i6RASM6y/88pE8tQzQzB2K', 'tutor', NULL, NULL, '2025-05-25 18:47:54', NULL, 'approved', 1, '2025-05-30 09:20:51', NULL, 1, NULL, NULL),
(10, 'Special Learner', 'learn@lalapi.com', '+256776121423', '$2y$10$AdK2/ph6aMV2/z6DgheMTOK1LriuHNcCNSt.2rrDs8hPquRwMX30q', 'learner', NULL, NULL, '2025-05-25 19:22:25', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(11, 'isaac mukonyezi', 'student@lalapi.com', '+256754902070', '$2y$10$fK0CgHyMn4fJvdpLMWflX.Sme2gITKrzFXpf7ubQDfcrSbx9G3tEe', 'learner', NULL, NULL, '2025-05-28 23:00:23', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(12, 'Mayson Jayson', 'fixer@lalapi.com', '+256736663634', '$2y$10$9LpGtiisIaxZj18lJD43fuifH9z1zlJIZe8C.iYT5MB/Nt6IFTgHG', 'learner', NULL, NULL, '2025-05-30 08:26:54', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(13, 'isaac mukonyezi', 'isaac.mukonyezi@xonib.com', '+256754902060', '$2y$10$95ZCHV78yChcteWqQbgxBOF5oA4jWXYhcfFcF/N56yI1ekE40wSda', 'learner', NULL, NULL, '2025-05-30 17:12:47', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL),
(14, 'Testing User', 'test2@lalapi.com', '+256762625536', '$2y$10$d5zsC80/CNv.IYlChT9Wguqxv.OoZxQzosoTnwS3Z89spiGMRnPQC', 'learner', NULL, NULL, '2025-06-03 14:07:22', NULL, 'pending', 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_answers`
--

CREATE TABLE `user_answers` (
  `response_id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_id` int(11) DEFAULT NULL COMMENT 'For MCQ/TrueFalse',
  `text_answer` text DEFAULT NULL COMMENT 'For fill-in-blank',
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE `user_meta` (
  `meta_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `language_preference` varchar(50) DEFAULT NULL COMMENT 'e.g., Luganda, Runyankole',
  `education_level` varchar(50) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_meta`
--

INSERT INTO `user_meta` (`meta_id`, `user_id`, `language_preference`, `education_level`, `device_type`) VALUES
(1, 2, 'Luganda', NULL, NULL),
(2, 4, 'Luganda', NULL, NULL),
(3, 1, 'Luganda', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `progress_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `status` enum('started','completed') DEFAULT 'started',
  `last_watched_at` datetime DEFAULT current_timestamp(),
  `time_spent` int(11) DEFAULT 0 COMMENT 'In seconds'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_admin_logs_user_id` (`user_id`),
  ADD KEY `idx_admin_logs_created_at` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `unique_user_course_cert` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `tutor_id` (`tutor_id`),
  ADD KEY `idx_course_language` (`language`),
  ADD KEY `fk_approved_by` (`approved_by`);

--
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_course_category` (`course_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD PRIMARY KEY (`section_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `idx_lesson_order` (`course_id`,`order`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `user_lesson` (`user_id`,`lesson_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `loan_number` (`loan_number`);

--
-- Indexes for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `memberz`
--
ALTER TABLE `memberz`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_no` (`member_no`),
  ADD UNIQUE KEY `nin_number` (`nin_number`),
  ADD UNIQUE KEY `member_no_3` (`member_no`),
  ADD KEY `district` (`district`),
  ADD KEY `member_no_2` (`member_no`),
  ADD KEY `nin_number_2` (`nin_number`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `payouts`
--
ALTER TABLE `payouts`
  ADD PRIMARY KEY (`payout_id`),
  ADD KEY `tutor_id` (`tutor_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`attempt_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `savings`
--
ALTER TABLE `savings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `account_number` (`account_number`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `tutors`
--
ALTER TABLE `tutors`
  ADD PRIMARY KEY (`tutor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_phone` (`phone`);

--
-- Indexes for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `answer_id` (`answer_id`);

--
-- Indexes for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD UNIQUE KEY `unique_user_lesson` (`user_id`,`lesson_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_materials`
--
ALTER TABLE `course_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `course_sections`
--
ALTER TABLE `course_sections`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `loan_repayments`
--
ALTER TABLE `loan_repayments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `memberz`
--
ALTER TABLE `memberz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `payouts`
--
ALTER TABLE `payouts`
  MODIFY `payout_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `savings`
--
ALTER TABLE `savings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tutors`
--
ALTER TABLE `tutors`
  MODIFY `tutor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_meta`
--
ALTER TABLE `user_meta`
  MODIFY `meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `fk_admin_logs_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD CONSTRAINT `course_categories_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_materials`
--
ALTER TABLE `course_materials`
  ADD CONSTRAINT `course_materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `course_sections`
--
ALTER TABLE `course_sections`
  ADD CONSTRAINT `course_sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`payment_id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lessons_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `course_sections` (`section_id`);

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `payouts`
--
ALTER TABLE `payouts`
  ADD CONSTRAINT `payouts_ibfk_1` FOREIGN KEY (`tutor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD CONSTRAINT `quiz_answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`);

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `savings`
--
ALTER TABLE `savings`
  ADD CONSTRAINT `savings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `memberz` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_accounts`
--
ALTER TABLE `savings_accounts`
  ADD CONSTRAINT `savings_accounts_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `memberz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`attempt_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`question_id`),
  ADD CONSTRAINT `user_answers_ibfk_3` FOREIGN KEY (`answer_id`) REFERENCES `quiz_answers` (`answer_id`);

--
-- Constraints for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD CONSTRAINT `user_meta_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`lesson_id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
