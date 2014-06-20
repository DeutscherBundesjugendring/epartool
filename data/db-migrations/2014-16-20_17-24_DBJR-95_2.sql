CREATE TABLE `urlkey_action` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `urlkey` varchar(40) NOT NULL,
    `time_created` timestamp NOT NULL,
    `time_visited` timestamp NULL DEFAULT NULL,
    `time_valid_to` timestamp NULL DEFAULT NULL,
    `handler_class` varchar(255) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE `urlkey_action_urlkey_idx` (`urlkey`)
) Engine=InnoDb;

CREATE TABLE `urlkey_action_parameter` (
    `urlkey_action_id` int unsigned  NOT NULL,
    `name` varchar(255) NOT NULL,
    `value` text NULL DEFAULT NULL,
    PRIMARY KEY (`urlkey_action_id`, `name`),
    CONSTRAINT `urlkey_action_parameter_urlkey_action_id_ibfk` FOREIGN KEY (`urlkey_action_id`) REFERENCES `urlkey_action` (`id`)
) Engine=InnoDb;

UPDATE `email_placeholder`
SET
    `name`='password_reset_url',
    `description`='The url where user can reset their password.'
WHERE `name`='password';

UPDATE `email_template`
SET
    `name` = 'password_reset',
    `body_html` = '<p>Hallo {{to_name}},</p>\n<p>Du hast neue Zugangsdaten angefordert. Mit den folgenden Daten kannst du dich einloggen:</p>\n\n<p>To reset your password, please visit this link:<br />{{password_reset_url}}</p>',
    `body_text` = 'Hallo {{to_name}},\nDu hast neue Zugangsdaten angefordert. Mit den folgenden Daten kannst du dich einloggen:\n\nTo reset your password, please visit this link:\n\n{{password_reset_url}}'
WHERE `name` = 'forgotten_password';
