-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 22, 2025 at 01:14 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fullstack`
--

-- --------------------------------------------------------

--
-- Table structure for table `t_document`
--

CREATE TABLE `t_document` (
  `TYPE_ID` int NOT NULL,
  `TYPE_NAME` varchar(100) NOT NULL,
  `INSERT_TIME` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UPDATED_BY` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_document`
--

INSERT INTO `t_document` (`TYPE_ID`, `TYPE_NAME`, `INSERT_TIME`, `UPDATED_BY`) VALUES
(1, 'E-KTP', '2023-11-13 16:17:12', NULL),
(2, 'ID WNA', '2023-11-13 16:17:12', NULL),
(3, 'Akta Lahir', '2023-11-13 16:17:12', NULL),
(4, 'SKL', '2023-11-13 16:17:12', NULL),
(5, 'KIM/KITAS', '2023-11-13 16:17:12', NULL),
(6, 'SIM', '2023-11-13 16:17:12', NULL),
(7, 'Passport', '2023-11-13 16:17:12', NULL),
(8, 'KIA', '2023-11-13 16:17:12', NULL),
(9, 'Others', '2023-11-13 16:17:12', NULL),
(10, 'KK', '2023-11-13 16:17:12', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_document`
--
ALTER TABLE `t_document`
  ADD PRIMARY KEY (`TYPE_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
