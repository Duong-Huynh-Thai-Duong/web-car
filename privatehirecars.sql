-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 26, 2026 at 07:56 AM
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
-- Database: `privatehirecars`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `VehicleID` int(11) DEFAULT NULL,
  `DriverID` int(11) DEFAULT NULL,
  `PickupLocation` varchar(255) NOT NULL,
  `Destination` varchar(255) NOT NULL,
  `PickupDateTime` datetime NOT NULL,
  `PassengerCount` int(11) DEFAULT 1,
  `VehicleSizeReq` varchar(50) DEFAULT '4-seater',
  `Status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `TotalFare` decimal(10,2) DEFAULT NULL,
  `OfferID` int(11) DEFAULT NULL,
  `PaymentMethod` enum('Cash','Card','PayPal') DEFAULT 'Cash',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`BookingID`, `CustomerID`, `VehicleID`, `DriverID`, `PickupLocation`, `Destination`, `PickupDateTime`, `PassengerCount`, `VehicleSizeReq`, `Status`, `TotalFare`, `OfferID`, `PaymentMethod`, `CreatedAt`) VALUES
(1001, 999, NULL, NULL, 'Tan Son Nhat Airport', 'District 1', '2026-02-20 10:00:00', 1, '4-seater', 'Completed', 250.00, NULL, 'Cash', '2026-02-21 17:18:19'),
(1002, 999, NULL, NULL, 'District 7', 'Vung Tau', '2026-02-21 08:00:00', 1, '4-seater', 'Completed', 800.00, NULL, 'Cash', '2026-02-21 17:18:19'),
(1003, 999, NULL, NULL, 'District 2', 'District 9', '2026-02-22 14:00:00', 1, '4-seater', 'Completed', 150.00, NULL, 'Cash', '2026-02-21 17:18:19'),
(2001, 1001, NULL, NULL, 'Tan Son Nhat Airport', 'District 1', '2026-02-01 10:00:00', 1, '4-seater', 'Completed', 250.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2002, 1002, NULL, NULL, 'District 7', 'Da Lat', '2026-02-02 08:00:00', 1, '4-seater', 'Completed', 1200.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2003, 1003, NULL, NULL, 'District 2', 'Vung Tau', '2026-02-05 09:00:00', 1, '4-seater', 'Completed', 800.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2004, 1004, NULL, NULL, 'District 1', 'Cu Chi Tunnels', '2026-02-08 07:30:00', 1, '4-seater', 'Completed', 450.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2005, 1005, NULL, NULL, 'District 3', 'Mui Ne', '2026-02-10 06:00:00', 1, '4-seater', 'Completed', 1500.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2006, 1006, NULL, NULL, 'District 4', 'District 9', '2026-02-12 14:00:00', 1, '4-seater', 'Completed', 150.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2007, 1007, NULL, NULL, 'Tan Binh', 'Nha Trang', '2026-02-15 05:00:00', 1, '4-seater', 'Completed', 2500.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2008, 1008, NULL, NULL, 'District 10', 'Can Tho', '2026-02-18 08:00:00', 1, '4-seater', 'Completed', 1100.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2009, 1009, NULL, NULL, 'Phu Nhuan', 'District 7', '2026-02-20 18:00:00', 1, '4-seater', 'Completed', 200.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2010, 1010, NULL, NULL, 'District 1', 'Tan Son Nhat Airport', '2026-02-21 22:00:00', 1, '4-seater', 'Completed', 250.00, NULL, 'Cash', '2026-02-21 17:24:11'),
(2011, 1012, NULL, NULL, 'thành phố hồ chí minh q7', 'thành phố hồ chí minh q1', '2026-02-28 00:34:00', 1, '4-seater', 'Completed', 85.00, NULL, 'Cash', '2026-02-22 17:34:26'),
(2012, 1016, NULL, NULL, 'thành phố hồ chí minh q7', 'thành phố hồ chí minh q1', '2026-02-24 13:31:00', 2, '4 Seats', 'Completed', 46.75, NULL, 'Cash', '2026-02-24 06:30:31'),
(2013, 1016, NULL, NULL, 'Tan Binh street', 'thành phố hồ chí minh q1', '2026-02-06 13:39:00', 2, '4 Seats', 'Completed', 46.75, NULL, 'Cash', '2026-02-24 06:39:52');

-- --------------------------------------------------------

--
-- Table structure for table `consignments`
--

CREATE TABLE `consignments` (
  `ConsignmentID` int(11) NOT NULL,
  `CustomerID` int(11) DEFAULT NULL,
  `FullName` varchar(100) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Brand` varchar(50) NOT NULL,
  `Model` varchar(100) NOT NULL,
  `Year` int(11) NOT NULL,
  `ODO` varchar(50) NOT NULL,
  `City` varchar(50) NOT NULL,
  `District` varchar(50) NOT NULL,
  `Availability` varchar(100) NOT NULL,
  `Referral` varchar(100) DEFAULT NULL,
  `Status` varchar(20) DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consignments`
--

INSERT INTO `consignments` (`ConsignmentID`, `CustomerID`, `FullName`, `Phone`, `Brand`, `Model`, `Year`, `ODO`, `City`, `District`, `Availability`, `Referral`, `Status`, `CreatedAt`) VALUES
(1, 1016, 'duong', '0917184402', 'Mazda', 'mazda 3', 2024, '0 - 10,000 km', 'Ho Chi Minh City', 'q7', 'Partially available', '', 'Pending', '2026-02-24 07:44:01'),
(2, NULL, 'mun', '0917184402', 'Other', 'mazda 3', 2024, '0 - 10,000 km', 'Ho Chi Minh City', '7', 'Mostly available', 'ujhgcnc', 'Pending', '2026-02-24 09:48:38');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `DriverID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `LicenseNumber` varchar(50) NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Status` enum('Active','Inactive','OnTrip') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL,
  `Rating` tinyint(4) DEFAULT NULL CHECK (`Rating` between 1 and 5),
  `Comment` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `BookingID`, `Rating`, `Comment`, `CreatedAt`) VALUES
(10, 1001, 5, 'Beautiful, clean, and very new car. Reasonable price, quick procedures. I am very satisfied!', '2026-02-21 17:18:19'),
(11, 1002, 5, 'Excellent customer support. The driver delivered the car on time and was very friendly. 5 stars!', '2026-02-21 17:18:19'),
(12, 1003, 5, 'Flexible booking time without many strict constraints. Very suitable for weekend family road trips.', '2026-02-21 17:18:19'),
(13, 2001, 5, 'The car was in perfect condition. The pickup process at the airport was seamless. Highly recommend HireMyCar!', '2026-02-21 17:24:11'),
(14, 2002, 5, 'Great value for money. The SUV was spacious enough for our family trip to Da Lat. Will definitely book again.', '2026-02-21 17:24:11'),
(15, 2003, 5, 'Five-star service! The customer support team was very responsive when I needed to extend my rental for another day.', '2026-02-21 17:24:11'),
(16, 2004, 5, 'Smooth driving experience and very clean interior. The Bluetooth connection worked perfectly for our road trip playlist.', '2026-02-21 17:24:11'),
(17, 2005, 5, 'No hidden fees, very transparent pricing. The handover staff was polite and explained all the features clearly.', '2026-02-21 17:24:11'),
(18, 2006, 5, 'I rented a sedan for a business trip. It was exactly as described. Reliable and professional service.', '2026-02-21 17:24:11'),
(19, 2007, 5, 'The best car rental platform I have used in Vietnam. The interface is intuitive, and getting the car took less than 10 minutes.', '2026-02-21 17:24:11'),
(20, 2008, 5, 'Extremely fuel-efficient car. We drove all weekend and barely spent anything on gas. Return process was a breeze.', '2026-02-21 17:24:11'),
(21, 2009, 5, 'Absolutely loved the convenience. The car was brought directly to my hotel. Top-notch customer service!', '2026-02-21 17:24:11'),
(22, 2010, 5, 'Everything was perfectly organized. The vehicle felt brand new and smelled great. Thank you for a wonderful trip.', '2026-02-21 17:24:11'),
(23, 2012, 1, '/.;,mnbvcx', '2026-02-24 06:30:40');

-- --------------------------------------------------------

--
-- Table structure for table `specialoffers`
--

CREATE TABLE `specialoffers` (
  `OfferID` int(11) NOT NULL,
  `OfferCode` varchar(20) NOT NULL,
  `DiscountPercent` decimal(5,2) DEFAULT NULL,
  `ValidUntil` date DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userdocuments`
