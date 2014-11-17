ALTER TABLE `users` CHANGE `newsl_subscr` `newsl_subscr` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter';
UPDATE `users` SET `newsl_subscr` = 'n' WHERE `newsl_subscr` != 'y';
