-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 04, 2012 at 05:23 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nutcracker`
--

-- --------------------------------------------------------
-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1.3
-- http://www.phpmyadmin.net
--
-- Host: 209.240.131.239

-- Generation Time: Sep 12, 2012 at 09:05 PM
-- Server version: 5.1.63
-- PHP Version: 5.2.4-2ubuntu5.25

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `nutcracker`
--

-- --------------------------------------------------------

-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `song_id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `frame_delay` int(11) NOT NULL,
  `model_name` varchar(16) NOT NULL,
  PRIMARY KEY (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_dtl`
--

CREATE TABLE IF NOT EXISTS `project_dtl` (
  `project_dtl_id` int(11) NOT NULL AUTO_INCREMENT,
  `phrase_name` varchar(100) NOT NULL,
  `start_secs` float(12,6) NOT NULL,
  `end_secs` float(12,6) NOT NULL,
  `effect_name` varchar(25) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`project_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_model`
--

CREATE TABLE IF NOT EXISTS `project_model` (
  `project_model_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) unsigned NOT NULL,
  `model_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`project_id`,`model_id`),
  UNIQUE KEY `project_model_id` (`project_model_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



--
-- Table structure for table `song`
--

CREATE TABLE IF NOT EXISTS `song` (
  `song_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `active_set` varchar(1) DEFAULT 'N',
  `song_name` varchar(256) DEFAULT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `song_url` varchar(256) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `audacity_aup` varchar(256) DEFAULT NULL,
  `music_mo_file` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`song_id`),
  KEY `user_song` (`song_name`,`artist`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=387 ;

-- --------------------------------------------------------

--
-- Table structure for table `song_dtl`
--

CREATE TABLE IF NOT EXISTS `song_dtl` (
  `song_dtl_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `song_id` int(11) NOT NULL,
  `phrase_name` varchar(100) NOT NULL,
  `start_secs` float(12,6) NOT NULL,
  `end_secs` float(12,6) NOT NULL,
  `sequence` int(6) NOT NULL DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`song_dtl_id`),
  KEY `song_id` (`song_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=88 ;



INSERT INTO `song_dtl` (`song_dtl_id`, `song_id`, `phrase_name`, `start_secs`, `end_secs`, `sequence`, `date_created`) VALUES
(1, 1, 'phrase21', 191.280533, 200.690002, 0, '2012-08-14 17:42:11'),
(2, 1, 'phrase20', 187.295120, 191.280533, 0, '2012-08-14 17:42:11'),
(3, 1, 'phrase19', 183.445786, 187.295120, 0, '2012-08-14 17:42:11'),
(4, 1, 'phrase18', 175.708252, 183.445786, 0, '2012-08-14 17:42:11'),
(5, 1, 'phrase17', 171.820038, 175.708252, 0, '2012-08-14 17:42:11'),
(6, 1, 'phrase16', 168.126236, 171.820038, 0, '2012-08-14 17:42:11'),
(7, 1, 'phrase15', 156.364395, 168.126236, 0, '2012-08-14 17:42:11'),
(8, 1, 'phrase14', 148.704620, 156.364395, 0, '2012-08-14 17:42:11'),
(9, 1, 'phrase13', 140.869873, 148.704620, 0, '2012-08-14 17:42:11'),
(10, 1, 'phrase12', 125.433685, 140.869873, 0, '2012-08-14 17:42:11'),
(11, 1, 'phrase11', 117.773911, 125.433685, 0, '2012-08-14 17:42:11'),
(12, 1, 'phrase10', 94.639053, 117.773911, 0, '2012-08-14 17:42:11'),
(13, 1, 'phrase9', 75.392410, 94.639053, 0, '2012-08-14 17:42:11'),
(14, 1, 'phrase8', 63.650013, 75.392410, 0, '2012-08-14 17:42:11'),
(15, 1, 'phrase7', 55.990234, 63.650013, 0, '2012-08-14 17:42:11'),
(16, 1, 'phrase6', 48.213814, 55.990234, 0, '2012-08-14 17:42:11'),
(17, 1, 'phrase5', 40.515156, 48.213814, 0, '2012-08-14 17:42:11'),
(18, 1, 'phrase4', 32.699852, 40.515156, 0, '2012-08-14 17:42:11'),
(19, 1, 'phrase3', 16.952599, 32.699852, 0, '2012-08-14 17:42:11'),
(20, 1, 'phrase2', 6.648841, 16.952599, 0, '2012-08-14 17:42:11'),
(21, 1, 'phrase1', 0.272175, 6.648841, 0, '2012-08-14 17:42:11'),
(22, 2, 'phrase30', 178.189926, 183.040314, 0, '2012-08-14 18:01:57'),
(23, 2, 'phrase29', 171.746399, 178.224762, 0, '2012-08-14 18:01:57'),
(24, 2, 'phrase28', 165.268021, 171.758011, 0, '2012-08-14 18:01:57'),
(25, 2, 'phrase27', 149.095322, 165.291245, 0, '2012-08-14 18:01:57'),
(26, 2, 'phrase26', 142.613113, 149.130157, 0, '2012-08-14 18:01:57'),
(27, 2, 'phrase25', 137.775604, 142.613113, 0, '2012-08-14 18:01:57'),
(28, 2, 'phrase24', 136.161819, 137.787216, 0, '2012-08-14 18:01:57'),
(29, 2, 'phrase21', 105.412460, 124.826813, 0, '2012-08-14 18:01:57'),
(30, 2, 'phrase22', 124.826813, 131.290115, 0, '2012-08-14 18:01:57'),
(31, 2, 'phrase23', 131.290115, 136.142487, 0, '2012-08-14 18:01:57'),
(32, 2, 'phrase20', 98.949158, 105.412460, 0, '2012-08-14 18:01:57'),
(33, 2, 'phrase19', 87.621262, 98.949158, 0, '2012-08-14 18:01:57'),
(34, 2, 'phrase18', 82.773788, 87.621262, 0, '2012-08-14 18:01:57'),
(35, 2, 'phrase17', 81.157959, 82.773788, 0, '2012-08-14 18:01:57'),
(36, 2, 'phrase16', 76.300774, 81.157959, 0, '2012-08-14 18:01:57'),
(37, 2, 'phrase15', 69.837402, 76.323990, 0, '2012-08-14 18:01:57'),
(38, 2, 'phrase14', 56.912109, 69.837402, 0, '2012-08-14 18:01:57'),
(39, 2, 'phrase13', 50.445351, 56.923717, 0, '2012-08-14 18:01:57'),
(40, 2, 'phrase12', 42.353195, 50.445351, 0, '2012-08-14 18:01:57'),
(41, 2, 'phrase11', 37.488617, 42.341587, 0, '2012-08-14 18:01:57'),
(42, 2, 'phrase10', 35.861061, 37.523445, 0, '2012-08-14 18:01:57'),
(43, 2, 'phrase9', 31.013586, 35.861061, 0, '2012-08-14 18:01:57'),
(44, 2, 'phrase8', 24.566711, 31.013586, 0, '2012-08-14 18:01:57'),
(45, 2, 'phrase7', 19.702131, 24.566711, 0, '2012-08-14 18:01:57'),
(46, 2, 'phrase6', 18.076735, 19.725351, 0, '2012-08-14 18:01:57'),
(47, 2, 'phrase5', 13.212154, 18.088345, 0, '2012-08-14 18:01:57'),
(48, 2, 'phrase4', 11.606566, 13.229726, 0, '2012-08-14 18:01:57'),
(49, 2, 'phrase3', 6.749311, 11.606566, 0, '2012-08-14 18:01:57'),
(50, 2, 'phrase2', 5.133486, 6.751756, 0, '2012-08-14 18:01:57'),
(51, 2, 'phrase1', 0.261563, 5.133486, 0, '2012-08-14 18:01:57'),
(52, 3, 'phrase36', 277.350739, 278.894867, 0, '2012-08-15 01:54:38'),
(53, 3, 'phrase35', 267.551941, 277.350739, 0, '2012-08-15 01:54:38'),
(54, 3, 'phrase34', 260.179596, 267.551941, 0, '2012-08-15 01:54:38'),
(55, 3, 'phrase33', 253.097504, 260.179596, 0, '2012-08-15 01:54:38'),
(56, 3, 'phrase32', 245.701950, 253.097504, 0, '2012-08-15 01:54:38'),
(57, 3, 'phrase31', 238.805618, 245.701950, 0, '2012-08-15 01:54:38'),
(58, 3, 'phrase30', 231.665482, 238.805618, 0, '2012-08-15 01:54:38'),
(59, 3, 'phrase29', 224.455688, 231.665482, 0, '2012-08-15 01:54:38'),
(60, 3, 'phrase28', 217.292343, 224.455688, 0, '2012-08-15 01:54:38'),
(61, 3, 'phrase27', 192.284439, 217.292343, 0, '2012-08-15 01:54:38'),
(62, 3, 'phrase26', 185.388123, 192.284439, 0, '2012-08-15 01:54:38'),
(63, 3, 'phrase25', 178.480179, 185.388123, 0, '2012-08-15 01:54:38'),
(64, 3, 'phrase24', 169.877182, 178.480179, 0, '2012-08-15 01:54:38'),
(65, 3, 'phrase23', 161.309021, 169.877182, 0, '2012-08-15 01:54:38'),
(66, 3, 'phrase22', 150.987762, 161.309021, 0, '2012-08-15 01:54:38'),
(67, 3, 'phrase21', 140.643265, 150.987762, 0, '2012-08-15 01:54:38'),
(68, 3, 'phrase20', 126.804169, 140.643265, 0, '2012-08-15 01:54:38'),
(69, 3, 'phrase19', 119.989113, 126.780952, 0, '2012-08-15 01:54:38'),
(70, 3, 'phrase18', 113.127617, 119.989113, 0, '2012-08-15 01:54:38'),
(71, 3, 'phrase17', 103.038551, 113.127617, 0, '2012-08-15 01:54:38'),
(72, 3, 'phrase16', 98.847343, 106.184853, 0, '2012-08-15 01:54:38'),
(73, 3, 'phrase15', 84.787666, 98.847343, 0, '2012-08-15 01:54:38'),
(74, 3, 'phrase14', 81.439400, 84.787666, 0, '2012-08-15 01:54:38'),
(75, 3, 'phrase13', 74.317825, 81.439400, 0, '2012-08-15 01:54:38'),
(76, 3, 'phrase12', 58.108585, 74.317825, 0, '2012-08-15 01:54:38'),
(77, 3, 'phrase11', 52.122967, 58.108585, 0, '2012-08-15 01:54:38'),
(78, 3, 'phrase10', 48.540333, 52.122967, 0, '2012-08-15 01:54:38'),
(79, 3, 'phrase9', 47.448063, 48.627712, 0, '2012-08-15 01:54:38'),
(80, 3, 'phrase8', 43.603287, 47.448063, 0, '2012-08-15 01:54:38'),
(81, 3, 'phrase7', 42.467327, 43.603287, 0, '2012-08-15 01:54:38'),
(82, 3, 'phrase6', 29.753344, 42.467327, 0, '2012-08-15 01:54:38'),
(83, 3, 'phrase5', 19.660801, 29.753344, 0, '2012-08-15 01:54:38'),
(84, 3, 'phrase4', 15.990784, 19.617109, 0, '2012-08-15 01:54:38'),
(85, 3, 'phrase3', 10.791595, 15.947093, 0, '2012-08-15 01:54:38'),
(86, 3, 'phrase2', 5.767168, 10.791595, 0, '2012-08-15 01:54:38'),
(87, 3, 'phrase1', 0.000000, 5.767168, 0, '2012-08-15 01:54:38');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


--
-- Dumping data for table `song`
--

INSERT INTO `song` (`song_id`, `active_set`, `song_name`, `artist`, `song_url`, `last_updated`, `audacity_aup`, `music_mo_file`) VALUES
(1, 'N', 'Chistmas Sarajevo', 'TSO ', 'http://www.amazon.com/Christmas-Sarajevo-12-24-Instrumental/dp/B001237DT0/ref=pd_sim_dmusic_t_2', '2012-09-03 08:14:07', NULL, 'Christmas_Sarajevo.mo'),
(2, 'N', 'Wizards of Winter', 'TSO', 'http://www.amazon.com/gp/product/B00123B3S2/ref=dm_mu_dp_trk4', '2012-09-03 08:14:07', 'C:\\Users\\sean\\Music\\Amazon MP3\\Trans-Siberian Orchestra\\The Lost Christmas Eve\\04 - Wizards In Winter (Instrumental).aup', 'Wizards_In_Winter.mo'),
(3, 'N', 'Mad Russian Christmas', 'TSO ', 'http://www.amazon.com/gp/product/B00123D0CE/ref=dm_mu_dp_trk6', '2012-09-03 08:14:07', NULL, 'Mad_Russian_Christmas.mo');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
