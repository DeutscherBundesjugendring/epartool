ALTER TABLE `articles` ADD COLUMN `time_modified` timestamp NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `quests` ADD COLUMN `time_modified` timestamp NOT NULL DEFAULT '0000-00-00';
