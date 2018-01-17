-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned DEFAULT NULL,
  `proj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'which project',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Readable descr for admin',
  `is_showed` tinyint(1) DEFAULT '1',
  `ref_nm` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Article reference name',
  `artcl` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Article itself',
  `sidebar` text COLLATE utf8mb4_unicode_ci COMMENT 'Content for sidebar',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'Parent article',
  `time_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`art_id`) USING BTREE,
  KEY `articles_kid_fkey` (`kid`),
  KEY `articles_ref_nm_fkey` (`ref_nm`),
  KEY `articles_parent_id_fkey` (`parent_id`),
  CONSTRAINT `articles_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `articles_parent_id_fkey` FOREIGN KEY (`parent_id`) REFERENCES `articles` (`art_id`),
  CONSTRAINT `articles_ref_nm_fkey` FOREIGN KEY (`ref_nm`) REFERENCES `articles_refnm` (`ref_nm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `articles_refnm`;
CREATE TABLE `articles_refnm` (
  `ref_nm` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'article reference name',
  `lng` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'language code',
  `desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'readable description',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scope` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  UNIQUE KEY `ref_nm` (`ref_nm`,`lng`) USING BTREE,
  KEY `type` (`type`),
  KEY `scope` (`scope`),
  CONSTRAINT `articles_refnm_ibfk_1` FOREIGN KEY (`type`) REFERENCES `article_type` (`name`),
  CONSTRAINT `articles_refnm_ibfk_2` FOREIGN KEY (`scope`) REFERENCES `article_scope` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='default list of article types';

INSERT INTO `articles_refnm` (`ref_nm`, `lng`, `desc`, `type`, `scope`) VALUES
('about',	'de',	'Über uns',	'global',	'static'),
('article_explanation',	'de',	'Main consultation explanation text',	'consultation',	'info'),
('cnslt_backgr',	'de',	'Infos zum Thema',	'consultation',	'info'),
('cnslt_help',	'de',	'Praxishilfen',	'consultation',	'none'),
('cnslt_info',	'de',	'Infos zum Verfahren',	'consultation',	'info'),
('cnslt_quest',	'de',	'Fragenübersicht',	'consultation',	'info'),
('cnslt_summ',	'de',	'Zusammenfassung',	'consultation',	'none'),
('contact',	'de',	'Kontakt',	'global',	'static'),
('faq',	'de',	'Häufige Fragen',	'global',	'static'),
('followup',	'de',	'Follow-up',	'consultation',	'followup'),
('imprint',	'de',	'Impressum',	'global',	'static'),
('privacy',	'de',	'Datenschutz',	'global',	'static'),
('vot_res',	'de',	'Erklärung Abstimmungsergebnisse',	'consultation',	'voting'),
('vot_res_cnslt',	'de',	'Erklärung Abstimmungsergebnisse',	'consultation',	'voting');

DROP TABLE IF EXISTS `article_scope`;
CREATE TABLE `article_scope` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `article_scope` (`name`) VALUES
('followup'),
('info'),
('none'),
('static'),
('voting');

DROP TABLE IF EXISTS `article_type`;
CREATE TABLE `article_type` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `article_type` (`name`) VALUES
('consultation'),
('global');

DROP TABLE IF EXISTS `cnslt`;
CREATE TABLE `cnslt` (
  `kid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol',
  `inp_fr` datetime DEFAULT NULL COMMENT 'Input possible from date on',
  `inp_to` datetime DEFAULT NULL COMMENT 'Input possible till',
  `is_input_phase_showed` tinyint(1) NOT NULL DEFAULT '1',
  `is_support_phase_showed` tinyint(1) NOT NULL DEFAULT '0',
  `spprt_fr` datetime DEFAULT NULL COMMENT 'support button clickable from',
  `spprt_to` datetime DEFAULT NULL COMMENT 'Supporting possible until',
  `spprt_ct` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Counter for accumulated supports',
  `vot_fr` datetime DEFAULT NULL COMMENT 'Voting possible from date on',
  `vot_to` datetime DEFAULT NULL COMMENT 'Voting possible till',
  `is_voting_phase_showed` tinyint(1) NOT NULL DEFAULT '1',
  `vot_expl` text COLLATE utf8mb4_unicode_ci COMMENT 'info text for voting start',
  `is_voting_result_phase_showed` tinyint(1) NOT NULL DEFAULT '0',
  `is_followup_phase_showed` tinyint(1) NOT NULL DEFAULT '0',
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'order in slider (the higher the more important)',
  `titl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of consultation',
  `titl_short` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)',
  `titl_sub` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'subtitle (optional)',
  `img_file` text COLLATE utf8mb4_unicode_ci,
  `img_expl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics',
  `expl_short` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ln` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `is_discussion_active` tinyint(1) NOT NULL DEFAULT '1',
  `discussion_from` datetime DEFAULT NULL,
  `discussion_to` datetime DEFAULT NULL,
  `is_vt_finalized` tinyint(1) NOT NULL DEFAULT '1',
  `is_vt_anonymized` tinyint(1) NOT NULL DEFAULT '0',
  `phase_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phase_support` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phase_input` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phase_voting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phase_followup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `follow_up_explanation` text COLLATE utf8mb4_unicode_ci,
  `discussion_video_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_name` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_age` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_state` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_comments` tinyint(1) NOT NULL DEFAULT '1',
  `allow_groups` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_contribution_origin` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_individuals_sum` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_group_name` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_contact_person` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_notification` tinyint(1) NOT NULL DEFAULT '1',
  `field_switch_newsletter` tinyint(1) NOT NULL DEFAULT '1',
  `state_field_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `contribution_confirmation_info` text COLLATE utf8mb4_unicode_ci,
  `license_agreement` text COLLATE utf8mb4_unicode_ci,
  `groups_no_information` tinyint(1) NOT NULL DEFAULT '1',
  `anonymous_contribution` tinyint(1) NOT NULL DEFAULT '0',
  `anonymous_contribution_finish_info` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`kid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Basic definitions of consultations';


DROP TABLE IF EXISTS `contribution_type`;
CREATE TABLE `contribution_type` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contribution_type` (`name`) VALUES
('bp'),
('f'),
('from_discussion'),
('l');

DROP TABLE IF EXISTS `contributor_age`;
CREATE TABLE `contributor_age` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultation_id` (`consultation_id`),
  CONSTRAINT `contributor_age_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `dirs`;
CREATE TABLE `dirs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `dir_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `order` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cnslt_kid_fkey` (`kid`),
  CONSTRAINT `cnslt_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='voting prep folders (nested sets)';


DROP TABLE IF EXISTS `email`;
CREATE TABLE `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time_queued` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_sent` timestamp NULL DEFAULT NULL,
  `sent_by_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `body_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_project_code_ibfk` (`project_code`),
  KEY `email_time_sent_idx` (`time_sent`),
  KEY `email_time_queued_idx` (`time_queued`),
  CONSTRAINT `email_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_attachment`;
CREATE TABLE `email_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `email_attachment_email_id_ibfk` (`email_id`),
  CONSTRAINT `email_attachment_ibfk_1` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_component`;
CREATE TABLE `email_component` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `project_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_component_name_idx` (`name`),
  KEY `email_component_project_code_ibfk` (`project_code`),
  CONSTRAINT `email_component_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_placeholder`;
CREATE TABLE `email_placeholder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_global` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_placeholder_name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `email_placeholder` (`id`, `name`, `description`, `is_global`) VALUES
(1,	'voter_email',	'The email of the original voter.',	0),
(2,	'to_name',	'The name of the recipient. If the name is not known, teh value of {{to_email}} is used.',	0),
(3,	'to_email',	'The email address of the recipient.',	0),
(4,	'password_reset_url',	'The url where user can reset their password.',	0),
(5,	'confirmation_url',	'The confirmation link for the user to visit.',	0),
(6,	'rejection_url',	'The rejection link for the user to visit.',	0),
(7,	'consultation_title_short',	'The short version of the consultation title.',	0),
(8,	'consultation_title_long',	'The long version of the consultation title.',	0),
(9,	'input_phase_end',	'The end of the input phase.',	0),
(10,	'input_phase_start',	'The start of the input phase.',	0),
(11,	'voting_phase_end',	'The end of the voting phase.',	0),
(12,	'voting_phase_start',	'The start of the voting phase.',	0),
(13,	'inputs_html',	'The users inputs in html formatting.',	0),
(14,	'inputs_text',	'The users inputs in plain text formatting.',	0),
(15,	'voting_weight',	'The voting weight of the relevant user.',	0),
(16,	'voting_url',	'the url where voting takes place.',	0),
(17,	'group_category',	'The type of the relevant group',	0),
(18,	'from_name',	'The name of the sender.',	1),
(19,	'from_address',	'The email address of the sender.',	1),
(20,	'contact_name',	'The name from the contact info.',	1),
(21,	'contact_www',	'The www from the contact info.',	1),
(22,	'contact_email',	'The email address from the contact info.',	1),
(23,	'send_date',	'The date the email was send',	1),
(24,	'website_url',	'Link to the relevant page on the website.',	0),
(25,	'question_text',	'The number and the text of the relevant question.',	0),
(26,	'unsubscribe_url',	'Link to remove user from the relevant subscription or mailing list.',	0),
(27,	'contribution_text',	'The text of the contribution.',	0),
(28,	'input_thes',	'The theses part of the input.',	0),
(29,	'input_expl',	'The explanation part of the input.',	0),
(30,	'video_url',	'Link to the video contribution.',	0);

DROP TABLE IF EXISTS `email_recipient`;
CREATE TABLE `email_recipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `email_recipient_email_id_ibfk` (`email_id`),
  KEY `type` (`type`),
  CONSTRAINT `email_recipient_ibfk_1` FOREIGN KEY (`type`) REFERENCES `email_recipient_type` (`name`),
  CONSTRAINT `email_recipient_ibfk_2` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_recipient_type`;
CREATE TABLE `email_recipient_type` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `email_recipient_type` (`name`) VALUES
('bcc'),
('cc'),
('to');

DROP TABLE IF EXISTS `email_template`;
CREATE TABLE `email_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `type_id` int(10) unsigned NOT NULL,
  `project_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_template_name_project_code_idx` (`name`,`project_code`),
  KEY `email_template_project_code_ibfk` (`project_code`),
  KEY `email_template_type_id_ibfk` (`type_id`),
  CONSTRAINT `email_template_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`),
  CONSTRAINT `email_template_type_id_ibfk` FOREIGN KEY (`type_id`) REFERENCES `email_template_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_template_has_email_placeholder`;
CREATE TABLE `email_template_has_email_placeholder` (
  `email_template_id` int(10) unsigned NOT NULL,
  `email_placeholder_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`email_template_id`,`email_placeholder_id`),
  KEY `et_has_ep_email_placeholder_id_ibfk` (`email_placeholder_id`),
  CONSTRAINT `et_has_ep_email_placeholder_id_ibfk` FOREIGN KEY (`email_placeholder_id`) REFERENCES `email_placeholder` (`id`),
  CONSTRAINT `et_has_ep_email_template_id_ibfk` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `email_template_type`;
