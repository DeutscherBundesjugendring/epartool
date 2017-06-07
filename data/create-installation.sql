-- Database must have CHARACTER SET = utf8mb4 and COLLATE = utf8mb4_unicode_ci;
SET foreign_key_checks = 0;

CREATE TABLE `articles` (
  `art_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned DEFAULT NULL,
  `proj` varchar(20) NOT NULL DEFAULT '' COMMENT 'which project',
  `desc` varchar(44) NOT NULL DEFAULT '' COMMENT 'Readable descr for admin',
  `hid` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'y=hide from public',
  `ref_nm` varchar(30) DEFAULT NULL COMMENT 'Article reference name',
  `artcl` text NOT NULL COMMENT 'Article itself',
  `sidebar` text NOT NULL COMMENT 'Content for sidebar',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'Parent article',
  `time_modified` timestamp NULL,
  PRIMARY KEY (`art_id`) USING BTREE,
  KEY `articles_kid_fkey` (`kid`),
  KEY `articles_ref_nm_fkey` (`ref_nm`),
  KEY `articles_parent_id_fkey` (`parent_id`),
  CONSTRAINT `articles_parent_id_fkey` FOREIGN KEY (`parent_id`) REFERENCES `articles` (`art_id`),
  CONSTRAINT `articles_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `articles_ref_nm_fkey` FOREIGN KEY (`ref_nm`) REFERENCES `articles_refnm` (`ref_nm`)
);


CREATE TABLE `articles_refnm` (
  `ref_nm` varchar(30) NOT NULL DEFAULT '' COMMENT 'article reference name',
  `lng` char(2) NOT NULL DEFAULT '' COMMENT 'language code',
  `desc` varchar(44) NOT NULL DEFAULT '' COMMENT 'readable description',
  `type` enum('g','b') NOT NULL DEFAULT 'g' COMMENT 'general, basic',
  `scope` enum('none','info','voting','followup','static') NOT NULL DEFAULT 'none' COMMENT 'none, info, voting, followup, static',
  UNIQUE KEY `ref_nm` (`ref_nm`,`lng`) USING BTREE
) COMMENT='default list of article types';

INSERT INTO `articles_refnm` (`ref_nm`, `lng`, `desc`, `type`, `scope`) VALUES
('about', 'de', 'Über uns', 'g', 'static'),
('article_explanation', 'de', 'Main consultation explanation text', 'b', 'info'),
('cnslt_backgr', 'de', 'Infos zum Thema', 'b', 'info'),
('cnslt_help', 'de', 'Praxishilfen', 'b', 'none'),
('cnslt_info', 'de', 'Infos zum Verfahren', 'b', 'info'),
('cnslt_quest', 'de', 'Fragenübersicht', 'b', 'info'),
('cnslt_summ', 'de', 'Zusammenfassung', 'b', 'none'),
('contact', 'de', 'Kontakt', 'g', 'static'),
('faq', 'de', 'Häufige Fragen', 'g', 'static'),
('followup', 'de', 'Follow-up', 'b', 'followup'),
('imprint', 'de', 'Impressum', 'g', 'static'),
('privacy', 'de', 'Datenschutz', 'g', 'static'),
('vot_res', 'de', 'Erklärung Abstimmungsergebnisse', 'b', 'voting'),
('vot_res_cnslt', 'de', 'Erklärung Abstimmungsergebnisse', 'b', 'voting');

CREATE TABLE `cnslt` (
  `kid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj` varchar(20) NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol',
  `inp_fr` datetime NOT NULL COMMENT 'Input possible from date on',
  `inp_to` datetime NOT NULL COMMENT 'Input possible till',
  `inp_show` enum('y','n') NOT NULL DEFAULT 'y' COMMENT 'Show input period',
  `spprt_show` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Show support button',
  `spprt_fr` datetime NOT NULL COMMENT 'support button clickable from',
  `spprt_to` datetime NOT NULL COMMENT 'Supporting possible until',
  `spprt_ct` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Counter for accumulated supports',
  `vot_fr` datetime NOT NULL COMMENT 'Voting possible from date on',
  `vot_to` datetime NOT NULL COMMENT 'Voting possible till',
  `vot_show` enum('y','n') NOT NULL DEFAULT 'y' COMMENT 'Show voting period',
  `vot_expl` text NOT NULL COMMENT 'info text for voting start',
  `vot_res_show` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Show voting results',
  `follup_show` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'show follow-up',
  `ord` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'order in slider (the higher the more important)',
  `titl` varchar(200) NOT NULL DEFAULT '' COMMENT 'Title of consultation',
  `titl_short` varchar(40) NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)',
  `titl_sub` varchar(200) NOT NULL DEFAULT '' COMMENT 'subtitle (optional)',
  `img_file` varchar(64) DEFAULT NULL COMMENT 'File name of the title image.',
  `img_expl` varchar(100) NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics',
  `expl_short` text NOT NULL,
  `ln` char(2) NOT NULL DEFAULT '' COMMENT 'Language',
  `public` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'y=visible to public',
  `is_discussion_active` tinyint(1) NOT NULL DEFAULT '1',
  `discussion_from` datetime DEFAULT NULL,
  `discussion_to` datetime DEFAULT NULL,
  `vt_finalized` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'voting results are in vt_final',
  `vt_anonymized` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'is voting anonymized',
  `phase_info` varchar(50) DEFAULT NULL,
  `phase_support` varchar(50) DEFAULT NULL,
  `phase_input` varchar(50) DEFAULT NULL,
  `phase_voting` varchar(50) DEFAULT NULL,
  `phase_followup` varchar(50) DEFAULT NULL,
  `follow_up_explanation` text,
  PRIMARY KEY (`kid`) USING BTREE
) COMMENT='Basic definitions of consultations';


CREATE TABLE `dirs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `dir_name` varchar(120) NOT NULL,
  `order` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cnslt_kid_fkey` (`kid`),
  CONSTRAINT `cnslt_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) COMMENT='voting prep folders (nested sets)';


CREATE TABLE `email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_code` char(2) NOT NULL,
  `time_queued` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `time_sent` timestamp NULL DEFAULT NULL,
  `sent_by_user` varchar(255) DEFAULT NULL,
  `subject` varchar(75) DEFAULT NULL,
  `body_html` text,
  `body_text` text,
  PRIMARY KEY (`id`),
  KEY `email_project_code_ibfk` (`project_code`),
  KEY `email_time_sent_idx` (`time_sent`),
  KEY `email_time_queued_idx` (`time_queued`),
  CONSTRAINT `email_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`)
);


CREATE TABLE `email_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned NOT NULL,
  `filepath` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email_attachment_email_id_ibfk` (`email_id`),
  CONSTRAINT `email_attachment_email_id_ibfk` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`)
);


CREATE TABLE `email_component` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `project_code` char(2) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_component_name_idx` (`name`),
  KEY `email_component_project_code_ibfk` (`project_code`),
  CONSTRAINT `email_component_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`)
);


CREATE TABLE `email_placeholder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `is_global` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_placeholder_name_idx` (`name`)
);

INSERT INTO `email_placeholder` (`name`, `description`, `is_global`) VALUES
('voter_email', 'The email of the original voter.', 0),
('to_name', 'The name of the recipient. If the name is not known, teh value of {{to_email}} is used.', 0),
('to_email', 'The email address of the recipient.', 0),
('password_reset_url', 'The url where user can reset their password.', 0),
('confirmation_url', 'The confirmation link for the user to visit.', 0),
('rejection_url', 'The rejection link for the user to visit.', 0),
('consultation_title_short', 'The short version of the consultation title.', 0),
('consultation_title_long', 'The long version of the consultation title.', 0),
('input_phase_end', 'The end of the input phase.', 0),
('input_phase_start', 'The start of the input phase.', 0),
('voting_phase_end', 'The end of the voting phase.', 0),
('voting_phase_start', 'The start of the voting phase.', 0),
('inputs_html', 'The users inputs in html formatting.', 0),
('inputs_text', 'The users inputs in plain text formatting.', 0),
('voting_weight', 'The voting weight of the relevant user.', 0),
('voting_url', 'the url where voting takes place.', 0),
('group_category', 'The type of the relevant group', 0),
('from_name', 'The name of the sender.', 1),
('from_address', 'The email address of the sender.', 1),
('contact_name', 'The name from the contact info.', 1),
('contact_www', 'The www from the contact info.', 1),
('contact_email', 'The email address from the contact info.', 1),
('send_date', 'The date the email was send', 1),
('website_url', 'Link to the relevant page on the website.', 0),
('question_text', 'The number and the text of the relevant question.', 0),
('unsubscribe_url', 'Link to remove user from the relevant subscription or mailing list.', 0),
('contribution_text', 'The text of the contribution.', 0),
('input_thes', 'The theses part of the input.', 0),
('input_expl', 'The explanation part of the input.', 0),
('video_url', 'Link to the video contribution.', 0);

CREATE TABLE `email_recipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned NOT NULL,
  `type` enum('to','cc','bcc') NOT NULL,
  `name` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_recipient_email_id_ibfk` (`email_id`),
  CONSTRAINT `email_recipient_email_id_ibfk` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`)
);


CREATE TABLE `email_template` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type_id` int(10) unsigned NOT NULL,
  `project_code` char(2) NOT NULL,
  `subject` varchar(75) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_template_name_project_code_idx` (`name`,`project_code`),
  KEY `email_template_project_code_ibfk` (`project_code`),
  KEY `email_template_type_id_ibfk` (`type_id`),
  CONSTRAINT `email_template_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`),
  CONSTRAINT `email_template_type_id_ibfk` FOREIGN KEY (`type_id`) REFERENCES `email_template_type` (`id`)
);


CREATE TABLE `email_template_has_email_placeholder` (
  `email_template_id` int(10) unsigned NOT NULL,
  `email_placeholder_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`email_template_id`,`email_placeholder_id`),
  KEY `et_has_ep_email_placeholder_id_ibfk` (`email_placeholder_id`),
  CONSTRAINT `et_has_ep_email_placeholder_id_ibfk` FOREIGN KEY (`email_placeholder_id`) REFERENCES `email_placeholder` (`id`),
  CONSTRAINT `et_has_ep_email_template_id_ibfk` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`)
);


CREATE TABLE `email_template_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_template_type_name_idx` (`name`)
);

INSERT INTO `email_template_type` (`name`) VALUES
('custom'),
('system');

