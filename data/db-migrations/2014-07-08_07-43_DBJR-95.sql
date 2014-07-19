ALTER TABLE `users`
    CHANGE `pwd` `password` varchar(150) NOT NULL,
    ADD UNIQUE `users_email_idx` (`email`);
