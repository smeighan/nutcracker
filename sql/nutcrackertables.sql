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

--
-- Table structure for table `audit_log`
--

CREATE TABLE IF NOT EXISTS `audit_log` (
  `username` varchar(25) NOT NULL,
  `date_field` date NOT NULL DEFAULT '2000-01-01',
  `time_field` time NOT NULL DEFAULT '00:00:00',
  `action` varchar(15) DEFAULT NULL,
  `object_name` varchar(50) DEFAULT NULL,
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `effects_dtl`
--

CREATE TABLE IF NOT EXISTS `effects_dtl` (
  `active` varchar(4) NOT NULL,
  `effect_class` varchar(25) NOT NULL,
  `param_name` varchar(32) NOT NULL,
  `param_prompt` varchar(60) NOT NULL,
  `param_desc` varchar(1024) DEFAULT NULL,
  `param_range` varchar(80) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `last_upd` datetime DEFAULT NULL,
  `sequence` int(11) DEFAULT NULL,
  UNIQUE KEY `idx_class_name` (`effect_class`,`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `effects_hdr`
--

CREATE TABLE IF NOT EXISTS `effects_hdr` (
  `active` varchar(4) DEFAULT NULL,
  `effect_class` varchar(25) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_upd` datetime DEFAULT NULL,
  `php_program` varchar(25) DEFAULT NULL,
  UNIQUE KEY `idx_effect_class` (`effect_class`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `effects_user_dtl`
--

CREATE TABLE IF NOT EXISTS `effects_user_dtl` (
  `username` varchar(25) NOT NULL,
  `effect_name` varchar(25) NOT NULL,
  `param_name` varchar(32) NOT NULL,
  `param_value` varchar(4000) NOT NULL,
  `segment` int(6) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `last_upd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `idx_login_name` (`username`,`effect_name`,`param_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `effects_user_hdr`
--

CREATE TABLE IF NOT EXISTS `effects_user_hdr` (
  `effect_class` varchar(25) NOT NULL,
  `username` varchar(25) NOT NULL,
  `effect_name` varchar(25) NOT NULL,
  `effect_desc` varchar(1024) NOT NULL,
  `music_object_id` int(11) DEFAULT NULL,
  `start_secs` float(12,6) DEFAULT NULL,
  `end_secs` float(12,6) DEFAULT NULL,
  `phrase_name` varchar(100) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `last_upd` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `username_effect_name` (`username`,`effect_name`,`effect_class`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE IF NOT EXISTS `gallery` (
  `fullpath` varchar(100) NOT NULL,
  `effect_class` varchar(25) DEFAULT NULL,
  `username` varchar(25) DEFAULT NULL,
  `effect_name` varchar(25) DEFAULT NULL,
  `linenumber` int(6) DEFAULT NULL,
  `member_id` int(6) NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fullpath`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Gallery of thumbnail gifs';

-- --------------------------------------------------------

--
-- Table structure for table `library_dtl`
--

CREATE TABLE IF NOT EXISTS `library_dtl` (
  `library_id` int(12) NOT NULL,
  `string` int(12) NOT NULL,
  `pixel` int(12) NOT NULL,
  `frame` int(12) NOT NULL,
  `rgb_val` int(12) NOT NULL,
  KEY `library_id` (`library_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `library_hdr`
--

CREATE TABLE IF NOT EXISTS `library_hdr` (
  `username` varchar(25) NOT NULL,
  `object_name` varchar(16) NOT NULL,
  `effect_name` varchar(25) NOT NULL,
  `library_id` int(12) NOT NULL AUTO_INCREMENT,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`library_id`),
  KEY `username_object_name` (`username`,`object_name`),
  KEY `username_effect_name` (`username`,`effect_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `username` varchar(25) NOT NULL,
  `passwd` varchar(80) NOT NULL,
  `enable_projects` char(1) DEFAULT 'N',
  `LSP1_8` char(1) DEFAULT 'N',
  `LSP2_0` char(1) DEFAULT 'N',
  `LSP3_0` char(1) DEFAULT 'N',
  `LOR_S2` char(1) DEFAULT 'N',
  `LOR_S3` char(1) DEFAULT 'N',
  `VIXEN211` char(1) DEFAULT 'N',
  `VIXEN25` char(1) DEFAULT 'N',
  `VIXEN3` char(1) DEFAULT 'N',
  `HLS` char(1) DEFAULT 'N',
  `OTHER` char(1) DEFAULT 'N',
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=564 ;

-- --------------------------------------------------------

--
-- Table structure for table `models`
--

CREATE TABLE IF NOT EXISTS `models` (
  `username` varchar(25) NOT NULL,
  `object_name` varchar(16) NOT NULL,
  `object_desc` varchar(80) DEFAULT NULL,
  `model_type` varchar(16) DEFAULT NULL,
  `string_type` varchar(16) DEFAULT NULL,
  `pixel_count` int(11) DEFAULT NULL,
  `folds` int(11) NOT NULL,
  `start_bottom` char(1) DEFAULT NULL,
  `pixel_first` int(11) DEFAULT NULL,
  `pixel_last` int(11) DEFAULT NULL,
  `pixel_length` decimal(13,2) unsigned DEFAULT NULL,
  `pixel_spacing` float(11,4) DEFAULT NULL,
  `unit_of_measure` varchar(12) DEFAULT NULL,
  `total_strings` int(11) DEFAULT NULL,
  `total_pixels` int(12) DEFAULT NULL,
  `number_segments` varchar(6) DEFAULT '0',
  `gif_model` varchar(25) DEFAULT 'single',
  `direction` varchar(10) DEFAULT NULL,
  `orientation` int(11) DEFAULT NULL,
  `topography` varchar(16) DEFAULT NULL,
  `h1` decimal(13,2) unsigned DEFAULT NULL,
  `h2` decimal(13,2) unsigned DEFAULT NULL,
  `d1` decimal(13,2) unsigned DEFAULT NULL,
  `d2` decimal(13,2) unsigned DEFAULT NULL,
  `d3` decimal(13,2) unsigned DEFAULT NULL,
  `d4` decimal(13,2) unsigned DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `username_object_name` (`username`,`object_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `models_strands`
--

CREATE TABLE IF NOT EXISTS `models_strands` (
  `username` varchar(25) NOT NULL,
  `object_name` varchar(16) NOT NULL,
  `string` int(6) NOT NULL DEFAULT '0',
  `pixels` int(6) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`,`object_name`,`string`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `models_strand_segments`
--

CREATE TABLE IF NOT EXISTS `models_strand_segments` (
  `username` varchar(25) NOT NULL,
  `object_name` varchar(16) NOT NULL,
  `segment` int(6) NOT NULL,
  `starting_pixel` int(6) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`,`object_name`,`segment`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `music_object_dtl`
--

CREATE TABLE IF NOT EXISTS `music_object_dtl` (
  `music_object_id` int(11) NOT NULL,
  `phrase_name` varchar(100) NOT NULL,
  `start_secs` float(12,6) NOT NULL,
  `end_secs` float(12,6) NOT NULL,
  `effect_name` varchar(25) DEFAULT NULL,
  `sequence` int(6) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `music_object_id` (`music_object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `music_object_hdr`
--

CREATE TABLE IF NOT EXISTS `music_object_hdr` (
  `username` varchar(25) NOT NULL,
  `music_object_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `active_set` varchar(1) DEFAULT 'N',
  `song_name` varchar(256) DEFAULT NULL,
  `artist` varchar(100) DEFAULT NULL,
  `desc3` varchar(100) DEFAULT NULL,
  `frame_delay` int(6) DEFAULT NULL,
  `target` varchar(100) DEFAULT NULL,
  `song_url` varchar(256) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `audacity_aup` varchar(256) DEFAULT NULL,
  `music_mo_file` varchar(256) DEFAULT NULL,
  `object_name` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`music_object_id`),
  KEY `user_song` (`username`,`song_name`,`artist`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=421 ;

-- --------------------------------------------------------

--
-- Table structure for table `music_object_votes`
--

CREATE TABLE IF NOT EXISTS `music_object_votes` (
  `username` varchar(25) NOT NULL,
  `music_object_id` int(11) NOT NULL,
  `desc` varchar(10) NOT NULL,
  `date_created` date NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `music_object_votes_count`
--

CREATE TABLE IF NOT EXISTS `music_object_votes_count` (
  `music_object_id` int(11) NOT NULL,
  `cnt` bigint(21) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
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
  
  `project_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  
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

-- --------------------------------------------------------

--
-- Table structure for table `snowstorm`
--

CREATE TABLE IF NOT EXISTS `snowstorm` (
  `state` int(6) NOT NULL,
  `strand` int(6) NOT NULL,
  `pixel` int(6) NOT NULL,
  `rgb` int(8) NOT NULL,
  `parent_rgb` int(8) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `counter` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`username`,`strand`,`pixel`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='used for snowtorm.php';

-- --------------------------------------------------------

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

