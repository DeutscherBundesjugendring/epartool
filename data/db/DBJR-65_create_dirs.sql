-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 09. Sep 2013 um 14:55
-- Server Version: 5.5.25
-- PHP-Version: 5.3.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dbjr_tool`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `dirs`
--

DROP TABLE IF EXISTS `dirs`;
CREATE TABLE `dirs` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(12) NOT NULL,
  `dir_name` varchar(120) COLLATE utf8_bin NOT NULL,
  `left` int(12) unsigned NOT NULL,
  `right` int(12) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`left`),
  KEY `rgt` (`right`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='DB-Model nested sets' AUTO_INCREMENT=32 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
