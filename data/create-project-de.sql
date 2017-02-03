SET collation_connection = 'utf8mb4_unicode_ci';
SET @project_code = 'xx';
SET @project_name = 'ePartool (default)';
SET @locale = 'de_DE';

INSERT INTO `proj` (`proj`, `titl_short`, `vot_q`, `locale`, `license`) VALUES
    (
        @project_code,
        @project_name,
        'Wie wichtig findest Du diesen Beitrag für die weitere politische Diskussion zum Thema?',
        'de_DE',
        (SELECT `number` FROM `license` WHERE `number` = 1 AND `locale` = @locale)
    );

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
VALUES
    (
        'password_reset',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Passwort neu vergeben',
        '<p>Hallo {{to_name}},</p>\n<p>du möchtest dein Passwort zurücksetzen oder neu vergeben. Um ein neues Kennwort festzulegen, klicke bitte auf folgenden Link:<br />{{password_reset_url}}</p>',
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
<p>bitte best&auml;tige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Diskussionsbeiträgen und denn dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Viele Grüße<br />
Das Team des ePartool</p>',
        'Hallo {{to_name}},
bitte best&auml;tige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Diskussionsbeiträgen und denn dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:
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
<p>bitte bestätige, dass du künftig über neue Reaktionen und Wirkungen zu &bdquo;{{consultation_title_long}}&ldquo; automatisch benachrichtigt werden willst. Klicke hierfür auf folgenden Bestätigungslink:<br />
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
<p>bitte bestätige deine Registrierung als neue_r Empfänger_in für automatische Benachrichtigungen bei neuen Reaktionen zu &bdquo;{{consultation_title_long}}&ldquo; und den dazugehörigen Nutzeraccount. Klicke hierfür auf folgenden Bestätigungslink:<br />
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
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
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



INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);

