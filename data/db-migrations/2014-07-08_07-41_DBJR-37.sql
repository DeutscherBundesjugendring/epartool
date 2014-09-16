DROP TABLE `ml_def`;
DROP TABLE `ml_sent`;

ALTER TABLE `proj` ENGINE=InnoDB;

CREATE TABLE `email_template_type` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE `email_template_type_name_idx` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `email_template` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `type_id` int unsigned NOT NULL,
    `project_code` char(2) NOT NULL,
    `subject` varchar(75) NOT NULL,
    `body_html` text NOT NULL,
    `body_text` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE `email_template_name_project_code_idx` (`name`, `project_code`),
    CONSTRAINT `email_template_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`),
    CONSTRAINT `email_template_type_id_ibfk` FOREIGN KEY (`type_id`) REFERENCES `email_template_type` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `email_component` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `project_code` char(2) NOT NULL,
    `body_html` text NOT NULL,
    `body_text` text NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE `email_component_name_idx` (`name`),
    CONSTRAINT `email_component_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`)
) ENGINE=InnoDB;

CREATE TABLE `email` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `project_code` char(2) NOT NULL,
    `time_queued` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `time_sent` timestamp NULL DEFAULT NULL,
    `sent_by_user` varchar(255) NULL DEFAULT NULL,
    `subject` varchar(75) NULL DEFAULT NULL,
    `body_html` text DEFAULT NULL,
    `body_text` text NULL DEFAULT NULL,
    `attachment` varchar(255) NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `email_project_code_ibfk` FOREIGN KEY (`project_code`) REFERENCES `proj` (`proj`),
    KEY `email_time_sent_idx` (`time_sent`),
    KEY `email_time_queued_idx` (`time_queued`)
)  ENGINE=InnoDB;

CREATE TABLE `email_attachment` (
    `id` int unsigned AUTO_INCREMENT,
    `email_id` int unsigned NOT NULL,
    `filepath` varchar(255) ,
    PRIMARY KEY (`id`),
    CONSTRAINT `email_attachment_email_id_ibfk` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`),
    UNIQUE `email_attachment_filepath_idx` (`filepath`)
) ENGINE=InnoDB;

CREATE TABLE `email_recipient` (
    `id` int unsigned AUTO_INCREMENT,
    `email_id` int unsigned NOT NULL,
    `type` enum('to', 'cc', 'bcc') NOT NULL,
    `name` varchar(255),
    `email` varchar(255),
    PRIMARY KEY (`id`),
    CONSTRAINT `email_recipient_email_id_ibfk` FOREIGN KEY (`email_id`) REFERENCES `email` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `email_template_attachment` (
    `id` int unsigned AUTO_INCREMENT,
    `email_template_id` int unsigned NOT NULL,
    `filepath` varchar(255) ,
    PRIMARY KEY (`id`),
    CONSTRAINT `email_template_attachment_email_template_id_ibfk` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`),
    UNIQUE `email_template_attachment_filepath_idx` (`filepath`)
) ENGINE=InnoDB;

CREATE TABLE `email_placeholder` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `description` text NULL DEFAULT NULL,
    `is_global` boolean NOT NULL DEFAULT 0,
    PRIMARY KEY(`id`),
    UNIQUE `email_placeholder_name_idx` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `email_template_has_email_placeholder` (
    `email_template_id` int unsigned NOT NULL,
    `email_placeholder_id` int unsigned NOT NULL,
    PRIMARY KEY (`email_template_id`, `email_placeholder_id`),
    CONSTRAINT `et_has_ep_email_placeholder_id_ibfk`
        FOREIGN KEY (`email_placeholder_id`) REFERENCES `email_placeholder` (`id`),
    CONSTRAINT `et_has_ep_email_template_id_ibfk`
        FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`)
) ENGINE=InnoDB;



INSERT INTO `email_template_type` (`name`)
VALUES
    ('system'),
    ('custom');

INSERT INTO `email_placeholder` (`name`, `description`, `is_global`)
VALUES
    ('voter_email',  'The email of the original voter.', 0),
    ('to_name',  'The name of the recipient. If the name is not known, teh value of {{to_email}} is used.',   0),
    ('to_email', 'The email address of the recipient.',  0),
    ('password', 'The new password.',    0),
    ('confirmation_url', 'The confirmation link for the user to visit.', 0),
    ('rejection_url',    'The rejection link for the user to visit.',    0),
    ('consultation_title_short', 'The short version of the consultation title.', 0),
    ('consultation_title_long',  'The long version of the consultation title.',  0),
    ('input_phase_end',  'The end of the input phase.',  0),
    ('input_phase_start',    'The start of the input phase.',    0),
    ('voting_phase_end', 'The end of the voting phase.', 0),
    ('voting_phase_start',   'The start of the voting phase.',   0),
    ('inputs_html',  'The users inputs in html formatting.', 0),
    ('inputs_text',  'The users inputs in plain text formatting.',   0),
    ('voting_weight',    'The voting weight of the relevant user.',  0),
    ('voting_url',   'the url where voting takes place.',    0),
    ('group_category',  'The type of the relevant group',   0),
    ('from_name',    'The name of the sender.',  1),
    ('from_address', 'The email address of the sender.', 1),
    ('contact_name', 'The name from the contact info.',  1),
    ('contact_www',  'The www from the contact info.',   1),
    ('contact_email',    'The email address from the contact info.', 1);
