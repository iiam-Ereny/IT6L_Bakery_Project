-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 08, 2025 at 05:29 AM
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
-- Database: `bakery_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`) VALUES
(1, 'Pastry'),
(2, 'Bread'),
(3, 'bottled'),
(4, 'cake'),
(5, 'can');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `EmployeeID` int(11) NOT NULL,
  `First_name` varchar(50) DEFAULT NULL,
  `Last_name` varchar(50) DEFAULT NULL,
  `Position` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Phone_number` varchar(20) DEFAULT NULL,
  `DateHired` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`EmployeeID`, `First_name`, `Last_name`, `Position`, `Email`, `Phone_number`, `DateHired`) VALUES
(4, 'Jedrik', 'Saniel', 'baker', 'jedrik@gmail.com', '09092323232', '2025-03-22'),
(5, 'Vladimir', 'Putin', '69', 'vlad@gmail.com', '09121212123', '2025-03-20'),
(6, 'Miguel', 'Tayao', 'baker', 'miggytayao98@gmail.com', '09122016094', '2025-03-08');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `ID` int(11) NOT NULL,
  `Item_Name` varchar(255) NOT NULL,
  `Category` varchar(100) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Stock_Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`ID`, `Item_Name`, `Category`, `Price`, `Stock_Quantity`) VALUES
(2, 'Bread Flour', 'Flour', 22.00, 55),
(4, 'Wheat Flour', 'flour', 500.00, 99999);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL,
  `Order_Date` date NOT NULL,
  `Payment_Method` varchar(50) NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`OrderID`, `Order_Date`, `Payment_Method`, `Amount`) VALUES
(10, '2025-03-02', 'cash', 125.00),
(11, '2025-03-02', 'cash', 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `ProductID` int(11) NOT NULL,
  `Product_Name` varchar(100) NOT NULL,
  `categoryID` varchar(255) DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Stock_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`ProductID`, `Product_Name`, `categoryID`, `Price`, `Stock_quantity`) VALUES
(2, 'Lolipop', '2', 2.00, 100),
(7, 'pandepane', '1', 25.00, 50),
(11, 'Jedrik', '3', 25.00, 50);

-- --------------------------------------------------------

--
-- Table structure for table `yields`
--

CREATE TABLE `yields` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_produced` date NOT NULL,
  `description` text DEFAULT NULL,
  `BakerID` int(11) NOT NULL,
  `baker` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yields`
--

INSERT INTO `yields` (`id`, `product_name`, `quantity`, `date_produced`, `description`, `BakerID`, `baker`) VALUES
(3, 'pan de coco', 500, '2025-03-08', 'a batch of dough made 500 pan de coco baked by a baker', 0, 4),
(4, 'Choco Lanay', 50, '2025-03-08', 'a batch of dough made 100 choco lanay baked by a baker', 0, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`EmployeeID`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `CategoryID` (`categoryID`);

--
-- Indexes for table `yields`
--
ALTER TABLE `yields`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `yields`
--
ALTER TABLE `yields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `order_item_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`),
  ADD CONSTRAINT `order_item_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `product` (`ProductID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
