-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2025 at 07:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arm`
--

-- --------------------------------------------------------

--
-- Table structure for table `game_state`
--

CREATE TABLE `game_state` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `game_time` int(11) DEFAULT 480,
  `quarter` int(11) DEFAULT 1,
  `game_status` varchar(50) DEFAULT 'Ready',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game_state`
--

INSERT INTO `game_state` (`id`, `match_id`, `game_time`, `quarter`, `game_status`, `updated_at`) VALUES
(24, 214, 0, 4, 'Live', '2025-06-17 05:28:00');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `team1_id` int(11) DEFAULT NULL,
  `team2_id` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `match_type` enum('regular','semifinal','final') DEFAULT 'regular',
  `status` enum('Scheduled','Ongoing','Completed','Cancelled') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `team1_id`, `team2_id`, `match_date`, `match_type`, `status`) VALUES
(187, 1, 2, '2025-01-20', 'regular', 'Completed'),
(188, 1, 3, '2025-01-20', 'regular', 'Completed'),
(189, 1, 5, '2025-01-20', 'regular', 'Completed'),
(190, 2, 4, '2025-01-20', 'regular', 'Completed'),
(191, 2, 19, '2025-01-21', 'regular', 'Completed'),
(192, 3, 4, '2025-01-21', 'regular', 'Completed'),
(193, 3, 20, '2025-01-21', 'regular', 'Completed'),
(194, 4, 21, '2025-01-21', 'regular', 'Completed'),
(195, 5, 19, '2025-01-22', 'regular', 'Completed'),
(197, 19, 21, '2025-01-22', 'regular', 'Completed'),
(198, 20, 21, '2025-01-22', 'regular', 'Completed'),
(212, 5, 20, '2025-06-18', 'regular', 'Completed'),
(213, 5, 1, '2025-06-19', 'semifinal', 'Completed'),
(214, 21, 3, '2025-06-19', 'semifinal', 'Completed'),
(215, 3, 5, '2025-06-20', 'final', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `player_name` varchar(100) NOT NULL,
  `team_id` int(11) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `jersey_num` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `player_name`, `team_id`, `position`, `age`, `image`, `jersey_num`) VALUES
(1, 'Marvs ', 1, 'Center', 18, 'uploads/player_images/player_6835dbaa41eff3.85748908.png', 99),
(2, 'Shaq', 2, 'Power Forward', 17, 'uploads/player_images/player_683c651e9410b7.17265751.jpg', 18),
(5, 'Vin', 2, 'Point Guard', 18, 'uploads/player_images/player_68468509c9fe00.36128657.png', 10),
(8, 'AKONG', 5, 'Center', 20, 'uploads/player_images/player_68495877cf8c93.33105243.png', 18),
(9, 'James', 5, 'Point Guard', 20, 'uploads/player_images/player_684958c51a2290.30621734.jpg', 20),
(10, 'Hev', 5, 'Point Guard', 18, 'uploads/player_images/player_6849591f2e6d07.16809430.jpg', 1),
(11, 'Ronz', 3, 'Small Forward', 19, 'uploads/player_images/player_68495f2d59f4d7.91072115.png', 14),
(12, 'Marvelous Jaco Gonzales', 3, 'Power Forward', 20, 'uploads/player_images/player_68495f4b4c3b79.05565720.jpg', 11),
(13, 'Faker', 3, 'Point Guard', 21, 'uploads/player_images/player_68495f6c408d52.44964857.jpg', 8),
(14, 'Travis Scott', 3, 'Center', 30, 'uploads/player_images/player_68495f8fda2f11.38375012.png', 5),
(15, 'Ralf', 1, 'Point Guard', 19, 'uploads/player_images/player_68495fb1215cd2.31710892.png', 8),
(16, 'Erick Spoelstra', 1, 'Small Forward', 20, 'uploads/player_images/player_68495fc71a6c89.98543977.png', 20),
(17, 'Onyeka Okongwu', 3, 'Center', 25, 'uploads/player_images/player_6849661a36e433.14800415.png', 43),
(26, 'Trae Young', 4, 'Point Guard', 17, NULL, 18);

-- --------------------------------------------------------

--
-- Table structure for table `player_stats`
--

