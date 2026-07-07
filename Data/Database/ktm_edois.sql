-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jun 21, 2026 at 06:34 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ktm_edois`
--

-- --------------------------------------------------------

--
-- Table structure for table `auditlog`
--

CREATE TABLE `auditlog` (
  `Log_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Action` text CHARACTER SET utf8mb4,
  `Affected_Record` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auditlog`
--

INSERT INTO `auditlog` (`Log_ID`, `User_ID`, `Action`, `Affected_Record`, `Timestamp`) VALUES
(70001, 20001, 'Create DO', 'DO-2026-0001', '2026-06-22 00:32:15'),
(70002, 20001, 'Review DO', 'DO-2026-0002', '2026-06-22 00:32:15'),
(70003, 20002, 'Approve DO', 'DO-2026-0003', '2026-06-22 00:32:15'),
(70004, 20003, 'Reject DO', 'DO-2026-0004', '2026-06-22 00:32:15'),
(70005, 20004, 'Create DO', 'DO-2026-0005', '2026-06-22 00:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `do`
--

CREATE TABLE `do` (
  `DO_id` int(11) NOT NULL,
  `DO_number` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `PO_number` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `supplier_ID` int(11) DEFAULT NULL,
  `Staff_id` int(11) DEFAULT NULL,
  `DO_link` longblob,
  `Proof_link` longblob,
  `Status` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Reason` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_by` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `created_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `do`
--

INSERT INTO `do` (`DO_id`, `DO_number`, `PO_number`, `supplier_ID`, `Staff_id`, `DO_link`, `Proof_link`, `Status`, `Reason`, `created_by`, `created_date`) VALUES
(40001, 'DO-2026-0001', 'PO-2026-001', 30001, 20001, NULL, NULL, 'Submitted', '-', 'ABC Engineering Sdn Bhd', '2026-06-22 00:32:15'),
(40002, 'DO-2026-0002', 'PO-2026-002', 30002, 20001, NULL, NULL, 'Under Review', '-', 'Maju Rail Supply Sdn Bhd', '2026-06-22 00:32:15'),
(40003, 'DO-2026-0003', 'PO-2026-003', 30003, 20002, NULL, NULL, 'Approved', '-', 'Tech Industrial Sdn Bhd', '2026-06-22 00:32:15'),
(40004, 'DO-2026-0004', 'PO-2026-004', 30004, 20003, NULL, NULL, 'Rejected', 'Incomplete document', 'Global Parts Sdn Bhd', '2026-06-22 00:32:15'),
(40005, 'DO-2026-0005', 'PO-2026-005', 30005, 20004, NULL, NULL, 'Submitted', '-', 'Railway Solutions Sdn Bhd', '2026-06-22 00:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `Invoice_id` int(11) NOT NULL,
  `Invoice_num` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `DO_id` int(11) DEFAULT NULL,
  `supplier_ID` int(11) DEFAULT NULL,
  `Staff_id` int(11) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `Subtotal` decimal(10,0) DEFAULT NULL,
  `Tax` decimal(10,0) DEFAULT NULL,
  `Total` decimal(10,0) DEFAULT NULL,
  `Status` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Reason` varchar(250) DEFAULT NULL,
  `Created_At` datetime DEFAULT NULL,
  `Description` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Credit_note` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `item_no` int(5) NOT NULL,
  `item_description` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `quantity` int(4) DEFAULT NULL,
  `DO_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_no`, `item_description`, `quantity`, `DO_id`) VALUES
(50001, 'Rail Fastener', 100, 40001),
(50002, 'Steel Plate', 50, 40002),
(50003, 'Track Bolt', 200, 40003),
(50004, 'Rail Joint Bar', 75, 40004),
(50005, 'Track Clip', 150, 40005);

-- --------------------------------------------------------

--
-- Table structure for table `ktm staff`
--

CREATE TABLE `ktm staff` (
  `User_ID` int(11) NOT NULL,
  `Username` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Password_Hash` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Role` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Last_Login` datetime DEFAULT NULL,
  `Email` varchar(150) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Status` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ktm staff`
--

INSERT INTO `ktm staff` (`User_ID`, `Username`, `Password_Hash`, `Role`, `Last_Login`, `Email`, `Status`) VALUES
(20001, 'officer1', '123456', 'KTM Officer', '2026-06-22 00:32:15', 'officer1@ktm.com', 'Active'),
(20002, 'finance1', '123456', 'Finance Officer', '2026-06-22 00:32:15', 'finance1@ktm.com', 'Active'),
(20003, 'manager1', '123456', 'Manager', '2026-06-22 00:32:15', 'manager1@ktm.com', 'Active'),
(20004, 'officer2', '123456', 'KTM Officer', '2026-06-22 00:32:15', 'officer2@ktm.com', 'Active'),
(20005, 'admin1', '123456', 'Admin', '2026-06-22 00:32:15', 'admin1@ktm.com', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `Notification` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Supplier_id` int(11) DEFAULT NULL,
  `Type` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Content` text CHARACTER SET utf8mb4,
  `Status` varchar(50) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Creates_At` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`Notification`, `User_ID`, `Supplier_id`, `Type`, `Content`, `Status`, `Creates_At`) VALUES
(60001, 20001, 30001, 'DO Submission', 'Delivery Order DO-2026-0001 submitted successfully', 'Unread', '2026-06-22 00:32:15'),
(60002, 20001, 30002, 'DO Review', 'Delivery Order DO-2026-0002 is under review', 'Unread', '2026-06-22 00:32:15'),
(60003, 20002, 30003, 'DO Approval', 'Delivery Order DO-2026-0003 approved', 'Unread', '2026-06-22 00:32:15'),
(60004, 20003, 30004, 'DO Rejection', 'Delivery Order DO-2026-0004 rejected', 'Unread', '2026-06-22 00:32:15'),
(60005, 20004, 30005, 'DO Submission', 'Delivery Order DO-2026-0005 submitted successfully', 'Unread', '2026-06-22 00:32:15');

-- --------------------------------------------------------

--
-- Table structure for table `po`
--

CREATE TABLE `po` (
  `PO_number` varchar(50) CHARACTER SET utf8mb4 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `po`
--

INSERT INTO `po` (`PO_number`) VALUES
('PO-2026-001'),
('PO-2026-002'),
('PO-2026-003'),
('PO-2026-004'),
('PO-2026-005'),
('PO-2026-006'),
('PO-2026-007'),
('PO-2026-008'),
('PO-2026-009'),
('PO-2026-010');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `Report_ID` int(11) NOT NULL,
  `DO_id` int(11) DEFAULT NULL,
  `Invoice_id` int(11) DEFAULT NULL,
  `Log_ID` int(11) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4,
  `created_At` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `Supplier_id` int(11) NOT NULL,
  `Supplier_name` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Contac_person` varchar(1200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `phone` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `Inactive_date` datetime DEFAULT NULL,
  `Billing_address` text CHARACTER SET utf8mb4,
  `Vendor_Number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`Supplier_id`, `Supplier_name`, `Contac_person`, `phone`, `email`, `status`, `Inactive_date`, `Billing_address`, `Vendor_Number`) VALUES
(30001, 'ABC Engineering Sdn Bhd', 'Ahmad Rahman', '0123456789', 'abc@gmail.com', 'Active', NULL, 'Kuala Lumpur', 10001),
(30002, 'Maju Rail Supply Sdn Bhd', 'Siti Nur', '0134567890', 'maju@gmail.com', 'Active', NULL, 'Selangor', 10002),
(30003, 'Tech Industrial Sdn Bhd', 'John Lee', '0145678901', 'tech@gmail.com', 'Active', NULL, 'Johor', 10003),
(30004, 'Global Parts Sdn Bhd', 'Nurul Huda', '0156789012', 'global@gmail.com', 'Active', NULL, 'Penang', 10004),
(30005, 'Railway Solutions Sdn Bhd', 'Ali Hassan', '0167890123', 'railway@gmail.com', 'Active', NULL, 'Perak', 10005);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD PRIMARY KEY (`Log_ID`),
  ADD KEY `auditlog_ibfk_1` (`User_ID`);

--
-- Indexes for table `do`
--
ALTER TABLE `do`
  ADD PRIMARY KEY (`DO_id`),
  ADD KEY `do_ibfk_1` (`PO_number`),
  ADD KEY `Staff_id` (`Staff_id`),
  ADD KEY `supplier_ID` (`supplier_ID`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`Invoice_id`),
  ADD KEY `invoice_ibfk_1` (`DO_id`),
  ADD KEY `invoice_ibfk_2` (`supplier_ID`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`item_no`),
  ADD KEY `item_ibfk_1` (`DO_id`);

--
-- Indexes for table `ktm staff`
--
ALTER TABLE `ktm staff`
  ADD PRIMARY KEY (`User_ID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`Notification`),
  ADD KEY `notification_ibfk_1` (`Supplier_id`),
  ADD KEY `notification_ibfk_2` (`User_ID`);

--
-- Indexes for table `po`
--
ALTER TABLE `po`
  ADD PRIMARY KEY (`PO_number`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`Report_ID`),
  ADD KEY `report_ibfk_1` (`DO_id`),
  ADD KEY `report_ibfk_2` (`Log_ID`),
  ADD KEY `report_ibfk_3` (`Invoice_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`Supplier_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auditlog`
--
ALTER TABLE `auditlog`
  ADD CONSTRAINT `auditlog_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `ktm staff` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `do`
--
ALTER TABLE `do`
  ADD CONSTRAINT `do_ibfk_1` FOREIGN KEY (`PO_number`) REFERENCES `po` (`PO_number`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `do_ibfk_2` FOREIGN KEY (`Staff_id`) REFERENCES `ktm staff` (`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `do_ibfk_3` FOREIGN KEY (`supplier_ID`) REFERENCES `supplier` (`Supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`DO_id`) REFERENCES `do` (`DO_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`supplier_ID`) REFERENCES `supplier` (`Supplier_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`DO_id`) REFERENCES `do` (`DO_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
