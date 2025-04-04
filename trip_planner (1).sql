-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 03:49 PM
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
  `user_id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trip_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`id`, `user_id`, `hotel_id`, `room_type`, `check_in_date`, `check_out_date`, `status`, `created_at`, `updated_at`, `trip_id`) VALUES
(9, 3, 1, 'Delux', '2025-04-03', '2025-04-04', '0', '2025-04-03 16:44:39', '2025-04-03 16:44:39', 23),
(11, 6, 2, 'Single Bed', '2025-04-13', '2025-04-15', '0', '2025-04-03 17:08:50', '2025-04-03 17:08:50', 25);

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `name`, `location`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Fairmont Royal York', 'Dhaka', 'N/A', '2025-03-21 18:23:46', '2025-03-26 08:45:40'),
(2, 'Rose View ', 'Sylhet', 'N/A', '2025-03-22 07:33:00', '2025-03-31 10:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

CREATE TABLE `hotel_rooms` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_type` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_rooms` int(11) NOT NULL,
  `available_rooms` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`id`, `hotel_id`, `room_type`, `price`, `total_rooms`, `available_rooms`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'AC but Single Bed', 500.00, 100, 100, 'Facilities: WiFi, Air Conditioning, Television.', '2025-03-20 09:27:02', '2025-03-23 12:21:39'),
(4, 2, 'Single Bed', 10.99, 100, 100, 'N/A', '2025-03-22 12:49:44', '2025-03-22 12:49:44'),
(5, 1, 'Delux', 800.00, 100, 100, 'N/A', '2025-03-23 16:32:27', '2025-03-24 13:40:06');

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_edit_requests`
--

CREATE TABLE `itinerary_edit_requests` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `itinerary_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itinerary_edit_requests`
--

