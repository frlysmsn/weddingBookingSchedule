-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 03:44 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `st_rita_wedding`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `wedding_date` date NOT NULL,
  `preferred_time` time DEFAULT NULL,
  `groom_name` varchar(100) DEFAULT NULL,
  `bride_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` enum('pending','waiting_for_confirmation','confirmed','approved','rejected') DEFAULT 'pending',
  `selected_optional_doc` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `document_progress` decimal(5,2) DEFAULT 0.00,
  `bride_dob` date DEFAULT NULL,
  `bride_birthplace` text DEFAULT NULL,
  `bride_mother` varchar(255) DEFAULT NULL,
  `bride_father` varchar(255) DEFAULT NULL,
  `bride_interview` tinyint(1) DEFAULT 0,
  `bride_seminar` tinyint(1) DEFAULT 0,
  `groom_dob` date DEFAULT NULL,
  `groom_birthplace` text DEFAULT NULL,
  `groom_mother` varchar(255) DEFAULT NULL,
  `groom_father` varchar(255) DEFAULT NULL,
  `groom_interview` tinyint(1) DEFAULT 0,
  `groom_seminar` tinyint(1) DEFAULT 0,
  `is_confirmed` tinyint(1) DEFAULT 0,
  `confirmation_date` datetime DEFAULT NULL,
  `bride_prenup` enum('yes','no') DEFAULT NULL,
  `bride_precana` enum('yes','no') DEFAULT NULL,
  `groom_prenup` enum('yes','no') DEFAULT NULL,
  `groom_precana` enum('yes','no') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `wedding_date`, `preferred_time`, `groom_name`, `bride_name`, `contact_number`, `email`, `status`, `selected_optional_doc`, `created_at`, `updated_at`, `document_progress`, `bride_dob`, `bride_birthplace`, `bride_mother`, `bride_father`, `bride_interview`, `bride_seminar`, `groom_dob`, `groom_birthplace`, `groom_mother`, `groom_father`, `groom_interview`, `groom_seminar`, `is_confirmed`, `confirmation_date`, `bride_prenup`, `bride_precana`, `groom_prenup`, `groom_precana`) VALUES
(106, 55, '2024-12-04', '09:00:00', 'Eanz Marvic Go', 'Jelian Tasan Morga', '09546246763', 'jelianmaemorga@gmail.com', 'approved', NULL, '2024-12-03 14:21:33', '2024-12-03 14:22:13', 0.00, '2024-12-26', 'Cebu city', 'Maria Morga', 'Ron Morga', 0, 0, '2024-12-26', 'tacloban', 'Jasmine Go', 'Don Go', 0, 0, 0, NULL, 'yes', 'yes', 'yes', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `booking_actions`
--

CREATE TABLE `booking_actions` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `action_type` enum('approved','rejected','deleted') NOT NULL,
  `reason` text DEFAULT NULL,
  `acted_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_actions`
--

INSERT INTO `booking_actions` (`id`, `booking_id`, `action_type`, `reason`, `acted_by`, `created_at`) VALUES
(48, 106, 'approved', NULL, 3, '2024-12-03 14:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `document_type`, `file_path`, `status`, `remarks`, `created_at`, `updated_at`, `user_id`, `booking_id`) VALUES
(178, 'baptismal', 'uploads/documents/55/baptismal_1733235446.pdf', 'approved', '', '2024-12-03 14:17:26', '2024-12-03 14:18:48', 55, NULL),
(179, 'birth_certificate', 'uploads/documents/55/birth_certificate_1733235456.pdf', 'approved', '', '2024-12-03 14:17:36', '2024-12-03 14:18:43', 55, NULL),
(180, 'confirmation', 'uploads/documents/55/confirmation_1733235463.pdf', 'approved', '', '2024-12-03 14:17:43', '2024-12-03 14:18:33', 55, NULL),
(181, 'cenomar', 'uploads/documents/55/cenomar_1733235474.pdf', 'approved', '', '2024-12-03 14:17:54', '2024-12-03 14:18:20', 55, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `document_requirements`
--

CREATE TABLE `document_requirements` (
  `id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `mutually_exclusive_with` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_requirements`
--

INSERT INTO `document_requirements` (`id`, `document_type`, `name`, `is_required`, `mutually_exclusive_with`, `description`, `created_at`) VALUES
(1, 'baptismal', 'Baptismal Certificate', 1, NULL, 'Recent copy with annotation for marriage', '2024-11-28 14:38:22'),
(2, 'confirmation', 'Confirmation Certificate', 1, NULL, 'Must be authenticated', '2024-11-28 14:38:22'),
(3, 'birth_certificate', 'Birth Certificate', 1, NULL, 'PSA authenticated copy', '2024-11-28 14:38:22'),
(4, 'marriage_license', 'Marriage License', 0, 'cenomar', 'Valid government-issued marriage license', '2024-11-28 14:38:22'),
(5, 'cenomar', 'CENOMAR', 0, 'marriage_license', 'Certificate of No Marriage Record from PSA', '2024-11-28 14:38:22');

-- --------------------------------------------------------

--
-- Table structure for table `unavailable_dates`
--

CREATE TABLE `unavailable_dates` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `verification_code` varchar(6) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `active`, `verification_code`, `email_verified`) VALUES
(3, 'Super Admin', 'admin@admin.com', '$2y$10$p3RoOn375Qns1.gwXeOOg.0nd8Q1ZKKKzMWuZwnPOo/GUNryX.1eu', 'admin', '2024-11-28 14:51:46', 1, NULL, 0),
(23, 'cherry', 'cherry@gmail.com', '$2y$10$QjJYB1VaYjxjJmoc9fFm6e0ioG2.n9anXlQ8jUdBoYfGMtcWm79QO', 'admin', '2024-12-01 06:01:21', 1, NULL, 0),
(30, 'Admin', 'admin@admin1.com', '$2y$10$ADPnZasp1PDbXN/QBa0haOxQMuwdJ/jXAz1I.LBzUZ1uX48USfnUe', 'admin', '2024-12-01 16:49:05', 1, NULL, 0),
(35, 'Kylan Cleveland', 'kylieviasulibran12@gmail.com', '$2y$10$LFpuMobIEe.t/vedLW1ZNOqi.4UtMg9nLhm4H5Ip1qwQNz2Eqc7/q', 'client', '2024-12-03 03:22:50', 0, NULL, 0),
(55, 'Jelian Morga', 'jelianmaemorga@gmail.com', '$2y$10$UJAwGO/nDPlz2.gBgR1L8eNfrDq1alGy0QAo7pwJvKgiPaTFjQsdS', 'client', '2024-12-03 14:16:21', 1, NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `selected_optional_doc` (`selected_optional_doc`);

--
-- Indexes for table `booking_actions`
--
ALTER TABLE `booking_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `acted_by` (`acted_by`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_type` (`document_type`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `document_requirements`
--
ALTER TABLE `document_requirements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_type` (`document_type`);

--
-- Indexes for table `unavailable_dates`
--
ALTER TABLE `unavailable_dates`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `booking_actions`
--
ALTER TABLE `booking_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `document_requirements`
--
ALTER TABLE `document_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `unavailable_dates`
--
ALTER TABLE `unavailable_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`selected_optional_doc`) REFERENCES `document_requirements` (`document_type`);

--
-- Constraints for table `booking_actions`
--
ALTER TABLE `booking_actions`
  ADD CONSTRAINT `booking_actions_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_actions_ibfk_2` FOREIGN KEY (`acted_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`document_type`) REFERENCES `document_requirements` (`document_type`),
  ADD CONSTRAINT `documents_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `documents_ibfk_4` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
