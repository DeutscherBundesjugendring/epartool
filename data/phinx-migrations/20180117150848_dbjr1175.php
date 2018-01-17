<?php

use Phinx\Migration\AbstractMigration;

class Dbjr1175 extends AbstractMigration
{
    public function up()
    {
        $this->execute(<<<EOD
ALTER TABLE `fowups`
CHANGE `type` `type` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'general' AFTER `expl`;

ALTER TABLE `help_text_module`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL FIRST;

ALTER TABLE `input_discussion`
CHANGE `video_service` `video_service` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `body`,
CHANGE `video_id` `video_id` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `video_service`;

ALTER TABLE `language`
CHANGE `code` `code` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL FIRST;

ALTER TABLE `notification`
CHANGE `is_confirmed` `is_confirmed` tinyint(1) NOT NULL DEFAULT '0' AFTER `user_id`;

ALTER TABLE `notification_parameter`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `notification_id`;

ALTER TABLE `notification_type`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`;

ALTER TABLE `parameter`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL FIRST,
CHANGE `proj` `proj` char(2) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`;

ALTER TABLE `proj`
CHANGE `color_accent_1` `color_accent_1` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `theme_id`,
CHANGE `color_primary` `color_primary` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `color_accent_1`,
CHANGE `color_accent_2` `color_accent_2` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `color_primary`,
CHANGE `logo` `logo` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `color_accent_2`,
CHANGE `favicon` `favicon` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `logo`;

ALTER TABLE `quests`
CHANGE `nr` `nr` varchar(4) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Number shown in ordered list' AFTER `kid`;
ALTER TABLE `quests`
ADD PRIMARY KEY `quests_qi_pkey` (`qi`),
DROP INDEX `qi`,
DROP INDEX `qi_2`;

ALTER TABLE `theme`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
CHANGE `color_accent_1` `color_accent_1` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `name`,
CHANGE `color_primary` `color_primary` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `color_accent_1`,
CHANGE `color_accent_2` `color_accent_2` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `color_primary`;

ALTER TABLE `urlkey_action`
CHANGE `urlkey` `urlkey` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
CHANGE `handler_class` `handler_class` varchar(255) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `time_valid_to`;

ALTER TABLE `urlkey_action_parameter`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `urlkey_action_id`;

ALTER TABLE `users`
CHANGE `name` `name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `last_act`,
CHANGE `email` `email` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL COMMENT 'Mail Address' AFTER `name`,
CHANGE `password` `password` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `email`,
CHANGE `role` `role` varchar(191) COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT 'user' AFTER `cmnt`,
CHANGE `source` `source` set('d','g','p','m') COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Dialogue, Group, Misc, Position paper' AFTER `role`,
CHANGE `src_misc` `src_misc` varchar(300) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Explanation of misc source' AFTER `source`,
CHANGE `name_group` `name_group` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Name of group' AFTER `group_size`,
CHANGE `name_pers` `name_pers` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Name of contact person' AFTER `name_group`,
CHANGE `regio_pax` `regio_pax` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `age_group_to`,
CHANGE `nick` `nick` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `is_receiving_consultation_results`;

ALTER TABLE `user_info`
CHANGE `source` `source` set('d','g','p','m') COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Dialogue, Group, Misc, Position paper' AFTER `cmnt`,
CHANGE `src_misc` `src_misc` varchar(300) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'explanation of misc source' AFTER `source`,
CHANGE `name_group` `name_group` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Name of group' AFTER `group_size`,
CHANGE `name_pers` `name_pers` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Name of contact person' AFTER `name_group`,
CHANGE `regio_pax` `regio_pax` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL COMMENT 'Bundesländer' AFTER `age_group`,
CHANGE `confirmation_key` `confirmation_key` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `time_user_confirmed`,
CHANGE `name` `name` varchar(255) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `confirmation_key`;	 

ALTER TABLE `video_service`
CHANGE `name` `name` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL FIRST;

ALTER TABLE `vt_final`
CHANGE `id` `id` varchar(191) COLLATE 'utf8mb4_unicode_ci' NOT NULL COMMENT 'md5 (tid\'.-.\'uid)' AFTER `is_followups`;

ALTER TABLE `vt_indiv`
CHANGE `confirmation_hash` `confirmation_hash` char(32) COLLATE 'utf8mb4_unicode_ci' NULL AFTER `upd`;

EOD
);
    }
}
