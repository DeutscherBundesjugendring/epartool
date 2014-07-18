CREATE TABLE `notification_type` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY(`id`),
    UNIQUE `notification_type_name_idx` (`name`)
) ENGINE=InnoDb;

INSERT INTO `notification_type` (`name`) VALUES ('input_created');

CREATE TABLE `notification` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `type_id` int unsigned NOT NULL,
    `user_id` int unsigned NOT NULL,
    `is_confirmed` boolean DEFAULT 0,
    PRIMARY KEY(`id`),
    CONSTRAINT `notification_type_id_ibfk` FOREIGN KEY (`type_id`) REFERENCES `notification_type` (`id`),
    CONSTRAINT `notification_user_id_ibfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`uid`)
) ENGINE=InnoDb;

CREATE TABLE `notification_parameter` (
    `notification_id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `value` text NULL,
    CONSTRAINT `notification_parameter_notification_id_ibfk` FOREIGN KEY (`notification_id`) REFERENCES `notification` (`id`),
    PRIMARY KEY(`notification_id`, `name`)
) ENGINE=InnoDb;

INSERT INTO `email_placeholder` (`name`, `description`, `is_global`)
VALUES
    ('website_url',  'Link to the relevant page on the website.', 0),
    ('question_text',  'The text of the relevant question.',   0),
    ('unsubscribe_url',  'Link to remove user from the relevant subscription or mailing list.',   0);
