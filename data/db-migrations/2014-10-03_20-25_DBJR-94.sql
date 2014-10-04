ALTER TABLE `cnslt`
    ADD COLUMN `phase_info` varchar(50) NULL DEFAULT NULL,
    ADD COLUMN `phase_support` varchar(50) NULL DEFAULT NULL,
    ADD COLUMN `phase_input` varchar(50) NULL DEFAULT NULL,
    ADD COLUMN `phase_voting` varchar(50) NULL DEFAULT NULL,
    ADD COLUMN `phase_followup` varchar(50) NULL DEFAULT NULL;
