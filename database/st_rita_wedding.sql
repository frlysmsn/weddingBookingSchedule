-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 11:36 AM
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
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"st_rita_wedding\",\"table\":\"bookings\"},{\"db\":\"st_rita_wedding\",\"table\":\"users\"},{\"db\":\"st_rita_wedding\",\"table\":\"documents\"},{\"db\":\"st_rita_wedding\",\"table\":\"booking_status_history\"},{\"db\":\"wsms\",\"table\":\"users\"},{\"db\":\"wsms_db\",\"table\":\"users\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2024-11-29 09:37:51', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `st_rita_wedding`
--
CREATE DATABASE IF NOT EXISTS `st_rita_wedding` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `st_rita_wedding`;

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
(4, 2, '2024-11-29', NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, '2024-11-28 14:49:27', '2024-11-28 16:21:57', 100.00, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(5, 4, '2024-11-29', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, '2024-11-28 15:37:22', '2024-11-28 16:26:07', 100.00, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(6, 5, '0000-00-00', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, '2024-11-28 16:43:58', '2024-11-28 16:43:58', 0.00, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(7, 6, '2024-11-30', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, '2024-11-29 03:50:03', '2024-11-29 03:52:19', 100.00, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(8, 7, '2024-11-30', NULL, NULL, NULL, NULL, NULL, 'pending', NULL, '2024-11-29 04:25:41', '2024-11-29 04:27:24', 100.00, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, NULL, NULL),
(9, NULL, '2011-02-03', '08:00:00', 'Melinda Yates Brenda Wiley Vaughan Duffy', 'Kelly Gutierrez Chandler Neal Adrian Vang', '+1 (431) 575-4864', 'jidol@mailinator.com', 'pending', NULL, '2024-11-29 08:30:45', '2024-11-29 08:30:45', 0.00, '2006-11-28', 'Possimus autem qui ', 'Id consequatur Nam ', 'Dolor natus nisi ut ', 0, 0, '1974-03-06', 'Et minima cupiditate', 'Sit perferendis volu', 'Tenetur minus omnis ', 0, 0, 0, NULL, 'yes', 'yes', 'no', 'yes'),
(10, NULL, '1985-05-22', '13:00:00', 'Kieran Gould Patience Mathis Tara Olsen', 'Dane Curry Driscoll Hewitt Taylor Hughes', '+1 (276) 784-1627', 'ryxihifiv@mailinator.com', 'pending', NULL, '2024-11-29 09:18:25', '2024-11-29 09:18:25', 0.00, '1979-01-24', 'Odit dolor dolor arc', 'Voluptate repudianda', 'Facere enim consequa', 0, 0, '2000-06-19', 'Unde similique minus', 'Natus fugiat tempore', 'Voluptatum dolore es', 0, 0, 0, NULL, 'yes', 'no', 'no', 'yes'),
(11, NULL, '2024-12-06', '08:00:00', 'Clementine Jacobs Maile Malone Graham Gibbs', 'Jorden Webb Zenia Little Shaeleigh Leach', '+1 (994) 737-3253', 'furywym@mailinator.com', 'pending', NULL, '2024-11-29 10:06:26', '2024-11-29 10:06:26', 0.00, '1998-11-11', 'Minus dolore repelle', 'Cumque itaque sunt d', 'Dolor ut dolorem qui', 0, 0, '2019-05-05', 'Optio fugit dolore', 'Id et sit rem sint i', 'Excepteur in est ve', 0, 0, 0, NULL, 'no', 'yes', 'no', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `booking_id`, `document_type`, `file_path`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 4, 'baptismal', 'uploads/documents/4/baptismal_1732805367.pdf', 'approved', '', '2024-11-28 14:49:27', '2024-11-28 16:21:57'),
(2, 4, 'confirmation', 'uploads/documents/4/confirmation_1732805388.pdf', 'approved', '', '2024-11-28 14:49:48', '2024-11-28 16:21:36'),
(3, 4, 'birth_certificate', 'uploads/documents/4/birth_certificate_1732805393.pdf', 'approved', '', '2024-11-28 14:49:53', '2024-11-28 16:20:48'),
(4, 4, 'cenomar', 'uploads/documents/4/cenomar_1732805397.pdf', 'approved', '', '2024-11-28 14:49:57', '2024-11-28 16:17:40'),
(5, 4, 'marriage_license', 'uploads/documents/4/marriage_license_1732805404.pdf', 'approved', '', '2024-11-28 14:50:04', '2024-11-28 16:19:30'),
(6, 5, 'baptismal', 'uploads/documents/5/baptismal_1732808242.pdf', 'approved', '', '2024-11-28 15:37:22', '2024-11-28 16:26:07'),
(7, 5, 'birth_certificate', 'uploads/documents/5/birth_certificate_1732808252.pdf', 'approved', '', '2024-11-28 15:37:32', '2024-11-28 16:26:01'),
(8, 5, 'confirmation', 'uploads/documents/5/confirmation_1732808255.pdf', 'approved', '', '2024-11-28 15:37:35', '2024-11-28 16:25:54'),
(9, 5, 'cenomar', 'uploads/documents/5/cenomar_1732808258.pdf', 'approved', '', '2024-11-28 15:37:38', '2024-11-28 16:25:47'),
(10, 5, 'marriage_license', 'uploads/documents/5/marriage_license_1732808276.pdf', 'approved', '', '2024-11-28 15:37:56', '2024-11-28 16:24:09'),
(11, 6, 'baptismal', 'uploads/documents/6/baptismal_1732812302.pdf', 'pending', NULL, '2024-11-28 16:45:02', '2024-11-28 16:45:02'),
(12, 7, 'baptismal', 'uploads/documents/7/baptismal_1732852203.pdf', 'approved', '', '2024-11-29 03:50:03', '2024-11-29 03:52:13'),
(13, 7, 'birth_certificate', 'uploads/documents/7/birth_certificate_1732852222.pdf', 'approved', '', '2024-11-29 03:50:22', '2024-11-29 03:52:19'),
(14, 7, 'confirmation', 'uploads/documents/7/confirmation_1732852229.pdf', 'approved', '', '2024-11-29 03:50:29', '2024-11-29 03:51:57'),
(15, 7, 'cenomar', 'uploads/documents/7/cenomar_1732852238.pdf', 'approved', '', '2024-11-29 03:50:38', '2024-11-29 03:52:06'),
(16, 7, 'marriage_license', 'uploads/documents/7/marriage_license_1732852252.pdf', 'approved', '', '2024-11-29 03:50:52', '2024-11-29 03:51:43'),
(17, 8, 'baptismal', 'uploads/documents/8/baptismal_1732854341.pdf', 'approved', '', '2024-11-29 04:25:41', '2024-11-29 04:27:24'),
(18, 8, 'birth_certificate', 'uploads/documents/8/birth_certificate_1732854349.pdf', 'approved', '', '2024-11-29 04:25:49', '2024-11-29 04:27:17'),
(19, 8, 'confirmation', 'uploads/documents/8/confirmation_1732854364.pdf', 'approved', '', '2024-11-29 04:26:04', '2024-11-29 04:27:10'),
(20, 8, 'cenomar', 'uploads/documents/8/cenomar_1732854371.pdf', 'approved', '', '2024-11-29 04:26:11', '2024-11-29 04:27:02'),
(21, 8, 'marriage_license', 'uploads/documents/8/marriage_license_1732854375.pdf', 'approved', '', '2024-11-29 04:26:15', '2024-11-29 04:26:47'),
(22, 6, 'confirmation', 'uploads/documents/6/confirmation_1732873259.pdf', 'pending', NULL, '2024-11-29 09:40:59', '2024-11-29 09:40:59'),
(23, 6, 'birth_certificate', 'uploads/documents/6/birth_certificate_1732873263.pdf', 'pending', NULL, '2024-11-29 09:41:03', '2024-11-29 09:41:03'),
(24, 6, 'cenomar', 'uploads/documents/6/cenomar_1732873267.pdf', 'pending', NULL, '2024-11-29 09:41:07', '2024-11-29 09:41:07'),
(25, 6, 'marriage_license', 'uploads/documents/6/marriage_license_1732873271.pdf', 'pending', NULL, '2024-11-29 09:41:11', '2024-11-29 09:41:11');

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
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `active`) VALUES
(2, 'Ritzmond Manzo', 'ritzmond.manzo@gmail.com', '$2y$10$YL1pWOVKqfLlwXlY.quG8uY8ZLhdJnnnMU2cUdDoFLVH1yfdOshWe', 'client', '2024-11-28 14:49:15', 1),
(3, 'Admin', 'admin@admin.com', '$2y$10$p3RoOn375Qns1.gwXeOOg.0nd8Q1ZKKKzMWuZwnPOo/GUNryX.1eu', 'admin', '2024-11-28 14:51:46', 1),
(4, 'ritzmond manzo', 'otin@gmail.com', '$2y$10$vY4QQyqv04dsxu9VhtTJ0uXLYWvIzqbKQhQLMb6XlELjEAS4CaxsW', 'client', '2024-11-28 15:34:41', 1),
(5, 'KYLIE SULIBRAN', 'kylieviasulibran12@gmail.com', '$2y$10$LiA2UFG79S29WCZckhzUXeqUlb3zPmmu4IIblDG9JUlKdognP2nfu', 'client', '2024-11-28 16:41:11', 1),
(6, 'jelian', 'rr@gmail.com', '$2y$10$OaW0s4qOHKCoUIGJkaLdReZrEulzO2hKS.D2O..m/BdiBvDcbGaSu', 'client', '2024-11-29 03:49:12', 1),
(7, 'jelian', 'w@gmail.com', '$2y$10$knxUGGS6TBZokCR6sB1iM.vqfqzP0hK6VhKKgZSnPgr187BHLMdNe', 'client', '2024-11-29 04:25:10', 1);

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
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `document_type` (`document_type`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`document_type`) REFERENCES `document_requirements` (`document_type`);
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
--
-- Database: `wsms_db`
--
CREATE DATABASE IF NOT EXISTS `wsms_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `wsms_db`;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_events`
--

CREATE TABLE `calendar_events` (
  `id` int(11) NOT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `event_type` enum('wedding','meeting','rehearsal','other') DEFAULT 'wedding',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `payment_status` enum('pending','completed','refunded') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_number` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `id` int(11) NOT NULL,
  `wedding_id` int(11) DEFAULT NULL,
  `requirement_type` varchar(100) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','client') DEFAULT 'client',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weddings`
--

CREATE TABLE `weddings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bride_name` varchar(100) NOT NULL,
  `groom_name` varchar(100) NOT NULL,
  `wedding_date` date NOT NULL,
  `ceremony_time` time NOT NULL,
  `guest_count` int(11) DEFAULT NULL,
  `status` enum('pending','approved','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_id` (`wedding_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_id` (`wedding_id`);

--
-- Indexes for table `requirements`
--
ALTER TABLE `requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wedding_id` (`wedding_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `weddings`
--
ALTER TABLE `weddings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calendar_events`
--
ALTER TABLE `calendar_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requirements`
--
ALTER TABLE `requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weddings`
--
ALTER TABLE `weddings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `calendar_events`
--
ALTER TABLE `calendar_events`
  ADD CONSTRAINT `calendar_events_ibfk_1` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requirements`
--
ALTER TABLE `requirements`
  ADD CONSTRAINT `requirements_ibfk_1` FOREIGN KEY (`wedding_id`) REFERENCES `weddings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weddings`
--
ALTER TABLE `weddings`
  ADD CONSTRAINT `weddings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
