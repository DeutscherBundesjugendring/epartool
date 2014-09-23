ALTER TABLE `user_info`
    DROP COLUMN `lg`,
    DROP COLUMN `grp`,
    DROP COLUMN `group_type`;

ALTER TABLE `users`
    DROP COLUMN `grp`,
    DROP COLUMN `group_type`;
