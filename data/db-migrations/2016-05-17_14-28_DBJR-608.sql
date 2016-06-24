ALTER TABLE `proj`
ADD `video_facebook_enabled` tinyint(1) NOT NULL DEFAULT '0',
ADD `video_youtube_enabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `video_facebook_enabled`,
ADD `video_vimeo_enabled` tinyint(1) NOT NULL DEFAULT '0' AFTER `video_youtube_enabled`;

ALTER TABLE `quests`
ADD `video_enabled` tinyint(1) NOT NULL DEFAULT '0';

ALTER TABLE `inpt`
ADD `video_service` varchar(255) NULL,
ADD `video_id` varchar(255) NULL AFTER `video_service`;