CREATE TABLE `footer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proj` char(2) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`),
  KEY `footer_proj_ibfk` (`proj`),
  CONSTRAINT `footer_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj` (`proj`)
);


CREATE TABLE `fowups` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Follow-up ID',
  `docorg` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'internal order of document',
  `embed` varchar(600) NOT NULL DEFAULT '' COMMENT 'embedding for multimedia',
  `expl` text NOT NULL COMMENT 'Erläuterung',
  `typ` enum('g','s','a','r','e') NOT NULL DEFAULT 'g' COMMENT 'general, supporting, action, rejected, end',
  `ffid` int(10) unsigned NOT NULL,
  `hlvl` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'hierarchy level in document, 1 is standard text,0 is footnoote, >1 are headings',
  `lkyea` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of likes',
  `lknay` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'number of dislikes',
  PRIMARY KEY (`fid`),
  KEY `fowups_ffid_fkey` (`ffid`),
  CONSTRAINT `fowups_ffid_fkey` FOREIGN KEY (`ffid`) REFERENCES `fowup_fls` (`ffid`)
);


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
);


CREATE TABLE `fowups_supports` (
  `fid` int(10) unsigned NOT NULL,
  `tmphash` char(32) NOT NULL,
  PRIMARY KEY (`fid`,`tmphash`),
  CONSTRAINT `fowups_supports_fid_fkey` FOREIGN KEY (`fid`) REFERENCES `fowups` (`fid`)
);


CREATE TABLE `fowup_fls` (
  `ffid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `titl` varchar(300) NOT NULL COMMENT 'Title of follow-up document',
  `who` varchar(200) NOT NULL COMMENT 'Who gave the follow-up',
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When was it released',
  `show_no_day` enum('n','y') NOT NULL DEFAULT 'n' COMMENT 'cell ''when'' shown only as year and month',
  `ref_doc` varchar(160) NOT NULL COMMENT 'reference to downloadable document',
  `ref_view` varchar(2000) NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document',
  `gfx_who` varchar(160) NOT NULL DEFAULT '' COMMENT 'Graphic of who',
  PRIMARY KEY (`ffid`) USING BTREE,
  KEY `fowup_fls_kid_fkey` (`kid`),
  CONSTRAINT `fowup_fls_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
);


CREATE TABLE `help_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `body` text,
  `project_code` char(2) NOT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE `inpt` (
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Thesen ID',
  `qi` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'QuestionID (new)',
  `dir` int(10) unsigned DEFAULT NULL,
  `thes` varchar(330) NOT NULL DEFAULT '' COMMENT 'User reply',
  `expl` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Longer explanation',
  `uid` int(10) unsigned DEFAULT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Input given when',
  `block` enum('y','n','u') NOT NULL DEFAULT 'u' COMMENT 'yes, no, unchecked',
  `user_conf` enum('u','c','r') NOT NULL DEFAULT 'u' COMMENT 'unconfirmed, confirmed, rejected',
  `vot` enum('y','n','u') NOT NULL DEFAULT 'u' COMMENT 'Zum Voting zugelassen',
  `typ` enum('p','f','l','bp') NOT NULL COMMENT 'Problemanzeige, Forderung, Lösungsvorschlag, Best Practice',
  `spprts` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'how many pressed support-haken',
  `votes` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'Votes received',
  `place` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Place (rank)',
  `rel_tid` varchar(200) NOT NULL DEFAULT '' COMMENT 'Related tids',
  `tg_nrs` varchar(55) NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags',
  `notiz` varchar(300) NOT NULL DEFAULT '' COMMENT 'Notes for internal use',
  `confirmation_key` varchar(40) DEFAULT NULL,
  `input_discussion_contrib` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`tid`) USING BTREE,
  KEY `inpt_uid_ibfk` (`uid`),
  KEY `input_discussion_contrib_fkey` (`input_discussion_contrib`),
  KEY `inpt_qi_fkey` (`qi`),
  KEY `inpt_dirs_fkey` (`dir`),
  CONSTRAINT `inpt_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  CONSTRAINT `inpt_dirs_fkey` FOREIGN KEY (`dir`) REFERENCES `dirs` (`id`),
  CONSTRAINT `inpt_qi_fkey` FOREIGN KEY (`qi`) REFERENCES `quests` (`qi`),
  CONSTRAINT `inpt_uid_ibfk` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  CONSTRAINT `input_discussion_contrib_fkey` FOREIGN KEY (`input_discussion_contrib`) REFERENCES `input_discussion` (`id`)
) COMMENT='User input to questions';


CREATE TABLE `inpt_tgs` (
  `tg_nr` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`tg_nr`,`tid`) USING BTREE,
  KEY `inpt_tgs_tid_ibfk` (`tid`),
  CONSTRAINT `inpt_tgs_tid_ibfk` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`)
);


CREATE TABLE `input_discussion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `input_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `is_user_confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `body` text,
  `video_id` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `input_discussion_time_created_idx` (`time_created`),
  KEY `input_discussion_is_visible_idx` (`is_visible`),
  KEY `input_discussion_input_id_fkey` (`input_id`),
  KEY `input_discussion_user_id_fkey` (`user_id`),
  CONSTRAINT `input_discussion_input_id_fkey` FOREIGN KEY (`input_id`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `input_discussion_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`)
);


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
);


CREATE TABLE `notification_parameter` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `value` text,
  PRIMARY KEY (`notification_id`,`name`),
  CONSTRAINT `notification_parameter_notification_id_ibfk` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`)
);


CREATE TABLE `notification_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_type_name_idx` (`name`)
);

INSERT INTO `notification_type` (`name`) VALUES
('input_created'),
('input_discussion_contribution_created'),
('follow_up_created');

CREATE TABLE `parameter` (
  `name` varchar(191) NOT NULL,
  `proj` char(2) NOT NULL DEFAULT '',
  `value` text,
  PRIMARY KEY (`name`,`proj`),
  KEY `parameter_proj_ibfk` (`proj`),
  CONSTRAINT `parameter_proj_ibfk` FOREIGN KEY (`proj`) REFERENCES `proj` (`proj`)
);


CREATE TABLE `proj` (
  `proj` char(2) NOT NULL,
  `titl_short` varchar(80) NOT NULL DEFAULT '' COMMENT 'Short title/name for project',
  `vot_q` varchar(200) NOT NULL DEFAULT '' COMMENT 'Question used for voting',
  PRIMARY KEY (`proj`)
) COMMENT='All projects active in this installation';


