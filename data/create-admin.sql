SET @name = 'Name';
SET @email = 'email@email.com';

INSERT INTO `users` (`name`, `email`, `grp`, `password`, `lvl`)
VALUES
    (@name, @email, 1, '', 'adm');
