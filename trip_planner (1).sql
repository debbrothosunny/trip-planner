-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 11:20 AM
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
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `trip_id` int(11) UNSIGNED DEFAULT NULL,
  `hotel_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` datetime NOT NULL,
  `check_out_date` datetime NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`id`, `user_id`, `trip_id`, `hotel_id`, `room_id`, `check_in_date`, `check_out_date`, `price`, `booking_date`, `status`, `created_at`, `updated_at`) VALUES
(6, 18, 5, 3, 6, '2025-05-09 23:21:00', '2025-05-10 23:21:00', 16.99, '2025-05-08 17:22:00', 1, '2025-05-08 17:22:00', '2025-05-08 17:22:00');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Bangladesh', 0, '2025-04-20 19:48:46', '2025-05-08 07:38:23'),
(2, 'India', 0, '2025-04-21 12:23:39', '2025-05-08 07:46:12');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(10) UNSIGNED NOT NULL,
  `following_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `follower_id`, `following_id`, `created_at`) VALUES
(8, 21, 18, '2025-05-03 15:28:33');

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `state_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `star_rating` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO `hotels` (`id`, `country_id`, `state_id`, `name`, `address`, `description`, `star_rating`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 'Radisson Blu Hotel', ' National Highway 37, Guwahati, Assam', 'Set in a tranquil sector of Assam, the elegant Radisson Blu Hotel Guwahati provides hotel guests with stellar amenities and a restful location outside of the city bustle. Our convenient location on NH-37 makes transportation around the city a breeze with easy access to Lokpriya Gopinath Bordoloi International Airport (GAU) and the nearby railway station. When you&#039;re ready to explore, view the local wildlife at Deepor Beel or visit cultural sites including Hindu temples and museums like Kamakhya Temple, Purva Tirupati Shri Balaji Temple and Sankardev Kalakshetra.', 5, 0, '2025-04-21 12:24:57', '2025-05-08 17:34:29'),
(3, 1, 3, 'Rose View', 'Sylhet,Uposhohor', 'N/A', 4, 0, '2025-04-24 09:05:51', '2025-05-07 15:20:11');

-- --------------------------------------------------------

--
-- Table structure for table `hotel_rooms`
--

