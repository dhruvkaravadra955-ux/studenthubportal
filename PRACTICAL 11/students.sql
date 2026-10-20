-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2026 at 08:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `practical11`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `course`, `year`) VALUES
(1, 'Aarav Patel', 'aarav@example.com', 'BCA', 1),
(2, 'Diya Shah', 'diya@example.com', 'BCA', 2),
(3, 'Rohan Mehta', 'rohan@example.com', 'BCA', 3),
(4, 'Priya Desai', 'priya@example.com', 'BSc IT', 1),
(5, 'Karan Joshi', 'karan@example.com', 'BSc IT', 2),
(6, 'Neha Trivedi', 'neha@example.com', 'BSc IT', 3),
(7, 'Rahul Parmar', 'rahul@example.com', 'BTech', 1),
(8, 'Ananya Shah', 'ananya@example.com', 'BTech', 2),
(9, 'Vivek Patel', 'vivek@example.com', 'BTech', 3),
(10, 'Isha Mehta', 'isha@example.com', 'BCA', 1),
(11, 'Dev Shah', 'dev@example.com', 'BSc IT', 2),
(12, 'Mira Joshi', 'mira@example.com', 'BTech', 1),
(13, 'Jay Desai', 'jay@example.com', 'BCA', 2),
(14, 'Riya Patel', 'riya@example.com', 'BSc IT', 1),
(15, 'Harsh Mehta', 'harsh@example.com', 'BTech', 2),
(16, 'Nisha Parmar', 'nisha@example.com', 'BCA', 3),
(17, 'Krish Trivedi', 'krish@example.com', 'BSc IT', 3),
(18, 'Pooja Shah', 'pooja@example.com', 'BTech', 3),
(19, 'Yash Joshi', 'yash@example.com', 'BCA', 2),
(20, 'Tara Desai', 'tara@example.com', 'BSc IT', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