--

CREATE TABLE `userdocuments` (
  `DocumentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `DocumentType` enum('ID_Card','Driving_License','Passport') NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `Status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `UploadedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `PhoneNumber` varchar(20) NOT NULL,
  `Address` text DEFAULT NULL,
  `Role` enum('Customer','CallStaff','Admin') DEFAULT 'Customer',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `CitizenID` varchar(20) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `Email`, `PasswordHash`, `PhoneNumber`, `Address`, `Role`, `CreatedAt`, `CitizenID`, `Avatar`) VALUES
(1, 'Super Administrator', 'admin@system.com', 'e10adc3949ba59abbe56e057f20f883e', '0909000000', 'Office HQ', 'Admin', '2026-02-18 05:09:12', NULL, NULL),
(999, 'System Tester', 'tester@hiremycar.com', '123456', '0900000000', NULL, 'Customer', '2026-02-21 17:18:19', NULL, NULL),
(1001, 'Alex Johnson', 'alex@hiremycar.com', '123456', '0901000001', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1002, 'Minh Pham', 'minh@hiremycar.com', '123456', '0901000002', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1003, 'Sarah Tran', 'sarah@hiremycar.com', '123456', '0901000003', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1004, 'David Lee', 'david@hiremycar.com', '123456', '0901000004', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1005, 'Elena Nguyen', 'elena@hiremycar.com', '123456', '0901000005', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1006, 'Michael Davis', 'michael@hiremycar.com', '123456', '0901000006', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1007, 'Lan Anh', 'lananh@hiremycar.com', '123456', '0901000007', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1008, 'Chris Wilson', 'chris@hiremycar.com', '123456', '0901000008', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1009, 'Tuan Vu', 'tuan@hiremycar.com', '123456', '0901000009', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1010, 'Emma Smith', 'emma@hiremycar.com', '123456', '0901000010', NULL, 'Customer', '2026-02-21 17:24:11', NULL, NULL),
(1011, 'System Admin', 'admin@hiremycar.com', '123456', '0999999999', NULL, 'Admin', '2026-02-21 18:03:05', NULL, NULL),
(1012, 'John Customer', 'customer@hiremycar.com', '123456', '0888888888', NULL, 'Customer', '2026-02-21 18:03:05', NULL, NULL),
(1013, 'slea', 'slea@hiremycar.com', '123456', '0917184402', NULL, 'Customer', '2026-02-22 15:56:00', NULL, NULL),
(1014, 'bow', 'bow@hiremycar.com', '123', '123456', NULL, 'Customer', '2026-02-22 15:56:46', NULL, NULL),
(1015, 'bowbow', 'bowbow@hiremycar.com', '123', '123444', NULL, 'Customer', '2026-02-22 15:59:34', NULL, NULL),
(1016, 'duong', 'duong@hiremycar.com', '123', '0938441830', NULL, 'Customer', '2026-02-24 03:46:37', '091838440231', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `VehicleID` int(11) NOT NULL,
  `RegNumber` varchar(20) NOT NULL,
  `MakeModel` varchar(100) DEFAULT NULL,
  `Color` varchar(50) DEFAULT NULL,
  `Capacity` int(11) DEFAULT 4,
  `IsAvailable` tinyint(1) DEFAULT 1,
  `CurrentLocation` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `CustomerID` (`CustomerID`),
  ADD KEY `fk_booking_vehicle` (`VehicleID`),
  ADD KEY `fk_booking_driver` (`DriverID`),
  ADD KEY `fk_booking_offer` (`OfferID`);

--
-- Indexes for table `consignments`
--
ALTER TABLE `consignments`
  ADD PRIMARY KEY (`ConsignmentID`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`DriverID`),
  ADD UNIQUE KEY `LicenseNumber` (`LicenseNumber`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD UNIQUE KEY `BookingID` (`BookingID`);

--
-- Indexes for table `specialoffers`
--
ALTER TABLE `specialoffers`
  ADD PRIMARY KEY (`OfferID`),
  ADD UNIQUE KEY `OfferCode` (`OfferCode`);

--
-- Indexes for table `userdocuments`
--
ALTER TABLE `userdocuments`
  ADD PRIMARY KEY (`DocumentID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`VehicleID`),
  ADD UNIQUE KEY `RegNumber` (`RegNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2014;

--
-- AUTO_INCREMENT for table `consignments`
--
ALTER TABLE `consignments`
  MODIFY `ConsignmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `DriverID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `specialoffers`
--
ALTER TABLE `specialoffers`
  MODIFY `OfferID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userdocuments`
--
ALTER TABLE `userdocuments`
  MODIFY `DocumentID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1017;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `VehicleID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_driver` FOREIGN KEY (`DriverID`) REFERENCES `drivers` (`DriverID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_booking_offer` FOREIGN KEY (`OfferID`) REFERENCES `specialoffers` (`OfferID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_booking_vehicle` FOREIGN KEY (`VehicleID`) REFERENCES `vehicles` (`VehicleID`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`) ON DELETE CASCADE;

--
-- Constraints for table `userdocuments`
--
ALTER TABLE `userdocuments`
  ADD CONSTRAINT `userdocuments_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
