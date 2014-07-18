ALTER TABLE `user_info`
    DROP COLUMN `lg`,
    DROP COLUMN `grp`,
    DROP COLUMN `group_type`,
    DROP COLUMN `newsl_subscr`;

ALTER TABLE `users`
    DROP COLUMN `grp`,
    DROP COLUMN `group_type`;
