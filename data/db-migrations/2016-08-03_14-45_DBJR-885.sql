ALTER TABLE `proj` DROP COLUMN `contribution_confirmation_info`;
ALTER TABLE `proj` DROP COLUMN `state_field_label`;

ALTER TABLE `cnslt` ADD COLUMN `state_field_label` varchar(255) DEFAULT NULL;
ALTER TABLE `cnslt` ADD COLUMN `contribution_confirmation_info` text NOT NULL;

UPDATE `cnslt` `c`
JOIN `proj` `p` ON `c`.`proj` = `p`.`proj`
SET
    `contribution_confirmation_info` = '<p>If you want to participate as a representative of a group, please fill in the registration form, no matter if the corresponding email address is already registered! The email to confirm the contributions will be sent to the registered email address.</p><p>If you participated in former participation rounds, and you wish to update information concerning you or your group in this participation round, please fill in the data on person/group. Your data will thus be up-to-date.</p>'
WHERE `locale` = 'en_US';

UPDATE `cnslt` `c`
JOIN `proj` `p` ON `c`.`proj` = `p`.`proj`
SET
`contribution_confirmation_info` = '<p>Wenn du als Vertreter_in einer Gruppe beitragen willst und die entsprechende E-Mail-Adresse ist bereits registriert, trage deine Daten bitte trotzdem in das Registrierungsformular ein! Die Mail zur Bestätigung der Beiträge wird dann an die hinterlegte Adresse gesendet.</p><p>Wenn du schon an früheren Beteiligungsrunden teilgenommen hast und für die aktuelle Beteiligungsrunde abweichende Angaben zu dir oder deiner Gruppe übermitteln willst, fülle bitte die Angaben zu Person/Gruppe neu aus: Damit sind dann auch alle Daten auf aktuellem Stand.</p>'
WHERE `locale` = 'de_DE';
