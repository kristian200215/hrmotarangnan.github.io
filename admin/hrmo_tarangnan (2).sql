-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2024 at 05:16 AM
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
-- Database: `hrmo_tarangnan`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(1, 'Human Resource Management Officer '),
(2, 'Engineering Office ');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `first_name`, `middle_name`, `last_name`, `contact_number`, `address`, `birthday`) VALUES
(18, 'Jireh', 'Jabinal', 'Salinas', '161314', '342 Purok1 Barangay 13', '2000-02-20'),
(19, 'Wilfredo', 'Astig', 'Jamindang', '123', 'Brgy Guinso', '2001-04-20');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `position_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `department_id`, `position_title`) VALUES
(1, 1, 'HR Coordinator'),
(2, 2, 'Head Engineer');

-- --------------------------------------------------------

--
-- Table structure for table `serviceperiods`
--

CREATE TABLE `serviceperiods` (
  `period_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Active','Retired','Transferred','Resigned','Promoted') NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `employment_type` enum('Casual','Permanent') NOT NULL,
  `station_assignment` varchar(255) DEFAULT NULL,
  `branch_type` enum('Local','National') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `serviceperiods`
--

INSERT INTO `serviceperiods` (`period_id`, `employee_id`, `department_id`, `position_id`, `start_date`, `end_date`, `status`, `salary`, `employment_type`, `station_assignment`, `branch_type`) VALUES
(26, 18, 2, 2, '2005-02-20', '2010-06-25', 'Promoted', 99999.99, 'Permanent', 'LGU TARANGNAN', 'Local'),
(27, 18, 1, 1, '2010-02-04', '2011-02-04', 'Transferred', 122.98, 'Casual', 'LGU TARANGNAN', 'Local'),
(28, 18, 2, 2, '2012-02-05', NULL, 'Active', 320.99, 'Permanent', 'LGU TARANGNAN', 'Local'),
(29, 19, 2, 2, '2005-05-23', '2009-07-09', 'Transferred', 123.00, 'Casual', 'LGU ahah', 'Local'),
(30, 19, 1, 1, '2013-07-25', '2015-01-14', 'Promoted', 3333.00, 'Permanent', 'LGU TARANGNAN', 'National'),
(31, 19, 2, 2, '2015-12-28', NULL, 'Active', 55555.00, 'Casual', 'AC', 'Local');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `serviceperiods`
--
ALTER TABLE `serviceperiods`
  ADD PRIMARY KEY (`period_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `position_id` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `serviceperiods`
--
ALTER TABLE `serviceperiods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE;

--
-- Constraints for table `serviceperiods`
--
ALTER TABLE `serviceperiods`
  ADD CONSTRAINT `serviceperiods_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `serviceperiods_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `serviceperiods_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
