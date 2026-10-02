-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2026 at 05:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `staff_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `booking_time` datetime NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT 'cash',
  `payment_status` varchar(50) DEFAULT 'unpaid',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `customer_id`, `branch_id`, `staff_id`, `service_id`, `booking_time`, `status`, `created_at`, `payment_method`, `payment_status`, `updated_at`) VALUES
(1, 4, NULL, 5, 1, '2026-09-08 16:16:00', 'completed', '2026-09-07 23:14:10', 'qr', 'paid', '2026-09-07 23:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`) VALUES
(1, 'Chi nhánh Hải Châu', '123 Nguyễn Văn Linh, Đà Nẵng'),
(2, 'Chi nhánh Thanh Khê', '456 Điện Biên Phủ, Đà Nẵng');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_threshold` int(11) NOT NULL DEFAULT 5,
  `unit` varchar(30) NOT NULL DEFAULT 'cái',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `branch_id`, `name`, `quantity`, `min_threshold`, `unit`, `created_at`, `updated_at`) VALUES
(1, 1, 'Thuốc nhuộm Loreal N.4', 2, 5, 'tuýp', '2026-09-08 06:11:13', '2026-09-08 06:11:13'),
(2, 1, 'Thuốc uốn setting cao cấp', 12, 5, 'gói', '2026-09-08 06:11:13', '2026-09-08 06:11:13'),
(3, 2, 'Dầu gội phục hồi Keratin 1000ml', 1, 3, 'chai', '2026-09-08 06:11:13', '2026-09-08 06:11:13');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `appointment_id`, `customer_id`, `staff_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 5, 5, 'Tốt', '2026-09-07 23:17:13', '2026-09-07 23:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `price`, `created_at`, `updated_at`) VALUES
(1, 'Cắt tóc + Gội massage', 100000.00, '2026-09-08 06:10:30', '2026-09-08 06:10:30'),
(2, 'Uốn tạo kiểu tiêu chuẩn', 350000.00, '2026-09-08 06:10:30', '2026-09-08 06:10:30'),
(3, 'Tẩy & Nhuộm thời trang', 500000.00, '2026-09-08 06:10:30', '2026-09-08 06:10:30'),
(4, 'Nhuộm', 300000.00, '2026-09-07 23:10:35', '2026-09-07 23:10:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Quản Trị Viên', 'admin@salon.com', '$2y$10$nIN16JOIuJjBQSmr8ctzGOWU/pBazqrtyCFfiZZ/ocLpNlmJU26Qq', 'admin', '2026-09-08 06:11:14', '2026-09-08 06:11:14'),
(2, 'Barber Trần Nam', 'barber1@salon.com', '$2y$10$nIN16JOIuJjBQSmr8ctzGOWU/pBazqrtyCFfiZZ/ocLpNlmJU26Qq', 'staff', '2026-09-08 06:11:14', '2026-09-08 06:11:14'),
(3, 'Barber Lê Tùng', 'barber2@salon.com', '$2y$10$nIN16JOIuJjBQSmr8ctzGOWU/pBazqrtyCFfiZZ/ocLpNlmJU26Qq', 'staff', '2026-09-08 06:11:14', '2026-09-08 06:11:14'),
(4, 'Nguyễn Văn A', 'khachhang@gmail.com', '$2y$10$nIN16JOIuJjBQSmr8ctzGOWU/pBazqrtyCFfiZZ/ocLpNlmJU26Qq', 'customer', '2026-09-08 06:11:14', '2026-09-08 06:11:14'),
(5, 'Tùng', 'tungbarber3@salon.com', '$2y$10$nIN16JOIuJjBQSmr8ctzGOWU/pBazqrtyCFfiZZ/ocLpNlmJU26Qq', 'staff', '2026-09-08 06:11:14', '2026-09-08 06:11:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
