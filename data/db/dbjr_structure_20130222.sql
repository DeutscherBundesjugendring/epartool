# SQL Manager for MySQL 5.3.1.3
# ---------------------------------------
# Host     : localhost
# Port     : 3306
# Database : dbjr


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

SET FOREIGN_KEY_CHECKS=0;

#
# Dropping database objects
#

DROP TABLE IF EXISTS `vt_rights`;
DROP TABLE IF EXISTS `vt_indiv`;
DROP TABLE IF EXISTS `vt_grps`;
DROP TABLE IF EXISTS `vt_final`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `tgs_used`;
DROP TABLE IF EXISTS `tgs`;
DROP TABLE IF EXISTS `supports`;
DROP TABLE IF EXISTS `sessns`;
DROP TABLE IF EXISTS `quests_choic`;
DROP TABLE IF EXISTS `quests`;
DROP TABLE IF EXISTS `proj`;
DROP TABLE IF EXISTS `ml_sent`;
DROP TABLE IF EXISTS `ml_def`;
DROP TABLE IF EXISTS `inpt_tgs`;
DROP TABLE IF EXISTS `inpt`;
DROP TABLE IF EXISTS `fowups`;
DROP TABLE IF EXISTS `fowup_fls`;
DROP TABLE IF EXISTS `edt_cnslt`;
DROP TABLE IF EXISTS `discussns`;
DROP TABLE IF EXISTS `discuss`;
DROP TABLE IF EXISTS `cnslt`;
DROP TABLE IF EXISTS `articles_refnm`;
DROP TABLE IF EXISTS `articles`;

#
# Structure for the `articles` table : 
#

