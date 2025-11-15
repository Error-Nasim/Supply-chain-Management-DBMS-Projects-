-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 14, 2025 at 07:07 AM
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
-- Database: `scms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `area_id` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `area_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`area_id`, `area_name`, `area_code`) VALUES
(1, 'Dhaka Division', 'DHA'),
(2, 'Chittagong Division', 'CTG'),
(3, 'Sylhet Division', 'SYL'),
(4, 'Khulna Division', 'KH'),
(5, 'Rajshahi Division', 'RAJ'),
(6, 'Barisal Division', 'BAR'),
(7, 'Rangpur Division', 'RAN'),
(8, 'Mymensingh Division', 'MYM');

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice`
--

INSERT INTO `invoice` (`invoice_id`, `order_id`, `manufacturer_id`, `invoice_date`, `total_amount`, `status`) VALUES
(1, 2, 2, '2024-01-22', 1850.00, 'Completed'),
(2, 7, 3, '2024-01-27', 2750.00, 'Paid'),
(12, 11, 1, '2025-10-30', 4765.00, 'Completed'),
(13, 12, 1, '2025-10-30', 260.00, 'Completed'),
(14, 14, 2, '2025-10-30', 40560.00, 'Completed'),
(15, 16, 1, '2025-10-30', 4505.00, 'Completed'),
(16, 15, 1, '2025-11-01', 125.00, 'Completed'),
(17, 19, 1, '2025-11-01', 140.00, 'Completed'),
(18, 17, 1, '2025-11-01', 1700.00, 'Completed'),
(19, 21, 1, '2025-11-01', 45305.00, 'Completed'),
(20, 24, 1, '2025-11-01', 1485.00, 'Completed'),
(21, 26, 1, '2025-11-01', 135.00, 'Completed'),
(22, 30, 1, '2025-11-01', 75.00, 'Completed'),
(23, 42, 1, '2025-11-01', 43350.00, 'Completed'),
(24, 43, 1, '2025-11-01', 43350.00, 'Completed'),
(25, 46, 1, '2025-11-01', 125.00, 'Completed'),
(26, 48, 1, '2025-11-01', 125.00, 'Completed'),
(27, 50, 1, '2025-11-05', 125.00, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer`
--

CREATE TABLE `manufacturer` (
  `man_id` int(11) NOT NULL,
  `man_name` varchar(255) NOT NULL,
  `man_email` varchar(255) NOT NULL,
  `man_phone` varchar(15) NOT NULL,
  `man_address` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturer`
--

INSERT INTO `manufacturer` (`man_id`, `man_name`, `man_email`, `man_phone`, `man_address`, `username`, `password`, `date`) VALUES
(1, 'Dhaka Food Industries Ltd.', 'contact@dhakafood.com.bd', '01712000001', 'Plot 15, BSCIC Industrial Area, Tejgaon, Dhaka-1208', 'dhaka_food', 'dhaka123', '2024-01-01 06:00:00'),
(2, 'Bengal Manufacturing Ltd.', 'info@bengalmfg.com.bd', '01823000002', 'Nasirabad Industrial Area, Chittagong-4220', 'bengal_mfg', 'bengal123', '2024-01-01 06:30:00'),
(3, 'Chittagong Sweets & Snacks Co.', 'orders@ctgsweets.com.bd', '01934000003', 'Halishahar Industrial Area, Chittagong-4216', 'ctg_sweets', 'ctg123', '2024-01-01 07:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `manufacturer_stock`
--

CREATE TABLE `manufacturer_stock` (
  `stock_id` int(11) NOT NULL,
  `manufacturer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manufacturer_stock`
--

INSERT INTO `manufacturer_stock` (`stock_id`, `manufacturer_id`, `product_id`, `quantity`, `last_updated`) VALUES
(1, 1, 1, 470, '2025-11-05 18:15:00'),
(2, 1, 2, 194, '2025-11-01 18:44:02'),
(3, 1, 3, 179, '2025-11-01 18:44:02'),
(4, 1, 4, 145, '2025-11-01 18:44:02'),
(5, 1, 5, 400, '2025-11-01 18:44:02'),
(6, 2, 6, 996, '2025-11-01 18:44:02'),
(7, 2, 7, 209, '2025-11-01 18:44:02'),
(8, 2, 8, 18, '2025-11-01 18:44:02'),
(9, 2, 9, 194, '2025-11-01 18:44:02'),
(10, 2, 10, 496, '2025-11-01 18:44:02'),
(11, 3, 11, 118, '2025-11-01 18:44:02'),
(12, 3, 12, 80, '2025-11-01 18:44:02'),
(13, 3, 13, 60, '2025-11-01 18:44:02'),
(14, 3, 14, 150, '2025-11-01 18:44:02'),
(15, 3, 15, 200, '2025-11-01 18:44:02'),
(16, 3, 18, 300, '2025-11-01 18:44:02'),
(17, 1, 19, 100, '2025-11-01 18:44:02'),
(18, 2, 20, 250, '2025-11-01 18:44:02'),
(32, 1, 6, 0, '2025-11-01 18:52:27'),
(33, 1, 9, 0, '2025-11-01 18:50:22'),
(35, 1, 15, 0, '2025-11-01 18:49:36'),
(37, 1, 13, 0, '2025-11-01 18:49:36'),
(38, 1, 20, 0, '2025-11-01 18:49:36'),
(40, 1, 8, 0, '2025-11-01 18:49:36'),
(42, 1, 18, 0, '2025-11-01 18:49:36'),
(43, 1, 10, 0, '2025-11-01 18:49:36'),
(44, 1, 14, 0, '2025-11-01 18:49:36'),
(45, 1, 7, 0, '2025-11-01 18:49:36'),
(47, 1, 11, 0, '2025-11-01 18:49:36'),
(48, 1, 12, 0, '2025-11-01 18:49:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `retailer_id` int(11) NOT NULL,
  `assigned_manufacturer_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_by` varchar(50) DEFAULT NULL,
  `cancelled_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `retailer_id`, `assigned_manufacturer_id`, `date`, `total_amount`, `approved`, `status`, `cancellation_reason`, `cancelled_by`, `cancelled_date`) VALUES
