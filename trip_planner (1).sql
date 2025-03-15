-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 10:43 AM
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
(51, 16, 'Fairmont Royal York', 'Toronto, Canada', 250.00, 'Free Wi-Fi, Pool, Gym, Breakfast', '00:16:00', '22:16:00', '2025-03-05 18:16:15', '2025-03-05 18:16:15', 3);

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
(5, 16, 'Flight', 'Air Canada	', 'New York, USA', 'Toronto, Canada', '2025-03-10 00:00:00', '2025-03-16 00:00:00', 'AC12345', 3, 4000.00);

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
(15, 'Japan Summer Trip 2025\"', 2, '2025-03-08', '2025-03-11', 5000.00),
(16, 'Canada Summer Trip 2025', 3, '2025-03-14', '2025-03-14', 20000.00);

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
(20, 3, 16, 'Transport', 750.00, 'USD', 'Fairmont Royal York Hotel', '2025-03-07');

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
(3, 16, ' Day 1: Vancouver', 'Vancouver, British Columbia', 'Explore Vancouver\'s vibrant cultural scene, including Indigenous art galleries and cultural centers.', '2025-03-17', '2025-02-27 21:17:07', '2025-03-05 18:07:02'),
(6, 16, 'Day:2 Whistler', 'Explore Whistler Village and engage in outdoor activities.', 'Whistler, British Columbia', '2025-03-16', '2025-03-05 18:07:41', '2025-03-05 18:07:52');

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
(27, 16, 8, 'accepted', '2025-03-15 08:38:12', '2025-03-15 07:38:12', '2025-03-15 02:38:12'),
(28, 16, 7, 'accepted', '2025-03-15 08:42:14', '2025-03-15 07:42:14', '2025-03-15 02:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `trip_reviews`
--

CREATE TABLE `trip_reviews` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_reviews`
--

INSERT INTO `trip_reviews` (`id`, `trip_id`, `user_id`, `rating`, `review_text`, `created_at`, `updated_at`) VALUES
(1, 16, 8, 3, 'Adipisci sed molliti', '2025-03-15 07:40:26', '2025-03-15 07:40:26'),
(2, 16, 7, 4, 'Nice ', '2025-03-15 07:54:48', '2025-03-15 07:54:48');

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
(6, 'New User', 'hovev31057@jomspar.com', '$2y$10$RsdNmyTKmov6AH538V9greLFiCZrSnwq0yrTSP1.S0Eq6QhNk70Xa', 'user', '2025-02-28 17:55:15', 1, '0000-00-00 00:00:00', '167785', '2025-03-12 16:09:52'),
(7, 'New Participant', 'posex52211@hartaria.com', '$2y$10$737LXIoIr7SWV1.qf0niSeYGaV/PHqwPTYqwcA1W0tfZ9v3Prjbgi', 'participant', '2025-03-04 20:04:30', 1, '0000-00-00 00:00:00', '650900', '2025-03-04 20:04:30'),
(8, 'Trip Participant', 'govih40059@jomspar.com', '$2y$10$ObQWAs.GPrir0akSk3xz0Oi/KhNDEE.xXhNgTp6B.WiSVswlwdZe2', 'participant', '2025-03-05 08:55:44', 1, '0000-00-00 00:00:00', '265221', '2025-03-05 08:56:45'),
(9, 'Trip Participant 2', 'hikoj10247@jomspar.com', '$2y$10$bB5ktkU8s.X107PuJNXER.BPJJtp3ycUGHBmL9uD4FLGattAauKhS', 'participant', '2025-03-05 09:00:16', 1, '0000-00-00 00:00:00', '523976', '2025-03-05 09:00:16'),
(11, 'yyyy', 'wofed66083@dwriters.com', '$2y$10$9pMwvjIBPwNvjP5UDbPOAuu9zUqodRxksTwiYXevJ7B6Vgr8qzYja', 'participant', '2025-03-09 16:17:34', 1, '0000-00-00 00:00:00', '852620', '2025-03-09 16:17:34');

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
-- Indexes for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `trip_invitations`
--
ALTER TABLE `trip_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `trip_participants`
--
ALTER TABLE `trip_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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

--
-- Constraints for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  ADD CONSTRAINT `trip_reviews_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `trip_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
