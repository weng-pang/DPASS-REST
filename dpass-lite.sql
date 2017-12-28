-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2017 at 05:32 AM
-- Server version: 5.7.19
-- PHP Version: 7.0.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dpass-lite`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_keys`
--

CREATE TABLE `api_keys` (
  `key` varchar(64) NOT NULL,
  `created` datetime NOT NULL,
  `expire` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  `comment` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `api_keys`
--

INSERT INTO `api_keys` (`key`, `created`, `expire`, `revoked`, `comment`) VALUES
('1', '2014-12-16 00:00:00', '2014-12-31 00:00:00', 0, 'test only - non production use'),
('8311ff11-d4aa-4274-8a09-94a0c307340f', '2014-12-19 18:54:38', '2018-01-01 00:00:00', 0, 'WE-EAC extended 2yr 2015-09-10'),
('8dc74aa6-adf9-4442-9ce5-52024c838f1b', '2014-12-21 05:52:48', '2018-01-01 00:00:00', 0, 'WE-EAE extended 2yr 2015-09-10'),
('b83bc0d2-b3ca-4dc6-b47f-fec816d2a2f9', '2014-12-21 05:52:48', '2018-01-01 00:00:00', 0, 'Excel Access extended 2yr 2016-01-01'),
('c6c64058-814d-46f1-8872-ec75345b6202', '2014-12-21 05:52:48', '2016-01-01 00:00:00', 0, 'Reporters'),
('e8bed275-1687-41f9-8c14-30933ef4fd35', '2016-02-14 20:10:58', '2018-01-01 00:00:00', 0, 'R-SCT-01'),
('f2d6995d-8c18-4031-a669-deff329f4471', '2014-12-21 05:52:48', '2016-01-01 00:00:00', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `configurations`
--

CREATE TABLE `configurations` (
  `parameter` varchar(25) NOT NULL,
  `setting` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `configurations`
--

INSERT INTO `configurations` (`parameter`, `setting`) VALUES
('enabled', 1),
('https_only', 0);

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `serial` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(64) NOT NULL,
  `ip` varchar(128) NOT NULL,
  `description` varchar(500) NOT NULL,
  `time` datetime NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `serial` int(10) UNSIGNED NOT NULL,
  `id` smallint(5) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL,
  `machineid` smallint(5) UNSIGNED NOT NULL DEFAULT '999',
  `entryid` tinyint(3) UNSIGNED NOT NULL,
  `ipaddress` varchar(15) NOT NULL DEFAULT '0.0.0.0',
  `portnumber` smallint(5) UNSIGNED NOT NULL,
  `update` datetime NOT NULL,
  `key` varchar(64) NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `record_approvals`
--

CREATE TABLE `record_approvals` (
  `serial` int(10) UNSIGNED NOT NULL,
  `record_serial` int(10) UNSIGNED NOT NULL,
  `id` smallint(5) UNSIGNED NOT NULL,
  `power` smallint(5) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  `update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `record_status`
--

CREATE TABLE `record_status` (
  `serial` int(11) NOT NULL,
  `status` smallint(6) NOT NULL,
  `new_serial` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`id`, `name`, `name2`, `checkin`, `checkout`, `lunch`, `overtime`, `repeatedrange`, `overtimeapproval`) VALUES
(1, 'wpang', 'wpang', '06:19:21', '06:19:21', 60, 60, 10, -1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_keys`
--
ALTER TABLE `api_keys`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`parameter`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`serial`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`serial`);

--
-- Indexes for table `record_approvals`
--
ALTER TABLE `record_approvals`
  ADD PRIMARY KEY (`serial`),
  ADD KEY `record_serial` (`record_serial`);

--
-- Indexes for table `record_status`
--
ALTER TABLE `record_status`
  ADD PRIMARY KEY (`serial`,`status`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `serial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=558042;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `serial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355704;

--
-- AUTO_INCREMENT for table `record_approvals`
--
ALTER TABLE `record_approvals`
  MODIFY `serial` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `record_approvals`
--
ALTER TABLE `record_approvals`
  ADD CONSTRAINT `record_serial` FOREIGN KEY (`record_serial`) REFERENCES `records` (`serial`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
