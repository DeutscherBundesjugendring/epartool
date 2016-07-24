ALTER TABLE `vt_grps`
ADD INDEX `vt_grps_sub_uid_fkey` (`sub_uid`);

ALTER TABLE `vt_indiv`
ADD FOREIGN KEY (`sub_uid`) REFERENCES `vt_grps` (`sub_uid`) ON DELETE RESTRICT ON UPDATE RESTRICT;