CREATE TABLE `email_template_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_template_type_name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `email_template_type` (`id`, `name`) VALUES
(1,	'custom'),
(2,	'system');

DROP TABLE IF EXISTS `footer`;
CREATE TABLE `footer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `footer_proj_ibfk` (`proj`),
  CONSTRAINT `footer_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj` (`proj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `fowups`;
CREATE TABLE `fowups` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up ID',
  `docorg` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'internal order of document',
  `embed` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'embedding for multimedia',
  `expl` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Erläuterung',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `ffid` int(10) unsigned NOT NULL,
  `lkyea` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of likes',
  `lknay` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of dislikes',
  PRIMARY KEY (`fid`),
  KEY `fowups_ffid_fkey` (`ffid`),
  KEY `type` (`type`),
  CONSTRAINT `fowups_ffid_fkey` FOREIGN KEY (`ffid`) REFERENCES `fowup_fls` (`ffid`),
  CONSTRAINT `fowups_ibfk_1` FOREIGN KEY (`type`) REFERENCES `fowups_type` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `fowups_rid`;
CREATE TABLE `fowups_rid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid_ref` int(10) NOT NULL,
  `fid` int(10) unsigned DEFAULT NULL,
  `tid` int(10) unsigned DEFAULT NULL,
  `ffid` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fowups_rid_fid_ref_fid_tid_ffid_key` (`fid_ref`,`fid`,`tid`,`ffid`),
  KEY `fowups_rid_fid_fkey` (`fid`),
  KEY `fowups_rid_tid_fkey` (`tid`),
  KEY `fowups_rid_ffid_fkey` (`ffid`),
  CONSTRAINT `fowups_rid_ffid_fkey` FOREIGN KEY (`ffid`) REFERENCES `fowup_fls` (`ffid`),
  CONSTRAINT `fowups_rid_fid_fkey` FOREIGN KEY (`fid`) REFERENCES `fowups` (`fid`),
  CONSTRAINT `fowups_rid_tid_fkey` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `fowups_supports`;
CREATE TABLE `fowups_supports` (
  `fid` int(10) unsigned NOT NULL,
  `tmphash` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`fid`,`tmphash`),
  CONSTRAINT `fowups_supports_fid_fkey` FOREIGN KEY (`fid`) REFERENCES `fowups` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `fowups_type`;
CREATE TABLE `fowups_type` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fowups_type` (`name`) VALUES
('action'),
('end'),
('general'),
('rejected'),
('supporting');

DROP TABLE IF EXISTS `fowup_fls`;
CREATE TABLE `fowup_fls` (
  `ffid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `titl` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Title of follow-up document',
  `who` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Who gave the follow-up',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was it released',
  `is_only_month_year_showed` tinyint(1) DEFAULT '0',
  `ref_doc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'reference to downloadable document',
  `ref_view` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document',
  `gfx_who` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Graphic of who',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  PRIMARY KEY (`ffid`) USING BTREE,
  KEY `fowup_fls_kid_fkey` (`kid`),
  KEY `type` (`type`),
  CONSTRAINT `fowup_fls_ibfk_1` FOREIGN KEY (`type`) REFERENCES `fowups_type` (`name`),
  CONSTRAINT `fowup_fls_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `group_size`;
CREATE TABLE `group_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultation_id` (`consultation_id`),
  CONSTRAINT `group_size_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `help_text`;
CREATE TABLE `help_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `body` text COLLATE utf8mb4_unicode_ci,
  `project_code` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `module` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  PRIMARY KEY (`id`),
  UNIQUE KEY `help_text_project_code_name_key` (`project_code`,`name`),
  KEY `module` (`module`),
  CONSTRAINT `help_text_ibfk_1` FOREIGN KEY (`module`) REFERENCES `help_text_module` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `help_text_module`;
CREATE TABLE `help_text_module` (
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `help_text_module` (`name`) VALUES
('admin'),
('default');

DROP TABLE IF EXISTS `inpt`;
CREATE TABLE `inpt` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Thesen ID',
  `qi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'QuestionID (new)',
  `dir` int(10) unsigned DEFAULT NULL,
  `thes` varchar(330) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'User reply',
  `expl` varchar(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Longer explanation',
  `uid` int(10) unsigned DEFAULT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Input given when',
  `is_confirmed` tinyint(1) DEFAULT NULL,
  `is_confirmed_by_user` tinyint(1) DEFAULT NULL,
  `is_votable` tinyint(1) DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spprts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'how many pressed support-haken',
  `votes` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Votes received',
  `place` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Place (rank)',
  `tg_nrs` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags',
  `notiz` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Notes for internal use',
  `confirmation_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_discussion_contrib` int(10) unsigned DEFAULT NULL,
  `video_service` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `reminders_sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`) USING BTREE,
  KEY `inpt_uid_ibfk` (`uid`),
  KEY `input_discussion_contrib_fkey` (`input_discussion_contrib`),
  KEY `inpt_qi_fkey` (`qi`),
  KEY `inpt_dirs_fkey` (`dir`),
  KEY `inpt_video_service_fkey` (`video_service`),
  KEY `type` (`type`),
  CONSTRAINT `inpt_dirs_fkey` FOREIGN KEY (`dir`) REFERENCES `dirs` (`id`),
  CONSTRAINT `inpt_ibfk_1` FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`),
  CONSTRAINT `inpt_ibfk_2` FOREIGN KEY (`type`) REFERENCES `contribution_type` (`name`),
  CONSTRAINT `inpt_qi_fkey` FOREIGN KEY (`qi`) REFERENCES `quests` (`qi`),
  CONSTRAINT `inpt_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  CONSTRAINT `input_discussion_contrib_fkey` FOREIGN KEY (`input_discussion_contrib`) REFERENCES `input_discussion` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='User input to questions';


DROP TABLE IF EXISTS `inpt_tgs`;
CREATE TABLE `inpt_tgs` (
  `tg_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tg_nr`,`tid`) USING BTREE,
  KEY `inpt_tgs_tid_ibfk` (`tid`),
  CONSTRAINT `inpt_tgs_tid_ibfk` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `input_discussion`;
CREATE TABLE `input_discussion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `input_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `is_user_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `body` text COLLATE utf8mb4_unicode_ci,
  `video_service` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `input_discussion_time_created_idx` (`time_created`),
  KEY `input_discussion_is_visible_idx` (`is_visible`),
  KEY `input_discussion_input_id_fkey` (`input_id`),
  KEY `input_discussion_user_id_fkey` (`user_id`),
  KEY `input_discussion_video_service_fkey` (`video_service`),
  CONSTRAINT `input_discussion_ibfk_1` FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`),
  CONSTRAINT `input_discussion_input_id_fkey` FOREIGN KEY (`input_id`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `input_discussion_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `input_relations`;
CREATE TABLE `input_relations` (
  `parent_id` int(10) unsigned NOT NULL,
  `child_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`parent_id`,`child_id`),
  KEY `child_id` (`child_id`),
  CONSTRAINT `input_relations_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `input_relations_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `language`;
CREATE TABLE `language` (
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `language` (`code`) VALUES
('ar_AE'),
('cs_CZ'),
('de_DE'),
('en_US'),
('es_ES'),
('fr_FR'),
('pl_PL'),
('ru_RU');

DROP TABLE IF EXISTS `license`;
CREATE TABLE `license` (
  `number` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`number`,`locale`),
  KEY `language_code_fkey` (`locale`),
  CONSTRAINT `license_ibfk_1` FOREIGN KEY (`locale`) REFERENCES `language` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `license` (`number`, `title`, `description`, `text`, `link`, `icon`, `alt`, `locale`) VALUES
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'ar_AE'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'cs_CZ'),
(1,	'Creative-Commons-Lizenz',	'Creative Commons 4.0: Namensnennung, nicht kommerziell, keine Bearbeitung',	'Die Beiträge werden unter einer <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.de\" target=\"_blank\" title=\"Mehr über die Creative-Commons-Lizenz erfahren\">Creative-Commons-Lizenz</a> veröffentlicht. Das bedeutet, dass eure Beiträge in Zusammenfassungen und Publikationen zu nicht-kommerziellen Zwecken weiterverwendet werden dürfen.          \"Da alle Beiträge hier anonym veröffentlicht werden, wird auch bei Weiterverwendung als Quelle nur diese Website genannt werden.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.de',	'license_cc.svg',	'CC-BY-NC 4.0',	'de_DE'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'en_US'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'es_ES'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'fr_FR'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'pl_PL'),
(1,	'Creative Commons license',	'Creative Commons license 4.0: attribution, non-commercial',	'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',	'http://creativecommons.org/licenses/by-nc/4.0/deed.en',	'license_cc.svg',	'CC-BY-NC 4.0',	'ru_RU');

DROP TABLE IF EXISTS `notification`;
CREATE TABLE `notification` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `notification_type_id_ibfk` (`type_id`),
  KEY `notification_user_id_ibfk` (`user_id`),
  CONSTRAINT `notification_type_id_ibfk` FOREIGN KEY (`type_id`) REFERENCES `notification_type` (`id`),
  CONSTRAINT `notification_user_id_ibfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `notification_parameter`;
CREATE TABLE `notification_parameter` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`notification_id`,`name`),
  CONSTRAINT `notification_parameter_notification_id_ibfk` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `notification_type`;
CREATE TABLE `notification_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_type_name_idx` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notification_type` (`id`, `name`) VALUES
(3,	'follow_up_created'),
(1,	'input_created'),
(2,	'input_discussion_contribution_created');

DROP TABLE IF EXISTS `parameter`;
CREATE TABLE `parameter` (
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `proj` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`name`,`proj`),
  KEY `parameter_proj_ibfk` (`proj`),
  CONSTRAINT `parameter_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj` (`proj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `proj`;
CREATE TABLE `proj` (
  `proj` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `titl_short` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Short title/name for project',
  `vot_q` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Question used for voting',
  `video_facebook_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `video_youtube_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `video_vimeo_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `theme_id` int(11) DEFAULT NULL,
  `color_accent_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `color_primary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `color_accent_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `mitmachen_bubble` tinyint(4) NOT NULL DEFAULT '1',
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `license` int(11) NOT NULL,
  `teaser_enabled` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`proj`),
  KEY `proj_theme_id_fk` (`theme_id`),
  KEY `proj_license_fkey` (`license`),
  KEY `language_code_fkey` (`locale`),
  CONSTRAINT `proj_ibfk_1` FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`),
  CONSTRAINT `proj_ibfk_2` FOREIGN KEY (`license`) REFERENCES `license` (`number`),
  CONSTRAINT `proj_ibfk_3` FOREIGN KEY (`locale`) REFERENCES `language` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='All projects active in this installation';


DROP TABLE IF EXISTS `quests`;
CREATE TABLE `quests` (
  `qi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `nr` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Number shown in ordered list',
  `q` varchar(300) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'The question itself',
  `q_xpl` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Explanation for question',
  `ln` char(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Language',
  `vot_q` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Introducing voting question',
  `time_modified` timestamp NULL DEFAULT NULL,
  `video_enabled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `qi` (`qi`) USING BTREE,
  KEY `qi_2` (`qi`) USING BTREE,
  KEY `quests_kid_fkey` (`kid`),
  CONSTRAINT `quests_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Questions for the consultations';


DROP TABLE IF EXISTS `tgs`;
CREATE TABLE `tgs` (
  `tg_nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tag number',
  `tg_de` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'German translation of tag',
  PRIMARY KEY (`tg_nr`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tgs` (`tg_nr`, `tg_de`) VALUES
(1,	'Bund'),
(2,	'Länder'),
(3,	'europäische Ebene'),
(4,	'regional'),
(5,	'Jugendverbände'),
(6,	'altersgemäß'),
(7,	'Aktivitäten'),
(8,	'aktiv werden'),
(9,	'auf allen Ebenen'),
(10,	'Beteiligungsangebote schaffen'),
(11,	'Bildung'),
(12,	'direkter Bezug'),
(13,	'contra Wahlaltersenkung'),
(14,	'Dialog'),
(15,	'Distanz'),
(16,	'Einfluss nehmen können'),
(17,	'Jugendliche ernst nehmen'),
(18,	'Europa'),
(19,	'Familie'),
(20,	'greifbar'),
(21,	'Glaubwürdigkeit'),
(22,	'Image'),
(23,	'Information'),
(24,	'Integration'),
(25,	'jugendfreundliche Ansprache'),
(26,	'Jugendinteressen berücksichtigen'),
(27,	'Wahlkampagnen'),
(28,	'Kompetenz'),
(29,	'Kommune'),
(30,	'Bundesland'),
(31,	'Medien'),
(32,	'Methoden'),
(33,	'Meinung bilden'),
(34,	'Parteien'),
(35,	'passives Wahlrecht'),
(36,	'politische Bildung'),
(37,	'Politiker_innen'),
(38,	'Wahlprogramme'),
(39,	'Kontakt, persönlicher'),
(40,	'pro Wahlaltersenkung'),
(41,	'Rahmenbedingungen'),
(42,	'Schule'),
(43,	'Transparenz'),
(44,	'Vorbilder'),
(45,	'Vorbereitung'),
(46,	'Wählen unter 14'),
(47,	'Wählen ab 14'),
(48,	'Wählen ab 16'),
(49,	'Wählen ab 18'),
(50,	'Wahlakt'),
(51,	'Wählen ist nicht alles'),
(52,	'Internet/soziale Netzwerke'),
(53,	'Zugang'),
(54,	'Ziele'),
(55,	'Klarheit'),
(56,	'Jugendbeteiligung verankern'),
(57,	'Demokratie'),
(58,	'Demokratie, direkte'),
(59,	'Engagement'),
(60,	'ePartizipation'),
(61,	'Gesetz'),
(62,	'Jugendinitiativen'),
(63,	'Motivation'),
(64,	'neue Beteiligungsformen'),
(65,	'Praxisbeispiel'),
(66,	'Reife'),
(67,	'UN-Kinderrechte'),
(68,	'Verbände'),
(69,	'Wählen ab 15'),
(70,	'Abiturient_innen'),
(71,	'Anerkennung'),
(72,	'Auszubildende'),
(73,	'benachteiligte Jugendliche'),
(74,	'Beratung'),
(75,	'Bewerbung'),
(76,	'Chancen/Potenziale'),
(77,	'Erfahrung'),
(78,	'Förderung'),
(79,	'Freiwilligendienst'),
(80,	'Freiwilligenprogramme'),
(81,	'ehrenamtliches Engagement'),
(82,	'Hauptschüler_innen'),
(83,	'Hindernisse'),
(84,	'Interesse'),
(85,	'Intransparenz'),
(86,	'junge Berufstätige'),
(87,	'Kosten'),
(88,	'Realschüler_innen'),
(89,	'Sprache'),
(90,	'Studium'),
(91,	'Unsicherheit'),
(92,	'Unübersichtlichkeit'),
(93,	'Vielfalt an Möglichkeiten'),
(94,	'wenig bekannt'),
(95,	'Zertifikat'),
(96,	'Selbstvertrauen'),
(97,	'Teamfähigkeit'),
(98,	'Organisationsfähigkeit'),
(99,	'Partizipation'),
(100,	'Kommunikation'),
(101,	'kritisches Denken'),
(102,	'Toleranz'),
(103,	'Verantwortung'),
(104,	'Wertschätzung'),
(105,	'Konflikte lösen können'),
(106,	'individuelle Fähigkeiten'),
(107,	'demokratische Strukturen'),
(108,	'Einfühlungsvermögen'),
(109,	'Selbstständigkeit'),
(110,	'Gleichberechtigung'),
(111,	'eigene Projekte umsetzen'),
(112,	'soziales Engagement'),
(113,	'Alltagskompetenz'),
(114,	'Zukunft, berufliche'),
(115,	'soziale Kompetenz'),
(116,	'Aushandlungsprozesse'),
(117,	'Kreativität'),
(118,	'Bildung, außerschulische'),
(119,	'sich ausprobieren'),
(120,	'Lösungsansätze finden'),
(121,	'Meinung vertreten'),
(122,	'dazu gehören'),
(123,	'Rechte'),
(124,	'Gemeinschaft'),
(125,	'Problemmanagement'),
(126,	'fachspezifische Kenntnisse'),
(127,	'Leitungsfunktion'),
(128,	'Pädagogik'),
(129,	'sich einsetzen'),
(130,	'Nachhaltigkeit'),
(131,	'Offenheit'),
(132,	'Flexibilität'),
(133,	'Gesellschaft'),
(134,	'Selbstreflexion'),
(135,	'Unterstützung, finanzielle'),
(136,	'Sonderurlaub'),
(137,	'ideelle Unterstützung'),
(138,	'Leistung'),
(139,	'Unternehmen'),
(140,	'geringe Wertschätzung'),
(141,	'Investition'),
(142,	'Auszeichnung'),
(143,	'hohe Wertschätzung'),
(144,	'Unterstützung'),
(145,	'Einstellungskriterium'),
(146,	'erschwerte Durchführung'),
(147,	'Praxis'),
(148,	'Akzeptanz'),
(149,	'Dankbarkeit'),
(150,	'Freizeit'),
(151,	'Bewertung, quantitative'),
(152,	'Bewertung, qualitative'),
(153,	'Bildungsurlaub'),
(154,	'Aufwand'),
(155,	'persönlich/individuell'),
(156,	'wissenschaftliche Studien'),
(157,	'Öffentlichkeit(sarbeit)'),
(158,	'Personal'),
(159,	'Dokumentation/Nachweis'),
(160,	'Standard(s)'),
(161,	'einheitlich'),
(162,	'Aktion/Kampagne'),
(163,	'Vergünstigungen'),
(164,	'Vernetzung'),
(165,	'Vorteile'),
(166,	'Ausstattung'),
(167,	'Mitbestimmung'),
(168,	'weniger Bürokratie'),
(169,	'freier Nachmittag'),
(170,	'Leiterausbildung'),
(171,	'Vorurteile'),
(172,	'Lobby'),
(173,	'Freiräume'),
(174,	'Prozess'),
(175,	'Freiwilligkeit'),
(176,	'Lernen'),
(177,	'unterschiedliche Relevanz'),
(178,	'Spaß'),
(179,	'Politik'),
(180,	'Zeit(management)'),
(181,	'Freunde'),
(182,	'Grenzen erfahren'),
(183,	'Zuverlässigkeit'),
(184,	'Natur'),
(185,	'Fairness'),
(186,	'steigende Anerkennung'),
(187,	'Gleichstellung'),
(188,	'Arbeitgeber_innen'),
(189,	'junge Menschen'),
(190,	'erwünscht/vorausgesetzt'),
(191,	'Wertschätzung, mittlere'),
(192,	'keine Wertschätzung'),
(193,	'fehlendes Bewusstsein'),
(194,	'Anerkennung, langsame'),
(195,	'unterschätzt'),
(196,	'Alter'),
(197,	'visuelle  Ergebnisse'),
(198,	'Bonus'),
(199,	'Sichtbarkeit'),
(200,	'Jugendpolitik'),
(201,	'bundesweit'),
(202,	'Fort-/Weiterbildung'),
(203,	'gemeinsame Konzepte/Transfer'),
(204,	'Gruppe'),
(205,	'formale Bildung'),
(206,	'Zeugnis'),
(207,	'Ausbildung'),
(208,	'Freistellung'),
(209,	'Jugendarbeit/Jugendhilfe'),
(210,	'Wirtschaft'),
(211,	'Jugendleiter_innen'),
(212,	'Verwaltung'),
(213,	'Identität'),
(214,	'Eltern'),
(215,	'kulturelle Unterschiede'),
(216,	'Kultur'),
(217,	'bewusster Umgang'),
(218,	'interkulturelles Bewusstsein'),
(219,	'Inklusion'),
(220,	'Diskriminierung'),
(221,	'Gleichbehandlung'),
(222,	'Arbeit/Beschäftigung'),
(223,	'Wahlrecht'),
(224,	'Aufklärung'),
(225,	'Projekte'),
(226,	'Kooperation'),
(227,	'Religion'),
(228,	'Quote'),
(229,	'Pflicht'),
(230,	'Staatsbürgerschaft'),
(231,	'Qualifikation'),
(232,	'Wohnsituation'),
(233,	'Wissen'),
(234,	'Rolle'),
(235,	'Gremium'),
(236,	'Wahlen'),
(237,	'MJSO'),
(238,	'interkulturelle Öffnung'),
(239,	'Begegnung'),
(240,	'internationaler Jugendaustausch'),
(241,	'Ausgrenzung'),
(242,	'Armut'),
(243,	'prekäre Lebensbedingungen'),
(244,	'Attraktivität'),
(245,	'Auseinandersetzungsprozesse'),
(246,	'Noten'),
(247,	'Medienkompetenz'),
(248,	'Bachelor/Master'),
(249,	'Verzweckung'),
(250,	'Auslandserfahrung'),
(251,	'Ängste'),
(252,	'Lebensnähe'),
(253,	'Behinderung'),
(254,	'Lohn/Bezahlung'),
(255,	'Gleichaltrige/Peers'),
(256,	'Au Pair'),
(257,	'Lehrer_innen'),
(258,	'Fachkräftemangel'),
(259,	'LGBT/Queer'),
(260,	'Behinderung, geistige'),
(261,	'Behinderung, körperliche'),
(262,	'Leistungsdruck/Konkurrenzdenken'),
(263,	'finanzielle Mittel'),
(264,	'Anderssein'),
(265,	'Gewalt'),
(266,	'Schüler_innenvertretung'),
(267,	'handlungsfähig sein'),
(268,	'Pubertät'),
(269,	'Arbeitslosigkeit'),
(270,	'Migrationshintergrund'),
(271,	'Musik'),
(272,	'Bildungsgrad'),
(273,	'Klima/Atmosphäre'),
(274,	'offene Jugendarbeit'),
(275,	'sich wohlfühlen können'),
(276,	'Generationenunterschiede'),
(277,	'Mobbing'),
(278,	'Erziehung'),
(279,	'Mobilität'),
(280,	'Mehrfachbenachteiligung/-diskriminierung'),
(281,	'Bildung, frühkindliche'),
(282,	'Übergänge zwischen Lebensphasen'),
(283,	'demografischer Wandel'),
(284,	'Nähe (räumliche)'),
(285,	'thematisieren'),
(286,	'Flüchtlinge'),
(287,	'Schulabbrecher'),
(288,	'nicht bekannt'),
(289,	'Macht'),
(290,	'Schulleitung'),
(291,	'Selbstbestimmung'),
(292,	'Schulsystem'),
(293,	'Zukunft'),
(294,	'Ferien'),
(295,	'Politikverdrossenheit'),
(296,	'Stimmrecht'),
(297,	'Schwächen'),
(298,	'Stärken'),
(299,	'Empowerment'),
(300,	'Vielfalt'),
(301,	'Selbstverständlichkeit'),
(302,	'Miteinander'),
(303,	'Tabu'),
(304,	'Mittlerrolle'),
(305,	'formale Bildung'),
(306,	'Mangel'),
(307,	'Selbstbewusstsein'),
(308,	'ländlicher Raum'),
(309,	'Jugendhilfeausschuss'),
(310,	'Gewerkschaft'),
(311,	'Selbstverpflichtung'),
(312,	'urbaner Raum'),
(313,	'Ehrlichkeit'),
(314,	'Vertrauen'),
(315,	'Zielgruppe'),
(316,	'Angebote'),
(317,	'Sicherheit'),
(318,	'Rente'),
(319,	'Praktikum'),
(320,	'Gewerkschaft'),
(321,	'Austausch');

DROP TABLE IF EXISTS `theme`;
CREATE TABLE `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `color_accent_1` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `color_primary` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `color_accent_2` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `theme` (`id`, `name`, `color_accent_1`, `color_primary`, `color_accent_2`) VALUES
(1,	'Green',	'fc9026',	'5fa4a0',	'02afdb'),
(2,	'Pink',	'fc9026',	'990066',	'02afdb'),
(3,	'Blue',	'fc9026',	'04a5eb',	'0074b5');

DROP TABLE IF EXISTS `urlkey_action`;
CREATE TABLE `urlkey_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `urlkey` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_visited` timestamp NULL DEFAULT NULL,
  `time_valid_to` timestamp NULL DEFAULT NULL,
  `handler_class` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlkey_action_urlkey_idx` (`urlkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `urlkey_action_parameter`;
CREATE TABLE `urlkey_action_parameter` (
  `urlkey_action_id` int(10) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`urlkey_action_id`,`name`),
  CONSTRAINT `urlkey_action_parameter_urlkey_action_id_ibfk` FOREIGN KEY (`urlkey_action_id`) REFERENCES `urlkey_action` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_confirmed` tinyint(1) DEFAULT NULL,
  `last_act` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'feststellen der letzten aktivität',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Mail Address',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_subscribed_newsletter` tinyint(1) DEFAULT '0',
  `cmnt` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  `source` set('d','g','p','m') COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group_from` int(11) DEFAULT NULL,
  `age_group_to` int(11) DEFAULT NULL,
  `regio_pax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_receiving_consultation_results` tinyint(1) DEFAULT NULL,
  `nick` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  PRIMARY KEY (`uid`) USING BTREE,
  UNIQUE KEY `users_email_idx` (`email`),
  KEY `role` (`role`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role`) REFERENCES `users_role` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users_role`;
CREATE TABLE `users_role` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users_role` (`name`) VALUES
('admin'),
('editor'),
('user');

DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info` (
  `user_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `kid` int(10) unsigned NOT NULL,
  `cmnt` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `source` set('d','g','p','m') COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` int(11) DEFAULT NULL,
  `name_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Name of contact person',
  `age_group` int(11) DEFAULT NULL,
  `regio_pax` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Bundesländer',
  `is_receiving_consultation_results` tinyint(1) DEFAULT '1',
  `date_added` timestamp NULL DEFAULT NULL,
  `cmnt_ext` varchar(600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `time_user_confirmed` timestamp NULL DEFAULT NULL,
  `confirmation_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `is_subscribed_newsletter` tinyint(1) DEFAULT NULL,
  `invitation_sent_date` datetime DEFAULT NULL,
  PRIMARY KEY (`user_info_id`) USING BTREE,
  KEY `user_info_uid_ibfk` (`uid`),
  KEY `user_info_kid_fkey` (`kid`),
  KEY `group_size` (`group_size`),
  KEY `age_group` (`age_group`),
  CONSTRAINT `user_info_ibfk_1` FOREIGN KEY (`group_size`) REFERENCES `group_size` (`id`),
  CONSTRAINT `user_info_ibfk_2` FOREIGN KEY (`age_group`) REFERENCES `contributor_age` (`id`),
  CONSTRAINT `user_info_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `user_info_uid_ibfk` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `video_service`;
CREATE TABLE `video_service` (
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `video_service` (`name`) VALUES
('facebook'),
('vimeo'),
('youtube');

DROP TABLE IF EXISTS `vt_final`;
CREATE TABLE `vt_final` (
  `kid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL COMMENT 'related to which tid',
  `uid` int(10) unsigned NOT NULL,
  `place` smallint(5) unsigned NOT NULL,
  `points` float NOT NULL COMMENT 'summary points (accumulated value)',
  `cast` int(11) NOT NULL COMMENT 'summary votes (accumulated value)',
  `rank` float NOT NULL COMMENT 'divident points/cast',
  `is_followups` tinyint(1) NOT NULL DEFAULT '0',
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 (tid''.-.''uid)',
  PRIMARY KEY (`id`),
  KEY `vt_final_uid_fkey` (`uid`),
  KEY `vt_final_tid_fkey` (`tid`),
  KEY `vt_final_kid_fkey` (`kid`),
  CONSTRAINT `vt_final_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `vt_final_tid_fkey` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `vt_final_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='All votes cast';


DROP TABLE IF EXISTS `vt_grps`;
CREATE TABLE `vt_grps` (
  `uid` int(10) unsigned NOT NULL,
  `sub_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'email address',
  `sub_uid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid',
  `kid` int(10) unsigned NOT NULL,
  `is_member` tinyint(1) DEFAULT NULL,
  `vt_inp_list` text COLLATE utf8mb4_unicode_ci COMMENT 'list of votable tids',
  `vt_rel_qid` text COLLATE utf8mb4_unicode_ci COMMENT 'list of rel QIDs',
  `vt_tg_list` text COLLATE utf8mb4_unicode_ci COMMENT 'list of all (still) available tags for this user',
  `reminders_sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`,`sub_uid`,`kid`) USING BTREE,
  KEY `vt_grps_kid_fkey` (`kid`),
  KEY `vt_grps_sub_uid_fkey` (`sub_uid`),
  CONSTRAINT `vt_grps_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `vt_grps_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `vt_indiv`;
CREATE TABLE `vt_indiv` (
  `uid` int(10) unsigned NOT NULL,
  `sub_uid` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'individual subuser',
  `tid` int(10) unsigned NOT NULL COMMENT 'voted on which TID',
  `pts` tinyint(4) DEFAULT NULL COMMENT 'the vote itself (points)',
  `is_pimp` tinyint(1) DEFAULT '0',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when vote was cast',
  `confirmation_hash` char(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  UNIQUE KEY `sub_uid` (`sub_uid`,`tid`) USING BTREE,
  UNIQUE KEY `Stimmenzählung` (`uid`,`tid`,`sub_uid`) USING BTREE,
  KEY `vt_indiv_tid_fkey` (`tid`),
  KEY `status` (`status`),
  CONSTRAINT `vt_indiv_ibfk_1` FOREIGN KEY (`sub_uid`) REFERENCES `vt_grps` (`sub_uid`),
  CONSTRAINT `vt_indiv_ibfk_2` FOREIGN KEY (`status`) REFERENCES `vt_indiv_status` (`name`),
  CONSTRAINT `vt_indiv_tid_fkey` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `vt_indiv_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `vt_indiv_status`;
CREATE TABLE `vt_indiv_status` (
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `vt_indiv_status` (`name`) VALUES
('confirmed'),
('skipped'),
('voted');

DROP TABLE IF EXISTS `vt_rights`;
CREATE TABLE `vt_rights` (
  `kid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `vt_weight` smallint(5) unsigned NOT NULL COMMENT 'Voting weight of this group',
  `vt_code` char(8) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Voting access code for this group',
  `grp_siz` int(11) DEFAULT NULL COMMENT 'Group size that we recognise',
  PRIMARY KEY (`kid`,`uid`) USING BTREE,
  KEY `vt_rights_uid_fkey` (`uid`),
  KEY `grp_siz` (`grp_siz`),
  CONSTRAINT `vt_rights_ibfk_1` FOREIGN KEY (`grp_siz`) REFERENCES `group_size` (`id`),
  CONSTRAINT `vt_rights_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `vt_rights_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Voting rights at certain consultation';


DROP TABLE IF EXISTS `vt_settings`;
CREATE TABLE `vt_settings` (
  `kid` int(10) unsigned NOT NULL,
  `is_btn_important` tinyint(1) NOT NULL DEFAULT '0',
  `btn_no_opinion` tinyint(1) NOT NULL DEFAULT '1',
  `btn_important_label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ist mir besonders wichtig' COMMENT 'Label for the super preference button',
  `btn_important_max` tinyint(4) NOT NULL DEFAULT '6' COMMENT 'Max amount of items for the super preference button',
  `btn_important_factor` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'Multiplier for votes in super button',
  `btn_numbers` int(11) DEFAULT '3',
  `btn_labels` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig' COMMENT 'labels of voting buttons, comma-separated',
  PRIMARY KEY (`kid`),
  CONSTRAINT `vt_settings_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2018-01-17 14:43:02
