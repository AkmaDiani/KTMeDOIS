-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 18, 2026 at 12:40 PM
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
-- Database: `supplier`
--

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `SUPPLIERID` varchar(25) NOT NULL,
  `SUPPLIER_COMP_REG_NO` varchar(200) DEFAULT NULL,
  `SUPPLIER_COMP_NAME` varchar(200) DEFAULT NULL,
  `SUPPLIER_CTC_NO` varchar(200) DEFAULT NULL,
  `SUPPLIER_CTC_PERSON` varchar(100) DEFAULT NULL,
  `SUPPLIER_EMAIL_ADD` varchar(200) DEFAULT NULL,
  `SUPPLIER_EXPIRED_DATE` date DEFAULT NULL,
  `SUPPLIER_CTC_STATUS` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`SUPPLIERID`, `SUPPLIER_COMP_REG_NO`, `SUPPLIER_COMP_NAME`, `SUPPLIER_CTC_NO`, `SUPPLIER_CTC_PERSON`, `SUPPLIER_EMAIL_ADD`, `SUPPLIER_EXPIRED_DATE`, `SUPPLIER_CTC_STATUS`) VALUES
('30001', '202001000101', 'SAZ Global Services', '0199231558', 'Ahmad Zaki', 'ahmad.zaki@sazglobal.com', '2026-12-31', 'Active'),
('30002', '201901000202', 'Mega Engineering Sdn Bhd', '0174458899', 'Nurul Huda', 'nurul.huda@megaeng.com', '2026-12-31', 'Active'),
('30003', '201801000303', 'RailTech Solutions Sdn Bhd', '0137788990', 'Muhammad Firdaus', 'firdaus@railtech.com', '2026-12-31', 'Active'),
('30004', '202101000404', 'Northern Track Services', '0168822114', 'Aiman Hakimi', 'aiman@northerntrack.com', '2026-12-31', 'Active'),
('30005', '202201000505', 'Dynamic Engineering Works', '0127711223', 'Farah Syazana', 'farah@dynamicworks.com', '2026-12-31', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`SUPPLIERID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
