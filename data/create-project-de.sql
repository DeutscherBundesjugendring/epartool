SET collation_connection = 'utf8mb4_unicode_ci';
SET @project_code = 'xx';
SET @project_name = 'ePartool (default)';
SET @locale = 'de_DE';

INSERT INTO `proj` (`proj`, `title`, `vot_q`, `locale`, `license`, `motto`, `description`, `contact_www`, `contact_name`, `contact_email`) VALUES
    (
        @project_code,
        @project_name,
        'Wie wichtig findest Du diesen Beitrag für die weitere politische Diskussion zum Thema?',
        'de_DE',
        (SELECT `number` FROM `license` WHERE `number` = 1 AND `locale` = @locale),
        'Hier könnt ihr eine kurze Erklärung über diese Beteiligungsplattform eintragen. Die Beschreibung darf auch mehrzeilig sein.',
        'Beteiligung mit dem ePartool',
        '',
        '',
        ''
    );

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
VALUES
    (
        'password_reset',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Passwort neu vergeben',
        '<p>Hallo {{to_name}},</p>\n<p>du möchtest dein Passwort zurücksetzen oder neu vergeben. Um ein neues Kennwort festzulegen, klicke bitte auf folgenden Link:<br /><a href="{{password_reset_url}}">{{password_reset_url}}</a></p>',
        'Hallo {{to_name}},\ndu möchtest dein Passwort zurücksetzen oder neu vergeben. Um ein neues Kennwort festzulegen, klicke bitte auf folgenden Link:\n\n{{password_reset_url}}'
    ),
    (
        'input_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Beteiligungsrunde „{{consultation_title_short}}“: Bitte Einträge bestätigen',
        '<p>Hallo {{to_name}},</p>
<p>danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.</p>
<p>Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
<p>Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:<br />
<a href="{{rejection_url}}">{{rejection_url}}</a></p>
<p>Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen. Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns. Bei Rückfragen stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>
<h3>Übersicht über Eure Beiträge zur Beteiligungsrunde „{{consultation_title_long}}“</h3>
{{inputs_html}}',
        'Hallo {{to_name}},
danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.
Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:
{{confirmation_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:
{{rejection_url}}
Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen.
Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns.
Bei Rückfragen stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.
Viele Grüße
Das Team des ePartool
==============================================================================
Übersicht über Eure Beiträge zur Beteiligungsrunde
„{{consultation_title_long}}“
==============================================================================
{{inputs_text}}'
    ),
    (
        'input_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Beteiligungsrunde „{{consultation_title_short}}“: Bitte Einträge bestätigen',
        '<p>Hallo {{to_name}},</p>
<p>danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.</p>
<p>Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
<p>Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:<br />
<a href="{{rejection_url}}">{{rejection_url}}</a></p>
<p>Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen. Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns. Bei Rückfragen stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.</p>
<p>Ein Kennwort für deinen neuen Zugang kannst du über die Funktion »Passwort vergessen« neu erstellen.</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>
<h3>Übersicht über Eure Beiträge zur Beteiligungsrunde „{{consultation_title_long}}“</h3>
{{inputs_html}}',
        'Hallo {{to_name}},
danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.
Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:
{{confirmation_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:
{{rejection_url}}
Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen.
Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns.
Bei Rückfragen stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.
Ein Kennwort für deinen neuen Zugang kannst du über die Funktion »Passwort vergessen« neu erstellen.
Viele Grüße
Das Team des ePartool
==============================================================================
Übersicht über Eure Beiträge zur Beteiligungsrunde
„{{consultation_title_long}}“
==============================================================================
{{inputs_text}}'
    ),
    (
        'voting_confirmation_single',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Vielen Dank fürs Abstimmen! Bitte bestätige deine Teilnahme!',
'<p>Hallo {{to_email}},</p>
<p>vielen Dank für deine Teilnahme an der Abstimmung zur Beteiligungsrunde „{{consultation_title_long}}“. Damit wir sicherstellen können, dass wirklich du selbst abgestimmt hast, bitten wir dich, deine Teilnahme über den untenstehenden Linkzu bestätigen oder abzulehnen.</p>
<p>Hier kannst du deine Beiträge bestätigen oder ablehnen:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste deines Browsers ein. Drücke anschließend die Eingabetaste. Wir freuen uns über dein Interesse und werden dich nach Abschluss der Beteiligungsrunde per E-Mail über die Ergebnisse informieren.</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_email}},
vielen Dank für deine Teilnahme an der Abstimmung zur Beteiligungsrunde „{{consultation_title_long}}“.
Damit wir sicherstellen können, dass wirklich du selbst abgestimmt hast, bitten wir dich, deine Teilnahme über den untenstehenden Link zu bestätigen oder abzulehnen.
Hier kannst du deine Beiträge bestätigen oder ablehnen:
{{confirmation_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste deines Browsers ein. Drücke anschließend die Eingabetaste.
Wir freuen uns über dein Interesse und werden dich nach Abschluss der Beteiligungsrunde per E-Mail über die Ergebnisse informieren.
Viele Grüße
Das Team des ePartool'
    ),
    (
        'voting_confirmation_group',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Jemand hat  für euch abgestimmt - bitte bestätigen!',
        '<p>Hallo {{to_email}},</p>
<p>im Rahmen eurer Teilnahme an der Beteiligungsrunde „{{consultation_title_long}}“ hat jemand neu für eure Gruppe abgestimmt. Bitte bestätige, ob „{{voter_email}}“ für euch abstimmungsberechtigt ist.</p>
<p>Ja, diese Person gehört zu unserer Gruppe:<br />
{{confirmation_url}}</p>
<p>Nein, diese Person gehört nicht zu unserer Gruppe:<br />
{{rejection_url}}</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_email}},
im Rahmen eurer Teilnahme an der Beteiligungsrunde „{{consultation_title_long}}“ hat jemand neu für eure Gruppe abgestimmt. Bitte bestätige, ob „{{voter_email}}“ für euch abstimmungsberechtigt ist.
Ja, diese Person gehört zu unserer Gruppe:
{{confirmation_url}}
Nein, diese Person gehört nicht zu unserer Gruppe:
{{rejection_url}}
Viele Grüße
Das Team des ePartool'
    ),
    (
        'voting_invitation_single',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Beteiligungsrunde „{{consultation_title_short}}“: Jetzt abstimmen!',
        '<p>Hallo {{to_name}},</p>
<p>du hast an der Beteiligungsrunde zu „{{consultation_title_long}}“ teilgenommen. Noch einmal herzlichen Dank für deine Beiträge! In der zweiten Phase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb haben du und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage eurer Bewertungen werden wir am Ende die Zusammenfassung erstellen. Die Abstimmung erfolgt anonym.</p>
<p>Hier geht’s los:<br />
{{voting_url}}</p>
<p>Sollten technische Probleme auftreten oder Fragen aufkommen, stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
du hast an der Beteiligungsrunde zu „{{consultation_title_long}}“ teilgenommen. Noch einmal herzlichen Dank für deine Beiträge!
In der zweiten Phase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb haben du und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage eurer Bewertungen werden wir am Ende die Zusammenfassung erstellen.
Die Abstimmung erfolgt anonym.
Hier geht’s los:
{{voting_url}}
Sollten technische Probleme auftreten oder Fragen aufkommen, stehen wir dir gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.
Viele Grüße
Das Team des ePartool'
    ),
    (
        'voting_invitation_group',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Beteiligungsrunde „{{consultation_title_short}}“: Jetzt abstimmen!',
        '<p>Hallo {{to_name}},</p>
<p>ihr habt euch an der Beteiligungsrunde zu „{{consultation_title_long}}“ als Gruppe beteiligt. Noch einmal herzlichen Dank für eure Beiträge!</p>
<p>In der Abstimmungsphase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb habt ihr und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage aller Bewertungen werden wir am Ende die Zusammenfassung erstellen.</p>
<p>Du wurdest als Kontaktperson für diese Gruppe eingetragen.</p>
<p>Was ist nun deine Aufgabe?</p>
<ol>
<li>Eure Gruppe zählt wegen ihrer Größe zur Kategorie {{group_category}} Teilnehmenden und hat damit bei dieser Beteiligungsrunde ein Gewicht von {{voting_weight}}. Das bedeutet, egal wie viele Leute für eure Gruppe teilnehmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet. Das heißt, ihr könnt frei entscheiden, wie viele Personen für eure Gruppe an der 2. Phase teilnehmen sollen: eine, zwölf, dreiundfünfzig, hundert oder mehr!</li>
<li>Leite denjenigen, die für eure Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.</li>
<li>Damit du als Kontaktpersonen den Überblick behältst, wer sich für eure Gruppe beteiligt, und um Missbrauch zu vermeiden, musst du anschließend bestätigen, dass die Personen, die teilgenommen haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.</li>
<li>Am Ende des Abstimmungszeitraums werdet ihr von uns selbstverständlich über das Endergebnis informiert.</li>
</ul>
<p>Der Zugangslink für Ihre/eure Gruppe lautet:<br />
{{voting_url}}</p>
<p>***</p>
<p>Vorschlag für ein Anschreiben an die Mitglieder deiner Gruppe:</p>
<p>Wir haben an der Beteiligungsrunde „{{consultation_title_long}}“ teilgenommen. Bis {{voting_phase_end}} haben nun alle Teilnehmenden die Möglichkeit, online darüber abzustimmen, welche der Beiträge aus ihrer Sicht besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.</p>
<p>Macht mit und stimmt mit ab. Hier geht’s los:<br />
<p>{{voting_url}}</p>
<p>***</p>
<p>Sollten technische Probleme auftreten oder Fragen aufkommen, stehen wir euch gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
ihr habt euch an der Beteiligungsrunde zu „{{consultation_title_long}}“ als Gruppe beteiligt. Noch einmal herzlichen Dank für eure Beiträge!
In der Abstimmungsphase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb habt ihr und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage aller Bewertungen werden wir am Ende die Zusammenfassung erstellen.
Du wurdest als Kontaktperson für diese Gruppe eingetragen.
Was ist nun deine Aufgabe?
1. Eure Gruppe zählt wegen ihrer Größe zur Kategorie {{group_category}} Teilnehmenden und hat damit bei dieser Beteiligungsrunde ein Gewicht von {{voting_weight}}. Das bedeutet, egal wie viele Leute für eure Gruppe teilnehmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet. Das heißt, ihr könnt frei entscheiden, wie viele Personen für eure Gruppe an der 2. Phase teilnehmen sollen: eine, zwölf, dreiundfünfzig, hundert oder mehr!
2. Leite denjenigen, die für eure Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.
3. Damit du als Kontaktpersonen den Überblick behältst, wer sich für eure Gruppe beteiligt, und um Missbrauch zu vermeiden, musst du anschließend bestätigen, dass die Personen, die teilgenommen haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.
4. Am Ende des Abstimmungszeitraums werdet ihr von uns selbstverständlich über das Endergebnis informiert.
Der Zugangslink für Ihre/eure Gruppe lautet:
{{voting_url}}
***
Vorschlag für ein Anschreiben an die Mitglieder deiner Gruppe:
Wir haben an der Beteiligungsrunde „{{consultation_title_long}}“ teilgenommen. Bis {{voting_phase_end}} haben nun alle Teilnehmenden die Möglichkeit, online darüber abzustimmen, welche der Beiträge aus ihrer Sicht besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.
Macht mit und stimmt mit ab. Hier geht’s los:
{{voting_url}}
***
Sollten technische Probleme auftreten oder Fragen aufkommen, stehen wir euch gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter xxx / xxx xx xx xx.
Viele Grüße
Das Team des ePartool'
    ),
    (
        'question_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige deine automatische Benachrichtigung für neue Beiträge',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige die automatische Benachrichtigung für neue Beiträge zur Frage „{{question_text}}“, indem du auf folgenden Link klickst. Erst dann sind die automatischen Benachrichtigungen aktiv.</p>
<p>{{confirmation_url}}</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

bitte bestätige die automatische Benachrichtigung für neue Beiträge zur Frage „{{question_text}}“, indem du auf folgenden Link klickst. Erst dann sind die automatischen Benachrichtigungen aktiv.
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'question_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige die Registrierung für automatische Benachrichtigungen',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Beiträgen zu „{{question_text}}“ und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:</p>
<p>{{confirmation_url}}</p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Beiträgen zu „{{question_text}}“ und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:
{{confirmation_url}}
Viele Grüße
Das Team des ePartool'
    ),
    (
        'notification_new_input_created',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Neuer Beitrag vorhanden',
        '<p>Hallo {{to_name}},</p>
<p>Du bist für die automatische Benachrichtigung bei neuen Beiträgen eingetragen. Es gibt Neuigkeiten!</p>
<p>Um die neuen Beiträge anzusehen, klicke hier:</p>
<a href="{{website_url}}">{{website_url}}</a></p>
<p>Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:<br />
<a href="{{unsubscribe_url}}">{{unsubscribe_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

Du bist für die automatische Benachrichtigung bei neuen Beiträgen eingetragen. Es gibt Neuigkeiten!

Um die neuen Beiträge anzusehen, klicke hier:
{{website_url}}

Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:
{{unsubscribe_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'input_discussion_contrib_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige deinen Diskussionsbeitrag als neue_r Nutzer_in',
        '<p>Hallo {{to_name}},</p>
<p>bitte klicke auf den Bestätigungslink unten, um deinen Diskussionsbeitrag und den damit verbundenen Zugang zu bestätigen:</p>
<p>Dein Beitrag:<br />
{{contribution_text}}<br />
<a href="{{video_url}}">{{video_url}}</a></p>
<p>Hier bestätigen:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
bitte klicke auf den Bestätigungslink unten, um deinen Diskussionsbeitrag und den damit verbundenen Zugang zu bestätigen:

Dein Beitrag:
==========================================================================
{{contribution_text}}
{{video_url}}
==========================================================================
Hier bestätigen:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'input_discussion_contrib_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige deinen Diskussionsbeitrag ',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige deinen Diskussionsbeitrag:</p>
<p>{{contribution_text}}<br />
{{video_url}}</p>
<p>Zum Bestätigen hier klicken:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

bitte bestätige deinen Diskussionsbeitrag:
==========================================================================
{{contribution_text}}
{{video_url}}
==========================================================================

Zum Bestätigen hier klicken:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'notification_new_input_discussion_contrib_created',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Neuer Diskussionsbeitrag wurde erstellt',
        '<p>Hallo {{to_name}},</p>
<p>es gab einen neuen Diskussionsbeitrag:</p>
<p>{{website_url}}</p>
<p>Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:<br />
<a href="{{unsubscribe_url}}">{{unsubscribe_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
es gab einen neuen Diskussionsbeitrag:
{{website_url}}

Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:
{{unsubscribe_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'input_discussion_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige die automatische Benachrichtigung für Diskussionbeiträge',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Diskussionsbeiträgen und denn dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Diskussionsbeiträgen und denn dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'input_discussion_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Bitte bestätige die automatische Benachrichtigung für Diskussionsbeiträge',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige, dass du künftig über Diskussionsbeiträge zu diesem Ursprungsbeitrag informiert werden möchtest:</p>
<p><strong>{{input_thes}}</strong><br />
<em>{{input_expl}}</em></p>

<p>Zur Bestätigung klicke bitte hier:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

bitte bestätige, dass du künftig über Diskussionsbeiträge zu diesem Ursprungsbeitrag informiert werden möchtest:
==========================================================================
{{input_thes}}
{{input_expl}}
==========================================================================

Zur Bestätigung klicke bitte hier:
{{confirmation_url}}

Viele Grüße
Das Team des ePartool'
    ),
   (
        'notification_new_follow_up_file_created',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'Neue Reaktion oder Wirkung vorhanden',
        '<p>Hallo {{to_name}},</p>
<p>Du bist für die automatische Benachrichtigung bei neuen Reaktionen oder Wirkungen eingetragen. Es gibt Neuigkeiten!</p>
<p>Um die neuen Reaktionen anzusehen, klicke hier:<br />
<a href="{{website_url}}">{{website_url}}</a></p>
<p>Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:<br />
<a href="{{unsubscribe_url}}">{{unsubscribe_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},

Du bist für die automatische Benachrichtigung bei neuen Reaktionen oder Wirkungen eingetragen. Es gibt Neuigkeiten!
Um die neuen Reaktionen anzusehen, klicke hier:
{{website_url}}

Um dich von den automatischen Benachrichtigungen abzumelden, klicke hier:
{{unsubscribe_url}}

Viele Grüße
Das Team des ePartool'
    ),
    (
        'follow_up_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'Bitte bestätige die automatische Benachrichtigung bei neuen Reaktionen',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige, dass du künftig über neue Reaktionen und Wirkungen zu „{{consultation_title_long}}“ automatisch benachrichtigt werden willst. Klicke hierfür auf folgenden Bestätigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
         bitte bestätige, dass du künftig über neue Reaktionen und Wirkungen zu „{{consultation_title_long}}“ automatisch benachrichtigt werden willst. Klicke hierfür auf folgenden Bestätigungslink:
         {{confirmation_url}}
         Viele Grüße
         Das Team des ePartool'
    ),
    (
        'follow_up_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'Bitte bestätige die Registrierung für automatische Benachrichtigungen über Reaktionen',
        '<p>Hallo {{to_name}},</p>
<p>bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Reaktionen zu „{{consultation_title_long}}“ und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Reaktionen zu „{{consultation_title_long}}“ und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:
{{confirmation_url}}
Viele Grüße
Das Team des ePartool'
    ),
    (
    'voting_participants_reminder_voter',
     (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
     @project_code,
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
    ),
    (
    'voting_participants_reminder_group_admin',
     (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
     @project_code,
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
    );


INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
VALUES
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='password_reset_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_html')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_text')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_html')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_text')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voter_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_weight')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='group_category')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='unsubscribe_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='unsubscribe_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_thes')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_expl')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_thes')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_expl')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'unsubscribe_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_voter' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_voter' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_voter' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_voter' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_voter' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voter_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_participants_reminder_group_admin' AND `project_code` = @project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    );



INSERT INTO `footer` (`proj`, `text`) VALUES (@project_code, '');
INSERT INTO `footer` (`proj`, `text`) VALUES (@project_code, '');
INSERT INTO `footer` (`proj`, `text`) VALUES (@project_code, '<p>Das Tool für ePartizipation</p>\r\n\r\n<p><a href=\"https://tooldoku.dbjr.de\" target=\"_blank\" title=\"Link zur ePartool-Entwicklungswebsite\"><img alt=\"ePartool-Logo\" height=\"35\" src=\"{{BASE_URL}}/www/media/folders/misc/epartool_logo.png\" width=\"140\" /></a></p>\r\n');
INSERT INTO `footer` (`proj`, `text`) VALUES (@project_code, '<p><strong>Name</strong></p>\r\n\r\n<p>Adresse<br />\r\nPLZ, Ort</p>\r\n\r\n<p><a href=\"https://www.dbjr.de/\">www.dbjr.de (ersetzen)</a></p>\r\n');

INSERT INTO `articles`
    (`kid`, `proj`, `desc`, `is_showed`, `ref_nm`, `artcl`, `sidebar`, `parent_id`)
VALUES
    (NULL,  @project_code,   'Datenschutz',  1,    'privacy',  '<h1>Datenschutz</h1>\r\n\r\n<p><span style="color:#008080;">Genau wie der Deutsche Bundesjugendring nimmt ihr den Schutz personenbezogener Daten sicher sehr ernst. So wollt ihr sicher auch, dass die Teilnehmenden wissen, wann ihr welche Daten erhebt und wie ihr sie verwendet. Einigt euch im Vorfeld auf Maßnahmen, die sicherstellen, dass die Vorschriften über den Datenschutz sowohl von euch selbst als auch von externen Dienstleistenden beachtet werden.</span></p>\r\n\r\n<p><span style="color:#008080;">Hier findet ihr unseren Text zum Datenschutz, an dem ihr euch gerne orientieren könnt:</span></p>\r\n\r\n<h3><br />\r\nWelche Daten werden gesammelt und wie werden sie weiterverwendet?</h3>\r\n\r\n<p>Die einzige Voraussetzung für die Teilnahme an einer Online-Beteiligungsrunde unter <a href="http://abc.de" target="_blank">Link zu eurer ePartool-Seite</a> ist eine funktionierende <strong>E-Mail-Adresse</strong>. Diese wird nicht veröffentlicht und auch nicht an Dritte weitergegeben. Sie wird allein dazu genutzt,</p>\r\n\r\n<ul>\r\n  <li>um euch einen Link zuzuschicken, mit dem ihr eure Beiträge bestätigt (Verifizierung);</li>\r\n    <li>damit ihr zu einem späteren Zeitpunkt noch einmal auf eure Beiträge zugreifen könnt;</li>\r\n    <li>um mit euch Kontakt aufzunehmen, sollten eure Beiträge z.B. nicht richtig übermittelt worden zu sein;</li>\r\n    <li>um euch die Informationen für die Teilnahme an einer Abstimmung zukommen zu lassen;</li>\r\n   <li>um euch – sofern ihr das wollt - über die Ergebnisse einer Beteiligungsrunde und  die darauf folgenden Reaktionen zu informieren.</li>\r\n</ul>\r\n\r\n<p>Die <strong>Passwörter</strong>, die mit der Bestätigungsmail verschickt werden, werden vom System automatisiert erstellt und nie im Klartext gespeichert. Aus diesem Grund können Passwörter nicht wieder hergestellt, sondern nur neu vergeben werden.</p>\r\n\r\n<p>Die <strong>Eingabe weiterer Daten</strong>, wie Name, Alter und Gruppengröße, erfolgt <strong>freiwillig</strong>. Diese Daten dienen dazu, uns einen Überblick zu geben, wer an der Beteiligungsrunde teilgenommen hat.</p>\r\n\r\n<p>Während des Eintragens werden die <strong>IP-Adresse</strong> eures Internetzugriffs und der von euch verwendete <strong>Internetbrowser</strong> erfasst. Diese Daten werden allerdings nur wenige Tage gespeichert und dienen dazu, euch bei Unterbrechungen den späteren Zugriff auf schon eingetragene Texte zu ermöglichen sowie Spamrobots auszuschließen.</p>\r\n\r\n<p>Die Funktion „Unterstützen“ von anderen Beiträgen generiert aus eurer IP-Adresse und dem verwendeten Browser eine Art Quersumme („Hash“), damit jede_r einen Beitrag nur einmal „unterstützen“ kann. Eine Rückverfolgung zu eurem Rechner ist damit nicht möglich.</p>\r\n\r\n<p>Die Daten, die beim Zugriff auf das Internetangebot <a href="http://abc.de" target="_blank">Link zu eurer ePartool-Seite</a> protokolliert worden sind, werden vom Deutschen Bundesjugendring nur an Dritte übermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nicht-kommerziellen oder zu kommerziellen Zwecken erfolgt nicht.</p>\r\n\r\n<p><strong>Bitte beachtet</strong>:<br />\r\nDie Datenübertragung im Internet kann Sicherheitslücken aufweisen. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich. Wir sind aber darum bemüht, die Hürden möglichst hoch zu setzen.</p>\r\n\r\n<p>Die Nutzung des Internetangebots <a href="http://abc.de" target="_blank">Link zu eurer ePartool-Seite</a> kann deshalb über eine verschlüsselte https-Verbindung erfolgen. Wir setzen hierzu jeweils aktuelle SSL-Zertifikate ein.</p>\r\n\r\n<p><br />\r\n<strong>Noch Fragen?</strong><br />\r\nDann schreibt uns unter <a href="mailto:abc@d.de">EMAIL</a> oder ruft an unter TELEFON.</p>\r\n\r\n<p> </p>\r\n', '', NULL),
    (NULL,  @project_code,   'Kontakt',  1,    'contact',  '<h1><a id="oben" name="oben"></a>Kontakt</h1>\r\n\r\n<p><a id="kontakt" name="kontakt"></a></p>\r\n\r\n<p>PROJEKTNAME</p>\r\n\r\n<p>ADRESSE</p>\r\n\r\n<p>Telefon:<br />\r\nTelefax:<br />\r\nE-Mail:   <br />\r\nInternet: </p>\r\n\r\n<h4> </h4>\r\n\r\n<h2><a id="oben" name="oben"></a>Impressum</h2>\r\n\r\n<h3>Herausgeber dieser Website</h3>\r\n\r\n<h3>Verantwortlich</h3>\r\n\r\n<h3>Redaktion</h3>\r\n\r\n<h3>Adresse</h3>\r\n\r\n<p> </p>\r\n\r\n<h3>Kontakt</h3>\r\n\r\n<p>E-Mail:</p>\r\n\r\n<p>Internet:</p>\r\n\r\n<div> </div>\r\n\r\n<div>\r\n<h2><a id="gap" name="gap"></a>Bewusste Entscheidung zur Nutzung des Gender_Gap</h2>\r\n\r\n<p>Das „_“ ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen möchten unabhängig von ihrer Geschlechtsidentität. Ein Gender_Gap wird eingefügt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentitäten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache wäre dies sonst nur durch Umschreibungen möglich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschließliche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.</p>\r\n</div>\r\n\r\n<h2> </h2>\r\n\r\n<h2><a id="Rechtliches" name="Rechtliches"></a>Rechtliche Hinweise</h2>\r\n\r\n<p>Alle Angaben unseres Internetangebotes wurden sorgfältig geprüft. Wir bemühen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollständig anzubieten. Dennoch ist das Auftreten von Fehlern nicht völlig auszuschließen. Eine Garantie für die Vollständigkeit, Richtigkeit und letzte Aktualität kann daher nicht übernommen werden.</p>\r\n\r\n<p><br />\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ankündigung verändern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.<br />\r\n<br />\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und übernimmt keinerlei Haftung für Schäden, u.a. für direkte, indirekte, zufällige, vorab konkret zu bestimmende oder Folgeschäden, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.<br />\r\n<br />\r\nDer Betreiber übernimmt keine Verantwortung für die Inhalte und die Verfügbarkeit von Websites Dritter, die über externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdrücklich von allen Inhalten, die möglicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten verstoßen.<br />\r\n<br />\r\nSofern innerhalb des Internetangebotes die Möglichkeit zur Eingabe persönlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdrücklich freiwilliger Basis.</p>\r\n\r\n<h3> </h3>\r\n\r\n<h3><a id="textnutzung" name="textnutzung"></a>Nutzungsrechte für Texte und Dateien</h3>\r\n\r\n<p>Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.</p>\r\n\r\n<p>Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von <a href="http://tool.ichmache-politik.de" target="_blank">tool.ichmache-politik.de</a> als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erwünscht, dass die Quelle (<a href="http://tool.ichmache-politik.de" target="_blank">tool.ichmache-politik.de</a>) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdrücklich untersagt.</p>\r\n\r\n<p>Nutzungsrechte für Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verfügung gestellt, sofern nicht anders gekennzeichnet.</p>\r\n\r\n<h4> </h4>\r\n\r\n<h3><a id="datenschutz" name="datenschutz"></a>Datenschutzhinweise</h3>\r\n\r\n<p>Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir möchten, dass jede_r weiß, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Maßnahmen getroffen, die sicherstellen, dass die Vorschriften über den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.<br />\r\n<br />\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, können auch Änderungen dieser Datenschutzerklärung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerklärung ab und zu erneut durchzulesen.</p>\r\n\r\n<p> </p>\r\n\r\n<h3><a id="zugriff www" name="zugriff www"></a>Zugriff auf das Internetangebot</h3>\r\n\r\n<p>Jeder Zugriff auf das Internetangebot <a href="http://tool.ichmache-politik.de" target="_blank">tool.ichmache-politik.de</a> wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:</p>\r\n\r\n<ul>\r\n <li>Informationen über die Seite, von der aus die Datei angefordert wurde</li>\r\n <li>Name der abgerufenen Datei</li>\r\n <li>Datum und Uhrzeit des Abrufs</li>\r\n   <li>übertragene Datenmenge</li>\r\n    <li>Meldung, ob der Abruf erfolgreich war</li>\r\n</ul>\r\n\r\n<p>Die gespeicherten Daten werden ausschließlich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen möglich, die mit der technischen, kaufmännischen oder redaktionellen Betreuung der Server befasst sind.</p>\r\n\r\n<p> </p>\r\n\r\n<h3><a id="datenweitergabe" name="datenweitergabe"></a>Weitergabe personenbezogener Daten an Dritte</h3>\r\n\r\n<p>Daten, die beim Zugriff auf das Internetangebot <a href="http://tool.ichmache-politik.de" target="_blank">tool.ichmache-politik.de</a> protokolliert worden sind, werden an Dritte nur übermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.</p>\r\n\r\n<p>Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschließlich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdrücklich nicht an Dritte weiter.</p>\r\n\r\n<h3><br />\r\n<a id="u18" name="u18"></a>Schutz von Minderjährigen</h3>\r\n\r\n<p>Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns übermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.</p>\r\n\r\n<p>Für weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verfügung:</p>\r\n\r\n<p>Michael Scholl</p>\r\n\r\n<p>Telefon: +49 (0)30.400 40-412<br />\r\nTelefax: +49 (0)30.400 40-422</p>\r\n\r\n<p>E-Mail: <a href="mailto:info@dbjr.de">info@dbjr.de</a></p>\r\n\r\n<p><a href="http://tool.ichmache-politik.de/privacy" target="_blank">» Weitere Informationen zum Datenschutz</a></p>\r\n\r\n<p> </p>\r\n\r\n<h3><a id="design" name="design"></a>Gestaltung</h3>\r\n\r\n<p><a href="http://www.die-projektoren.de" target="_blank">DIE.PROJEKTOREN – FARYS & RUSCH GBR</a>  </p>\r\n\r\n<p> </p>\r\n\r\n<h3><a id="progammierung" name="progammierung"></a>Programmierung</h3>\r\n\r\n<ul>\r\n    <li>Anne Bohnet</li>\r\n    <li><a href="http://www.digitalroyal.de" target="_blank">Digital Royal GmbH </a></li>\r\n  <li>Tim Schrock</li>\r\n    <li><a href="http://www.seitenmeister.com" target="_blank">seitenmeister</a> </li>\r\n <li>Synerigc</li>\r\n   <li><a href="http://www.xima.de" target="_blank">xima media GmbH </a></li>\r\n</ul>\r\n\r\n<p> </p>\r\n\r\n<h3><a id="software" name="software"></a>Verwendete Software</h3>\r\n\r\n<p>Das Internetangebot <sup>e</sup>Partool basiert auf quelloffener Software. Wir verwenden u.a. den </p>\r\n\r\n<ul>\r\n   <li><a href="https://httpd.apache.org/" target="_blank">Apache Webserver</a> mit <a href="http://php.net/" target="_blank">PHP</a> und <a href="http://mysql.com/" target="_blank">MySQL-Datenbanken</a></li>\r\n   <li><a href="http://framework.zend.com/" target="_blank">Zend PHP Framework</a></li>\r\n    <li><a href="http://twitter.github.io/bootstrap/" target="_blank">Bootstrap</a></li>\r\n    <li><a href="http://ckeditor.com/" target="_blank">CKEditor</a></li>\r\n    <li><a href="http://www.yaml.de/" target="_blank">YAML</a></li>\r\n</ul>\r\n\r\n<p>Berlin im Mai 2013</p>\r\n\r\n<p><a href="#oben"> Nach oben</a></p>\r\n',   '<h4><a href="#kontakt">Kontakt</a></h4>\r\n\r\n<h4><a href="#impressum">Impressum</a></h4>\r\n\r\n<h4><a href="#gap">Nutzung des Gender_Gap</a></h4>\r\n\r\n<h4><a href="#Rechtliches">Rechtliche Hinweise</a></h4>\r\n\r\n<p><a href="#textnutzung">Nutzungsrechte für Texte und Dateien</a></p>\r\n\r\n<p><a href="#datenschutz">Datenschutzhinweise</a></p>\r\n\r\n<p><a href="#zugriff www">Zugriff auf das Internetangebot</a></p>\r\n\r\n<p><a href="#datenweitergabe">Weitergabe personenbezogener Daten an Dritte</a></p>\r\n\r\n<p><a href="#u18">Schutz von Minderjährigen</a></p>\r\n\r\n<p><a href="#design">Gestaltung</a></p>\r\n\r\n<p><a href="#progammierung">Programmierung</a></p>\r\n\r\n<p><a href="#software">Verwendete Software</a></p>\r\n', NULL),
    (NULL,  @project_code,   'Häufige Fragen',   1,    'faq',  '<h1>Häufig gestellte Fragen</h1>\r\n\r\n<p><span style="color:#008080;">Häufig kommen Fragen rund um die Beteiligungsrunden und das<sup> e</sup>Partool auf. Auf dieser Seite könnt ihr einige bereits im Voraus beantworten. Hier findet ihr eine Auswahl potentieller Fragen und teilweise auch Antworten, die ihr nach eurern Wünschen ergänzen und anpassen könnt.</span></p>\r\n\r\n<p> </p>\r\n\r\n<ol>\r\n   <li>\r\n  <h2><a id="Worum" name="Worum"></a>Worum geht es hier eigentlich?</h2>\r\n  </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; "><span style="color:#008080;">Hier kommt eine kurzbeschreibung eures Projekts hin. Unsere lautete so:</span></p>\r\n\r\n<p style="margin-left: 40px; ">Das Bundesministerium für Familie, Senioren, Frauen und Jugend (BMFSFJ) hat 2011 einen Prozess zur Entwicklung einer Eigenständigen Jugendpolitik (EiJP) gestartet. Ein solcher Prozess ist undenkbar ohne die Beteiligung junger Menschen – also undenkbar ohne EUCH! Darum wird die Jugendbeteiligung am Prozess über Ichmache>Politik initiiert und abgesichert. Das ist ein Projekt des Deutschen Bundesjugendrings (DBJR).<br />\r\n<br />\r\nIchmache>Politik ermöglicht es jungen Menschen zwischen 12 und 27 Jahren in unterschiedlichen Kontexten (Gruppe, Verband, Schule, etc.) oder als Einzelpersonen, sich vor Ort mit den Themen und Ergebnissen des EiJP-Prozesses auseinanderzusetzen sowie diese online über unser ePartool zu bewerten und zu qualifizieren. Über das ePartool werden eure Beiträge gesammelt und später von allen Teilnehmenden gewichtet. Die Resultate gehen schließlich in die Entscheidungsfindung des EiJP-Prozesses ein: Politische Akteur_innen beschäftigen sich bewusst und ernsthaft mit den Ergebnissen der Jugendbeteiligung und geben euch schließlich ein Feedback über die Wirkung Eures Engagements. Junge Menschen – also ihr – wirken somit an der Entwicklung einer Eigenständigen Jugendpolitik mit. Wichtig ist hierbei, dass ihr nicht nur Impulsgeber_innen sein sollt, sondern vor allem Beurteilungsinstanz für die inhaltlichen Ergebnisse im Prozessverlauf seid.</p>\r\n\r\n<p style="margin-left: 40px; ">Mehr zum Projekt und zum Prozess erfahrt ihr unter >> <a href="/about#what" target="_blank">WAS WIR MIT EUCH MACHEN.</a></p>\r\n\r\n<p style="margin-left: 40px; "> </p>\r\n\r\n<ol>\r\n   <li value="2">\r\n  <h2><a name="Wer kann sich"></a>Wer kann sich hier beteiligen?</h2>\r\n   </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; "><span style="color:#008080;">Na, wer denn?</span></p>\r\n\r\n<ol start="3">\r\n    <li>\r\n  <h2><a name="Worauf sollte ich"></a>Worauf sollte ich beim Eintragen der Beiträge achten?</h2>\r\n   </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Bitte formuliert eure Beiträge möglichst knapp und beschränkt euch pro Box auf eine Idee bzw. einen Gedanken. Das Eingabefeld für eure Beiträge ist begrentzt auf max. 300 Buchstaben. Für Erklärungen, weitergehende Infos usw. nutzt bitte die jeweilige Erläuterungsbox.</p>\r\n\r\n<p style="margin-left: 40px; "> </p>\r\n\r\n<ol start="4">\r\n <li>\r\n  <h2><a name="Müssen alle"></a>Müssen alle Fragen beantwortet werden?</h2>\r\n   </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Nein, ihr könnt frei entscheiden, ob ihr eine, zwei, drei oder alle Fragen beantworten möchtet.<br />\r\n </p>\r\n\r\n<ol start="5">\r\n    <li>\r\n  <h2><a name="Müssen die"></a>Müssen die Fragen der Reihenfolge nach beantwortet werden?</h2>\r\n    </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Nein, ihr könnt die Reihenfolge, in der ihr die Fragen beantwortet, frei wählen und dabei ganz einfach zwischen den Fragen hin und her wechseln.<br />\r\n </p>\r\n\r\n<ol start="6">\r\n   <li>\r\n  <h2><a name="Wie kann ich"></a>Wie kann ich einen Eintrag von mir löschen?</h2>\r\n  </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Ihr könnt einen Eintrag löschen, indem ihr den Text in der entsprechenden Box löscht.<br />\r\n </p>\r\n\r\n<ol start="7">\r\n <li>\r\n  <h2><a name="Warum muss ich"></a>Warum muss ich eine E-Mail-Adresse angeben?</h2>\r\n </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Die E-Mail-Adresse ist notwendig, damit wir sicherstellen können, dass die Einträge von einer realen Person stammen und nicht von einem Spamversender. An die von euch angegebene E-Mail-Adresse schicken wir automatisch eine E-Mail mit einem Bestätigungslink, den ihr aktivieren müsst, indem ihr darauf klickt oder ihn in euren Internetbrowser kopiert. Erst dann werden eure Beiträge endgültig gespeichert und auf der Website veröffentlicht.<br />\r\nMit der E-Mail erhaltet ihr gleichzeitig ein Passwort. Dieses benötigt ihr, wenn ihr zu einem späteren Zeitpunkt Einträge ergänzen oder bearbeiten möchtet. Ihr solltet unsere E-Mail also für einige Tage aufbewahren!<br />\r\nWir sichern zu, dass E-Mail-Adressen weder an Dritte weitergegeben noch für andere Zwecke als für diese Jugendbeteiligung genutzt werden.<br />\r\nWeitere Informationen zum Datenschutz: <a href="/privacy" target="_blank">>> hier</a>.<br />\r\n </p>\r\n\r\n<ol start="8">\r\n  <li>\r\n  <h2><a name="Welche Daten"></a>Welche Daten werden gesammelt und wie werden sie weiterverwendet?</h2>\r\n </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Ausführliche Infos zum Datenschutz findet ihr <a href="/privacy" target="_blank">>> hier</a>.</p>\r\n\r\n<p style="margin-left: 40px; "> </p>\r\n\r\n<ol start="9">\r\n   <li>\r\n  <h2><a name="Was passiert"></a>Was passiert mit meinen Beiträgen, nachdem ich auf den Bestätigungslink geklickt habe?</h2>\r\n  </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Das Projektbüro überprüft alle Einträge und behält sich vor, diese wenn nötig zu sperren – z.B. wenn sie diskriminierende Inhalte haben. Alle geprüften Beiträge werden auf tool.ichmache-politik.de veröffentlicht und können von anderen Besucher_innen gelesen werden. Euer Name oder eure E-Mail-Adresse sind dabei nicht sichtbar.<br />\r\n </p>\r\n\r\n<ol start="10">\r\n <li>\r\n  <h2><a name="Wie kann ich sehen"></a>Wie kann ich sehen, was andere eingetragen haben?</h2>\r\n   </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Ihr könnt euch die Beiträge der anderen zu den jeweiligen Fragen ansehen, indem ihr auf der Startseite auf die Box „Beiträge“ in der jeweiligen Beteiligungsrunde klickt.</p>\r\n\r\n<ol start="11">\r\n  <li>\r\n  <h2><br />\r\n  <a name="Was muss ich tun"></a>Was muss ich tun, um über die Ergebnisse der Beteiligung informiert zu werden?</h2>\r\n </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Am Ende des Online-Fragebogens könnt ihr angeben, dass ihr über die Ergebnisse der Beteiligung informiert werden möchtet. Die Informationen schicken wir dann an die von euch angegebene E-Mail-Adresse. Solltet ihr diesen Service nicht mehr wünschen, könnt ihr ihn jederzeit abbestellen. Darüber hinaus könnt ihr<br />\r\n </p>\r\n\r\n<ol start="12">\r\n   <li>\r\n  <h2><a name="Mein Passwort"></a>Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?</h2>\r\n    </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Klickt im oberen rechten Teil der Website auf „Login“. Dort könnt ihr ein neues Passwort oder einen neuen Zugangslink anfordern, indem ihr auf "Passwort vergessen" klickt.<br />\r\n </p>\r\n\r\n<ol start="13">\r\n  <li>\r\n  <h2><a name="Weshalb"></a>Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?</h2>\r\n   </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Wer verschiedene Internetbrowser verwendet, dem werden beim Design der Website Unterschiede auffallen. Das liegt daran, dass unterschiedliche Browserversionen unterschiedliche Anforderungen an die Programmierung stellen. Die Unterschiede haben aber keine Auswirkungen auf die Funktionen der Website. Wir arbeiten daran, die Design-Unterschiede so gering wie möglich zu halten. Vorerst empfehlen wir euch, möglichst aktuelle Versionen von Firefox, Chrome, Opera oder Safari zu verwenden.<br />\r\n </p>\r\n\r\n<ol start="14">\r\n    <li>\r\n  <h2><strong><a name="Warum findet sich"></a></strong>Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?</h2>\r\n\r\n  <p><span style="color:#008080;">So machen wir, von Ichmache>Politik das. Ihr auch?</span><br />\r\n    <br />\r\n    Bevor die Abstimmung gestartet wird, geht die Redaktion alle Beiträge noch einmal durch. Drei Punkte sind hierbei wichtig:</p>\r\n   </li>\r\n</ol>\r\n\r\n<ul>\r\n    <li><strong>Gibt es Beiträge mit demselben Inhalt, derselben Aussage, Forderung oder Idee?</strong> Wenn ja, fassen wir die Beiträge, die einen gleichen oder ähnlichen Inhalt haben, zusammen, damit ihr nicht immer wieder über Ähnliches abstimmen müsst. Dabei wird immer festgehalten, welche Beiträge zusammengeflossen sind oder auch wo genau Teilaspekte gelandet sind.<br />\r\n    </li>\r\n   <li><strong>Enthält ein Beitrag mehrere unterschiedliche Aussagen, Forderungen oder Ideen? </strong>Wenn ja, "splitten" wir den Beitrag überlicherweise auf, damit die anderen besser über die einzelnen Aspekte abstimmen können.<br />\r\n   </li>\r\n   <li><strong>Sind die Beiträge für jeden verständlich formuliert? </strong>Wenn nicht, achten wir darauf, dass z.B. in euren Beiträgen verwendete Fremdwörter übersetzt werden und der Satzbau nicht zu verschachtelt ist, damit die Aussage im Vordergrund steht und für jede_n nachvollziehbar ist.<strong> </strong></li>\r\n</ul>\r\n\r\n<p style="margin-left: 40px;">Wir bemühen uns bei der redaktionellen Arbeit darum, so nah wie möglich, an euren Formulierungen zu bleiben und inhaltlich nichts zu verändern.</p>\r\n\r\n<p style="margin-left: 40px;">Wenn ihr genauere Auskünfte dazu haben wollt, was mit eurem Beitrag passiert ist, ruft uns einfach an (030 400 40 441). Zukünftig soll das im <sup>e</sup>Partool sichtbar gemacht werden. Dieses wird stetig weiterentwickelt, der große Relaunch steht vor der Tür.<br />\r\n </p>\r\n\r\n<ol start="15">\r\n <li>\r\n  <h2><a name="Warum schreibt"></a>Warum schreibt ihr „jede_r“ oder „Besucher_innen“?</h2>\r\n  </li>\r\n</ol>\r\n\r\n<p style="margin-left: 40px; ">Das „_“ ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen möchten unabhängig von ihrer Geschlechtsidentität. Ein Gender_Gap wird eingefügt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentitäten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache wäre dies sonst nur durch Umschreibungen möglich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschließliche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.</p>\r\n\r\n<p style="margin-left: 40px; "> </p>\r\n\r\n<h2><a name="Ihr findet"></a>Ihr findet hier keine Antwort auf eure Frage?</h2>\r\n\r\n<p style="margin-left: 40px; ">Dann wendet euch an das PROJEKTNAME Projektbüro<br />\r\nE-Mail:<br />\r\nTelefon:</p>\r\n', '<ol>\r\n <li>\r\n  <p><a href="#Worum">Worum geht es hier eigentlich?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Wer kann sich">Wer kann sich hier beteiligen?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Worauf sollte ich">Worauf sollte ich beim Eintragen der Beiträge achten?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Müssen alle">Müssen alle Fragen beantwortet werden?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Müssen die">Müssen die Fragen der Reihenfolge nach beantwortet werden?</a></p>\r\n </li>\r\n <li>\r\n  <p><a href="#Wie kann ich">Wie kann ich einen Eintrag von mir löschen?</a></p>\r\n   </li>\r\n <li>\r\n  <p><a href="#Warum muss ich">Warum muss ich eine E-Mail-Adresse angeben?</a></p>\r\n  </li>\r\n <li>\r\n  <p><a href="#Welche Daten">Welche Daten werden gesammelt und wie werden sie weiterverwendet?</a></p>\r\n  </li>\r\n <li>\r\n  <p><a href="#Was passiert">Was passiert mit meinen Beiträgen, nachdem ich auf den Bestätigungslink geklickt habe?</a></p>\r\n   </li>\r\n <li>\r\n  <p><a href="#Wie kann ich sehen">Wie kann ich sehen, was andere eingetragen haben?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Was muss ich tun">Was muss ich tun, um über die Ergebnisse der Beteiligung informiert zu werden?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Worum">Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?</a></p>\r\n </li>\r\n <li>\r\n  <p><a href="#Weshalb">Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Warum findet sich">Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?</a></p>\r\n    </li>\r\n <li>\r\n  <p><a href="#Warum schreibt">Warum schreibt ihr „jede_r“ oder „Besucher_innen“?</a></p>\r\n   </li>\r\n</ol>\r\n\r\n<p><a href="#Ihr findet">Ihr findet hier keine Antwort auf eure Frage?</a></p>\r\n', NULL),
    (NULL,  @project_code,   'Was wir mit euch machen',  1,    'about',    '<p><em>Hier erfahren die Teilnehmenden mehr darüber, </em><a href="#what"><strong>›››WAS</strong></a><em> es mit eurem Projekt auf sich hat. – Sie sollen herausfinden, wer </em><a href="#us"><strong>›››WIR</strong></a><em>, also IHR, seid. – Sie können nachlesen wen ihr ansprechen wollt </em><a href="#you"><strong>›››MIT EUCH</strong></a><em> – Und sie können sich kurz und knapp darüber informieren, wie sie mit</em><a href="#vision"><strong>›››MACHEN</strong></a><em> können.</em><br />\r\n </p>\r\n\r\n<h1><a name="what"></a>Was</h1>\r\n\r\n<h1><br />\r\n<br />\r\n<a name="us"></a> Wir</h1>\r\n\r\n<p> </p>\r\n\r\n<h2> </h2>\r\n\r\n<p> </p>\r\n\r\n<h1><a name="you"></a> Mit euch</h1>\r\n\r\n<p> </p>\r\n\r\n<h1><br />\r\n<a name="vision"></a> Machen</h1>\r\n\r\n<p> </p>\r\n\r\n<h2>1. Ideen, Vorschläge und Forderungen entwickeln!</h2>\r\n\r\n<p>Setzt euch vor Ort, in eurer Gruppe oder auch alleine mit den Themen der Beteiligungsrunde auseinander. Ihr entscheidet dabei, wie ihr das genau machen wollt. Ob ihr dazu eine kleine Diskussion im Freundeskreis durchführt, einen Workshop darauf organisiert oder eine größere Aktion startet, bleibt euch überlassen. Ebenso, ob ihr euch alle Fragen vornehmt oder nur ein oder zwei.</p>\r\n\r\n<p>Findet heraus, wo das Thema in eurer Umgebung überall eine Rolle spielt, diskutiert im Verband, in der Schule, mit Freunden oder mit Verantwortlichen und bildet euch eine Meinung. Wir sammeln sowohl Einzelmeinungen als auch Ergebnisse aus Workshops, Gesprächen am Lagerfeuer oder thematischen Gruppenstunden. Selbstverständlich könnt ihr auch Teile aus fertigen Beschlüssen verwenden, z. B. Positionspapiere eures Verbandes oder eurer Initiative.</p>\r\n\r\n<h2><br />\r\n2. Beitragen!</h2>\r\n\r\n<p>Wenn eure Ideen, Vorschläge und Forderungen fertig sind, tragt ihr sie hier online anhand der Fragen ein. Dort könnt ihr auch nachgucken, was andere bereits geschrieben haben. So können die Ergebnisse eurer Arbeit weitreichendere Bedeutung bekommen und Jugendpolitik in Deutschland und der EU beeinflussen.</p>\r\n\r\n<p>Bitte formuliert eure Beiträge möglichst knapp und beschränkt euch pro Box auf eure „Kernbotschaft“ (max. 300 Buchstaben). Für Erklärungen, weitergehende Infos usw. nutzt bitte die jeweilige Erläuterungsbox.</p>\r\n\r\n<h2><br />\r\n3. Abstimmen!</h2>\r\n\r\n<p>Nach dem Ende der Beitragsphase seid ihr ein zweites Mal gefragt! Gemeinsam mit den anderen Teilnehmer_innen der Beteiligungsrunde könnt ihr darüber abstimmen, welche der Beiträge eurer Meinung nach besonders wichtig für die weitere politische Diskussion in der EU und hier in Deutschland sind. Wie viele für eure Gruppe an der Abstimmung teilnehmen, ob alle, nur einige oder ein_e Gruppenvertreter_in könnt ihr frei entscheiden.</p>\r\n\r\n<p>Um euch das Abstimmen zu vereinfachen und die Beiträge auf eine abstimmbare Zahl zu reduzieren, fassen wir inhaltlich identische Beiträge redaktionell zusammen bzw. unterteilen facettenreiche Positionen in ihre einzelnen Aspekte. Dabei bemühen wir uns darum, so nah wie möglich am Inhalt eures Beitrags zu bleiben und nichts zu verfälschen. Ihr könnt dabei nachvollziehen, aus welchen eurer Antworten sich ein zur Abstimmung stehender Beitrag zusammensetzt.  </p>\r\n\r\n<p>Durch die Abstimmung bestimmt ihr darüber, was weiterkommt und was nicht. Die Beiträge mit der höchsten Punktzahl fließen am Ende in die Zusammenfassung ein.</p>\r\n\r\n<p>Übrigens: Aus Zeitgründen müssen wir manchmal auf die Abstimmung verzichten. In dem Fall berücksichtigen wir alle Beiträge in der Zusammenfassung.</p>\r\n\r\n<h2><br />\r\n4. Wirkung erzielen!</h2>\r\n\r\n<p>Wir sorgen dafür, dass eure Ideen, Vorschläge und Forderungen an die Zuständigen weitergeleitet werden und damit in die politischen Diskussionen einfließen. Einige politische Akteur_innen hier in Deutschland haben verbindlich zugesagt, sich mit den Ergebnissen auseinanderzusetzen und euch eine Rückmeldung dazu zu geben. Weitere fragen wir je nach Thema an. Wenn Zwischenergebnisse und Reaktionen vorliegen, informieren wir euch darüber.</p>\r\n', '', NULL),
    (NULL,  @project_code,   'Impressum',    1,    'imprint',  '<h1><a id="oben" name="oben"></a>Impressum</h1>\r\n\r\n<h2><br />\r\nHerausgeber dieser Website</h2>\r\n\r\n<h3><br />\r\nVerantwortlich</h3>\r\n\r\n<h3>Redaktion</h3>\r\n\r\n<h3>Adresse</h3>\r\n\r\n<h3>Kontakt</h3>\r\n\r\n<p>Telefon:<br />\r\nTelefax:<br />\r\nE-Mail:<br />\r\nInternet:</p>\r\n\r\n<p> </p>\r\n\r\n<h2><a id="Rechtliches" name="Rechtliches"></a>Rechtliche Hinweise</h2>\r\n\r\n<p>Alle Angaben unseres Internetangebotes wurden sorgfältig geprüft. Wir bemühen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollständig anzubieten. Dennoch ist das Auftreten von Fehlern nicht völlig auszuschließen. Eine Garantie für die Vollständigkeit, Richtigkeit und letzte Aktualität kann daher nicht übernommen werden.</p>\r\n\r\n<p><br />\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ankündigung verändern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.<br />\r\n<br />\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und übernimmt keinerlei Haftung für Schäden, u.a. für direkte, indirekte, zufällige, vorab konkret zu bestimmende oder Folgeschäden, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.<br />\r\n<br />\r\nDer Betreiber übernimmt keine Verantwortung für die Inhalte und die Verfügbarkeit von Websites Dritter, die über externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdrücklich von allen Inhalten, die möglicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten verstoßen.<br />\r\n<br />\r\nSofern innerhalb des Internetangebotes die Möglichkeit zur Eingabe persönlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdrücklich freiwilliger Basis.<br />\r\n </p>\r\n\r\n<h3><a id="textnutzung" name="textnutzung"></a>Nutzungsrechte für Texte und Dateien</h3>\r\n\r\n<p>Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.</p>\r\n\r\n<p>Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von <a href="http://www.strukturierter-dialog.de/mitmachen">www.strukturierter-dialog.de/mitmachen</a> als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erwünscht, dass die Quelle (<a href="http://www.strukturierter-dialog.de/mitmachen">www.strukturierter-dialog.de/mitmachen</a>) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdrücklich untersagt.</p>\r\n\r\n<p>Nutzungsrechte für Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verfügung gestellt, sofern nicht anders gekennzeichnet.<br />\r\n </p>\r\n\r\n<h3><a id="datenschutz" name="datenschutz"></a>Datenschutzhinweise</h3>\r\n\r\n<p>Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir möchten, dass jede_r weiß, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Maßnahmen getroffen, die sicherstellen, dass die Vorschriften über den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.<br />\r\n<br />\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, können auch Änderungen dieser Datenschutzerklärung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerklärung ab und zu erneut durchzulesen.<br />\r\n </p>\r\n\r\n<h3><a id="zugriff www" name="zugriff www"></a>Zugriff auf das Internetangebot</h3>\r\n\r\n<p>Jeder Zugriff auf das Internetangebot <a href="http://www.strukturierter-dialog.de/mitmachen">www.strukturierter-dialog.de/mitmachen</a> wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:</p>\r\n\r\n<ul>\r\n  <li>Informationen über die Seite, von der aus die Datei angefordert wurde</li>\r\n <li>Name der abgerufenen Datei</li>\r\n <li>Datum und Uhrzeit des Abrufs</li>\r\n   <li>übertragene Datenmenge</li>\r\n    <li>Meldung, ob der Abruf erfolgreich war</li>\r\n</ul>\r\n\r\n<p>Die gespeicherten Daten werden ausschließlich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen möglich, die mit der technischen, kaufmännischen oder redaktionellen Betreuung der Server befasst sind.<br />\r\n </p>\r\n\r\n<h3><a id="datenweitergabe" name="datenweitergabe"></a>Weitergabe personenbezogener Daten an Dritte</h3>\r\n\r\n<p>Daten, die beim Zugriff auf <a href="http://www.strukturierter-dialog.de/mitmachen">www.strukturierter-dialog.de/mitmachen</a> protokolliert worden sind, werden an Dritte nur übermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.</p>\r\n\r\n<p>Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschließlich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdrücklich nicht an Dritte weiter.</p>\r\n\r\n<h3><br />\r\n<a id="u18" name="u18"></a>Schutz von Minderjährigen</h3>\r\n\r\n<p>Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns übermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.</p>\r\n\r\n<p>Für weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verfügung:</p>\r\n\r\n<p>Michael Scholl<br />\r\nTelefon: +49 (0)30.400 40-412<br />\r\nTelefax: +49 (0)30.400 40-422<br />\r\nE-Mail: <a href="mailto:info@dbjr.de">info@dbjr.de</a></p>\r\n\r\n<p><a href="http://tool.ichmache-politik.de/privacy" target="_blank"><strong>» Weitere Informationen zum Datenschutz</strong></a><br />\r\n </p>\r\n\r\n<p> </p>\r\n\r\n<h2>Gestaltung und Realisierung</h2>\r\n\r\n<h3><a id="design" name="design"></a>Design</h3>\r\n\r\n<ul>\r\n    <li><a href="http://www.die-projektoren.de" target="_blank">DIE.PROJEKTOREN – FARYS & RUSCH GBR</a> </li>\r\n</ul>\r\n\r\n<h3><a id="progammierung" name="progammierung"></a>Programmierung</h3>\r\n\r\n<ul>\r\n <li><a href="http://bohnetlingua.de/">Anne Bohnet</a></li>\r\n    <li><a href="http://www.digitalroyal.de" target="_blank">Digital Royal GmbH </a></li>\r\n  <li>Tim Schrock</li>\r\n    <li><a href="http://www.seitenmeister.com" target="_blank">seitenmeister</a> </li>\r\n <li>Synergic</li>\r\n   <li><a href="http://www.xima.de" target="_blank">xima media GmbH </a></li>\r\n</ul>\r\n\r\n<h3><a id="software" name="software"></a>Verwendete Software</h3>\r\n\r\n<p>Das Internetangebot <sup>e</sup>Partool basiert auf quelloffener Software. Wir verwenden unter anderem</p>\r\n\r\n<ul>\r\n   <li><a href="https://httpd.apache.org/" target="_blank">Apache Webserver</a> mit <a href="http://php.net/" target="_blank">PHP</a> und <a href="http://mysql.com/" target="_blank">MySQL-Datenbanken</a></li>\r\n <li><a href="http://twitter.github.io/bootstrap/" target="_blank">Bootstrap</a></li>\r\n    <li><a href="http://ckeditor.com/" target="_blank">CKEditor</a></li>\r\n    <li><a href="http://jquery.com/" target="_blank">jQuery</a></li>\r\n    <li><a href="http://framework.zend.com/" target="_blank">Zend PHP Framework</a></li>\r\n</ul>\r\n\r\n<p><br />\r\nBerlin im Mai 2013</p>\r\n\r\n<p> </p>\r\n<p&gtDas ePartool wird seit 2011 beim Deutschen Bundesjugendring entwickelt. Gefördert vom Bundesministerium für Familie, Senioren, Frauen und Jugend.</p&gt\r\n', '', NULL);


INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`) VALUES
(
    'help-text-home',
    '<p>Diese Seite begrüßt alle Besucher_innen. Oben leiten euch die Buttons <strong>„Was – wir – mit euch – machen“</strong> auf eine Unterseite mit detaillierten Informationen zum Projekt/ Prozess und den Phasen des Beteiligungstools.</p>
<p><strong>Login:</strong> Falls ihr schon einmal auf der Seite wart und an einer Beteiligungsrunde teilgenommen habt, könnt ihr euch mit eurer E-Mail-Adresse und eurem Passwort anmelden und unter <strong>„Alle meine Beiträge ansehen“</strong> dort weitermachen, wo ihr das letzte Mal aufgehört habt, oder die Beteiligung an einer neuen Runde beginnen. Wenn ihr eurer Passwort vergessen habt, könnt ihr euch ganz unkompliziert ein neues zuschicken lassen, indem ihr auf <strong>„Passwort vergessen“ </strong>klickt.</p>
<p> </p>
<p>Im Hauptteil der Seite findet ihr eine Übersicht über laufende oder kürzlich beendete Beteiligungsrunden, die aktuellste an oberster Stelle. In horizontal nebeneinander stehenden schwarzen Balken sind Informationen zur Runde, die Fragen sowie die einzelnen Phasen der Beteiligungsrunde zum Anklicken aufgelistet. Die Phasen <strong>„Beiträge“</strong> und <strong>„Abstimmung“</strong> haben zumeist bestimmte Laufzeiten, in denen dann zusätzlich ein gelber Button <strong>„<em>Jetzt mitmachen!</em>“</strong> auf dem schwarzen Balken leuchtet. Ist ein Balken grau, so ist diese Phase noch nicht aktiv und kann nicht ausgewählt werden.</p>
<p> </p>
<p>Unter dem Hauptteil der Seite befindet sich eine Leiste mit Links zu besonderen Seiten (<strong><em>Datenschutz</em>, <em>Impressum</em> etc.</strong>). Darunter befindet sich der Fußbereich mit Informationen zu Projektträger, Förderer usw. Beides wird genau wie der Kopfbereich auf allen Unterseiten angezeigt.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-info',
    '<p>Unter <strong>„Infos“</strong> gibt es die wichtigsten Informationen zur entsprechenden Beteiligungsrunde auf einen Blick.</p>
     <p>Links auf der Seite befinden sich weitere Buttons, z.B. <strong>„<em>So geht’s</em>“</strong> und <strong>„<em>Infos zum Thema“</em>, </strong>die euch auf weitere Unterseiten führen und den Beteiligungsprozess sowie den thematischen Hintergrund der jeweiligen Beteiligungsrunde und ihrer Fragen erläutern.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-question',
    '<p>Der Balken <strong>„<em>Fragen</em>“</strong> führt euch auf eine Unterseite, auf der alle Fragen der Beteiligungsrunde sowie kurze Erläuterungen dazu aufgeführt sind.</p>
     <p>Wenn die Beitragsphase der Beteiligungsrunde aktiv ist, seht ihr außerdem rechts neben jeder Frage den Button <strong>„<em>Beitrag verfassen</em>“ </strong>– hier könnt ihr sofort loslegen.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-input',
    '<p>Durch Klicken auf den Balken <strong>„Beiträge“</strong> kommt ihr auf diese Unterseite, auf der Felder mit den Fragen der Beteiligungsrunde und jeweils abgegebene Beiträge angezeigt werden.</p>
     <p>Wenn die Beitragsphase der Beteiligungsrunde aktiv ist, seht ihr außerdem rechts neben jeder Frage den Button <strong>„<em>Beitrag verfassen</em>“ </strong>– hier könnt ihr sofort loslegen.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-voting',
    '<p>In der Abstimmungsphase erhaltet ihr (als Einzelperson von den Admins oder als Gruppenmitglieder von eurem_eurer Gruppenverantwortlichen) eine <strong>E-Mail mit Einladung zur Abstimmung und einem Zugangscode</strong>. Dieser Zugangscode ist entweder Einzelpersonen oder Gruppen zugeordnet – Gruppen haben ein stärkeres Stimmgewicht.</p>
     <p><strong>Wichtig: </strong>Mit dem Zugangscode einer Gruppe können beliebig viele junge Menschen teilnehmen, das Stimmengewicht der gesamten Gruppe ist vorher festgelegt und verteilt sich auf alle Abstimmenden. Gruppenverantwortliche müssen euch freischalten, können aber nicht sehen, wie ihr abstimmt, nur, dass ihr abgestimmt habt!</p>
     <p>Zum Abstimmen braucht ihr neben diesem Code eure E-Mail-Adresse.</p>
     <p>Zum Abstimmen klickt ihr bei laufendem Abstimmungszeitraum auf <strong>„Jetzt abstimmen!“</strong> in der entsprechenden Beteiligungsrunde. Dadurch gelangt ihr nach Eingabe von E-Mail-Adresse und Zugangscode auf eine Übersichtsseite mit allen Fragen der Beteiligungsrunde sowie allen in den Beiträgen verwendeten Schlagwörtern.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-followup',
    '<p>Mit der Abstimmung ist die Beteiligung noch nicht zu Ende, denn ihr möchtet ja auch sehen, was dabei herauskommt. Hier werden die Reaktionen anschaulich dargestellt:</p>
     <p>In der <strong>Übersichtsseite der Reaktionen</strong> könnt ihr sehen, wie eure Beiträge in der Abstimmung gewertet wurden und ob sie Teil der Ergebniszusammenfassung wurden. Ihr seht außerdem, von wem es welche Reaktionen auf eine Beteiligungsrunde bzw. auf eure einzelnen Beiträge gab. In welche Beschlusspapiere sind die Beiträge eingegangen, welche politischen Debatten schlossen sich an die Beteiligungsrunde an? All das veranschaulicht das Tool durch Bereitstellung aller Dokumente und Links zu Websites, Videos etc.</p>
     <p>Unter diesem Überblick gelangt ihr über die Fragen zu <strong>„Reaktionen und Feedback auf einzelne Beiträge“:</strong> Hier könnt ihr bestimmte Beiträge sowie die Reaktionen darauf im Einzelnen grafisch aufbereitet nachverfolgen.</p>',
    @project_code,
    'default'
),
(
    'help-text-login',
    '<p>Wenn ihr schon einmal teilgenommen habt oder Adminberechtigungen für eine oder mehrere Beteiligungsrunden besitzt, so habt ihr nach dem Login die Möglichkeit, von der Startseite zu den Beteiligungsrunden in den <strong>geschützten Bereich</strong> zu wechseln. Dazu klickt ihr oben rechts neben der Anzeige eures Login-Namens auf den Pfeil, hier bekommt ihr die Optionen „<strong>Alle meine Beiträge ansehen“</strong>, <strong>„Gruppenmitglieder ansehen“</strong> und <strong>„Logout“</strong> angezeigt. Administrator_innen können zudem in den<strong> „Adminbereich“</strong> wechseln.</p>',
    @project_code,
    'default'
),

('help-text-admin-consultation-voting-preparation', 'Sample voting-preparation text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-permissions', 'Sample voting-permissions text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-participants', 'Sample voting-participants text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-results', 'Sample voting-results text.', @project_code, 'admin'),
('help-text-admin-consultation-follow-up', 'Sample follow-up text.', @project_code, 'admin'),
('help-text-admin-consultation-follow-up-snippets', 'Sample follow-up-snippets text.', @project_code, 'admin'),
('help-text-admin-question', 'Sample question text.', @project_code, 'admin'),
('help-text-admin-contribution', 'Sample contribution text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-general', 'Sample consultation settings general page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-participants-data', 'Sample consultation settings participants data page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-voting', 'Sample consultation settings voting page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-phases', 'Sample consultation settings phases page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-groups', 'Sample consultation settings groups page text.', @project_code, 'admin');

