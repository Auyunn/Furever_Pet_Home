-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 01:09 PM
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
-- Database: `furever_pet_home`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` varchar(10) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `NumberPhone` varchar(15) NOT NULL,
  `Password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adopt_application`
--

CREATE TABLE `adopt_application` (
  `AdoptionID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `PetID` varchar(10) NOT NULL,
  `Status` enum('Submit','Pending','Approved','Rejected') NOT NULL,
  `Reason` varchar(250) DEFAULT NULL,
  `RequestDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `CommentID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `BoardID` varchar(10) NOT NULL,
  `Content` text NOT NULL,
  `Date` date NOT NULL,
  `ReplyID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `community_board`
--

CREATE TABLE `community_board` (
  `BoardID` varchar(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Content` text DEFAULT NULL,
  `Photo` varchar(100) DEFAULT NULL,
  `Date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `FaqID` int(11) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Question` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guidelines`
--

CREATE TABLE `guidelines` (
  `GuidelineID` int(11) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `PetType` enum('Dog','Cat') NOT NULL,
  `Description` text DEFAULT NULL,
  `Budget` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

CREATE TABLE `inbox` (
  `InboxID` varchar(10) NOT NULL,
  `ReportID` varchar(10) DEFAULT NULL,
  `AdoptionID` varchar(10) DEFAULT NULL,
  `Title` varchar(40) DEFAULT NULL,
  `Message` text DEFAULT NULL,
  `DateTime` datetime DEFAULT current_timestamp(),
  `Type` enum('Pet Adoption Application','Pet Report') DEFAULT NULL,
  `Status` enum('Approve','Reject','Pending','In Progress','Resolve') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `OrgID` varchar(10) NOT NULL,
  `OrgName` varchar(100) NOT NULL,
  `NumberPhone` varchar(15) NOT NULL,
  `OrgAddress` varchar(255) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pet`
--

CREATE TABLE `pet` (
  `PetID` varchar(10) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `PetType` enum('Dog','Cat') NOT NULL,
  `Breed` varchar(100) NOT NULL,
  `Age` int(11) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Neutered` tinyint(1) NOT NULL,
  `Allergies` varchar(255) DEFAULT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `PetName` varchar(100) NOT NULL,
  `IsAvailable` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `ReportID` varchar(10) NOT NULL,
  `ResidentID` varchar(5) NOT NULL,
  `OrgID` varchar(10) NOT NULL,
  `PetName` varchar(100) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Status` enum('Submit','Pending','Resolved') NOT NULL,
  `Photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `ResidentID` varchar(5) NOT NULL,
  `FirstName` varchar(20) NOT NULL,
  `LastName` varchar(20) NOT NULL,
  `NumberPhone` varchar(11) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Status` tinyint(1) DEFAULT 1,
  `Salary` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `adopt_application`
--
ALTER TABLE `adopt_application`
  ADD PRIMARY KEY (`AdoptionID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `PetID` (`PetID`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`CommentID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `BoardID` (`BoardID`),
  ADD KEY `ReplyID` (`ReplyID`) USING BTREE;

--
-- Indexes for table `community_board`
--
ALTER TABLE `community_board`
  ADD PRIMARY KEY (`BoardID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`FaqID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `guidelines`
--
ALTER TABLE `guidelines`
  ADD PRIMARY KEY (`GuidelineID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `inbox`
--
ALTER TABLE `inbox`
  ADD PRIMARY KEY (`InboxID`),
  ADD KEY `ReportID` (`ReportID`),
  ADD KEY `AdoptionID` (`AdoptionID`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`OrgID`);

--
-- Indexes for table `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`PetID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ReportID`),
  ADD KEY `ResidentID` (`ResidentID`),
  ADD KEY `OrgID` (`OrgID`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`ResidentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `FaqID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `guidelines`
--
ALTER TABLE `guidelines`
  MODIFY `GuidelineID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `adopt_application`
--
ALTER TABLE `adopt_application`
  ADD CONSTRAINT `adopt_application_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `adopt_application_ibfk_2` FOREIGN KEY (`PetID`) REFERENCES `pet` (`PetID`);

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`BoardID`) REFERENCES `community_board` (`BoardID`),
  ADD CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`ReplyID`) REFERENCES `comment` (`CommentID`);

--
-- Constraints for table `community_board`
--
ALTER TABLE `community_board`
  ADD CONSTRAINT `community_board_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `faq`
--
ALTER TABLE `faq`
  ADD CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `guidelines`
--
ALTER TABLE `guidelines`
  ADD CONSTRAINT `guidelines_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `inbox`
--
ALTER TABLE `inbox`
  ADD CONSTRAINT `inbox_ibfk_1` FOREIGN KEY (`ReportID`) REFERENCES `report` (`ReportID`) ON DELETE CASCADE,
  ADD CONSTRAINT `inbox_ibfk_2` FOREIGN KEY (`AdoptionID`) REFERENCES `adopt_application` (`AdoptionID`) ON DELETE CASCADE;

--
-- Constraints for table `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`ResidentID`) REFERENCES `resident` (`ResidentID`),
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`OrgID`) REFERENCES `organization` (`OrgID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
