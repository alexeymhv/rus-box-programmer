-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 12, 2015 at 10:44 PM
-- Server version: 5.6.19-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gramatnica`
--

-- --------------------------------------------------------

--
-- Table structure for table `Pasutijums`
--

CREATE TABLE IF NOT EXISTS `Pasutijums` (
  `ID_Pasutijums` int(11) NOT NULL AUTO_INCREMENT,
  `ID_Lietotajs` int(11) DEFAULT NULL,
  `Datums` date DEFAULT NULL,
  PRIMARY KEY (`ID_Pasutijums`),
  KEY `ID_Lietotajs` (`ID_Lietotajs`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `Pasutijums`
--

INSERT INTO `Pasutijums` (`ID_Pasutijums`, `ID_Lietotajs`, `Datums`) VALUES
(1, 1, '2015-10-31'),
(2, 2, '2015-10-21'),
(3, 6, '2015-10-06'),
(4, 5, '2015-10-22'),
(5, 4, '2015-10-01'),
(6, 7, '0000-00-00'),
(7, 8, '2015-12-10'),
(8, 9, '2015-12-10'),
(9, 10, '2015-12-11'),
(10, 11, '2015-12-12'),
(11, 11, '2015-12-12'),
(12, 11, '2015-12-12'),
(13, 11, '2015-12-12'),
(14, 11, '2015-12-12'),
(15, 11, '2015-12-12'),
(16, 11, '2015-12-12');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Pasutijums`
--
ALTER TABLE `Pasutijums`
  ADD CONSTRAINT `Pasutijums_ibfk_1` FOREIGN KEY (`ID_Lietotajs`) REFERENCES `Lietotajs` (`ID_Lietotajs`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
