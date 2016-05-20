INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
(
    SELECT
        'voting_participants_reminder_voter',
         (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
         `proj`.`proj`,
         'Erinnerung: Bitte bestätige {{consultation_title_short}}',
         '<p>Hello,</p>
<p>Some time ago, you took part in the voting about {{consultation_title_long}}. Unfortunately you have not confirmed your identity yet. Only after you have clicked on the confirmation link, we will be able to count your votes in the overall results. Please confirm by clicking here: {{confirmation_url}}</p>
<p>Until voting end date the voting is still open and you have the opportunity to confirm your identity or vote on any remaining contributions.</p>

<p>Viele Grüße<br/>
Das Team des ePartool</p>',
'Hello,

Some time ago, you took part in the voting about {{consultation_title_long}}. Unfortunately you have not confirmed your identity yet. Only after you have clicked on the confirmation link, we will be able to count your votes in the overall results. Please confirm by clicking here: {{confirmation_url}}

Until voting end date the voting is still open and you have the opportunity to confirm your identity or vote on any remaining contributions.

Viele Grüße
Das Team des ePartool'
    FROM
        proj
);

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
(
    SELECT
        'voting_participants_reminder_group_admin',
         (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
         `proj`.`proj`,
         'Erinnerung: Bitte bestätige die Abstimmungsteilnahme von Gruppenmitgliedern',
         '<p>Hallo,</p>
<p>Dies ist eine kleine Erinnerung, dass noch nicht alle Gruppenmitglieder bestätigt wurden, die für eure Gruppe zum Thema {{consultation_title_long}} abgestimmt haben.</p>
<p>Bitte bestätige, ob {{voter_email}} für euch abstimmungsberechtigt ist.</p>
<p>Ja, diese Person gehört zu unserer Gruppe:
{{confirmation_url}}</p>
<p>Nein, diese Person gehört nicht zu unserer Gruppe:
{{rejection_url}}</p>
<p>Viele Grüße<br/>
Das Team des ePartool</p>',
        'Hallo,

Dies ist eine kleine Erinnerung, dass noch nicht alle Gruppenmitglieder bestätigt wurden, die für eure Gruppe zum Thema {{consultation_title_long}} abgestimmt haben.

Bitte bestätige, ob {{voter_email}} für euch abstimmungsberechtigt ist.

Ja, diese Person gehört zu unserer Gruppe:
{{confirmation_url}}

Nein, diese Person gehört nicht zu unserer Gruppe:
{{rejection_url}}

Viele Grüße
Das Team des ePartool'
    FROM
        proj
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_voter'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_voter'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'rejection_url')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_voter'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_short')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_voter'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_voter'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'voter_email')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'rejection_url')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_short')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);

INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
(
    SELECT
        `id`,
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    FROM `email_template`
    WHERE `name` = 'voting_participants_reminder_group_admin'
);
