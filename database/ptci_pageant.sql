-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 20, 2025 at 09:43 AM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u378403689_ptci_pageant`
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
(170196, 4, 'philippe reyes', 'green', 'male', '2025-10-19 09:25:13'),
(244146, 9, 'john fritz mendoza', 'yellow', 'male', '2025-10-19 09:22:12'),
(459218, 7, 'froilan nuhay', 'red', 'male', '2025-10-19 09:23:27'),
(592538, 9, 'ivy cortez', 'yellow', 'female', '2025-10-19 09:17:27'),
(678178, 7, 'janguem gabuco', 'red', 'female', '2025-10-19 09:23:48'),
(759515, 1, 'andrea mae aniar', 'yellow', 'female', '2025-10-19 09:22:32'),
(776802, 4, 'rhea tindog', 'green', 'female', '2025-10-19 09:26:22'),
(821169, 1, 'rey eldrine paniza', 'yellow', 'male', '2025-10-19 09:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `final_score`
--

CREATE TABLE `final_score` (
  `id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `talent_final_score` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `final_score`
--

INSERT INTO `final_score` (`id`, `cand_id`, `talent_final_score`, `created_at`, `updated_at`) VALUES
(0, 170196, 80.50, '2025-10-20 09:40:46', '2025-10-20 09:43:17'),
(0, 821169, 86.00, '2025-10-20 09:40:46', '2025-10-20 09:40:46'),
(0, 244146, 78.00, '2025-10-20 09:40:47', '2025-10-20 09:43:17'),
(0, 459218, 84.00, '2025-10-20 09:40:47', '2025-10-20 09:43:17');

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
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`choreography` + `projection` + `audience_impact`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `swimware_score`
--

CREATE TABLE `swimware_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `stage_presence` decimal(5,2) NOT NULL CHECK (`stage_presence` >= 0 and `stage_presence` <= 100),
  `figure_and_fitness` decimal(5,2) NOT NULL CHECK (`figure_and_fitness` >= 0 and `figure_and_fitness` <= 100),
  `poise_and_bearing` decimal(5,2) NOT NULL CHECK (`poise_and_bearing` >= 0 and `poise_and_bearing` <= 100),
  `overall_impact` decimal(5,2) NOT NULL CHECK (`overall_impact` >= 0 and `overall_impact` <= 100),
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`stage_presence` + `figure_and_fitness` + `poise_and_bearing` + `overall_impact`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `talent_score`
--

CREATE TABLE `talent_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `mastery` decimal(5,2) NOT NULL CHECK (`mastery` >= 0 and `mastery` <= 100),
  `performance_choreography` decimal(5,2) NOT NULL CHECK (`performance_choreography` >= 0 and `performance_choreography` <= 100),
  `overall_impression` decimal(5,2) NOT NULL CHECK (`overall_impression` >= 0 and `overall_impression` <= 100),
  `audience_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`mastery` + `performance_choreography` + `overall_impression` + `audience_impact`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `talent_score`
--

INSERT INTO `talent_score` (`score_id`, `cand_id`, `judge_id`, `mastery`, `performance_choreography`, `overall_impression`, `audience_impact`, `created_at`) VALUES
(365070, 170196, 825535, 24.00, 30.00, 15.00, 8.00, '2025-10-20 09:40:45'),
(580831, 821169, 825535, 21.00, 37.00, 19.00, 9.00, '2025-10-20 09:40:46'),
(115674, 244146, 825535, 26.00, 21.00, 14.00, 9.00, '2025-10-20 09:40:47'),
(846269, 459218, 825535, 20.00, 31.00, 18.00, 7.00, '2025-10-20 09:40:47'),
(573227, 170196, 169508, 25.00, 32.00, 18.00, 9.00, '2025-10-20 09:43:17'),
(498895, 244146, 169508, 26.00, 37.00, 17.00, 6.00, '2025-10-20 09:43:17'),
(543243, 459218, 169508, 29.00, 38.00, 18.00, 7.00, '2025-10-20 09:43:17'),
(939943, 821169, 169508, 24.00, 36.00, 17.00, 9.00, '2025-10-20 09:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `uniform_score`
--

CREATE TABLE `uniform_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `poise_and_bearings` decimal(5,2) NOT NULL CHECK (`poise_and_bearings` >= 0 and `poise_and_bearings` <= 100),
  `personality_and_projection` decimal(5,2) NOT NULL CHECK (`personality_and_projection` >= 0 and `personality_and_projection` <= 100),
  `neatness` decimal(5,2) NOT NULL CHECK (`neatness` >= 0 and `neatness` <= 100),
  `overall_impact` decimal(5,2) NOT NULL CHECK (`overall_impact` >= 0 and `overall_impact` <= 100),
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`poise_and_bearings` + `personality_and_projection` + `neatness` + `overall_impact`) STORED,
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
(169508, 'judge2', '$2y$10$2WIewArnoEgj5f.WiecQA.VITi8XwzEN3/96N4MzBaxaIJYNotNRi', 'judge', '2025-10-19 09:08:03'),
(285124, 'judge3', '$2y$10$QnTf5U9hnC77zuS7qxND7u7C5rkImqCPmoKt9hlL5Ct.B2fd71sW6', 'judge', '2025-10-19 09:08:09'),
(310270, 'judge5', '$2y$10$hRabDPe2y.WJDT2OTv2VweFD.diZKn91Ht3yTl3/NkuUXV7goOxKi', 'judge', '2025-10-19 15:27:44'),
(557632, 'judge4', '$2y$10$5NZt/jIF5cEVK3j63eSiP.jEBmRyfxCd8rjyX473qkksXc4UIrQz.', 'judge', '2025-10-19 15:27:20'),
(825535, 'judge1', '$2y$10$RnCiOBgKQdCnmRq8pGljG.CC0O3eqJ9.7GZi7pw3BdZK/MCsx3f22', 'judge', '2025-10-19 09:07:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contestants`
--
ALTER TABLE `contestants`
  ADD PRIMARY KEY (`cand_id`);

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
  MODIFY `cand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=821170;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=825536;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