INSERT INTO `itinerary_edit_requests` (`id`, `trip_id`, `itinerary_id`, `user_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 23, 1, 7, 'pending', 'Hello', '2025-04-03 13:46:56', '2025-04-04 13:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `trip_id`, `amount`, `payment_method`, `transaction_id`, `payment_status`, `created_at`) VALUES
(14, 9, 23, 20000.00, 'bkash', 'sunny123', 'completed', '2025-03-19 02:22:18'),
(15, 11, 23, 20000.00, 'nagad', 'Particpant123', 'pending', '2025-03-25 10:05:02');

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
(50, 23, 'Car', 'Japan Airlines', 'New York', 'London', '2025-04-04 00:00:00', '2025-04-04 00:00:00', 'ABC12345', 3, 400000.00);

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
(23, '\"Japan Summer Trip 2025\"', 3, '2025-03-31', '2025-04-28', 20000.00),
(25, 'Canada Winter Trip 2025', 6, '2025-03-20', '2025-03-28', 25000.00);

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
(20, 3, 16, 'Transport', 750.00, 'USD', 'Fairmont Royal York Hotel', '2025-03-07'),
(31, 3, 23, 'Accommodation', 920.00, 'USD', 'Et excepturi illo in', '2008-11-19'),
(33, 3, 53, 'Transport', 59.00, 'USD', 'Deserunt vitae quae ', '2021-02-04');

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
(1, 23, 'Day 1: Arrival at Beach Resorts', 'Beach Resort, Maldivesss', 'Arrive at the Maldives, relax on the beach, and enjoy water sports.', '2025-03-31', '2025-03-20 09:30:43', '2025-04-04 12:25:47');

-- --------------------------------------------------------

--
-- Table structure for table `trip_participants`
--

CREATE TABLE `trip_participants` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL,
  `responded_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_participants`
--

INSERT INTO `trip_participants` (`id`, `trip_id`, `user_id`, `status`, `responded_at`, `created_at`, `updated_at`) VALUES
(3, 23, 7, 'accepted', '2025-03-18 18:24:36', '2025-03-18 17:24:36', '2025-03-19 14:14:22'),
(4, 23, 9, 'accepted', '2025-03-19 07:51:50', '2025-03-19 06:51:50', '2025-03-19 14:14:01'),
(5, 25, 9, 'pending', '2025-03-19 15:58:38', '2025-03-19 14:58:38', '2025-03-19 14:59:05'),
(6, 25, 7, 'pending', '2025-03-24 11:41:43', '2025-03-24 10:41:43', '2025-03-24 10:42:20'),
(7, 23, 11, 'accepted', '2025-03-25 16:04:40', '2025-03-25 15:04:40', '2025-03-25 10:04:40'),
(8, 23, 8, 'pending', '2025-03-31 12:32:23', '2025-03-31 10:32:23', '2025-03-31 10:34:12');

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
(2, 16, 7, 4, 'Nice ', '2025-03-15 07:54:48', '2025-03-15 07:54:48'),
(3, 16, 7, 1, 'fvdvdfg', '2025-03-17 14:54:18', '2025-03-17 14:54:18'),
(4, 23, 7, 5, 'Hello', '2025-03-18 17:26:30', '2025-03-18 17:26:30');

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
(3, 'purna', 'purnadebosree@gmail.com', '$2y$10$nPA/VBvTxvHq4JuuuN4IRuF9FQCvZZfKUOY5oV66g8lAkxyTiYMe2', 'user', '2025-02-27 19:03:48', 1, '0000-00-00 00:00:00', '358178', '2025-03-18 17:22:27'),
(6, 'New User', 'hovev31057@jomspar.com', '$2y$10$RsdNmyTKmov6AH538V9greLFiCZrSnwq0yrTSP1.S0Eq6QhNk70Xa', 'user', '2025-02-28 17:55:15', 1, '0000-00-00 00:00:00', '167785', '2025-03-12 16:09:52'),
(7, 'New Participant', 'po52211@hartaria.com', '$2y$10$Pn85iYm0nmgrMp0PnvDmDOvyNaZMUCBwF7rMGhjps/PDTOJZ4EOV6', 'participant', '2025-03-04 20:04:30', 1, '0000-00-00 00:00:00', '650900', '2025-03-25 11:07:31'),
(8, 'Trip Participant', 'govih40059@jomspar.com', '$2y$10$ObQWAs.GPrir0akSk3xz0Oi/KhNDEE.xXhNgTp6B.WiSVswlwdZe2', 'participant', '2025-03-05 08:55:44', 1, '0000-00-00 00:00:00', '265221', '2025-03-05 08:56:45'),
(9, 'Trip Participant 2', 'hikoj10247@jomspar.com', '$2y$10$bB5ktkU8s.X107PuJNXER.BPJJtp3ycUGHBmL9uD4FLGattAauKhS', 'participant', '2025-03-05 09:00:16', 1, '0000-00-00 00:00:00', '523976', '2025-03-05 09:00:16'),
(11, 'yyyy', 'wofed66083@dwriters.com', '$2y$10$9pMwvjIBPwNvjP5UDbPOAuu9zUqodRxksTwiYXevJ7B6Vgr8qzYja', 'participant', '2025-03-09 16:17:34', 1, '0000-00-00 00:00:00', '852620', '2025-03-09 16:17:34'),
(12, 'user', 'secoge2611@oronny.com', '$2y$10$mE7VWbdwq32N3bkjsjOAvuUjbcUwykjhPquEYcwU6JQMlSLRCo/Ti', 'user', '2025-03-30 14:37:36', 1, '0000-00-00 00:00:00', '208261', '2025-03-30 14:37:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `trip_id` (`trip_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`);

--
-- Indexes for table `itinerary_edit_requests`
--
ALTER TABLE `itinerary_edit_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `itinerary_id` (`itinerary_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `itinerary_edit_requests`
--
ALTER TABLE `itinerary_edit_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `trip_invitations`
--
ALTER TABLE `trip_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `trip_participants`
--
ALTER TABLE `trip_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD CONSTRAINT `accommodations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodations_ibfk_2` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `accommodations_ibfk_3` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `itinerary_edit_requests`
--
ALTER TABLE `itinerary_edit_requests`
  ADD CONSTRAINT `itinerary_edit_requests_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerary_edit_requests_ibfk_2` FOREIGN KEY (`itinerary_id`) REFERENCES `trip_itineraries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `itinerary_edit_requests_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`);

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
  ADD CONSTRAINT `trip_participants_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `trip_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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
