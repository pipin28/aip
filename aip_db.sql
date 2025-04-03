-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 03:58 AM
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
-- Database: `aip_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `aip_sector`
--

CREATE TABLE `aip_sector` (
  `aip_id` int(11) NOT NULL,
  `aip_code` varchar(100) DEFAULT NULL,
  `department_office` varchar(255) DEFAULT NULL,
  `sector_category` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aip_sector`
--

INSERT INTO `aip_sector` (`aip_id`, `aip_code`, `department_office`, `sector_category`) VALUES
(58, '1000-000-2-1-01', 'Promotion for the Welfate and protection of Children', 'Environment Sector'),
(63, '1000-000-2-1-01', 'Cooperative Development', 'Institutional Development Sector'),
(64, '1000-000-2-1-01', 'Management Information and Computer Services', 'Environment Sector');

-- --------------------------------------------------------

--
-- Table structure for table `child`
--

CREATE TABLE `child` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `personal_services` decimal(15,2) DEFAULT 0.00,
  `maintenance_expenses` decimal(15,2) DEFAULT 0.00,
  `capital_outlay` decimal(15,2) DEFAULT 0.00,
  `climate_adaptation` decimal(15,2) DEFAULT 0.00,
  `climate_mitigation` decimal(15,2) DEFAULT 0.00,
  `cc_typology_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `child`
--

INSERT INTO `child` (`id`, `parent_id`, `description`, `funding_source`, `personal_services`, `maintenance_expenses`, `capital_outlay`, `climate_adaptation`, `climate_mitigation`, `cc_typology_code`) VALUES
(22, 20, 'TEST9', 'TEST9', 234123.00, 1231.00, 123.00, 123.00, 123.00, '123'),
(23, 20, 'TEST9v1', 'TEST9v1', 123.00, 123.00, 123.00, 123.00, 123.00, '123');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_head` varchar(255) NOT NULL,
  `department_office` varchar(255) NOT NULL,
  `department_init` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_head`, `department_office`, `department_init`, `sector_category`) VALUES
(1, 'Jenil', 'Promotion for the Welfate and protection of Children', '', 'Environment Sector'),
(2, '', 'Cooperative Development', '', 'Institutional Development Sector'),
(3, '', 'Management Information and Computer Services', 'MICS', 'Environment Sector');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `aip_ref_code` varchar(10) NOT NULL,
  `dept_head` varchar(255) NOT NULL,
  `budget_head` varchar(255) NOT NULL,
  `exe_head` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `implementing_office` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`id`, `user_id`, `aip_ref_code`, `dept_head`, `budget_head`, `exe_head`, `sector_category`, `description`, `implementing_office`, `start_date`, `end_date`) VALUES
(18, 1, '007', '', '', '', 'Economic Sector', 'TEST7', 'TEST7', '2024-12-05', '2024-11-26'),
(19, 1, '008', '', '', '', 'Economic Sector', 'Geographical', 'PWD', '2024-11-28', '2024-11-27'),
(20, 1, '009', '', '', '', 'Economic Sector', 'TEST9', 'TEST9', '2024-11-29', '2024-11-28'),
(21, 2, '001', '', 'ochea', '', 'Environment Sector', '', '', '2024-11-28', '2024-11-27'),
(22, 2, '002', '', 'ochea', '', 'Environment Sector', '', '', '2024-12-04', '2024-11-19'),
(23, 2, '003', '', 'test3', 'test3', 'Environment Sector', '', '', '2024-11-28', '2024-12-05'),
(24, 2, '004', 'Jenil', 'test4', 'test4', 'Environment Sector', 'Management Information and Computer Services', '', '2024-11-27', '2024-11-27');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `department_office` varchar(255) NOT NULL,
  `department_email` varchar(255) NOT NULL,
  `department_password` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `department_office`, `department_email`, `department_password`, `sector_category`, `status`) VALUES
(1, 'Cooperative Development', 'welfare@gmail.com', '123', 'Institutional Development Sector', 'department'),
(2, 'Management Information and Computer Services', 'Cop.Dev@gmail.com', 'cop.dev', 'Environment Sector', 'department'),
(3, '0', 'admin@gmail.com', 'admin', '', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aip_sector`
--
ALTER TABLE `aip_sector`
  ADD PRIMARY KEY (`aip_id`);

--
-- Indexes for table `child`
--
ALTER TABLE `child`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent`
--
ALTER TABLE `parent`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aip_sector`
--
ALTER TABLE `aip_sector`
  MODIFY `aip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