CREATE TABLE `quests` (
  `qi` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(10) unsigned NOT NULL,
  `nr` char(4) NOT NULL DEFAULT '' COMMENT 'Number shown in ordered list',
  `q` varchar(300) NOT NULL DEFAULT '' COMMENT 'The question itself',
  `q_xpl` text NOT NULL COMMENT 'Explanation for question',
  `ln` char(2) NOT NULL DEFAULT '' COMMENT 'Language',
  `vot_q` varchar(220) NOT NULL DEFAULT '' COMMENT 'Introducing voting question',
  `time_modified` timestamp NULL,
  UNIQUE KEY `qi` (`qi`) USING BTREE,
  KEY `qi_2` (`qi`) USING BTREE,
  KEY `quests_kid_fkey` (`kid`),
  CONSTRAINT `quests_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
) COMMENT='Questions for the consultations';


CREATE TABLE `tgs` (
  `tg_nr` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tag number',
  `tg_de` varchar(40) NOT NULL DEFAULT '' COMMENT 'German translation of tag',
  PRIMARY KEY (`tg_nr`) USING BTREE
);

INSERT INTO `tgs` (`tg_de`) VALUES
('Bund'),
('Länder'),
('europäische Ebene'),
('regional'),
('Jugendverbände'),
('altersgemäß'),
('Aktivitäten'),
('aktiv werden'),
('auf allen Ebenen'),
('Beteiligungsangebote schaffen'),
('Bildung'),
('direkter Bezug'),
('contra Wahlaltersenkung'),
('Dialog'),
('Distanz'),
('Einfluss nehmen können'),
('Jugendliche ernst nehmen'),
('Europa'),
('Familie'),
('greifbar'),
('Glaubwürdigkeit'),
('Image'),
('Information'),
('Integration'),
('jugendfreundliche Ansprache'),
('Jugendinteressen berücksichtigen'),
('Wahlkampagnen'),
('Kompetenz'),
('Kommune'),
('Bundesland'),
('Medien'),
('Methoden'),
('Meinung bilden'),
('Parteien'),
('passives Wahlrecht'),
('politische Bildung'),
('Politiker_innen'),
('Wahlprogramme'),
('Kontakt, persönlicher'),
('pro Wahlaltersenkung'),
('Rahmenbedingungen'),
('Schule'),
('Transparenz'),
('Vorbilder'),
('Vorbereitung'),
('Wählen unter 14'),
('Wählen ab 14'),
('Wählen ab 16'),
('Wählen ab 18'),
('Wahlakt'),
('Wählen ist nicht alles'),
('Internet/soziale Netzwerke'),
('Zugang'),
('Ziele'),
('Klarheit'),
('Jugendbeteiligung verankern'),
('Demokratie'),
('Demokratie, direkte'),
('Engagement'),
('ePartizipation'),
('Gesetz'),
('Jugendinitiativen'),
('Motivation'),
('neue Beteiligungsformen'),
('Praxisbeispiel'),
('Reife'),
('UN-Kinderrechte'),
('Verbände'),
('Wählen ab 15'),
('Abiturient_innen'),
('Anerkennung'),
('Auszubildende'),
('benachteiligte Jugendliche'),
('Beratung'),
('Bewerbung'),
('Chancen/Potenziale'),
('Erfahrung'),
('Förderung'),
('Freiwilligendienst'),
('Freiwilligenprogramme'),
('ehrenamtliches Engagement'),
('Hauptschüler_innen'),
('Hindernisse'),
('Interesse'),
('Intransparenz'),
('junge Berufstätige'),
('Kosten'),
('Realschüler_innen'),
('Sprache'),
('Studium'),
('Unsicherheit'),
('Unübersichtlichkeit'),
('Vielfalt an Möglichkeiten'),
('wenig bekannt'),
('Zertifikat'),
('Selbstvertrauen'),
('Teamfähigkeit'),
('Organisationsfähigkeit'),
('Partizipation'),
('Kommunikation'),
('kritisches Denken'),
('Toleranz'),
('Verantwortung'),
('Wertschätzung'),
('Konflikte lösen können'),
('individuelle Fähigkeiten'),
('demokratische Strukturen'),
('Einfühlungsvermögen'),
('Selbstständigkeit'),
('Gleichberechtigung'),
('eigene Projekte umsetzen'),
('soziales Engagement'),
('Alltagskompetenz'),
('Zukunft, berufliche'),
('soziale Kompetenz'),
('Aushandlungsprozesse'),
('Kreativität'),
('Bildung, außerschulische'),
('sich ausprobieren'),
('Lösungsansätze finden'),
('Meinung vertreten'),
('dazu gehören'),
('Rechte'),
('Gemeinschaft'),
('Problemmanagement'),
('fachspezifische Kenntnisse'),
('Leitungsfunktion'),
('Pädagogik'),
('sich einsetzen'),
('Nachhaltigkeit'),
('Offenheit'),
('Flexibilität'),
('Gesellschaft'),
('Selbstreflexion'),
('Unterstützung, finanzielle'),
('Sonderurlaub'),
('ideelle Unterstützung'),
('Leistung'),
('Unternehmen'),
('geringe Wertschätzung'),
('Investition'),
('Auszeichnung'),
('hohe Wertschätzung'),
('Unterstützung'),
('Einstellungskriterium'),
('erschwerte Durchführung'),
('Praxis'),
('Akzeptanz'),
('Dankbarkeit'),
('Freizeit'),
('Bewertung, quantitative'),
('Bewertung, qualitative'),
('Bildungsurlaub'),
('Aufwand'),
('persönlich/individuell'),
('wissenschaftliche Studien'),
('Öffentlichkeit(sarbeit)'),
('Personal'),
('Dokumentation/Nachweis'),
('Standard(s)'),
('einheitlich'),
('Aktion/Kampagne'),
('Vergünstigungen'),
('Vernetzung'),
('Vorteile'),
('Ausstattung'),
('Mitbestimmung'),
('weniger Bürokratie'),
('freier Nachmittag'),
('Leiterausbildung'),
('Vorurteile'),
('Lobby'),
('Freiräume'),
('Prozess'),
('Freiwilligkeit'),
('Lernen'),
('unterschiedliche Relevanz'),
('Spaß'),
('Politik'),
('Zeit(management)'),
('Freunde'),
('Grenzen erfahren'),
('Zuverlässigkeit'),
('Natur'),
('Fairness'),
('steigende Anerkennung'),
('Gleichstellung'),
('Arbeitgeber_innen'),
('junge Menschen'),
('erwünscht/vorausgesetzt'),
('Wertschätzung, mittlere'),
('keine Wertschätzung'),
('fehlendes Bewusstsein'),
('Anerkennung, langsame'),
('unterschätzt'),
('Alter'),
('visuelle  Ergebnisse'),
('Bonus'),
('Sichtbarkeit'),
('Jugendpolitik'),
('bundesweit'),
('Fort-/Weiterbildung'),
('gemeinsame Konzepte/Transfer'),
('Gruppe'),
('formale Bildung'),
('Zeugnis'),
('Ausbildung'),
('Freistellung'),
('Jugendarbeit/Jugendhilfe'),
('Wirtschaft'),
('Jugendleiter_innen'),
('Verwaltung'),
('Identität'),
('Eltern'),
('kulturelle Unterschiede'),
('Kultur'),
('bewusster Umgang'),
('interkulturelles Bewusstsein'),
('Inklusion'),
('Diskriminierung'),
('Gleichbehandlung'),
('Arbeit/Beschäftigung'),
('Wahlrecht'),
('Aufklärung'),
('Projekte'),
('Kooperation'),
('Religion'),
('Quote'),
('Pflicht'),
('Staatsbürgerschaft'),
('Qualifikation'),
('Wohnsituation'),
('Wissen'),
('Rolle'),
('Gremium'),
('Wahlen'),
('MJSO'),
('interkulturelle Öffnung'),
('Begegnung'),
('internationaler Jugendaustausch'),
('Ausgrenzung'),
('Armut'),
('prekäre Lebensbedingungen'),
('Attraktivität'),
('Auseinandersetzungsprozesse'),
('Noten'),
('Medienkompetenz'),
('Bachelor/Master'),
('Verzweckung'),
('Auslandserfahrung'),
('Ängste'),
('Lebensnähe'),
('Behinderung'),
('Lohn/Bezahlung'),
('Gleichaltrige/Peers'),
('Au Pair'),
('Lehrer_innen'),
('Fachkräftemangel'),
('LGBT/Queer'),
('Behinderung, geistige'),
('Behinderung, körperliche'),
('Leistungsdruck/Konkurrenzdenken'),
('finanzielle Mittel'),
('Anderssein'),
('Gewalt'),
('Schüler_innenvertretung'),
('handlungsfähig sein'),
('Pubertät'),
('Arbeitslosigkeit'),
('Migrationshintergrund'),
('Musik'),
('Bildungsgrad'),
('Klima/Atmosphäre'),
('offene Jugendarbeit'),
('sich wohlfühlen können'),
('Generationenunterschiede'),
('Mobbing'),
('Erziehung'),
('Mobilität'),
('Mehrfachbenachteiligung/-diskriminierung'),
('Bildung, frühkindliche'),
('Übergänge zwischen Lebensphasen'),
('demografischer Wandel'),
('Nähe (räumliche)'),
('thematisieren'),
('Flüchtlinge'),
('Schulabbrecher'),
('nicht bekannt'),
('Macht'),
('Schulleitung'),
('Selbstbestimmung'),
('Schulsystem'),
('Zukunft'),
('Ferien'),
('Politikverdrossenheit'),
('Stimmrecht'),
('Schwächen'),
('Stärken'),
('Empowerment'),
('Vielfalt'),
('Selbstverständlichkeit'),
('Miteinander'),
('Tabu'),
('Mittlerrolle'),
('formale Bildung'),
('Mangel'),
('Selbstbewusstsein'),
('ländlicher Raum'),
('Jugendhilfeausschuss'),
('Gewerkschaft'),
('Selbstverpflichtung'),
('urbaner Raum'),
('Ehrlichkeit'),
('Vertrauen'),
('Zielgruppe'),
('Angebote'),
('Sicherheit'),
('Rente'),
('Praktikum'),
('Gewerkschaft'),
('Austausch');


CREATE TABLE `urlkey_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `urlkey` varchar(40) NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `time_visited` timestamp NULL DEFAULT NULL,
  `time_valid_to` timestamp NULL DEFAULT NULL,
  `handler_class` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urlkey_action_urlkey_idx` (`urlkey`)
);


CREATE TABLE `urlkey_action_parameter` (
  `urlkey_action_id` int(10) unsigned NOT NULL,
  `name` varchar(191) NOT NULL,
  `value` text,
  PRIMARY KEY (`urlkey_action_id`,`name`),
  CONSTRAINT `urlkey_action_parameter_urlkey_action_id_ibfk` FOREIGN KEY (`urlkey_action_id`) REFERENCES `urlkey_action` (`id`)
);


CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `block` enum('b','u','c') NOT NULL DEFAULT 'u' COMMENT 'blocked. unknown, user-confirmed',
  `last_act` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'feststellen der letzten aktivität',
  `name` varchar(80) DEFAULT NULL,
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT 'Mail Address',
  `password` varchar(150) DEFAULT NULL,
  `newsl_subscr` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter',
  `cmnt` varchar(400) NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `lvl` enum('usr','adm','edt') NOT NULL DEFAULT 'usr' COMMENT 'User, Editor or Admin',
  `source` set('d','g','p','m') DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) DEFAULT NULL COMMENT 'Explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) DEFAULT NULL COMMENT 'Name of group',
  `name_pers` varchar(80) DEFAULT NULL COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) DEFAULT NULL,
  `cnslt_results` enum('y','n') DEFAULT NULL COMMENT 'Receives results of consultations',
  PRIMARY KEY (`uid`) USING BTREE,
  UNIQUE KEY `users_email_idx` (`email`)
);


CREATE TABLE `user_info` (
  `user_info_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `kid` int(10) unsigned NOT NULL,
  `cmnt` varchar(400) NOT NULL DEFAULT '' COMMENT 'Internal comments for admins',
  `source` set('d','g','p','m') DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper',
  `src_misc` varchar(300) DEFAULT '' COMMENT 'explanation of misc source',
  `group_size` tinyint(3) unsigned DEFAULT NULL COMMENT '1,10,30,80,150,over',
  `name_group` varchar(80) DEFAULT '' COMMENT 'Name of group',
  `name_pers` varchar(80) DEFAULT '' COMMENT 'Name of contact person',
  `age_group` enum('1','2','3','4','5') NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo',
  `regio_pax` varchar(200) DEFAULT '' COMMENT 'Bundesländer',
  `cnslt_results` enum('y','n') NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations',
  `date_added` datetime NULL,
  `cmnt_ext` varchar(600) NOT NULL DEFAULT '',
  `time_user_confirmed` timestamp NULL DEFAULT NULL,
  `confirmation_key` varchar(40) DEFAULT NULL,
  `name` varchar(80) DEFAULT NULL,
  `newsl_subscr` enum('y','n') DEFAULT NULL COMMENT 'Subscription of newsletter',
  PRIMARY KEY (`user_info_id`) USING BTREE,
  KEY `user_info_uid_ibfk` (`uid`),
  KEY `user_info_kid_fkey` (`kid`),
  CONSTRAINT `user_info_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `user_info_uid_ibfk` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
);


CREATE TABLE `vt_final` (
  `kid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL COMMENT 'related to which tid',
  `uid` int(10) unsigned NOT NULL,
  `place` smallint(5) unsigned NOT NULL,
  `points` float NOT NULL COMMENT 'summary points (accumulated value)',
  `cast` int(11) NOT NULL COMMENT 'summary votes (accumulated value)',
  `rank` float NOT NULL COMMENT 'divident points/cast',
  `fowups` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'follow-up exists?',
  `id` varchar(191) NOT NULL COMMENT 'md5 (tid''.-.''uid)',
  PRIMARY KEY (`id`),
  KEY `vt_final_uid_fkey` (`uid`),
  KEY `vt_final_tid_fkey` (`tid`),
  KEY `vt_final_kid_fkey` (`kid`),
  CONSTRAINT `vt_final_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `vt_final_tid_fkey` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `vt_final_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) COMMENT='All votes cast';


