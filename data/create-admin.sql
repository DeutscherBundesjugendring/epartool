SET collation_connection = 'utf8mb4_unicode_ci';
SET @name = 'Name';
SET @email = 'email@email.com';

INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES
    (@name, @email, 1, 'admin');
