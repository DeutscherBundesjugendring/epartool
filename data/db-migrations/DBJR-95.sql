ALTER TABLE `users`
    CHANGE `pwd` `password` varchar(150) NOT NULL;
    -- ADD UNIQUE `idx_users_email` (`email`);