CREATE TABLE `player_stats` (
  `stat_id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `player_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `rebounds` int(11) DEFAULT 0,
  `assists` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `player_stats`
--

INSERT INTO `player_stats` (`stat_id`, `match_id`, `player_id`, `points`, `rebounds`, `assists`) VALUES
(16, 214, 11, 1, 0, 0),
(17, 214, 12, 6, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `score_id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `team1_score` int(11) DEFAULT NULL,
  `team2_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`score_id`, `match_id`, `team1_score`, `team2_score`) VALUES
(136, 187, 82, 65),
(137, 188, 79, 79),
(138, 189, 71, 87),
(139, 190, 74, 79),
(140, 191, 83, 89),
(141, 192, 78, 61),
(142, 193, 72, 86),
(143, 194, 66, 71),
(144, 195, 67, 65),
(146, 197, 76, 84),
(147, 198, 72, 78),
(150, 212, 15, 0),
(151, 214, 39, 110),
(152, 213, 30, 0),
(153, 215, 30, 0);

-- --------------------------------------------------------

--
-- Table structure for table `score_transactions`
--

CREATE TABLE `score_transactions` (
  `transaction_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `team_number` int(11) NOT NULL,
  `old_score` int(11) NOT NULL,
  `new_score` int(11) NOT NULL,
  `score_change` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `action_type` enum('add','subtract','correction') DEFAULT 'add'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `score_transactions`
--

INSERT INTO `score_transactions` (`transaction_id`, `match_id`, `team_number`, `old_score`, `new_score`, `score_change`, `reason`, `created_by`, `created_at`, `action_type`) VALUES
(1, 214, 1, 21, 24, 3, 'Score added: +3', 'Admin', '2025-06-17 04:46:21', 'add'),
(2, 214, 1, 24, 27, 3, 'Score added: +3', 'Admin', '2025-06-17 04:46:24', 'add'),
(3, 214, 2, 6, 9, 3, 'Score added: +3', 'Admin', '2025-06-17 04:46:51', 'add'),
(4, 214, 2, 9, 12, 3, 'Score added: +3', 'Admin', '2025-06-17 04:47:04', 'add'),
(5, 214, 2, 12, 15, 3, 'Score added: +3', 'Admin', '2025-06-17 04:47:35', 'add'),
(6, 214, 2, 15, 14, -1, 'Score removed: -1', 'Admin', '2025-06-17 04:47:44', 'subtract'),
(7, 214, 2, 14, 13, -1, 'Score removed: -1', 'Admin', '2025-06-17 04:48:08', 'subtract'),
(8, 214, 2, 13, 12, -1, 'Score removed: -1', 'Admin', '2025-06-17 04:48:16', 'subtract'),
(9, 214, 2, 12, 13, 1, 'Score added: +1', 'Admin', '2025-06-17 04:48:22', 'add'),
(10, 214, 2, 13, 14, 1, 'Score added: +1', 'Admin', '2025-06-17 04:48:23', 'add'),
(11, 214, 2, 14, 17, 3, 'Score added: +3', 'Admin', '2025-06-17 04:58:37', 'add'),
(12, 214, 2, 17, 20, 3, 'Score added: +3', 'Admin', '2025-06-17 04:59:24', 'add'),
(13, 214, 2, 20, 23, 3, 'Score added: +3', 'Admin', '2025-06-17 04:59:33', 'add'),
(14, 214, 2, 23, 26, 3, 'Score added: +3', 'Admin', '2025-06-17 05:00:09', 'add'),
(15, 214, 2, 26, 29, 3, 'Score added: +3', 'Admin', '2025-06-17 05:00:38', 'add'),
(16, 214, 2, 29, 28, -1, 'Score removed: -1', 'Admin', '2025-06-17 05:01:00', 'subtract'),
(17, 214, 2, 28, 31, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:06', 'add'),
(18, 214, 2, 31, 34, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:07', 'add'),
(19, 214, 2, 34, 37, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:08', 'add'),
(20, 214, 2, 37, 40, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:08', 'add'),
(21, 214, 2, 40, 43, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:08', 'add'),
(22, 214, 2, 43, 46, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:08', 'add'),
(23, 214, 2, 46, 49, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:08', 'add'),
(24, 214, 2, 49, 52, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:09', 'add'),
(25, 214, 2, 52, 55, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:09', 'add'),
(26, 214, 2, 52, 55, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:10', 'add'),
(27, 214, 2, 55, 54, -1, 'Score removed: -1', 'Admin', '2025-06-17 05:01:12', 'subtract'),
(28, 214, 2, 54, 53, -1, 'Score removed: -1', 'Admin', '2025-06-17 05:01:26', 'subtract'),
(29, 214, 2, 53, 56, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:28', 'add'),
(30, 214, 2, 56, 59, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:28', 'add'),
(31, 214, 2, 59, 62, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:28', 'add'),
(32, 214, 2, 62, 65, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:28', 'add'),
(33, 214, 2, 65, 68, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:29', 'add'),
(34, 214, 2, 68, 71, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:29', 'add'),
(35, 214, 2, 71, 74, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:29', 'add'),
(36, 214, 2, 74, 77, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:29', 'add'),
(37, 214, 2, 77, 80, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:30', 'add'),
(38, 214, 2, 80, 83, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:30', 'add'),
(39, 214, 2, 83, 86, 3, 'Score added: +3', 'Admin', '2025-06-17 05:01:34', 'add'),
(40, 214, 2, 86, 87, 1, 'Score added: +1', 'Admin', '2025-06-17 05:02:21', 'add'),
(41, 214, 2, 87, 86, -1, 'Score removed: -1', 'Admin', '2025-06-17 05:02:25', 'subtract'),
(42, 214, 2, 86, 89, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:20', 'add'),
(43, 214, 2, 89, 92, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:23', 'add'),
(44, 214, 2, 92, 95, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:25', 'add'),
(45, 214, 2, 95, 92, -3, 'Undid: Score added: +3', 'Admin', '2025-06-17 05:07:29', ''),
(46, 214, 2, 92, 95, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:36', 'add'),
(47, 214, 2, 95, 98, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:38', 'add'),
(48, 214, 2, 98, 101, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:38', 'add'),
(49, 214, 2, 101, 104, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:38', 'add'),
(50, 214, 2, 104, 107, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:38', 'add'),
(51, 214, 2, 107, 110, 3, 'Score added: +3', 'Admin', '2025-06-17 05:07:38', 'add'),
(52, 214, 1, 27, 30, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:00', 'add'),
(53, 214, 1, 30, 33, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:00', 'add'),
(54, 214, 1, 33, 36, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:00', 'add'),
(55, 214, 1, 36, 39, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:00', 'add'),
(56, 214, 1, 39, 42, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:01', 'add'),
(57, 214, 1, 42, 45, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:01', 'add'),
(58, 214, 1, 45, 48, 3, 'Score added: +3', 'Admin', '2025-06-17 05:08:01', 'add'),
(59, 214, 1, 42, 39, -3, 'Undid: Score added: +3', 'Admin', '2025-06-17 05:08:02', ''),
(60, 214, 1, 39, 42, 3, 'Undid: Undid: Score added: +3', 'Admin', '2025-06-17 05:08:04', ''),
(61, 214, 1, 42, 39, -3, 'Undid: Undid: Undid: Score added: +3', 'Admin', '2025-06-17 05:08:05', ''),
(62, 213, 1, 0, 3, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:22', 'add'),
(63, 213, 1, 3, 6, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(64, 213, 1, 6, 9, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(65, 213, 1, 9, 12, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(66, 213, 1, 12, 15, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(67, 213, 1, 15, 18, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(68, 213, 1, 18, 21, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:23', 'add'),
(69, 213, 1, 21, 24, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:24', 'add'),
(70, 213, 1, 24, 27, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:24', 'add'),
(71, 213, 1, 27, 30, 3, 'Score added: +3', 'Admin', '2025-06-17 05:28:24', 'add'),
(72, 215, 1, 0, 3, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:07', 'add'),
(73, 215, 1, 3, 6, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:07', 'add'),
(74, 215, 1, 6, 9, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:07', 'add'),
(75, 215, 1, 9, 12, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(76, 215, 1, 12, 15, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(77, 215, 1, 15, 18, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(78, 215, 1, 18, 21, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(79, 215, 1, 21, 24, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(80, 215, 1, 24, 27, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:08', 'add'),
(81, 215, 1, 27, 30, 3, 'Score added: +3', 'Admin', '2025-06-17 05:41:09', 'add');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `coach_name` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`team_id`, `team_name`, `coach_name`, `logo`) VALUES
(1, 'Borland', 'John', '../uploads/team_logos/team_68357d362fbb89.65854291.png'),
(2, 'Gentri', 'Mary', '../uploads/team_logos/team_68357d5e86e865.72546140.png'),
(3, 'Fontana', NULL, '../uploads/team_logos/team_68357fea8e9543.00065791.png'),
(4, 'Conchu', NULL, 'uploads/team_logos/team_68370c987e4385.36143584.png'),
(5, 'La Tri', NULL, 'uploads/team_logos/team_68370a0156df48.96388581.png'),
(19, 'Indang', 'ssssss', 'uploads/team_logos/team_6849df80490a24.56955492.png'),
(20, 'Paradahan', 'Janli', 'uploads/team_logos/team_6849e0217ac874.00233866.jpeg'),
(21, 'Tanza', 'Marvs', 'uploads/team_logos/team_6849e0466b1882.07474956.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_admin` int(5) NOT NULL DEFAULT 0,
  `password` varchar(20) NOT NULL DEFAULT '0',
  `access_key` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `is_admin`, `password`, `access_key`) VALUES
(13, 'da', 'hatdog@gmail.com', 1, '12345678', 'Fein'),
(15, '12121', 'asdasdasd@hhh', 2, '12345678', 'Respect');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `game_state`
--
ALTER TABLE `game_state`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `team1_id` (`team1_id`),
  ADD KEY `team2_id` (`team2_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `player_stats`
--
ALTER TABLE `player_stats`
  ADD PRIMARY KEY (`stat_id`),
  ADD KEY `match_id` (`match_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `score_transactions`
--
ALTER TABLE `score_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `game_state`
--
ALTER TABLE `game_state`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `player_stats`
--
ALTER TABLE `player_stats`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `score_transactions`
--
ALTER TABLE `score_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `game_state`
--
ALTER TABLE `game_state`
  ADD CONSTRAINT `game_state_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`team1_id`) REFERENCES `teams` (`team_id`),
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`team2_id`) REFERENCES `teams` (`team_id`);

--
-- Constraints for table `players`
--
ALTER TABLE `players`
  ADD CONSTRAINT `players_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`);

--
-- Constraints for table `player_stats`
--
ALTER TABLE `player_stats`
  ADD CONSTRAINT `player_stats_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`),
  ADD CONSTRAINT `player_stats_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`);

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `scores_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`);

--
-- Constraints for table `score_transactions`
--
ALTER TABLE `score_transactions`
  ADD CONSTRAINT `score_transactions_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
