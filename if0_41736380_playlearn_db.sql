-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql100.infinityfree.com
-- Generation Time: May 27, 2026 at 03:27 PM
-- Server version: 11.4.11-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41736380_playlearn_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_links`
--

CREATE TABLE `account_links` (
  `link_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `child_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `account_links`
--

INSERT INTO `account_links` (`link_id`, `parent_id`, `child_id`) VALUES
(3, 19, 18),
(2, 19, 23),
(4, 18, 18);

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_categories`
--

CREATE TABLE `curriculum_categories` (
  `id` int(11) NOT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `category_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_categories`
--

INSERT INTO `curriculum_categories` (`id`, `subject`, `category_name`) VALUES
(1, 'Math', 'Number Sequence');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_skills`
--

CREATE TABLE `curriculum_skills` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `skill_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum_skills`
--

INSERT INTO `curriculum_skills` (`id`, `category_id`, `skill_name`) VALUES
(1, 1, 'Count to 10'),
(2, 1, 'Count forward from any number to 10'),
(3, 1, 'Count back from 10');

-- --------------------------------------------------------

--
-- Table structure for table `game_reviews`
--

CREATE TABLE `game_reviews` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `rating_type` enum('student','parent') NOT NULL,
  `rating_score` decimal(2,1) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_reviews`
--

INSERT INTO `game_reviews` (`id`, `game_id`, `user_id`, `username`, `rating_type`, `rating_score`, `comment_text`, `created_at`) VALUES
(1, 12, 19, 'teoruize045', '', '2.0', 'this is a easy game', '2026-05-18 02:42:25'),
(2, 12, 20, 'teoruize044', '', '5.0', 'test', '2026-05-20 00:15:50'),
(3, 12, 20, 'teoruize044', '', '5.0', 'test', '2026-05-20 00:16:06');

-- --------------------------------------------------------

--
-- Table structure for table `game_scores`
--

CREATE TABLE `game_scores` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_name` varchar(50) NOT NULL,
  `score` int(11) NOT NULL,
  `level_reached` int(11) DEFAULT 1,
  `played_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` int(11) NOT NULL,
  `level_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Logic',
  `level_description` text DEFAULT NULL,
  `difficulty` enum('Easy','Normal','Hard') DEFAULT 'Easy',
  `target_score` int(11) DEFAULT 100,
  `image_url` varchar(255) DEFAULT 'assets/img/default_level.jpg',
  `game_url` varchar(255) NOT NULL DEFAULT 'game2048.php',
  `status` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) DEFAULT 0,
  `is_trending` tinyint(1) DEFAULT 0,
  `editor_choice` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`id`, `level_name`, `category`, `level_description`, `difficulty`, `target_score`, `image_url`, `game_url`, `status`, `created_at`, `is_featured`, `is_trending`, `editor_choice`) VALUES
