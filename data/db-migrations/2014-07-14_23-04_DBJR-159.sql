ALTER TABLE `inpt`
    DROP COLUMN `kid`,
    CHANGE `confirm_key` `confirmation_key` varchar(40) NULL DEFAULT NULL,
    CHANGE `uid` `uid` int unsigned NULL DEFAULT NULL,
    ENGINE = InnoDB;

ALTER TABLE `inpt_tgs`
    ENGINE = InnoDB,
    CHARSET='UTF8',
    CHANGE `tid` `tid` int unsigned NOT NULL;

DELETE FROM inpt_tgs WHERE tid NOT IN (SELECT tid FROM inpt);

ALTER TABLE `inpt_tgs`
    ADD CONSTRAINT `inpt_tgs_tid_ibfk` FOREIGN KEY (`tid` ) REFERENCES `inpt` (`tid` );

ALTER TABLE `users`
    ENGINE = InnoDB,
    ADD UNIQUE `users_email_idx` (`email`),
    DROP COLUMN `confirm_key`,
    DROP COLUMN `ip`,
    DROP COLUMN `agt`,
    CHANGE `password` `password` varchar(150) NULL DEFAULT NULL,
    CHANGE `uid` `uid` int unsigned NOT NULL AUTO_INCREMENT,
    CHANGE `name` `name` varchar(80) NULL DEFAULT NULL,
    CHANGE `group_type` `group_type` enum('single','group') NULL DEFAULT NULL,
    CHANGE `regio_pax` `regio_pax` varchar(200) NULL DEFAULT NULL,
    CHANGE `cnslt_results` `cnslt_results` enum('y','n') NULL DEFAULT NULL COMMENT 'Receives results of consultations',
    CHANGE `src_misc` `src_misc` varchar(300) NULL DEFAULT NULL COMMENT 'Explanation of misc source',
    CHANGE `name_group` `name_group` varchar(80) NULL DEFAULT NULL COMMENT 'Name of group',
    CHANGE `name_pers` `name_pers` varchar(80) NULL DEFAULT NULL COMMENT 'Name of contact person',
    CHANGE `newsl_subscr` `newsl_subscr` enum('y','n') NULL DEFAULT NULL COMMENT 'Subscription of newsletter';

UPDATE `inpt` SET `uid` = NULL WHERE uid NOT IN(
    SELECT uid FROM users
);

ALTER TABLE `inpt`
    ADD CONSTRAINT `inpt_uid_ibfk` FOREIGN KEY (`uid` ) REFERENCES `users` (`uid` );

ALTER TABLE `user_info`
    ENGINE = InnoDB,
    DROP COLUMN `ip`,
    DROP COLUMN `agt`,
    CHANGE `uid` `uid` int unsigned NOT NULL,
    ADD COLUMN `confirmation_key` varchar(40) NULL DEFAULT NULL,
    ADD COLUMN `is_contrib_under_cc` tinyint(1) NOT NULL DEFAULT '0',
    ADD COLUMN `name` varchar(80) NULL DEFAULT NULL;

DELETE FROM user_info WHERE uid NOT IN (SELECT uid FROM users);

ALTER TABLE `user_info`
    ADD CONSTRAINT `user_info_uid_ibfk` FOREIGN KEY (`uid` ) REFERENCES `users` (`uid`);
