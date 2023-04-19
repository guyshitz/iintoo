-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: אפריל 19, 2023 בזמן 06:50 AM
-- גרסת שרת: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iintoo`
--

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `io_products`
--

CREATE TABLE `io_products` (
  `ID` int(11) NOT NULL,
  `product_title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_price` decimal(15,2) NOT NULL,
  `sale_price` decimal(15,2) DEFAULT NULL,
  `on_sale` bit(1) NOT NULL,
  `theme_img_file` varchar(64) DEFAULT NULL,
  `theme_img_ext` varchar(20) DEFAULT NULL,
  `theme_img_mimetype` varchar(20) DEFAULT NULL,
  `creation_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- מבנה טבלה עבור טבלה `io_product_features`
--

CREATE TABLE `io_product_features` (
  `feature_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `feature_name` text DEFAULT NULL,
  `feature_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- אינדקסים לטבלה `io_products`
--
ALTER TABLE `io_products`
  ADD PRIMARY KEY (`ID`);

--
-- אינדקסים לטבלה `io_product_features`
--
ALTER TABLE `io_product_features`
  ADD PRIMARY KEY (`feature_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `io_products`
--
ALTER TABLE `io_products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `io_product_features`
--
ALTER TABLE `io_product_features`
  MODIFY `feature_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- הגבלות לטבלאות שהוצאו
--

--
-- הגבלות לטבלה `io_product_features`
--
ALTER TABLE `io_product_features`
  ADD CONSTRAINT `io_product_features_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `io_products` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
