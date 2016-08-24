ALTER TABLE `vt_indiv`
CHANGE `pts` `pts` tinyint(4) NULL COMMENT 'the vote itself (points)' AFTER `tid`;
UPDATE `vt_indiv` SET `pts` = NULL WHERE `pts` = 5;
