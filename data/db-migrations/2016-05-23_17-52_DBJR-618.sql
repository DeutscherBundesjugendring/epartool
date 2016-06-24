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

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-voting-preparation',
        'Sample voting-preparation text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-voting-permissions',
        'Sample voting-permissions text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-voting-invitations',
        'Sample voting-invitations text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-voting-participants',
        'Sample voting-participants text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-voting-results',
        'Sample voting-results text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-follow-up',
        'Sample follow-up text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-consultation-follow-up-snippets',
        'Sample follow-up-snippets text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);
