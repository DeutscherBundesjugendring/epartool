SET collation_connection = 'utf8mb4_unicode_ci';
SET @salt = RAND();

START TRANSACTION;

UPDATE `email` SET
    `body_text` = 'Text was removed by anonymization process.',
    `body_html` = 'Text was removed by anonymization process.';

UPDATE `email_recipient` SET
    `email` = CONCAT('user', SUBSTRING(MD5(CONCAT(`email`, @salt)), 1, 8), '@example.com');

UPDATE `proj` SET
    `contact_email` = CONCAT('user', SUBSTRING(MD5(CONCAT(`contact_email`, @salt)), 1, 8), '@example.com');

UPDATE `users` SET
    `name` = CONCAT('user name ', SUBSTRING(MD5(CONCAT(`name`, @salt)), 1, 8)),
    `name_group` = CONCAT('group ', SUBSTRING(MD5(CONCAT(`name_group`, @salt)), 1, 8)),
    `name_pers` = CONCAT('pers ', SUBSTRING(MD5(CONCAT(`name_pers`, @salt)), 1, 8)),
    `nick` = CONCAT('nick', SUBSTRING(MD5(CONCAT(`name`, @salt)), 1, 8)),
    `email` = CONCAT('user', SUBSTRING(MD5(CONCAT(`email`, @salt)), 1, 8), '@example.com'),
    `src_misc` = CONCAT('source ', SUBSTRING(MD5(CONCAT(`src_misc`, @salt)), 1, 8));

UPDATE `user_info` SET
    `name_group` = CONCAT('group ', SUBSTRING(MD5(CONCAT(`name_group`, @salt)), 1, 8)),
    `name_pers` = CONCAT('pers ', SUBSTRING(MD5(CONCAT(`name_pers`, @salt)), 1, 8)),
    `src_misc` = CONCAT('source ', SUBSTRING(MD5(CONCAT(`src_misc`, @salt)), 1, 8));

ALTER TABLE `vt_indiv`
DROP FOREIGN KEY `vt_indiv_ibfk_1`,
ADD FOREIGN KEY (`sub_uid`) REFERENCES `vt_grps` (`sub_uid`) ON DELETE RESTRICT ON UPDATE CASCADE;

UPDATE `vt_grps` SET
    `sub_user` = CONCAT('user', SUBSTRING(MD5(CONCAT(`sub_user`, @salt)), 1, 8), '@example.com'),
    `sub_uid` = MD5(CONCAT(
        CONCAT('user', SUBSTRING(MD5(CONCAT(`sub_user`, @salt)), 1, 8), '@example.com'),
        `kid`
    ));

COMMIT;
