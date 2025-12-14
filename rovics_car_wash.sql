-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 14, 2025 at 03:23 PM
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
-- Database: `rovics_car_wash`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `service_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `staff_assigned` varchar(255) DEFAULT 'Unassigned',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `service_id`, `service_name`, `service_price`, `notes`, `status`, `staff_assigned`, `created_at`, `started_at`, `completed_at`, `cancelled_at`) VALUES
(1, 4, 2, 'Premium Wash', 600.00, 'Sports Car', 'cancelled', 'Unassigned', '2025-10-28 03:28:20', NULL, NULL, '2025-10-28 03:54:41'),
(2, 5, 3, 'Interior Detailing', 1500.00, 'Toyota', 'completed', 'Kyle', '2025-10-28 03:29:06', '2025-11-04 07:46:53', '2025-11-04 07:47:31', NULL),
(3, 4, 1, 'Basic Wash', 350.00, 'Toyota car', 'completed', 'kyle', '2025-11-04 07:46:02', '2025-12-14 09:45:15', '2025-12-14 09:45:17', NULL),
(4, 4, 3, 'Interior Detailing', 1500.00, 'toyota', 'completed', 'eian', '2025-12-14 09:46:06', '2025-12-14 09:55:17', '2025-12-14 09:55:20', NULL),
(5, 4, 2, 'Premium Wash', 600.00, 'Toyota', 'cancelled', 'Unassigned', '2025-12-14 10:16:31', NULL, NULL, '2025-12-14 10:17:07'),
(6, 4, 1, 'Basic Wash', 350.00, '1123', 'completed', 'Soma', '2025-12-14 10:17:40', '2025-12-14 10:18:13', '2025-12-14 10:18:17', NULL),
(7, 4, 1, 'Basic Wash', 350.00, '1234', 'completed', 'nif', '2025-12-14 10:29:02', '2025-12-14 10:30:34', '2025-12-14 10:30:42', NULL),
(8, 4, 3, 'Interior Detailing', 1500.00, 'af', 'completed', 'nig', '2025-12-14 10:31:01', '2025-12-14 10:31:24', '2025-12-14 10:31:32', NULL),
(9, 4, 1, 'Basic Wash', 350.00, 'hyjy', 'completed', 'io', '2025-12-14 10:42:57', '2025-12-14 10:43:41', '2025-12-14 10:43:44', NULL),
(10, 4, 3, 'Interior Detailing', 1500.00, 'juj', 'completed', 'rika', '2025-12-14 10:51:35', '2025-12-14 10:52:08', '2025-12-14 10:52:12', NULL),
(11, 6, 6, 'Super wash', 12222.00, 'wow', 'completed', 'Admin', '2025-12-14 10:53:40', '2025-12-14 10:53:58', '2025-12-14 10:54:08', NULL),
(12, 6, 3, 'Interior Detailing', 1500.00, 'kik', 'completed', 'Admin', '2025-12-14 11:02:46', '2025-12-14 11:03:29', '2025-12-14 11:03:34', NULL),
(13, 4, 1, 'Basic Wash', 350.00, 'ioi', 'completed', 'qwert', '2025-12-14 11:36:49', '2025-12-14 11:38:59', '2025-12-14 11:39:00', NULL),
(14, 4, 3, 'Interior Detailing', 1500.00, '23', 'completed', 'qwert', '2025-12-14 12:09:51', '2025-12-14 12:10:06', '2025-12-14 12:10:06', NULL),
(15, 4, 1, 'Basic Wash', 350.00, 'pop', 'pending', 'Unassigned', '2025-12-14 12:51:13', NULL, NULL, NULL),
(16, 4, 3, 'Interior Detailing', 1500.00, '', 'completed', 'qwert', '2025-12-14 14:10:48', '2025-12-14 14:11:22', '2025-12-14 14:11:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'New Booking: Basic Wash', 0, '2025-12-14 12:51:13'),
(2, 2, 'New Booking: Basic Wash', 1, '2025-12-14 12:51:13'),
(3, 7, 'New Booking: Basic Wash', 0, '2025-12-14 12:51:13'),
(4, 1, 'New Booking: Interior Detailing', 0, '2025-12-14 14:10:48'),
(5, 2, 'New Booking: Interior Detailing', 1, '2025-12-14 14:10:48'),
(6, 7, 'New Booking: Interior Detailing', 0, '2025-12-14 14:10:48'),
(7, 4, 'Your wash (Interior Detailing) is now in progress.', 1, '2025-12-14 14:11:22'),
(8, 4, 'Your wash (Interior Detailing) is now completed.', 1, '2025-12-14 14:11:32');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `image_path`) VALUES
(1, 'Basic Wash', 'Exterior wash and dry.', 350.00, 'uploads/svc_693eb007a7471.png'),
(2, 'Premium Wash', 'Basic wash plus interior vacuum and tire shine.', 600.00, 'uploads/svc_693eb00f184c4.png'),
(3, 'Interior Detailing', 'Deep clean of all interior surfaces, seats, and carpets.', 1500.00, 'uploads/svc_693eb015ad229.png'),
(6, 'Super wash', '', 12222.00, 'uploads/svc_693e8ca1e9ed0.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('last_reset', '2025-12-14 20:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('customer','admin','staff') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$7R.E.N.V.Q.M/fg.m1C7YuO7m9L8z.61U/zG2.SOwG.1.S3BwG', 'admin', '2025-10-28 12:00:00'),
(2, 'Soma', 'Soma@gmail.com', '$2y$10$UkGN7JSKRpJXUFIVIKlVB.OuXmM8Ym64uJEmiXeviROMBvj6LQ1g2', 'admin', '2025-10-28 03:04:31'),
(4, 'Lol', 'Lol@gmail.com', '$2y$10$nIfEfuUzjftws.hq/p5qAONiHrteQTm/PCKvO0E0YMZN04b3Lv0Ri', 'customer', '2025-10-28 03:24:25'),
(5, 'number', '12345@gmail.com', '$2y$10$.piXAcBLCYCKCKy7baT1zuYWzwTWoc.uFnvESUAjHb3J09OwmifA2', 'customer', '2025-10-28 03:28:54'),
(6, 'nig', 'nig@gmail.com', '$2y$10$loVmWOw0Aa4feMqdkbktU.gcUFbjPusgOJStx3SXUjI/Xye2vVSjS', 'customer', '2025-12-14 10:53:10'),
(7, 'qwert', 'qwert@gmail.com', '$2y$10$3pc1KI0UtHxksz423E2pCuw8F0t6aMPt7Tdc3j0hAw6wdWiCfIHCm', 'staff', '2025-12-14 11:36:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
