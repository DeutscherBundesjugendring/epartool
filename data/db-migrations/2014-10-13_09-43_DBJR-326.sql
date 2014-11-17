ALTER TABLE `dirs`
    DROP COLUMN `left`,
    DROP COLUMN `right`,
    ADD COLUMN `order` smallint unsigned NULL DEFAULT NULL;
