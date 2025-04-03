-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 06:57 AM
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
(77, 'AIP-7000-000-2-7-35', 'Office of the Mayor', 'Institutional Development Sector'),
(78, 'AIP-4000-000-2-5-98', 'Office of the Mayor (Special Bodies/Program/Projects) All Offices CO', 'Institutional Development Sector'),
(79, 'AIP-5000-000-2-5-68', 'Promotion of the Welfare and Protection of Children', 'Institutional Development Sector'),
(80, 'AIP-3000-000-2-9-90', 'Cooperative Development', 'Institutional Development Sector'),
(81, 'AIP-9000-000-2-2-29', 'Cebu City Anti-Mendicancy', 'Institutional Development Sector'),
(82, 'AIP-6000-000-2-9-39', 'Cebu City Anti-Indecency', 'Institutional Development Sector'),
(83, 'AIP-3000-000-2-4-70', 'Local Housing Board', 'Institutional Development Sector'),
(84, 'AIP-5000-000-2-8-19', 'Cebu City Anti-Discrimination', 'Institutional Development Sector'),
(85, 'AIP-8000-000-2-9-32', 'Cebu City Senior Cetizen', 'Institutional Development Sector'),
(86, 'AIP-6000-000-2-6-24', 'Cebu City Women & Family Affairs Programs', 'Institutional Development Sector'),
(87, 'AIP-9000-000-2-5-25', 'Cebu City Tripartite Peace', 'Institutional Development Sector'),
(88, 'AIP-1000-000-2-8-59', 'Maint. & Operaion of  Fort San Pedro', 'Institutional Development Sector'),
(89, 'AIP-9000-000-2-4-14', 'SRP Management Services', 'Institutional Development Sector'),
(90, 'AIP-8000-000-2-6-94', 'Prevention Restoration Order Beautification Enhancement Program', 'Institutional Development Sector'),
(91, 'AIP-5000-000-2-0-36', 'Scholarship Program', 'Institutional Development Sector'),
(92, 'AIP-4000-000-2-2-97', 'City Schools Superintendent ', 'Social Sector'),
(93, 'AIP-2000-000-2-0-85', 'Cebu City Medical Center (CCMC - College of Nursing)', 'Social Sector'),
(94, 'AIP-5000-000-2-4-84', 'Cebu City Resource Management and Development Center (CREMDEC)', 'Social Sector'),
(95, 'AIP-3000-000-2-7-34', 'Cultural/Historical Affairs Commission (CHAC/CHAO)', 'Social Sector'),
(96, 'AIP-8000-000-2-7-50', 'Cebu City Sports Commission (CCSC)', 'Social Sector'),
(97, 'AIP-2000-000-2-8-43', 'Cebu City Youth Development Commision (CCYDC)', 'Social Sector'),
(98, 'AIP-5000-000-2-2-49', 'City Health Department - General Administration', 'Social Sector'),
(99, 'AIP-2000-000-2-2-44', 'City Agriculture Department (CAD)', 'Economic Sector'),
(100, 'AIP-9000-000-2-5-05', 'Department of Veterinary Medicine & Fisheries (DVMF)', 'Economic Sector'),
(101, 'AIP-1000-000-2-1-51', 'Department of Engineering and Public Works (DEPW)', 'Economic Sector'),
(102, 'AIP-9000-000-2-9-34', 'Cebu City Tourism Commission', 'Economic Sector'),
(103, 'AIP-5000-000-2-8-09', 'Office of the City Architect', 'Economic Sector'),
(104, 'AIP-3000-000-2-5-55', 'Local Development Fund (Economic Sector)', 'Economic Sector'),
(105, 'AIP-2000-000-2-5-64', 'Operation of City Market', 'Economic Sector'),
(106, 'AIP-9000-000-2-9-18', 'Department of Public Services (DPS)', 'Environmental Sector'),
(107, 'AIP-2000-000-2-8-80', 'Cebu City Parks and Playground', 'Environmental Sector'),
(108, 'AIP-3000-000-2-6-92', 'Cebu City Environment and Natural Resources Office (CCENRO)', 'Environmental Sector'),
(109, 'AIP-5000-000-2-1-15', 'Local Development Fund (Environmental Sector)', 'Environmental Sector'),
(110, 'AIP-6000-000-2-4-05', 'Management Information and Computer Services', 'Institutional Development Sector');

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
(64, 67, 'Effective update', 'new', 1000000.00, 1000000.00, 1000000.00, 0.00, 0.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_office` varchar(255) NOT NULL,
  `department_init` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_office`, `department_init`, `sector_category`, `status`) VALUES
