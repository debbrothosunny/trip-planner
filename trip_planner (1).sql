-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2025 at 01:11 PM
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
-- Database: `trip_planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodations`
--

CREATE TABLE `accommodations` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `amenities` text DEFAULT NULL,
  `check_in_time` time NOT NULL,
  `check_out_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`id`, `trip_id`, `name`, `location`, `price`, `amenities`, `check_in_time`, `check_out_time`, `created_at`, `updated_at`, `user_id`) VALUES
(48, 15, 'Grand Plaza Hotel', 'New York City, USA', 1200.00, 'N/A', '23:07:00', '11:07:00', '2025-02-28 17:07:38', '2025-02-28 17:07:38', 2),
(50, 16, 'Grand Plaza Hotel', 'New York City, USA', 1200.00, 'N/A', '14:22:00', '15:22:00', '2025-03-05 08:22:41', '2025-03-05 08:22:41', 3);

-- --------------------------------------------------------

--
-- Table structure for table `transportation`
--

CREATE TABLE `transportation` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `departure_location` varchar(255) NOT NULL,
  `arrival_location` varchar(255) NOT NULL,
  `departure_date` datetime NOT NULL,
  `arrival_date` datetime NOT NULL,
  `booking_reference` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transportation`
--

INSERT INTO `transportation` (`id`, `trip_id`, `type`, `company_name`, `departure_location`, `arrival_location`, `departure_date`, `arrival_date`, `booking_reference`, `user_id`, `amount`) VALUES
(2, 15, 'Flight', 'Airways Company', 'New York', 'London', '2025-02-28 00:00:00', '2025-03-01 00:00:00', 'ABC12345', 2, 5000.00),
(4, 16, 'Flight', 'Airways Company', 'New York', 'London', '2025-03-06 00:00:00', '2025-03-07 00:00:00', 'ABC12345', 3, 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `budget` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `name`, `user_id`, `start_date`, `end_date`, `budget`) VALUES
(15, 'Japan Summer Trip 2025\"', 2, '2025-02-28', '2025-03-20', 5000.00),
(16, 'canada Summer Trip 2025', 3, '2025-03-14', '2025-03-20', 20000.00);

-- --------------------------------------------------------

--
-- Table structure for table `trip_expenses`
--

CREATE TABLE `trip_expenses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `expense_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_expenses`
--

INSERT INTO `trip_expenses` (`id`, `user_id`, `trip_id`, `category`, `amount`, `currency`, `description`, `expense_date`) VALUES
(10, 17, 12, 'Activities', 17.00, 'USD', 'Aliquip quaerat cumq', '2008-04-26'),
(17, 2, 15, 'Food', 2500.00, 'USD', 'N/A', '2025-02-28'),
(18, 6, 19, 'Transport', 2500.00, 'USD', 'N/A', '2025-03-12'),
(19, 3, 16, 'Transport', 2500.00, 'USD', 'N/A', '2025-03-20');

-- --------------------------------------------------------

--
-- Table structure for table `trip_invitations`
--

CREATE TABLE `trip_invitations` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `inviter_id` int(11) NOT NULL,
  `invitee_email` varchar(255) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trip_itineraries`
--

