ALTER TABLE `input_discussion`
    ADD COLUMN `video_id` varchar(255) NULL DEFAULT NULL,
    CHANGE `body` `body` text NULL DEFAULT NULL;

INSERT INTO `email_placeholder` (`name`, `description`, `is_global`)
VALUES
    ('video_url', 'Link to the video contribution.', 0);



-- The following queries has to be run for each project in database
-- SET @project_code = 'sd';
SET @project_code = ;
INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
VALUES
    (
        (
            SELECT `id`
            FROM `email_template`
            WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code
            COLLATE utf8_unicode_ci
        ),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    ),
    (
        (
            SELECT `id`
            FROM `email_template`
            WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code
            COLLATE utf8_unicode_ci
        ),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    );
