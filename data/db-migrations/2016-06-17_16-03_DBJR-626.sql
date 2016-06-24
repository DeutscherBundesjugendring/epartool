ALTER TABLE `theme`
CHANGE `color_headings` `color_accent_1` varchar(255) NOT NULL AFTER `name`,
CHANGE `color_frame_background` `color_primary` varchar(255) NOT NULL AFTER `color_accent_1`,
CHANGE `color_active_link` `color_accent_2` varchar(255) NOT NULL AFTER `color_primary`;

ALTER TABLE `proj`
CHANGE `color_headings` `color_accent_1` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `theme_id`,
CHANGE `color_frame_background` `color_primary` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `color_accent_1`,
CHANGE `color_active_link` `color_accent_2` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `color_primary`;
