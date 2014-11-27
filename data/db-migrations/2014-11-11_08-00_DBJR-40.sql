DROP TABLE `discussns`;
DROP TABLE `discuss`;

CREATE TABLE `input_discussion` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `input_id` int unsigned NOT NULL,
    `user_id` int unsigned NOT NULL,
    `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_visible` boolean DEFAULT FALSE NOT NULL,
    `is_user_confirmed` boolean DEFAULT FALSE NOT NULL,
    `body` text NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `input_discussion_time_created_idx` (`time_created`),
    INDEX `input_discussion_is_visible_idx` (`is_visible`),
    CONSTRAINT `input_discussion_input_id_fkey` FOREIGN KEY (`input_id`) REFERENCES `inpt` (`tid`),
    CONSTRAINT `input_discussion_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`)
) ENGINE=InnoDb, COLLATE=utf8_unicode_ci;

ALTER TABLE `inpt`
    ADD COLUMN `input_discussion_contrib` int unsigned NULL DEFAULT NULL,
    ADD CONSTRAINT `input_discussion_contrib_fkey` FOREIGN KEY (`input_discussion_contrib`) REFERENCES `input_discussion` (`id`);

INSERT INTO `email_placeholder` (`name`, `description`, `is_global`)
VALUES
    ('contribution_text', 'The text of the contribution.', 0),
    ('input_thes', 'The theses part of the input.', 0),
    ('input_expl', 'The explanation part of the input.', 0);
