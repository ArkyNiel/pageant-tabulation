-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 23, 2025 at 08:11 AM
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
(192505, 9, 'Mendoza, John fritz', 'yellow', 'male', '2025-10-21 05:48:20'),
(241259, 4, 'Reyes, Julian philippe', 'green', 'male', '2025-10-21 05:44:50'),
(254348, 1, 'Paniza, Rey eldrine', 'yellow', 'male', '2025-10-21 05:42:59'),
(298368, 6, 'Tenorio, Sean steve', 'red', 'male', '2025-10-21 05:46:36'),
(304359, 7, 'Badang , Ethel ', 'red', 'female', '2025-10-21 05:48:15'),
(307858, 2, 'Monserate, Kevin', 'purple', 'male', '2025-10-21 05:43:29'),
(337043, 7, 'Nuhay, Froilan', 'red', 'male', '2025-10-21 05:47:05'),
(360494, 3, 'Napolitano, Joe mhari', 'purple', 'male', '2025-10-21 05:43:58'),
(381542, 3, 'Delos santos, Jona mae', 'purple', 'female', '2025-10-21 05:46:35'),
(425064, 8, 'Miguel, John israel', 'blue', 'male', '2025-10-21 05:47:53'),
(517505, 4, 'Tindog , Rea ', 'green', 'female', '2025-10-21 05:47:09'),
(533096, 5, 'Buenaflor , Roela ann', 'green', 'female', '2025-10-21 05:47:36'),
(565984, 2, 'Dela cruz , Christine ', 'purple', 'female', '2025-10-21 05:46:07'),
(573013, 8, 'Lungcay , Keanna', 'blue', 'female', '2025-10-21 05:49:12'),
(687440, 10, 'Quezada , Floria ', 'blue', 'female', '2025-10-21 05:50:27'),
(740089, 6, 'Gabuco, Jang geum ', 'red', 'female', '2025-10-21 05:47:59'),
(746412, 5, 'Avelino, Kenth francis jeo', 'green', 'male', '2025-10-21 05:45:43'),
(760869, 9, 'Cortez , Ivy ', 'yellow', 'female', '2025-10-21 05:49:46'),
(861205, 10, 'Palay, Roldan', 'blue', 'male', '2025-10-21 05:49:13'),
(882964, 1, 'Aniar , Andrea mae', 'yellow', 'female', '2025-10-21 05:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `final_score`
--

CREATE TABLE `final_score` (
  `cand_id` int(11) NOT NULL,
  `talent_final_score` decimal(5,2) DEFAULT 0.00,
  `uniform_final_score` decimal(5,2) DEFAULT 0.00,
  `swimwear_final_score` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `final_score`
--

INSERT INTO `final_score` (`cand_id`, `talent_final_score`, `uniform_final_score`, `swimwear_final_score`, `created_at`, `updated_at`) VALUES
(192505, 50.00, 80.00, 98.00, '2025-10-21 07:17:05', '2025-10-23 07:29:51'),
(241259, 58.80, 0.00, 0.00, '2025-10-21 07:16:33', '2025-10-21 07:17:28'),
(254348, 67.25, 0.00, 0.00, '2025-10-21 07:16:33', '2025-10-21 07:17:28'),
(298368, 83.80, 0.00, 0.00, '2025-10-21 07:16:26', '2025-10-21 07:17:28'),
(304359, 84.00, NULL, 0.00, '2025-10-21 07:44:05', '2025-10-23 04:12:20'),
(307858, 71.25, 0.00, 0.00, '2025-10-21 07:17:05', '2025-10-21 07:17:28'),
(337043, 82.33, 0.00, 0.00, '2025-10-21 07:17:09', '2025-10-21 07:17:28'),
(360494, 71.25, 0.00, 0.00, '2025-10-21 07:16:27', '2025-10-21 07:17:28'),
(381542, 83.00, 0.00, 0.00, '2025-10-21 07:44:05', '2025-10-21 07:44:09'),
(425064, 64.33, 0.00, 98.00, '2025-10-21 07:16:26', '2025-10-23 07:36:02'),
(517505, 66.00, 0.00, 0.00, '2025-10-21 07:44:05', '2025-10-21 07:44:09'),
(533096, 69.33, 0.00, 0.00, '2025-10-21 07:43:53', '2025-10-21 07:44:09'),
(565984, 73.75, 0.00, 0.00, '2025-10-21 07:44:05', '2025-10-21 07:44:09'),
(573013, 95.33, 0.00, 0.00, '2025-10-21 07:44:05', '2025-10-21 07:44:09'),
(687440, 74.67, 0.00, 0.00, '2025-10-21 07:44:05', '2025-10-23 03:56:11'),
(740089, 65.67, 0.00, 0.00, '2025-10-21 07:43:47', '2025-10-21 07:44:09'),
(746412, 63.67, 0.00, 0.00, '2025-10-21 07:16:33', '2025-10-21 07:17:28'),
(760869, 83.75, 0.00, 0.00, '2025-10-21 07:43:36', '2025-10-21 07:44:09'),
(861205, 91.60, 0.00, 0.00, '2025-10-21 07:17:05', '2025-10-21 07:17:28'),
(882964, 77.25, 40.00, 4.00, '2025-10-21 07:43:36', '2025-10-23 07:37:56');

-- --------------------------------------------------------

--
-- Table structure for table `production_score`
--

CREATE TABLE `production_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `choreography` decimal(5,2) NOT NULL CHECK (`choreography` >= 0 and `choreography` <= 100),
  `projection` decimal(5,2) NOT NULL CHECK (`projection` >= 0 and `projection` <= 100),
  `audience_impact` decimal(5,2) NOT NULL CHECK (`audience_impact` >= 0 and `audience_impact` <= 100),
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`choreography` + `projection` + `audience_impact`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `simwear_score`
--

CREATE TABLE `simwear_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `stage_presence` decimal(5,2) NOT NULL CHECK (`stage_presence` >= 0 and `stage_presence` <= 100),
  `figure_and_fitness` decimal(5,2) NOT NULL CHECK (`figure_and_fitness` >= 0 and `figure_and_fitness` <= 100),
  `poise_and_bearing` decimal(5,2) NOT NULL CHECK (`poise_and_bearing` >= 0 and `poise_and_bearing` <= 100),
  `overall_impact` decimal(5,2) NOT NULL CHECK (`overall_impact` >= 0 and `overall_impact` <= 100),
  `total_score` decimal(6,2) GENERATED ALWAYS AS (`stage_presence` + `figure_and_fitness` + `poise_and_bearing` + `overall_impact`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `simwear_score`
--

INSERT INTO `simwear_score` (`score_id`, `cand_id`, `stage_presence`, `figure_and_fitness`, `poise_and_bearing`, `overall_impact`, `created_at`) VALUES
(165613, 192505, 39.00, 29.00, 20.00, 10.00, '2025-10-23 07:26:35'),
(124119, 192505, 39.00, 29.00, 20.00, 10.00, '2025-10-23 07:29:51'),
(843076, 425064, 39.00, 29.00, 20.00, 10.00, '2025-10-23 07:36:02'),
(845240, 882964, 1.00, 1.00, 1.00, 1.00, '2025-10-23 07:37:56');

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
(926951, 298368, 177075, 29.00, 38.00, 18.00, 9.00, '2025-10-21 07:16:26'),
(821946, 425064, 177075, 26.00, 36.00, 18.00, 8.00, '2025-10-21 07:16:26'),
(317965, 360494, 177075, 23.00, 34.00, 15.00, 10.00, '2025-10-21 07:16:27'),
(338997, 298368, 411669, 24.00, 32.00, 14.00, 7.00, '2025-10-21 07:16:31'),
(774159, 425064, 411669, 14.00, 20.00, 14.00, 6.00, '2025-10-21 07:16:31'),
(464169, 254348, 508419, 20.00, 20.00, 10.00, 3.00, '2025-10-21 07:16:33'),
(226314, 746412, 508419, 20.00, 10.00, 8.00, 4.00, '2025-10-21 07:16:33'),
(634221, 241259, 508419, 12.00, 10.00, 8.00, 3.00, '2025-10-21 07:16:33'),
(149803, 425064, 508419, 15.00, 25.00, 7.00, 4.00, '2025-10-21 07:16:33'),
(437377, 746412, 177075, 25.00, 35.00, 17.00, 7.00, '2025-10-21 07:17:05'),
(657019, 241259, 411669, 24.00, 28.00, 13.00, 7.00, '2025-10-21 07:17:05'),
(528116, 746412, 411669, 22.00, 24.00, 12.00, 7.00, '2025-10-21 07:17:05'),
(784657, 861205, 411669, 29.00, 40.00, 19.00, 9.00, '2025-10-21 07:17:05'),
(204104, 307858, 411669, 22.00, 34.00, 12.00, 8.00, '2025-10-21 07:17:05'),
(302960, 861205, 508419, 30.00, 35.00, 12.00, 7.00, '2025-10-21 07:17:05'),
(426494, 192505, 508419, 5.00, 5.00, 15.00, 8.00, '2025-10-21 07:17:05'),
(371639, 254348, 177075, 27.00, 36.00, 18.00, 8.00, '2025-10-21 07:17:09'),
(314335, 307858, 177075, 28.00, 38.00, 17.00, 10.00, '2025-10-21 07:17:09'),
(566548, 360494, 177075, 23.00, 34.00, 15.00, 10.00, '2025-10-21 07:17:09'),
(497665, 425064, 177075, 26.00, 36.00, 18.00, 8.00, '2025-10-21 07:17:09'),
(690700, 298368, 177075, 29.00, 38.00, 18.00, 9.00, '2025-10-21 07:17:09'),
(707976, 241259, 177075, 24.00, 35.00, 18.00, 7.00, '2025-10-21 07:17:09'),
(139821, 192505, 177075, 24.00, 35.00, 17.00, 9.00, '2025-10-21 07:17:09'),
(189687, 746412, 177075, 25.00, 35.00, 17.00, 7.00, '2025-10-21 07:17:09'),
(726548, 861205, 177075, 29.00, 39.00, 20.00, 8.00, '2025-10-21 07:17:09'),
(721794, 337043, 177075, 25.00, 35.00, 18.00, 8.00, '2025-10-21 07:17:09'),
(655077, 254348, 411669, 24.00, 30.00, 14.00, 6.00, '2025-10-21 07:17:11'),
(355823, 241259, 411669, 24.00, 28.00, 13.00, 7.00, '2025-10-21 07:17:11'),
(813102, 360494, 411669, 13.00, 20.00, 13.00, 7.00, '2025-10-21 07:17:11'),
(768158, 307858, 411669, 22.00, 34.00, 12.00, 8.00, '2025-10-21 07:17:11'),
(491855, 337043, 411669, 27.00, 37.00, 19.00, 9.00, '2025-10-21 07:17:11'),
(120764, 298368, 411669, 24.00, 32.00, 14.00, 7.00, '2025-10-21 07:17:11'),
(968317, 746412, 411669, 22.00, 24.00, 12.00, 7.00, '2025-10-21 07:17:11'),
(917693, 861205, 411669, 29.00, 40.00, 19.00, 9.00, '2025-10-21 07:17:11'),
(227215, 425064, 411669, 14.00, 20.00, 14.00, 6.00, '2025-10-21 07:17:11'),
(293888, 192505, 411669, 14.00, 15.00, 13.00, 7.00, '2025-10-21 07:17:11'),
(800487, 746412, 508419, 20.00, 10.00, 8.00, 4.00, '2025-10-21 07:17:28'),
(973332, 307858, 508419, 10.00, 15.00, 5.00, 10.00, '2025-10-21 07:17:28'),
(110614, 337043, 508419, 25.00, 25.00, 13.00, 6.00, '2025-10-21 07:17:28'),
(614026, 241259, 508419, 12.00, 10.00, 8.00, 3.00, '2025-10-21 07:17:28'),
(549394, 360494, 508419, 15.00, 30.00, 15.00, 8.00, '2025-10-21 07:17:28'),
(323101, 861205, 508419, 30.00, 35.00, 12.00, 7.00, '2025-10-21 07:17:28'),
(369365, 254348, 508419, 20.00, 20.00, 10.00, 3.00, '2025-10-21 07:17:28'),
(622917, 298368, 508419, 25.00, 30.00, 15.00, 7.00, '2025-10-21 07:17:28'),
(674605, 425064, 508419, 15.00, 25.00, 7.00, 4.00, '2025-10-21 07:17:28'),
(964585, 192505, 508419, 5.00, 5.00, 15.00, 8.00, '2025-10-21 07:17:28'),
(804703, 760869, 177075, 29.00, 38.00, 17.00, 9.00, '2025-10-21 07:43:36'),
(860941, 882964, 177075, 27.00, 36.00, 17.00, 7.00, '2025-10-21 07:43:36'),
(756224, 740089, 508419, 10.00, 14.00, 13.00, 7.00, '2025-10-21 07:43:47'),
(969644, 760869, 508419, 22.00, 24.00, 12.00, 7.00, '2025-10-21 07:43:47'),
(616998, 760869, 411669, 26.00, 34.00, 16.00, 8.00, '2025-10-21 07:43:53'),
(812997, 533096, 411669, 24.00, 31.00, 18.00, 8.00, '2025-10-21 07:43:53'),
(273887, 565984, 177075, 26.00, 36.00, 18.00, 8.00, '2025-10-21 07:44:05'),
(181111, 687440, 508419, 20.00, 19.00, 11.00, 5.00, '2025-10-21 07:44:05'),
(243876, 573013, 508419, 30.00, 40.00, 20.00, 10.00, '2025-10-21 07:44:05'),
(979077, 304359, 177075, 27.00, 38.00, 19.00, 10.00, '2025-10-21 07:44:05'),
(885029, 517505, 411669, 20.00, 29.00, 13.00, 6.00, '2025-10-21 07:44:05'),
(638345, 304359, 411669, 25.00, 33.00, 15.00, 7.00, '2025-10-21 07:44:05'),
(279934, 517505, 508419, 13.00, 18.00, 10.00, 5.00, '2025-10-21 07:44:05'),
(513474, 882964, 411669, 25.00, 32.00, 16.00, 7.00, '2025-10-21 07:44:05'),
(566981, 565984, 508419, 13.00, 15.00, 8.00, 5.00, '2025-10-21 07:44:05'),
(232073, 565984, 411669, 23.00, 31.00, 17.00, 7.00, '2025-10-21 07:44:05'),
(710070, 304359, 508419, 18.00, 23.00, 17.00, 10.00, '2025-10-21 07:44:05'),
(127739, 533096, 508419, 10.00, 17.00, 12.00, 7.00, '2025-10-21 07:44:05'),
(772009, 882964, 508419, 20.00, 20.00, 10.00, 5.00, '2025-10-21 07:44:05'),
(530801, 381542, 508419, 25.00, 30.00, 12.00, 7.00, '2025-10-21 07:44:05'),
(647295, 573013, 411669, 28.00, 37.00, 18.00, 9.00, '2025-10-21 07:44:05'),
(934478, 687440, 411669, 27.00, 32.00, 17.00, 9.00, '2025-10-21 07:44:05'),
(525432, 740089, 411669, 20.00, 30.00, 16.00, 6.00, '2025-10-21 07:44:05'),
(623833, 381542, 411669, 24.00, 33.00, 18.00, 8.00, '2025-10-21 07:44:05'),
(630610, 882964, 177075, 27.00, 36.00, 17.00, 7.00, '2025-10-21 07:44:09'),
(455413, 517505, 177075, 24.00, 35.00, 18.00, 7.00, '2025-10-21 07:44:09'),
(185177, 304359, 177075, 27.00, 38.00, 19.00, 10.00, '2025-10-21 07:44:09'),
(330617, 533096, 177075, 23.00, 34.00, 17.00, 7.00, '2025-10-21 07:44:09'),
(759921, 381542, 177075, 28.00, 38.00, 18.00, 8.00, '2025-10-21 07:44:09'),
(189082, 565984, 177075, 26.00, 36.00, 18.00, 8.00, '2025-10-21 07:44:09'),
(896890, 740089, 177075, 23.00, 33.00, 17.00, 8.00, '2025-10-21 07:44:09'),
(910997, 573013, 177075, 29.00, 38.00, 18.00, 9.00, '2025-10-21 07:44:09'),
(905214, 760869, 177075, 29.00, 38.00, 17.00, 9.00, '2025-10-21 07:44:09'),
(494954, 687440, 177075, 24.00, 36.00, 16.00, 8.00, '2025-10-21 07:44:09');

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

--
-- Dumping data for table `uniform_score`
--

INSERT INTO `uniform_score` (`score_id`, `cand_id`, `poise_and_bearings`, `personality_and_projection`, `neatness`, `overall_impact`, `created_at`) VALUES
(757955, 304359, 30.00, 20.00, 20.00, 10.00, '2025-10-23 04:11:50'),
(446696, 882964, 10.00, 10.00, 10.00, 10.00, '2025-10-23 06:43:52'),
(798811, 192505, 30.00, 20.00, 20.00, 10.00, '2025-10-23 07:09:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('judge','admin') DEFAULT 'judge',
  `has_submitted` tinyint(1) DEFAULT 0,
  `has_agreed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `has_submitted`, `has_agreed`, `created_at`) VALUES
(508419, 'judge1', '$2y$10$JvoD5EPlEm8vTswrZgTn5uLWSCI0rGU5PhQCuA97bRpJrkewCYBV2', 'judge', 0, 0, '2025-10-21 06:20:50'),
(411669, 'judge2', '$2y$10$UgeyrSkvizX9o4Dd9cD7F.B8lg4fm.7oauxZJRPgxk6Bpdi9TO6PK', 'judge', 0, 0, '2025-10-21 06:20:56'),
(177075, 'judge3', '$2y$10$EhVRcRp81wcnPewMFEYP8ehdBLkhsDoM/v5UzmZjBlkKHTAPjKucm', 'judge', 0, 0, '2025-10-21 06:21:03'),
(725587, 'admin1', '$2y$10$ViU8QY5K3gQULR.bdS1VxeWb2WDd1W5itsFdNMd4nT00M3qTK7T1y', 'admin', 0, 0, '2025-10-21 06:46:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contestants`
--
ALTER TABLE `contestants`
  ADD PRIMARY KEY (`cand_id`);

--
-- Indexes for table `final_score`
--
ALTER TABLE `final_score`
  ADD PRIMARY KEY (`cand_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contestants`
--
ALTER TABLE `contestants`
  MODIFY `cand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=882965;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
