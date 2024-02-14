-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 14, 2023 at 08:35 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `osa_portal_system`
--
CREATE DATABASE IF NOT EXISTS `osa_portal_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `osa_portal_system`;

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `college` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `role` smallint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `date_created` varchar(255) NOT NULL DEFAULT current_timestamp(),
  `descriptions` text NOT NULL,
  `image` varchar(300) NOT NULL,
  `is_archive` tinyint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `code_of_conduct_images`
--

CREATE TABLE `code_of_conduct_images` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_on` datetime NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complainant_message`
--

CREATE TABLE `complainant_message` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_course` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_message` text NOT NULL,
  `user_file` varchar(500) NOT NULL,
  `is_send` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publication_page`
--

CREATE TABLE `publication_page` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `image` varchar(300) NOT NULL,
  `descriptions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publish_post`
--

CREATE TABLE `publish_post` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `image` varchar(300) NOT NULL,
  `descriptions` text NOT NULL,
  `date_created` varchar(300) NOT NULL,
  `own_by` int(11) NOT NULL,
  `is_archive` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_and_eval`
--

CREATE TABLE `research_and_eval` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `date_created` varchar(100) NOT NULL,
  `descriptions` text NOT NULL,
  `is_archive` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spectrum_page`
--

CREATE TABLE `spectrum_page` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `image` varchar(300) NOT NULL,
  `descriptions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spectrum_post`
--

CREATE TABLE `spectrum_post` (
  `id` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `image` varchar(300) NOT NULL,
  `descriptions` text NOT NULL,
  `date_created` varchar(300) NOT NULL,
  `own_by` int(11) NOT NULL,
  `is_archive` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_handbook`
--

CREATE TABLE `student_handbook` (
  `id` int(11) NOT NULL,
  `file_name` varchar(500) NOT NULL,
  `uploaded_on` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `code_of_conduct_images`
--
ALTER TABLE `code_of_conduct_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complainant_message`
--
ALTER TABLE `complainant_message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publication_page`
--
ALTER TABLE `publication_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `publish_post`
--
ALTER TABLE `publish_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `own_by` (`own_by`);

--
-- Indexes for table `research_and_eval`
--
ALTER TABLE `research_and_eval`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spectrum_page`
--
ALTER TABLE `spectrum_page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spectrum_post`
--
ALTER TABLE `spectrum_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `own_by` (`own_by`);

--
-- Indexes for table `student_handbook`
--
ALTER TABLE `student_handbook`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `code_of_conduct_images`
--
ALTER TABLE `code_of_conduct_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complainant_message`
--
ALTER TABLE `complainant_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publication_page`
--
ALTER TABLE `publication_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `publish_post`
--
ALTER TABLE `publish_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research_and_eval`
--
ALTER TABLE `research_and_eval`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spectrum_page`
--
ALTER TABLE `spectrum_page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spectrum_post`
--
ALTER TABLE `spectrum_post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_handbook`
--
ALTER TABLE `student_handbook`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `publish_post`
--
ALTER TABLE `publish_post`
  ADD CONSTRAINT `publish_post_ibfk_1` FOREIGN KEY (`own_by`) REFERENCES `publication_page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