CREATE TABLE `vt_grps` (
  `uid` int(10) unsigned NOT NULL,
  `sub_user` varchar(60) NOT NULL DEFAULT '' COMMENT 'email address',
  `sub_uid` char(32) NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid',
  `kid` int(10) unsigned NOT NULL,
  `member` enum('y','n','u') NOT NULL DEFAULT 'u' COMMENT 'y= confirmed by group',
  `vt_inp_list` text NOT NULL COMMENT 'list of votable tids',
  `vt_rel_qid` text NOT NULL COMMENT 'list of rel QIDs',
  `vt_tg_list` text NOT NULL COMMENT 'list of all (still) available tags for this user',
  PRIMARY KEY (`uid`,`sub_uid`,`kid`) USING BTREE,
  KEY `vt_grps_kid_fkey` (`kid`),
  CONSTRAINT `vt_grps_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`),
  CONSTRAINT `vt_grps_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
);


CREATE TABLE `vt_indiv` (
  `uid` int(10) unsigned NOT NULL,
  `sub_uid` char(32) NOT NULL DEFAULT '' COMMENT 'individual subuser',
  `tid` int(10) unsigned NOT NULL COMMENT 'voted on which TID',
  `pts` tinyint(4) NOT NULL COMMENT 'the vote itself (points)',
  `pimp` enum('y','n') NOT NULL DEFAULT 'n',
  `status` enum('v','s','c') NOT NULL COMMENT 'v=voted, s=skipped(vorerst), c=confirmed von sub_uid',
  `upd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when vote was cast',
  UNIQUE KEY `sub_uid` (`sub_uid`,`tid`) USING BTREE,
  UNIQUE KEY `Stimmenzählung` (`uid`,`tid`,`sub_uid`) USING BTREE,
  KEY `vt_indiv_tid_fkey` (`tid`),
  CONSTRAINT `vt_indiv_tid_fkey` FOREIGN KEY (`tid`) REFERENCES `inpt` (`tid`),
  CONSTRAINT `vt_indiv_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
);


CREATE TABLE `vt_rights` (
  `kid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `vt_weight` smallint(5) unsigned NOT NULL COMMENT 'Voting weight of this group',
  `vt_code` char(8) NOT NULL DEFAULT '' COMMENT 'Voting access code for this group',
  `grp_siz` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Group size that we recognise',
  PRIMARY KEY (`kid`,`uid`) USING BTREE,
  KEY `vt_rights_uid_fkey` (`uid`),
  CONSTRAINT `vt_rights_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`),
  CONSTRAINT `vt_rights_uid_fkey` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) COMMENT='Voting rights at certain consultation';


CREATE TABLE `vt_settings` (
  `kid` int(10) unsigned NOT NULL,
  `btn_important` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Super preference button on/off',
  `btn_important_label` varchar(50) NOT NULL DEFAULT 'Ist mir besonders wichtig' COMMENT 'Label for the super preference button',
  `btn_important_max` tinyint(4) NOT NULL DEFAULT '6' COMMENT 'Max amount of items for the super preference button',
  `btn_important_factor` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'Multiplier for votes in super button',
  `btn_numbers` enum('0','1','2','3','4') NOT NULL DEFAULT '3' COMMENT 'number of voting buttons',
  `btn_labels` varchar(255) NOT NULL DEFAULT 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig' COMMENT 'labels of voting buttons, comma-separated',
  PRIMARY KEY (`kid`),
  CONSTRAINT `vt_settings_kid_fkey` FOREIGN KEY (`kid`) REFERENCES `cnslt` (`kid`)
);

SET foreign_key_checks = 1;
-- Up until Migration 2016-04-15_15-00_DBJR-633.sql

-- Migration 2016-05-11_16-03_DBJR-614.sql
ALTER TABLE `users` ADD `nick` varchar(255) NULL DEFAULT NULL;

-- Migration 2016-05-17_14-28_DBJR-608.sql
ALTER TABLE `proj`
ADD `video_facebook_enabled` tinyint(1) NOT NULL DEFAULT '0',
ADD `video_youtube_enabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `video_facebook_enabled`,
ADD `video_vimeo_enabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `video_youtube_enabled`;

ALTER TABLE `quests`
ADD `video_enabled` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `inpt`
ADD `video_service` varchar(191) NULL,
ADD `video_id` varchar(255) NULL AFTER `video_service`;

-- Migration 2016-05-19-12-27_DBJR-609.sql
ALTER TABLE `cnslt`
ADD `discussion_video_enabled` tinyint(1) NOT NULL DEFAULT '1';

-- Migration 2016-05-19_14-18_DBJR-610.sql
ALTER TABLE `input_discussion`
ADD `video_service` varchar(191) NULL AFTER `body`;

-- Migration 2016-05-23_17-52_DBJR-618.sql
ALTER TABLE `help_text`
ADD `module` varchar(191) NOT NULL DEFAULT 'default';

ALTER TABLE `help_text`
ADD UNIQUE `help_text_project_code_name_key` (`project_code`, `name`);

CREATE TABLE `help_text_module` (`name` varchar(191) NOT NULL );
ALTER TABLE `help_text_module` ADD PRIMARY KEY `name` (`name`);

INSERT INTO `help_text_module` (`name`) VALUES ('admin');
INSERT INTO `help_text_module` (`name`) VALUES ('default');

ALTER TABLE `help_text`
ADD FOREIGN KEY (`module`) REFERENCES `help_text_module` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 2016-05-25_18-04_DBJR-626.sql
CREATE TABLE `theme` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(191) NOT NULL,
    `color_headings` varchar(255) NOT NULL,
    `color_frame_background` varchar(255) NOT NULL,
    `color_active_link` varchar(255) NOT NULL
);

ALTER TABLE `theme`
ADD UNIQUE `name` (`name`);

ALTER TABLE `proj`
ADD `theme_id` int NULL,
ADD `color_headings` varchar(255) NULL AFTER `theme_id`,
ADD `color_frame_background` varchar(255) NULL AFTER `color_headings`,
ADD `color_active_link` varchar(255) NULL AFTER `color_frame_background`,
ADD `logo` varchar(255) NULL AFTER `color_active_link`,
ADD `favicon` varchar(255) NULL AFTER `logo`;

ALTER TABLE `proj`
ADD INDEX `proj_theme_id_fk` (`theme_id`);

ALTER TABLE `proj`
ADD FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 2016-05-30_15-41_DBJR-666.sql
ALTER TABLE `proj`
ADD `mitmachen_bubble` tinyint NOT NULL DEFAULT '1';

-- Migration 2016-05-31_14-14_DBJR-647.sql
ALTER TABLE `inpt`
ADD `reminders_sent` int NOT NULL DEFAULT '0';

-- Migration 2016-06-15_13-23_DBJR-770.sql
ALTER TABLE `vt_indiv`
ADD `confirmation_hash` char(32) NULL;

-- Migration 2016-06-17_16-03_DBJR-626.sql
INSERT INTO `theme` (`name`, `color_headings`, `color_frame_background`, `color_active_link`)
VALUES ('Green', 'fc9026', '5fa4a0', '02afdb');

INSERT INTO `theme` (`name`, `color_headings`, `color_frame_background`, `color_active_link`)
VALUES ('Pink', 'fc9026', '990066', '02afdb');

INSERT INTO `theme` (`name`, `color_headings`, `color_frame_background`, `color_active_link`)
VALUES ('Blue', 'fc9026', '04a5eb', '0074b5');

-- Migration 2016-06-17_16-03_DBJR-626.sql
ALTER TABLE `theme`
CHANGE `color_headings` `color_accent_1` varchar(255) NOT NULL AFTER `name`,
CHANGE `color_frame_background` `color_primary` varchar(255) NOT NULL AFTER `color_accent_1`,
CHANGE `color_active_link` `color_accent_2` varchar(255) NOT NULL AFTER `color_primary`;

ALTER TABLE `proj`
CHANGE `color_headings` `color_accent_1` varchar(255) NULL AFTER `theme_id`,
CHANGE `color_frame_background` `color_primary` varchar(255) NULL AFTER `color_accent_1`,
CHANGE `color_active_link` `color_accent_2` varchar(255) NULL AFTER `color_primary`;

-- Migration 2016-06-27_18-36_DBJR-761.sql
ALTER TABLE `proj`
ADD `locale` varchar(191) NOT NULL DEFAULT 'en_US';

-- Migration 2016-06-27_14-58_DBJR-807.sql
UPDATE `proj` SET `theme_id` = (SELECT `id` FROM `theme` ORDER BY `id` LIMIT 1)
WHERE `theme_id` IS NULL AND color_accent_1 IS NULL AND color_accent_2 IS NULL AND color_primary IS NULL;

-- Migration 2016-07-13_13-47_DBJR-824.sql
ALTER TABLE `proj` ADD `state_label` varchar(255) DEFAULT NULL;

