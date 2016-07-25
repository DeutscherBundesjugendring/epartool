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

ALTER TABLE `license` CHANGE `number` `number` int(11) NOT NULL;
ALTER TABLE `proj` DROP FOREIGN KEY `proj_ibfk_2`;
ALTER TABLE `license` DROP INDEX `PRIMARY`;
ALTER TABLE `license` ADD PRIMARY KEY `pkey` (`number`, `locale`);
ALTER TABLE `proj` ADD FOREIGN KEY (`license`) REFERENCES `license` (`number`) ON DELETE RESTRICT ON UPDATE RESTRICT;
