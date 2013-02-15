ALTER TABLE `ml_sent` ADD COLUMN `id` INTEGER(11) NOT NULL;

ALTER TABLE `ml_sent` DROP INDEX `PRIMARY`;

ALTER TABLE `ml_sent` MODIFY COLUMN `when` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE `ml_sent` ADD PRIMARY KEY USING BTREE (`id`);

ALTER TABLE `ml_sent` MODIFY COLUMN `id` INTEGER(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ml_sent` MODIFY COLUMN `id` INTEGER(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ml_sent` MODIFY COLUMN `id` INTEGER(11) NOT NULL AUTO_INCREMENT AFTER `ip`;

ALTER TABLE `users` ADD COLUMN `group_type` ENUM('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe';

ALTER TABLE `users` ADD COLUMN `source` SET('d','g','p','m') DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper';

ALTER TABLE `users` ADD COLUMN `src_misc` VARCHAR(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source';

ALTER TABLE `users` ADD COLUMN `group_size` TINYINT(3) UNSIGNED DEFAULT NULL COMMENT '1,10,30,80,150,over';

ALTER TABLE `users` ADD COLUMN `name_group` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group';

ALTER TABLE `users` ADD COLUMN `name_pers` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person';

ALTER TABLE `users` ADD COLUMN `age_group` ENUM('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo';

ALTER TABLE `users` ADD COLUMN `regio_pax` VARCHAR(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer';

ALTER TABLE `users` ADD COLUMN `cnslt_results` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations';

ALTER TABLE `users` MODIFY COLUMN `cnslt_results` ENUM('y','n') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'y' COMMENT 'Receives results of consultations' AFTER `regio_pax`;

ALTER TABLE `users` MODIFY COLUMN `regio_pax` VARCHAR(200) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Bundesländer' AFTER `age_group`;

ALTER TABLE `users` MODIFY COLUMN `age_group` ENUM('1','2','3','4','5') COLLATE utf8_unicode_ci NOT NULL DEFAULT '5' COMMENT '(1)to17yrs(2)upto26yrs(3)upto27yrs(4)all(5)noinfo' AFTER `name_pers`;

ALTER TABLE `users` MODIFY COLUMN `name_pers` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of contact person' AFTER `name_group`;

ALTER TABLE `users` MODIFY COLUMN `name_group` VARCHAR(80) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'Name of group' AFTER `group_size`;

ALTER TABLE `users` MODIFY COLUMN `group_size` TINYINT(3) UNSIGNED DEFAULT NULL COMMENT '1,10,30,80,150,over' AFTER `src_misc`;

ALTER TABLE `users` MODIFY COLUMN `src_misc` VARCHAR(300) COLLATE utf8_unicode_ci DEFAULT '' COMMENT 'explanation of misc source' AFTER `source`;

ALTER TABLE `users` MODIFY COLUMN `source` SET('d','g','p','m') DEFAULT NULL COMMENT 'Dialogue, Group, Misc, Position paper' AFTER `group_type`;

ALTER TABLE `users` MODIFY COLUMN `group_type` ENUM('single','group') COLLATE utf8_unicode_ci DEFAULT 'single' COMMENT 'Art der Gruppe' AFTER `confirm_key`;

ALTER TABLE `users` MODIFY COLUMN `confirm_key` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'Key zur Bestätigung der Registrierung via Mail' AFTER `lvl`;

