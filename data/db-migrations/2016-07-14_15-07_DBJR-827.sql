CREATE TABLE `license` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `link` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL
) ENGINE='InnoDB';

ALTER TABLE `proj` ADD `license` int NULL AFTER `allow_groups`;
ALTER TABLE `proj` ADD INDEX `proj_license_fkey` (`license`);
ALTER TABLE `proj` ADD FOREIGN KEY (`license`) REFERENCES `license` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `license` (`title`,`description`,`text`,`link`,`icon`,`alt`) VALUES
('Creative commons license', 'Creative Commons license 4.0: attribution, non-commercial', 'The contributions are published under a creative commons license. This means that your contribution may be re-used in summaries and publications for non-commercial use. As all contributions are published anonymously on this page, this website will be referred to as the source when re-using contributions.', 'http://creativecommons.org/licenses/by-nc/4.0/deed.en', 'license_cc.svg','CC-BY-NC 4.0');

UPDATE `proj` SET `license` = (SELECT id FROM `license` WHERE title = 'Creative commons license');
ALTER TABLE `proj` CHANGE `license` `license` int(11) NOT NULL AFTER `allow_groups`;
