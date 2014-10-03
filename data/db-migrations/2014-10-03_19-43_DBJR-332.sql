ALTER TABLE `users` CHANGE `newsl_subscr` `newsl_subscr` enum('y','n') NOT NULL DEFAULT 'n' COMMENT 'Subscription of newsletter';