-- Migration 2016-07-13_15-00_DBJR-825.sql
ALTER TABLE `proj`
ADD `field_switch_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_age` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_state` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_comments` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `proj`
ADD `allow_groups` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_contribution_origin` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_individuals_num` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_group_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_contact_person` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `proj`
ADD `field_switch_notification` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `proj`
ADD `field_switch_newsletter` tinyint(1) NOT NULL DEFAULT '1';


ALTER TABLE `proj` CHANGE `state_label` `state_field_label` varchar(255) NULL AFTER `locale`;

-- Migration 2016-07-14_15-07_DBJR-827.sql + Migration 2016-07-15_11-03_DBJR-827.sql + 2016-07-15_16-40_DBJR-827.sql
CREATE TABLE `language` (
  `code` varchar(191) NOT NULL
);

ALTER TABLE `language` ADD PRIMARY KEY `pkey` (`code`);
INSERT INTO `language` (`code`) VALUES ('es_ES'), ('de_DE'), ('en_US'), ('fr_FR'), ('pl_PL'), ('cs_CZ'), ('ru_RU'), ('ar_AE');

CREATE TABLE `license` (
  `number` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  `locale` varchar(191) NOT NULL,
  PRIMARY KEY (`number`, `locale`)
) ENGINE='InnoDB';

INSERT INTO `license` (`number`,`title`,`description`,`text`,`link`,`icon`,`alt`,`locale`)
VALUES
    (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'en_US'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'es_ES'
    ), (
        1,
        'Creative-Commons-Lizenz',
        'Creative Commons 4.0: Namensnennung, nicht kommerziell, keine Bearbeitung',
        'Die Beiträge werden unter einer <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.de\" target=\"_blank\" title=\"Mehr über die Creative-Commons-Lizenz erfahren\">Creative-Commons-Lizenz</a> veröffentlicht. Das bedeutet, dass eure Beiträge in Zusammenfassungen und Publikationen zu nicht-kommerziellen Zwecken weiterverwendet werden dürfen.          "Da alle Beiträge hier anonym veröffentlicht werden, wird auch bei Weiterverwendung als Quelle nur diese Website genannt werden.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.de',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'de_DE'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'fr_FR'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'pl_PL'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'cs_CZ'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'ar_AE'
    ), (
        1,
        'Creative Commons license',
        'Creative Commons license 4.0: attribution, non-commercial',
        'The contributions are published under a <a href=\"http://creativecommons.org/licenses/by-nc/4.0/deed.en\" target=\"_blank\" title=\"More about Creative Commons license\">Creative Commons license</a>. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.',
        'http://creativecommons.org/licenses/by-nc/4.0/deed.en',
        'license_cc.svg',
        'CC-BY-NC 4.0',
        'ru_RU'
    );

ALTER TABLE `license` ADD INDEX `language_code_fkey` (`locale`);
ALTER TABLE `license` ADD FOREIGN KEY (`locale`) REFERENCES `language` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `proj` ADD `license` int NOT NULL;
ALTER TABLE `proj` ADD INDEX `proj_license_fkey` (`license`);
UPDATE `proj` SET `license` = (SELECT number FROM `license` WHERE title = 'Creative commons license');
ALTER TABLE `proj` ADD FOREIGN KEY (`license`) REFERENCES `license` (`number`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `proj` ADD INDEX `language_code_fkey` (`locale`);
ALTER TABLE `proj` CHANGE `locale` `locale` varchar(191) NOT NULL;
ALTER TABLE `proj` ADD FOREIGN KEY (`locale`) REFERENCES `language` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;


-- Migration 2016-07-19_10-41_DBJR-850.sql
ALTER TABLE `proj` ADD `contribution_confirmation_info` text NOT NULL;

-- Migration 2016-07-29_13-02_DBJR-873.sql
ALTER TABLE `proj`
DROP `field_switch_name`,
DROP `field_switch_age`,
DROP `field_switch_state`,
DROP `field_switch_comments`,
DROP `field_switch_contribution_origin`,
DROP `field_switch_individuals_num`,
DROP `field_switch_group_name`,
DROP `field_switch_contact_person`,
DROP `field_switch_notification`,
DROP `field_switch_newsletter`,
DROP `allow_groups`;

ALTER TABLE `cnslt`
ADD `field_switch_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_age` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_state` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_comments` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `cnslt`
ADD `allow_groups` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_contribution_origin` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_individuals_sum` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_group_name` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_contact_person` tinyint(1) NOT NULL DEFAULT '1';

ALTER TABLE `cnslt`
ADD `field_switch_notification` tinyint(1) NOT NULL DEFAULT '1';
ALTER TABLE `cnslt`
ADD `field_switch_newsletter` tinyint(1) NOT NULL DEFAULT '1';


-- Migration 2016-08-03_14-45_DBJR-885.sql
ALTER TABLE `proj` DROP COLUMN `contribution_confirmation_info`;
ALTER TABLE `proj` DROP COLUMN `state_field_label`;

ALTER TABLE `cnslt` ADD COLUMN `state_field_label` varchar(255) DEFAULT NULL;
ALTER TABLE `cnslt` ADD COLUMN `contribution_confirmation_info` text NOT NULL;

-- Migration 2016-08-24_18-35_DBJR-902.sql
ALTER TABLE `vt_indiv`
CHANGE `pts` `pts` tinyint(4) NULL COMMENT 'the vote itself (points)' AFTER `tid`;

-- 2016-09-15_13-31_DBJR-918.sql
ALTER TABLE `cnslt` ADD `license_agreement` text NULL DEFAULT NULL;

-- Migration 2016-09-05_14-44_DBJR-911.sql
ALTER TABLE `inpt` DROP FOREIGN KEY `inpt_uid_ibfk`;

-- Migration 2016-09-21_12-41_DBJR-955.sql
ALTER TABLE `quests` CHANGE `nr` `nr` varchar(4) NULL DEFAULT NULL COMMENT 'Number shown in ordered list' AFTER `kid`;

-- Migration 2016-06-23_15-34_DBJR-770.sql
ALTER TABLE `vt_grps`
ADD INDEX `vt_grps_sub_uid_fkey` (`sub_uid`);

ALTER TABLE `vt_indiv`
ADD FOREIGN KEY (`sub_uid`) REFERENCES `vt_grps` (`sub_uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 2016-08-30_09-36_DBJR-889.sql
CREATE TABLE `contributor_age` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int NOT NULL,
  `to` int NULL,
  FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE='InnoDB';

CREATE TABLE `group_size` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int NOT NULL,
  `to` int NULL,
  FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE='InnoDB';

ALTER TABLE `cnslt` ADD `groups_no_information` tinyint(1) NOT NULL DEFAULT '1';

INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '1', '2' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '3', '10' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '11', '30' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '31', '80' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '81', '150' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`) (SELECT `kid`, '151' FROM `cnslt`);

ALTER TABLE `vt_rights`
CHANGE `grp_siz` `grp_siz` int(11) NULL COMMENT 'Group size that we recognise' AFTER `vt_code`;

UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 1 AND `to` = 2) WHERE grp_siz = 1;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 3 AND `to` = 10) WHERE grp_siz = 10;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 11 AND `to` = 30) WHERE grp_siz = 30;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 31 AND `to` = 80) WHERE grp_siz = 80;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 81 AND `to` = 150) WHERE grp_siz = 150;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 151 AND `to` IS NULL) WHERE grp_siz = 200;

UPDATE `vt_rights` SET grp_siz = NULL WHERE grp_siz = 0;

ALTER TABLE `vt_rights`
ADD FOREIGN KEY (`grp_siz`) REFERENCES `group_size` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `user_info` CHANGE `group_size` `group_size` int NULL AFTER `src_misc`;

UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` = 2) WHERE group_size = 1;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 3 AND `to` = 10) WHERE group_size = 10;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 11 AND `to` = 30) WHERE group_size = 30;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 31 AND `to` = 80) WHERE group_size = 80;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 81 AND `to` = 150) WHERE group_size = 150;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 151 AND `to` IS NULL) WHERE group_size = 200;

ALTER TABLE `user_info`
ADD FOREIGN KEY (`group_size`) REFERENCES `group_size` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


ALTER TABLE `user_info`
CHANGE `age_group` `age_group` int NULL AFTER `name_pers`;

INSERT INTO `contributor_age` (`consultation_id`, `from`) (SELECT `kid`, '1' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`, `to`) (SELECT `kid`, '1', '17' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`, `to`) (SELECT `kid`, '18', '26' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`) (SELECT `kid`, '27' FROM `cnslt`);

UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` IS NULL) WHERE age_group = 4;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` = 17) WHERE age_group = 1;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 18 AND `to` = 26) WHERE age_group = 2;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 27 AND `to` IS NULL) WHERE age_group = 3;
UPDATE `user_info` SET age_group = NULL WHERE age_group = 5;

ALTER TABLE `user_info`
ADD FOREIGN KEY (`age_group`) REFERENCES `contributor_age` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 20161012114615_dbjr936.php
CREATE TABLE `input_relations` (`parent_id` int(10) unsigned NOT NULL, `child_id` int(10) unsigned NOT NULL)
ENGINE='InnoDB';
ALTER TABLE `input_relations` ADD PRIMARY KEY `pkey` (`parent_id`, `child_id`);
ALTER TABLE `input_relations` ADD FOREIGN KEY (`parent_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE
CASCADE;
ALTER TABLE `input_relations` ADD FOREIGN KEY (`child_id`) REFERENCES `inpt` (`tid`) ON DELETE CASCADE ON UPDATE
CASCADE;
ALTER TABLE `inpt` DROP `rel_tid`;

-- Migration 20161127190639_dbjr1029.php
ALTER TABLE `user_info` ADD `invitation_sent_date` datetime NULL DEFAULT NULL;

-- Migration 20161127161259_dbjr1028.php
ALTER TABLE `vt_settings` ADD `btn_no_opinion` boolean NOT NULL DEFAULT true AFTER `btn_important`;

-- Migration 20170109174207_dbjr1065.php
ALTER TABLE `users` CHANGE `age_group` `age_group_from` int(11) NULL AFTER `name_pers`,
ADD `age_group_to` int(11) NULL AFTER `age_group_from`;

-- Migration 20170116123840_dbjr1072.php
ALTER TABLE `cnslt` CHANGE `img_file` `img_file` text NULL AFTER `titl_sub`;

-- Migration 2016-05-24_10-57_DBJR-608.sql
CREATE TABLE `video_service` (`name` varchar(191) NOT NULL );
ALTER TABLE `video_service` ADD PRIMARY KEY `name` (`name`);
INSERT INTO `video_service` (`name`) VALUES ('vimeo');
INSERT INTO `video_service` (`name`) VALUES ('youtube');
INSERT INTO `video_service` (`name`) VALUES ('facebook');

ALTER TABLE `inpt`
ADD INDEX `inpt_video_service_fkey` (`video_service`);

ALTER TABLE `input_discussion`
ADD INDEX `input_discussion_video_service_fkey` (`video_service`);

