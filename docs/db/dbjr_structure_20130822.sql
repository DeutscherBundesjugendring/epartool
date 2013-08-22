-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: sql116.your-server.de
-- Generation Time: Aug 22, 2013 at 04:34 PM
-- Server version: 5.1.66-0+squeeze1
-- PHP Version: 5.3.3-7+squeeze16

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dbjrtool`
--

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE IF NOT EXISTS `articles` (
  `art_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Article ID',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'rel KID',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project',
  `desc` varchar(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Readable descr for admin',
  `hid` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=hide from public',
  `ref_nm` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `artcl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Article itself',
  `sidebar` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Content for sidebar',
  `parent_id` smallint(5) DEFAULT NULL COMMENT 'parent article',
  PRIMARY KEY (`art_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=261 CHECKSUM=1 AUTO_INCREMENT=166 ;

-- --------------------------------------------------------

--
-- Table structure for table `articles_refnm`
--

CREATE TABLE IF NOT EXISTS `articles_refnm` (
  `ref_nm` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `lng` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'language code',
  `desc` varchar(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'readable description',
  `type` enum('g','b','s','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 's' COMMENT 'general, basic, specific, mail',
  `scope` enum('none','info','voting','followup','static') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none' COMMENT 'none, info, voting, followup, static',
  UNIQUE KEY `ref_nm` (`ref_nm`,`lng`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='default list of article types';

-- --------------------------------------------------------

--
-- Table structure for table `cnslt`
--

CREATE TABLE IF NOT EXISTS `cnslt` (
  `kid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Consultation ID',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol',
  `inp_fr` datetime NOT NULL COMMENT 'Input possible from date on',
  `inp_to` datetime NOT NULL COMMENT 'Input possible till',
  `inp_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show input period',
  `spprt_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show support button',
  `spprt_fr` datetime NOT NULL COMMENT 'support button clickable from',
  `spprt_to` datetime NOT NULL COMMENT 'Supporting possible until',
  `spprt_ct` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Counter for accumulated supports',
  `disc_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show discussion button',
  `vot_fr` datetime NOT NULL COMMENT 'Voting possible from date on',
  `vot_to` datetime NOT NULL COMMENT 'Voting possible till',
  `vot_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show voting period',
  `vot_expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'info text for voting start',
  `vot_res_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show voting results',
  `summ_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show summary of voting',
  `follup_show` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show follow-up',
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'order in slider (the higher the more important)',
  `titl` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of consultation',
  `titl_short` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)',
  `titl_sub` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subtitle (optional)',
  `img_file` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'file name of title graphics',
  `img_expl` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics',
  `expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanatory text',
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `adm` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Administrated by [not used yet]',
  `public` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=visible to public',
  PRIMARY KEY (`kid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=217 CHECKSUM=1 COMMENT='Basic definitions of consultations' AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- Table structure for table `discuss`
--

CREATE TABLE IF NOT EXISTS `discuss` (
  `tid` int(10) unsigned NOT NULL,
  `uid` mediumint(8) unsigned NOT NULL,
  `tmphash` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`,`uid`,`tmphash`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED COMMENT='Zähler für Diskussionsbedarf-Button';

-- --------------------------------------------------------

--
-- Table structure for table `discussns`
--

CREATE TABLE IF NOT EXISTS `discussns` (
  `tid` int(10) unsigned NOT NULL COMMENT 'ThesenID',
  `uid` int(10) unsigned NOT NULL COMMENT 'UserID',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When written',
  `inpt` varchar(6000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Actual text',
  `type` enum('i','r','d','n','c','s') COLLATE utf8_unicode_ci NOT NULL COMMENT 'initial copy of tid, revision of tid, default, new tid, closed thread, secret',
  UNIQUE KEY `tid` (`tid`,`when`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Discussion threads';

-- --------------------------------------------------------

--
-- Table structure for table `edt_cnslt`
--

CREATE TABLE IF NOT EXISTS `edt_cnslt` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'ref to UserID of editor',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'ref to KID',
  `edt` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'right to edit',
  `own` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'is owner of this cnslt',
  PRIMARY KEY (`uid`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='defines rights of editors or admins related to consultations';

-- --------------------------------------------------------

--
-- Table structure for table `fowups`
--

CREATE TABLE IF NOT EXISTS `fowups` (
  `fid` int(10) unsigned NOT NULL COMMENT 'Follow-up ID',
  `docorg` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'internal order of document',
  `embed` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'embedding for multimedia',
  `expl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Erläuterung',
  `typ` enum('g','a','r','e') COLLATE utf8_unicode_ci NOT NULL COMMENT 'general, action, rejected, end',
  `ffid` smallint(5) unsigned zerofill NOT NULL COMMENT 'reference to Follow-up File ID',
  `hlvl` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'hierarchy level in document, 1 is standard text,0 is footnoote, >1 are headings',
  `lkyea` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of likes',
  `lknay` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of dislikes',
  UNIQUE KEY `fid` (`fid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fowups_rid`
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
-- Table structure for table `fowups_supports`
--

CREATE TABLE IF NOT EXISTS `fowups_supports` (
  `fid` int(10) NOT NULL,
  `tmphash` char(32) NOT NULL,
  PRIMARY KEY (`fid`,`tmphash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fowup_fls`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=174 COMMENT='Follow-up files' AUTO_INCREMENT=1202 ;

-- --------------------------------------------------------

--
-- Table structure for table `inpt`
--

CREATE TABLE IF NOT EXISTS `inpt` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Thesen ID',
  `qi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'QuestionID (new)',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'consultation ID',
  `thes` varchar(330) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'User reply',
  `expl` varchar(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Longer explanation',
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'from which User ID',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Input given when',
  `block` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'yes, no, unchecked',
  `user_conf` enum('u','c','r') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'unconfirmed, confirmed, rejected',
  `vot` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'Zum Voting zugelassen',
  `typ` enum('p','f','l','bp') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Problemanzeige, Forderung, Lösungsvorschlag, Best Practice',
  `spprts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'how many pressed support-haken',
  `votes` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Votes received',
  `pts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Points received',
  `rel_tid` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Related tids',
  `tg_nrs` varchar(55) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags',
  `notiz` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Notes for internal use',
  `confirm_key` varchar(64) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY (`tid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=109 CHECKSUM=1 COMMENT='User input to questions' AUTO_INCREMENT=4201 ;

-- --------------------------------------------------------

--
-- Table structure for table `inpt_tgs`
--

CREATE TABLE IF NOT EXISTS `inpt_tgs` (
  `tg_nr` int(11) NOT NULL COMMENT 'id of tag',
  `tid` int(11) NOT NULL COMMENT 'id of these (input)',
  PRIMARY KEY (`tg_nr`,`tid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AVG_ROW_LENGTH=9 ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `ml_def`
--

CREATE TABLE IF NOT EXISTS `ml_def` (
  `mid` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'MailID',
  `refnm` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Reference name, e.g. footer, header',
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'consultation-specific or general (=0)',
  `proj` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project(s)',
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language of mail',
  `subj` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Subject of e-mail (todo check maxlength in RFC)',
  `txt` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'TEXT mail',
  `html` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'HTML mail',
  `expl` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Explanation and possible variables',
  `head` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach header to mail',
  `foot` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach footer to mail',
  PRIMARY KEY (`mid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=240 CHECKSUM=1 COMMENT='Mail definitions / defaults' AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `ml_sent`
--

CREATE TABLE IF NOT EXISTS `ml_sent` (
  `when` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `rec` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Recipient of mail',
  `sender` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'sent by',
  `subj` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subject of mail sent',
  `proj` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Sent from which project',
  `ip` varchar(15) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT '' COMMENT 'IP address of sender',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=70 COMMENT='Mails sent by system' AUTO_INCREMENT=391 ;

-- --------------------------------------------------------

--
-- Table structure for table `proj`
--

CREATE TABLE IF NOT EXISTS `proj` (
  `proj` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Project abbrev',
  `titl_short` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Short title/name for project',
  `email` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Address which the tool uses for sending out messages (todo check maxlength by rfc)',
  `realnm` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Real name to be uses as sender alias for e-mail',
  `smtp_srv` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP server (not used yet)',
  `smtp_prt` tinyint(3) unsigned NOT NULL DEFAULT '25' COMMENT 'SMTP port (not used yet)',
  `smtp_usr` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP user (not used yet)',
  `smtp_pwd` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP password (not used yet)',
  `toolline` varchar(260) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Tool headline itself',
  `vot_q` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Question used for voting',
  PRIMARY KEY (`proj`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=20 COMMENT='All projects active in this installation';

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

CREATE TABLE IF NOT EXISTS `quests` (
  `qi` int(10) unsigned NOT NULL COMMENT 'QuestionID (new)',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'Consultation ID',
  `nr` char(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Number shown in ordered list',
  `q` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The question itself',
  `q_xpl` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanation for question',
  `ln` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `vot_q` varchar(220) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Introducing voting question',
  UNIQUE KEY `qi` (`qi`) USING BTREE,
  KEY `qi_2` (`qi`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=93 CHECKSUM=1 COMMENT='Questions for the consultations';

-- --------------------------------------------------------

--
-- Table structure for table `quests_choic`
--

CREATE TABLE IF NOT EXISTS `quests_choic` (
  `qi` int(10) unsigned NOT NULL COMMENT 'ref to QuestionID',
  `opt` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Option for reference',
  `desc` varchar(240) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Descriptive label',
  `ln` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de' COMMENT 'Language'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Options for multiple choice questions';

-- --------------------------------------------------------

--
-- Table structure for table `sessns`
--

CREATE TABLE IF NOT EXISTS `sessns` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'User ID',
  `cid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Confirmation code',
  `sess_strt` datetime NOT NULL COMMENT 'When session started',
  `sess_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When last activity took place',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(120) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of contact person',
  `name_grp` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of group',
  `source` set('d','g','m','p') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'm' COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanation of misc source',
  `grp_size` tinyint(3) unsigned NOT NULL COMMENT '1,10,30,80,150,over',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Bundesländer',
  `age_grp` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `find_out` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'woher kennt ihr uns',
  `group_type` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Art der Gruppe',
  `what_you_do` enum('school','education','work','selfemployed','volunteer','unemployed','notspecified') COLLATE utf8_unicode_ci NOT NULL,
  `kid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'KID, falls Eintragung abgeschlossen',
  `publ_us` enum('n','y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'publicize us a contributor',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `cmnt_ext` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Comment by user',
  PRIMARY KEY (`uid`,`sess_strt`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=184 CHECKSUM=1 COMMENT='Users in the system';

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE IF NOT EXISTS `supports` (
  `tid` int(10) unsigned NOT NULL COMMENT 'ref to TID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'uid if existing',
  `tmphash` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'hash from IP etc',
  PRIMARY KEY (`tid`,`uid`,`tmphash`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `tgs`
--

CREATE TABLE IF NOT EXISTS `tgs` (
  `tg_nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tag number',
  `tg_de` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'German translation of tag',
  `tg_en` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'English translation of tag',
  PRIMARY KEY (`tg_nr`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=26 COMMENT='Schlagwörter intern' AUTO_INCREMENT=483 ;

-- --------------------------------------------------------

--
-- Table structure for table `tgs_used`
--

CREATE TABLE IF NOT EXISTS `tgs_used` (
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel KID',
  `tg_nr` smallint(5) unsigned NOT NULL COMMENT 'number of tag',
  `modus` enum('i','v') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tg freq for inputs or voting tags',
  `freq` smallint(5) unsigned NOT NULL COMMENT 'frequency of used tag per voted tids',
  PRIMARY KEY (`kid`,`tg_nr`,`modus`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `tmid` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Temp User ID',
  `block` enum('b','u','c') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'blocked. unknown, user-confirmed',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(70) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `last_act` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'feststellen der letzten aktivität',
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Personen-/ Gruppenname',
  `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mail Address',
  `grp` smallint(5) unsigned NOT NULL COMMENT 'Group size or single',
  `pwd` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password hash',
  `newsl_subscr` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `lg` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Login via mail click',
  `cmnt` varchar(400) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `lvl` enum('usr','adm','edt') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'usr' COMMENT 'User, Editor or Admin',
  `confirm_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail',
  `group_type` enum('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe',
  `source` set('d','g','p','m') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=169 CHECKSUM=1 COMMENT='Users in the system' AUTO_INCREMENT=128744 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE IF NOT EXISTS `user_info` (
  `user_info_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'User Info ID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'User ID',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'Consultation ID',
  `ip` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` varchar(70) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `newsl_subscr` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `grp` smallint(5) unsigned NOT NULL COMMENT 'Group size or single',
  `lg` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Login via mail click',
  `cmnt` varchar(400) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `group_type` enum('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe',
  `source` set('d','g','p','m') COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cmnt_ext` varchar(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`user_info_id`) USING BTREE
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=148 CHECKSUM=1 COMMENT='User info' AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `vt_final`
--

CREATE TABLE IF NOT EXISTS `vt_final` (
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel to Consultation ID',
  `tid` int(10) unsigned NOT NULL COMMENT 'related to which ttid',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'from which User ID',
  `pts` tinyint(3) unsigned NOT NULL COMMENT 'actual vote (accumulated value)',
  PRIMARY KEY (`tid`,`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='All votes cast';

-- --------------------------------------------------------

--
-- Table structure for table `vt_grps`
--

CREATE TABLE IF NOT EXISTS `vt_grps` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'rel UID',
  `sub_user` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'email address',
  `sub_uid` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid',
  `kid` smallint(5) unsigned NOT NULL COMMENT 'rel KID',
  `member` enum('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'y= confirmed by group',
  `vt_inp_list` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of votable tids',
  `vt_rel_qid` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of rel QIDs',
  `vt_tg_list` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of all (still) available tags for this user',
  PRIMARY KEY (`uid`,`sub_uid`,`kid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=65 CHECKSUM=1 COMMENT='Abstimmende werden Gruppen zugeordnet';

-- --------------------------------------------------------

--
-- Table structure for table `vt_indiv`
--

CREATE TABLE IF NOT EXISTS `vt_indiv` (
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'ref UID',
  `sub_uid` char(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'individual subuser',
  `tid` int(10) unsigned NOT NULL COMMENT 'voted on which TID',
  `pts` tinyint(4) NOT NULL COMMENT 'the vote itself (points)',
  `pimp` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n',
  `status` enum('v','s','c') COLLATE utf8_unicode_ci NOT NULL COMMENT 'v=voted, s=skipped(vorerst), c=confirmed von sub_uid',
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when vote was cast',
  UNIQUE KEY `sub_uid` (`sub_uid`,`tid`) USING BTREE,
  UNIQUE KEY `Stimmenzählung` (`uid`,`tid`,`sub_uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=111 CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='Einzelstimmen';

-- --------------------------------------------------------

--
-- Table structure for table `vt_rights`
--

CREATE TABLE IF NOT EXISTS `vt_rights` (
  `kid` int(5) unsigned NOT NULL COMMENT 'rel KID',
  `uid` mediumint(8) unsigned NOT NULL COMMENT 'rel UID',
  `vt_weight` smallint(5) unsigned NOT NULL COMMENT 'Voting weight of this group',
  `vt_code` char(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Voting access code for this group',
  `grp_siz` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Group size that we recognise',
  PRIMARY KEY (`kid`,`uid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AVG_ROW_LENGTH=36 CHECKSUM=1 ROW_FORMAT=FIXED COMMENT='Voting rights at certain consultation';

-- --------------------------------------------------------

--
-- Table structure for table `vt_settings`
--

CREATE TABLE IF NOT EXISTS `vt_settings` (
  `kid` int(11) NOT NULL COMMENT 'ref to KonsultationID',
  `btn_important` enum('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Super preference button on/off',
  `btn_important_label` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Ist mir besonders wichtig' COMMENT 'Label for the super preference button',
  `btn_important_max` tinyint(4) NOT NULL DEFAULT '6' COMMENT 'Max amount of items for the super preference button',
  `btn_important_factor` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'Multiplier for votes in super button',
  `btn_numbers` enum('0','1','2','3','4') COLLATE utf8_unicode_ci NOT NULL DEFAULT '3' COMMENT 'number of voting buttons',
  `btn_labels` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig' COMMENT 'labels of voting buttons, comma-separated',
  PRIMARY KEY (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