CREATE TABLE `articles` (
  `art_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Article ID',
  `kid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'rel KID',
  `proj` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project',
  `desc` VARCHAR(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Readable descr for admin',
  `hid` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=hide from public',
  `ref_nm` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `artcl` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'Article itself',
  `sidebar` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'Content for sidebar',
  `parent_id` SMALLINT(5) DEFAULT NULL COMMENT 'parent article',
  PRIMARY KEY USING BTREE (`art_id`)
)ENGINE=MyISAM
CHECKSUM=1 AUTO_INCREMENT=121 AVG_ROW_LENGTH=261 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT=''
;

#
# Structure for the `articles_refnm` table : 
#

CREATE TABLE `articles_refnm` (
  `ref_nm` VARCHAR(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `lng` CHAR(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'language code',
  `desc` VARCHAR(44) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'readable description',
  `type` ENUM('g','b','s','m') COLLATE utf8_unicode_ci NOT NULL DEFAULT 's' COMMENT 'general, basic, specific, mail',
  UNIQUE INDEX `ref_nm` USING BTREE (`ref_nm`, `lng`)
)ENGINE=MyISAM
CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='default list of article types'
;

#
# Structure for the `cnslt` table : 
#

CREATE TABLE `cnslt` (
  `kid` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Consultation ID',
  `proj` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol',
  `inp_fr` DATETIME NOT NULL COMMENT 'Input possible from date on',
  `inp_to` DATETIME NOT NULL COMMENT 'Input possible till',
  `inp_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show input period',
  `spprt_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show support button',
  `spprt_fr` DATETIME NOT NULL COMMENT 'support button clickable from',
  `spprt_to` DATETIME NOT NULL COMMENT 'Supporting possible until',
  `spprt_ct` INTEGER(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Counter for accumulated supports',
  `vot_fr` DATETIME NOT NULL COMMENT 'Voting possible from date on',
  `vot_to` DATETIME NOT NULL COMMENT 'Voting possible till',
  `vot_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Show voting period',
  `vot_expl` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'info text for voting start',
  `vot_res_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Show voting results',
  `summ_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show summary of voting',
  `follup_show` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'show follow-up',
  `ord` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'order in slider (the higher the more important)',
  `titl` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of consultation',
  `titl_short` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)',
  `titl_sub` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subtitle (optional)',
  `img_file` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'file name of title graphics',
  `img_expl` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics',
  `expl` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanatory text',
  `ln` CHAR(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `adm` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Administrated by [not used yet]',
  `public` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'y=visible to public',
  PRIMARY KEY USING BTREE (`kid`)
)ENGINE=MyISAM
CHECKSUM=1 AUTO_INCREMENT=16 AVG_ROW_LENGTH=217 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Basic definitions of consultations'
;

#
# Structure for the `discuss` table : 
#

CREATE TABLE `discuss` (
  `tid` INTEGER(10) UNSIGNED NOT NULL,
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL,
  `tmphash` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY USING BTREE (`tid`, `uid`, `tmphash`)
)ENGINE=MyISAM
ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Zähler für Diskussionsbedarf-Button'
;

#
# Structure for the `discussns` table : 
#

CREATE TABLE `discussns` (
  `tid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'ThesenID',
  `uid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'UserID',
  `when` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When written',
  `inpt` VARCHAR(6000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Actual text',
  `type` ENUM('i','r','d','n','c','s') COLLATE utf8_unicode_ci NOT NULL COMMENT 'initial copy of tid, revision of tid, default, new tid, closed thread, secret',
  UNIQUE INDEX `tid` USING BTREE (`tid`, `when`)
)ENGINE=MyISAM
CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Discussion threads'
;

#
# Structure for the `edt_cnslt` table : 
#

CREATE TABLE `edt_cnslt` (
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'ref to UserID of editor',
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'ref to KID',
  `edt` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'right to edit',
  `own` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'is owner of this cnslt',
  PRIMARY KEY USING BTREE (`uid`, `kid`)
)ENGINE=MyISAM
CHECKSUM=1 ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='defines rights of editors or admins related to consultations'
;

#
# Structure for the `fowup_fls` table : 
#

CREATE TABLE `fowup_fls` (
  `ffid` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Follow-up File ID',
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'ref to KonsultationID',
  `titl` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of follow-up document',
  `who` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Who gave the follow-up',
  `when` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was it released',
  `ref_doc` VARCHAR(160) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'reference to downloadable document',
  `ref_view` VARCHAR(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document',
  `gfx_who` VARCHAR(160) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Graphic of who',
  PRIMARY KEY USING BTREE (`ffid`),
  UNIQUE INDEX `ffid` USING BTREE (`ffid`)
)ENGINE=MyISAM
AUTO_INCREMENT=3 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Follow-up files'
;

#
# Structure for the `fowups` table : 
#

CREATE TABLE `fowups` (
  `fid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'Follow-up ID',
  `rid` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'reference to TID/FID/FFID [Txx|Fxx|Dxx:linktyp]',
  `docorg` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'internal order of document',
  `embed` VARCHAR(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'embedding for multimedia',
  `expl` VARCHAR(10000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Erläuterung',
  `typ` ENUM('g','a','r','e') COLLATE utf8_unicode_ci NOT NULL COMMENT 'general, action, rejected, end',
  `ffid` SMALLINT(5) UNSIGNED ZEROFILL NOT NULL COMMENT 'reference to Follow-up File ID',
  `hlvl` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'hierarchy level in document, 1 is standard text,0 is footnoote, >1 are headings',
  `lkyea` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'number of likes',
  `lknay` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'number of dislikes',
  UNIQUE INDEX `fid` USING BTREE (`fid`)
)ENGINE=MyISAM
CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT=''
;

#
# Structure for the `inpt` table : 
#

CREATE TABLE `inpt` (
  `tid` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Thesen ID',
  `qi` INTEGER(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'QuestionID (new)',
  `kid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'consultation ID',
  `thes` VARCHAR(330) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'User reply',
  `expl` VARCHAR(2000) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Longer explanation',
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'from which User ID',
  `when` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Input given when',
  `block` ENUM('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'yes, no, unchecked',
  `user_conf` ENUM('u','c') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'unconfirmed. confirmed',
  `vot` ENUM('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'Zum Voting zugelassen',
  `typ` ENUM('p','f','l','bp') COLLATE utf8_unicode_ci NOT NULL COMMENT 'Problemanzeige, Forderung, Lösungsvorschlag, Best Practice',
  `spprts` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'how many pressed support-haken',
  `votes` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Votes received',
  `pts` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Points received',
  `rel_tid` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Related tids',
  `tg_nrs` VARCHAR(55) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags',
  `notiz` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Notes for internal use',
  `confirm_key` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT '',
  PRIMARY KEY USING BTREE (`tid`)
)ENGINE=MyISAM
CHECKSUM=1 AUTO_INCREMENT=3425 AVG_ROW_LENGTH=109 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='User input to questions'
;

#
# Structure for the `inpt_tgs` table : 
#

CREATE TABLE `inpt_tgs` (
  `tg_nr` INTEGER(11) NOT NULL COMMENT 'id of tag',
  `tid` INTEGER(11) NOT NULL COMMENT 'id of these (input)',
  PRIMARY KEY USING BTREE (`tg_nr`, `tid`)
)ENGINE=MyISAM
AVG_ROW_LENGTH=9 ROW_FORMAT=FIXED CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci'
COMMENT=''
;

#
# Structure for the `ml_def` table : 
#

CREATE TABLE `ml_def` (
  `mid` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'MailID',
  `refnm` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Reference name, e.g. footer, header',
  `kid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'consultation-specific or general (=0)',
  `proj` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project(s)',
  `ln` CHAR(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language of mail',
  `subj` VARCHAR(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Subject of e-mail (todo check maxlength in RFC)',
  `txt` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'TEXT mail',
  `html` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'HTML mail',
  `expl` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Explanation and possible variables',
  `head` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach header to mail',
  `foot` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Attach footer to mail',
  PRIMARY KEY USING BTREE (`mid`)
)ENGINE=MyISAM
CHECKSUM=1 AUTO_INCREMENT=19 AVG_ROW_LENGTH=240 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Mail definitions / defaults'
;

#
# Structure for the `ml_sent` table : 
#

CREATE TABLE `ml_sent` (
  `when` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `rec` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Recipient of mail',
  `sender` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'sent by',
  `subj` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'subject of mail sent',
  `proj` VARCHAR(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Sent from which project',
  `ip` VARCHAR(15) COLLATE latin1_bin NOT NULL DEFAULT '' COMMENT 'IP address of sender',
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY USING BTREE (`id`)
)ENGINE=MyISAM
AUTO_INCREMENT=12 AVG_ROW_LENGTH=70 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Mails sent by system'
;

#
# Structure for the `proj` table : 
#

CREATE TABLE `proj` (
  `proj` CHAR(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Project abbrev',
  `titl_short` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Short title/name for project',
  `email` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Address which the tool uses for sending out messages (todo check maxlength by rfc)',
  `realnm` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Real name to be uses as sender alias for e-mail',
  `smtp_srv` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP server (not used yet)',
  `smtp_prt` TINYINT(3) UNSIGNED NOT NULL DEFAULT 25 COMMENT 'SMTP port (not used yet)',
  `smtp_usr` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP user (not used yet)',
  `smtp_pwd` VARCHAR(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'SMTP password (not used yet)',
  `toolline` VARCHAR(260) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Tool headline itself',
  `vot_q` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Question used for voting',
  PRIMARY KEY USING BTREE (`proj`)
)ENGINE=MyISAM
AVG_ROW_LENGTH=20 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='All projects active in this installation'
;

#
# Structure for the `quests` table : 
#

CREATE TABLE `quests` (
  `qi` INTEGER(10) UNSIGNED NOT NULL COMMENT 'QuestionID (new)',
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'Consultation ID',
  `nr` CHAR(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Number shown in ordered list',
  `q` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'The question itself',
  `q_xpl` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'Explanation for question',
  `ln` CHAR(2) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `vot_q` VARCHAR(220) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Introducing voting question',
  UNIQUE INDEX `qi` USING BTREE (`qi`),
   INDEX `qi_2` USING BTREE (`qi`)
)ENGINE=MyISAM
CHECKSUM=1 AVG_ROW_LENGTH=93 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Questions for the consultations'
;

#
# Structure for the `quests_choic` table : 
#

CREATE TABLE `quests_choic` (
  `qi` INTEGER(10) UNSIGNED NOT NULL COMMENT 'ref to QuestionID',
  `opt` CHAR(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Option for reference',
  `desc` VARCHAR(240) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Descriptive label',
  `ln` CHAR(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'de' COMMENT 'Language'
)ENGINE=MyISAM
CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Options for multiple choice questions'
;

#
# Structure for the `sessns` table : 
#

CREATE TABLE `sessns` (
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'User ID',
  `cid` CHAR(32) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Confirmation code',
  `sess_strt` DATETIME NOT NULL COMMENT 'When session started',
  `sess_end` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When last activity took place',
  `ip` CHAR(15) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` VARCHAR(120) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `name_pers` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of contact person',
  `name_grp` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Name of group',
  `source` SET('d','g','m','p') NOT NULL DEFAULT 'm' COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanation of misc source',
  `grp_size` TINYINT(3) UNSIGNED NOT NULL COMMENT '1,10,30,80,150,over',
  `regio_pax` VARCHAR(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Bundesländer',
  `age_grp` ENUM('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `find_out` VARCHAR(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'woher kennt ihr uns',
  `group_type` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Art der Gruppe',
  `what_you_do` ENUM('school','education','work','selfemployed','volunteer','unemployed','notspecified') COLLATE utf8_unicode_ci NOT NULL,
  `kid` SMALLINT(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'KID, falls Eintragung abgeschlossen',
  `publ_us` ENUM('n','y') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'publicize us a contributor',
  `cnslt_results` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `cmnt_ext` VARCHAR(600) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Comment by user',
  PRIMARY KEY USING BTREE (`uid`, `sess_strt`, `kid`)
)ENGINE=MyISAM
CHECKSUM=1 AVG_ROW_LENGTH=184 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Users in the system'
;

#
# Structure for the `supports` table : 
#

CREATE TABLE `supports` (
  `tid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'ref to TID',
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'uid if existing',
  `tmphash` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'hash from IP etc',
  PRIMARY KEY USING BTREE (`tid`, `uid`, `tmphash`)
)ENGINE=MyISAM
ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT=''
;

#
# Structure for the `tgs` table : 
#

CREATE TABLE `tgs` (
  `tg_nr` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'tag number',
  `tg_de` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'German translation of tag',
  `tg_en` VARCHAR(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'English translation of tag',
  PRIMARY KEY USING BTREE (`tg_nr`)
)ENGINE=MyISAM
AUTO_INCREMENT=433 AVG_ROW_LENGTH=26 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Schlagwörter intern'
;

#
# Structure for the `tgs_used` table : 
#

CREATE TABLE `tgs_used` (
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'rel KID',
  `tg_nr` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'number of tag',
  `modus` ENUM('i','v') COLLATE utf8_unicode_ci NOT NULL COMMENT 'tg freq for inputs or voting tags',
  `freq` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'frequency of used tag per voted tids',
  PRIMARY KEY USING BTREE (`kid`, `tg_nr`, `modus`)
)ENGINE=MyISAM
ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT=''
;

#
# Structure for the `users` table : 
#

CREATE TABLE `users` (
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User ID',
  `tmid` CHAR(32) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'Temp User ID',
  `block` ENUM('b','u','c') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'blocked. unknown, user-confirmed',
  `ip` CHAR(15) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'IP Address User',
  `agt` VARCHAR(70) COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT 'User agent (Browser)',
  `last_act` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'feststellen der letzten aktivität',
  `name` VARCHAR(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Personen-/ Gruppenname',
  `email` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mail Address',
  `grp` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'Group size or single',
  `pwd` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Password hash',
  `newsl_subscr` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `lg` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Login via mail click',
  `cmnt` VARCHAR(400) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `lvl` ENUM('usr','adm','edt') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'usr' COMMENT 'User, Editor or Admin',
  `confirm_key` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail',
  `group_type` ENUM('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe',
  `source` SET('d','g','p','m') DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` VARCHAR(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` TINYINT(3) UNSIGNED DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` ENUM('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` VARCHAR(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  PRIMARY KEY USING BTREE (`uid`)
)ENGINE=MyISAM
CHECKSUM=1 AUTO_INCREMENT=70143 AVG_ROW_LENGTH=169 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Users in the system'
;

#
# Structure for the `vt_final` table : 
#

CREATE TABLE `vt_final` (
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'rel to Consultation ID',
  `tid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'related to which ttid',
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'from which User ID',
  `pts` TINYINT(3) UNSIGNED NOT NULL COMMENT 'actual vote (accumulated value)',
  PRIMARY KEY USING BTREE (`tid`, `uid`)
)ENGINE=MyISAM
CHECKSUM=1 ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='All votes cast'
;

#
# Structure for the `vt_grps` table : 
#

CREATE TABLE `vt_grps` (
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'rel UID',
  `sub_user` VARCHAR(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'email address',
  `sub_uid` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid',
  `kid` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'rel KID',
  `member` ENUM('y','n','u') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'u' COMMENT 'y= confirmed by group',
  `vt_inp_list` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of votable tids',
  `vt_rel_qid` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of rel QIDs',
  `vt_tg_list` TEXT COLLATE utf8_unicode_ci NOT NULL COMMENT 'list of all (still) available tags for this user',
  PRIMARY KEY USING BTREE (`uid`, `sub_uid`, `kid`)
)ENGINE=MyISAM
CHECKSUM=1 AVG_ROW_LENGTH=65 CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Abstimmende werden Gruppen zugeordnet'
;

#
# Structure for the `vt_indiv` table : 
#

CREATE TABLE `vt_indiv` (
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'ref UID',
  `sub_uid` CHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'individual subuser',
  `tid` INTEGER(10) UNSIGNED NOT NULL COMMENT 'voted on which TID',
  `pts` TINYINT(4) NOT NULL COMMENT 'the vote itself (points)',
  `status` ENUM('v','s','c') COLLATE utf8_unicode_ci NOT NULL COMMENT 'v=voted, s=skipped(vorerst), c=confirmed von sub_uid',
  `upd` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when vote was cast',
  UNIQUE INDEX `sub_uid` USING BTREE (`sub_uid`, `tid`),
  UNIQUE INDEX `Stimmenzählung` USING BTREE (`uid`, `tid`, `sub_uid`)
)ENGINE=MyISAM
CHECKSUM=1 AVG_ROW_LENGTH=111 ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Einzelstimmen'
;

#
# Structure for the `vt_rights` table : 
#

CREATE TABLE `vt_rights` (
  `kid` INTEGER(5) UNSIGNED NOT NULL COMMENT 'rel KID',
  `uid` MEDIUMINT(8) UNSIGNED NOT NULL COMMENT 'rel UID',
  `vt_weight` SMALLINT(5) UNSIGNED NOT NULL COMMENT 'Voting weight of this group',
  `vt_code` CHAR(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Voting access code for this group',
  `grp_siz` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Group size that we recognise',
  PRIMARY KEY USING BTREE (`kid`, `uid`)
)ENGINE=MyISAM
CHECKSUM=1 AVG_ROW_LENGTH=36 ROW_FORMAT=FIXED CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci'
COMMENT='Voting rights at certain consultation'
;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;