ALTER TABLE `input_discussion`
ADD FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `inpt`
ADD FOREIGN KEY (`video_service`) REFERENCES `video_service` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 20170127155643_dbjr972.php
SET foreign_key_checks = 0;
ALTER TABLE `articles` CHANGE `proj` `proj` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CHANGE `desc` `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CHANGE `hid` `hid` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CHANGE `ref_nm` `ref_nm` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CHANGE `artcl` `artcl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CHANGE `sidebar` `sidebar` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `articles_refnm` CHANGE `ref_nm` `ref_nm` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles_refnm` CHANGE `lng` `lng` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles_refnm` CHANGE `desc` `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles_refnm` CHANGE `type` `type` enum('g','b') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles_refnm` CHANGE `scope` `scope` enum('none','info','voting','followup','static') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `articles_refnm` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `cnslt` CHANGE `proj` `proj` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `inp_show` `inp_show` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `spprt_show` `spprt_show` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `vot_show` `vot_show` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `vot_expl` `vot_expl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `vot_res_show` `vot_res_show` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `follup_show` `follup_show` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `titl` `titl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `titl_short` `titl_short` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `titl_sub` `titl_sub` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `img_file` `img_file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `img_expl` `img_expl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `expl_short` `expl_short` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `ln` `ln` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `public` `public` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `vt_finalized` `vt_finalized` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `vt_anonymized` `vt_anonymized` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `phase_info` `phase_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `phase_support` `phase_support` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `phase_input` `phase_input` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `phase_voting` `phase_voting` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `phase_followup` `phase_followup` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `follow_up_explanation` `follow_up_explanation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `state_field_label` `state_field_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `contribution_confirmation_info` `contribution_confirmation_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CHANGE `license_agreement` `license_agreement` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `cnslt` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `contributor_age` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `dirs` CHANGE `dir_name` `dir_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `dirs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email` CHANGE `project_code` `project_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email` CHANGE `sent_by_user` `sent_by_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email` CHANGE `subject` `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email` CHANGE `body_html` `body_html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email` CHANGE `body_text` `body_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_attachment` CHANGE `filepath` `filepath` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_component` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_component` CHANGE `project_code` `project_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_component` CHANGE `body_html` `body_html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_component` CHANGE `body_text` `body_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_component` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_component` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_placeholder` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_placeholder` CHANGE `description` `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_placeholder` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_recipient` CHANGE `type` `type` enum('to','cc','bcc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_recipient` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_recipient` CHANGE `email` `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_recipient` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_template` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template` CHANGE `project_code` `project_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template` CHANGE `subject` `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template` CHANGE `body_html` `body_html` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template` CHANGE `body_text` `body_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_template_has_email_placeholder` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `email_template_type` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `email_template_type` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `footer` CHANGE `proj` `proj` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `footer` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `footer` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `fowup_fls` CHANGE `titl` `titl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CHANGE `who` `who` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CHANGE `show_no_day` `show_no_day` enum('n','y') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CHANGE `ref_doc` `ref_doc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CHANGE `ref_view` `ref_view` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CHANGE `gfx_who` `gfx_who` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowup_fls` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `fowups` CHANGE `embed` `embed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowups` CHANGE `expl` `expl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowups` CHANGE `typ` `typ` enum('g','s','a','r','e') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowups` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `fowups_rid` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `fowups_supports` CHANGE `tmphash` `tmphash` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `fowups_supports` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `group_size` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `help_text` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `help_text` CHANGE `body` `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `help_text` CHANGE `project_code` `project_code` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `help_text` CHANGE `module` `module` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `help_text` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `help_text_module` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `help_text_module` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `inpt` CHANGE `thes` `thes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `expl` `expl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `block` `block` enum('y','n','u') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `user_conf` `user_conf` enum('u','c','r') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `vot` `vot` enum('y','n','u') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `typ` `typ` enum('p','f','l','bp') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `tg_nrs` `tg_nrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `notiz` `notiz` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `confirmation_key` `confirmation_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `video_service` `video_service` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CHANGE `video_id` `video_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inpt` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `inpt_tgs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `input_discussion` CHANGE `body` `body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `input_discussion` CHANGE `video_service` `video_service` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `input_discussion` CHANGE `video_id` `video_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `input_discussion` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `input_relations` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `language` CHANGE `code` `code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `language` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `license` CHANGE `title` `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `description` `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `text` `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `link` `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `icon` `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `alt` `alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CHANGE `locale` `locale` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `license` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `notification` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `notification_parameter` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `notification_parameter` CHANGE `value` `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `notification_parameter` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `notification_type` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `notification_type` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `parameter` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `parameter` CHANGE `proj` `proj` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `parameter` CHANGE `value` `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `parameter` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `proj` CHANGE `proj` `proj` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `titl_short` `titl_short` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `vot_q` `vot_q` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `color_accent_1` `color_accent_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `color_primary` `color_primary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `color_accent_2` `color_accent_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `logo` `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `favicon` `favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CHANGE `locale` `locale` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `proj` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `quests` CHANGE `nr` `nr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `quests` CHANGE `q` `q` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `quests` CHANGE `q_xpl` `q_xpl` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `quests` CHANGE `ln` `ln` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `quests` CHANGE `vot_q` `vot_q` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `quests` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `tgs` CHANGE `tg_de` `tg_de` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `tgs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `theme` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `theme` CHANGE `color_accent_1` `color_accent_1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `theme` CHANGE `color_primary` `color_primary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `theme` CHANGE `color_accent_2` `color_accent_2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `theme` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `urlkey_action` CHANGE `urlkey` `urlkey` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `urlkey_action` CHANGE `handler_class` `handler_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `urlkey_action` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `urlkey_action_parameter` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `urlkey_action_parameter` CHANGE `value` `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `urlkey_action_parameter` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `user_info` CHANGE `cmnt` `cmnt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `source` `source` set('d','g','p','m') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `src_misc` `src_misc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `name_group` `name_group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `name_pers` `name_pers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `regio_pax` `regio_pax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `cnslt_results` `cnslt_results` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `cmnt_ext` `cmnt_ext` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `confirmation_key` `confirmation_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CHANGE `newsl_subscr` `newsl_subscr` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_info` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `users` CHANGE `block` `block` enum('b','u','c') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `name` `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `email` `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `password` `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `newsl_subscr` `newsl_subscr` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `cmnt` `cmnt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `lvl` `lvl` enum('usr','adm','edt') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `source` `source` set('d','g','p','m') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `src_misc` `src_misc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `name_group` `name_group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `name_pers` `name_pers` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `regio_pax` `regio_pax` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `cnslt_results` `cnslt_results` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CHANGE `nick` `nick` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `video_service` CHANGE `name` `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `video_service` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `vt_final` CHANGE `fowups` `fowups` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_final` CHANGE `id` `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_final` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `vt_grps` CHANGE `sub_user` `sub_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CHANGE `sub_uid` `sub_uid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CHANGE `member` `member` enum('y','n','u') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CHANGE `vt_inp_list` `vt_inp_list` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CHANGE `vt_rel_qid` `vt_rel_qid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CHANGE `vt_tg_list` `vt_tg_list` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_grps` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `vt_indiv` CHANGE `sub_uid` `sub_uid` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_indiv` CHANGE `pimp` `pimp` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_indiv` CHANGE `status` `status` enum('v','s','c') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_indiv` CHANGE `confirmation_hash` `confirmation_hash` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_indiv` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `vt_rights` CHANGE `vt_code` `vt_code` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_rights` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

ALTER TABLE `vt_settings` CHANGE `btn_important` `btn_important` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_settings` CHANGE `btn_important_label` `btn_important_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_settings` CHANGE `btn_numbers` `btn_numbers` enum('0','1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_settings` CHANGE `btn_labels` `btn_labels` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `vt_settings` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET foreign_key_checks = 1;

-- Migration 20170130105209_dbjr1077.php
ALTER TABLE `articles` CHANGE `hid` `is_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `inp_show` `is_input_phase_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `spprt_show` `is_support_phase_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `vot_show` `is_voting_phase_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `vot_res_show` `is_voting_result_phase_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `follup_show` `is_followup_phase_showed` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `public` `is_public` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `vt_finalized` `is_vt_finalized` tinyint(1) NULL;
ALTER TABLE `cnslt` CHANGE `vt_anonymized` `is_vt_anonymized` tinyint(1) NULL;
ALTER TABLE `fowup_fls` CHANGE `show_no_day` `is_only_month_year_showed` tinyint(1) NULL;
ALTER TABLE `inpt` CHANGE `block` `is_confirmed` tinyint(1) NULL;
ALTER TABLE `inpt` CHANGE `user_conf` `is_confirmed_by_user` tinyint(1) NULL;
ALTER TABLE `inpt` CHANGE `vot` `is_votable` tinyint(1) NULL;
ALTER TABLE `user_info` CHANGE `cnslt_results` `is_receiving_consultation_results` tinyint(1) NULL;
ALTER TABLE `user_info` CHANGE `newsl_subscr` `is_subscribed_newsletter` tinyint(1) NULL;
ALTER TABLE `users` CHANGE `block` `is_confirmed` tinyint(1) NULL;
ALTER TABLE `users` CHANGE `newsl_subscr` `is_subscribed_newsletter` tinyint(1) NULL;
ALTER TABLE `users` CHANGE `cnslt_results` `is_receiving_consultation_results` tinyint(1) NULL;
ALTER TABLE `vt_final` CHANGE `fowups` `is_followups` tinyint(1) NULL;
ALTER TABLE `vt_grps` CHANGE `member` `is_member` tinyint(1) NULL;
ALTER TABLE `vt_indiv` CHANGE `pimp` `is_pimp` tinyint(1) NULL;
ALTER TABLE `vt_settings` CHANGE `btn_important` `is_btn_important` tinyint(1) NULL;

-- Migration 20170130083602_dbjr1078.php
CREATE TABLE `article_type` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `article_type` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `article_type` VALUES ('global'), ('consultation');
ALTER TABLE `articles_refnm` CHANGE `type` `type` varchar(191) NULL DEFAULT 'global';
UPDATE `articles_refnm` SET `type` = 'global' WHERE `type` = 'g';
UPDATE `articles_refnm` SET `type` = 'consultation' WHERE `type` = 'b';
UPDATE `articles_refnm` SET `type` = NULL WHERE `type` = '';
ALTER TABLE `articles_refnm` CHANGE `type` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL, ADD FOREIGN KEY (`type`) REFERENCES `article_type` (`name`) ON DELETE RESTRICT;
CREATE TABLE `article_scope` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `article_scope` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `article_scope` VALUES ('none'), ('info'), ('voting'), ('followup'), ('static');
ALTER TABLE `articles_refnm` CHANGE `scope` `scope` varchar(191) NULL DEFAULT 'none';
UPDATE `articles_refnm` SET `scope` = NULL WHERE `scope` = '';
ALTER TABLE `articles_refnm` CHANGE `scope` `scope` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL, ADD FOREIGN KEY (`scope`) REFERENCES `article_scope` (`name`) ON DELETE RESTRICT;
CREATE TABLE `email_recipient_type` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `email_recipient_type` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `email_recipient_type` VALUES ('to'), ('cc'), ('bcc');
ALTER TABLE `email_recipient` CHANGE `type` `type` varchar(191) NULL;
UPDATE `email_recipient` SET `type` = NULL WHERE `type` = '';
ALTER TABLE `email_recipient` CHANGE `type` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL, ADD FOREIGN KEY (`type`) REFERENCES `email_recipient_type` (`name`) ON DELETE RESTRICT;
CREATE TABLE `fowups_type` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `fowups_type` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `fowups_type` VALUES ('general'), ('supporting'), ('action'), ('rejected'), ('end');
ALTER TABLE `fowups` CHANGE `typ` `typ` varchar(191) NULL;
UPDATE `fowups` SET `typ` = 'general' WHERE `typ` = 'g';
UPDATE `fowups` SET `typ` = 'supporting' WHERE `typ` = 's';
UPDATE `fowups` SET `typ` = 'action' WHERE `typ` = 'a';
UPDATE `fowups` SET `typ` = 'rejected' WHERE `typ` = 'r';
UPDATE `fowups` SET `typ` = 'end' WHERE `typ` = 'e';
UPDATE `fowups` SET `typ` = NULL WHERE `typ` = '';
ALTER TABLE `fowups` CHANGE `typ` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'general', ADD FOREIGN KEY (`type`) REFERENCES `fowups_type` (`name`) ON DELETE RESTRICT;
ALTER TABLE `users` CHANGE `lvl` `role` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `cmnt`;
CREATE TABLE `users_role` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `users_role` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `users_role` VALUES ('user'), ('admin'), ('editor');
ALTER TABLE `users` CHANGE `role` `role` varchar(191) NULL;
UPDATE `users` SET `role` = 'user' WHERE `role` = 'usr';
UPDATE `users` SET `role` = 'admin' WHERE `role` = 'adm';
UPDATE `users` SET `role` = 'editor' WHERE `role` = 'edt';
UPDATE `users` SET `role` = NULL WHERE `role` = '';
ALTER TABLE `users` CHANGE `role` `role` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'user', ADD FOREIGN KEY (`role`) REFERENCES `users_role` (`name`) ON DELETE RESTRICT;
CREATE TABLE `contribution_type` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `contribution_type` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `contribution_type` VALUES ('from_discussion'), ('f'), ('l'), ('bp');
ALTER TABLE `inpt` CHANGE `typ` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL;
UPDATE `inpt` SET `type` = 'from_discussion' WHERE `type` = 'p';
UPDATE `inpt` SET `type` = NULL WHERE `type` = '';
ALTER TABLE `inpt` CHANGE `type` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL, ADD FOREIGN KEY (`type`) REFERENCES `contribution_type` (`name`) ON DELETE RESTRICT;
CREATE TABLE `vt_indiv_status` (`name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL) ENGINE='InnoDB';
ALTER TABLE `vt_indiv_status` ADD PRIMARY KEY `pk_name` (`name`);
INSERT INTO `vt_indiv_status` VALUES ('voted'), ('skipped'), ('confirmed');
ALTER TABLE `vt_indiv` CHANGE `status` `status` varchar(191) NULL;
UPDATE `vt_indiv` SET `status` = 'voted' WHERE `status` = 'v';
UPDATE `vt_indiv` SET `status` = 'skipped' WHERE `status` = 's';
UPDATE `vt_indiv` SET `status` = 'confirmed' WHERE `status` = 'c';
UPDATE `vt_indiv` SET `status` = NULL WHERE `status` = '';
ALTER TABLE `vt_indiv` CHANGE `status` `status` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL, ADD FOREIGN KEY (`status`) REFERENCES `vt_indiv_status` (`name`) ON DELETE RESTRICT;
ALTER TABLE `vt_settings` CHANGE `btn_numbers` `btn_numbers` int(11) NULL DEFAULT 3;

