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
  `time_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
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
('question_text', 'The text of the relevant question.', 0),
('unsubscribe_url', 'Link to remove user from the relevant subscription or mailing list.', 0),
('contribution_text', 'The text of the contribution.', 0),
('input_thes', 'The theses part of the input.', 0),
('input_expl', 'The explanation part of the input.', 0),
('video_url', 'Link to the video contribution.', 0);

CREATE TABLE `email_recipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_id` int(10) unsigned NOT NULL,
  `type` enum('to','cc','bcc') NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
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
  `name` varchar(255) DEFAULT NULL,
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
  `video_id` varchar(255) DEFAULT NULL,
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
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`notification_id`,`name`),
  CONSTRAINT `notification_parameter_notification_id_ibfk` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`)
);


CREATE TABLE `notification_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_type_name_idx` (`name`)
);

INSERT INTO `notification_type` (`name`) VALUES
('input_created'),
('input_discussion_contribution_created'),
('follow_up_created');

CREATE TABLE `parameter` (
  `name` varchar(255) NOT NULL,
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
  `time_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `name` varchar(255) NOT NULL,
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
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
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
  `id` varchar(255) NOT NULL COMMENT 'md5 (tid''.-.''uid)',
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
ADD `video_service` varchar(255) NULL,
ADD `video_id` varchar(255) NULL AFTER `video_service`;

-- Migration 2016-05-19-12-27_DBJR-609.sql
ALTER TABLE `cnslt`
ADD `discussion_video_enabled` tinyint(1) NOT NULL DEFAULT '1';

-- Migration 2016-05-19_14-18_DBJR-610.sql
ALTER TABLE `input_discussion`
ADD `video_service` varchar(255) NULL AFTER `body`;

-- Migration 2016-05-23_17-52_DBJR-618.sql
ALTER TABLE `help_text`
ADD `module` varchar(255) NOT NULL DEFAULT 'default';

ALTER TABLE `help_text`
ADD UNIQUE `help_text_project_code_name_key` (`project_code`, `name`);

CREATE TABLE `help_text_module` (`name` varchar(255) NOT NULL );
ALTER TABLE `help_text_module` ADD PRIMARY KEY `name` (`name`);

INSERT INTO `help_text_module` (`name`) VALUES ('admin');
INSERT INTO `help_text_module` (`name`) VALUES ('default');

ALTER TABLE `help_text`
ADD FOREIGN KEY (`module`) REFERENCES `help_text_module` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Migration 2016-05-25_18-04_DBJR-626.sql
CREATE TABLE `theme` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
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
CHANGE `color_headings` `color_accent_1` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `theme_id`,
CHANGE `color_frame_background` `color_primary` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `color_accent_1`,
CHANGE `color_active_link` `color_accent_2` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `color_primary`;

-- Migration 2016-06-27_18-36_DBJR-761.sql
ALTER TABLE `proj`
ADD `locale` varchar(255) NOT NULL DEFAULT 'en_US';

-- Migration 2016-06-27_14-58_DBJR-807.sql
UPDATE `proj` SET `theme_id` = (SELECT `id` FROM `theme` ORDER BY `id` LIMIT 1)
WHERE `theme_id` IS NULL AND color_accent_1 IS NULL AND color_accent_2 IS NULL AND color_primary IS NULL;

-- Migration 2016-07-13_13-47_DBJR-824.sql
ALTER TABLE `proj` ADD `state_label` varchar(255) DEFAULT NULL;

-- Migration 2016-07-14_15-07_DBJR-827.sql
CREATE TABLE `license` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL
) ENGINE='InnoDB';

ALTER TABLE `proj` ADD `license` int NULL;
ALTER TABLE `proj` ADD INDEX `proj_license_fkey` (`license`);
ALTER TABLE `proj` ADD FOREIGN KEY (`license`) REFERENCES `license` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `license` (`title`,`description`,`text`,`link`,`icon`,`alt`) VALUES
('Creative commons license', 'Creative Commons license 4.0: attribution, non-commercial', 'The contributions are published under a creative commons license. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.', 'http://creativecommons.org/licenses/by-nc/4.0/deed.en', 'license_cc.svg','CC-BY-NC 4.0');

UPDATE `proj` SET `license` = (SELECT id FROM `license` WHERE title = 'Creative commons license');
ALTER TABLE `proj` CHANGE `license` `license` int(11) NOT NULL;

-- Migration 2016-07-15_11-03_DBJR-827.sql
CREATE TABLE `language` (
  `code` varchar(255) NOT NULL
);
ALTER TABLE `language` ADD PRIMARY KEY `pkey` (`code`);
INSERT INTO `language` (`code`) VALUES ('es_ES'), ('de_DE'), ('en_US');
ALTER TABLE `proj` ADD INDEX `language_code_fkey` (`locale`);
ALTER TABLE `proj` CHANGE `locale` `locale` varchar(255) NOT NULL;
ALTER TABLE `proj` ADD FOREIGN KEY (`locale`) REFERENCES `language` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `license` ADD `locale` varchar(255) NOT NULL;
ALTER TABLE `license` ADD INDEX `language_code_fkey` (`locale`);
UPDATE `license` SET `locale` = 'en_US';
ALTER TABLE `license` ADD FOREIGN KEY (`locale`) REFERENCES `language` (`code`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `license` CHANGE `id` `id` int(11) NOT NULL;
ALTER TABLE `proj` DROP FOREIGN KEY `proj_ibfk_2`;
ALTER TABLE `license` DROP INDEX `PRIMARY`;
ALTER TABLE `license` CHANGE `id` `number` int(11) NOT NULL;
ALTER TABLE `license` ADD PRIMARY KEY `pkey` (`number`, `locale`);
ALTER TABLE `proj` ADD FOREIGN KEY (`license`) REFERENCES `license` (`number`) ON DELETE RESTRICT ON UPDATE RESTRICT;