(16, 'Office of the Mayor', '', 'Institutional Development Sector', 'active'),
(17, 'Office of the Mayor (Special Bodies/Program/Projects) All Offices CO', '', 'Institutional Development Sector', 'active'),
(18, 'Promotion of the Welfare and Protection of Children', '', 'Institutional Development Sector', 'active'),
(19, 'Cooperative Development', '', 'Institutional Development Sector', 'active'),
(20, 'Cebu City Anti-Mendicancy', '', 'Institutional Development Sector', 'active'),
(21, 'Cebu City Anti-Indecency', '', 'Institutional Development Sector', 'active'),
(22, 'Local Housing Board', '', 'Institutional Development Sector', 'active'),
(23, 'Cebu City Anti-Discrimination', '', 'Institutional Development Sector', 'active'),
(24, 'Cebu City Senior Cetizen', '', 'Institutional Development Sector', 'active'),
(25, 'Cebu City Women & Family Affairs Programs', '', 'Institutional Development Sector', 'active'),
(26, 'Cebu City Tripartite Peace', '', 'Institutional Development Sector', 'active'),
(27, 'Maint. & Operaion of  Fort San Pedro', '', 'Institutional Development Sector', 'active'),
(28, 'SRP Management Services', '', 'Institutional Development Sector', 'active'),
(29, 'Prevention Restoration Order Beautification Enhancement Program', '', 'Institutional Development Sector', 'active'),
(30, 'Scholarship Program', '', 'Institutional Development Sector', 'active'),
(31, 'City Schools Superintendent ', '', 'Social Sector', 'active'),
(32, 'Cebu City Medical Center (CCMC - College of Nursing)', '', 'Social Sector', 'active'),
(33, 'Cebu City Resource Management and Development Center (CREMDEC)', '', 'Social Sector', 'active'),
(34, 'Cultural/Historical Affairs Commission (CHAC/CHAO)', '', 'Social Sector', 'active'),
(35, 'Cebu City Sports Commission (CCSC)', '', 'Social Sector', 'active'),
(36, 'Cebu City Youth Development Commision (CCYDC)', '', 'Institutional Development Sector', 'active'),
(37, 'City Health Department - General Administration', '', 'Social Sector', 'active'),
(38, 'City Agriculture Department (CAD)', '', 'Economic Sector', 'active'),
(39, 'Department of Veterinary Medicine & Fisheries (DVMF)', '', 'Economic Sector', 'active'),
(40, 'Department of Engineering and Public Works (DEPW)', '', 'Economic Sector', 'active'),
(41, 'Cebu City Tourism Commission', '', 'Economic Sector', 'active'),
(42, 'Office of the City Architect', '', 'Economic Sector', 'active'),
(43, 'Operation of City Market', '', 'Economic Sector', 'active'),
(44, 'Local Development Fund (Economic Sector)', '', 'Economic Sector', 'active'),
(45, 'Department of Public Services (DPS)', '', 'Environmental Sector', 'active'),
(46, 'Cebu City Parks and Playground', '', 'Environmental Sector', 'active'),
(47, 'Cebu City Environment and Natural Resources Office (CCENRO)', '', 'Environmental Sector', 'active'),
(48, 'Local Development Fund (Environmental Sector)', '', 'Environmental Sector', 'active'),
(49, 'Management Information and Computer Services', '', 'Institutional Development Sector', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `parent`
--

CREATE TABLE `parent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `aip_ref_code` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `implementing_office` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent`
--

INSERT INTO `parent` (`id`, `user_id`, `aip_ref_code`, `sector_category`, `description`, `implementing_office`, `start_date`, `end_date`, `status`) VALUES
(67, 21, 'AIP-6000-000-2-4-05', 'Institutional Development Sector', 'PWD PROGRAM', 'Management Information and Computer Services', '2024-12-11', '2025-01-11', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `project_requests`
--

CREATE TABLE `project_requests` (
  `id` int(11) NOT NULL,
  `aip_ref_code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','validating','Re-Submition','Approved') NOT NULL DEFAULT 'pending',
  `denial_comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_comment`
--

CREATE TABLE `tbl_comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `aip_ref_code` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_comment`
--

INSERT INTO `tbl_comment` (`id`, `user_id`, `sender_id`, `aip_ref_code`, `comment`, `status`) VALUES
(23, 1, 3, 'COO0001', 'hello', 'read'),
(24, 4, 3, 'MAN0001', 'Too much budget in Capital', 'unread'),
(25, 4, 3, 'MAN0001', 'Still too much budget', 'unread'),
(26, 1, 3, 'COO0002', 'Test', 'read'),
(27, 4, 3, 'MAN0001', 'dako ra', 'unread'),
(28, 9, 10, 'COO0001', 'hello', 'read'),
(29, 11, 10, 'AIP000-000-2-M-94', 'Comment', 'unread'),
(30, 11, 10, 'AIP000-000-2-M-94', 'evaluated', 'unread'),
(31, 23, 10, 'AIP-8000-000-2-7-50', 'balika na!', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_header`
--

CREATE TABLE `tbl_header` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `signatory_one` varchar(255) NOT NULL,
  `signatory_two` varchar(255) NOT NULL,
  `signatory_three` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_header`
--

INSERT INTO `tbl_header` (`id`, `department_id`, `signatory_one`, `signatory_two`, `signatory_three`) VALUES
(28, 21, 'head', 'head', 'head'),
(30, 23, 'Jenil', 'Vincent', 'Jerome'),
(31, 21, 'Engr. Conrado A. Ordesta III', 'Roseney G. Reyes, CPA', 'Hon. Raymond Alvin N. Garcia');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sector`
--

CREATE TABLE `tbl_sector` (
  `id` int(11) NOT NULL,
  `sector_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sector`
--

INSERT INTO `tbl_sector` (`id`, `sector_name`) VALUES
(1, 'Institutional Development Sector'),
(2, 'Social Sector'),
(4, 'Economic Sector'),
(6, 'Environmental Sector'),
(7, 'Special Account Sector');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id` int(11) NOT NULL,
  `department_office` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `sector_category` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `department_office`, `name`, `username`, `password`, `sector_category`, `role`, `status`) VALUES
(10, 'Admin Office', 'admin', 'administrator', 'admin', '', 'admin', 'active'),
(15, NULL, 'super admin', 'superadmin', 'superadmin', '', 'super-admin', 'active'),
(21, 'Management Information and Computer Services', 'mics', 'mics', 'Mics12345', 'Institutional Development Sector', 'author', 'active'),
(23, 'Cebu City Sports Commission (CCSC)', 'user', 'user', 'User2345', 'Social Sector', 'author', 'active');

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_ibfk_1` (`user_id`);

--
-- Indexes for table `project_requests`
--
ALTER TABLE `project_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_header`
--
ALTER TABLE `tbl_header`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dpt` (`department_id`);

--
-- Indexes for table `tbl_sector`
--
ALTER TABLE `tbl_sector`
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
  MODIFY `aip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `child`
--
ALTER TABLE `child`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `parent`
--
ALTER TABLE `parent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `project_requests`
--
ALTER TABLE `project_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_comment`
--
ALTER TABLE `tbl_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_header`
--
ALTER TABLE `tbl_header`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `tbl_sector`
--
ALTER TABLE `tbl_sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `child`
--
ALTER TABLE `child`
  ADD CONSTRAINT `child_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parent` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent`
--
ALTER TABLE `parent`
  ADD CONSTRAINT `parent_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_header`
--
ALTER TABLE `tbl_header`
  ADD CONSTRAINT `fk_dpt` FOREIGN KEY (`department_id`) REFERENCES `tbl_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