-- Migration 20170131143301_dbjr1083.php
ALTER TABLE `articles` CHANGE `artcl` `artcl` text NOT NULL COMMENT 'Article itself';
ALTER TABLE `articles` CHANGE `desc` `desc` varchar(255) NOT NULL DEFAULT '' COMMENT 'Readable descr for admin';
ALTER TABLE `articles` CHANGE `proj` `proj` varchar(255) NOT NULL DEFAULT '' COMMENT 'which project';
ALTER TABLE `articles` CHANGE `ref_nm` `ref_nm` varchar(191) NULL DEFAULT '' COMMENT 'Article reference name';
ALTER TABLE `articles` CHANGE `sidebar` `sidebar` text NULL COMMENT 'Content for sidebar';
ALTER TABLE `articles_refnm` CHANGE `desc` `desc` varchar(255) NOT NULL DEFAULT '' COMMENT 'readable description';
ALTER TABLE `articles_refnm` CHANGE `lng` `lng` char(2) NOT NULL DEFAULT '' COMMENT 'language code';
ALTER TABLE `articles_refnm` CHANGE `ref_nm` `ref_nm` varchar(191) NOT NULL DEFAULT '' COMMENT 'article reference name';
ALTER TABLE `cnslt` CHANGE `contribution_confirmation_info` `contribution_confirmation_info` text NULL COMMENT '';
ALTER TABLE `cnslt` CHANGE `expl_short` `expl_short` text NOT NULL COMMENT '';
ALTER TABLE `cnslt` CHANGE `follow_up_explanation` `follow_up_explanation` text NULL COMMENT '';
ALTER TABLE `cnslt` CHANGE `img_expl` `img_expl` varchar(255) NOT NULL DEFAULT '' COMMENT 'explanatory text for title graphics';
ALTER TABLE `cnslt` CHANGE `img_file` `img_file` text NULL COMMENT '';
ALTER TABLE `cnslt` CHANGE `license_agreement` `license_agreement` text NULL COMMENT '';
ALTER TABLE `cnslt` CHANGE `ln` `ln` char(2) NOT NULL DEFAULT '' COMMENT 'Language';
ALTER TABLE `cnslt` CHANGE `phase_followup` `phase_followup` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `phase_info` `phase_info` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `phase_input` `phase_input` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `phase_support` `phase_support` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `phase_voting` `phase_voting` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `proj` `proj` varchar(255) NOT NULL DEFAULT '' COMMENT 'gehört zu SD oder zur eigst Jugpol';
ALTER TABLE `cnslt` CHANGE `state_field_label` `state_field_label` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `cnslt` CHANGE `titl` `titl` varchar(255) NOT NULL DEFAULT '' COMMENT 'Title of consultation';
ALTER TABLE `cnslt` CHANGE `titl_short` `titl_short` varchar(255) NOT NULL DEFAULT '' COMMENT 'Shortened title (for slider, mails etc.)';
ALTER TABLE `cnslt` CHANGE `titl_sub` `titl_sub` varchar(255) NOT NULL DEFAULT '' COMMENT 'subtitle (optional)';
ALTER TABLE `cnslt` CHANGE `vot_expl` `vot_expl` text NULL COMMENT 'info text for voting start';
ALTER TABLE `dirs` CHANGE `dir_name` `dir_name` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email` CHANGE `body_html` `body_html` text NOT NULL COMMENT '';
ALTER TABLE `email` CHANGE `body_text` `body_text` text NOT NULL COMMENT '';
ALTER TABLE `email` CHANGE `project_code` `project_code` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email` CHANGE `sent_by_user` `sent_by_user` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `email` CHANGE `subject` `subject` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_attachment` CHANGE `filepath` `filepath` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_component` CHANGE `body_html` `body_html` text NOT NULL COMMENT '';
ALTER TABLE `email_component` CHANGE `body_text` `body_text` text NOT NULL COMMENT '';
ALTER TABLE `email_component` CHANGE `description` `description` text NOT NULL COMMENT '';
ALTER TABLE `email_component` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_component` CHANGE `project_code` `project_code` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_placeholder` CHANGE `description` `description` text NULL COMMENT '';
ALTER TABLE `email_placeholder` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_recipient` CHANGE `email` `email` varchar(191) NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_recipient` CHANGE `name` `name` varchar(191) NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_template` CHANGE `body_html` `body_html` text NOT NULL COMMENT '';
ALTER TABLE `email_template` CHANGE `body_text` `body_text` text NOT NULL COMMENT '';
ALTER TABLE `email_template` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_template` CHANGE `project_code` `project_code` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_template` CHANGE `subject` `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `email_template_type` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `footer` CHANGE `proj` `proj` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `footer` CHANGE `text` `text` text NULL COMMENT '';
ALTER TABLE `fowups` CHANGE `embed` `embed` varchar(600) NOT NULL DEFAULT '' COMMENT 'embedding for multimedia';
ALTER TABLE `fowups` CHANGE `expl` `expl` text NOT NULL COMMENT 'Erläuterung';
ALTER TABLE `fowups_supports` CHANGE `tmphash` `tmphash` char(32) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `fowup_fls` CHANGE `gfx_who` `gfx_who` varchar(255) NOT NULL DEFAULT '' COMMENT 'Graphic of who';
ALTER TABLE `fowup_fls` CHANGE `ref_doc` `ref_doc` varchar(255) NOT NULL DEFAULT '' COMMENT 'reference to downloadable document';
ALTER TABLE `fowup_fls` CHANGE `ref_view` `ref_view` varchar(2000) NOT NULL DEFAULT '' COMMENT 'introduction to viewable version of document';
ALTER TABLE `fowup_fls` CHANGE `titl` `titl` varchar(300) NOT NULL DEFAULT '' COMMENT 'Title of follow-up document';
ALTER TABLE `fowup_fls` CHANGE `who` `who` varchar(255) NOT NULL DEFAULT '' COMMENT 'Who gave the follow-up';
ALTER TABLE `help_text` CHANGE `body` `body` text NULL COMMENT '';
ALTER TABLE `help_text` CHANGE `module` `module` varchar(191) NOT NULL DEFAULT 'default' COMMENT '';
ALTER TABLE `help_text` CHANGE `name` `name` varchar(191) NULL DEFAULT '' COMMENT '';
ALTER TABLE `help_text` CHANGE `project_code` `project_code` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `help_text_module` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `inpt` CHANGE `confirmation_key` `confirmation_key` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `inpt` CHANGE `expl` `expl` varchar(2000) NOT NULL DEFAULT '' COMMENT 'Longer explanation';
ALTER TABLE `inpt` CHANGE `notiz` `notiz` varchar(300) NOT NULL DEFAULT '' COMMENT 'Notes for internal use';
ALTER TABLE `inpt` CHANGE `tg_nrs` `tg_nrs` varchar(255) NOT NULL DEFAULT '' COMMENT 'Nummern der Keywords (100-999), max 14 Tags';
ALTER TABLE `inpt` CHANGE `thes` `thes` varchar(330) NOT NULL DEFAULT '' COMMENT 'User reply';
ALTER TABLE `inpt` CHANGE `video_id` `video_id` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `inpt` CHANGE `video_service` `video_service` varchar(191) NULL COMMENT '';
ALTER TABLE `input_discussion` CHANGE `body` `body` text NULL COMMENT '';
ALTER TABLE `input_discussion` CHANGE `video_id` `video_id` varchar(191) NULL DEFAULT '' COMMENT '';
ALTER TABLE `input_discussion` CHANGE `video_service` `video_service` varchar(191) NULL COMMENT '';
ALTER TABLE `language` CHANGE `code` `code` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `alt` `alt` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `description` `description` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `icon` `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `link` `link` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `locale` `locale` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `license` CHANGE `text` `text` text NOT NULL COMMENT '';
ALTER TABLE `license` CHANGE `title` `title` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `notification_parameter` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `notification_parameter` CHANGE `value` `value` text NULL COMMENT '';
ALTER TABLE `notification_type` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `parameter` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `parameter` CHANGE `proj` `proj` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `parameter` CHANGE `value` `value` text NULL COMMENT '';
ALTER TABLE `proj` CHANGE `color_accent_1` `color_accent_1` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `color_accent_2` `color_accent_2` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `color_primary` `color_primary` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `favicon` `favicon` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `locale` `locale` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `logo` `logo` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `proj` `proj` char(2) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `proj` CHANGE `titl_short` `titl_short` varchar(255) NOT NULL DEFAULT '' COMMENT 'Short title/name for project';
ALTER TABLE `proj` CHANGE `vot_q` `vot_q` varchar(200) NOT NULL DEFAULT '' COMMENT 'Question used for voting';
ALTER TABLE `quests` CHANGE `ln` `ln` char(2) NOT NULL DEFAULT '' COMMENT 'Language';
ALTER TABLE `quests` CHANGE `nr` `nr` varchar(4) NULL DEFAULT '' COMMENT 'Number shown in ordered list';
ALTER TABLE `quests` CHANGE `q` `q` varchar(300) NOT NULL DEFAULT '' COMMENT 'The question itself';
ALTER TABLE `quests` CHANGE `q_xpl` `q_xpl` text NOT NULL COMMENT 'Explanation for question';
ALTER TABLE `quests` CHANGE `vot_q` `vot_q` varchar(220) NOT NULL DEFAULT '' COMMENT 'Introducing voting question';
ALTER TABLE `tgs` CHANGE `tg_de` `tg_de` varchar(191) NOT NULL DEFAULT '' COMMENT 'German translation of tag';
ALTER TABLE `theme` CHANGE `color_accent_1` `color_accent_1` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `theme` CHANGE `color_accent_2` `color_accent_2` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `theme` CHANGE `color_primary` `color_primary` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `theme` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `urlkey_action` CHANGE `handler_class` `handler_class` varchar(255) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `urlkey_action` CHANGE `urlkey` `urlkey` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `urlkey_action_parameter` CHANGE `name` `name` varchar(191) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `urlkey_action_parameter` CHANGE `value` `value` text NULL COMMENT '';
ALTER TABLE `users` CHANGE `cmnt` `cmnt` varchar(400) NOT NULL DEFAULT '' COMMENT 'Internal comments for admins';
ALTER TABLE `users` CHANGE `email` `email` varchar(191) NOT NULL DEFAULT '' COMMENT 'Mail Address';
ALTER TABLE `users` CHANGE `name` `name` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `users` CHANGE `name_group` `name_group` varchar(255) NULL DEFAULT '' COMMENT 'Name of group';
ALTER TABLE `users` CHANGE `name_pers` `name_pers` varchar(255) NULL DEFAULT '' COMMENT 'Name of contact person';
ALTER TABLE `users` CHANGE `nick` `nick` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `users` CHANGE `password` `password` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `users` CHANGE `regio_pax` `regio_pax` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `users` CHANGE `source` `source` set('d','g','p','m') NULL DEFAULT '' COMMENT 'Dialogue, Group, Misc, Position paper';
ALTER TABLE `users` CHANGE `src_misc` `src_misc` varchar(300) NULL DEFAULT '' COMMENT 'Explanation of misc source';
ALTER TABLE `user_info` CHANGE `cmnt` `cmnt` varchar(400) NOT NULL DEFAULT '' COMMENT 'Internal comments for admins';
ALTER TABLE `user_info` CHANGE `cmnt_ext` `cmnt_ext` varchar(600) NOT NULL DEFAULT '' COMMENT '';
ALTER TABLE `user_info` CHANGE `confirmation_key` `confirmation_key` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `user_info` CHANGE `name` `name` varchar(255) NULL DEFAULT '' COMMENT '';
ALTER TABLE `user_info` CHANGE `name_group` `name_group` varchar(255) NULL DEFAULT '' COMMENT 'Name of group';
ALTER TABLE `user_info` CHANGE `name_pers` `name_pers` varchar(255) NULL DEFAULT '' COMMENT 'Name of contact person';
ALTER TABLE `user_info` CHANGE `regio_pax` `regio_pax` varchar(255) NULL DEFAULT '' COMMENT 'Bundesländer';
ALTER TABLE `user_info` CHANGE `source` `source` set('d','g','p','m') NULL DEFAULT '' COMMENT 'Dialogue, Group, Misc, Position paper';
ALTER TABLE `user_info` CHANGE `src_misc` `src_misc` varchar(300) NULL DEFAULT '' COMMENT 'explanation of misc source';
ALTER TABLE `video_service` CHANGE `name` `name` varchar(191) NOT NULL COMMENT '';
ALTER TABLE `vt_final` CHANGE `id` `id` varchar(191) NOT NULL DEFAULT '' COMMENT 'md5 (tid\'.-.\'uid)';
ALTER TABLE `vt_grps` CHANGE `sub_uid` `sub_uid` char(32) NOT NULL DEFAULT '' COMMENT 'md5 of mail.kid';
ALTER TABLE `vt_grps` CHANGE `sub_user` `sub_user` varchar(255) NOT NULL DEFAULT '' COMMENT 'email address';
ALTER TABLE `vt_grps` CHANGE `vt_inp_list` `vt_inp_list` text NULL COMMENT 'list of votable tids';
ALTER TABLE `vt_grps` CHANGE `vt_rel_qid` `vt_rel_qid` text NULL COMMENT 'list of rel QIDs';
ALTER TABLE `vt_grps` CHANGE `vt_tg_list` `vt_tg_list` text NULL COMMENT 'list of all (still) available tags for this user';
ALTER TABLE `vt_indiv` CHANGE `confirmation_hash` `confirmation_hash` char(32) NULL DEFAULT '' COMMENT '';
ALTER TABLE `vt_indiv` CHANGE `sub_uid` `sub_uid` char(32) NOT NULL DEFAULT '' COMMENT 'individual subuser';
ALTER TABLE `vt_rights` CHANGE `vt_code` `vt_code` char(8) NOT NULL DEFAULT '' COMMENT 'Voting access code for this group';
ALTER TABLE `vt_settings` CHANGE `btn_important_label` `btn_important_label` varchar(255) NOT NULL DEFAULT 'Ist mir besonders wichtig' COMMENT 'Label for the super preference button';
ALTER TABLE `vt_settings` CHANGE `btn_labels` `btn_labels` varchar(255) NOT NULL DEFAULT 'Stimme nicht zu,Nicht wichtig,Wichtig,Sehr wichtig,Super wichtig' COMMENT 'labels of voting buttons, comma-separated';

ALTER TABLE `articles` ALTER `is_showed` SET DEFAULT 1;
ALTER TABLE `cnslt` ALTER `is_followup_phase_showed` SET DEFAULT 0;
ALTER TABLE `cnslt` ALTER `is_input_phase_showed` SET DEFAULT 1;
ALTER TABLE `cnslt` ALTER `is_public` SET DEFAULT 0;
ALTER TABLE `cnslt` ALTER `is_support_phase_showed` SET DEFAULT 0;
ALTER TABLE `cnslt` ALTER `is_voting_result_phase_showed` SET DEFAULT 0;
ALTER TABLE `cnslt` ALTER `is_voting_phase_showed` SET DEFAULT 1;
ALTER TABLE `cnslt` ALTER `is_vt_anonymized` SET DEFAULT 0;
ALTER TABLE `cnslt` ALTER `is_vt_finalized` SET DEFAULT 1;
ALTER TABLE `fowup_fls` ALTER `is_only_month_year_showed` SET DEFAULT 0;
ALTER TABLE `inpt` ALTER `is_confirmed` SET DEFAULT NULL;
ALTER TABLE `inpt` ALTER `is_confirmed_by_user` SET DEFAULT NULL;
ALTER TABLE `inpt` ALTER `is_votable` SET DEFAULT NULL;
ALTER TABLE `users` ALTER `is_confirmed` SET DEFAULT NULL;
ALTER TABLE `users` ALTER `is_subscribed_newsletter` SET DEFAULT 0;
ALTER TABLE `user_info` ALTER `is_receiving_consultation_results` SET DEFAULT 1;
ALTER TABLE `vt_final` ALTER `is_followups` SET DEFAULT 0;
ALTER TABLE `vt_grps` ALTER `is_member` SET DEFAULT NULL;
ALTER TABLE `vt_indiv` ALTER `is_pimp` SET DEFAULT 0;
ALTER TABLE `vt_settings` ALTER `is_btn_important` SET DEFAULT 0;

-- Migration 20170222165108_dbjr1098.php
ALTER TABLE `cnslt`
CHANGE inp_fr inp_fr datetime NULL COMMENT 'Input possible from date on' AFTER proj,
CHANGE inp_to inp_to datetime NULL COMMENT 'Input possible till' AFTER inp_fr,
CHANGE spprt_fr spprt_fr datetime NULL COMMENT 'support button clickable from' AFTER is_support_phase_showed,
CHANGE spprt_to spprt_to datetime NULL COMMENT 'Supporting possible until' AFTER spprt_fr,
CHANGE vot_fr vot_fr datetime NULL COMMENT 'Voting possible from date on' AFTER spprt_ct,
CHANGE vot_to vot_to datetime NULL COMMENT 'Voting possible till' AFTER vot_fr;

-- Migration 20170523104756_dbjr1095.php
ALTER TABLE `fowups`DROP `hlvl`;

ALTER TABLE `fowup_fls` ADD COLUMN `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'general', ADD FOREIGN KEY (`type`) REFERENCES `fowups_type` (`name`) ON DELETE RESTRICT;
