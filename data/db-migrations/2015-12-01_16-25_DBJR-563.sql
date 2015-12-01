ALTER TABLE `articles` CHANGE COLUMN `kid` `kid` smallint(5) unsigned NULL DEFAULT NULL;
UPDATE `articles` SET `kid` = NULL WHERE `kid` = 0;
ALTER TABLE `articles` ENGINE=InnoDB;
ALTER TABLE `cnslt` ENGINE=InnoDB;
ALTER TABLE `articles` ADD CONSTRAINT articles_kid_fkey FOREIGN KEY (kid) REFERENCES cnslt(kid);
