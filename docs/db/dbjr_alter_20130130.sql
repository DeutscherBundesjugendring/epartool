ALTER TABLE `inpt` ADD COLUMN `confirm_key` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT '';

ALTER TABLE `inpt` CHANGE COLUMN `by` `uid` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'from which User ID';

ALTER TABLE `inpt` MODIFY COLUMN `confirm_key` VARCHAR(64) COLLATE utf8_unicode_ci DEFAULT '' AFTER `notiz`;

ALTER TABLE `users` ADD COLUMN `confirm_key` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail';

ALTER TABLE `users` MODIFY COLUMN `confirm_key` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail' AFTER `lvl`;

