ALTER TABLE `cnslt` ADD COLUMN `follow_up_explanation` text NULL DEFAULT NULL;

UPDATE `cnslt` SET `follow_up_explanation`='Hier erfahrt ihr, wie es mit euren Beiträgen weiterging, in welche Prozesse und Dokumente sie eingeflossen sind, wer wie darauf reagiert hat und inwieweit bereits Ergebnisse erreicht wurden.';
