CREATE TABLE `contributor_age` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int NOT NULL,
  `to` int NULL,
  FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE='InnoDB';

CREATE TABLE `group_size` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `consultation_id` int(10) unsigned NOT NULL,
  `from` int NOT NULL,
  `to` int NULL,
  FOREIGN KEY (`consultation_id`) REFERENCES `cnslt` (`kid`)
) ENGINE='InnoDB';

ALTER TABLE `cnslt` ADD `groups_no_information` tinyint(1) NOT NULL DEFAULT '1';

INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '1', '2' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '3', '10' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '11', '30' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '31', '80' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`, `to`) (SELECT `kid`, '81', '150' FROM `cnslt`);
INSERT INTO `group_size` (`consultation_id`, `from`) (SELECT `kid`, '151' FROM `cnslt`);

ALTER TABLE `vt_rights`
CHANGE `grp_siz` `grp_siz` int(11) NULL COMMENT 'Group size that we recognise' AFTER `vt_code`;

UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 1 AND `to` = 2) WHERE grp_siz = 1;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 3 AND `to` = 10) WHERE grp_siz = 10;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 11 AND `to` = 30) WHERE grp_siz = 30;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 31 AND `to` = 80) WHERE grp_siz = 80;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 81 AND `to` = 150) WHERE grp_siz = 150;
UPDATE `vt_rights` SET grp_siz = (SELECT id FROM `group_size` WHERE consultation_id = `vt_rights`.kid AND `from` = 151 AND `to` IS NULL) WHERE grp_siz = 200;

UPDATE `vt_rights` SET grp_siz = NULL WHERE grp_siz = 0;

ALTER TABLE `vt_rights`
ADD FOREIGN KEY (`grp_siz`) REFERENCES `group_size` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `user_info` CHANGE `group_size` `group_size` int NULL AFTER `src_misc`;

UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` = 2) WHERE group_size = 1;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 3 AND `to` = 10) WHERE group_size = 10;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 11 AND `to` = 30) WHERE group_size = 30;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 31 AND `to` = 80) WHERE group_size = 80;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 81 AND `to` = 150) WHERE group_size = 150;
UPDATE `user_info` SET group_size = (SELECT id FROM `group_size` WHERE consultation_id = `user_info`.kid AND `from` = 151 AND `to` IS NULL) WHERE group_size = 200;

ALTER TABLE `user_info`
ADD FOREIGN KEY (`group_size`) REFERENCES `group_size` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


ALTER TABLE `user_info`
CHANGE `age_group` `age_group` int NULL AFTER `name_pers`;

INSERT INTO `contributor_age` (`consultation_id`, `from`) (SELECT `kid`, '1' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`, `to`) (SELECT `kid`, '1', '17' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`, `to`) (SELECT `kid`, '18', '26' FROM `cnslt`);
INSERT INTO `contributor_age` (`consultation_id`, `from`) (SELECT `kid`, '27' FROM `cnslt`);

UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` IS NULL) WHERE age_group = 4;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 1 AND `to` = 17) WHERE age_group = 1;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 18 AND `to` = 26) WHERE age_group = 2;
UPDATE `user_info` SET age_group = (SELECT id FROM `contributor_age` WHERE consultation_id = `user_info`.kid AND `from` = 27 AND `to` IS NULL) WHERE age_group = 3;
UPDATE `user_info` SET age_group = NULL WHERE age_group = 5;

ALTER TABLE `user_info`
ADD FOREIGN KEY (`age_group`) REFERENCES `contributor_age` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
