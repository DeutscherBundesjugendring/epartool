ALTER TABLE `cnslt`
    DROP COLUMN `disc_show`,
    ADD COLUMN `discussion_from` datetime NULL DEFAULT NULL,
    ADD COLUMN `discussion_to` datetime NULL DEFAULT NULL;