CREATE TABLE `hotel_rooms` (
  `id` int(11) UNSIGNED NOT NULL,
  `hotel_id` int(11) UNSIGNED NOT NULL,
  `room_type_id` int(11) UNSIGNED DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `total_rooms` int(11) NOT NULL DEFAULT 1,
  `available_rooms` int(11) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Pending',
  `amenities` text DEFAULT NULL COMMENT 'e.g., JSON array of amenities',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hotel_rooms`
--

INSERT INTO `hotel_rooms` (`id`, `hotel_id`, `room_type_id`, `capacity`, `price`, `description`, `total_rooms`, `available_rooms`, `status`, `amenities`, `created_at`, `updated_at`) VALUES
(4, 2, 1, 2, 208.00, 'Indulge in the comfort and spaciousness of our Deluxe Room. Thoughtfully designed with your relaxation in mind, this room offers a generous layout, plush furnishings, and a tranquil ambiance. Enjoy premium bedding for a restful night&#039;s sleep and ample natural light streaming through large windows.', 100, 100, 0, 'King-sized bed with premium bedding\r\nComplimentary high-speed Wi-Fi\r\nLarge flat-screen TV with cable channels\r\nIndividually controlled air conditioning\r\nMini-refrigerator\r\nCoffee and tea maker\r\nWork desk with ergonomic chair\r\nPrivate bathroom with shower\r\nComplimentary toiletries\r\nHair dryer\r\nIn-room safe', '2025-04-23 18:32:16', '2025-05-08 17:37:15'),
(6, 3, 1, 4, 16.99, 'Our Deluxe Room offers a perfect blend of comfort and modern convenience. Unwind in a stylish setting with comfortable seating areas and enjoy features like a large flat-screen TV, high-speed Wi-Fi, and individual climate control. Experience a restful night\'s sleep and wake up refreshed in our thoughtfully designed space.', 100, 100, 0, 'King-sized bed with premium bedding\nComplimentary high-speed Wi-Fi\nLarge flat-screen TV with cable channels\nIndividually controlled air conditioning\nMini-refrigerator\nCoffee and tea maker\nWork desk with ergonomic chair\nPrivate bathroom with shower\nComplimentary toiletries\nHair dryer\nIn-room safe', '2025-04-24 12:52:38', '2025-05-08 17:27:39'),
(7, 3, 2, 10, 995.00, 'Dolore adipisci sed ', 4, 85, 0, 'Non omnis nobis quos', '2025-05-08 07:50:43', '2025-05-08 08:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` int(10) UNSIGNED NOT NULL,
  `trip_id` int(10) UNSIGNED NOT NULL,
  `inviter_user_id` int(10) UNSIGNED NOT NULL,
  `invitation_code` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `invited_user_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invitations`
--

INSERT INTO `invitations` (`id`, `trip_id`, `inviter_user_id`, `invitation_code`, `created_at`, `expires_at`, `status`, `invited_user_id`) VALUES
(8, 5, 35, 'INV_681522bb87bc05.91715640', '2025-05-02 15:53:31', NULL, 'pending', NULL),
(9, 5, 35, 'INV_681522d49297e2.18918779', '2025-05-02 15:53:56', NULL, 'pending', NULL),
(10, 5, 35, 'INV_681523ed35b456.95681300', '2025-05-02 15:58:37', NULL, 'pending', NULL),
(11, 5, 35, 'INV_68152409dd3062.97262033', '2025-05-02 15:59:05', NULL, 'pending', NULL),
(12, 5, 35, 'INV_68152413f355c7.42927299', '2025-05-02 15:59:15', NULL, 'pending', NULL),
(13, 5, 35, 'INV_6815caf87e9677.64040872', '2025-05-03 03:51:20', NULL, 'pending', NULL),
(14, 5, 35, 'INV_6815d1cacc3328.05306409', '2025-05-03 04:20:26', NULL, 'pending', NULL),
(15, 5, 34, 'INV_6818ec5abcbfa2.39454852', '2025-05-05 12:50:34', NULL, 'pending', NULL),
(16, 5, 34, 'INV_6818eca46513a6.56865307', '2025-05-05 12:51:48', NULL, 'pending', NULL),
(17, 5, 34, 'INV_6818ed0b623ed4.71062514', '2025-05-05 12:53:31', NULL, 'pending', NULL),
(18, 5, 34, 'INV_6818ed1dc16486.24232974', '2025-05-05 12:53:49', NULL, 'pending', NULL),
(19, 5, 21, 'INV_681d0bc6340455.36677658', '2025-05-08 15:53:42', NULL, 'pending', NULL),
(20, 5, 21, 'INV_681d0d98f09e26.74092376', '2025-05-08 16:01:28', NULL, 'pending', NULL),
(21, 5, 21, 'INV_681d0dab9bc548.39799986', '2025-05-08 16:01:47', NULL, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `itinerary_edit_requests`
--

CREATE TABLE `itinerary_edit_requests` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `itinerary_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `itinerary_edit_requests`
--

INSERT INTO `itinerary_edit_requests` (`id`, `trip_id`, `itinerary_id`, `user_id`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(6, 5, 2, 21, 'approved', 'XXXXXXX', '2025-04-30 13:40:17', '2025-04-30 13:41:30'),
(7, 5, 3, 21, 'rejected', 'yyyyyyyyy', '2025-04-30 13:46:33', '2025-04-30 13:46:43'),
(8, 5, 4, 21, 'rejected', 'xxxxxxxxxa', '2025-04-30 13:55:48', '2025-04-30 13:55:54'),
(9, 5, 5, 21, 'rejected', 'fffef', '2025-04-30 14:00:01', '2025-04-30 14:01:15');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `payment_gateway` varchar(255) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `payment_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 = completed, 1 = pending	 ',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payer_id` varchar(255) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `trip_id`, `payment_gateway`, `transaction_id`, `amount`, `currency`, `payment_status`, `payment_date`, `payer_id`, `payment_method`, `created_at`, `updated_at`) VALUES
(52, 21, 5, 'paypal', '8P162742XB974654L', 40000.00, 'USD', 0, '2025-05-08 15:59:30', 'KWX5E36QNRK98', 'paypal', '2025-05-08 19:59:30', '2025-05-08 20:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `trip_id` int(10) UNSIGNED NOT NULL,
  `itinerary_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `question` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `likes` int(11) DEFAULT 0,
  `liked_by` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`id`, `trip_id`, `itinerary_id`, `user_id`, `question`, `created_at`, `likes`, `liked_by`) VALUES
(15, 5, 5, 35, 'We are going to bisnakandhi.', '2025-05-05 14:22:40', 2, '21,34'),
(16, 5, 5, 21, 'dsasdd', '2025-05-08 20:02:13', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `room_types`
--

CREATE TABLE `room_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = active, 1 = pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_types`
--

INSERT INTO `room_types` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Delux', 0, '2025-04-20 19:50:37', '2025-04-30 17:21:31'),
(2, 'Suite', 0, '2025-04-21 10:10:57', '2025-04-23 16:42:31'),
(3, 'normal', 0, '2025-04-21 12:25:41', '2025-05-08 07:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `id` int(11) UNSIGNED NOT NULL,
  `country_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Active, 1 = Inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`id`, `country_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(2, 2, 'Assam', 0, '2025-04-21 12:24:04', '2025-04-24 09:02:58'),
(3, 1, 'Sylhet', 0, '2025-04-23 14:42:50', '2025-05-07 15:19:39');

-- --------------------------------------------------------

--
-- Table structure for table `transportations`
--

CREATE TABLE `transportations` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `departure_location` varchar(255) NOT NULL,
  `arrival_location` varchar(255) NOT NULL,
  `departure_date` date NOT NULL,
  `arrival_date` date NOT NULL,
  `booking_reference` varchar(255) DEFAULT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transportations`
--

INSERT INTO `transportations` (`id`, `trip_id`, `type`, `company_name`, `departure_location`, `arrival_location`, `departure_date`, `arrival_date`, `booking_reference`, `user_id`, `amount`, `created_at`, `updated_at`) VALUES
(1, 5, 'Flight', 'Biman Bangladesh Airlines', '[Origin City]', 'Dhaka (DAC)', '2025-04-30', '2025-04-30', 'ABC12345', 18, 300.00, '2025-04-27 08:53:32', '2025-04-27 08:53:32'),
(2, 5, 'Local', 'Various', 'N/A', 'N/A', '2025-05-01', '2025-05-01', 'N/A', 18, 50.00, '2025-04-27 08:56:20', '2025-04-27 08:56:20'),
(3, 5, 'Bus', '[Rental Co./Bus]	', 'Dhaka', 'Sonargaon', '2025-05-02', '2025-05-02', '[Sonargaon Ref]', 18, 40.00, '2025-04-27 08:58:09', '2025-04-27 08:58:09'),
(4, 5, 'Flight', 'Novoair', 'Dhaka (DAC)', 'Sylhet (ZYL)', '2025-05-03', '2025-05-03', '[Sylhet Flight Ref]', 18, 120.00, '2025-04-27 09:00:25', '2025-04-27 09:00:25'),
(5, 5, 'Local', '[Rental Co./Local]', 'Sylhet', 'Jaflong', '2025-05-04', '2025-05-04', '[Jaflong Ref]', 18, 10.00, '2025-04-27 09:03:28', '2025-04-27 09:03:28'),
(6, 5, 'Flight', 'US-Bangla Airlines	', 'Sylhet (ZYL)	', 'Dhaka (DAC)', '2025-05-05', '2025-05-05', 'ABC', 18, 120.00, '2025-04-27 09:06:12', '2025-04-27 09:06:12'),
(7, 5, 'Local', '[Transfer Co.]', '[Hotel/Location]	', 'Dhaka (DAC)', '2025-05-06', '2025-05-06', 'N/A', 18, 150.00, '2025-04-27 09:09:14', '2025-04-27 09:09:14'),
(17, 5, 'Bus', 'cc', 'asdd', 'sadsd', '2025-05-08', '2025-05-09', 'ABC12345', 18, 400000.00, '2025-05-07 16:08:51', '2025-05-07 16:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trip_style` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trips`
--

INSERT INTO `trips` (`id`, `user_id`, `name`, `start_date`, `end_date`, `budget`, `created_at`, `updated_at`, `trip_style`, `destination`) VALUES
(5, 18, 'A Cultural and Natural Exploration', '2025-05-15', '2025-05-20', 40000.00, '2025-04-27 08:20:11', '2025-05-10 09:19:53', 'Family', 'Bangladesh '),
(9, 18, '\"Canada Summer Trip 2025\"', '2025-05-10', '2025-05-18', 25000.00, '2025-04-29 18:31:11', '2025-05-08 19:31:43', 'Family', 'Canada'),
(18, 18, '\"Japan Summer Trip\"', '2025-05-07', '2025-05-23', 10000.00, '2025-05-02 09:32:59', '2025-05-08 19:34:33', 'Friends', 'Dubai'),
(42, 18, 'Sri Lanka Summer Trip 2025', '2025-05-21', '2025-05-31', 12000.00, '2025-05-10 09:17:13', '2025-05-10 09:20:29', 'Family', 'Sri Lanka');

-- --------------------------------------------------------

--
-- Table structure for table `trip_expenses`
--

CREATE TABLE `trip_expenses` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `description` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_expenses`
--

INSERT INTO `trip_expenses` (`id`, `user_id`, `trip_id`, `category`, `amount`, `currency`, `description`, `expense_date`, `created_at`, `updated_at`) VALUES
(1, 18, 5, 'Transport', 300.00, 'USD', 'Nobis ipsum irure a', '2025-04-30', '2025-04-27 11:42:54', '2025-04-30 15:01:27'),
(2, 18, 5, 'Accommodation', 300.00, 'USD', 'N/A', '2025-04-30', '2025-04-30 15:28:40', '2025-04-30 15:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `trip_itineraries`
--

CREATE TABLE `trip_itineraries` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) NOT NULL,
  `day_title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `itinerary_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_itineraries`
--

INSERT INTO `trip_itineraries` (`id`, `trip_id`, `day_title`, `location`, `description`, `itinerary_date`, `created_at`, `updated_at`, `image`) VALUES
(2, 5, 'Day 1: Arrival in Dhaka - The Bustling Capital', 'Dhaka', 'Morning: Continue exploring Dhaka: National Museum: Discover Bangladesh&#39;s rich history, art, and cultural heritage. Dhakeshwari Temple: An important Hindu temple and a significant historical site. Star Mosque (Tara Masjid): Admire its intricate mosaic work. Afternoon: Immerse yourself in the local crafts and shopping: New Market: A bustling marketplace offering a wide array of goods. Aarong: A popular store for ethically sourced handicrafts, clothing, and more. Evening: Consider attending a cultural performance or enjoying a relaxing evening at your hotel.', '2025-04-24', '2025-04-27 08:25:41', '2025-05-07 09:38:27', 'lake-lucerne-landscape-mountains-sunset-switzerland-3840x2160-49.jpg'),
(3, 5, 'Day 2: Dhaka\'s History and Culture', 'Dhaka', 'Morning: Continue exploring Dhaka:\r\nNational Museum: Discover Bangladesh\'s rich history, art, and cultural heritage.\r\nDhakeshwari Temple: An important Hindu temple and a significant historical site.\r\nStar Mosque (Tara Masjid): Admire its intricate mosaic work.\r\nAfternoon: Immerse yourself in the local crafts and shopping:\r\nNew Market: A bustling marketplace offering a wide array of goods.\r\nAarong: A popular store for ethically sourced handicrafts, clothing, and more.\r\nEvening: Consider attending a cultural performance or enjoying a relaxing evening at your hotel.', '2025-05-01', '2025-04-27 08:26:44', '2025-04-27 08:39:57', NULL),
(4, 5, 'Day 3: Journey to Sonargaon - The Ancient Capital', 'Sonargaon', 'Morning: Take a day trip to Sonargaon (about an hour and a half from Dhaka), the ancient capital of Bengal.\r\nDaytime: Explore the historical sites of Sonargaon:\r\nPanam City: A deserted historical trading city with well-preserved architecture.\r\nGoaldi Mosque: A beautiful example of medieval Bengali architecture.\r\nFolk Arts and Crafts Museum: Discover traditional Bangladeshi crafts.\r\nEvening: Return to Dhaka for the night.', '2025-05-02', '2025-04-27 08:27:44', '2025-05-03 16:21:39', 'sonarga2.JPG'),
(5, 5, 'Day 4: Fly to Sylhet - Tea Gardens and Spiritual Sites', 'Sylhet', 'Day 4: Fly to Sylhet - Tea Gardens and Spiritual Sites\r\nDay 4: Sylhet Beckons - Tea Garden Serenity and Sufi Culture Morning: Your journey continues with a flight from Dhaka (DAC) to Osmani International Airport (ZYL) in Sylhet. Settle into your hotel in Sylhet town or near the rolling hills of a tea garden. Afternoon: Indulge in the visual treat of Sylhet&#39;s renowned tea gardens. Take a leisurely walk through either Lakatura Tea Estate or Malnicherra Tea Estate, witnessing the art of tea cultivation. Evening: Connect with the local culture and spirituality at the Shrine of Hazrat Shahjalal (R.). Experience the peaceful atmosphere and learn about the Sufi heritage of the region.', '2025-05-03', '2025-04-27 08:31:19', '2025-05-07 09:46:45', NULL),
(6, 5, 'Day 5: Natural Wonders of Sylhet - Jaflong and Beyond', 'Jaflong (Sylhet Region)', 'Morning: Embark on a scenic day trip to Jaflong (about 1-2 hours from Sylhet).\r\nDaytime: Explore the natural beauty of Jaflong:\r\nPiyain River: Enjoy the clear waters and the views of the Meghalaya hills across the border. Consider a boat ride.\r\nStone Collection Area: Witness the unique activity of collecting stones from the riverbed.\r\nKhasia Tribal Village: Get a glimpse into the local tribal culture (with respect and permission).\r\nAfternoon (Optional): Depending on time and interest, you could also visit:\r\nSada Pathor (Bholaganj): Another stunning natural site with white stones and clear water (requires more travel time).\r\nEvening: Return to Sylhet for relaxation.', '2025-05-04', '2025-04-27 08:32:32', '2025-05-03 16:23:59', 'Jaflong.jpg'),
(7, 5, 'Day 6: Sylhet&#39;s Lakes and Return to Dhaka', 'Sylhet, Dhaka', 'Morning: Explore more of Sylhet&#39;s natural beauty:\r\nRatargul Swamp Forest: Take a boat trip through this unique freshwater swamp forest (best visited during the monsoon or post-monsoon season when it&#39;s filled with water).\r\nBisnakandi: A beautiful area where several layers of hills meet at a single point at the confluence of rivers (can be combined with Ratargul).\r\nAfternoon: Depending on your flight schedule, you might have some free time for last-minute souvenir shopping in Sylhet.\r\nEvening: Fly back from Sylhet (ZYL) to Dhaka (DAC). Transfer to your hotel near the airport or in a central location.', '2025-05-05', '2025-04-27 08:33:03', '2025-05-08 17:16:35', NULL),
(8, 5, 'Day 7: Departure', 'Dhaka', 'Morning: Enjoy a final Bengali breakfast. Depending on your flight schedule, you might have time for some last-minute activities or shopping in Dhaka.\r\nDeparture: Transfer to Hazrat Shahjalal International Airport (DAC) for your onward flight.', '2025-05-06', '2025-04-27 08:33:51', '2025-05-03 16:27:19', 'Hazrat Shahjalal International Airport.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `trip_participants`
--

CREATE TABLE `trip_participants` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trip_participants`
--

INSERT INTO `trip_participants` (`id`, `trip_id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(3, 5, 21, 'accepted', '2025-04-27 11:30:48', '2025-05-08 15:59:05'),
(4, 9, 21, 'pending', '2025-04-30 08:52:07', '2025-04-30 09:46:05'),
(5, 5, 35, 'accepted', '2025-05-01 14:27:38', '2025-05-06 10:26:32'),
(6, 5, 34, 'accepted', '2025-05-05 16:49:26', '2025-05-05 12:49:26'),
(7, 18, 34, 'pending', '2025-05-05 16:59:26', '2025-05-05 17:04:04'),
(8, 18, 21, 'pending', '2025-05-08 19:32:25', '2025-05-08 19:54:12');

-- --------------------------------------------------------

--
-- Table structure for table `trip_reviews`
--

CREATE TABLE `trip_reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `trip_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL COMMENT 'Rating out of 5 (e.g., 1 to 5)',
  `review_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','participant') NOT NULL DEFAULT 'user',
  `is_verified` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `otp_expiry` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(4) DEFAULT 0,
  `resend_attempt` int(11) DEFAULT 0,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `is_verified`, `otp_expiry`, `verification_token`, `profile_photo`, `created_at`, `updated_at`, `status`, `resend_attempt`, `country`, `city`, `language`, `currency`, `gender`) VALUES
(5, 'Admin', 'admin@gmail.com', '01886338865', '$2y$10$P3cTSWLa4AklqULL5aIhnuD0IhIz0ncm.LjbEIdd24hJMCJZ6IC12', 'admin', 1, '0000-00-00 00:00:00', '409245', 'image/profile_photos/681ce1c7edb5b_matrix-falling-code-3840x2160-13665.jpg', '2025-04-23 12:01:36', '2025-05-08 16:54:31', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'USD', 'male'),
(18, 'Hasan Misbah', 'hasan@gmail.com', '01886338868', '$2y$10$JDyWNkt8bjvNIuJm4DaxluGpkB..maNJSHUcyILjLVewJ/V3zbWK.', 'user', 1, '0000-00-00 00:00:00', '603642', 'image/profile_photos/681ce03d782fa_anonymous-hacker-data-breach-5k-3840x2160-7.jpg', '2025-04-25 17:16:49', '2025-05-08 16:47:57', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'BDT', 'male'),
(21, 'Mahmudul', 'mahmudul@gmail.com', '01886338885', '$2y$10$BDr0dpPThewcANeyHVwJJeF3ehzG2nfOX5NHKVtxLnpBu.itSimBy', 'participant', 1, '0000-00-00 00:00:00', '197436', 'image/profile_photos/681cdbdee80cc_Shahjahan-Jewel-1 (1).jpg', '2025-04-25 17:58:47', '2025-05-08 16:29:18', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'USD', 'male'),
(26, 'Sourob Roy', 'sourob@gmail.com', '01886338861', '$2y$10$vefnAL5b3oCyjprxMTtJle8..YfFPZPc71Mb6rerWXeaStfxUVbsa', 'user', 1, '0000-00-00 00:00:00', '621602', '/image/profile_photos/680ca09248c2b_arif.jpeg', '2025-04-26 09:00:31', '2025-05-08 13:21:37', 0, 0, 'Bangladesh', 'Dhaka', 'Bangla', 'BDT', 'male'),
(34, 'Biman P', 'biman@gmail.com', '01886338866', '$2y$10$Az1NmdJK2gqFq3uEPKeKs.esCCNG.4DgBwoPiAvyX2Jsl45BpKdLG', 'participant', 1, '0000-00-00 00:00:00', '804705', NULL, '2025-05-01 08:06:38', '2025-05-05 16:48:59', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'USD', 'male'),
(35, 'New Participant', 'newparticipant@gmail.com', '01886338864', '$2y$10$mgvjarTgXuOg9iITuoMuceX9VKVAn88Uo9JUrctkCu3mQFOo0dfEu', 'participant', 1, '0000-00-00 00:00:00', '994442', 'image/profile_photos/681cd1a0ce658_arif.jpeg', '2025-05-01 10:01:00', '2025-05-08 15:45:36', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'USD', 'male'),
(36, 'Md Shahjahan', 'shahjahan@gmail.com', '01615887714', '$2y$10$3NpR.ROcGYQVzl62dSg6HepaUni3ASWf5ePbiwAdPiG8lqW4CWrh.', 'participant', 1, '0000-00-00 00:00:00', '816329', 'image/profile_photos/681ce29bc0130_Shahjahan-Jewel-1 (1).jpg', '2025-05-08 13:27:52', '2025-05-08 19:38:03', 0, 0, 'Bangladesh', 'Sylhet', 'Bangla', 'USD', 'male');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_follow` (`follower_id`,`following_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `hotels`
--
ALTER TABLE `hotels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `state_id` (`state_id`);

--
-- Indexes for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hotel_id` (`hotel_id`),
  ADD KEY `room_type_id` (`room_type_id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invitation_code` (`invitation_code`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `inviter_user_id` (`inviter_user_id`),
  ADD KEY `invited_user_id` (`invited_user_id`);

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
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `itinerary_id` (`itinerary_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `room_types`
--
ALTER TABLE `room_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `transportations`
--
ALTER TABLE `transportations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `verification_token` (`verification_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hotels`
--
ALTER TABLE `hotels`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `itinerary_edit_requests`
--
ALTER TABLE `itinerary_edit_requests`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room_types`
--
ALTER TABLE `room_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transportations`
--
ALTER TABLE `transportations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `trip_itineraries`
--
ALTER TABLE `trip_itineraries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `trip_participants`
--
ALTER TABLE `trip_participants`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `trip_reviews`
--
ALTER TABLE `trip_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotels`
--
ALTER TABLE `hotels`
  ADD CONSTRAINT `hotels_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotels_ibfk_2` FOREIGN KEY (`state_id`) REFERENCES `states` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hotel_rooms`
--
ALTER TABLE `hotel_rooms`
  ADD CONSTRAINT `hotel_rooms_ibfk_1` FOREIGN KEY (`hotel_id`) REFERENCES `hotels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hotel_rooms_ibfk_2` FOREIGN KEY (`room_type_id`) REFERENCES `room_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `invitations_ibfk_2` FOREIGN KEY (`inviter_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `invitations_ibfk_3` FOREIGN KEY (`invited_user_id`) REFERENCES `users` (`id`);

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
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `polls_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `polls_ibfk_2` FOREIGN KEY (`itinerary_id`) REFERENCES `trip_itineraries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `polls_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `states`
--
ALTER TABLE `states`
  ADD CONSTRAINT `states_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transportations`
--
ALTER TABLE `transportations`
  ADD CONSTRAINT `transportations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transportations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `trip_expenses`
--
ALTER TABLE `trip_expenses`
  ADD CONSTRAINT `trip_expenses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_expenses_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `trip_reviews_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trip_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
