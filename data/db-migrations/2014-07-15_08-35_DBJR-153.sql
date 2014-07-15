ALTER TABLE `vt_final`
    ADD COLUMN `id` varchar(255) NOT NULL COMMENT 'md5 (tid''.-.''uid)',
    DROP INDEX `tid`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY(`id`);

ALTER TABLE `cnslt`
    ADD COLUMN `vt_finalized` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'voting results are in vt_final',
    ADD COLUMN `vt_anonymized` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'is voting anonymized';
