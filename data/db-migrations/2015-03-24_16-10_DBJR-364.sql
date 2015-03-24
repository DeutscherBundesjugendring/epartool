CREATE TABLE `parameter` (
    `name` varchar(255) NOT NULL,
    `proj` char(2),
    `value` text NULL DEFAULT NULL,
    PRIMARY KEY (`name`, `proj`)
);

INSERT INTO `parameter` (`name`, `proj`) (SELECT 'site.title', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'site.description', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'site.motto', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.name', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.email', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.www', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.street', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.town', `proj` FROM `proj`);
INSERT INTO `parameter` (`name`, `proj`) (SELECT 'contact.zip', `proj` FROM `proj`);
