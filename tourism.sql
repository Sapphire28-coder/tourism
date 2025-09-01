-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2025 at 09:22 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourism`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodations`
--

CREATE TABLE `accommodations` (
  `accommodation_id` int(11) NOT NULL,
  `resort_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`accommodation_id`, `resort_id`, `name`, `description`, `price`, `capacity`, `picture`, `status`, `created_at`) VALUES
(1, 3, 'Room 1', 'good for 2 pax', 1000.00, 2, 'uploads/1756747593_room.jpg', 'available', '2025-09-01 17:26:33'),
(2, 3, 'Family room 1', 'Good for Family of 6', 2500.00, 6, 'uploads/1756748178_suite.jpg', 'available', '2025-09-01 17:36:18');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Sachi', 'Ochida', 'admin@gmail.com', '$2y$10$AJkjjZFTZwF6SMFsOKpHXO5JKIcEtriSwtnG8RhOgOjKHeG8Gxmjm', '2025-09-01 18:58:49', '2025-09-01 18:58:49');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `resort_id` int(11) NOT NULL,
  `accommodation_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `reference_no`, `resort_id`, `accommodation_id`, `customer_name`, `customer_email`, `check_in`, `check_out`, `amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BNK-20250901-8825', 3, 1, 'Kyla Bagaforo', 'kyangkyang@gmail.com', '2025-09-18', '2025-09-20', 2000.00, 'confirmed', '2025-09-01 18:47:38', '2025-09-01 18:52:27');

-- --------------------------------------------------------

--
-- Table structure for table `resorts`
--

CREATE TABLE `resorts` (
  `resort_id` int(11) NOT NULL,
  `resort_name` varchar(150) NOT NULL,
  `resort_address` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `resort_picture` varchar(255) DEFAULT NULL,
  `owner_name` varchar(150) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resorts`
--

INSERT INTO `resorts` (`resort_id`, `resort_name`, `resort_address`, `logo`, `resort_picture`, `owner_name`, `amenities`, `description`, `email`, `contact_number`, `password`, `status`, `latitude`, `longitude`, `created_at`) VALUES
(1, 'Giannas Mountain Resort', 'Sitio Abaca, Barangay Mailum, Bago, 6101 Negros Occidental', NULL, NULL, 'Joshua Selorio', NULL, NULL, 'gmr@gmail.com', '09664558436', 'GMR123', '', NULL, NULL, '2025-09-01 16:19:17'),
(2, 'Kipot Twin Falls', 'F3H9+2W6, Barangay Mailum, Bago City, Negros Occidental, Bago', NULL, NULL, 'Nalyn Lozada', NULL, NULL, 'Kipottwinfalls@gmail.com', '0344610540', '$2y$10$mwxdDn9lVJnz/izVrOe0ze0KOsNSz2N3fiyccKu1RBIfgmzRZoNqS', '', NULL, NULL, '2025-09-01 16:30:33'),
(3, '', 'F3RW+3W5, Murcia, Negros Occidental', 'logo_3_1756746398.jpg', 'resort_3_1756746502.jpg', 'Dhazel Orquia', 'campsite, cafe, comfort rooms, pool, rooms', 'Experience the heights and view of the City lights', 'superview.campsite@gmail.com', '09664558436', '$2y$10$vRyjfKADuBcmux5D7LrZJ.DgBdeUEh.5wEpHHMFgPiDXv3/pf88cm', 'active', 10.5296698, 123.0922794, '2025-09-01 16:38:34');

-- --------------------------------------------------------

--
-- Table structure for table `tourist`
--

CREATE TABLE `tourist` (
  `tourist_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourist`
--

INSERT INTO `tourist` (`tourist_id`, `first_name`, `last_name`, `email`, `contact_number`, `profile_picture`, `password`, `created_at`) VALUES
(1, 'Reinster May', 'Ochida', 'renrengoaway052896@gmail.com', '09454562441', NULL, 'Sachi09028', '2025-09-01 16:17:54'),
(2, 'Kyla', 'Bagaforo', 'kyangkyang@gmail.com', '09454562441', NULL, '$2y$10$LGgr0BpPn6p2/v0hX7wVq.17ySXQz351hzHlmxqDvS8Uz0YRUC8.2', '2025-09-01 17:52:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`accommodation_id`),
  ADD KEY `resort_id` (`resort_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`),
  ADD KEY `resort_id` (`resort_id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- Indexes for table `resorts`
--
ALTER TABLE `resorts`
  ADD PRIMARY KEY (`resort_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tourist`
--
ALTER TABLE `tourist`
  ADD PRIMARY KEY (`tourist_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `accommodation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `resorts`
--
ALTER TABLE `resorts`
  MODIFY `resort_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tourist`
--
ALTER TABLE `tourist`
  MODIFY `tourist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD CONSTRAINT `accommodations_ibfk_1` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`resort_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`resort_id`) REFERENCES `resorts` (`resort_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`accommodation_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