INSERT INTO `parameter` (`name`, `proj`) VALUES ('site.title', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('site.description', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('site.motto', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.name', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.email', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.www', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.street', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.town', @project_code);
INSERT INTO `parameter` (`name`, `proj`) VALUES ('contact.zip', @project_code);


INSERT INTO `articles`
    (`kid`, `proj`, `desc`, `is_showed`, `ref_nm`, `artcl`, `sidebar`, `parent_id`)
VALUES
    (NULL,  @project_code,   'Datenschutz',  1,    'privacy',  '&lt;h1&gt;Datenschutz&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Genau wie der Deutsche Bundesjugendring nimmt ihr den Schutz personenbezogener Daten sicher sehr ernst. So wollt ihr sicher auch, dass die Teilnehmenden wissen, wann ihr welche Daten erhebt und wie ihr sie verwendet. Einigt euch im Vorfeld auf Ma&amp;szlig;nahmen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von euch selbst als auch von externen Dienstleistenden beachtet werden.&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Hier findet ihr unseren Text zum Datenschutz, an dem ihr euch gerne orientieren k&amp;ouml;nnt:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\nWelche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/h3&gt;\r\n\r\n&lt;p&gt;Die einzige Voraussetzung f&amp;uuml;r die Teilnahme an einer Online-Beteiligungsrunde unter&amp;nbsp;&lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; ist eine funktionierende &lt;strong&gt;E-Mail-Adresse&lt;/strong&gt;. Diese wird nicht ver&amp;ouml;ffentlicht und auch nicht an Dritte weitergegeben. Sie wird allein dazu genutzt,&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n  &lt;li&gt;um euch einen Link zuzuschicken, mit dem ihr eure Beitr&amp;auml;ge best&amp;auml;tigt (Verifizierung);&lt;/li&gt;\r\n    &lt;li&gt;damit ihr zu einem sp&amp;auml;teren Zeitpunkt noch einmal auf eure Beitr&amp;auml;ge zugreifen k&amp;ouml;nnt;&lt;/li&gt;\r\n    &lt;li&gt;um mit euch Kontakt aufzunehmen, sollten eure Beitr&amp;auml;ge z.B. nicht richtig &amp;uuml;bermittelt worden zu sein;&lt;/li&gt;\r\n    &lt;li&gt;um euch die Informationen f&amp;uuml;r die Teilnahme an einer Abstimmung zukommen zu lassen;&lt;/li&gt;\r\n   &lt;li&gt;um euch &amp;ndash; sofern ihr das wollt - &amp;uuml;ber die Ergebnisse einer Beteiligungsrunde und&amp;nbsp; die darauf folgenden Reaktionen zu informieren.&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die &lt;strong&gt;Passw&amp;ouml;rter&lt;/strong&gt;, die mit der Best&amp;auml;tigungsmail verschickt werden, werden vom System automatisiert erstellt und nie im Klartext gespeichert. Aus diesem Grund k&amp;ouml;nnen Passw&amp;ouml;rter nicht wieder hergestellt, sondern nur neu vergeben werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die &lt;strong&gt;Eingabe weiterer Daten&lt;/strong&gt;, wie Name, Alter und Gruppengr&amp;ouml;&amp;szlig;e, erfolgt &lt;strong&gt;freiwillig&lt;/strong&gt;. Diese Daten dienen dazu, uns einen &amp;Uuml;berblick zu geben, wer an der Beteiligungsrunde teilgenommen hat.&lt;/p&gt;\r\n\r\n&lt;p&gt;W&amp;auml;hrend des Eintragens werden die &lt;strong&gt;IP-Adresse&lt;/strong&gt; eures Internetzugriffs und der von euch verwendete &lt;strong&gt;Internetbrowser&lt;/strong&gt; erfasst. Diese Daten werden allerdings nur wenige Tage gespeichert und dienen dazu, euch bei Unterbrechungen den sp&amp;auml;teren Zugriff auf schon eingetragene Texte zu erm&amp;ouml;glichen sowie Spamrobots auszuschlie&amp;szlig;en.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Funktion &amp;bdquo;Unterst&amp;uuml;tzen&amp;ldquo; von anderen Beitr&amp;auml;gen generiert aus eurer IP-Adresse und dem verwendeten Browser eine Art Quersumme (&amp;bdquo;Hash&amp;ldquo;), damit jede_r einen Beitrag nur einmal &amp;bdquo;unterst&amp;uuml;tzen&amp;ldquo; kann. Eine R&amp;uuml;ckverfolgung zu eurem Rechner ist damit nicht m&amp;ouml;glich.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Daten, die beim Zugriff auf das Internetangebot &lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; protokolliert worden sind, werden vom Deutschen Bundesjugendring nur an Dritte &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nicht-kommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;strong&gt;Bitte beachtet&lt;/strong&gt;:&lt;br /&gt;\r\nDie Daten&amp;uuml;bertragung im Internet kann Sicherheitsl&amp;uuml;cken aufweisen. Ein l&amp;uuml;ckenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht m&amp;ouml;glich. Wir sind aber darum bem&amp;uuml;ht, die H&amp;uuml;rden m&amp;ouml;glichst hoch zu setzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Die Nutzung des Internetangebots &lt;a href=&quot;http://abc.de&quot; target=&quot;_blank&quot;&gt;Link zu eurer ePartool-Seite&lt;/a&gt; kann deshalb &amp;uuml;ber eine verschl&amp;uuml;sselte https-Verbindung erfolgen. Wir setzen hierzu jeweils aktuelle SSL-Zertifikate ein.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\n&lt;strong&gt;Noch Fragen?&lt;/strong&gt;&lt;br /&gt;\r\nDann schreibt uns unter &lt;a href=&quot;mailto:abc@d.de&quot;&gt;EMAIL&lt;/a&gt; oder ruft an unter TELEFON.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n', '', NULL),
    (NULL,  @project_code,   'Kontakt',  1,    'contact',  '&lt;h1&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Kontakt&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;a id=&quot;kontakt&quot; name=&quot;kontakt&quot;&gt;&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;PROJEKTNAME&lt;/p&gt;\r\n\r\n&lt;p&gt;ADRESSE&lt;/p&gt;\r\n\r\n&lt;p&gt;Telefon:&lt;br /&gt;\r\nTelefax:&lt;br /&gt;\r\nE-Mail:&amp;nbsp;&amp;nbsp;&amp;nbsp;&lt;br /&gt;\r\nInternet:&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h4&gt;&amp;nbsp;&lt;/h4&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Impressum&lt;/h2&gt;\r\n\r\n&lt;h3&gt;Herausgeber dieser Website&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Verantwortlich&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Redaktion&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Adresse&lt;/h3&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;Kontakt&lt;/h3&gt;\r\n\r\n&lt;p&gt;E-Mail:&lt;/p&gt;\r\n\r\n&lt;p&gt;Internet:&lt;/p&gt;\r\n\r\n&lt;div&gt;&amp;nbsp;&lt;/div&gt;\r\n\r\n&lt;div&gt;\r\n&lt;h2&gt;&lt;a id=&quot;gap&quot; name=&quot;gap&quot;&gt;&lt;/a&gt;Bewusste Entscheidung zur Nutzung des Gender_Gap&lt;/h2&gt;\r\n\r\n&lt;p&gt;Das &amp;bdquo;_&amp;ldquo; ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen m&amp;ouml;chten unabh&amp;auml;ngig von ihrer Geschlechtsidentit&amp;auml;t. Ein Gender_Gap wird eingef&amp;uuml;gt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentit&amp;auml;ten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache w&amp;auml;re dies sonst nur durch Umschreibungen m&amp;ouml;glich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschlie&amp;szlig;liche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.&lt;/p&gt;\r\n&lt;/div&gt;\r\n\r\n&lt;h2&gt;&amp;nbsp;&lt;/h2&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;Rechtliches&quot; name=&quot;Rechtliches&quot;&gt;&lt;/a&gt;Rechtliche Hinweise&lt;/h2&gt;\r\n\r\n&lt;p&gt;Alle Angaben unseres Internetangebotes wurden sorgf&amp;auml;ltig gepr&amp;uuml;ft. Wir bem&amp;uuml;hen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollst&amp;auml;ndig anzubieten. Dennoch ist das Auftreten von Fehlern nicht v&amp;ouml;llig auszuschlie&amp;szlig;en. Eine Garantie f&amp;uuml;r die Vollst&amp;auml;ndigkeit, Richtigkeit und letzte Aktualit&amp;auml;t kann daher nicht &amp;uuml;bernommen werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ank&amp;uuml;ndigung ver&amp;auml;ndern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und &amp;uuml;bernimmt keinerlei Haftung f&amp;uuml;r Sch&amp;auml;den, u.a. f&amp;uuml;r direkte, indirekte, zuf&amp;auml;llige, vorab konkret zu bestimmende oder Folgesch&amp;auml;den, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Betreiber &amp;uuml;bernimmt keine Verantwortung f&amp;uuml;r die Inhalte und die Verf&amp;uuml;gbarkeit von Websites Dritter, die &amp;uuml;ber externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdr&amp;uuml;cklich von allen Inhalten, die m&amp;ouml;glicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten versto&amp;szlig;en.&lt;br /&gt;\r\n&lt;br /&gt;\r\nSofern innerhalb des Internetangebotes die M&amp;ouml;glichkeit zur Eingabe pers&amp;ouml;nlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdr&amp;uuml;cklich freiwilliger Basis.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&amp;nbsp;&lt;/h3&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;textnutzung&quot; name=&quot;textnutzung&quot;&gt;&lt;/a&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/h3&gt;\r\n\r\n&lt;p&gt;Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erw&amp;uuml;nscht, dass die Quelle (&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdr&amp;uuml;cklich untersagt.&lt;/p&gt;\r\n\r\n&lt;p&gt;Nutzungsrechte f&amp;uuml;r Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verf&amp;uuml;gung gestellt, sofern nicht anders gekennzeichnet.&lt;/p&gt;\r\n\r\n&lt;h4&gt;&amp;nbsp;&lt;/h4&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenschutz&quot; name=&quot;datenschutz&quot;&gt;&lt;/a&gt;Datenschutzhinweise&lt;/h3&gt;\r\n\r\n&lt;p&gt;Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir m&amp;ouml;chten, dass jede_r wei&amp;szlig;, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Ma&amp;szlig;nahmen getroffen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.&lt;br /&gt;\r\n&lt;br /&gt;\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, k&amp;ouml;nnen auch &amp;Auml;nderungen dieser Datenschutzerkl&amp;auml;rung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerkl&amp;auml;rung ab und zu erneut durchzulesen.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;zugriff www&quot; name=&quot;zugriff www&quot;&gt;&lt;/a&gt;Zugriff auf das Internetangebot&lt;/h3&gt;\r\n\r\n&lt;p&gt;Jeder Zugriff auf das Internetangebot&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n &lt;li&gt;Informationen &amp;uuml;ber die Seite, von der aus die Datei angefordert wurde&lt;/li&gt;\r\n &lt;li&gt;Name der abgerufenen Datei&lt;/li&gt;\r\n &lt;li&gt;Datum und Uhrzeit des Abrufs&lt;/li&gt;\r\n   &lt;li&gt;&amp;uuml;bertragene Datenmenge&lt;/li&gt;\r\n    &lt;li&gt;Meldung, ob der Abruf erfolgreich war&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die gespeicherten Daten werden ausschlie&amp;szlig;lich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen m&amp;ouml;glich, die mit der technischen, kaufm&amp;auml;nnischen oder redaktionellen Betreuung der Server befasst sind.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenweitergabe&quot; name=&quot;datenweitergabe&quot;&gt;&lt;/a&gt;Weitergabe personenbezogener Daten an Dritte&lt;/h3&gt;\r\n\r\n&lt;p&gt;Daten, die beim Zugriff auf das Internetangebot&amp;nbsp;&lt;a href=&quot;http://tool.ichmache-politik.de&quot; target=&quot;_blank&quot;&gt;tool.ichmache-politik.de&lt;/a&gt;&amp;nbsp;protokolliert worden sind, werden an Dritte nur &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschlie&amp;szlig;lich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdr&amp;uuml;cklich nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\n&lt;a id=&quot;u18&quot; name=&quot;u18&quot;&gt;&lt;/a&gt;Schutz von Minderj&amp;auml;hrigen&lt;/h3&gt;\r\n\r\n&lt;p&gt;Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns &amp;uuml;bermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;p&gt;F&amp;uuml;r weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verf&amp;uuml;gung:&lt;/p&gt;\r\n\r\n&lt;p&gt;Michael Scholl&lt;/p&gt;\r\n\r\n&lt;p&gt;Telefon: +49 (0)30.400 40-412&lt;br /&gt;\r\nTelefax: +49 (0)30.400 40-422&lt;/p&gt;\r\n\r\n&lt;p&gt;E-Mail:&amp;nbsp;&lt;a href=&quot;mailto:info@dbjr.de&quot;&gt;info@dbjr.de&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://tool.ichmache-politik.de/privacy&quot; target=&quot;_blank&quot;&gt;&amp;raquo; Weitere Informationen zum Datenschutz&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;design&quot; name=&quot;design&quot;&gt;&lt;/a&gt;Gestaltung&lt;/h3&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://www.die-projektoren.de&quot; target=&quot;_blank&quot;&gt;DIE.PROJEKTOREN &amp;ndash; FARYS &amp;amp; RUSCH GBR&lt;/a&gt;&amp;nbsp;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;progammierung&quot; name=&quot;progammierung&quot;&gt;&lt;/a&gt;Programmierung&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n    &lt;li&gt;Anne Bohnet&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.digitalroyal.de&quot; target=&quot;_blank&quot;&gt;Digital Royal GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n  &lt;li&gt;Tim Schrock&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.seitenmeister.com&quot; target=&quot;_blank&quot;&gt;seitenmeister&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n &lt;li&gt;Synerigc&lt;/li&gt;\r\n   &lt;li&gt;&lt;a href=&quot;http://www.xima.de&quot; target=&quot;_blank&quot;&gt;xima media GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;software&quot; name=&quot;software&quot;&gt;&lt;/a&gt;Verwendete Software&lt;/h3&gt;\r\n\r\n&lt;p&gt;Das Internetangebot&amp;nbsp;&lt;sup&gt;e&lt;/sup&gt;Partool basiert auf quelloffener Software. Wir verwenden u.a. den&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n   &lt;li&gt;&lt;a href=&quot;https://httpd.apache.org/&quot; target=&quot;_blank&quot;&gt;Apache Webserver&lt;/a&gt;&amp;nbsp;mit&amp;nbsp;&lt;a href=&quot;http://php.net/&quot; target=&quot;_blank&quot;&gt;PHP&lt;/a&gt;&amp;nbsp;und&amp;nbsp;&lt;a href=&quot;http://mysql.com/&quot; target=&quot;_blank&quot;&gt;MySQL-Datenbanken&lt;/a&gt;&lt;/li&gt;\r\n   &lt;li&gt;&lt;a href=&quot;http://framework.zend.com/&quot; target=&quot;_blank&quot;&gt;Zend PHP Framework&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://twitter.github.io/bootstrap/&quot; target=&quot;_blank&quot;&gt;Bootstrap&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://ckeditor.com/&quot; target=&quot;_blank&quot;&gt;CKEditor&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.yaml.de/&quot; target=&quot;_blank&quot;&gt;YAML&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Berlin im Mai 2013&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#oben&quot;&gt;&amp;nbsp;Nach oben&lt;/a&gt;&lt;/p&gt;\r\n',   '&lt;h4&gt;&lt;a href=&quot;#kontakt&quot;&gt;Kontakt&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#impressum&quot;&gt;Impressum&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#gap&quot;&gt;Nutzung des Gender_Gap&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;h4&gt;&lt;a href=&quot;#Rechtliches&quot;&gt;Rechtliche Hinweise&lt;/a&gt;&lt;/h4&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#textnutzung&quot;&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#datenschutz&quot;&gt;Datenschutzhinweise&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#zugriff www&quot;&gt;Zugriff auf das Internetangebot&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#datenweitergabe&quot;&gt;Weitergabe personenbezogener Daten an Dritte&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#u18&quot;&gt;Schutz von Minderj&amp;auml;hrigen&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#design&quot;&gt;Gestaltung&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#progammierung&quot;&gt;Programmierung&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#software&quot;&gt;Verwendete Software&lt;/a&gt;&lt;/p&gt;\r\n', NULL),
    (NULL,  @project_code,   'Häufige Fragen',   1,    'faq',  '&lt;h1&gt;H&amp;auml;ufig gestellte Fragen&lt;/h1&gt;\r\n\r\n&lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;H&amp;auml;ufig kommen Fragen rund um die Beteiligungsrunden und das&lt;sup&gt; e&lt;/sup&gt;Partool auf. Auf dieser Seite k&amp;ouml;nnt ihr einige bereits im Voraus beantworten. Hier findet ihr eine Auswahl potentieller Fragen und teilweise auch Antworten, die ihr nach eurern W&amp;uuml;nschen erg&amp;auml;nzen und anpassen k&amp;ouml;nnt.&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol&gt;\r\n   &lt;li&gt;\r\n  &lt;h2&gt;&lt;a id=&quot;Worum&quot; name=&quot;Worum&quot;&gt;&lt;/a&gt;Worum geht es hier eigentlich?&lt;/h2&gt;\r\n  &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Hier kommt eine kurzbeschreibung eures Projekts hin. Unsere lautete so:&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das Bundesministerium f&amp;uuml;r Familie, Senioren, Frauen und Jugend (BMFSFJ) hat 2011 einen Prozess zur Entwicklung einer Eigenst&amp;auml;ndigen Jugendpolitik (EiJP) gestartet. Ein solcher Prozess ist undenkbar ohne die Beteiligung junger Menschen &amp;ndash; also undenkbar ohne EUCH! Darum wird die Jugendbeteiligung am Prozess &amp;uuml;ber Ichmache&amp;gt;Politik initiiert und abgesichert. Das ist ein Projekt des Deutschen Bundesjugendrings (DBJR).&lt;br /&gt;\r\n&lt;br /&gt;\r\nIchmache&amp;gt;Politik erm&amp;ouml;glicht es jungen Menschen zwischen 12 und 27 Jahren in unterschiedlichen Kontexten (Gruppe, Verband, Schule, etc.) oder als Einzelpersonen, sich vor Ort mit den Themen und Ergebnissen des EiJP-Prozesses auseinanderzusetzen sowie diese online &amp;uuml;ber unser ePartool zu bewerten und zu qualifizieren. &amp;Uuml;ber das ePartool werden eure Beitr&amp;auml;ge gesammelt und sp&amp;auml;ter von allen Teilnehmenden gewichtet. Die Resultate gehen schlie&amp;szlig;lich in die Entscheidungsfindung des EiJP-Prozesses ein: Politische Akteur_innen besch&amp;auml;ftigen sich bewusst und ernsthaft mit den Ergebnissen der Jugendbeteiligung und geben euch schlie&amp;szlig;lich ein Feedback &amp;uuml;ber die Wirkung Eures Engagements. Junge Menschen &amp;ndash; also ihr &amp;ndash; wirken somit an der Entwicklung einer Eigenst&amp;auml;ndigen Jugendpolitik mit. Wichtig ist hierbei, dass ihr nicht nur Impulsgeber_innen sein sollt, sondern vor allem Beurteilungsinstanz f&amp;uuml;r die inhaltlichen Ergebnisse im Prozessverlauf seid.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Mehr zum Projekt und zum Prozess erfahrt ihr unter &amp;gt;&amp;gt; &lt;a href=&quot;/about#what&quot; target=&quot;_blank&quot;&gt;WAS WIR MIT EUCH MACHEN.&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol&gt;\r\n   &lt;li value=&quot;2&quot;&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Wer kann sich&quot;&gt;&lt;/a&gt;Wer kann sich hier beteiligen?&lt;/h2&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&lt;span style=&quot;color:#008080;&quot;&gt;Na, wer denn?&lt;/span&gt;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;3&quot;&gt;\r\n    &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Worauf sollte ich&quot;&gt;&lt;/a&gt;Worauf sollte ich beim Eintragen der Beitr&amp;auml;ge achten?&lt;/h2&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Bitte formuliert eure Beitr&amp;auml;ge m&amp;ouml;glichst knapp und beschr&amp;auml;nkt euch pro Box auf eine Idee bzw. einen Gedanken. Das Eingabefeld f&amp;uuml;r eure Beitr&amp;auml;ge ist begrentzt auf max. 300 Buchstaben. F&amp;uuml;r Erkl&amp;auml;rungen, weitergehende Infos usw. nutzt bitte die jeweilige Erl&amp;auml;uterungsbox.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;4&quot;&gt;\r\n &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;M&uuml;ssen alle&quot;&gt;&lt;/a&gt;M&amp;uuml;ssen alle Fragen beantwortet werden?&lt;/h2&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Nein, ihr k&amp;ouml;nnt frei entscheiden, ob ihr eine, zwei, drei oder alle Fragen beantworten m&amp;ouml;chtet.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;5&quot;&gt;\r\n    &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;M&uuml;ssen die&quot;&gt;&lt;/a&gt;M&amp;uuml;ssen die Fragen der Reihenfolge nach beantwortet werden?&lt;/h2&gt;\r\n    &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Nein, ihr k&amp;ouml;nnt die Reihenfolge, in der ihr die Fragen beantwortet, frei w&amp;auml;hlen und dabei ganz einfach zwischen den Fragen hin und her wechseln.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;6&quot;&gt;\r\n   &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Wie kann ich&quot;&gt;&lt;/a&gt;Wie kann ich einen Eintrag von mir l&amp;ouml;schen?&lt;/h2&gt;\r\n  &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ihr k&amp;ouml;nnt einen Eintrag l&amp;ouml;schen, indem ihr den Text in der entsprechenden Box l&amp;ouml;scht.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;7&quot;&gt;\r\n &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Warum muss ich&quot;&gt;&lt;/a&gt;Warum muss ich eine E-Mail-Adresse angeben?&lt;/h2&gt;\r\n &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Die E-Mail-Adresse ist notwendig, damit wir sicherstellen k&amp;ouml;nnen, dass die Eintr&amp;auml;ge von einer realen Person stammen und nicht von einem Spamversender. An die von euch angegebene E-Mail-Adresse schicken wir automatisch eine E-Mail mit einem Best&amp;auml;tigungslink, den ihr aktivieren m&amp;uuml;sst, indem ihr darauf klickt oder ihn in euren Internetbrowser kopiert. Erst dann werden eure Beitr&amp;auml;ge endg&amp;uuml;ltig gespeichert und auf der Website ver&amp;ouml;ffentlicht.&lt;br /&gt;\r\nMit der E-Mail erhaltet ihr gleichzeitig ein Passwort. Dieses ben&amp;ouml;tigt ihr, wenn ihr zu einem sp&amp;auml;teren Zeitpunkt Eintr&amp;auml;ge erg&amp;auml;nzen oder bearbeiten m&amp;ouml;chtet. Ihr solltet unsere E-Mail also f&amp;uuml;r einige Tage aufbewahren!&lt;br /&gt;\r\nWir sichern zu, dass E-Mail-Adressen weder an Dritte weitergegeben noch f&amp;uuml;r andere Zwecke als f&amp;uuml;r diese Jugendbeteiligung genutzt werden.&lt;br /&gt;\r\nWeitere Informationen zum Datenschutz:&amp;nbsp;&lt;a href=&quot;/privacy&quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; hier&lt;/a&gt;.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;8&quot;&gt;\r\n  &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Welche Daten&quot;&gt;&lt;/a&gt;Welche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/h2&gt;\r\n &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ausf&amp;uuml;hrliche Infos zum Datenschutz findet ihr&amp;nbsp;&lt;a href=&quot;/privacy&quot; target=&quot;_blank&quot;&gt;&amp;gt;&amp;gt; hier&lt;/a&gt;.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;9&quot;&gt;\r\n   &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Was passiert&quot;&gt;&lt;/a&gt;Was passiert mit meinen Beitr&amp;auml;gen, nachdem ich auf den Best&amp;auml;tigungslink geklickt habe?&lt;/h2&gt;\r\n  &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das Projektb&amp;uuml;ro &amp;uuml;berpr&amp;uuml;ft alle Eintr&amp;auml;ge und beh&amp;auml;lt sich vor, diese wenn n&amp;ouml;tig zu sperren &amp;ndash; z.B. wenn sie diskriminierende Inhalte haben. Alle gepr&amp;uuml;ften Beitr&amp;auml;ge werden auf tool.ichmache-politik.de ver&amp;ouml;ffentlicht und k&amp;ouml;nnen von anderen Besucher_innen gelesen werden. Euer Name oder eure E-Mail-Adresse sind dabei nicht sichtbar.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;10&quot;&gt;\r\n &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Wie kann ich sehen&quot;&gt;&lt;/a&gt;Wie kann ich sehen, was andere eingetragen haben?&lt;/h2&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Ihr k&amp;ouml;nnt euch die Beitr&amp;auml;ge der anderen zu den jeweiligen Fragen ansehen, indem ihr auf der Startseite auf die Box &amp;bdquo;Beitr&amp;auml;ge&amp;ldquo; in der jeweiligen Beteiligungsrunde klickt.&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;11&quot;&gt;\r\n  &lt;li&gt;\r\n  &lt;h2&gt;&lt;br /&gt;\r\n  &lt;a name=&quot;Was muss ich tun&quot;&gt;&lt;/a&gt;Was muss ich tun, um &amp;uuml;ber die Ergebnisse der Beteiligung informiert zu werden?&lt;/h2&gt;\r\n &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Am Ende des Online-Fragebogens k&amp;ouml;nnt ihr angeben, dass ihr &amp;uuml;ber die Ergebnisse der Beteiligung informiert werden m&amp;ouml;chtet. Die Informationen schicken wir dann an die von euch angegebene E-Mail-Adresse. Solltet ihr diesen Service nicht mehr w&amp;uuml;nschen, k&amp;ouml;nnt ihr ihn jederzeit abbestellen. Dar&amp;uuml;ber hinaus k&amp;ouml;nnt ihr&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;12&quot;&gt;\r\n   &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Mein Passwort&quot;&gt;&lt;/a&gt;Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?&lt;/h2&gt;\r\n    &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Klickt im oberen rechten Teil der Website auf &amp;bdquo;Login&amp;ldquo;. Dort k&amp;ouml;nnt ihr ein neues Passwort oder einen neuen Zugangslink anfordern, indem ihr auf &amp;quot;Passwort vergessen&amp;quot; klickt.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;13&quot;&gt;\r\n  &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Weshalb&quot;&gt;&lt;/a&gt;Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?&lt;/h2&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Wer verschiedene Internetbrowser verwendet, dem werden beim Design der Website Unterschiede auffallen. Das liegt daran, dass unterschiedliche Browserversionen unterschiedliche Anforderungen an die Programmierung stellen. Die Unterschiede haben aber keine Auswirkungen auf die Funktionen der Website. Wir arbeiten daran, die Design-Unterschiede so gering wie m&amp;ouml;glich zu halten. Vorerst empfehlen wir euch, m&amp;ouml;glichst aktuelle Versionen von Firefox, Chrome, Opera oder Safari zu verwenden.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;14&quot;&gt;\r\n    &lt;li&gt;\r\n  &lt;h2&gt;&lt;strong&gt;&lt;a name=&quot;Warum findet sich&quot;&gt;&lt;/a&gt;&lt;/strong&gt;Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?&lt;/h2&gt;\r\n\r\n  &lt;p&gt;&lt;span style=&quot;color:#008080;&quot;&gt;So machen wir, von Ichmache&amp;gt;Politik das. Ihr auch?&lt;/span&gt;&lt;br /&gt;\r\n    &lt;br /&gt;\r\n    Bevor die Abstimmung gestartet wird, geht die Redaktion alle Beitr&amp;auml;ge noch einmal durch. Drei Punkte sind hierbei wichtig:&lt;/p&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;ul&gt;\r\n    &lt;li&gt;&lt;strong&gt;Gibt es Beitr&amp;auml;ge mit demselben Inhalt, derselben Aussage, Forderung oder Idee?&lt;/strong&gt; Wenn ja, fassen wir die Beitr&amp;auml;ge, die einen gleichen oder &amp;auml;hnlichen Inhalt haben, zusammen, damit ihr nicht immer wieder &amp;uuml;ber &amp;Auml;hnliches abstimmen m&amp;uuml;sst. Dabei wird immer festgehalten, welche Beitr&amp;auml;ge zusammengeflossen sind oder auch wo genau Teilaspekte gelandet sind.&lt;br /&gt;\r\n   &amp;nbsp;&lt;/li&gt;\r\n   &lt;li&gt;&lt;strong&gt;Enth&amp;auml;lt ein Beitrag mehrere unterschiedliche Aussagen, Forderungen oder Ideen? &lt;/strong&gt;Wenn ja, &amp;quot;splitten&amp;quot; wir den Beitrag &amp;uuml;berlicherweise auf, damit die anderen besser &amp;uuml;ber die einzelnen Aspekte abstimmen k&amp;ouml;nnen.&lt;br /&gt;\r\n  &amp;nbsp;&lt;/li&gt;\r\n   &lt;li&gt;&lt;strong&gt;Sind die Beitr&amp;auml;ge f&amp;uuml;r jeden verst&amp;auml;ndlich formuliert? &lt;/strong&gt;Wenn nicht, achten wir darauf, dass z.B. in euren Beitr&amp;auml;gen verwendete Fremdw&amp;ouml;rter &amp;uuml;bersetzt werden und der Satzbau nicht zu verschachtelt ist, damit die Aussage im Vordergrund steht und f&amp;uuml;r jede_n nachvollziehbar ist.&lt;strong&gt;&amp;nbsp;&lt;/strong&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px;&quot;&gt;Wir bem&amp;uuml;hen uns bei der redaktionellen Arbeit darum, so nah wie m&amp;ouml;glich, an euren Formulierungen zu bleiben und inhaltlich nichts zu ver&amp;auml;ndern.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px;&quot;&gt;Wenn ihr genauere Ausk&amp;uuml;nfte dazu haben wollt, was mit eurem Beitrag passiert ist, ruft uns einfach an (030 400 40 441). Zuk&amp;uuml;nftig soll das im &lt;sup&gt;e&lt;/sup&gt;Partool sichtbar gemacht werden. Dieses wird stetig weiterentwickelt, der gro&amp;szlig;e Relaunch steht vor der T&amp;uuml;r.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;ol start=&quot;15&quot;&gt;\r\n &lt;li&gt;\r\n  &lt;h2&gt;&lt;a name=&quot;Warum schreibt&quot;&gt;&lt;/a&gt;Warum schreibt ihr &amp;bdquo;jede_r&amp;ldquo; oder &amp;bdquo;Besucher_innen&amp;ldquo;?&lt;/h2&gt;\r\n  &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Das &amp;bdquo;_&amp;ldquo; ist der sogenannte Gender_Gap. Wie verwenden diese Schreibweise um deutlich zu machen, dass wir alle Menschen ansprechen m&amp;ouml;chten unabh&amp;auml;ngig von ihrer Geschlechtsidentit&amp;auml;t. Ein Gender_Gap wird eingef&amp;uuml;gt, um neben dem biologischen das soziale Geschlecht (Gender) darzustellen. Es ist eine aus dem Bereich der Queer-Theorie stammende Variante des Binnen-I. Der Gender_Gap soll ein Mittel der sprachlichen Darstellung aller sozialen Geschlechter und Geschlechtsidentit&amp;auml;ten abseits der Zweigeschlechtlichkeit sein. In der deutschen Sprache w&amp;auml;re dies sonst nur durch Umschreibungen m&amp;ouml;glich. Die Intention ist, durch den Zwischenraum einen Hinweis auf diejenigen Menschen zu geben, die nicht in das ausschlie&amp;szlig;liche Frau-Mann-Schema hineinpassen oder nicht hineinpassen wollen, wie Intersexuelle oder Transgender.&lt;/p&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;a name=&quot;Ihr findet&quot;&gt;&lt;/a&gt;Ihr findet hier keine Antwort auf eure Frage?&lt;/h2&gt;\r\n\r\n&lt;p style=&quot;margin-left: 40px; &quot;&gt;Dann wendet euch an das PROJEKTNAME Projektb&amp;uuml;ro&lt;br /&gt;\r\nE-Mail:&lt;br /&gt;\r\nTelefon:&lt;/p&gt;\r\n', '&lt;ol&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Worum&quot;&gt;Worum geht es hier eigentlich?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Wer kann sich&quot;&gt;Wer kann sich hier beteiligen?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Worauf sollte ich&quot;&gt;Worauf sollte ich beim Eintragen der Beitr&amp;auml;ge achten?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#M&uuml;ssen alle&quot;&gt;M&amp;uuml;ssen alle Fragen beantwortet werden?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#M&uuml;ssen die&quot;&gt;M&amp;uuml;ssen die Fragen der Reihenfolge nach beantwortet werden?&lt;/a&gt;&lt;/p&gt;\r\n &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Wie kann ich&quot;&gt;Wie kann ich einen Eintrag von mir l&amp;ouml;schen?&lt;/a&gt;&lt;/p&gt;\r\n   &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Warum muss ich&quot;&gt;Warum muss ich eine E-Mail-Adresse angeben?&lt;/a&gt;&lt;/p&gt;\r\n  &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Welche Daten&quot;&gt;Welche Daten werden gesammelt und wie werden sie weiterverwendet?&lt;/a&gt;&lt;/p&gt;\r\n  &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Was passiert&quot;&gt;Was passiert mit meinen Beitr&amp;auml;gen, nachdem ich auf den Best&amp;auml;tigungslink geklickt habe?&lt;/a&gt;&lt;/p&gt;\r\n   &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Wie kann ich sehen&quot;&gt;Wie kann ich sehen, was andere eingetragen haben?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Was muss ich tun&quot;&gt;Was muss ich tun, um &amp;uuml;ber die Ergebnisse der Beteiligung informiert zu werden?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Worum&quot;&gt;Mein Passwort bzw. mein Zugangslink ist verloren gegangen oder funktioniert nicht mehr. Was kann ich tun?&lt;/a&gt;&lt;/p&gt;\r\n &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Weshalb&quot;&gt;Weshalb sieht die Website in verschiedenen Internetbrowsern unterschiedlich aus?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Warum findet sich&quot;&gt;Warum findet sich mein Beitrag im Voting nicht im genauen Wortlaut wieder?&lt;/a&gt;&lt;/p&gt;\r\n    &lt;/li&gt;\r\n &lt;li&gt;\r\n  &lt;p&gt;&lt;a href=&quot;#Warum schreibt&quot;&gt;Warum schreibt ihr &amp;bdquo;jede_r&amp;ldquo; oder &amp;bdquo;Besucher_innen&amp;ldquo;?&lt;/a&gt;&lt;/p&gt;\r\n   &lt;/li&gt;\r\n&lt;/ol&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;#Ihr findet&quot;&gt;Ihr findet hier keine Antwort auf eure Frage?&lt;/a&gt;&lt;/p&gt;\r\n', NULL),
    (NULL,  @project_code,   'Was wir mit euch machen',  1,    'about',    '&lt;p&gt;&lt;em&gt;Hier erfahren die Teilnehmenden mehr dar&amp;uuml;ber, &lt;/em&gt;&lt;a href=&quot;#what&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;WAS&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; es mit eurem Projekt auf sich hat. &amp;ndash; Sie sollen herausfinden, wer &lt;/em&gt;&lt;a href=&quot;#us&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;WIR&lt;/strong&gt;&lt;/a&gt;&lt;em&gt;, also IHR, seid. &amp;ndash; Sie k&amp;ouml;nnen nachlesen wen ihr ansprechen wollt &lt;/em&gt;&lt;a href=&quot;#you&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;MIT EUCH&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; &amp;ndash; Und sie k&amp;ouml;nnen sich kurz und knapp dar&amp;uuml;ber informieren, wie sie mit&lt;/em&gt;&lt;a href=&quot;#vision&quot;&gt;&lt;strong&gt;&amp;rsaquo;&amp;rsaquo;&amp;rsaquo;MACHEN&lt;/strong&gt;&lt;/a&gt;&lt;em&gt; k&amp;ouml;nnen.&lt;/em&gt;&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;a name=&quot;what&quot;&gt;&lt;/a&gt;Was&lt;/h1&gt;\r\n\r\n&lt;h1&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;a name=&quot;us&quot;&gt;&lt;/a&gt; Wir&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&amp;nbsp;&lt;/h2&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;a name=&quot;you&quot;&gt;&lt;/a&gt; Mit euch&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h1&gt;&lt;br /&gt;\r\n&lt;a name=&quot;vision&quot;&gt;&lt;/a&gt; Machen&lt;/h1&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;1. Ideen, Vorschl&amp;auml;ge und Forderungen entwickeln!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Setzt euch vor Ort, in eurer Gruppe oder auch alleine mit den Themen der Beteiligungsrunde auseinander. Ihr entscheidet dabei, wie ihr das genau machen wollt. Ob ihr dazu eine kleine Diskussion im Freundeskreis durchf&amp;uuml;hrt, einen Workshop darauf organisiert oder eine gr&amp;ouml;&amp;szlig;ere Aktion startet, bleibt euch &amp;uuml;berlassen. Ebenso, ob ihr euch alle Fragen vornehmt oder nur ein oder zwei.&lt;/p&gt;\r\n\r\n&lt;p&gt;Findet heraus, wo das Thema in eurer Umgebung &amp;uuml;berall eine Rolle spielt, diskutiert im Verband, in der Schule, mit Freunden oder mit Verantwortlichen und bildet euch eine Meinung. Wir sammeln sowohl Einzelmeinungen als auch Ergebnisse aus Workshops, Gespr&amp;auml;chen am Lagerfeuer oder thematischen Gruppenstunden. Selbstverst&amp;auml;ndlich k&amp;ouml;nnt ihr auch Teile aus fertigen Beschl&amp;uuml;ssen verwenden, z. B. Positionspapiere eures Verbandes oder eurer Initiative.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n2. Beitragen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Wenn eure Ideen, Vorschl&amp;auml;ge und Forderungen fertig sind, tragt ihr sie hier online anhand der Fragen ein. Dort k&amp;ouml;nnt ihr auch nachgucken, was andere bereits geschrieben haben. So k&amp;ouml;nnen die Ergebnisse eurer Arbeit weitreichendere Bedeutung bekommen und Jugendpolitik in Deutschland und der EU beeinflussen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Bitte formuliert eure Beitr&amp;auml;ge m&amp;ouml;glichst knapp und beschr&amp;auml;nkt euch pro Box auf eure &amp;bdquo;Kernbotschaft&amp;ldquo; (max. 300 Buchstaben). F&amp;uuml;r Erkl&amp;auml;rungen, weitergehende Infos usw. nutzt bitte die jeweilige Erl&amp;auml;uterungsbox.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n3. Abstimmen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Nach dem Ende der Beitragsphase seid ihr ein zweites Mal gefragt! Gemeinsam mit den anderen Teilnehmer_innen der Beteiligungsrunde k&amp;ouml;nnt ihr dar&amp;uuml;ber abstimmen, welche der Beitr&amp;auml;ge eurer Meinung nach besonders wichtig f&amp;uuml;r die weitere politische Diskussion in der EU und hier in Deutschland sind. Wie viele f&amp;uuml;r eure Gruppe an der Abstimmung teilnehmen, ob alle, nur einige oder ein_e Gruppenvertreter_in k&amp;ouml;nnt ihr frei entscheiden.&lt;/p&gt;\r\n\r\n&lt;p&gt;Um euch das Abstimmen zu vereinfachen und die Beitr&amp;auml;ge auf eine abstimmbare Zahl zu reduzieren, fassen wir inhaltlich identische Beitr&amp;auml;ge redaktionell zusammen bzw. unterteilen facettenreiche Positionen in ihre einzelnen Aspekte. Dabei bem&amp;uuml;hen wir uns darum, so nah wie m&amp;ouml;glich am Inhalt eures Beitrags zu bleiben und nichts zu verf&amp;auml;lschen. Ihr k&amp;ouml;nnt dabei nachvollziehen, aus welchen eurer Antworten sich ein zur Abstimmung stehender Beitrag zusammensetzt. &amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;Durch die Abstimmung bestimmt ihr dar&amp;uuml;ber, was weiterkommt und was nicht. Die Beitr&amp;auml;ge mit der h&amp;ouml;chsten Punktzahl flie&amp;szlig;en am Ende in die Zusammenfassung ein.&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;Uuml;brigens: Aus Zeitgr&amp;uuml;nden m&amp;uuml;ssen wir manchmal auf die Abstimmung verzichten. In dem Fall ber&amp;uuml;cksichtigen wir alle Beitr&amp;auml;ge in der Zusammenfassung.&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\n4. Wirkung erzielen!&lt;/h2&gt;\r\n\r\n&lt;p&gt;Wir sorgen daf&amp;uuml;r, dass eure Ideen, Vorschl&amp;auml;ge und Forderungen an die Zust&amp;auml;ndigen weitergeleitet werden und damit in die politischen Diskussionen einflie&amp;szlig;en. Einige politische Akteur_innen hier in Deutschland haben verbindlich zugesagt, sich mit den Ergebnissen auseinanderzusetzen und euch eine R&amp;uuml;ckmeldung dazu zu geben. Weitere fragen wir je nach Thema an. Wenn Zwischenergebnisse und Reaktionen vorliegen, informieren wir euch dar&amp;uuml;ber.&lt;/p&gt;\r\n', '', NULL),
    (NULL,  @project_code,   'Impressum',    1,    'imprint',  '&lt;h1&gt;&lt;a id=&quot;oben&quot; name=&quot;oben&quot;&gt;&lt;/a&gt;Impressum&lt;/h1&gt;\r\n\r\n&lt;h2&gt;&lt;br /&gt;\r\nHerausgeber dieser Website&lt;/h2&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\nVerantwortlich&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Redaktion&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Adresse&lt;/h3&gt;\r\n\r\n&lt;h3&gt;Kontakt&lt;/h3&gt;\r\n\r\n&lt;p&gt;Telefon:&lt;br /&gt;\r\nTelefax:&lt;br /&gt;\r\nE-Mail:&lt;br /&gt;\r\nInternet:&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;&lt;a id=&quot;Rechtliches&quot; name=&quot;Rechtliches&quot;&gt;&lt;/a&gt;Rechtliche Hinweise&lt;/h2&gt;\r\n\r\n&lt;p&gt;Alle Angaben unseres Internetangebotes wurden sorgf&amp;auml;ltig gepr&amp;uuml;ft. Wir bem&amp;uuml;hen uns, dieses Informationsangebot aktuell und inhaltlich richtig sowie vollst&amp;auml;ndig anzubieten. Dennoch ist das Auftreten von Fehlern nicht v&amp;ouml;llig auszuschlie&amp;szlig;en. Eine Garantie f&amp;uuml;r die Vollst&amp;auml;ndigkeit, Richtigkeit und letzte Aktualit&amp;auml;t kann daher nicht &amp;uuml;bernommen werden.&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nDer Deutsche Bundesjugendring kann diese Website nach eigenem Ermessen jederzeit ohne Ank&amp;uuml;ndigung ver&amp;auml;ndern und/oder deren Betrieb einstellen. Er ist nicht verpflichtet, Inhalte dieser Website zu aktualisieren.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Zugang und die Benutzung dieser Website geschieht auf eigene Gefahr der Benutzer_innen. Der Deutsche Bundesjugendring ist nicht verantwortlich und &amp;uuml;bernimmt keinerlei Haftung f&amp;uuml;r Sch&amp;auml;den, u.a. f&amp;uuml;r direkte, indirekte, zuf&amp;auml;llige, vorab konkret zu bestimmende oder Folgesch&amp;auml;den, die angeblich durch den oder in Verbindung mit dem Zugang und/oder der Benutzung dieser Website aufgetreten sind.&lt;br /&gt;\r\n&lt;br /&gt;\r\nDer Betreiber &amp;uuml;bernimmt keine Verantwortung f&amp;uuml;r die Inhalte und die Verf&amp;uuml;gbarkeit von Websites Dritter, die &amp;uuml;ber externe Links dieses Informationsangebotes erreicht werden. Der Deutsche Bundesjugendring distanziert sich ausdr&amp;uuml;cklich von allen Inhalten, die m&amp;ouml;glicherweise straf- oder haftungsrechtlich relevant sind oder gegen die guten Sitten versto&amp;szlig;en.&lt;br /&gt;\r\n&lt;br /&gt;\r\nSofern innerhalb des Internetangebotes die M&amp;ouml;glichkeit zur Eingabe pers&amp;ouml;nlicher Daten (z.B. E-Mail-Adressen, Namen) besteht, so erfolgt die Preisgabe dieser Daten seitens der Nutzer_innen auf ausdr&amp;uuml;cklich freiwilliger Basis.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;textnutzung&quot; name=&quot;textnutzung&quot;&gt;&lt;/a&gt;Nutzungsrechte f&amp;uuml;r Texte und Dateien&lt;/h3&gt;\r\n\r\n&lt;p&gt;Soweit nicht anders angegeben liegen alle Rechte beim Deutschen Bundesjugendring. Davon ausgenommen sind Texte und Dateien unter CreativeCommons-Lizenzen.&lt;/p&gt;\r\n\r\n&lt;p&gt;Soweit im Einzelfall nicht anders geregelt und soweit nicht fremde Rechte betroffen sind, ist die Verbreitung der Dokumente und Bilder von&amp;nbsp;&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt; als Ganzes oder in Teilen davon in elektronischer und gedruckter Form unter der Voraussetzung erw&amp;uuml;nscht, dass die Quelle (&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;) genannt wird. Ohne vorherige schriftliche Genehmigung durch den Deutschen Bundesjugendring ist eine kommerzielle Verbreitung der bereitgestellten Texte, Informationen und Bilder ausdr&amp;uuml;cklich untersagt.&lt;/p&gt;\r\n\r\n&lt;p&gt;Nutzungsrechte f&amp;uuml;r Inhalte, die von Nutzer_innen erstellt werden, werden unter einer CreativeCommons-Lizenz zur Verf&amp;uuml;gung gestellt, sofern nicht anders gekennzeichnet.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenschutz&quot; name=&quot;datenschutz&quot;&gt;&lt;/a&gt;Datenschutzhinweise&lt;/h3&gt;\r\n\r\n&lt;p&gt;Der Deutsche Bundesjugendring nimmt den Schutz personenbezogener Daten sehr ernst. Wir m&amp;ouml;chten, dass jede_r wei&amp;szlig;, wann wir welche Daten erheben und wie wir sie verwenden. Wir haben technische und organisatorische Ma&amp;szlig;nahmen getroffen, die sicherstellen, dass die Vorschriften &amp;uuml;ber den Datenschutz sowohl von uns als auch von externen Dienstleistenden beachtet werden.&lt;br /&gt;\r\n&lt;br /&gt;\r\nIm Zuge der Weiterentwicklung unserer Webseiten und der Implementierung neuer Technologien, um unseren Service zu verbessern, k&amp;ouml;nnen auch &amp;Auml;nderungen dieser Datenschutzerkl&amp;auml;rung erforderlich werden. Daher empfehlen wir, sich diese Datenschutzerkl&amp;auml;rung ab und zu erneut durchzulesen.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;zugriff www&quot; name=&quot;zugriff www&quot;&gt;&lt;/a&gt;Zugriff auf das Internetangebot&lt;/h3&gt;\r\n\r\n&lt;p&gt;Jeder Zugriff auf das Internetangebot &lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;&amp;nbsp;wird in einer Protokolldatei gespeichert. In der Protokolldatei werden folgende Daten gespeichert:&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n  &lt;li&gt;Informationen &amp;uuml;ber die Seite, von der aus die Datei angefordert wurde&lt;/li&gt;\r\n &lt;li&gt;Name der abgerufenen Datei&lt;/li&gt;\r\n &lt;li&gt;Datum und Uhrzeit des Abrufs&lt;/li&gt;\r\n   &lt;li&gt;&amp;uuml;bertragene Datenmenge&lt;/li&gt;\r\n    &lt;li&gt;Meldung, ob der Abruf erfolgreich war&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;Die gespeicherten Daten werden ausschlie&amp;szlig;lich zur Optimierung des Internetangebotes ausgewertet. Die Angaben speichern wir auf Servern in Deutschland. Der Zugriff darauf ist nur wenigen, besonders befugten Personen m&amp;ouml;glich, die mit der technischen, kaufm&amp;auml;nnischen oder redaktionellen Betreuung der Server befasst sind.&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;datenweitergabe&quot; name=&quot;datenweitergabe&quot;&gt;&lt;/a&gt;Weitergabe personenbezogener Daten an Dritte&lt;/h3&gt;\r\n\r\n&lt;p&gt;Daten, die beim Zugriff auf&amp;nbsp;&lt;a href=&quot;http://www.strukturierter-dialog.de/mitmachen&quot;&gt;www.strukturierter-dialog.de/mitmachen&lt;/a&gt;&amp;nbsp;protokolliert worden sind, werden an Dritte nur &amp;uuml;bermittelt, soweit wir gesetzlich oder durch Gerichtsentscheidung dazu verpflichtet sind oder die Weitergabe im Falle von Angriffen auf die Internetinfrastruktur zur Rechts- oder Strafverfolgung erforderlich ist. Eine Weitergabe zu anderen nichtkommerziellen oder zu kommerziellen Zwecken erfolgt nicht.&lt;/p&gt;\r\n\r\n&lt;p&gt;Eingegebene personenbezogene Informationen werden vom Deutschen Bundesjugendring ausschlie&amp;szlig;lich zur Kommunikation mit den Nutzer_ innen verwendet. Wir geben sie ausdr&amp;uuml;cklich nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;h3&gt;&lt;br /&gt;\r\n&lt;a id=&quot;u18&quot; name=&quot;u18&quot;&gt;&lt;/a&gt;Schutz von Minderj&amp;auml;hrigen&lt;/h3&gt;\r\n\r\n&lt;p&gt;Personen unter 18 Jahren sollten ohne Zustimmung der Eltern oder Erziehungsberechtigten keine personenbezogenen Daten an uns &amp;uuml;bermitteln. Wir fordern keine personenbezogenen Daten von Kindern und Jugendlichen an. Wissentlich sammeln wir solche Daten nicht und geben sie auch nicht an Dritte weiter.&lt;/p&gt;\r\n\r\n&lt;p&gt;F&amp;uuml;r weitere Informationen in Bezug auf die Behandlung von personenbezogenen Daten steht zur Verf&amp;uuml;gung:&lt;/p&gt;\r\n\r\n&lt;p&gt;Michael Scholl&lt;br /&gt;\r\nTelefon: +49 (0)30.400 40-412&lt;br /&gt;\r\nTelefax: +49 (0)30.400 40-422&lt;br /&gt;\r\nE-Mail: &lt;a href=&quot;mailto:info@dbjr.de&quot;&gt;info@dbjr.de&lt;/a&gt;&lt;/p&gt;\r\n\r\n&lt;p&gt;&lt;a href=&quot;http://tool.ichmache-politik.de/privacy&quot; target=&quot;_blank&quot;&gt;&lt;strong&gt;&amp;raquo; Weitere Informationen zum Datenschutz&lt;/strong&gt;&lt;/a&gt;&lt;br /&gt;\r\n&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n\r\n&lt;h2&gt;Gestaltung und Realisierung&lt;/h2&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;design&quot; name=&quot;design&quot;&gt;&lt;/a&gt;Design&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.die-projektoren.de&quot; target=&quot;_blank&quot;&gt;DIE.PROJEKTOREN &amp;ndash; FARYS &amp;amp; RUSCH GBR&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;progammierung&quot; name=&quot;progammierung&quot;&gt;&lt;/a&gt;Programmierung&lt;/h3&gt;\r\n\r\n&lt;ul&gt;\r\n &lt;li&gt;&lt;a href=&quot;http://bohnetlingua.de/&quot;&gt;Anne Bohnet&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.digitalroyal.de&quot; target=&quot;_blank&quot;&gt;Digital Royal GmbH&amp;nbsp;&lt;/a&gt;&lt;/li&gt;\r\n  &lt;li&gt;Tim Schrock&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://www.seitenmeister.com&quot; target=&quot;_blank&quot;&gt;seitenmeister&lt;/a&gt;&amp;nbsp;&lt;/li&gt;\r\n &lt;li&gt;Synergic&lt;/li&gt;\r\n   &lt;li&gt;&lt;a href=&quot;http://www.xima.de&quot; target=&quot;_blank&quot;&gt;xima media GmbH &lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;h3&gt;&lt;a id=&quot;software&quot; name=&quot;software&quot;&gt;&lt;/a&gt;Verwendete Software&lt;/h3&gt;\r\n\r\n&lt;p&gt;Das Internetangebot &lt;sup&gt;e&lt;/sup&gt;Partool basiert auf quelloffener Software. Wir verwenden unter anderem&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n   &lt;li&gt;&lt;a href=&quot;https://httpd.apache.org/&quot; target=&quot;_blank&quot;&gt;Apache Webserver&lt;/a&gt;&amp;nbsp;mit &lt;a href=&quot;http://php.net/&quot; target=&quot;_blank&quot;&gt;PHP&lt;/a&gt;&amp;nbsp;und &lt;a href=&quot;http://mysql.com/&quot; target=&quot;_blank&quot;&gt;MySQL-Datenbanken&lt;/a&gt;&lt;/li&gt;\r\n &lt;li&gt;&lt;a href=&quot;http://twitter.github.io/bootstrap/&quot; target=&quot;_blank&quot;&gt;Bootstrap&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://ckeditor.com/&quot; target=&quot;_blank&quot;&gt;CKEditor&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://jquery.com/&quot; target=&quot;_blank&quot;&gt;jQuery&lt;/a&gt;&lt;/li&gt;\r\n    &lt;li&gt;&lt;a href=&quot;http://framework.zend.com/&quot; target=&quot;_blank&quot;&gt;Zend PHP Framework&lt;/a&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;&lt;br /&gt;\r\nBerlin im Mai 2013&lt;/p&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gtDas ePartool wird seit 2011 beim Deutschen Bundesjugendring entwickelt. Gefördert vom Bundesministerium für Familie, Senioren, Frauen und Jugend.&lt;/p&gt\r\n', '', NULL);


INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`) VALUES
(
    'help-text-home',
    '<p>Diese Seite begr&uuml;&szlig;t alle Besucher_innen. Oben leiten euch die Buttons <strong>&bdquo;Was &ndash; wir &ndash; mit euch &ndash; machen&ldquo;</strong> auf eine Unterseite mit detaillierten Informationen zum Projekt/ Prozess und den Phasen des Beteiligungstools.</p>
<p><strong>Login:</strong> Falls ihr schon einmal auf der Seite wart und an einer Beteiligungsrunde teilgenommen habt, k&ouml;nnt ihr euch mit eurer E-Mail-Adresse und eurem Passwort anmelden und unter <strong>&bdquo;Alle meine Beitr&auml;ge ansehen&ldquo;</strong> dort weitermachen, wo ihr das letzte Mal aufgeh&ouml;rt habt, oder die Beteiligung an einer neuen Runde beginnen. Wenn ihr eurer Passwort vergessen habt, k&ouml;nnt ihr euch ganz unkompliziert ein neues zuschicken lassen, indem ihr auf <strong>&bdquo;Passwort vergessen&ldquo; </strong>klickt.</p>
<p>&nbsp;</p>
<p>Im Hauptteil der Seite findet ihr eine &Uuml;bersicht &uuml;ber laufende oder k&uuml;rzlich beendete Beteiligungsrunden, die aktuellste an oberster Stelle. In horizontal nebeneinander stehenden schwarzen Balken sind Informationen zur Runde, die Fragen sowie die einzelnen Phasen der Beteiligungsrunde zum Anklicken aufgelistet. Die Phasen <strong>&bdquo;Beitr&auml;ge&ldquo;</strong> und <strong>&bdquo;Abstimmung&ldquo;</strong> haben zumeist bestimmte Laufzeiten, in denen dann zus&auml;tzlich ein gelber Button <strong>&bdquo;<em>Jetzt mitmachen!</em>&ldquo;</strong> auf dem schwarzen Balken leuchtet. Ist ein Balken grau, so ist diese Phase noch nicht aktiv und kann nicht ausgew&auml;hlt werden.</p>
<p>&nbsp;</p>
<p>Unter dem Hauptteil der Seite befindet sich eine Leiste mit Links zu besonderen Seiten (<strong><em>Datenschutz</em>, <em>Impressum</em> etc.</strong>). Darunter befindet sich der Fu&szlig;bereich mit Informationen zu Projekttr&auml;ger, F&ouml;rderer usw. Beides wird genau wie der Kopfbereich auf allen Unterseiten angezeigt.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-info',
    '<p>Unter <strong>&bdquo;Infos&ldquo;</strong> gibt es die wichtigsten Informationen zur entsprechenden Beteiligungsrunde auf einen Blick.</p>
     <p>Links auf der Seite befinden sich weitere Buttons, z.B. <strong>&bdquo;<em>So geht&rsquo;s</em>&ldquo;</strong> und <strong>&bdquo;<em>Infos zum Thema&ldquo;</em>, </strong>die euch auf weitere Unterseiten f&uuml;hren und den Beteiligungsprozess sowie den thematischen Hintergrund der jeweiligen Beteiligungsrunde und ihrer Fragen erl&auml;utern.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-question',
    '<p>Der Balken <strong>&bdquo;<em>Fragen</em>&ldquo;</strong> f&uuml;hrt euch auf eine Unterseite, auf der alle Fragen der Beteiligungsrunde sowie kurze Erl&auml;uterungen dazu aufgef&uuml;hrt sind.</p>
     <p>Wenn die Beitragsphase der Beteiligungsrunde aktiv ist, seht ihr au&szlig;erdem rechts neben jeder Frage den Button <strong>&bdquo;<em>Beitrag verfassen</em>&ldquo; </strong>&ndash; hier k&ouml;nnt ihr sofort loslegen.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-input',
    '<p>Durch Klicken auf den Balken <strong>&bdquo;Beitr&auml;ge&ldquo;</strong> kommt ihr auf diese Unterseite, auf der Felder mit den Fragen der Beteiligungsrunde und jeweils abgegebene Beitr&auml;ge angezeigt werden.</p>
     <p>Wenn die Beitragsphase der Beteiligungsrunde aktiv ist, seht ihr au&szlig;erdem rechts neben jeder Frage den Button <strong>&bdquo;<em>Beitrag verfassen</em>&ldquo; </strong>&ndash; hier k&ouml;nnt ihr sofort loslegen.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-voting',
    '<p>In der Abstimmungsphase erhaltet ihr (als Einzelperson von den Admins oder als Gruppenmitglieder von eurem_eurer Gruppenverantwortlichen) eine <strong>E-Mail mit Einladung zur Abstimmung und einem Zugangscode</strong>. Dieser Zugangscode ist entweder Einzelpersonen oder Gruppen zugeordnet &ndash; Gruppen haben ein st&auml;rkeres Stimmgewicht.</p>
     <p><strong>Wichtig: </strong>Mit dem Zugangscode einer Gruppe k&ouml;nnen beliebig viele junge Menschen teilnehmen, das Stimmengewicht der gesamten Gruppe ist vorher festgelegt und verteilt sich auf alle Abstimmenden. Gruppenverantwortliche m&uuml;ssen euch freischalten, k&ouml;nnen aber nicht sehen, wie ihr abstimmt, nur, dass ihr abgestimmt habt!</p>
     <p>Zum Abstimmen braucht ihr neben diesem Code eure E-Mail-Adresse.</p>
     <p>Zum Abstimmen klickt ihr bei laufendem Abstimmungszeitraum auf <strong>&bdquo;Jetzt abstimmen!&ldquo;</strong> in der entsprechenden Beteiligungsrunde. Dadurch gelangt ihr nach Eingabe von E-Mail-Adresse und Zugangscode auf eine &Uuml;bersichtsseite mit allen Fragen der Beteiligungsrunde sowie allen in den Beitr&auml;gen verwendeten Schlagw&ouml;rtern.</p>',
    @project_code,
    'default'
),
(
    'help-text-consultation-followup',
    '<p>Mit der Abstimmung ist die Beteiligung noch nicht zu Ende, denn ihr m&ouml;chtet ja auch sehen, was dabei herauskommt. Hier werden die Reaktionen anschaulich dargestellt:</p>
     <p>In der <strong>&Uuml;bersichtsseite der Reaktionen</strong> k&ouml;nnt ihr sehen, wie eure Beitr&auml;ge in der Abstimmung gewertet wurden und ob sie Teil der Ergebniszusammenfassung wurden. Ihr seht au&szlig;erdem, von wem es welche Reaktionen auf eine Beteiligungsrunde bzw. auf eure einzelnen Beitr&auml;ge gab. In welche Beschlusspapiere sind die Beitr&auml;ge eingegangen, welche politischen Debatten schlossen sich an die Beteiligungsrunde an? All das veranschaulicht das Tool durch Bereitstellung aller Dokumente und Links zu Websites, Videos etc.</p>
     <p>Unter diesem &Uuml;berblick gelangt ihr &uuml;ber die Fragen zu <strong>&bdquo;Reaktionen und Feedback auf einzelne Beitr&auml;ge&ldquo;:</strong> Hier k&ouml;nnt ihr bestimmte Beitr&auml;ge sowie die Reaktionen darauf im Einzelnen grafisch aufbereitet nachverfolgen.</p>',
    @project_code,
    'default'
),
(
    'help-text-login',
    '<p>Wenn ihr schon einmal teilgenommen habt oder Adminberechtigungen f&uuml;r eine oder mehrere Beteiligungsrunden besitzt, so habt ihr nach dem Login die M&ouml;glichkeit, von der Startseite zu den Beteiligungsrunden in den <strong>gesch&uuml;tzten Bereich</strong> zu wechseln. Dazu klickt ihr oben rechts neben der Anzeige eures Login-Namens auf den Pfeil, hier bekommt ihr die Optionen &bdquo;<strong>Alle meine Beitr&auml;ge ansehen&ldquo;</strong>, <strong>&bdquo;Gruppenmitglieder ansehen&ldquo;</strong> und <strong>&bdquo;Logout&ldquo;</strong> angezeigt. Administrator_innen k&ouml;nnen zudem in den<strong> &bdquo;Adminbereich&ldquo;</strong> wechseln.</p>',
    @project_code,
    'default'
),

('help-text-admin-consultation-voting-preparation', 'Sample voting-preparation text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-permissions', 'Sample voting-permissions text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-invitations', 'Sample voting-invitations text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-participants', 'Sample voting-participants text.', @project_code, 'admin'),
('help-text-admin-consultation-voting-results', 'Sample voting-results text.', @project_code, 'admin'),
('help-text-admin-consultation-follow-up', 'Sample follow-up text.', @project_code, 'admin'),
('help-text-admin-consultation-follow-up-snippets', 'Sample follow-up-snippets text.', @project_code, 'admin'),
('help-text-admin-question', 'Sample question text.', @project_code, 'admin'),
('help-text-admin-contribution', 'Sample contribution text.', @project_code, 'admin');

-- Migration 2016-09-26_14-50_DBJR-963.sql
DELETE FROM email_template_has_email_placeholder
WHERE email_template_id = (SELECT id FROM email_template WHERE name = 'voting_confirmation_single')
AND email_placeholder_id = (SELECT id FROM email_placeholder WHERE name = 'rejection_url');

-- Migration 20161123214112_dbjr1020.php
INSERT INTO `help_text` (`name`, `body`, `project_code`, `module`) VALUES
('help-text-admin-consultation-settings-general', 'Sample consultation settings general page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-participants-data', 'Sample consultation settings participants data page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-voting', 'Sample consultation settings voting page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-phases', 'Sample consultation settings phases page text.', @project_code, 'admin'),
('help-text-admin-consultation-settings-groups', 'Sample consultation settings groups page text.', @project_code, 'admin');