(1, 1, 1, '2024-01-20', 2500.00, 1, 1, NULL, NULL, NULL),
(2, 2, 2, '2024-01-21', 1850.00, 1, 1, NULL, NULL, NULL),
(3, 3, 2, '2024-01-22', 3200.00, 1, 1, NULL, NULL, NULL),
(4, 4, 2, '2024-01-23', 1650.00, 1, 1, NULL, NULL, NULL),
(5, 5, 2, '2024-01-24', 4200.00, 1, 1, NULL, NULL, NULL),
(6, 1, 2, '2024-01-25', 1900.00, 1, 1, NULL, NULL, NULL),
(7, 2, 2, '2024-01-26', 2750.00, 1, 1, NULL, NULL, NULL),
(8, 3, 2, '2024-01-27', 3850.00, 1, 1, NULL, NULL, NULL),
(11, 10, 2, '2025-10-30', 4765.00, 1, 1, NULL, NULL, NULL),
(12, 10, 2, '2025-10-30', 10440.00, 1, 1, NULL, NULL, NULL),
(13, 10, 2, '2025-10-30', 43350.00, 1, 2, '', 'admin', '2025-11-01 21:30:15'),
(14, 10, 2, '2025-10-30', 40560.00, 1, 1, NULL, NULL, NULL),
(15, 10, 2, '2025-10-30', 125.00, 1, 1, NULL, NULL, NULL),
(16, 10, 1, '2025-10-30', 4505.00, 1, 1, NULL, NULL, NULL),
(17, 10, 1, '2025-11-01', 1700.00, 1, 1, NULL, NULL, NULL),
(18, 10, 1, '2025-11-01', 125.00, 1, 2, 'nothing', 'admin', '2025-11-01 21:53:25'),
(19, 10, 1, '2025-11-01', 140.00, 1, 1, NULL, NULL, NULL),
(20, 10, 1, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-01 23:54:41'),
(21, 10, 1, '2025-11-01', 45305.00, 0, 1, '', 'admin', '2025-11-02 00:04:11'),
(22, 10, 1, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-02 00:08:21'),
(23, 10, 1, '2025-11-01', 75.00, 0, 2, '', 'retailer', '2025-11-01 23:57:41'),
(24, 10, 1, '2025-11-01', 1485.00, 1, 1, NULL, NULL, NULL),
(25, 10, NULL, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-02 00:10:59'),
(26, 10, 1, '2025-11-01', 135.00, 1, 1, NULL, NULL, NULL),
(27, 10, 2, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-02 00:23:25'),
(28, 10, NULL, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-02 00:30:18'),
(29, 10, 1, '2025-11-01', 4505.00, 0, 2, '', 'admin', '2025-11-02 00:31:21'),
(30, 10, 1, '2025-11-01', 75.00, 1, 1, NULL, NULL, NULL),
(31, 10, NULL, '2025-11-01', 75.00, 0, 2, '', 'admin', '2025-11-02 00:35:50'),
(32, 10, NULL, '2025-11-01', 125.00, 0, 2, '', 'admin', '2025-11-02 00:37:46'),
(33, 10, NULL, '2025-11-01', 135.00, 0, 2, '', 'admin', '2025-11-02 00:39:16'),
(34, 10, NULL, '2025-11-01', 360.00, 0, 2, 'yes', 'retailer', '2025-11-02 00:38:47'),
(35, 10, 1, '2025-11-01', 685.00, 0, 2, '', 'admin', '2025-11-02 00:41:22'),
(36, 10, NULL, '2025-11-01', 4765.00, 0, 2, '', 'admin', '2025-11-02 01:07:36'),
(37, 10, NULL, '2025-11-01', 6115.00, 0, 2, '', 'retailer', '2025-11-02 01:49:49'),
(38, 10, NULL, '2025-11-01', 4580.00, 0, 2, '', 'retailer', '2025-11-02 01:49:52'),
(39, 10, NULL, '2025-11-01', 720.00, 0, 2, '', 'admin', '2025-11-02 01:53:26'),
(40, 10, NULL, '2025-11-01', 3250.00, 0, 2, '', 'admin', '2025-11-02 01:54:40'),
(41, 10, NULL, '2025-11-01', 75.00, 0, 2, '', 'admin', '2025-11-02 01:54:04'),
(42, 10, NULL, '2025-11-01', 43350.00, 0, 2, '', 'retailer', '2025-11-02 01:54:14'),
(43, 10, 1, '2025-11-01', 43350.00, 1, 1, NULL, NULL, NULL),
(44, 10, NULL, '2025-11-01', 38000.00, 0, 2, '', 'admin', '2025-11-02 02:02:24'),
(45, 10, 1, '2025-11-01', 720.00, 0, 2, '', 'admin', '2025-11-02 02:02:27'),
(46, 10, 1, '2025-11-01', 125.00, 1, 1, NULL, NULL, NULL),
(47, 10, NULL, '2025-11-01', 720.00, 0, 2, '', 'admin', '2025-11-02 02:05:56'),
(48, 10, 1, '2025-11-01', 125.00, 1, 1, NULL, NULL, NULL),
(49, 10, NULL, '2025-11-05', 4745.00, 0, 2, '', 'admin', '2025-11-06 00:15:33'),
(50, 10, 1, '2025-11-05', 125.00, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `pro_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 1, 50, 25.00, 1250.00),
(2, 1, 2, 20, 45.00, 900.00),
(3, 1, 5, 10, 35.00, 350.00),
(4, 2, 6, 10, 180.00, 1800.00),
(6, 3, 11, 5, 320.00, 1600.00),
(7, 3, 12, 4, 380.00, 1520.00),
(8, 3, 15, 1, 95.00, 95.00),
(9, 4, 7, 5, 220.00, 1100.00),
(10, 4, 10, 4, 140.00, 560.00),
(11, 5, 8, 3, 850.00, 2550.00),
(12, 5, 9, 2, 650.00, 1300.00),
(14, 6, 3, 15, 85.00, 1275.00),
(15, 6, 4, 5, 120.00, 600.00),
(16, 7, 13, 5, 420.00, 2100.00),
(17, 7, 14, 10, 65.00, 650.00),
(18, 8, 19, 20, 150.00, 3000.00),
(19, 8, 20, 12, 75.00, 900.00),
(20, 11, 1, 5, 25.00, 125.00),
(21, 11, 2, 3, 45.00, 135.00),
(22, 11, 3, 53, 85.00, 4505.00),
(23, 12, 1, 5, 25.00, 125.00),
(24, 12, 2, 3, 45.00, 135.00),
(25, 12, 6, 4, 180.00, 720.00),
(26, 12, 7, 43, 220.00, 9460.00),
(27, 13, 8, 51, 850.00, 43350.00),
(28, 14, 7, 43, 220.00, 9460.00),
(29, 14, 8, 32, 850.00, 27200.00),
(30, 14, 9, 6, 650.00, 3900.00),
(31, 15, 1, 5, 25.00, 125.00),
(32, 16, 3, 53, 85.00, 4505.00),
(33, 17, 1, 3, 25.00, 75.00),
(34, 17, 2, 33, 45.00, 1485.00),
(35, 17, 5, 4, 35.00, 140.00),
(36, 18, 1, 5, 25.00, 125.00),
(37, 19, 5, 4, 35.00, 140.00),
(38, 20, 1, 5, 25.00, 125.00),
(39, 21, 3, 533, 85.00, 45305.00),
(40, 22, 1, 5, 25.00, 125.00),
(41, 23, 1, 3, 25.00, 75.00),
(42, 24, 2, 33, 45.00, 1485.00),
(43, 25, 1, 5, 25.00, 125.00),
(44, 26, 2, 3, 45.00, 135.00),
(45, 27, 1, 5, 25.00, 125.00),
(46, 28, 1, 5, 25.00, 125.00),
(47, 29, 3, 53, 85.00, 4505.00),
(48, 30, 1, 3, 25.00, 75.00),
(49, 31, 1, 3, 25.00, 75.00),
(50, 32, 1, 5, 25.00, 125.00),
(51, 33, 2, 3, 45.00, 135.00),
(52, 34, 4, 3, 120.00, 360.00),
(53, 35, 1, 5, 25.00, 125.00),
(54, 35, 10, 4, 140.00, 560.00),
(55, 36, 1, 5, 25.00, 125.00),
(56, 36, 2, 3, 45.00, 135.00),
(57, 36, 3, 53, 85.00, 4505.00),
(58, 37, 1, 5, 25.00, 125.00),
(59, 37, 2, 33, 45.00, 1485.00),
(60, 37, 3, 53, 85.00, 4505.00),
(61, 38, 1, 3, 25.00, 75.00),
(62, 38, 3, 53, 85.00, 4505.00),
(63, 39, 6, 4, 180.00, 720.00),
(64, 40, 9, 5, 650.00, 3250.00),
(65, 41, 1, 3, 25.00, 75.00),
(66, 42, 8, 51, 850.00, 43350.00),
(67, 43, 8, 51, 850.00, 43350.00),
(68, 44, 12, 100, 380.00, 38000.00),
(69, 45, 6, 4, 180.00, 720.00),
(70, 46, 1, 5, 25.00, 125.00),
(71, 47, 6, 4, 180.00, 720.00),
(72, 48, 1, 5, 25.00, 125.00),
(73, 49, 1, 5, 25.00, 125.00),
(74, 49, 6, 4, 180.00, 720.00),
(75, 49, 9, 6, 650.00, 3900.00),
(76, 50, 1, 5, 25.00, 125.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `pro_id` int(11) NOT NULL,
  `pro_name` varchar(255) NOT NULL,
  `pro_desc` text DEFAULT NULL,
  `pro_price` decimal(10,2) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`pro_id`, `pro_name`, `pro_desc`, `pro_price`, `unit_id`, `added_date`) VALUES
(1, 'Burger Bun', 'Fresh burger buns made daily', 25.00, 3, '2024-01-15 06:00:00'),
(2, 'Pizza Base', 'Ready-to-use pizza base', 45.00, 3, '2024-01-15 06:05:00'),
(3, 'Chicken Patty', 'Frozen chicken patties', 85.00, 3, '2024-01-15 06:10:00'),
(4, 'French Fries', 'Frozen french fries', 120.00, 1, '2024-01-15 06:15:00'),
(5, 'Soft Drinks', 'Assorted soft drinks', 35.00, 8, '2024-01-15 06:20:00'),
(6, 'Basmati Rice', 'Premium quality basmati rice', 180.00, 1, '2024-01-15 06:25:00'),
(7, 'Mustard Oil', 'Pure mustard oil', 220.00, 2, '2024-01-15 06:30:00'),
(8, 'Hilsa Fish', 'Fresh hilsa fish from Padma river', 850.00, 1, '2024-01-15 06:35:00'),
(9, 'Beef', 'Fresh beef from local farms', 650.00, 1, '2024-01-15 06:40:00'),
(10, 'Lentils (Dal)', 'Mixed lentils variety pack', 140.00, 1, '2024-01-15 06:45:00'),
(11, 'Roshogolla', 'Traditional Bengali roshogolla', 320.00, 1, '2024-01-15 06:50:00'),
(12, 'Sandesh', 'Fresh milk sandesh', 380.00, 1, '2024-01-15 06:55:00'),
(13, 'Chomchom', 'Syrupy chomchom sweets', 420.00, 1, '2024-01-15 07:00:00'),
(14, 'Mishti Doi', 'Sweet yogurt in clay pots', 65.00, 3, '2024-01-15 07:05:00'),
(15, 'Chanachur', 'Spicy Bengali snack mix', 95.00, 4, '2024-01-15 07:10:00'),
(18, 'Jhalmuri', 'Spicy puffed rice snack', 25.00, 4, '2024-01-15 07:25:00'),
(19, 'Ice Cream', 'Assorted flavored ice cream', 150.00, 3, '2024-01-15 07:30:00'),
(20, 'Fish Curry Masala', 'Special fish curry spice mix', 75.00, 4, '2024-01-15 07:35:00');

-- --------------------------------------------------------

--
-- Table structure for table `retailer`
--

CREATE TABLE `retailer` (
  `retailer_id` int(11) NOT NULL,
  `retailer_name` varchar(255) NOT NULL,
  `retailer_email` varchar(255) NOT NULL,
  `retailer_phone` varchar(15) NOT NULL,
  `retailer_address` text NOT NULL,
  `area_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `retailer`
--

INSERT INTO `retailer` (`retailer_id`, `retailer_name`, `retailer_email`, `retailer_phone`, `retailer_address`, `area_id`, `username`, `password`, `date`) VALUES
(1, 'Bashundhara City Shopping Complex', 'info@bashundhara.com.bd', '01712111001', 'Level 3, Bashundhara City, Panthapath, Dhaka-1205', 1, 'bashundhara_shop', 'bash123', '2024-01-05 06:00:00'),
(2, 'New Market Food Court', 'orders@newmarket.com.bd', '01823222002', 'Shop 45-47, New Market, Dhaka-1205', 1, 'newmarket_food', 'new123', '2024-01-05 06:30:00'),
(3, 'GEC Circle Mart', 'contact@gecmart.com.bd', '01934333003', 'GEC Circle, Chittagong-4000', 2, 'gec_mart', 'gec123', '2024-01-05 07:00:00'),
(4, 'Zindabazar Super Shop', 'info@zindabazar.com.bd', '01745444004', '152 Zindabazar, Sylhet-3100', 3, 'zinda_shop', 'zinda123', '2024-01-05 07:30:00'),
(5, 'Khulna City Center', 'orders@khulnacity.com.bd', '01856555005', 'Royal More, Khulna-9100', 4, 'khulna_center', 'khulna123', '2024-01-05 08:00:00'),
(10, 'nasim', 'nabasim1213@gmail.com', '01315365416', 'tsdfgsdf', 1, 'nasim', 'nas123', '2025-10-30 14:18:19');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL,
  `unit_details` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`unit_id`, `unit_name`, `unit_details`) VALUES
(1, 'Kg', 'Kilogram - Weight measurement'),
(2, 'Liter', 'Liter - Volume measurement'),
(3, 'Piece', 'Individual pieces'),
(4, 'Packet', 'Packaged units'),
(5, 'Box', 'Boxed items'),
(6, 'Dozen', '12 pieces'),
(7, 'Gram', 'Gram - Small weight measurement'),
(8, 'Bottle', 'Bottled items');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`area_id`),
  ADD UNIQUE KEY `area_code` (`area_code`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `manufacturer_id` (`manufacturer_id`);

--
-- Indexes for table `manufacturer`
--
ALTER TABLE `manufacturer`
  ADD PRIMARY KEY (`man_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `manufacturer_stock`
--
ALTER TABLE `manufacturer_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD UNIQUE KEY `unique_manufacturer_product` (`manufacturer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `retailer_id` (`retailer_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_cancelled_date` (`cancelled_date`),
  ADD KEY `idx_assigned_manufacturer` (`assigned_manufacturer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `pro_id` (`pro_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`pro_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `retailer`
--
ALTER TABLE `retailer`
  ADD PRIMARY KEY (`retailer_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `area_id` (`area_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`unit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `manufacturer`
--
ALTER TABLE `manufacturer`
  MODIFY `man_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `manufacturer_stock`
--
ALTER TABLE `manufacturer_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `pro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `retailer`
--
ALTER TABLE `retailer`
  MODIFY `retailer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`man_id`);

--
-- Constraints for table `manufacturer_stock`
--
ALTER TABLE `manufacturer_stock`
  ADD CONSTRAINT `manufacturer_stock_ibfk_1` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`man_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `manufacturer_stock_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`pro_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`retailer_id`) REFERENCES `retailer` (`retailer_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`pro_id`) REFERENCES `products` (`pro_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`unit_id`);

--
-- Constraints for table `retailer`
--
ALTER TABLE `retailer`
  ADD CONSTRAINT `retailer_ibfk_1` FOREIGN KEY (`area_id`) REFERENCES `area` (`area_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
