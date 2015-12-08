DROP TABLE `email_template_attachment`;

INSERT INTO `notification_type` (`name`)
VALUES
    ('follow_up_created');

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
(
    SELECT
        'notification_new_follow_up_file_created',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        `proj`.`proj`,
        'New follow-up created',
        'html text email version',
        'plain text email version'
    FROM
        proj
);

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
(
    SELECT
        'follow_up_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        `proj`.`proj`,
        'Follow up subscription confirmation',
        'html text email version',
        'plain text email version'
    FROM
        proj
);

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
(
    SELECT
        'follow_up_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        `proj`.`proj`,
        'Follow up subscription confirmation for new user',
        'html text email version',
        'plain text email version'
    FROM
        proj
);


INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    FROM `email_template`
    WHERE `name` = 'notification_new_follow_up_file_created'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    FROM `email_template`
    WHERE `name` = 'follow_up_subscription_confirmation'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
     FROM `email_template`
     WHERE `name` = 'follow_up_subscription_confirmation_new_user'
);
