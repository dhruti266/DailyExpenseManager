-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2016 at 09:08 PM
-- Server version: 5.5.32
-- PHP Version: 5.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `f6team11_dem`
--
CREATE DATABASE IF NOT EXISTS `f6team11_dem` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `f6team11_dem`;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `categoryId` int(5) NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(15) NOT NULL,
  `userId` int(3) unsigned NOT NULL,
  UNIQUE KEY `categoryId` (`categoryId`),
  KEY `categoryId_2` (`categoryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=234 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`categoryId`, `categoryName`, `userId`) VALUES
(218, 'Extra', 11),
(216, 'Hydro', 10),
(215, 'Home Stuff', 11),
(214, 'School Fee', 10),
(213, 'Personal Care', 10),
(212, 'Medical', 10),
(211, 'Automobile', 10),
(222, 'Loan', 11),
(189, 'Internet', 10),
(190, 'Groceries', 10),
(221, 'Savings', 10),
(196, 'Trip', 10),
(225, 'Salary', 10),
(226, 'Tax Return', 10),
(227, 'OSAP', 10),
(230, 'Beauty Parler', 11),
(231, 'School Fee', 11),
(232, 'Apartment Rent', 11),
(233, 'Hydro', 11);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE IF NOT EXISTS `transaction` (
  `transId` int(5) NOT NULL AUTO_INCREMENT,
  `amount` float NOT NULL,
  `category` varchar(15) NOT NULL DEFAULT 'Rent',
  `date` date NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `paymentMode` varchar(15) NOT NULL DEFAULT 'Cash',
  `transType` varchar(10) NOT NULL DEFAULT 'Expense',
  `userId` int(3) unsigned NOT NULL,
  PRIMARY KEY (`transId`),
  UNIQUE KEY `transId` (`transId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transId`, `amount`, `category`, `date`, `description`, `paymentMode`, `transType`, `userId`) VALUES
(1, 234, 'Hydro', '2016-11-17', '', 'Cash', 'Income', 10),
(2, 5345, 'Extra', '2016-12-08', '', 'Credit', 'Expense', 10),
(3, 2342, 'Groceries', '2016-12-01', '', 'Credit', 'Expense', 10),
(4, 1234, 'Salary', '2016-12-01', '', 'Debit', 'Income', 10),
(5, 2344, 'Other Income', '2016-01-19', '', 'Credit', 'Income', 10),
(6, 700, 'Trip', '2016-01-12', '', 'Debit', 'Expense', 10),
(7, 12324, 'Extra', '2016-12-02', '', 'Cash', 'Income', 10),
(8, 4655, 'Loan', '2016-12-02', '', 'Credit', 'Income', 10),
(9, 3456, 'Medical', '2016-12-02', '', 'Cash', 'Income', 10),
(10, 564, 'OSAP', '2016-02-16', '', 'Cash', 'Income', 10),
(11, 3453, 'Medical', '2016-02-18', '', 'Credit', 'Expense', 10),
(12, 63, 'Internet', '2016-12-02', '', 'Debit', 'Expense', 10),
(13, 322, 'Home Stuff', '2016-02-25', '', 'Cash', 'Expense', 10),
(14, 3532, 'Tax Return', '2016-02-22', '', 'Cash', 'Income', 10),
(16, 123, 'Personal Care', '2016-12-03', '', 'Cash', 'Income', 10),
(17, 2321, 'Extra', '2016-12-03', '', 'Cash', 'Income', 11),
(18, 2344, 'Loan', '2016-12-03', '', 'Debit', 'Expense', 11),
(19, 23, 'Beauty Parler', '2016-01-04', '', 'Cash', 'Expense', 11),
(20, 1232, 'Extra', '2016-01-12', '', 'Debit', 'Income', 11);

-- --------------------------------------------------------

--
-- Table structure for table `user_information`
--

CREATE TABLE IF NOT EXISTS `user_information` (
  `userId` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) DEFAULT NULL,
  `username` varchar(10) NOT NULL,
  `pin` int(4) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userId` (`userId`,`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `user_information`
--

INSERT INTO `user_information` (`userId`, `firstName`, `lastName`, `username`, `pin`, `password`) VALUES
(10, 'Dhruti', 'Parekh', 'Dparekh', 4646, 'd2363b25bc82706b35144f9dcea353a9'),
(11, 'Rajvi Mukeshbhai', 'Lathia', 'rlathia', 7997, '8f958540651a2f7fccf8b267e0e16d86'),
(12, 'Kartavya', 'Lathia', 'klathia', 1999, 'ba1e677b952b42e373e31e08aa468196');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
