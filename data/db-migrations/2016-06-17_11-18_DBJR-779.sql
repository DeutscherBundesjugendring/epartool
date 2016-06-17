INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-question',
        'Sample question text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);

INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`)
(
    SELECT
        'help-text-admin-contribution',
        'Sample contribution text.',
        `proj`.`proj`,
        'admin'
    FROM
        proj
);
