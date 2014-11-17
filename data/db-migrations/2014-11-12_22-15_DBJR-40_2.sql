INSERT INTO `notification_type` (`name`)
VALUES
    ('input_discussion_contribution_created');

ALTER TABLE `email_template` CHANGE `name` `name` varchar(100) NOT NULL;

UPDATE `email_template` SET `name` = 'question_subscription_confirmation' WHERE `name` = 'subscription_confirmation';
UPDATE `email_template` SET `name` = 'question_subscription_confirmation_new_user' WHERE `name` = 'subscription_confirmation_new_user';
