-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. Mai 2013 um 10:26
-- Server Version: 5.1.41
-- PHP-Version: 5.3.2-1ubuntu4.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dbjr`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups`
--

CREATE TABLE IF NOT EXISTS `fowups` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up ID',
  `docorg` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'internal order of document',
  `embed` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'embedding for multimedia',
  `expl` text COLLATE utf8_unicode_ci COMMENT 'Erläuterung',
  `typ` enum('g','a','r','e') COLLATE utf8_unicode_ci NOT NULL COMMENT 'general, action, rejected, end',
  `ffid` smallint(5) NOT NULL COMMENT 'reference to Follow-up File ID',
  `hlvl` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'hierarchy level in document, 1 is standard text,0 is footnoote, >1 are headings',
  `lkyea` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of likes',
  `lknay` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of dislikes',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=117 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups_rid`
--

CREATE TABLE IF NOT EXISTS `fowups_rid` (
  `fid_ref` int(10) NOT NULL,
  `fid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: fowups.fid',
  `tid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: inpt.tid',
  `ffid` int(10) NOT NULL DEFAULT '0' COMMENT 'fk: fowup_fls.ffid',
  PRIMARY KEY (`fid_ref`,`fid`,`tid`,`ffid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowups_supports`
--

CREATE TABLE IF NOT EXISTS `fowups_supports` (
  `fid` int(10) NOT NULL,
  `tmphash` char(32) NOT NULL,
  PRIMARY KEY (`fid`,`tmphash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fowup_fls`
--

CREATE TABLE IF NOT EXISTS `fowup_fls` (
  `ffid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up File ID',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'ref to KonsultationID',
  `titl` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Title of follow-up document',
  `who` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Who gave the follow-up',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was it released',
  `show_no_day` enum('n','y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'cell ''when'' shown only as year and month',
  `ref_doc` varchar(160) COLLATE utf8_unicode_ci NOT NULL COMMENT 'reference to downloadable document',
  `ref_view` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document',
  `gfx_who` varchar(160) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Graphic of who',
  PRIMARY KEY (`ffid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=174 COMMENT='Follow-up files' AUTO_INCREMENT=33 ;
