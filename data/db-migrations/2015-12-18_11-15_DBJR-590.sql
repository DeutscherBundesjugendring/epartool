ALTER TABLE `help_text` ADD COLUMN `project_code` char(2) NOT NULL DEFAULT '$$';

INSERT INTO `help_text` (`name`, `body`, `project_code`)
(
    SELECT `name`, `body`, `proj`
    FROM `help_text` `ht`
    JOIN `proj` `p`
);

DELETE FROM `help_text` WHERE `project_code` = '$$';
