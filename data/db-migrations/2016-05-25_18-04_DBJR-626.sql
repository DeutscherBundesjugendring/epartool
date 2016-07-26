CREATE TABLE `theme` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255) NOT NULL,
    `color_headings` varchar(255) NOT NULL,
    `color_frame_background` varchar(255) NOT NULL,
    `color_active_link` varchar(255) NOT NULL
);

ALTER TABLE `theme`
ADD UNIQUE `name` (`name`);

ALTER TABLE `proj`
ADD `theme_id` int NULL,
ADD `color_headings` varchar(255) NULL AFTER `theme_id`,
ADD `color_frame_background` varchar(255) NULL AFTER `color_headings`,
ADD `color_active_link` varchar(255) NULL AFTER `color_frame_background`,
ADD `logo` varchar(255) NULL AFTER `color_active_link`,
ADD `favicon` varchar(255) NULL AFTER `logo`;

ALTER TABLE `proj`
ADD INDEX `proj_theme_id_fk` (`theme_id`);

ALTER TABLE `proj`
ADD FOREIGN KEY (`theme_id`) REFERENCES `theme` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