(5, 'Word Wanderer', 'Focus', 'Great!', 'Hard', 0, 'assets/uploads/levels/1777121129_69ecb7698ab16.png', 'games/word_wanderer.php', 'published', '2026-04-25 12:45:29', 1, 1, 0),
(6, 'Snake', 'Speed', 'Great', 'Normal', 0, 'assets/uploads/levels/1777121151_69ecb77f83bbe.png', 'games/snake.php', 'published', '2026-04-25 12:45:51', 0, 0, 0),
(7, 'Odd One Out', 'Logic', 'Great!', 'Easy', 0, 'assets/uploads/levels/1777121266_69ecb7f28ba47.jpeg', 'games/odd_one_out.php', 'published', '2026-04-25 12:46:12', 0, 0, 0),
(8, 'Mine Sweeper', 'Logic', 'Great!', 'Hard', 0, 'assets/uploads/levels/1777121197_69ecb7ade21f8.png', 'games/minesweeper.php', 'published', '2026-04-25 12:46:38', 0, 0, 0),
(10, 'Math Pop', 'Speed', 'Great!', 'Easy', 0, 'assets/uploads/levels/1777121232_69ecb7d039f22.png', 'games/math_pop.php', 'published', '2026-04-25 12:47:12', 0, 1, 0),
(11, 'Emoji Memory Match', 'Memory', 'Great', 'Normal', 0, 'assets/uploads/levels/1777121260_69ecb7ec0c402.png', 'games/memory.php', 'published', '2026-04-25 12:47:40', 0, 0, 0),
(12, 'Math Merge 2048', 'Logic', 'Great!', 'Normal', 0, 'assets/uploads/levels/1777121293_69ecb80d63df6.png', 'games/game2048.php', 'published', '2026-04-25 12:48:13', 0, 1, 0),
(14, 'AAA', 'Logic', 'AAAAAAA', 'Normal', 0, 'assets/uploads/levels/1779241929_6a0d13c9aaff5.png', 'games/memory.php', 'published', '2026-05-20 01:52:08', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `site_config`
--

CREATE TABLE `site_config` (
  `config_key` varchar(50) NOT NULL,
  `config_value` varchar(255) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `site_config`
--

INSERT INTO `site_config` (`config_key`, `config_value`) VALUES
('maintenance_mode', '0'),
('maintenance_eta', '15'),
('maintenance_expires_at', '2026-05-20 00:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT 'Guest',
  `role` varchar(20) DEFAULT 'Visitor',
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `user_id`, `username`, `role`, `action`, `details`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(1, 1, 'Heng', 'admin', 'Login Attempt', 'Failed login (Wrong Password) for: Abc', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-12 02:30:58'),
(2, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 02:31:03'),
(3, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 02:31:10'),
(4, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 02:31:47'),
(5, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 02:47:39'),
(6, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Unknown user: teoruize035', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Failed', '2026-05-12 02:49:53'),
(7, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 02:50:09'),
(8, 19, 'teoruize045', 'parent', 'Security Violation', 'Unauthorized score submission attempt', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Unauthorized', '2026-05-12 03:00:14'),
(9, 19, 'teoruize045', 'parent', 'Security Violation', 'Unauthorized score submission attempt', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Unauthorized', '2026-05-12 03:00:40'),
(10, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:00:47'),
(11, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:00:52'),
(12, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:00:54'),
(13, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:00:59'),
(14, 18, 'A', 'player', 'Game Score', 'Saved score 0 for game: Odd One Out (Lvl 1)', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:01:04'),
(15, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:02:13'),
(16, 18, 'A', 'player', 'Game Score', 'Saved score 5 for game: Snake (Lvl 1)', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:04:13'),
(17, 18, 'A', 'player', 'Logout', 'User logged out safely', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:04:29'),
(18, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:04:51'),
(19, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:22:50'),
(20, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:22:52'),
(21, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:22:52'),
(22, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:22:53'),
(23, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:22:53'),
(24, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:23:42'),
(25, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:23:42'),
(26, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:23:42'),
(27, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:23:43'),
(28, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:27:23'),
(29, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:27:24'),
(30, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:27:24'),
(31, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:27:25'),
(32, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:27:25'),
(33, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:32'),
(34, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:32'),
(35, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:32'),
(36, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:33'),
(37, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:38'),
(38, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:37:51'),
(39, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:38:14'),
(40, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:38:25'),
(41, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:38:57'),
(42, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:39:00'),
(43, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:39:22'),
(44, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:39:37'),
(45, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:40:09'),
(46, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:40:23'),
(47, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:40:54'),
(48, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:41:12'),
(49, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:41:24'),
(50, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:41:36'),
(51, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:41:51'),
(52, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:42:07'),
(53, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:42:10'),
(54, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-12 03:42:26'),
(55, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:43:12'),
(56, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:03'),
(57, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:04'),
(58, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:04'),
(59, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:04'),
(60, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:04'),
(61, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:05'),
(62, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:05'),
(63, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:07'),
(64, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:10'),
(65, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:45:12'),
(66, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:46:55'),
(67, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:47:18'),
(68, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:47:37'),
(69, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:47:52'),
(70, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 30 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:49:00'),
(71, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:49:13'),
(72, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 30 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:49:22'),
(73, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:49:29'),
(74, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 30 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:08'),
(75, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:10'),
(76, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:23'),
(77, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:32'),
(78, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:41'),
(79, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:53:54'),
(80, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:55:39'),
(81, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:56:44'),
(82, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:57:21'),
(83, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 30 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:57:29'),
(84, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:58:26'),
(85, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '203.106.57.11', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-12 03:58:33'),
(86, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:19:32'),
(87, 19, 'teoruize045', 'parent', 'Login Attempt', 'Failed password for: Heng', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-16 15:20:35'),
(88, 19, 'teoruize045', 'parent', 'Login Attempt', 'Failed password for: Heng', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-16 15:21:01'),
(89, 19, 'teoruize045', 'admin', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:25:20'),
(90, 19, 'teoruize045', 'admin', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:25:32'),
(91, 19, 'teoruize045', 'admin', 'Login Attempt', 'Failed password for: Heng', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-16 15:26:10'),
(92, 19, 'teoruize045', 'admin', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:26:16'),
(93, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:39:57'),
(94, 19, 'teoruize045', 'admin', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:50:32'),
(95, 19, 'teoruize045', 'admin', 'Logout', 'User logged out safely', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:52:11'),
(96, 19, 'teoruize045', 'admin', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:52:12'),
(97, 19, 'teoruize045', 'admin', 'Logout', 'User logged out safely', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:52:43'),
(98, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:52:44'),
(99, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '14.192.215.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-16 15:52:54'),
(100, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-17 14:38:25'),
(101, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-17 14:38:38'),
(102, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-17 14:39:08'),
(103, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.147', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:04:44'),
(104, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.147', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:04:53'),
(105, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:39:44'),
(106, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:46:49'),
(107, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:46:55'),
(108, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:47:03'),
(109, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:47:09'),
(110, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:47:14'),
(111, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:47:30'),
(112, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 02:47:31'),
(113, 20, 'teoruize044', 'player', 'Logout', 'User logged out safely', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:09:01'),
(114, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Failed password for: Heng', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-18 03:09:15'),
(115, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.15', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:09:26'),
(116, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Unknown user: Df', '203.106.57.3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Failed', '2026-05-18 03:12:14'),
(117, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.3', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:12:31'),
(118, 18, 'A', 'player', 'Logout', 'User logged out safely', '27.125.248.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:13:17'),
(119, 18, 'A', 'player', 'Login', 'User logged in successfully', '27.125.248.143', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:13:30'),
(120, 18, 'A', 'player', 'Logout', 'User logged out safely', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:16:31'),
(121, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Unknown user: Df', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Failed', '2026-05-18 03:20:05'),
(122, 18, 'A', 'player', 'Login', 'User logged in successfully', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:20:52'),
(123, 18, 'A', 'player', 'Logout', 'User logged out safely', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:21:27'),
(124, 18, 'A', 'player', 'Login', 'User logged in successfully', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:22:00'),
(125, 18, 'A', 'player', 'Logout', 'User logged out safely', '27.125.248.189', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:22:06'),
(126, 18, 'A', 'player', 'Login', 'User logged in successfully', '27.125.248.253', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:23:25'),
(127, 18, 'A', 'player', 'Logout', 'User logged out safely', '27.125.248.253', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:23:40'),
(128, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:24:34'),
(129, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:24:35'),
(130, 18, 'A', 'player', 'Logout', 'User logged out safely', '203.106.57.199', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 03:24:46'),
(131, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:29:42'),
(132, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:08'),
(133, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:09'),
(134, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:10'),
(135, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:12'),
(136, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:14'),
(137, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:16'),
(138, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:30:17'),
(139, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:40:44'),
(140, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Failed password for: A', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-18 03:40:58'),
(141, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.8', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 03:41:06'),
(142, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 04:14:51'),
(143, 18, 'A', 'player', 'Logout', 'User logged out safely', '203.106.57.18', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Mobile Safari/537.36', 'Success', '2026-05-18 04:16:09'),
(144, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 04:16:12'),
(145, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 04:18:24'),
(146, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.22', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-18 04:42:06'),
(147, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 02:38:54'),
(148, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 02:41:27'),
(149, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 03:01:50'),
(150, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.4', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 03:09:08'),
(151, 20, 'teoruize044', 'player', 'Game Score', 'Saved score 6 for game: Memory Match (Lvl 1)', '203.106.57.13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 03:21:43'),
(152, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:05:26'),
(153, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:05:35'),
(154, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:05:36'),
(155, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.31', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 06:23:03'),
(156, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:26:07'),
(157, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:26:19'),
(158, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:34:24'),
(159, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:34:25'),
(160, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:08'),
(161, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:13'),
(162, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:18'),
(163, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:24'),
(164, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:32'),
(165, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:34'),
(166, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:35'),
(167, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:35:36'),
(168, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:37:17'),
(169, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:37:21'),
(170, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:37:24'),
(171, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:38'),
(172, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:41'),
(173, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:48'),
(174, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:51'),
(175, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:51'),
(176, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:52'),
(177, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:52'),
(178, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:41:53'),
(179, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:44:55'),
(180, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:45:06'),
(181, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:10'),
(182, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:15'),
(183, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:16'),
(184, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:23'),
(185, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:29'),
(186, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:42'),
(187, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:46:44'),
(188, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:47:26'),
(189, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:47:26'),
(190, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:51:29'),
(191, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:51:30'),
(192, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:51:37'),
(193, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:51:50'),
(194, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:02'),
(195, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:06'),
(196, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:16'),
(197, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:16'),
(198, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:24'),
(199, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:25'),
(200, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:26'),
(201, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:26'),
(202, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:27'),
(203, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:28'),
(204, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:35'),
(205, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:35');
INSERT INTO `system_logs` (`log_id`, `user_id`, `username`, `role`, `action`, `details`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(206, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:55'),
(207, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:52:57'),
(208, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:54:32'),
(209, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:54:33'),
(210, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:54:45'),
(211, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:54:46'),
(212, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:58:15'),
(213, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:58:16'),
(214, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:58:26'),
(215, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 06:58:26'),
(216, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:04:35'),
(217, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:04:50'),
(218, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:05:18'),
(219, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:05:22'),
(220, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:07:21'),
(221, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:08:30'),
(222, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:08:36'),
(223, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:08:45'),
(224, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:08:56'),
(225, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:12:31'),
(226, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:33:49'),
(227, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:35:13'),
(228, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.21', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:35:20'),
(229, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 07:55:40'),
(230, 20, 'teoruize044', 'player', 'Game Score', 'Saved score 5 for game: Math Pop (Lvl 1)', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 08:12:18'),
(231, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:04:14'),
(232, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:05:36'),
(233, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:05:44'),
(234, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:08:49'),
(235, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:09:16'),
(236, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:15:50'),
(237, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:16:08'),
(238, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:19:52'),
(239, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:20:09'),
(240, 1, 'Heng', 'admin', 'User Management', 'Admin changed status of User ID #23 to banned', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:24:14'),
(241, 1, 'Heng', 'admin', 'User Management', 'Admin changed status of User ID #23 to active', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:24:23'),
(242, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:25:10'),
(243, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:25:46'),
(244, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:26:46'),
(245, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:33'),
(246, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:36'),
(247, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:37'),
(248, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:39'),
(249, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:40'),
(250, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:41'),
(251, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:42'),
(252, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:42'),
(253, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 13:43:43'),
(254, 18, 'A', 'player', 'Login', 'User logged in successfully', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 13:44:46'),
(255, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 13:49:47'),
(256, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:02:07'),
(257, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:03:21'),
(258, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:07:34'),
(259, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:20:48'),
(260, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:20:54'),
(261, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:23:20'),
(262, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:23:21'),
(263, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:23:22'),
(264, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:23:23'),
(265, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:32:30'),
(266, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:42:51'),
(267, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:42:54'),
(268, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:46:35'),
(269, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:46:37'),
(270, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:47:29'),
(271, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 14:51:50'),
(272, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:53:23'),
(273, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:53:24'),
(274, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:53:25'),
(275, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:53:27'),
(276, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:57:23'),
(277, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:57:24'),
(278, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:57:27'),
(279, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 14:57:28'),
(280, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:02:02'),
(281, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:02:03'),
(282, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:02:26'),
(283, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:02:48'),
(284, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:02:56'),
(285, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:03:00'),
(286, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:03:09'),
(287, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:08:45'),
(288, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:14:31'),
(289, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:18:45'),
(290, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:18:51'),
(291, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:18:57'),
(292, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:19:25'),
(293, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:21:50'),
(294, 2, 'Abc', 'player', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:24:27'),
(295, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:24:31'),
(296, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:24:40'),
(297, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:31:21'),
(298, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:06'),
(299, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 15 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:13'),
(300, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:19'),
(301, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:29'),
(302, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:34'),
(303, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:41'),
(304, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:32:57'),
(305, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:33:12'),
(306, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:33:24'),
(307, 18, 'A', 'player', 'Game Score', 'Saved score 5 for game: Memory Match (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:35:41'),
(308, 18, 'A', 'player', 'Game Score', 'Saved score 6 for game: Memory Match (Lvl 2)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:35:57'),
(309, 18, 'A', 'player', 'Game Score', 'Saved score 24 for game: Memory Match (Lvl 3)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:37:02'),
(310, 18, 'A', 'player', 'Game Score', 'Saved score 14 for game: Memory Match (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:37:35'),
(311, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:37:35'),
(312, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:37:39'),
(313, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 1 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:37:59'),
(314, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:38:02'),
(315, 18, 'A', 'player', 'Game Score', 'Saved score 57 for game: Memory Match (Lvl 5)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:39:36'),
(316, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:39:37'),
(317, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:40:07'),
(318, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:41:48'),
(319, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:42:13'),
(320, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 2)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:42:49'),
(321, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 2)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:43:08'),
(322, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:43:25'),
(323, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:43:32'),
(324, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:43:51'),
(325, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 1)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:43:58'),
(326, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 2)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:44:06'),
(327, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 2)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:44:23'),
(328, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 3)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:45:48'),
(329, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:46:04'),
(330, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:46:11'),
(331, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 1 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:46:27'),
(332, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:46:38'),
(333, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:46:39'),
(334, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:46:45'),
(335, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:47:04'),
(336, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:47:10'),
(337, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:47:36'),
(338, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:48:51'),
(339, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:49:23'),
(340, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:49:29'),
(341, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:49:58'),
(342, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:50:00'),
(343, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:50:04'),
(344, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:50:17'),
(345, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 1 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:50:25'),
(346, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:50:36'),
(347, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 4)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:50:38'),
(348, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 5)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:52:30'),
(349, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:52:31'),
(350, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:53:14'),
(351, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:53:41'),
(352, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 6)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:53:51'),
(353, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 6)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:55:30'),
(354, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 6)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:55:44'),
(355, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:56:10'),
(356, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:56:16'),
(357, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:56:28'),
(358, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 3 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:56:47'),
(359, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:56:54'),
(360, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:57:02'),
(361, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:57:18'),
(362, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:57:20'),
(363, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:57:25'),
(364, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 6)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:57:27'),
(365, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 15:57:35'),
(366, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 7)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 15:59:56'),
(367, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 7)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:03:06'),
(368, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 8)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:03:07'),
(369, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 8)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:03:13'),
(370, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 8)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:03:15'),
(371, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:04:28'),
(372, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 1 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:04:55'),
(373, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:04:59'),
(374, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:05:06'),
(375, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:05:30'),
(376, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 8)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:05:42'),
(377, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:06:07'),
(378, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:06:37'),
(379, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:07:08'),
(380, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:07:16'),
(381, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 8)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:08:38'),
(382, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 9)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:09:10'),
(383, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 9)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:09:14'),
(384, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 9)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:11:06'),
(385, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 9)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:11:10'),
(386, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 9)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:12:57'),
(387, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:13:31'),
(388, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:13:45'),
(389, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 2 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:13:54'),
(390, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:14:06'),
(391, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:14:26'),
(392, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:14:53'),
(393, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:15:31'),
(394, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 10)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:16:24'),
(395, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:16:25'),
(396, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:16:34'),
(397, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:16:39'),
(398, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:17:00'),
(399, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:17:12'),
(400, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:17:15'),
(401, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 2 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:17:25'),
(402, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:17:47'),
(403, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:18:57'),
(404, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:19:06'),
(405, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:20:38'),
(406, 1, 'Heng', 'admin', 'System Maintenance', 'Admin DISABLED maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:20:50'),
(407, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:20:54');
INSERT INTO `system_logs` (`log_id`, `user_id`, `username`, `role`, `action`, `details`, `ip_address`, `user_agent`, `status`, `created_at`) VALUES
(408, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:21:35'),
(409, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:21:46'),
(410, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:21:46'),
(411, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 11)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:24:50'),
(412, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 12)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:24:59'),
(413, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 2 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:25:58'),
(414, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:26:02'),
(415, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:26:10'),
(416, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 12)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:27:01'),
(417, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:28:48'),
(418, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 12)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:30:16'),
(419, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:30:47'),
(420, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:30:47'),
(421, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:32:38'),
(422, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:34:17'),
(423, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:34:43'),
(424, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:35:26'),
(425, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:35:53'),
(426, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:35:57'),
(427, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:36:10'),
(428, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:36:10'),
(429, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:36:11'),
(430, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:37:25'),
(431, 1, 'Heng', 'admin', 'System Maintenance', 'Admin ENABLED (Estimated: 1 mins) maintenance mode', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:37:34'),
(432, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:37:53'),
(433, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:38:21'),
(434, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:39:14'),
(435, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:39:53'),
(436, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:34'),
(437, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:39'),
(438, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:41'),
(439, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:45'),
(440, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:48'),
(441, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:43:52'),
(442, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:45:03'),
(443, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:45:32'),
(444, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:45:37'),
(445, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:46:27'),
(446, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:48:09'),
(447, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:48:27'),
(448, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to dark', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-19 16:49:53'),
(449, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 13)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:50:16'),
(450, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 14)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 16:54:07'),
(451, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 14)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:05:04'),
(452, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 15)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:15:30'),
(453, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 16)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:15:48'),
(454, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 16)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:16:38'),
(455, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 16)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:21:27'),
(456, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 16)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:21:39'),
(457, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 16)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:30:29'),
(458, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:30:35'),
(459, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:30:37'),
(460, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:30:42'),
(461, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:48:14'),
(462, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:48:24'),
(463, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:48:26'),
(464, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:50:38'),
(465, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:50:43'),
(466, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:50:46'),
(467, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:50:49'),
(468, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:50:51'),
(469, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:51:49'),
(470, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:54:51'),
(471, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:54:54'),
(472, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:54:59'),
(473, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:55:23'),
(474, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:56:07'),
(475, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:56:09'),
(476, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:56:12'),
(477, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:56:17'),
(478, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:58:50'),
(479, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:58:55'),
(480, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:58:58'),
(481, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:59:02'),
(482, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 17:59:15'),
(483, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:00:04'),
(484, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:00:08'),
(485, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:00:50'),
(486, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:00:54'),
(487, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:01:00'),
(488, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:02:23'),
(489, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:02:40'),
(490, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:02:55'),
(491, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:05:46'),
(492, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:05:48'),
(493, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:05:53'),
(494, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:06:03'),
(495, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:06:05'),
(496, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:06:07'),
(497, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:06:13'),
(498, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:07:55'),
(499, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:08:00'),
(500, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:08:51'),
(501, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:08:54'),
(502, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:08:57'),
(503, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:09:00'),
(504, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:09:04'),
(505, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:09:10'),
(506, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:09:16'),
(507, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:09:56'),
(508, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:11:18'),
(509, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:11:24'),
(510, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:11:53'),
(511, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:14:31'),
(512, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:14:36'),
(513, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:14:43'),
(514, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:14:46'),
(515, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:15:05'),
(516, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:15:08'),
(517, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:15:19'),
(518, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:17:57'),
(519, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:00'),
(520, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:03'),
(521, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:08'),
(522, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:15'),
(523, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:24'),
(524, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:32'),
(525, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:18:35'),
(526, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:19:02'),
(527, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:19:05'),
(528, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:19:09'),
(529, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:20:13'),
(530, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:20:16'),
(531, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:20:36'),
(532, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:23:39'),
(533, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:23:46'),
(534, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:23:51'),
(535, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:23:55'),
(536, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:27:35'),
(537, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:27:45'),
(538, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:27:59'),
(539, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:28:02'),
(540, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:28:05'),
(541, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:28:15'),
(542, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:28:25'),
(543, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:28:32'),
(544, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:18'),
(545, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:21'),
(546, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:26'),
(547, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:30'),
(548, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:34'),
(549, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:37'),
(550, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:40'),
(551, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:29:44'),
(552, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:31:02'),
(553, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:31:31'),
(554, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:32:35'),
(555, 18, 'A', 'player', 'Game Score', 'Saved score 100 for game: Mine Sweeper (Lvl 17)', '124.13.195.186', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0', 'Success', '2026-05-19 18:35:58'),
(556, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '161.142.143.159', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 00:12:40'),
(557, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 00:34:53'),
(558, 2, 'Abc', 'player', 'Logout', 'User logged out safely', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 00:36:21'),
(559, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '175.140.52.44', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 00:36:24'),
(560, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:03:14'),
(561, 24, 'ruize', 'parent', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:14:25'),
(562, 24, 'ruize', 'parent', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:14:51'),
(563, 24, 'ruize', 'parent', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:15:52'),
(564, 24, 'ruize', 'parent', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:16:56'),
(565, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:17:27'),
(566, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:18:14'),
(567, 18, 'A', 'player', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:19:29'),
(568, 18, 'A', 'player', 'Game Score', 'Saved score 5 for game: Memory Match (Lvl 1)', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:25:01'),
(569, 18, 'A', 'player', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:30:01'),
(570, 19, 'teoruize045', 'parent', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:30:19'),
(571, 19, 'teoruize045', 'parent', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:32:26'),
(572, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:32:37'),
(573, 20, 'teoruize044', 'player', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:34:33'),
(574, 20, 'teoruize044', 'player', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:42:37'),
(575, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Failed password for: Heng', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-20 01:42:50'),
(576, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:43:27'),
(577, 1, 'Heng', 'admin', 'Preference Change', 'Admin updated UI theme to light', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:44:53'),
(578, 1, 'Heng', 'admin', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:52:32'),
(579, NULL, 'Guest', 'Visitor', 'Login Attempt', 'Failed password for: ruize', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Failed', '2026-05-20 01:52:36'),
(580, 24, 'ruize', 'parent', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:52:44'),
(581, 24, 'ruize', 'parent', 'Logout', 'User logged out safely', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:52:54'),
(582, 2, 'Abc', 'player', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:53:20'),
(583, 1, 'Heng', 'admin', 'Login', 'User logged in successfully', '203.106.57.227', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'Success', '2026-05-20 01:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `avatar_frame` varchar(255) DEFAULT NULL,
  `frame_expires_at` datetime DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `title_expires_at` datetime DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `role` enum('player','admin','parent') DEFAULT 'player',
  `level` int(11) DEFAULT 1,
  `total_exp` int(11) DEFAULT 0,
  `weekly_exp` int(11) DEFAULT 0,
  `link_code` varchar(10) DEFAULT NULL,
  `status` enum('active','banned') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_seen` datetime DEFAULT current_timestamp(),
  `theme_preference` varchar(10) DEFAULT 'dark'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `profile_image`, `avatar_frame`, `frame_expires_at`, `title`, `title_expires_at`, `email`, `password`, `birthday`, `gender`, `role`, `level`, `total_exp`, `weekly_exp`, `link_code`, `status`, `last_login`, `created_at`, `last_seen`, `theme_preference`) VALUES
(1, 'Heng', NULL, NULL, NULL, NULL, NULL, 'junhengtoh@gmail.com', '$2y$10$Qaho/XATr4UdskDPbu83QuA0H8Dak03VarZn8Kk.Tyo62B.RLtzOy', '2006-09-12', '', 'admin', 1, 0, 0, NULL, 'active', '2026-05-20 09:53:43', '2026-04-22 15:04:08', '2026-05-20 09:54:33', 'light'),
(2, 'Abc', NULL, NULL, NULL, NULL, NULL, 'Abc@gmail.com', '$2y$10$vW6Ea09cXdp32nqRTrsbcO4//Yz4K8OuhvLwO8Fw8y56Yt6.dumpG', '2023-01-01', 'girl', 'player', 1, 0, 0, 'C9F940', 'active', '2026-05-20 09:53:20', '2026-04-22 15:06:46', '2026-05-20 09:53:20', 'dark'),
(17, 'Abc1', NULL, NULL, NULL, NULL, NULL, 'forgarenagame8787@gmail.com', '$2y$10$tRFwq4dXSEr5B6F6/nN5rurKXMj3nGi0wF6bpel9liuDJbt9rt5Jq', '2014-01-01', '', 'player', 1, 0, 0, 'A569A1', 'active', '2026-04-26 22:59:23', '2026-04-23 15:06:53', '2026-04-26 23:12:22', 'dark'),
(18, 'A', 'uploads/avatars/avatar_18_1779162443.png', NULL, NULL, NULL, NULL, 'youhong0311@gmail.com', '$2y$10$2p9qgxOqtBhF/cqYUy/2weJ72puOmUfTLVehcS9Z4FrDVchOZuRW2', '2006-11-03', 'boy', 'player', 1, 0, 0, 'E95104', 'active', '2026-05-20 09:19:29', '2026-04-26 05:33:22', '2026-05-20 09:24:26', 'dark'),
(19, 'teoruize045', NULL, NULL, NULL, NULL, NULL, 'teoruize001@gmail.com', '$2y$10$caew4P9ZXbB9rZ3Q810/3.1nToQDX7/1LsNUjfVeomaMEJeSHD4Re', '2026-02-02', 'boy', 'parent', 1, 0, 0, NULL, 'active', '2026-05-20 09:30:19', '2026-04-26 19:46:45', '2026-05-20 09:30:19', 'dark'),
(20, 'teoruize044', 'uploads/avatars/avatar_20_1778959848.png', 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMjAgMTIwIj4KICA8Y2lyY2xlIGN4PSI2MCIgY3k9IjYwIiByPSI1NiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjRkZEMzAwIiBzdHJva2Utd2lkdGg9IjYiIC8+CiAgPGNpcmNsZSBjeD0iNjAiIGN5PSI2MCIgc', '2026-05-27 00:20:57', NULL, NULL, 'teoruize044@gmail.com', '$2y$10$stq17Uc5y8ZazS9HlcadI.BknEGa65tTBgV3a8R/OcTByMPoXbOGO', '2005-02-03', 'boy', 'player', 5, 2110, 830, '8E5993', 'active', '2026-05-20 09:34:33', '2026-04-27 01:01:05', '2026-05-20 09:34:33', 'dark'),
(23, 'jayden', NULL, NULL, NULL, NULL, NULL, 'jaydenlow88888@gmail.com', '$2y$10$dxqOrRmtyn5kamn6O9H1COfsC9Rfa2EtX01cmvji4TJhBiZaGrf.y', '2006-05-20', 'boy', 'player', 1, 0, 0, 'E7C139', 'active', '2026-04-26 22:22:19', '2026-04-27 04:00:01', '2026-04-26 22:22:19', 'dark'),
(24, 'ruize', NULL, NULL, NULL, NULL, NULL, 'teoruize003@gmail.com', '$2y$10$l5/tl.ee6AywNLWgzj5xyOb7YqcpEk/jBpytI4pcIz2Iofxygldny', '2022-01-01', '', 'parent', 1, 0, 0, NULL, 'active', '2026-05-20 09:52:44', '2026-05-20 01:13:58', '2026-05-20 09:52:44', 'dark');

-- --------------------------------------------------------

--
-- Table structure for table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_favorites`
--

INSERT INTO `user_favorites` (`id`, `user_id`, `game_id`, `created_at`) VALUES
(5, 20, 12, '2026-05-19 08:50:30'),
(13, 20, 5, '2026-05-20 01:33:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_game_history`
--

CREATE TABLE `user_game_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `last_played` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_game_history`
--

INSERT INTO `user_game_history` (`id`, `user_id`, `game_id`, `last_played`) VALUES
(1, 20, 12, '2026-05-19 08:50:25'),
(2, 18, 11, '2026-05-20 01:24:26'),
(5, 18, 8, '2026-05-19 15:39:48'),
(6, 1, 12, '2026-05-19 15:45:29'),
(8, 18, 12, '2026-05-20 01:23:45'),
(10, 20, 5, '2026-05-20 01:33:46'),
(11, 1, 14, '2026-05-20 01:54:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_quests`
--

CREATE TABLE `user_quests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quest_key` varchar(50) NOT NULL,
  `quest_type` enum('daily','weekly') NOT NULL,
  `progress` int(11) DEFAULT 0,
  `is_claimed` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_quests`
--

INSERT INTO `user_quests` (`id`, `user_id`, `quest_key`, `quest_type`, `progress`, `is_claimed`, `updated_at`) VALUES
(1, 20, '20260519_dq1', 'daily', 0, 1, '2026-05-19 08:50:08'),
(2, 20, '20260519_dq2', 'daily', 0, 1, '2026-05-19 08:50:09'),
(3, 20, '20260519_dq3', 'daily', 0, 1, '2026-05-19 08:50:10'),
(4, 20, '202621_wq1', 'daily', 0, 1, '2026-05-19 16:10:37'),
(5, 20, '202621_chest_1', 'daily', 0, 1, '2026-05-19 16:10:39'),
(6, 20, '20260520_dq1', 'daily', 0, 1, '2026-05-19 16:20:50'),
(7, 20, '20260520_dq3', 'daily', 0, 1, '2026-05-19 16:20:50'),
(8, 20, '20260520_dq2', 'daily', 0, 1, '2026-05-19 16:20:50'),
(9, 20, '202621_wq3', 'daily', 0, 1, '2026-05-19 16:20:53'),
(10, 20, '202621_wq4', 'daily', 0, 1, '2026-05-19 16:20:53'),
(11, 20, '202621_wq2', 'daily', 0, 1, '2026-05-19 16:20:55'),
(12, 20, '202621_chest_3', 'daily', 0, 1, '2026-05-19 16:20:57'),
(13, 20, '202621_chest_2', 'daily', 0, 1, '2026-05-19 16:23:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_links`
--
ALTER TABLE `account_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `game_reviews`
--
ALTER TABLE `game_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `game_scores`
--
ALTER TABLE `game_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`config_key`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `link_code` (`link_code`);

--
-- Indexes for table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_fav` (`user_id`,`game_id`);

--
-- Indexes for table `user_game_history`
--
ALTER TABLE `user_game_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_history` (`user_id`,`game_id`);

--
-- Indexes for table `user_quests`
--
ALTER TABLE `user_quests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_quest` (`user_id`,`quest_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_links`
--
ALTER TABLE `account_links`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `game_reviews`
--
ALTER TABLE `game_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `game_scores`
--
ALTER TABLE `game_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=584;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_game_history`
--
ALTER TABLE `user_game_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_quests`
--
ALTER TABLE `user_quests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_reviews`
--
ALTER TABLE `game_reviews`
  ADD CONSTRAINT `game_reviews_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_scores`
--
ALTER TABLE `game_scores`
  ADD CONSTRAINT `game_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
