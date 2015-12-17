SET @project_code = 'xx';

INSERT INTO `email_template` (`name`, `type_id`, `project_code`, `subject`, `body_html`, `body_text`)
VALUES
    (
        'password_reset',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Neue Zugangsdaten',
        '<p>Hallo {{to_name}},</p>\n<p>Du hast neue Zugangsdaten angefordert. Mit den folgenden Daten kannst du dich einloggen:</p>\n\n<p>To reset your password, please visit this link:<br />{{password_reset_url}}</p>',
        'Hallo {{to_name}},\nDu hast neue Zugangsdaten angefordert. Mit den folgenden Daten kannst du dich einloggen:\n\nTo reset your password, please visit this link:\n\n{{password_reset_url}}'
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
<p>Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Zugangsdaten hast du bereits erhalten. Solltest du sie vergessen haben, kannst du mit Eingabe deiner E-Mail-Adresse jederzeit ein neues Passwort anfordern. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen. Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns. Bei Rückfragen stehen dir Jasmin-Marei Christen und Sanja Zeljko im Projektbüro gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40-441.</p>
<p>Liebe Grüße,<br />
Euer Projektbüro</p>
<h3>Übersicht über Eure Beiträge zur Beteiligungsrunde „{{consultation_title_long}}“</h3>
{{inputs_html}}',
        'Hallo {{to_name}},
danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.
Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:
{{confirmation_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:
{{rejection_url}}
Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Zugangsdaten hast du bereits erhalten. Solltest du sie vergessen haben, kannst du mit Eingabe deiner E-Mail-Adresse jederzeit ein neues Passwort anfordern. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen.
Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns.
Bei Rückfragen stehen dir Jasmin-Marei Christen und Sanja Zeljko im Projektbüro gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40-441.
Liebe Grüße,
Euer Projektbüro
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
<p>Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Zugangsdaten hast du bereits erhalten. Solltest du sie vergessen haben, kannst du mit Eingabe deiner E-Mail-Adresse jederzeit ein neues Passwort anfordern. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen. Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns. Bei Rückfragen stehen dir Jasmin-Marei Christen und Sanja Zeljko im Projektbüro gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40-441.</p>
<p>Also a new user account was created for you!</p>
<p>Liebe Grüße,<br />
Euer Projektbüro</p>
<h3>Übersicht über Eure Beiträge zur Beteiligungsrunde „{{consultation_title_long}}“</h3>
{{inputs_html}}',
        'Hallo {{to_name}},
danke für die Beteiligung an „{{consultation_title_long}}“. Unten findest Du eine Übersicht deiner/eurer Antworten auf die Fragestellungen.
Um sicherzustellen, dass die Einträge nicht von einem Spamversender kommen, bitten wir dich um die Bestätigung der Eingaben über den folgenden Link:
{{confirmation_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste Deines Browsers ein. Drücke anschließend die Eingabetaste.
Wenn die Beiträge nicht von dir sind, kannst du sie mithilfe dieses Links ablehnen:
{{rejection_url}}
Der Bestätigungslink verliert nach dem Ende der Beteiligungsphase seine Gültigkeit. Deine Zugangsdaten hast du bereits erhalten. Solltest du sie vergessen haben, kannst du mit Eingabe deiner E-Mail-Adresse jederzeit ein neues Passwort anfordern. Deine Beiträge kannst du im Login-Bereich bis zum Ende der ersten Beteiligungsphase weiter bearbeiten, ergänzen oder löschen.
Das Sammeln von Beiträgen und damit die erste Beteiligungsphase endet am {{input_phase_end}}. Danach werden wir alle Teilnehmenden bitten, aus den gesammelten Beiträgen diejenigen auszuwählen, die ihnen am wichtigsten sind. Dazu erhältst du/erhaltet ihr eine E-Mail mit weiteren Informationen von uns.
Bei Rückfragen stehen dir Jasmin-Marei Christen und Sanja Zeljko im Projektbüro gern zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40-441.
Also a new user account was created for you!
Liebe Grüße,
Euer Projektbüro
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
<p>vielen Dank für deine Teilnahme an der Abstimmung zur Beteiligungsrunde „{{consultation_title_long}}“. Damit wir sicherstellen können, dass wirklich du selbst abgestimmt hast, bitten wir dich, deine Teilnahme über den unten stehenden Link zu bestätigen.</p>
<p>Ja, ich habe abgestimmt:<br />
<a href="{{confirmation_url}}">{{confirmation_url}}</a></p>
<p>Solltest du nicht an der Abstimmung teilgenommen haben, klicke auf diesen Link:<br />
<a href="{{rejection_url}}">{{rejection_url}}</a></p>
<p>Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste deines Browsers ein. Drücke anschließend die Eingabetaste. Wir freuen uns über dein Interesse und werden dich nach Abschluss der Beteiligungsrunde per E-Mail über die Ergebnisse informieren.</p>
<p>Liebe Grüße,<br />
dein Projektbüro</p>',
        'Hallo {{to_email}},
vielen Dank für deine Teilnahme an der Abstimmung zur Beteiligungsrunde „{{consultation_title_long}}“.
Damit wir sicherstellen können, dass wirklich du selbst abgestimmt hast, bitten wir dich, deine Teilnahme über den unten stehenden Link zu bestätigen.
Ja, ich habe abgestimmt:
{{confirmation_url}}
Solltest du nicht an der Abstimmung teilgenommen haben, klicke auf diesen Link:
{{rejection_url}}
Falls sich der Bestätigungslink nicht anklicken lässt oder ein Teil des Links abgeschnitten wurde, kopiere bitte den gesamten Link und füge ihn in die Adressleiste deines Browsers ein. Drücke anschließend die Eingabetaste.
Wir freuen uns über dein Interesse und werden dich nach Abschluss der Beteiligungsrunde per E-Mail über die Ergebnisse informieren.
Liebe Grüße,
dein Projektbüro'
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
<p>Liebe Grüße,<br />
euer Projektbüro</p>',
        'Hallo {{to_email}},
im Rahmen eurer Teilnahme an der Beteiligungsrunde „{{consultation_title_long}}“ hat jemand neu für eure Gruppe abgestimmt. Bitte bestätige, ob „{{voter_email}}“ für euch abstimmungsberechtigt ist.
Ja, diese Person gehört zu unserer Gruppe:
{{confirmation_url}}
Nein, diese Person gehört nicht zu unserer Gruppe:
{{rejection_url}}
Liebe Grüße,
euer Projektbüro'
    ),
    (
        'voting_invitation_single',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Konsultation „{{consultation_title_short}}“: Jetzt abstimmen!',
        '<p>Hallo {{to_name}},</p>
<p>du hast an der Beteiligungsrunde zu „{{consultation_title_long}}“ teilgenommen. Noch einmal herzlichen Dank für deine Beiträge! In der zweiten Phase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb haben du und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage eurer Bewertungen werden wir am Ende die Zusammenfassung erstellen. Die Abstimmung erfolgt anonym. Die IP-Adresse deines Computers wird nicht gespeichert.</p>
<p>Hier geht’s los:<br />
{{voting_url}}</p>
<p>Sollten technische Probleme auftreten oder Fragen aufkommen, stehen dir Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40 443.</p>
<p>Mit freundlichen Grüßen<br />
Deine Koordinierungsstelle</p>',
        'Hallo {{to_name}},
du hast an der Beteiligungsrunde zu „{{consultation_title_long}}“ teilgenommen. Noch einmal herzlichen Dank für deine Beiträge!
In der zweiten Phase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb haben du und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage eurer Bewertungen werden wir am Ende die Zusammenfassung erstellen.
Die Abstimmung erfolgt anonym. Die IP-Adresse deines Computers wird nicht gespeichert.
Hier geht’s los:
{{voting_url}}
Sollten technische Probleme auftreten oder Fragen aufkommen, stehen dir Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40 443.
Mit freundlichen Grüßen
Deine Koordinierungsstelle'
    ),
    (
        'voting_invitation_group',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Konsultation „{{consultation_title_short}}“: Jetzt abstimmen!',
        '<p>Hallo {{to_name}},</p>
<p>ihr habt euch an der Beteiligungsrunde zu „{{consultation_title_long}}“ als Gruppe beteiligt. Noch einmal herzlichen Dank für eure Beiträge!</p>
<p>In der Abstimmungsphase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb habt ihr und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage aller Bewertungen werden wir am Ende die Zusammenfassung erstellen.</p>
<p>Du wurdest als Kontaktperson für diese Gruppe eingetragen.</p>
<p>Was ist nun deine Aufgabe?</p>
<ol>
<li>Eure Gruppe zählt wegen ihrer Größe zur Kategorie {{group_category}} Teilnehmer_innen und hat damit bei dieser Beteiligungsrunde ein Gewicht von {{voting_weight}}. Das bedeutet, egal wie viele Leute für eure Gruppe teilnehmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet. Das heißt, ihr könnt frei entscheiden, wie viele Personen für eure Gruppe an der 2. Phase teilnehmen sollen: eine, zwölf, dreiundfünfzig, hundert oder mehr!</li>
<li>Leite denjenigen, die für eure Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.</li>
<li>Damit du als Kontaktpersonen den Überblick behältst, wer sich für eure Gruppe beteiligt, und um Missbrauch zu vermeiden, musst du anschließend bestätigen, dass die Personen, die teilgenommen haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.</li>
<li>Am Ende des Abstimmungszeitraums werdet ihr von uns selbstverständlich über das Endergebnis informiert.</li>
</ul>
<p>Der Zugangslink für Ihre/eure Gruppe lautet:<br />
{{voting_url}}</p>
<p>***</p>
<p>Vorschlag für ein Anschreiben an die Mitglieder deiner Gruppe:</p>
<p>Wir haben an der Beteiligungsrunde „{{consultation_title_long}}“ teilgenommen. Bis {{voting_phase_end}} haben nun alle Teilnehmer_innen die Möglichkeit, online darüber abzustimmen, welche der Beiträge aus ihrer Sicht besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.</p>
<p>Macht mit und stimmt mit ab. Hier geht’s los:<br />
<p>{{voting_url}}</p>
<p>***</p>
<p>Sollten technische Probleme auftreten oder Fragen aufkommen, stehen euch Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40 443.</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
ihr habt euch an der Beteiligungsrunde zu „{{consultation_title_long}}“ als Gruppe beteiligt. Noch einmal herzlichen Dank für eure Beiträge!
In der Abstimmungsphase der Beteiligungsrunde geht es nun darum, die gesammelten Beiträge zu bewerten. Deshalb habt ihr und alle anderen Teilnehmenden vom {{voting_phase_start}} bis {{voting_phase_end}} die Möglichkeit, online darüber abzustimmen, welche der Beiträge besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage aller Bewertungen werden wir am Ende die Zusammenfassung erstellen.
Du wurdest als Kontaktperson für diese Gruppe eingetragen.
Was ist nun deine Aufgabe?
1. Eure Gruppe zählt wegen ihrer Größe zur Kategorie {{group_category}} Teilnehmer_innen und hat damit bei dieser Beteiligungsrunde ein Gewicht von {{voting_weight}}. Das bedeutet, egal wie viele Leute für eure Gruppe teilnehmen, in der Summe werden ihre Stimmen anteilig auf dieses Gewicht hoch- bzw. heruntergerechnet. Das heißt, ihr könnt frei entscheiden, wie viele Personen für eure Gruppe an der 2. Phase teilnehmen sollen: eine, zwölf, dreiundfünfzig, hundert oder mehr!
2. Leite denjenigen, die für eure Gruppe an der Abstimmung teilnehmen sollen, den unten stehenden Zugangslink weiter. Nach Eingabe einer E-Mail-Adresse können diese Personen mit der Abstimmung beginnen. Diese erfolgt anonym.
3. Damit du als Kontaktpersonen den Überblick behältst, wer sich für eure Gruppe beteiligt, und um Missbrauch zu vermeiden, musst du anschließend bestätigen, dass die Personen, die teilgenommen haben, auch dazu berechtigt waren. Dies geschieht ganz einfach per E-Mail.
4. Am Ende des Abstimmungszeitraums werdet ihr von uns selbstverständlich über das Endergebnis informiert.
Der Zugangslink für Ihre/eure Gruppe lautet:
{{voting_url}}
***
Vorschlag für ein Anschreiben an die Mitglieder deiner Gruppe:
Wir haben an der Beteiligungsrunde „{{consultation_title_long}}“ teilgenommen. Bis {{voting_phase_end}} haben nun alle Teilnehmer_innen die Möglichkeit, online darüber abzustimmen, welche der Beiträge aus ihrer Sicht besonders wichtig für die weitere politische Diskussion zum Thema sind. Auf der Grundlage des Abstimmungsergebnisses wird am Ende die Zusammenfassung erstellt.
Macht mit und stimmt mit ab. Hier geht’s los:
{{voting_url}}
***
Sollten technische Probleme auftreten oder Fragen aufkommen, stehen euch Ann-Kathrin Fischer und Tim Schrock gerne zur Verfügung. Einfach anmailen ({{contact_email}}) oder anrufen unter 030. 400 40 443.
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'question_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your subscription',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your subscription:</p>
<p>{{question_text}}</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your subscription:
{{question_text}}
{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'question_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your subscription and your new user account',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your subscription and your new user account:</p>
<p>{{question_text}}</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your subscription and your new user account:
{{question_text}}
{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'notification_new_input_created',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'New input created',
        '<p>Hallo {{to_name}},</p>
<p>There has been new input created:</p>
<p>{{website_url}}</p>
<p>Unsubscribe:</p>
<p>{{unsubscribe_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
There has been new input created:
{{website_url}}
Unsubscribe:
{{unsubscribe_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'input_discussion_contrib_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your input discussion contribution and your new user account',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your input discussion contribution and your new user account:</p>
<p>Your contribution:</p>
<p>{{contribution_text}}</p>
<p>{{video_url}}</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your input discussion contribution and your new user account:
Your contribution:
{{contribution_text}}

{{video_url}}

{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'input_discussion_contrib_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your input discussion contribution ',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your input discussion contribution:</p>
<p>Your contribution:</p>
<p>{{contribution_text}}</p>
<p>{{video_url}}</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your input discussion contribution:
Your contribution:
{{contribution_text}}

{{video_url}}

{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'notification_new_input_discussion_contrib_created',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'New input discussion contribution created',
        '<p>Hallo {{to_name}},</p>
<p>There has been new input discussion contribution created:</p>
<p>{{website_url}}</p>
<p>Unsubscribe:</p>
<p>{{unsubscribe_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
There has been new input discussion contribution created:
{{website_url}}
Unsubscribe:
{{unsubscribe_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'input_discussion_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your subscription',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your subscription:</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your subscription:
{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
    (
        'input_discussion_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name`='system'),
        @project_code,
        'Please confirm your subscription and your new user account',
        '<p>Hallo {{to_name}},</p>
<p>Please confirm your subscription and your new user account:</p>
<p>{{input_thes}}</p>
<p>{{input_expl}}</p>
<p>{{confirmation_url}}</p>
<p>Mit freundlichen Grüßen<br />
Eure Koordinierungsstelle</p>',
        'Hallo {{to_name}},
Please confirm your subscription and your new user account:
{{input_thes}}
{{input_expl}}
{{confirmation_url}}
Mit freundlichen Grüßen
Eure Koordinierungsstelle'
    ),
   (
        'notification_new_follow_up_file_created',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'New follow-up created',
        'html text email version',
        'plain text email version'
    ),
    (
        'follow_up_subscription_confirmation',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'Follow up subscription confirmation',
        'html text email version',
        'plain text email version'
    ),
    (
        'follow_up_subscription_confirmation_new_user',
        (SELECT `id` FROM `email_template_type` WHERE `name` = 'system'),
        @project_code,
        'Follow up subscription confirmation for new user',
        'html text email version',
        'plain text email version'
    );




INSERT INTO `email_template_has_email_placeholder` (`email_template_id`, `email_placeholder_id`)
VALUES
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='password_reset' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='password_reset_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_html')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_text')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_html')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='inputs_text')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voter_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='rejection_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_confirmation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_single' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='consultation_title_short')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_start')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_phase_end')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_weight')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='group_category')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='voting_invitation_group' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='voting_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='question_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='question_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='unsubscribe_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_contrib_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='video_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='contribution_text')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='notification_new_input_discussion_contrib_created' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='unsubscribe_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_thes')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_expl')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),


    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_thes')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='input_expl')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name`='input_discussion_subscription_confirmation_new_user' AND `project_code`=@project_code),
        (SELECT `id` FROM `email_placeholder` WHERE `name`='confirmation_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'notification_new_follow_up_file_created'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'unsubscribe_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    ),

    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'consultation_title_long')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_name')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'to_email')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'website_url')
    ),
    (
        (SELECT `id` FROM `email_template` WHERE `name` = 'follow_up_subscription_confirmation_new_user'),
        (SELECT `id` FROM `email_placeholder` WHERE `name` = 'confirmation_url')
    );


INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);
INSERT INTO `footer` (`proj`) VALUES (@project_code);
