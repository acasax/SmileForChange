-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 04, 2021 at 11:50 AM
-- Server version: 10.3.28-MariaDB-log
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `osmehzap_dentist`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('A','N') NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `username`, `password`, `role`, `status`) VALUES
(1, 'kreativdental.lab@gmail.com', '$2y$10$Gvfk/gH1m0bUjkKWnCiC7u8RlOJEcmtfOJVv6qSKA/VTeZcLbbfDG', 'admin', 'A'),
(2, 'admin@gmail.com', '$2y$10$psuWyUCnMsRmcviy95K5oude1PC0Q5qQ75DJQ5DMVwfevdlel.GtW', 'admin', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `booking_appointment_data`
--

CREATE TABLE `booking_appointment_data` (
  `id` int(11) NOT NULL,
  `fk_booking_records_id` int(10) UNSIGNED NOT NULL,
  `appointment_email_body` longtext NOT NULL,
  `appointnment_my_comment` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabela o prihvatanju i odbijanju termnia\nEmail poruka\nKomentari koje smo mi pisali za nas';

-- --------------------------------------------------------

--
-- Table structure for table `booking_records`
--

CREATE TABLE `booking_records` (
  `id` int(10) UNSIGNED NOT NULL,
  `fk_reservation_time_id` int(10) UNSIGNED NOT NULL,
  `booking_first_name` varchar(100) NOT NULL,
  `booking_last_name` varchar(150) NOT NULL,
  `booking_email` varchar(150) NOT NULL,
  `booking_phone` varchar(12) NOT NULL,
  `booking_comment` mediumtext DEFAULT NULL,
  `booking_recrod_status` enum('A','R','S','C') NOT NULL DEFAULT 'A' COMMENT 'A - Active term R - Reserved termS - Stand by term - waiting adming to apply reservation C - Cancel appointment',
  `booking_record_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `booking_records`
--

INSERT INTO `booking_records` (`id`, `fk_reservation_time_id`, `booking_first_name`, `booking_last_name`, `booking_email`, `booking_phone`, `booking_comment`, `booking_recrod_status`, `booking_record_year`) VALUES
(1, 11, 'asd', 'acasax@gmail.com', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(2, 15, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(3, 16, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(4, 19, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(5, 17, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', '12', 'S', 2019),
(6, 20, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(7, 18, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asdasdasdasd', 'S', 2019),
(8, 14, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asdsasdadsasdasdasdasdadsadsa', 'S', 2019),
(9, 13, 'Aleksandar', 'Djordjevic', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019),
(10, 12, 'Aleksandar', 'Djordjevic', 'acasax@gmail.com', '064/330-8102', 'Aca sax aca sax ', 'S', 2019),
(11, 12, 'aca', 'aca', 'acasax@gmail.com', '064/330-8102', 'asd', 'S', 2019);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_date`
--

CREATE TABLE `reservation_date` (
  `id` int(10) UNSIGNED NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_date_status` enum('A','N') NOT NULL DEFAULT 'A',
  `reservation_date_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation_date`
--

INSERT INTO `reservation_date` (`id`, `reservation_date`, `reservation_date_status`, `reservation_date_year`) VALUES
(1, '2019-12-16', 'A', 2019),
(2, '2019-12-26', 'N', 2019),
(3, '2020-01-28', 'A', 2019),
(4, '2020-11-22', 'A', 2020),
(5, '2021-07-23', 'A', 2021);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_time`
--

CREATE TABLE `reservation_time` (
  `id` int(10) UNSIGNED NOT NULL,
  `fk_reservation_date_id` int(10) UNSIGNED NOT NULL,
  `reservation_time` varchar(2) NOT NULL,
  `reservation_time_status` enum('A','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `reservation_time`
--

INSERT INTO `reservation_time` (`id`, `fk_reservation_date_id`, `reservation_time`, `reservation_time_status`) VALUES
(1, 1, '8', 'N'),
(2, 1, '9', 'N'),
(3, 1, '10', 'N'),
(4, 1, '11', 'N'),
(5, 1, '12', 'N'),
(6, 1, '13', 'N'),
(7, 1, '14', 'N'),
(8, 1, '15', 'N'),
(9, 1, '16', 'N'),
(10, 1, '17', 'N'),
(11, 2, '8', 'A'),
(12, 2, '9', 'A'),
(13, 2, '10', 'A'),
(14, 2, '11', 'A'),
(15, 2, '12', 'A'),
(16, 2, '13', 'A'),
(17, 2, '14', 'A'),
(18, 2, '15', 'A'),
(19, 2, '16', 'A'),
(20, 2, '17', 'A'),
(21, 3, '8', 'N'),
(22, 3, '9', 'N'),
(23, 3, '10', 'N'),
(24, 3, '11', 'N'),
(25, 3, '12', 'N'),
(26, 3, '13', 'N'),
(27, 3, '14', 'N'),
(28, 3, '15', 'N'),
(29, 3, '16', 'N'),
(30, 3, '17', 'N'),
(31, 4, '8', 'N'),
(32, 4, '9', 'N'),
(33, 4, '10', 'N'),
(34, 4, '11', 'N'),
(35, 4, '12', 'N'),
(36, 4, '13', 'N'),
(37, 4, '14', 'N'),
(38, 4, '15', 'N'),
(39, 4, '16', 'N'),
(40, 4, '17', 'N'),
(41, 5, '9', 'N'),
(42, 5, '10', 'N'),
(43, 5, '11', 'N'),
(44, 5, '12', 'N'),
(45, 5, '13', 'N'),
(46, 5, '14', 'N'),
(47, 5, '15', 'N'),
(48, 5, '16', 'N'),
(49, 5, '17', 'N');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`);

--
-- Indexes for table `booking_appointment_data`
--
ALTER TABLE `booking_appointment_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_booking_records_id_idx` (`fk_booking_records_id`);

--
-- Indexes for table `booking_records`
--
ALTER TABLE `booking_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_time_id_idx` (`fk_reservation_time_id`);

--
-- Indexes for table `reservation_date`
--
ALTER TABLE `reservation_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation_time`
--
ALTER TABLE `reservation_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_date_id` (`fk_reservation_date_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_appointment_data`
--
ALTER TABLE `booking_appointment_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_records`
--
ALTER TABLE `booking_records`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `reservation_date`
--
ALTER TABLE `reservation_date`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reservation_time`
--
ALTER TABLE `reservation_time`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_appointment_data`
--
ALTER TABLE `booking_appointment_data`
  ADD CONSTRAINT `fk_booking_records_id` FOREIGN KEY (`fk_booking_records_id`) REFERENCES `booking_records` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `booking_records`
--
ALTER TABLE `booking_records`
  ADD CONSTRAINT `fk_reservation_time_id` FOREIGN KEY (`fk_reservation_time_id`) REFERENCES `reservation_time` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `reservation_time`
--
ALTER TABLE `reservation_time`
  ADD CONSTRAINT `fk_reservation_date_id` FOREIGN KEY (`fk_reservation_date_id`) REFERENCES `reservation_date` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
