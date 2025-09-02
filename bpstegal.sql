-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 02, 2025 at 11:34 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bpstegal`
--

-- --------------------------------------------------------

--
-- Table structure for table `carousel`
--

CREATE TABLE `carousel` (
  `id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `posisi` enum('start','center','end') COLLATE utf8mb4_general_ci DEFAULT 'center',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `infografis`
--

CREATE TABLE `infografis` (
  `id` int NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `infografis`
--

INSERT INTO `infografis` (`id`, `judul`, `deskripsi`, `gambar`, `tanggal`, `created_at`) VALUES
(1, 'Sensus Penduduk 2020', 'Jumlah Penduduk Jawa Barat Hasil SP 2020', '1756432767_b0a25241c5e9c02724ff.jpg', '2025-08-29', '2025-08-29 01:59:27'),
(2, 'Sensus Penduduk 2020', 'Jumlah Penduduk Jawa Barat Hasil SP 2020', '1756433187_2f031ca640e36ad98c53.jpg', '2025-08-29', '2025-08-29 02:06:27');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `token_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(13, 'jordandwifebri@gmail.com', '$2y$12$PRm3aVLZjVG59NRY.16dUuYscTrr/QXd39lzBT.ZuRizyJzz8UVFO', '2025-09-02 19:33:02', '2025-09-02 19:04:23', '2025-09-02 19:03:02'),
(14, 'jordandwifebri@gmail.com', '$2y$12$NiHHApJ8wDNTerpEBl4IoeLsc8y3TmhQmqNAoaiMCnjwlo0vMXxFG', '2025-09-02 19:36:08', '2025-09-02 19:06:26', '2025-09-02 19:06:08'),
(15, 'jordandwifebri@gmail.com', '$2y$12$mrmR7dgVXOHMUJ.N0nFS2ONaXMkCHwYKDyQrnYO4O3XzoM6sqf7Ly', '2025-09-02 19:37:36', '2025-09-02 19:15:48', '2025-09-02 19:07:36'),
(16, 'jordandwifebri@gmail.com', '$2y$12$EAQVecrmUOkNPxPigq9RnecYeJb8ZUCFsD59SsdFgRtM89tWUf0sy', '2025-09-02 19:45:48', NULL, '2025-09-02 19:15:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `email`, `phone`, `photo`, `password`, `role`, `created_at`) VALUES
(1, 'elvares', 'elva', 'elvareshadni@gmail.com', NULL, NULL, '$2y$12$MpQURkUvsl7Rirmdm7L8AuUsc2WM9Yo82VyKcT72JLxxNLC38SJAm', 'user', '2025-08-24 19:14:18'),
(2, 'jordan', 'jada', 'jojo@gmail.com', '125125125', NULL, '$2y$12$Y/WvmwC5Wm56PFpUl1drnuZ2xgQmNfyCvJbJnVZkcT5SevpWO1HDm', 'admin', '2025-08-31 16:15:59'),
(6, 'coolman23', 'Coolman023', 'coolman023.pro@gmail.com', '12152125', NULL, '$2y$12$9RaRLWLYNRtwvf5SqUCkru/WWFNsxFCVdfuzcuPvGSrBJM4aHElFu', 'user', '2025-09-02 18:51:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `infografis`
--
ALTER TABLE `infografis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infografis`
--
ALTER TABLE `infografis`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
