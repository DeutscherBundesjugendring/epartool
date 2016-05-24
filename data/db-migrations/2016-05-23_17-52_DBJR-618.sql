ALTER TABLE `help_text`
ADD `module` varchar(255) NOT NULL DEFAULT 'default';

ALTER TABLE `help_text`
ADD UNIQUE `help_text_project_code_name_key` (`project_code`, `name`);

CREATE TABLE `help_text_module` (`name` varchar(255) NOT NULL ); 
ALTER TABLE `help_text_module` ADD PRIMARY KEY `name` (`name`);

INSERT INTO `help_text_module` (`name`) VALUES ('admin');
INSERT INTO `help_text_module` (`name`) VALUES ('default');

ALTER TABLE `help_text`
ADD FOREIGN KEY (`module`) REFERENCES `help_text_module` (`name`) ON DELETE RESTRICT ON UPDATE RESTRICT;
