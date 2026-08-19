-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2026 at 02:30 PM
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
-- Database: `lost_found_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `post_type` enum('Lost','Found') NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(200) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `status` enum('Active','Resolved') DEFAULT 'Active',
  `posted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `user_id`, `post_type`, `item_name`, `description`, `location`, `image_path`, `status`, `posted_at`) VALUES
(1, 1, 'Lost', 'Blue Water Bottle', 'Blue Cello water bottle, 1 litre, has name sticker.', 'Library Reading Room', NULL, 'Active', '2026-04-16 17:00:33'),
(2, 1, 'Found', 'Scientific Calculator', 'Casio fx-991ES Plus found near Lab 2 benches.', 'Lab 2', NULL, 'Active', '2026-04-16 17:00:33'),
(3, 2, 'Lost', 'Black Umbrella', 'Black fold-up umbrella with red handle, lost during rain.', 'Canteen', NULL, 'Active', '2026-04-16 17:00:33'),
(4, 2, 'Found', 'Student ID Card', 'Found an ID card near the main gate. Owner may collect.', 'Main Gate', NULL, 'Active', '2026-04-16 17:00:33'),
(5, 3, 'Lost', 'WALLET', 'i have lost my wallet if anyone has got it call me', 'at nd desai', 'uploads/item_1776339560_3.png', 'Resolved', '2026-04-16 17:09:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `contact_no`, `created_at`) VALUES
(1, 'Rahul Sharma', 'rahul@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9876543210', '2026-04-16 17:00:33'),
(2, 'Priya Patel', 'priya@college.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9123456780', '2026-04-16 17:00:33'),
(3, 'Prit Parekh', '25ceubs081@ddu.ac.in', '$2y$10$HfU7q205Ci9UKCIO.f.1ZeddBnL2ma7qWBzkUrkyaeAohckPOwAvi', '9173876874', '2026-04-16 17:05:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_items_status` (`status`),
  ADD KEY `idx_items_posttype` (`post_type`),
  ADD KEY `idx_items_userid` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
