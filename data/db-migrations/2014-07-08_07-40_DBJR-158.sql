ALTER TABLE proj DROP PRIMARY KEY;
ALTER TABLE `proj`
    CHANGE `proj` `proj` char(2) NOT NULL PRIMARY KEY,
    DROP COLUMN `email`,
    DROP COLUMN `realnm`,
    DROP COLUMN `smtp_srv`,
    DROP COLUMN `smtp_prt`,
    DROP COLUMN `smtp_usr`,
    DROP COLUMN `smtp_pwd`,
    DROP COLUMN `toolline`;

