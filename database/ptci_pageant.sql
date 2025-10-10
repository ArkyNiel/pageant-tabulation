-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 03:17 AM
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
-- Database: `ptci_pageant`
--

-- --------------------------------------------------------

--
-- Table structure for table `contestants`
--

CREATE TABLE `contestants` (
  `cand_id` int(11) NOT NULL,
  `cand_number` int(11) NOT NULL,
  `cand_name` varchar(100) NOT NULL,
  `cand_team` enum('red','yellow','green','purple','blue') NOT NULL,
  `cand_gender` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contestants`
--

INSERT INTO `contestants` (`cand_id`, `cand_number`, `cand_name`, `cand_team`, `cand_gender`, `created_at`) VALUES
(1, 1, 'Ivy Coret', 'yellow', 'Female', '2025-10-03 06:03:41');

-- --------------------------------------------------------

--
-- Table structure for table `talent_score`
--

CREATE TABLE `talent_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `mastery` decimal(5,2) NOT NULL CHECK (`mastery` >= 0 and `mastery` <= 100),
  `performance_choreography` decimal(5,2) NOT NULL CHECK (`performance_choreography` >= 0 and `performance_choreography` <= 100),
  `overall_impression` decimal(5,2) NOT NULL CHECK (`overall_impression` >= 0 and `overall_impression` <= 100),
  `audience_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`mastery` * 0.30 + `performance_choreography` * 0.40 + `overall_impression` * 0.20 + `audience_impact` * 0.10) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `talent_score`
--

INSERT INTO `talent_score` (`score_id`, `cand_id`, `mastery`, `performance_choreography`, `overall_impression`, `audience_impact`, `created_at`) VALUES
(1, 1, 90.00, 89.00, 88.00, 87.00, '2025-10-06 08:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `production_score`
--

CREATE TABLE `production_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `choreography` decimal(5,2) NOT NULL CHECK (`choreography` >= 0 and `choreography` <= 100),
  `projection` decimal(5,2) NOT NULL CHECK (`projection` >= 0 and `projection` <= 100),
  `audience_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  -- must be added
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`choreography` * 0.40 + `projection` * 0.40 + `audience_impact` * 0.20) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------



CREATE TABLE `swimware_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `stage_presence` decimal(5,2) NOT NULL CHECK (`stage_presence` >= 0 and `stage_presence` <= 100),
  `figure_fitness` decimal(5,2) NOT NULL CHECK (`figure_fitness` >= 0 and `figure_fitness` <= 100),
  `poise_bearing` decimal(5,2) NOT NULL CHECK (`poise_bearing` >= 0 and `poise_bearing` <= 100),
  `overall_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  `overall_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`stage_presence` * 0.40 + `figure_fitness` * 0.30 + `poise_bearing` * 0.20 + `overall_impact` * 0.10) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('judge','admin') DEFAULT 'judge',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(27, 'Ivy coret', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC', 'judge', '2025-10-02 03:18:11'),
(28, 'Ivy', '$2y$10$pJRS9XigailBZ0G37DbMkeIscouVp2Sd1wYZGc/5tS5bJ/4spigVC', 'judge', '2025-10-02 06:16:19'),
(34, 'ivy cortez', '$2y$10$R61Q5biHfO4vt7qwUiaEC.QPG2QSGAeqykcVo/56ZsC.qvJU1RiJy', 'admin', '2025-10-04 02:27:05'),
(35, 'Niel', '$2y$10$cjgeaHMPRg/tzcEKtbGXrOpVFIcft2zFq1jMQreM5rC6vFKZ9sy7W', 'judge', '2025-10-04 02:27:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contestants`
--
ALTER TABLE `contestants`
  ADD PRIMARY KEY (`cand_id`);

--
-- Indexes for table `production_score`
--
ALTER TABLE `production_score`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `cand_id` (`cand_id`);

--
-- Indexes for table `talent_score`
--
ALTER TABLE `talent_score`
  ADD PRIMARY KEY (`score_id`),
  ADD KEY `cand_id` (`cand_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contestants`
--
ALTER TABLE `contestants`
  MODIFY `cand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `production_score`
--
ALTER TABLE `production_score`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `talent_score`
--
ALTER TABLE `talent_score`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `production_score`
--
ALTER TABLE `production_score`
  ADD CONSTRAINT `production_score_ibfk_1` FOREIGN KEY (`cand_id`) REFERENCES `contestants` (`cand_id`) ON DELETE CASCADE;

--
-- Constraints for table `talent_score`
--
ALTER TABLE `talent_score`
  ADD CONSTRAINT `talent_score_ibfk_1` FOREIGN KEY (`cand_id`) REFERENCES `contestants` (`cand_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
