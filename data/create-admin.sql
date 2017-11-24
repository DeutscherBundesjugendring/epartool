SET collation_connection = 'utf8mb4_unicode_ci';
SET @name = 'Name';
SET @email = 'email@email.com';
SET @pass = '$2a$04$zzRQ8gjyObNtNcDM7.fkvOjg4lzSw3HXkHWH9iRT9qklR3emxT6lG'; -- Sets password to: passpass
SET @pass = 1; -- Comment out this line to have a working password defined in the line above

INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES
    (@name, @email, @pass, 'admin');
