-- DPASS-REST Database Schema
-- This file contains the Data Definition Language (DDL) for the DPASS-REST application
-- 
-- Usage:
--   1. Create a new database: CREATE DATABASE `dpass-lite` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
--   2. Import this file: mysql -u username -p dpass-lite < dpass-rest.sql
--
-- Note: This file contains only the schema and sample data structure.
--       Production API keys and sensitive data have been removed for security.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Table structure for table `api_keys`
-- Stores API authentication keys with expiration and revocation support
-- --------------------------------------------------------

CREATE TABLE `api_keys` (
  `key` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `expire` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `comment` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data structure (replace with your actual API keys)
-- INSERT INTO `api_keys` (`key`, `created`, `expire`, `revoked`, `comment`) VALUES
-- ('your-api-key-here', '2024-01-01 00:00:00', '2025-12-31 23:59:59', 0, 'Description of API key usage');

-- --------------------------------------------------------
-- Table structure for table `configurations`
-- System-wide configuration parameters
-- --------------------------------------------------------

CREATE TABLE `configurations` (
  `parameter` varchar(25) NOT NULL,
  `setting` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Default configuration values
INSERT INTO `configurations` (`parameter`, `setting`) VALUES
('enabled', 1),
('https_only', 0);

-- --------------------------------------------------------
-- Table structure for table `log`
-- Application activity and audit log
-- --------------------------------------------------------

CREATE TABLE `log` (
  `serial` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `description` varchar(500) NOT NULL,
  `time` datetime NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `records`
-- Main records table for storing entry data
-- --------------------------------------------------------

CREATE TABLE `records` (
  `serial` int(10) UNSIGNED NOT NULL,
  `id` smallint(5) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `machineid` smallint(5) UNSIGNED NOT NULL DEFAULT 999,
  `entryid` tinyint(3) UNSIGNED NOT NULL,
  `ipaddress` varchar(15) NOT NULL DEFAULT '0.0.0.0',
  `portnumber` smallint(5) UNSIGNED NOT NULL,
  `update` datetime NOT NULL,
  `key` varchar(64) NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `record_approvals`
-- Approval workflow for records
-- --------------------------------------------------------

CREATE TABLE `record_approvals` (
  `serial` int(10) UNSIGNED NOT NULL,
  `record_serial` int(10) UNSIGNED NOT NULL,
  `id` smallint(5) UNSIGNED NOT NULL,
  `power` smallint(5) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `update` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `record_status`
-- Status tracking for records
-- --------------------------------------------------------

CREATE TABLE `record_status` (
  `serial` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  `new_serial` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `staffs`
-- Staff information and work schedule
-- --------------------------------------------------------

CREATE TABLE `staffs` (
  `id` smallint(6) NOT NULL,
  `name` varchar(50) NOT NULL,
  `name2` varchar(50) DEFAULT NULL,
  `checkin` time NOT NULL,
  `checkout` time NOT NULL,
  `lunch` int(11) NOT NULL,
  `overtime` int(11) NOT NULL,
  `repeatedrange` smallint(6) NOT NULL,
  `overtimeapproval` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Indexes and Primary Keys
-- --------------------------------------------------------

ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`key`);

ALTER TABLE `configurations`
  ADD PRIMARY KEY (`parameter`);

ALTER TABLE `log`
  ADD PRIMARY KEY (`serial`);

ALTER TABLE `records`
  ADD PRIMARY KEY (`serial`);

ALTER TABLE `record_approvals`
  ADD PRIMARY KEY (`serial`),
  ADD KEY `record_serial` (`record_serial`);

ALTER TABLE `record_status`
  ADD PRIMARY KEY (`serial`,`status`);

ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`);

-- --------------------------------------------------------
-- AUTO_INCREMENT settings
-- --------------------------------------------------------

ALTER TABLE `log`
  MODIFY `serial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `records`
  MODIFY `serial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `record_approvals`
  MODIFY `serial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------
-- Foreign Key Constraints
-- --------------------------------------------------------

ALTER TABLE `record_approvals`
  ADD CONSTRAINT `record_serial` FOREIGN KEY (`record_serial`) REFERENCES `records` (`serial`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
