-- Create top_5_qna_score table for individual judge scores
CREATE TABLE `top_5_qna_score` (
  `score_id` int(11) NOT NULL,
  `cand_id` int(11) NOT NULL,
  `judge_id` int(11) NOT NULL,
  `qna` decimal(5,2) NOT NULL,
  `beauty` decimal(5,2) NOT NULL,
  `total_score` decimal(5,2) GENERATED ALWAYS AS (`qna` + `beauty`) STORED,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`score_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create top_5_final table for final scores
CREATE TABLE `top_5_final` (
  `cand_id` int(11) NOT NULL,
  `final_score` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`cand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
