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
-- Table structure for table `t_relation`
--

CREATE TABLE `t_relation` (
  `RELATION_CODE` int NOT NULL,
  `RELATION_DESC` varchar(50) NOT NULL,
  `INSERT_TIME` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `t_relation`
--

INSERT INTO `t_relation` (`RELATION_CODE`, `RELATION_DESC`, `INSERT_TIME`) VALUES
(1, 'Suami', '2022-06-27 11:34:33'),
(2, 'Istri', '2022-06-27 11:34:33'),
(3, 'Ayah', '2022-06-27 11:34:33'),
(4, 'Ibu', '2022-06-27 11:34:33'),
(5, 'Anak', '2022-06-27 11:34:33'),
(6, 'Adik', '2022-06-27 11:34:33'),
(7, 'Kakak', '2022-06-27 11:34:33'),
(8, 'Paman', '2022-06-27 11:34:33'),
(9, 'Bibi', '2022-06-27 11:34:33'),
(10, 'Keponakan', '2022-06-27 11:34:33'),
(11, 'Kakek', '2022-06-27 11:34:33'),
(12, 'Nenek', '2022-06-27 11:34:33'),
(13, 'Cucu', '2022-06-27 11:34:33'),
(14, 'Anak angkat', '2022-06-27 11:34:33'),
(15, 'Other', '2022-06-27 11:34:33'),
(16, 'Anak tiri', '2022-06-27 11:34:33'),
(17, 'Yayasan', '2022-06-27 11:34:33'),
(18, 'Perusahaan', '2022-06-27 11:34:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `t_relation`
--
ALTER TABLE `t_relation`
  ADD PRIMARY KEY (`RELATION_CODE`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
