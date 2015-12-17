UPDATE `email_template_has_email_placeholder`
SET
    `email_placeholder_id` = (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
WHERE
    `email_placeholder_id` IN (SELECT `id` FROM `email_placeholder` WHERE `name` = 'unsubscribe_url')
    AND `email_template_id` IN (
        SELECT `id`
        FROM `email_template`
        WHERE `name` IN ('follow_up_subscription_confirmation_new_user', 'follow_up_subscription_confirmation')
    );


UPDATE `email_template`
SET
    `subject` = 'Neue Reaktion oder Wirkung vorhanden',
    `body_html` = '<p>Hallo {{to_name}},</p>
<p>Du bist f&uuml;r die automatische Benachrichtigung bei neuen Reaktionen oder Wirkungen eingetragen. Es gibt Neuigkeiten!</p>
<p>Um die neuen Reaktionen anzusehen, klicke hier:<br />
<a href="{{website_url}}">{{website_url}}</a></p>
<p>Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:<br />
<a href="{{unsubscribe_url}}">{{unsubscribe_url}}</a></p>
<p>Viele Gr&uuml;&szlig;e<br />
Das Team des ePartool</p>',
    `body_text` =
'Hallo {{to_name}},

Du bist für die automatische Benachrichtigung bei neuen Reaktionen oder Wirkungen eingetragen. Es gibt Neuigkeiten!

Um die neuen Reaktionen anzusehen, klicke hier:
{{website_url}}

Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:
{{unsubscribe_url}}

Viele Grüße
Das Team des ePartool'
WHERE
    `name` = 'notification_new_follow_up_file_created';


UPDATE `email_template`
SET
    `subject` = 'Bitte bestätige die automatische Benachrichtigung bei neuen Reaktionen',
    `body_html` = '<p>Hallo {{to_name}},</p>
<p>bitte best&auml;tige, dass du k&uuml;nftig &uuml;ber neue Reaktionen und Wirkungen zu „{{consultation_title_long}}“ automatisch benachrichtigt werden willst. Klicke hierf&uuml;r auf folgenden Best&auml;tigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Gr&uuml;&szlig;e<br />
Das Team des ePartool</p>',
    `body_text` =
'Hallo {{to_name}},

bitte bestätige, dass du künftig über neue Reaktionen und Wirkungen zu „{{consultation_title_long}}“ automatisch benachrichtigt werden willst. Klicke hierfür auf folgenden Bestätigungslink:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
WHERE
    `name` = 'follow_up_subscription_confirmation';


UPDATE `email_template`
SET
    `subject` = 'Bitte bestätige die Registrierung für automatische Benachrichtigungen über Reaktionen',
    `body_html` = '<p>Hallo {{to_name}},</p>
<p>bitte best&auml;tige deine Registrierung als neue_r Empf&auml;nger_in f&uuml;r automatische Benachrichtigungen bei neuen Reaktionen zu „{{consultation_title_long}}“ und den dazugeh&ouml;rigen Nutzeraccount. Klicke hierf&uuml;r auf folgenden Best&auml;tigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Gr&uuml;&szlig;e<br />
Das Team des ePartool</p>',
    `body_text` =
'Hallo {{to_name}},

bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Reaktionen zu „{{consultation_title_long}}“ und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
WHERE
    `name` = 'follow_up_subscription_confirmation_new_user';