CREATE TABLE `trip_itineraries` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `day_title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `itinerary_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_itineraries`
--

INSERT INTO `trip_itineraries` (`id`, `trip_id`, `day_title`, `location`, `description`, `itinerary_date`, `created_at`, `updated_at`) VALUES
(2, 15, 'Arrival & Check-in', 'New York City, USA', 'Arrive at the destination, check into the hotel, and relax.', '2025-03-10', '2025-02-27 18:33:54', '2025-02-27 18:33:54'),
(3, 16, 'Arrival & Check-in', 'New York City, USA', 'N/A', '2025-02-28', '2025-02-27 21:17:07', '2025-02-27 21:17:07');

-- --------------------------------------------------------

--
-- Table structure for table `trip_participants`
--

CREATE TABLE `trip_participants` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `responded_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_participants`
--

INSERT INTO `trip_participants` (`id`, `trip_id`, `user_id`, `status`, `responded_at`, `created_at`, `updated_at`) VALUES
(10, 16, 7, 'accepted', '2025-03-05 09:28:47', '2025-03-05 08:23:57', '2025-03-05 03:28:47'),
(11, 15, 7, 'accepted', '2025-03-05 09:30:34', '2025-03-05 08:30:34', '2025-03-05 03:30:34'),
(12, 16, 9, 'accepted', '2025-03-05 10:01:32', '2025-03-05 09:01:32', '2025-03-05 04:01:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','participant','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `otp_expiry` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(6) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `is_verified`, `otp_expiry`, `verification_token`, `updated_at`) VALUES
(2, 'Sunny', 'debnathsunny7852@gmail.com', '$2y$10$9dPAZ51FzdJuLxyVJQmGxup0d8TzgFs30zeANRMU2C5ltzt3Ew2fe', 'admin', '2025-02-27 18:27:37', 1, '0000-00-00 00:00:00', '989281', '2025-03-04 15:12:38'),
(3, 'purna', 'purnadebosree@gmail.com', '$2y$10$nPA/VBvTxvHq4JuuuN4IRuF9FQCvZZfKUOY5oV66g8lAkxyTiYMe2', 'user', '2025-02-27 19:03:48', 1, '0000-00-00 00:00:00', '358178', '2025-02-27 19:03:48'),
(6, 'Unknown', 'hovev31057@jomspar.com', '$2y$10$Zmvm1WonmEEg84YhRB9q/.Rui4TLAnLRoltdYAKviuBqAzLlCaSd2', 'user', '2025-02-28 17:55:15', 1, '0000-00-00 00:00:00', '167785', '2025-03-05 07:58:56'),
(7, 'New Participant', 'posex52211@hartaria.com', '$2y$10$737LXIoIr7SWV1.qf0niSeYGaV/PHqwPTYqwcA1W0tfZ9v3Prjbgi', 'participant', '2025-03-04 20:04:30', 1, '0000-00-00 00:00:00', '650900', '2025-03-04 20:04:30'),
(8, 'Trip Participant', 'govih40059@jomspar.com', '$2y$10$ObQWAs.GPrir0akSk3xz0Oi/KhNDEE.xXhNgTp6B.WiSVswlwdZe2', 'participant', '2025-03-05 08:55:44', 1, '0000-00-00 00:00:00', '265221', '2025-03-05 08:56:45'),
(9, 'Trip Participant 2', 'hikoj10247@jomspar.com', '$2y$10$bB5ktkU8s.X107PuJNXER.BPJJtp3ycUGHBmL9uD4FLGattAauKhS', 'participant', '2025-03-05 09:00:16', 1, '0000-00-00 00:00:00', '523976', '2025-03-05 09:00:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `transportation`
--
ALTER TABLE `transportation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`user_id`);

--
-- Indexes for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trip_invitations`
--
ALTER TABLE `trip_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `inviter_id` (`inviter_id`);

--
-- Indexes for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `trip_participants`
--
ALTER TABLE `trip_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `trip_invitations`
--
ALTER TABLE `trip_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `trip_participants`
--
ALTER TABLE `trip_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD CONSTRAINT `accommodations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transportation`
--
ALTER TABLE `transportation`
  ADD CONSTRAINT `transportation_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transportation_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  ADD CONSTRAINT `trip_expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `trip_invitations`
--
ALTER TABLE `trip_invitations`
  ADD CONSTRAINT `trip_invitations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_invitations_ibfk_2` FOREIGN KEY (`inviter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  ADD CONSTRAINT `trip_itineraries_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_participants`
--
ALTER TABLE `trip_participants`
  ADD CONSTRAINT `trip_participants_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
